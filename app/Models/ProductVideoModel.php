<?php
 
namespace App\Models;
 
use CodeIgniter\Model;
 
class ProductVideoModel extends Model
{
    protected $table            = 'product_videos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['product_id', 'video_path'];
 
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
 
    // Validation
    protected $validationRules      = [
        'product_id' => 'required|integer',
        'video_path' => 'required|max_length[255]',
    ];
}
