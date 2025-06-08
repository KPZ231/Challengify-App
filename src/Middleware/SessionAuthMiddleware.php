<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Middleware;

use Kpzsproductions\Challengify\Models\User;
use Medoo\Medoo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionAuthMiddleware implements MiddlewareInterface
{
    private Medoo $db;
    private User $user;

    public function __construct(Medoo $db, User $user)
    {
        $this->db = $db;
        $this->user = $user;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in via session
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
            // Get user from database
            $userData = $this->db->get('users', '*', ['id' => $_SESSION['user_id']]);
            
            if ($userData) {
                // Update the User model with logged-in user data
                $this->user = new User(
                    (int)$userData['id'],
                    $userData['username'],
                    $userData['email'],
                    '',  // Don't include password for security
                    $userData['role'],
                    $userData['avatar'] ?? null,
                    new \DateTime($userData['created_at']),
                    new \DateTime($userData['updated_at'] ?? $userData['created_at'])
                );
                $this->user->setLoggedIn(true);
                
                // Update the last activity timestamp
                $_SESSION['last_activity'] = time();
            }
        }

        // Pass the updated user to the container
        $request = $request->withAttribute('user', $this->user);
        
        // Continue with request handling
        return $handler->handle($request);
    }
} 