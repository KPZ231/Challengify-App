<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Load helper functions
require_once __DIR__ . '/src/Views/helpers/helpers.php';

// Initialize logger
require_once __DIR__ . '/config/logging.php';
$logger = LoggerSetup::getInstance();

// Initialize Whoops error handler
$whoops = new \Whoops\Run;
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler());
    // Log errors in production
    $whoops->pushHandler(function($exception, $inspector, $run) use ($logger) {
        $logger->error($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    });
} else {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    // Log errors in development
    $whoops->pushHandler(function($exception, $inspector, $run) use ($logger) {
        $logger->debug($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    });
}
$whoops->register();

// Set timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// Log application startup
$logger->info('Application started', [
    'environment' => $_ENV['APP_ENV'] ?? 'unknown',
    'timezone' => date_default_timezone_get()
]);

// Return the application base path
return __DIR__;