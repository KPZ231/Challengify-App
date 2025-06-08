<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

class AdminMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Get user from request attributes (set by JwtMiddleware)
        $user = $request->getAttribute('user');
        
        if (!$user || $user['role'] !== 'admin') {
            return new JsonResponse([
                'success' => false,
                'message' => 'Forbidden: Admin access required'
            ], 403);
        }
        
        return $handler->handle($request);
    }
} 