<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Middleware;

use Kpzsproductions\Challengify\Models\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\RedirectResponse;

class AuthMiddleware implements MiddlewareInterface
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Get user from the request attributes (set by SessionAuthMiddleware)
        $user = $request->getAttribute('user', $this->user);
        
        // Check if user is logged in
        if (!$user->isLoggedIn()) {
            // Store the originally requested URL for redirect after login
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['redirect_after_login'] = $request->getUri()->getPath();
            
            // Redirect to login page
            return new RedirectResponse('/login');
        }
        
        // User is authenticated, continue with request handling
        return $handler->handle($request);
    }
} 