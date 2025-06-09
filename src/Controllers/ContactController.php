<?php

namespace Kpzsproductions\Challengify\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Kpzsproductions\Challengify\Services\SecurityService;
use Kpzsproductions\Challengify\Models\User;


class ContactController
{
    private SecurityService $securityService;
    private User $user;

    public function __construct(SecurityService $securityService, User $user)
    {
        $this->securityService = $securityService;
        $this->user = $user;
    }
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        // Create new response
        $response = new Response();
        
        // Start output buffering
        ob_start();
        
        // Extract user data from request attributes (set by SessionAuthMiddleware)
        $user = $request->getAttribute('user', $this->user);
        
        // Pass CSRF token to view
        $csrfToken = $this->securityService->generateToken();
        
        // Set security headers
        $response = $response->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('X-XSS-Protection', '1; mode=block')
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('Content-Security-Policy', 
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; " .
                "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com fonts.googleapis.com; " .
                "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com; " .
                "img-src 'self' data:;"
            );
        
        // Include the view file
        require __DIR__ . '/../Views/contact.php';
        
        // Get buffered content
        $content = ob_get_clean();
        
        // Write content to response body
        $response->getBody()->write($content);
        
        return $response->withHeader('Content-Type', 'text/html');
    }
    
    public function submit(ServerRequestInterface $request): ResponseInterface
    {
        // Create new response
        $response = new Response();
        
        // Get form data (already sanitized by InputSanitizationMiddleware)
        $formData = $request->getParsedBody();
        
        // Validate CSRF token
        $csrfToken = $formData['csrf_token'] ?? null;
        if (!$this->securityService->validateToken($csrfToken)) {
            // CSRF validation failed
            $response = new Response\RedirectResponse('/contact?error=invalid_token');
            return $response;
        }
        
        // Validate required fields
        $requiredFields = ['name', 'email', 'subject', 'message'];
        foreach ($requiredFields as $field) {
            if (empty($formData[$field])) {
                $response = new Response\RedirectResponse('/contact?error=missing_fields');
                return $response;
            }
        }
        
        // Validate email format
        if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $response = new Response\RedirectResponse('/contact?error=invalid_email');
            return $response;
        }
        
        // Here you would typically:
        // 1. Store the message in the database
        // 2. Send an email notification
        // For demonstration purposes, we'll just redirect with success
        
        // TODO: Store message in database and/or send email
        
        // Redirect with success message
        $response = new Response\RedirectResponse('/contact?success=true');
        return $response;
    }
}