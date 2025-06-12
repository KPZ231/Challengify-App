<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Kpzsproductions\Challengify\Models\User;

class MailService
{
    private static ?MailService $instance = null;
    private PHPMailer $mailer;
    
    private function __construct()
    {
        $this->mailer = new PHPMailer(true);
        
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['MAIL_HOST'] ?? 'sandbox.smtp.mailtrap.io';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Port = (int)($_ENV['MAIL_PORT'] ?? 2525);
        $this->mailer->Username = $_ENV['MAIL_USERNAME'] ?? null;
        $this->mailer->Password = $_ENV['MAIL_PASSWORD'] ?? null;
        
        // Only set SMTPSecure if specified in .env
        if (!empty($_ENV['MAIL_ENCRYPTION'])) {
            $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
        }
        
        // Default sender
        $this->mailer->setFrom(
            $_ENV['MAIL_FROM_ADDRESS'] ?? 'hello@challengify.com',
            $_ENV['MAIL_FROM_NAME'] ?? 'Challengify'
        );
        
        $this->mailer->isHTML(true);
    }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Send an email to a user
     */
    public function sendToUser(User $user, string $subject, string $body, string $plainTextBody = ''): bool
    {
        // Don't send emails to users who have disabled email notifications
        if (!$user->getNotificationEmail()) {
            return false;
        }
        
        return $this->send($user->getEmail(), $subject, $body, $plainTextBody);
    }
    
    /**
     * Send an email to a specified address
     */
    public function send(string $to, string $subject, string $body, string $plainTextBody = ''): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            
            if (empty($plainTextBody)) {
                $plainTextBody = strip_tags($body);
            }
            
            $this->mailer->AltBody = $plainTextBody;
            
            return $this->mailer->send();
        } catch (Exception $e) {
            // Log the error
            error_log('Mail error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generates an HTML email template with the provided content
     */
    public function generateEmailTemplate(string $title, string $content): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $title . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    background-color: #3490dc;
                    padding: 20px;
                    text-align: center;
                }
                .header h1 {
                    color: white;
                    margin: 0;
                }
                .content {
                    padding: 20px;
                    background-color: #f8fafc;
                }
                .footer {
                    text-align: center;
                    padding: 20px;
                    font-size: 12px;
                    color: #718096;
                }
                .button {
                    display: inline-block;
                    background-color: #3490dc;
                    color: white;
                    text-decoration: none;
                    padding: 10px 20px;
                    border-radius: 4px;
                    margin: 20px 0;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Challengify</h1>
                </div>
                <div class="content">
                    ' . $content . '
                </div>
                <div class="footer">
                    <p>© ' . date('Y') . ' Challengify. All rights reserved.</p>
                    <p>
                        If you no longer wish to receive these emails, you can 
                        <a href="https://challengify.com/settings/notifications">change your notification settings</a>.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ';
    }
} 