<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Services;

class SecurityService
{
    /**
     * Hash a password using Bcrypt
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify if a password matches its hash
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Sanitize input data to prevent XSS with enhanced protections
     */
    public function sanitizeInput(mixed $data): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitizeInput($value);
            }
            return $data;
        }
        
        if (is_string($data)) {
            // First normalize the data to prevent Unicode attacks
            $data = $this->normalizeUnicode($data);
            
            // Remove dangerous content
            $data = preg_replace([
                '/javascript\s*:/i',
                '/data\s*:/i',
                '/vbscript\s*:/i',
                '/expression\s*\(/i',
                '/on\w+\s*=/i',  // Matches onclick, onload, etc.
                '/<\s*script/i'
            ], '', $data);
            
            // Remove all HTML tags
            $data = strip_tags($data);
            
            // Encode special characters to prevent HTML injection
            $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            return $data;
        }
        
        return $data;
    }
    
    /**
     * Normalize Unicode to prevent homograph attacks
     */
    private function normalizeUnicode(string $string): string
    {
        if (function_exists('normalizer_normalize')) {
            return normalizer_normalize($string, \Normalizer::FORM_C) ?: $string;
        }
        return $string;
    }

    /**
     * Escape output for HTML display
     */
    public function escapeOutput(string $output): string
    {
        return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate a secure random token with form-specific tokens and expiration
     */
    public function generateToken(string $formName = 'default', int $length = 32): string
    {
        if (!isset($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = [];
        }
        
        // Generate a unique token for this form
        $token = bin2hex(random_bytes($length / 2));
        
        // Store with timestamp for expiration
        $_SESSION['csrf_tokens'][$formName] = [
            'token' => $token,
            'created_at' => time()
        ];
        
        return $token;
    }
    
    /**
     * Validate CSRF token against the one stored in session
     * Implements one-time use and expiration for enhanced security
     */
    public function validateToken(?string $token, string $formName = 'default'): bool
    {
        if (empty($token) || empty($_SESSION['csrf_tokens'][$formName])) {
            return false;
        }
        
        $storedToken = $_SESSION['csrf_tokens'][$formName]['token'];
        $tokenTime = $_SESSION['csrf_tokens'][$formName]['created_at'];
        
        // Check if token is expired (1 hour lifetime)
        if (time() - $tokenTime > 3600) {
            unset($_SESSION['csrf_tokens'][$formName]);
            return false;
        }
        
        // Use constant-time comparison
        $valid = hash_equals($storedToken, $token);
        
        // One-time use: remove after validation
        if ($valid) {
            unset($_SESSION['csrf_tokens'][$formName]);
        }
        
        return $valid;
    }
} 