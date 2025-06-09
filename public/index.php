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
use Kpzsproductions\Challengify\Middleware\JwtMiddleware;
use Kpzsproductions\Challengify\Middleware\RateLimitMiddleware;
use Kpzsproductions\Challengify\Middleware\InputSanitizationMiddleware;
use Kpzsproductions\Challengify\Middleware\SessionAuthMiddleware;
use Kpzsproductions\Challengify\Middleware\AuthMiddleware;
use Kpzsproductions\Challengify\Middleware\AdminMiddleware;
use Kpzsproductions\Challengify\Services\JwtService;
use Kpzsproductions\Challengify\Services\SecurityService;
use Kpzsproductions\Challengify\Services\RateLimiterService;
use Kpzsproductions\Challengify\Services\CacheService;
use Kpzsproductions\Challengify\Services\FileUploadService;
use Kpzsproductions\Challengify\Services\WebSocketService;
use Medoo\Medoo;
use DI\ContainerBuilder;
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
    InputSanitizationMiddleware::class => function($c) {
        return new InputSanitizationMiddleware($c->get(SecurityService::class));
    },
    SessionAuthMiddleware::class => function($c) {
        return new SessionAuthMiddleware($c->get(Medoo::class), $c->get(User::class));
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
            'guest'             // guest role
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
    $r->addRoute('GET', '/challenges/{id:\d+}', [ChallengesController::class, 'show']);
    $r->addRoute('POST', '/challenges/{id:\d+}/submit', [ChallengesController::class, 'submitEntry']);
    
    // Add POST routes for form submissions
    $r->addRoute('POST', '/login', [AuthController::class, 'processLogin']);
    $r->addRoute('POST', '/register', [AuthController::class, 'processRegister']);
    
    // Add logout route
    $r->addRoute('GET', '/logout', [AuthController::class, 'logout']);
    
    // User profile routes
    $r->addRoute('GET', '/profile', [UserController::class, 'profile']);
    $r->addRoute('POST', '/profile/update-avatar', [UserController::class, 'updateAvatar']);
    $r->addRoute('POST', '/profile/update-username', [UserController::class, 'updateUsername']);
    
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
        
        // Add Auth middleware for protected routes
        if (in_array($request->getUri()->getPath(), ['/profile', '/profile/update-avatar', '/profile/update-username']) || 
            (strpos($request->getUri()->getPath(), '/challenges/') === 0 && 
            substr($request->getUri()->getPath(), -7) === '/submit')) {
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
        ->withHeader('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net; connect-src 'self' ws: wss:");
} 