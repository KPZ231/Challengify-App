<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Load helper functions
require_once __DIR__ . '/src/Views/helpers/helpers.php';

// Initialize Whoops error handler
$whoops = new \Whoops\Run;
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
    $whoops->pushHandler(new \Whoops\Handler\PlainTextHandler());
} else {
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
}
$whoops->register();

// Set timezone
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'UTC');

// Return the application base path
return __DIR__;