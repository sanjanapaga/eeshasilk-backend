<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewModel extends Model
{
    protected $table            = 'reviews';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['product_id', 'user_id', 'user_name', 'rating', 'comment'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'product_id' => 'required|integer',
        'user_name'  => 'required|max_length[255]',
        'rating'     => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
        'comment'    => 'required',
    ];
}
