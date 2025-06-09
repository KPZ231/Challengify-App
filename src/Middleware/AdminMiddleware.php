<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;

class AdminMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // For API requests, user is set in the request attributes by JwtMiddleware
        // For web requests, user role is stored in the session
        
        // First check if we have a JWT authenticated user (API)
        $user = $request->getAttribute('user');
        
        // If no JWT user, check session (web)
        if (!$user) {
            if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
                return $this->accessDenied($request);
            }
            
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
                return $this->accessDenied($request);
            }
        } else {
            // We have a JWT user, check if they're an admin
            // Check if it's an array (from JWT) or User object
            if (is_array($user)) {
                if ($user['role'] !== 'admin') {
                    return $this->accessDenied($request);
                }
            } else {
                // It's a User object
                if ($user->getRole() !== 'admin') {
                    return $this->accessDenied($request);
                }
            }
        }
        
        return $handler->handle($request);
    }
    
    private function accessDenied(ServerRequestInterface $request): ResponseInterface
    {
        // Check if this is an API or web request
        if (strpos($request->getUri()->getPath(), '/api/') === 0) {
            // API request, return JSON response
            return new JsonResponse([
                'success' => false,
                'message' => 'Forbidden: Admin access required'
            ], 403);
        } else {
            // Web request, redirect to login page with error
            setFlash('error', 'Admin access required');
            return new RedirectResponse('/login');
        }
    }
} 