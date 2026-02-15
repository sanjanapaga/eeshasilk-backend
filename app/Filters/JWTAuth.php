<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getServer('HTTP_AUTHORIZATION') ?? $request->getHeaderLine('Authorization');
        
        if (!$header) {
            return service('response')->setJSON([
                'status'  => 401,
                'message' => 'Token Required'
            ])->setStatusCode(401);
        }

        $token = str_replace('Bearer ', '', $header);

        try {
            $key = getenv('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            
            // Add user data to request object for use in controllers
            $request->user = $decoded;
            
        } catch (Exception $e) {
            return service('response')->setJSON([
                'status'  => 401,
                'message' => 'Invalid or Expired Token'
            ])->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
