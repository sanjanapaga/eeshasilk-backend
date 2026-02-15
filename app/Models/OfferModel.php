<?php

namespace App\Models;

use CodeIgniter\Model;

class OfferModel extends Model
{
    protected $table            = 'offers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['code', 'type', 'discount', 'min_spend', 'description', 'target_category'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'code'      => 'required|max_length[50]|is_unique[offers.code,id,{id}]',
        'type'      => 'required|in_list[percentage,fixed,shipping]',
        'discount'  => 'required|decimal',
        'min_spend' => 'permit_empty|decimal',
    ];
}
