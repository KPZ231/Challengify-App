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
            
            // Remove all HTML tags and encode special characters
            $data = strip_tags($data);
            
            // Block script injections in CSS properties or event handlers
            $data = preg_replace('/javascript\s*:/i', '', $data);
            $data = preg_replace('/on\w+\s*=/i', '', $data);
            $data = preg_replace('/expression\s*\(/i', '', $data);
            
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
     * Generate a secure random token
     */
    public function generateToken(int $length = 32): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes($length / 2));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF token against the one stored in session
     */
    public function validateToken(?string $token): bool
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
} 