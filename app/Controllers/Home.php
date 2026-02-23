<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SHOW COLUMNS FROM products");
        $columns = $query->getResultArray();
        echo json_encode([
            'database' => $db->getDatabase(),
            'columns' => $columns
        ]);
        exit;
    }
}
