<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Middleware;

use Kpzsproductions\Challengify\Services\SecurityService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class InputSanitizationMiddleware implements MiddlewareInterface
{
    private SecurityService $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Sanitize query parameters
        $queryParams = $this->securityService->sanitizeInput($request->getQueryParams());
        $request = $request->withQueryParams($queryParams);
        
        // Sanitize parsed body
        $parsedBody = $this->securityService->sanitizeInput($request->getParsedBody());
        $request = $request->withParsedBody($parsedBody);
        
        // Sanitize uploaded files (just pass through for now)
        $uploadedFiles = $request->getUploadedFiles();
        
        // Sanitize cookies
        $cookies = $this->securityService->sanitizeInput($request->getCookieParams());
        $request = $request->withCookieParams($cookies);
        
        // Sanitize server params
        $serverParams = $this->securityService->sanitizeInput($request->getServerParams());
        
        // Process the sanitized request
        return $handler->handle($request);
    }
} 