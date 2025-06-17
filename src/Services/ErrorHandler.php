<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Services;

use Psr\Log\LoggerInterface;

class ErrorHandler
{
    private LoggerInterface $logger;
    private bool $debugMode;
    
    public function __construct(LoggerInterface $logger, bool $debugMode = false)
    {
        $this->logger = $logger;
        $this->debugMode = $debugMode;
    }
    
    /**
     * Handle exceptions with proper logging and user-friendly responses
     * 
     * @param \Throwable $exception The exception to handle
     * @param string $context Additional context information
     * @return array Structured error information
     */
    public function handleException(\Throwable $exception, string $context = ''): array
    {
        // Log the full error
        $this->logger->error($context . ': ' . $exception->getMessage(), [
            'exception' => $exception,
            'trace' => $exception->getTraceAsString(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ]);
        
        // Return user-friendly error message
        if ($this->debugMode) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
                'context' => $context,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ];
        } else {
            return [
                'error' => true,
                'message' => 'An error occurred while processing your request.'
            ];
        }
    }
    
    /**
     * Log general errors
     * 
     * @param string $message Error message
     * @param array $context Additional context information
     */
    public function logError(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
    
    /**
     * Log security events (login attempts, permission changes, etc.)
     * 
     * @param string $message Security event message
     * @param array $context Additional context information
     */
    public function logSecurityEvent(string $message, array $context = []): void
    {
        $this->logger->alert('SECURITY: ' . $message, $context);
    }
    
    /**
     * Format validation errors for consistent API responses
     * 
     * @param array $errors Validation errors
     * @return array Structured validation error response
     */
    public function formatValidationErrors(array $errors): array
    {
        return [
            'error' => true,
            'type' => 'validation',
            'message' => 'The submitted data contains errors.',
            'errors' => $errors
        ];
    }
} 