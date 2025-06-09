<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Middleware;

use Kpzsproductions\Challengify\Services\JwtService;
use Kpzsproductions\Challengify\Models\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

class JwtMiddleware implements MiddlewareInterface
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader) || !preg_match('/^Bearer\s+(.*)$/', $authHeader, $matches)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Unauthorized: No token provided'
            ], 401);
        }
        
        $token = $matches[1];
        $payload = $this->jwtService->validate($token);
        
        if ($payload === null) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Unauthorized: Invalid or expired token'
            ], 401);
        }
        
        // Extract user data from token and add to request attributes
        $userData = (array) $payload->data;
        
        // Verify user data contains minimal required fields
        if (!isset($userData['id']) || !isset($userData['username']) || !isset($userData['email'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Unauthorized: Invalid token payload'
            ], 401);
        }
        
        // Add user data to request attributes
        $request = $request->withAttribute('jwt_user', $userData);
        $request = $request->withAttribute('authenticated', true);
                  
        return $handler->handle($request);
    }
} 