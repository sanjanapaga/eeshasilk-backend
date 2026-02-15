<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'customer_name', 'customer_email', 'customer_phone', 'shipping_address', 'subtotal', 'total_amount', 'delivery_fee', 'discount', 'status', 'payment_method', 'payment_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'user_id'         => 'permit_empty|integer',
        'customer_name'   => 'required|max_length[255]',
        'customer_email'  => 'required|valid_email|max_length[255]',
        'customer_phone'  => 'required|max_length[20]',
        'shipping_address' => 'required',
        'subtotal'        => 'required|decimal',
        'total_amount'    => 'required|decimal',
        'delivery_fee'    => 'required|decimal',
        'discount'        => 'permit_empty|decimal',
        'status'          => 'required|max_length[50]',
    ];
}
