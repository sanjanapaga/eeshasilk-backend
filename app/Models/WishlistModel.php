<?php

namespace App\Models;

use CodeIgniter\Model;

class WishlistModel extends Model
{
    protected $table            = 'wishlist';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'product_id'];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules      = [
        'user_id'    => 'required|integer',
        'product_id' => 'required|integer',
    ];
}
