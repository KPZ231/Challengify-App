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
use Kpzsproductions\Challengify\Middleware\JwtMiddleware;
use Kpzsproductions\Challengify\Middleware\RateLimitMiddleware;
use Kpzsproductions\Challengify\Middleware\InputSanitizationMiddleware;
use Kpzsproductions\Challengify\Middleware\SessionAuthMiddleware;
use Kpzsproductions\Challengify\Middleware\AuthMiddleware;
use Kpzsproductions\Challengify\Services\JwtService;
use Kpzsproductions\Challengify\Services\SecurityService;
use Kpzsproductions\Challengify\Services\RateLimiterService;
use Kpzsproductions\Challengify\Services\CacheService;
use Kpzsproductions\Challengify\Services\FileUploadService;
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
    // Add User model definition
    User::class => function() {
        // Create a guest/default user for non-authenticated requests
        return new User(
            0,                  // default id
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
                
                if (empty($vars)) {
                    return $controller->$method($request);
                } else {
                    return $controller->$method($request, $vars);
                }
            } else {
                return $handler($request, $vars);
            }
        };
        
        // Create and dispatch relay
        $relay = new Relay($queue);
        $response = $relay->handle($request);
        break;
}

// Send the response
(function (Response $response) {
    $statusCode = $response->getStatusCode();
    
    // Send headers
    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }
    }
    
    // Send status code
    http_response_code($statusCode);
    
    // Send body
    echo $response->getBody();
})($response); 