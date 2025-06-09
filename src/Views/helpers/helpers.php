<?php

declare(strict_types=1);

use Kpzsproductions\Challengify\Services\Database;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Render a view
 */
function view(string $view, array $data = []): ResponseInterface
{
    // Extract data to make variables available in the view
    extract($data);
    
    // Start output buffering
    ob_start();
    
    // Include the view file
    $viewPath = __DIR__ . '/../../Views/' . $view . '.php';
    if (file_exists($viewPath)) {
        include $viewPath;
    } else {
        throw new Exception("View {$view} not found");
    }
    
    // Get the buffered content
    $content = ob_get_clean();
    
    // Create and return a response
    $response = new Response();
    $response->getBody()->write($content);
    return $response->withHeader('Content-Type', 'text/html');
}

/**
 * Redirect to a URL
 */
function redirect(string $url): ResponseInterface
{
    $response = new Response();
    return $response
        ->withStatus(302)
        ->withHeader('Location', $url);
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Get current user
 */
function currentUser()
{
    if (!isLoggedIn()) {
        return null;
    }
    
    return new \Kpzsproductions\Challengify\Models\User(
        $_SESSION['user_id'],
        $_SESSION['username'],
        $_SESSION['email'],
        $_SESSION['password'],
        $_SESSION['role'] ?? 'user',
        $_SESSION['avatar'] ?? null,
        isset($_SESSION['created_at']) ? new \DateTime($_SESSION['created_at']) : null,
        isset($_SESSION['updated_at']) ? new \DateTime($_SESSION['updated_at']) : null
    );
}

/**
 * Set flash message
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Get flash message
 */
function getFlash(string $type): ?string
{
    if (!isset($_SESSION['flash'][$type])) {
        return null;
    }
    
    $message = $_SESSION['flash'][$type];
    unset($_SESSION['flash'][$type]);
    
    return $message;
}

/**
 * Generate a CSRF token
 */
function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf_token(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if flash message exists
 */
function hasFlash(string $type): bool
{
    return isset($_SESSION['flash'][$type]);
}

/**
 * Generate a UUID v4
 */
function generateUuid(): string
{
    return Ramsey\Uuid\Uuid::uuid4()->toString();
}

/**
 * Format date
 */
function formatDate(string $date, string $format = 'Y-m-d H:i'): string
{
    return (new DateTime($date))->format($format);
}

/**
 * Get time ago
 */
function timeAgo(string $datetime): string
{
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    } else {
        $years = floor($diff / 31536000);
        return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
    }
}

/**
 * Escape HTML
 */
function e(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Truncate text
 */
function truncate(string $text, int $length = 100): string
{
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . '...';
} 