<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Libraries\EmailService;

class TestEmailController extends ResourceController
{
    public function sendTest()
    {
        $emailService = new EmailService();
        
        $testData = [
            'id' => 'TEST-001',
            'customer_name' => 'Boutique Owner',
            'customer_email' => 'eeshasilkss@gmail.com',
            'items' => [
                ['product_name' => 'Heritage Kanchipuram Silk Saree', 'quantity' => 1, 'price' => 25000],
                ['product_name' => 'Premium Silk Designer Kurta', 'quantity' => 1, 'price' => 12500]
            ],
            'subtotal' => 37500,
            'discount' => 0,
            'delivery_fee' => 0,
            'total_amount' => 37500
        ];

        $result = $emailService->sendOrderNotification($testData);

        if ($result) {
            return $this->respond(['status' => 200, 'message' => 'Test email sent successfully! Check your inbox.']);
        } else {
            return $this->fail('Failed to send test email. Check your backend/writable/logs for details.');
        }
    }

    public function preview()
    {
        $testData = [
            'id' => 'PREVIEW-001',
            'customer_name' => 'Boutique Patron',
            'customer_email' => 'customer@example.com',
            'items' => [
                ['product_name' => 'Heritage Kanchipuram Silk Saree', 'quantity' => 1, 'price' => 25000],
                ['product_name' => 'Premium Silk Designer Kurta', 'quantity' => 1, 'price' => 12500]
            ],
            'subtotal' => 37500,
            'discount' => 2500,
            'delivery_fee' => 0,
            'total_amount' => 35000
        ];

        return view('emails/order_template', $testData);
    }
}
