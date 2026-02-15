<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Libraries\EmailService;

class AuthController extends ResourceController
{
    protected $format = 'json';

    public function register()
    {
        try {
            // Get data from request
            $json = $this->request->getJSON(true);
            
            // Fallback for manual parsing if getJSON() is null but body exists
            if ($json === null) {
                $rawBody = $this->request->getBody();
                if (!empty($rawBody)) {
                    $json = json_decode($rawBody, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        return $this->fail('Invalid JSON: ' . json_last_error_msg());
                    }
                } else {
                    return $this->fail('Empty request body');
                }
            }

            $rules = [
                'username' => 'permit_empty|min_length[3]|max_length[100]',
                'name'     => 'permit_empty|min_length[3]|max_length[100]',
                'email'    => 'required|valid_email|is_unique[users.email]|max_length[100]',
                'password' => 'required|min_length[6]|max_length[255]',
                'role'     => 'permit_empty|in_list[admin,user]',
            ];

            // Use the data extracted from JSON for validation
            if (!$this->validateData($json, $rules)) {
                return $this->fail($this->validator->getErrors());
            }

            $username = $json['username'] ?? $json['name'] ?? null;
            
            if (empty($username)) {
                return $this->fail(['username' => 'The name or username field is required.']);
            }

            $model = new UserModel();
            $data = [
                'username' => $username,
                'email'    => $json['email'],
                'password' => password_hash($json['password'], PASSWORD_DEFAULT),
                'role'     => $json['role'] ?? 'user',
            ];

            $id = $model->insert($data);

            if (!$id) {
                return $this->fail($model->errors() ?: 'Failed to register user.');
            }

            $data['id'] = $id;
            unset($data['password']);

            // Generate JWT Token
            $key = getenv('JWT_SECRET');
            $iat = time();
            $exp = $iat + (int)getenv('JWT_EXPIRATION');

            $payload = [
                'iat'  => $iat,
                'exp'  => $exp,
                'uid'  => $id,
                'email'=> $data['email'],
                'role' => $data['role'],
            ];

            $token = JWT::encode($payload, $key, 'HS256');

            // Send Welcome Email
            try {
                $emailService = new EmailService();
                $emailService->sendWelcomeEmail($data);
            } catch (\Exception $e) {
                log_message('error', 'Welcome email failed: ' . $e->getMessage());
            }

            return $this->respondCreated([
                'status'  => 201,
                'message' => 'User registered successfully',
                'user'    => $data,
                'token'   => $token
            ]);
        } catch (\Exception $e) {
            return $this->fail('Registration Error: ' . $e->getMessage());
        }
    }

    public function login()
    {
        try {
            $json = $this->request->getJSON(true);
            
            // Fallback for manual parsing
            if ($json === null) {
                $rawBody = $this->request->getBody();
                if (!empty($rawBody)) {
                    $json = json_decode($rawBody, true);
                }
            }

            $login    = $json['email'] ?? $this->request->getVar('email');
            $password = $json['password'] ?? $this->request->getVar('password');

            if (empty($login) || empty($password)) {
                return $this->fail('Email/Username and password are required');
            }

            $model = new UserModel();
            $user  = $model->where('email', $login)
                           ->orWhere('username', $login)
                           ->first();

            if (!$user || !password_verify($password, $user['password'])) {
                return $this->failUnauthorized('Invalid email/username or password');
            }

            // Generate JWT Token
            $key = getenv('JWT_SECRET');
            $iat = time();
            $exp = $iat + (int)getenv('JWT_EXPIRATION');

            $payload = [
                'iat'  => $iat,
                'exp'  => $exp,
                'uid'  => $user['id'],
                'email'=> $user['email'],
                'role' => $user['role'],
            ];

            $token = JWT::encode($payload, $key, 'HS256');

            unset($user['password']);

            return $this->respond([
                'status'  => 200,
                'message' => 'Login successful',
                'user'    => $user,
                'token'   => $token
            ]);
        } catch (\Exception $e) {
            return $this->fail('Login Error: ' . $e->getMessage());
        }
    }
}
