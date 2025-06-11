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
            // Configure secure session cookies
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            $httpOnly = true;
            $sameSite = 'Lax';
            $path = '/';
            
            // Set secure cookie parameters
            session_set_cookie_params([
                'lifetime' => 7200, // 2 hours
                'path' => $path, 
                'domain' => '', // current domain
                'secure' => $secure,
                'httponly' => $httpOnly,
                'samesite' => $sameSite
            ]);
            
            session_start();
            
            // Set session timeout
            if (!isset($_SESSION['created_at'])) {
                $_SESSION['created_at'] = time();
            }
            
            // Expire session after 2 hours of inactivity
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
                session_unset();
                session_destroy();
                session_start(); // Start a new session
            }
        }

        // Check if user is logged in via session
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
            // Get user from database with proper validation
            $userData = $this->db->get('users', '*', ['id' => $_SESSION['user_id']]);
            
            if ($userData && isset($userData['id'], $userData['username'], $userData['email'], $userData['role'])) {
                // Validate account status before proceeding
                if (isset($userData['status']) && $userData['status'] !== 'active') {
                    // Account is suspended or inactive - force logout
                    session_unset();
                    session_destroy();
                } else {
                    // Create new User instance with validated data
                    $user = new User(
                        $userData['id'],  // No casting to integer since we're using UUIDs
                        $userData['username'],
                        $userData['email'],
                        '',  // Don't include password for security
                        $userData['role'],
                        $userData['avatar'] ?? null,
                        $userData['bio'] ?? null,
                        new \DateTime($userData['created_at']),
                        new \DateTime($userData['updated_at'] ?? $userData['created_at']),
                        (bool)($userData['notification_email'] ?? true),
                        (bool)($userData['notification_push'] ?? false),
                        (bool)($userData['notification_sms'] ?? false),
                        $userData['notification_time'] ?? '18:00',
                        (bool)($userData['weekly_summary'] ?? true),
                        (bool)($userData['monthly_summary'] ?? false),
                        $userData['profile_visibility'] ?? 'public',
                        $userData['messaging_permission'] ?? 'all',
                        $userData['language'] ?? 'en',
                        $userData['timezone'] ?? 'UTC',
                        (bool)($userData['auto_timezone'] ?? true)
                    );
                    $user->setLoggedIn(true);
                    
                    // Replace current user with authenticated user
                    $this->user = $user;
                    
                    // Update the last activity timestamp
                    $_SESSION['last_activity'] = time();
                }
            }
        }

        // Pass the updated user to the container
        $request = $request->withAttribute('user', $this->user);
        
        // Continue with request handling
        return $handler->handle($request);
    }
} 