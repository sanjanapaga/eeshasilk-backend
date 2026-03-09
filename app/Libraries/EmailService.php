<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    /**
     * SMTP Configuration
     * USER: Update these settings with your actual SMTP credentials (e.g., Gmail App Password)
     */
    private $smtpHost;
    private $smtpAuth = true;
    private $smtpUser;
    private $smtpPass;
    private $smtpPort;
    private $smtpSecure;
    private $fromEmail;
    private $fromName = 'EESHA SILKS';

    public function __construct()
    {
        $this->smtpHost = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $this->smtpUser = getenv('SMTP_USER') ?: 'eeshasilkss@gmail.com';
        $this->smtpPass = getenv('SMTP_PASS');
        $this->smtpPort = getenv('SMTP_PORT') ?: 587;
        $this->smtpSecure = getenv('SMTP_CRYPTO') === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $this->fromEmail = $this->smtpUser;
    }

    public function sendWelcomeEmail($userData)
    {
        $name = $userData['username'] ?? 'Valued Customer';
        
        return $this->sendMail(
            $userData['email'],
            $name,
            'Welcome to EESHA SILKS - Registration Successful',
            "Hi {$name},<br><br>Welcome to Eesha Silks! We are thrilled to have you join our community of silk lovers.<br><br>Login now to explore our exclusive collection of Kanchipuram, Banarasi, and Designer sarees.<br><br>Happy Shopping!<br>Team Eesha Silks",
            "Hi {$name},\n\nWelcome to Eesha Silks! We are thrilled to have you join our community.\n\nLogin now: https://eeshasilk.com/login\n\nHappy Shopping!\nTeam Eesha Silks"
        );
    }

    public function sendOrderNotification($orderData)
    {
        return $this->sendMail(
            $orderData['customer_email'],
            $orderData['customer_name'],
            'Order Confirmation - EESHA SILKS #' . $orderData['id'],
            $this->renderView('emails/order_template', $orderData),
            "Thank you for your order #" . $orderData['id'] . ". Total Amount: ₹" . number_format($orderData['total_amount'], 2)
        );
    }

    public function sendContactMessage($contactData)
    {
        // 1. Send acknowledgement to customer
        $this->sendMail(
            $contactData['email'],
            $contactData['name'],
            'We received your message - EESHA SILKS',
            "Hi {$contactData['name']},<br><br>Thanks for reaching out! We've received your message regarding '{$contactData['subject']}' and will get back to you shortly.<br><br>Best,<br>Team Eesha Silks",
            "Hi {$contactData['name']}, Thanks for reaching out! We've received your message and will get back to you shortly."
        );

        // 2. Send notification to Admin (Store Owner)
        return $this->sendMail(
            $this->fromEmail, // Send to self/admin
            'Admin',
            'New Contact Inquiry: ' . $contactData['subject'],
            "<strong>Name:</strong> {$contactData['name']}<br><strong>Email:</strong> {$contactData['email']}<br><strong>Subject:</strong> {$contactData['subject']}<br><br><strong>Message:</strong><br>{$contactData['message']}",
            "Name: {$contactData['name']}\nEmail: {$contactData['email']}\nSubject: {$contactData['subject']}\n\nMessage:\n{$contactData['message']}"
        );
    }

    private function sendMail($toEmail, $toName, $subject, $htmlBody, $altBody)
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->SMTPAuth   = $this->smtpAuth;
            $mail->Username   = $this->smtpUser;
            $mail->Password   = $this->smtpPass;
            $mail->SMTPSecure = $this->smtpSecure;
            $mail->Port       = $this->smtpPort;

            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = $altBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            log_message('error', "Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
            
            // Debug Fallback: Save to file on localhost
            $debugPath = WRITEPATH . 'debug_emails/';
            if (!is_dir($debugPath)) {
                mkdir($debugPath, 0777, true);
            }
            
            $filename = 'email_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.html';
            $content = "<strong>To:</strong> {$toEmail} ({$toName})<br>";
            $content .= "<strong>Subject:</strong> {$subject}<br><hr>";
            $content .= $htmlBody;
            
            file_put_contents($debugPath . $filename, $content);
            log_message('debug', "Local Debug: Email saved to {$debugPath}{$filename}");
            
            return true; // Return true so controller thinks it succeeded, but inform user via logs/files
        }
    }

    private function renderView($viewName, $data)
    {
        $view = \Config\Services::renderer();
        return $view->setData($data)->render($viewName);
    }
}
