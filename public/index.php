<?php

declare(strict_types=1);

$basePath = require_once dirname(__DIR__) . '/bootstrap.php';

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response;
use FastRoute\RouteCollector;
use Kpzsproductions\Challengify\Controllers\HomeController;
use Kpzsproductions\Challengify\Controllers\AuthController;
use Kpzsproductions\Challengify\Controllers\ChallengesController;
use Kpzsproductions\Challengify\Controllers\UserController;
use Kpzsproductions\Challengify\Controllers\AdminController;
use Kpzsproductions\Challengify\Controllers\ContactController;
use Kpzsproductions\Challengify\Controllers\VoteController;
use Kpzsproductions\Challengify\Controllers\CommunityController;
use Kpzsproductions\Challengify\Controllers\ReportController;
use Kpzsproductions\Challengify\Middleware\JwtMiddleware;
use Kpzsproductions\Challengify\Middleware\RateLimitMiddleware;
use Kpzsproductions\Challengify\Middleware\InputSanitizationMiddleware;
use Kpzsproductions\Challengify\Middleware\SessionAuthMiddleware;
use Kpzsproductions\Challengify\Middleware\AuthMiddleware;
use Kpzsproductions\Challengify\Middleware\AdminMiddleware;
use Kpzsproductions\Challengify\Middleware\SettingsMiddleware;
use Kpzsproductions\Challengify\Services\JwtService;
use Kpzsproductions\Challengify\Services\SecurityService;
use Kpzsproductions\Challengify\Services\RateLimiterService;
use Kpzsproductions\Challengify\Services\CacheService;
use Kpzsproductions\Challengify\Services\FileUploadService;
use Kpzsproductions\Challengify\Services\WebSocketService;
use Kpzsproductions\Challengify\Services\TranslationService;
use Kpzsproductions\Challengify\Services\PrivacyService;
use Medoo\Medoo;
use DI\ContainerBuilder;
use Kpzsproductions\Challengify\Controllers\AboutController;
use Kpzsproductions\Challengify\Models\User;
use Relay\Relay;

// Load configuration
$config = require_once __DIR__ . '/../config/app.php';

// Set up dependency injection container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    'config' => $config,
    Medoo::class => function() use ($config) {
        return new Medoo([
            'type' => $config['database']['driver'],
            'host' => $config['database']['host'],
            'database' => $config['database']['database'],
            'username' => $config['database']['username'],
            'password' => $config['database']['password'],
            'charset' => $config['database']['charset'],
            'port' => $config['database']['port'],
        ]);
    },    
    SecurityService::class => function() {
        return new SecurityService();
    },
    JwtService::class => function($c) {
        return new JwtService($c->get('config'));
    },
    RateLimiterService::class => function($c) {
        return new RateLimiterService($c->get(Medoo::class));
    },
    CacheService::class => function() {
        return new CacheService();
    },
    FileUploadService::class => function() {
        return new FileUploadService(__DIR__ . '/../uploads');
    },
    TranslationService::class => function() {
        return new TranslationService();
    },
    PrivacyService::class => function($c) {
        return new PrivacyService($c->get(Medoo::class));
    },
    InputSanitizationMiddleware::class => function($c) {
        return new InputSanitizationMiddleware($c->get(SecurityService::class));
    },
    SessionAuthMiddleware::class => function($c) {
        return new SessionAuthMiddleware($c->get(Medoo::class), $c->get(User::class));
    },
    SettingsMiddleware::class => function($c) {
        return new SettingsMiddleware($c->get(TranslationService::class));
    },
    AuthMiddleware::class => function($c) {
        return new AuthMiddleware($c->get(User::class));
    },
    AdminMiddleware::class => function() {
        return new AdminMiddleware();
    },
    WebSocketService::class => function() {
        return new WebSocketService();
    },
    // Add User model definition
    User::class => function() {
        // Create a guest/default user for non-authenticated requests
        return new User(
            '00000000-0000-0000-0000-000000000000', // default id as UUID string
            'guest',            // default username
            'guest@example.com', // default email
            '',                 // empty password for guest
            'guest',            // guest role
            null,               // no avatar for guest
            null                // no bio for guest
        );
    },
    
    // Controllers with dependencies
    ChallengesController::class => function($c) {
        return new ChallengesController(
            $c->get(FileUploadService::class),
            $c->get(SecurityService::class)
        );
    },
    
    // Vote controller
    VoteController::class => function($c) {
        return new VoteController(
            $c->get(SecurityService::class)
        );
    },
    UserController::class => function($c) {
        return new UserController(
            $c->get(User::class),
            $c->get(SecurityService::class),
            $c->get(FileUploadService::class),
            $c->get(TranslationService::class),
            $c->get(PrivacyService::class),
            $c->get(Medoo::class)
        );
    },
    
    // Report controller and service
    Kpzsproductions\Challengify\Services\ReportService::class => function($c) {
        return new Kpzsproductions\Challengify\Services\ReportService($c->get(Medoo::class));
    },
    
    ReportController::class => function($c) {
        return new ReportController(
            $c->get(Kpzsproductions\Challengify\Services\ReportService::class)
        );
    },
]);

