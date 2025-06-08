<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Services;

use Psr\Http\Message\ResponseInterface;

class CacheService
{
    private int $defaultTtl;

    public function __construct(int $defaultTtl = 3600)
    {
        $this->defaultTtl = $defaultTtl;
    }

    public function withCacheHeaders(ResponseInterface $response, int $ttl = null): ResponseInterface
    {
        $ttl = $ttl ?? $this->defaultTtl;
        
        return $response
            ->withHeader('Cache-Control', 'public, max-age=' . $ttl)
            ->withHeader('Expires', gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT')
            ->withHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT')
            ->withHeader('Pragma', 'public');
    }

    public function withETag(ResponseInterface $response, string $content): ResponseInterface
    {
        $etag = md5($content);
        return $response->withHeader('ETag', '"' . $etag . '"');
    }

    public function isNotModified(array $serverParams, string $etag): bool
    {
        $ifNoneMatch = $serverParams['HTTP_IF_NONE_MATCH'] ?? '';
        return $ifNoneMatch === '"' . $etag . '"';
    }
} 