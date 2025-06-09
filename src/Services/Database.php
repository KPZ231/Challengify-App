<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Services;

use Medoo\Medoo;

class Database
{
    private static ?Medoo $instance = null;
    
    /**
     * Get the database instance
     */
    public static function getInstance(): Medoo
    {
        if (self::$instance === null) {
            // Load environment variables
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $database = $_ENV['DB_NAME'] ?? 'challengify';
            $username = $_ENV['DB_USER'] ?? 'root';
            $password = $_ENV['DB_PASS'] ?? '';
            $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
            
            // Initialize Medoo
            self::$instance = new Medoo([
                'type' => 'mysql',
                'host' => $host,
                'port' => (int)$port,
                'database' => $database,
                'username' => $username,
                'password' => $password,
                'charset' => $charset,
                'logging' => true,
                'option' => [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            ]);
        }
        
        return self::$instance;
    }
    
    /**
     * Set a custom database instance (useful for testing)
     */
    public static function setInstance(Medoo $db): void
    {
        self::$instance = $db;
    }
    
    /**
     * Generate a UUID v4
     */
    public static function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
} 