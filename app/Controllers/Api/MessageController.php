<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\MessageModel;

class MessageController extends ResourceController
{
    protected $modelName = 'App\Models\MessageModel';
    protected $format    = 'json';

    public function create()
    {
        $data = $this->request->getJSON(true); // Always try to get JSON as array first
        
        if (!$data) {
             $data = $this->request->getPost(); // Fallback to POST data array
        }

        // Sanitize Subject to prevent promotional injections
        if (isset($data['subject'])) {
            $suspicious = ['Weekend Sale', 'Sale Starts', 'OFF using code'];
            foreach ($suspicious as $term) {
                if (stripos($data['subject'], $term) !== false) {
                    $data['subject'] = 'General Inquiry';
                    break;
                }
            }
        }

        if (!$this->model->insert($data)) {
            return $this->fail($this->model->errors());
        }

        // Send Email Notification - wrapped in try-catch to prevent 500 errors if SMTP fails
        try {
            $emailService = new \App\Libraries\EmailService();
            $emailService->sendContactMessage($data);
        } catch (\Exception $e) {
            log_message('error', 'Contact message email failed: ' . $e->getMessage());
            // We still proceed to return success so the front-end doesn't show a CORS/500 error
        }

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Message sent successfully'
        ]);
    }

    public function index()
    {
        return $this->respond([
            'status'   => 200,
            'messages' => $this->model->findAll()
        ]);
    }
}
