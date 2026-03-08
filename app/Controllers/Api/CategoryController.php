<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CategoryModel;

class CategoryController extends ResourceController
{
    protected $modelName = 'App\Models\CategoryModel';
    protected $format    = 'json';

    public function index()
    {
        return $this->respond([
            'status'     => 200,
            'categories' => $this->model->findAll()
        ]);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        
        // Auto-generate slug from name if not provided
        if (!isset($data['slug']) && isset($data['name'])) {
            $data['slug'] = url_title($data['name'], '-', true);
        }
        
        if (!$this->model->insert($data)) {
            return $this->fail($this->model->errors());
        }
        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Category created successfully'
        ]);
    }

    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Category not found');
        }
        
        $data = $this->request->getJSON(true);
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = url_title($data['name'], '-', true);
        }
        
        $data['id'] = $id;
        
        if (!$this->model->update($id, $data)) {
            return $this->fail($this->model->errors());
        }
        
        return $this->respond([
            'status'  => 200,
            'message' => 'Category updated successfully'
        ]);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Category not found');
        }
        $this->model->delete($id);
        return $this->respondDeleted([
            'status'  => 200,
            'message' => 'Category deleted successfully'
        ]);
    }
}
