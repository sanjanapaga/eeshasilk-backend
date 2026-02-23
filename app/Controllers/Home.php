<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        return $this->respond([
            'status' => 'success',
            'message' => 'EeshaSilk Backend is running',
            'environment' => CI_ENVIRONMENT,
            'database' => 'connected'
        ]);
    }

    /**
     * Helper to run migrations from browser
     * URL: https://eeshasilk.com/api/backend/public/migrate
     */
    public function migrate()
    {
        $migrate = \Config\Services::migrations();

        try {
            if ($migrate->latest()) {
                return $this->respond(['status' => 'success', 'message' => 'Migrations completed successfully']);
            }
        } catch (\Throwable $e) {
            return $this->respond(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
