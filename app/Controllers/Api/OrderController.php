<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Libraries\EmailService;

class OrderController extends ResourceController
{
    protected $modelName = 'App\Models\OrderModel';
    protected $format    = 'json';
    protected $helpers   = ['url'];

    private function ensureImageUrl(&$item)
    {
        if (!empty($item['image'])) {
            if (strpos($item['image'], 'http://') === 0 || strpos($item['image'], 'https://') === 0) {
                $item['image_url'] = $item['image'];
            } else {
                $item['image_url'] = base_url($item['image']);
            }
        }
    }

    public function index()
    {
        $userId = $this->request->getGet('user_id');
        $query = $this->model->orderBy('created_at', 'DESC');
        if ($userId) {
            $query->where('user_id', $userId);
        }
        $orders = $query->findAll();

        $orderItemModel = new \App\Models\OrderItemModel();
        foreach ($orders as &$order) {
            $order['items'] = $orderItemModel->where('order_id', $order['id'])
                ->select('order_items.*, products.name as product_name, products.image')
                ->join('products', 'products.id = order_items.product_id')
                ->findAll();
            
            foreach ($order['items'] as &$item) {
                $this->ensureImageUrl($item);
                $item['product_image'] = $item['image_url'] ?? null; // For frontend compatibility
            }
        }

        return $this->respond(['status' => 200, 'orders' => $orders]);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $data = $this->request->getJSON(true);

        $orderData = [
            'user_id'          => $data['user_id'] ?? null,
            'customer_name'    => $data['customerName'] ?? '',
            'customer_email'   => $data['customerEmail'] ?? '',
            'customer_phone'   => $data['customerPhone'] ?? '',
            'shipping_address' => is_array($data['shippingAddress']) ? json_encode($data['shippingAddress']) : ($data['shippingAddress'] ?? ''),
            'subtotal'         => $data['subtotal'] ?? 0,
            'total_amount'     => $data['total'] ?? 0,
            'delivery_fee'     => $data['deliveryFee'] ?? 0,
            'discount'         => $data['discount'] ?? 0,
            'status'           => $data['status'] ?? 'pending',
            'payment_method'   => $data['payment_method'] ?? 'cod',
            'payment_id'       => $data['payment_id'] ?? null,
        ];

        $orderId = $this->model->insert($orderData);

        if (!$orderId) {
            $db->transRollback();
            return $this->fail($this->model->errors());
        }

        $items = $data['items'] ?? [];
        $orderItemModel = new OrderItemModel();

        foreach ($items as $item) {
            $orderItemModel->insert([
                'order_id'   => $orderId,
                'product_id' => $item['id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->fail('Failed to create order');
        }

        // Send Email Notification
        try {
            $emailService = new EmailService();
            $orderData['id'] = $orderId;
            $orderData['items'] = $items;
            
            // Map product names for email if not present
            $productModel = new \App\Models\ProductModel();
            foreach ($orderData['items'] as &$item) {
                if (!isset($item['product_name'])) {
                    $product = $productModel->find($item['id']);
                    $item['product_name'] = $product['name'] ?? 'Product';
                }
            }

            $emailService->sendOrderNotification($orderData);
        } catch (\Exception $e) {
            log_message('error', 'Email notification failed: ' . $e->getMessage());
        }

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Order placed successfully',
            'order_id' => $orderId
        ]);
    }

    public function show($id = null)
    {
        $order = $this->model->find($id);
        if (!$order) {
            return $this->failNotFound('Order not found');
        }

        $orderItemModel = new OrderItemModel();
        $order['items'] = $orderItemModel->select('order_items.*, products.name as product_name, products.image')
            ->join('products', 'products.id = order_items.product_id')
            ->where('order_id', $id)
            ->findAll();

        foreach ($order['items'] as &$item) {
            $this->ensureImageUrl($item);
            $item['product_image'] = $item['image_url'] ?? null; // For frontend compatibility
        }

        if (!empty($order['shipping_address'])) {
            $order['shippingAddress'] = json_decode($order['shipping_address'], true);
        }

        return $this->respond([
            'status' => 200,
            'order'  => $order
        ]);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON(true);
        if (!$this->model->update($id, $data)) {
            return $this->fail($this->model->errors());
        }
        return $this->respond([
            'status'  => 200,
            'message' => 'Order updated successfully'
        ]);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Order not found');
        }
        $this->model->delete($id);
        return $this->respondDeleted([
            'status'  => 200,
            'message' => 'Order deleted successfully'
        ]);
    }
}
