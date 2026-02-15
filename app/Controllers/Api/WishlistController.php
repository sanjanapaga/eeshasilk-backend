<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\WishlistModel;

class WishlistController extends ResourceController
{
    protected $modelName = 'App\Models\WishlistModel';
    protected $format    = 'json';

    public function index()
    {
        $userId = $this->request->user->uid;

        $items = $this->model
            ->select('wishlist.*, products.name, products.price, products.image')
            ->join('products', 'products.id = wishlist.product_id')
            ->where('user_id', $userId)
            ->findAll();

        return $this->respond([
            'status' => 200,
            'items'  => $items
        ]);
    }

    public function create()
    {
        $userId = $this->request->user->uid;
        $productId = $this->request->getVar('product_id');

        // Check for duplicates
        if ($this->model->where(['user_id' => $userId, 'product_id' => $productId])->first()) {
            return $this->respond(['status' => 200, 'message' => 'Already in wishlist']);
        }

        $data = [
            'user_id'    => $userId,
            'product_id' => $productId,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (!$this->model->insert($data)) {
            return $this->fail($this->model->errors());
        }

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Added to wishlist'
        ]);
    }

    public function delete($id = null)
    {
        $this->model->delete($id);
        return $this->respondDeleted([
            'status'  => 200,
            'message' => 'Removed from wishlist'
        ]);
    }
}
