<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table            = 'messages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'email', 'subject', 'message'];

    // Dates
    protected $useTimestamps = false;
    protected $beforeInsert  = ['setCreatedAt'];

    protected function setCreatedAt(array $data)
    {
        $data['data']['created_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    // Validation
    protected $validationRules      = [
        'name'    => 'required|max_length[100]',
        'email'   => 'required|valid_email|max_length[100]',
        'subject' => 'required|max_length[255]',
        'message' => 'required',
    ];
}
