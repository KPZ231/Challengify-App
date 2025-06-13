<?php

namespace Kpzsproductions\Challengify\Controllers;

use Kpzsproductions\Challengify\Services\ReportService;

class ReportController
{
    private $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
        
        // Check if user is logged in
        if (!isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        
        // Set secure headers
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Generate a nonce for CSP to allow safe inline scripts/styles
        if (empty($_SESSION['csp_nonce'])) {
            $_SESSION['csp_nonce'] = bin2hex(random_bytes(16));
        }
        $cspNonce = $_SESSION['csp_nonce'];

        // Set Content-Security-Policy with nonce for inline scripts and styles
        header(
            "Content-Security-Policy: "
            . "default-src 'self'; "
            . "script-src 'self' 'nonce-{$cspNonce}'; "
            . "style-src 'self' https://fonts.googleapis.com 'nonce-{$cspNonce}'; "
            . "font-src 'self' https://fonts.gstatic.com"
        );

        // Make nonce available to views (if using a view system)
        $GLOBALS['cspNonce'] = $cspNonce;
    }

    public function index()
    {
        // Get current user ID
        $userId = $_SESSION['user_id'];
        
        // Default to today's reports
        $period = $_GET['period'] ?? 'today';
        $customDate = $_GET['date'] ?? null;
        
        // Generate CSRF token
        $csrfToken = $this->generateCsrfToken();
        
        // Get report data
        $reportData = $this->reportService->getReportData($userId, $period, $customDate);
        
        // Render the view
        require_once __DIR__ . '/../Views/reports.php';
    }
    
    private function generateCsrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    public function validateCsrfToken($token)
    {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            http_response_code(403);
            die('CSRF token validation failed');
        }
        
        return true;
    }
} 