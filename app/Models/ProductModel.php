<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'description', 'price', 'discount', 'category', 'sizes', 'image', 'stock_quantity'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'name'           => 'required|min_length[3]|max_length[255]',
        'price'          => 'required|decimal',
        'discount'       => 'permit_empty|integer',
        'category'       => 'required|max_length[100]',
        'stock_quantity' => 'required|integer',
    ];
}
