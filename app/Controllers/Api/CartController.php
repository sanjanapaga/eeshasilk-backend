<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CartModel;

class CartController extends ResourceController
{
    protected $modelName = 'App\Models\CartModel';
    protected $format    = 'json';

    public function index()
    {
        $userId = $this->request->user->uid;

        $items = $this->model
            ->select('cart.*, products.name, products.price, products.image, products.discount, products.stock_quantity, products.category')
            ->join('products', 'products.id = cart.product_id')
            ->where('user_id', $userId)
            ->findAll();

        $base = rtrim(config('App')->baseURL, '/');
        // Calculate image URLs
        foreach ($items as &$item) {
            if (!empty($item['image'])) {
                if (strpos($item['image'], 'http://') === 0 || strpos($item['image'], 'https://') === 0) {
                    $item['image_url'] = $item['image'];
                } else {
                    $item['image_url'] = $base . '/' . ltrim($item['image'], '/');
                }
            }
        }

        return $this->respond([
            'status' => 200,
            'items'  => $items
        ]);
    }

    public function create()
    {
        $userId = $this->request->user->uid;
        $productId = $this->request->getVar('product_id');
        $size = $this->request->getVar('size');
        $quantity = $this->request->getVar('quantity') ?? 1;

        // Check if item already exists in cart with SAME size
        $existing = $this->model->where([
            'user_id' => $userId,
            'product_id' => $productId,
            'size' => $size
        ])->first();

        if ($existing) {
            $this->model->update($existing['id'], [
                'quantity' => $existing['quantity'] + $quantity
            ]);
            return $this->respond([
                'status' => 200,
                'message' => 'Cart updated'
            ]);
        }

        $data = [
            'user_id'    => $userId,
            'product_id' => $productId,
            'size'       => $size,
            'quantity'   => $quantity
        ];

        if (!$this->model->insert($data)) {
            return $this->fail($this->model->errors());
        }

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Added to cart'
        ]);
    }

    public function update($id = null)
    {
        $quantity = $this->request->getVar('quantity');
        
        if ($quantity <= 0) {
            return $this->delete($id);
        }

        if (!$this->model->update($id, ['quantity' => $quantity])) {
            return $this->fail($this->model->errors());
        }

        return $this->respond([
            'status' => 200,
            'message' => 'Quantity updated'
        ]);
    }

    public function delete($id = null)
    {
        $this->model->delete($id);
        return $this->respondDeleted([
            'status'  => 200,
            'message' => 'Removed from cart'
        ]);
    }

    public function clear()
    {
        $userId = $this->request->user->uid;
        $this->model->where('user_id', $userId)->delete();
        
        return $this->respond([
            'status' => 200,
            'message' => 'Cart cleared'
        ]);
    }
}