$container = $containerBuilder->build();

// Create the request
$request = ServerRequestFactory::fromGlobals();

// Create the router
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    // Home route
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('GET', '/login', [AuthController::class, 'loadLogin']);
    $r->addRoute('GET', '/register', [AuthController::class, 'loadRegister']);
    
    // Challenges routes
    $r->addRoute('GET', '/challenges', [ChallengesController::class, 'index']);
    $r->addRoute('GET', '/challenges/{id:[^/]+}', [ChallengesController::class, 'show']);
    $r->addRoute('POST', '/challenges/{id:[^/]+}/submit', [ChallengesController::class, 'submitEntry']);
    $r->addRoute('GET', '/challenges/{id:[^/]+}/download-submission', [ChallengesController::class, 'downloadSubmission']);
    
    // Vote routes
    $r->addRoute('POST', '/vote', [VoteController::class, 'vote']);
    $r->addRoute('GET', '/vote/count/{id:[^/]+}', [VoteController::class, 'getVoteCount']);
    $r->addRoute('GET', '/vote/status/{id:[^/]+}', [VoteController::class, 'hasVoted']);
    
    // Reports routes
    $r->addRoute('GET', '/reports', [ReportController::class, 'index']);
    
    // Add POST routes for form submissions
    $r->addRoute('POST', '/login', [AuthController::class, 'processLogin']);
    $r->addRoute('POST', '/register', [AuthController::class, 'processRegister']);
    
    // Add logout route
    $r->addRoute('GET', '/logout', [AuthController::class, 'logout']);
    
    // User profile routes
    $r->addRoute('GET', '/profile', [UserController::class, 'profile']);
    $r->addRoute('POST', '/profile/update-avatar', [UserController::class, 'updateAvatar']);
    $r->addRoute('POST', '/profile/update-username', [UserController::class, 'updateUsername']);
    $r->addRoute('POST', '/profile/update-bio', [UserController::class, 'updateBio']);
    $r->addRoute('GET', '/user/{username:[^/]+}', [UserController::class, 'viewProfile']);
    $r->addRoute('GET', '/user/{username:[^/]+}/follow', [UserController::class, 'toggleFollow']);
    
    // User settings routes
    $r->addRoute('GET', '/settings', [UserController::class, 'settings']);
    $r->addRoute('POST', '/settings/notifications', [UserController::class, 'updateNotificationSettings']);
    $r->addRoute('POST', '/settings/privacy', [UserController::class, 'updatePrivacySettings']);
    $r->addRoute('POST', '/settings/language', [UserController::class, 'updateLanguageSettings']);
    
    // Admin routes
    $r->addRoute('GET', '/admin', [AdminController::class, 'dashboard']);
    $r->addRoute('GET', '/admin/challenges', [AdminController::class, 'challenges']);
    $r->addRoute('GET', '/admin/challenges/create', [AdminController::class, 'createChallengeForm']);
    $r->addRoute('POST', '/admin/challenges/create', [AdminController::class, 'createChallenge']);
    $r->addRoute('GET', '/admin/challenges/edit/{id:[^/]+}', [AdminController::class, 'editChallengeForm']);
    $r->addRoute('POST', '/admin/challenges/update/{id:[^/]+}', [AdminController::class, 'updateChallenge']);
    $r->addRoute('POST', '/admin/challenges/delete/{id:[^/]+}', [AdminController::class, 'deleteChallenge']);
    $r->addRoute('GET', '/admin/users', [AdminController::class, 'users']);
    $r->addRoute('POST', '/admin/users/role/{id:[^/]+}', [AdminController::class, 'updateUserRole']);
    $r->addRoute('GET', '/admin/logs', [AdminController::class, 'logs']);
    $r->addRoute('GET', '/admin/console', [AdminController::class, 'console']);

    $r->addRoute('GET', '/about', [AboutController::class, 'index']);   
    $r->addRoute('GET', '/contact', [ContactController::class, 'index']);
    $r->addRoute('POST', '/contact/submit', [ContactController::class, 'submit']);
    $r->addRoute('GET', '/community', [CommunityController::class, 'index']);

    // Legal routes
    $r->addRoute('GET', '/cookie-policy', function() {
        $response = new \Laminas\Diactoros\Response();
        ob_start();
        require __DIR__ . '/../src/Views/cookie-policy.php';
        $content = ob_get_clean();
        $response->getBody()->write($content);
        return $response;
    });
    $r->addRoute('GET', '/tos', function() {
        $response = new \Laminas\Diactoros\Response();
        ob_start();
        require __DIR__ . '/../src/Views/tos.php';
        $content = ob_get_clean(); 
        $response->getBody()->write($content);
        return $response;
    });
    $r->addRoute('GET', '/privacy-policy', function() {
        $response = new \Laminas\Diactoros\Response();
        $content = file_get_contents(__DIR__ . '/../src/Views/privacy-policy.html');
        $response->getBody()->write($content);
        return $response;
    });
});

