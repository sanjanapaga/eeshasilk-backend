<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use Razorpay\Api\Api;

class RazorpayController extends ResourceController
{
    private $keyId;
    private $keySecret;

    public function __construct()
    {
        $this->keyId = getenv('RAZORPAY_KEY_ID');
        $this->keySecret = getenv('RAZORPAY_KEY_SECRET');
    }

    public function createOrder()
    {
        $amount = $this->request->getVar('amount'); // In rupees
        
        $api = new Api($this->keyId, $this->keySecret);

        $orderData = [
            'receipt'         => 'rcpt_' . time(),
            'amount'          => $amount * 100, // Razorpay amount is in paisa
            'currency'        => 'INR',
            'payment_capture' => 1 // Auto-capture payment
        ];

        try {
            $razorpayOrder = $api->order->create($orderData);
            return $this->respond([
                'id' => $razorpayOrder['id'],
                'amount' => $razorpayOrder['amount'],
                'key' => $this->keyId
            ]);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function verifyPayment()
    {
        $razorpayPaymentId = $this->request->getVar('razorpay_payment_id');
        $razorpayOrderId = $this->request->getVar('razorpay_order_id');
        $razorpaySignature = $this->request->getVar('razorpay_signature');

        $api = new Api($this->keyId, $this->keySecret);

        try {
            $attributes = [
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature
            ];
            $api->utility->verifyPaymentSignature($attributes);
            
            return $this->respond(['status' => 'success', 'message' => 'Payment verified']);
        } catch (\Exception $e) {
            return $this->fail('Payment verification failed: ' . $e->getMessage());
        }
    }
}
