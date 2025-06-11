<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Kpzsproductions\Challengify\Models\User;
use Kpzsproductions\Challengify\Services\SecurityService;
use Kpzsproductions\Challengify\Services\Database;  
use Medoo\Medoo;

class HomeController
{
    private User $user;
    private SecurityService $securityService;
    private Database $db;

    public function __construct(User $user, SecurityService $securityService)
    {
        $this->user = $user;
        $this->securityService = $securityService;
        $this->db = new Database();
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
        require __DIR__ . '/../Views/home.php';
        
        // Get buffered content
        $content = ob_get_clean();
        
        // Write content to response body
        $response->getBody()->write($content);
        
        return $response->withHeader('Content-Type', 'text/html');
    }

    /**
     * Calculate time difference between challenge start and end dates
     * 
     * @param string $challengeId The ID of the challenge
     * @return int|null Hours between start and end date, or null if dates not found
     */
    public function readTime(string $challengeId): ?int 
    {
        $challenge = Database::getInstance()->select('challenges', 
            ['start_date', 'end_date'],
            ['id' => $challengeId]
        );

        if (!$challenge) {
            return null;
        }

        $startDate = new \DateTime($challenge['start_date']);
        $endDate = new \DateTime($challenge['end_date']);
        
        $interval = $startDate->diff($endDate);
        
        // Convert to total hours
        $hours = ($interval->days * 24) + $interval->h;
        
        return $hours;
    }
}