// Dispatch the request
$routeInfo = $dispatcher->dispatch(
    $request->getMethod(),
    $request->getUri()->getPath()
);

// Handle the response
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response = new Response();
        $response->getBody()->write(file_get_contents(__DIR__ . '/../src/Views/notfound.html'));
        $response = $response->withStatus(404);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response = new Response();
        $response->getBody()->write(file_get_contents(__DIR__ . '/../src/Views/notallowed.html'));
        $response = $response->withStatus(405);
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        
        // Create middleware queue
        $queue = [];
        
        // Add session auth middleware first to update user data
        $queue[] = $container->get(SessionAuthMiddleware::class);
        
        // Add settings middleware to apply user settings
        $queue[] = $container->get(SettingsMiddleware::class);
        
        // Add input sanitization middleware
        $queue[] = $container->get(InputSanitizationMiddleware::class);
        
        // Add rate limiting for login endpoint
        if ($request->getUri()->getPath() === '/login') {
            $queue[] = new RateLimitMiddleware(
                $container->get(RateLimiterService::class),
                'login'
            );
        }
        
        // Add rate limiting for submissions endpoint
        if (strpos($request->getUri()->getPath(), '/challenges/') === 0 && 
            substr($request->getUri()->getPath(), -7) === '/submit') {
            $queue[] = new RateLimitMiddleware(
                $container->get(RateLimiterService::class),
                'submissions'
            );
        }
        
        // Add rate limiting for contact form
        if ($request->getUri()->getPath() === '/contact/submit') {
            $queue[] = new RateLimitMiddleware(
                $container->get(RateLimiterService::class),
                'contact'
            );
        }
        
        // Add Auth middleware for protected routes
        if (in_array($request->getUri()->getPath(), ['/profile', '/profile/update-avatar', '/profile/update-username', '/profile/update-bio', '/reports']) || 
            (strpos($request->getUri()->getPath(), '/challenges/') === 0 && 
            substr($request->getUri()->getPath(), -7) === '/submit') ||
            (strpos($request->getUri()->getPath(), '/user/') === 0 && 
            strpos($request->getUri()->getPath(), '/follow') !== false) || 
            $request->getUri()->getPath() === '/settings' ||
            strpos($request->getUri()->getPath(), '/settings/') === 0) {
            $queue[] = $container->get(AuthMiddleware::class);
        }
        
        // Add AdminMiddleware for admin routes
        if (strpos($request->getUri()->getPath(), '/admin') === 0) {
            $queue[] = $container->get(AdminMiddleware::class);
        }
        
        // Add JWT middleware for API routes
        if (strpos($request->getUri()->getPath(), '/api/') === 0 && 
            !in_array($request->getUri()->getPath(), ['/api/login', '/api/register'])) {
            $queue[] = new JwtMiddleware(
                $container->get(JwtService::class),
                $container->get(User::class)
            );
        }
        
        // Add final handler
        $queue[] = function ($request, $next) use ($container, $handler, $vars) {
            // Process the request with the appropriate controller method
            if (is_array($handler)) {
                [$controllerName, $method] = $handler;
                $controller = $container->get($controllerName);
                
                $response = empty($vars) ? 
                    $controller->$method($request) : 
                    $controller->$method($request, $vars);
                
                // Add security headers to all responses
                if ($response instanceof Response) {
                    $response = addSecurityHeaders($response);
                }
                
                return $response;
            } else {
                $response = $handler($request, $vars);
                
                // Add security headers to all responses
                if ($response instanceof Response) {
                    $response = addSecurityHeaders($response);
                }
                
                return $response;
            }
        };
        
        // Create and dispatch relay
        $relay = new Relay($queue);
        $response = $relay->handle($request);
        break;
}

// Output the response
if (isset($response)) {
    $status = $response->getStatusCode();
    $headers = $response->getHeaders();
    
    // Send status code
    header(sprintf(
        'HTTP/%s %s %s',
        $response->getProtocolVersion(),
        $status,
        $response->getReasonPhrase()
    ));
    
    // Send headers
    foreach ($headers as $name => $values) {
        foreach ($values as $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }
    }
    
    // Output body
    echo $response->getBody();
}

/**
 * Add security headers to response
 */
function addSecurityHeaders(Response $response): Response
{
    return $response
        ->withHeader('X-Content-Type-Options', 'nosniff')
        ->withHeader('X-Frame-Options', 'DENY')
        ->withHeader('X-XSS-Protection', '1; mode=block')
        ->withHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->withHeader('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://cdnjs.cloudflare.com https://unpkg.com; font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data:; connect-src 'self' ws: wss:");
}