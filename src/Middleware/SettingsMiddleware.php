<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Kpzsproductions\Challengify\Models\User;
use Kpzsproductions\Challengify\Services\TranslationService;

/**
 * Middleware to apply user settings throughout the application
 */
class SettingsMiddleware implements MiddlewareInterface
{
    private TranslationService $translationService;
    
    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Store the request in a global variable for use in helper functions
        $_SERVER['REQUEST'] = $request;
        
        // Get user from request (set by authentication middleware)
        $user = $request->getAttribute('user');
        
        if ($user instanceof User && $user->isLoggedIn()) {
            // Apply language setting
            $language = $user->getLanguage() ?? 'en';
            $this->translationService->setLocale($language);
            
            // If auto timezone is enabled and timezone cookie exists, update user timezone
            if ($user->getAutoTimezone() && isset($_COOKIE['timezone'])) {
                $browserTimezone = $_COOKIE['timezone'];
                $validTimezones = \DateTimeZone::listIdentifiers();
                if (in_array($browserTimezone, $validTimezones) && $browserTimezone !== $user->getTimezone()) {
                    // Update user timezone in the database
                    $user->setTimezone($browserTimezone);
                }
            }
            
            // Set user timezone for the current request
            date_default_timezone_set($user->getTimezone() ?? 'UTC');
            
            // Make translation service available in views
            $request = $request->withAttribute('translationService', $this->translationService);
            
            // Also make it available as a global variable as a fallback
            $GLOBALS['translationService'] = $this->translationService;
        } else {
            // For non-logged in users, use default timezone and language
            date_default_timezone_set('UTC');
            $this->translationService->setLocale('en');
            $request = $request->withAttribute('translationService', $this->translationService);
            
            // Also make it available as a global variable as a fallback
            $GLOBALS['translationService'] = $this->translationService;
        }
        
        return $handler->handle($request);
    }
} 