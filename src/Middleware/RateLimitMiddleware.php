<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Middleware;

use Kpzsproductions\Challengify\Services\RateLimiterService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

class RateLimitMiddleware implements MiddlewareInterface
{
    private RateLimiterService $rateLimiter;
    private string $key;

    public function __construct(RateLimiterService $rateLimiter, string $key)
    {
        $this->rateLimiter = $rateLimiter;
        $this->key = $key;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? '0.0.0.0';
        
        // Check if the client has exceeded the rate limit
        if ($this->rateLimiter->tooManyAttempts($this->key, $ip)) {
            $retryAfter = 60; // 1 minute
            
            $response = new JsonResponse([
                'success' => false,
                'message' => 'Too many attempts, please try again later.',
                'retries_left' => 0,
                'retry_after' => $retryAfter
            ], 429);
            
            return $response->withHeader('Retry-After', (string) $retryAfter);
        }
        
        // Get the response
        $response = $handler->handle($request);
        
        // Add rate limit headers to the response
        $retriesLeft = $this->rateLimiter->retriesLeft($this->key, $ip);
        
        $response = $response->withHeader('X-RateLimit-Limit', '5');
        $response = $response->withHeader('X-RateLimit-Remaining', (string) $retriesLeft);
        
        // If the request was successful, reset the rate limit for certain endpoints
        if ($response->getStatusCode() === 200 && in_array($this->key, ['login'])) {
            $this->rateLimiter->resetAttempts($this->key, $ip);
        }
        
        return $response;
    }
} 