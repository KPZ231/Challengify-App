<?php

namespace Kpzsproductions\Challengify\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Kpzsproductions\Challengify\Services\SecurityService;
use Kpzsproductions\Challengify\Models\User;
use Kpzsproductions\Challengify\Models\Contact;


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
        
        try {
            // Get the client IP address
            $ip = $request->getServerParams()['REMOTE_ADDR'] ?? '0.0.0.0';
            
            // Create new contact message in the database
            Contact::create(
                $formData['name'],
                $formData['email'],
                $formData['subject'],
                $formData['message'],
                $ip
            );
            
            // TODO: Send email notification to admin (optional)
            
            // Regenerate CSRF token to prevent form resubmission
            $this->securityService->generateToken();
            
            // Redirect with success message
            $response = new Response\RedirectResponse('/contact?success=true');
            return $response;
        } catch (\Exception $e) {
            // Log the error
            error_log('Error saving contact form: ' . $e->getMessage());
            
            // Redirect with generic error message
            $response = new Response\RedirectResponse('/contact?error=server_error');
            return $response;
        }
    }
}