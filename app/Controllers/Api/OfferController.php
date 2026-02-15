<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\OfferModel;

class OfferController extends ResourceController
{
    protected $modelName = 'App\Models\OfferModel';
    protected $format    = 'json';

    public function index()
    {
        return $this->respond([
            'status' => 200,
            'offers' => $this->model->findAll()
        ]);
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }
        
        if (!$this->model->insert($data)) {
            return $this->fail($this->model->errors());
        }
        
        $id = $this->model->getInsertID();
        $newOffer = $this->model->find($id);
        
        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Offer added successfully',
            'offer'   => $newOffer
        ]);
    }

    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Offer not found');
        }
        $this->model->delete($id);
        return $this->respondDeleted([
            'status'  => 200,
            'message' => 'Offer deleted successfully'
        ]);
    }
}
