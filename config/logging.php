<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\WebProcessor;

class LoggerSetup {
    private static $instance = null;
    private $logger;

    private function __construct() {
        $this->logger = new Logger('challengify');

        // Custom date format for logs
        $dateFormat = "Y-m-d H:i:s";
        $output = "[%datetime%] %level_name% %extra.file%:%extra.line% - %message% %context%\n";
        $formatter = new LineFormatter($output, $dateFormat);

        // Add processors for extra information
        $this->logger->pushProcessor(new IntrospectionProcessor());
        $this->logger->pushProcessor(new WebProcessor());

        // Console Handler (with colors)
        $consoleHandler = new StreamHandler('php://stdout', Logger::DEBUG);
        $consoleHandler->setFormatter($formatter);
        $this->logger->pushHandler($consoleHandler);

        // Rotating File Handler (keeps 7 days of logs, max 50MB per file)
        $rotatingHandler = new RotatingFileHandler(
            dirname(__DIR__) . '/logs/app.log',
            7,
            Logger::DEBUG,
            true,
            0644,
            true
        );
        $rotatingHandler->setFormatter($formatter);
        $this->logger->pushHandler($rotatingHandler);

        // Error log handler (for errors and critical issues)
        $errorHandler = new RotatingFileHandler(
            dirname(__DIR__) . '/logs/error.log',
            7,
            Logger::ERROR,
            true,
            0644,
            true
        );
        $errorHandler->setFormatter($formatter);
        $this->logger->pushHandler($errorHandler);
    }

    public static function getInstance(): Logger {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->logger;
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Prevent unserializing of the instance
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}

// Global function for easy logging
if (!function_exists('log_message')) {
    function log_message($level, $message, array $context = []): void {
        LoggerSetup::getInstance()->log($level, $message, $context);
    }
} 