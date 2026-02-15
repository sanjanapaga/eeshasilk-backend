<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ReviewModel;

class ReviewController extends ResourceController
{
    protected $modelName = 'App\Models\ReviewModel';
    protected $format    = 'json';

    public function index()
    {
        $productId = $this->request->getVar('product_id');
        if ($productId) {
            $reviews = $this->model->where('product_id', $productId)->findAll();
        } else {
            $reviews = $this->model->findAll();
        }

        return $this->respond([
            'status'  => 200,
            'reviews' => $reviews
        ]);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        if (!$this->model->insert($data)) {
            return $this->fail($this->model->errors());
        }
        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Review added successfully'
        ]);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Review not found');
        }
        $this->model->delete($id);
        return $this->respondDeleted([
            'status'  => 200,
            'message' => 'Review deleted successfully'
        ]);
    }
}
