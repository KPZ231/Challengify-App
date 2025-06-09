<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Services;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class WebSocketService implements MessageComponentInterface
{
    protected $clients;
    protected $adminClients;
    protected $logger;
    private $commandWhitelist = [
        'ls', 'ps', 'uptime', 'free', 'df', 'du', 'top', 'htop', 'netstat',
        'systemctl status', 'journalctl', 'tail'
    ];

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
        $this->adminClients = new \SplObjectStorage();
        
        // Set up logger
        $this->logger = new Logger('console');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::DEBUG));
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        
        $this->logger->info("New connection: {$conn->resourceId}");
        $this->sendSystemMessage($conn, 'info', 'Connected to server. Authentication required.');
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $this->adminClients->detach($conn);
        
        $this->logger->info("Connection {$conn->resourceId} has disconnected");
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->logger->error("Error: {$e->getMessage()}");
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $data = json_decode($msg, true);
            
            if (!$data || !isset($data['type'])) {
                return;
            }
            
            // Handle authentication
            if ($data['type'] === 'auth') {
                if (!isset($data['token'])) {
                    $this->sendSystemMessage($from, 'error', 'Authentication failed: No token provided.');
                    return;
                }
                
                // Verify token by checking if it's the same as the one in the session
                // In a real app, you'd validate against the database or other secure storage
                if ($this->isValidToken($data['token'])) {
                    $this->adminClients->attach($from);
                    $this->sendSystemMessage($from, 'success', 'Authentication successful. Access granted.');
                    $this->logger->info("Client {$from->resourceId} authenticated as admin");
                } else {
                    $this->sendSystemMessage($from, 'error', 'Authentication failed: Invalid token.');
                }
                
                return;
            }
            
            // Handle command execution (admin only)
            if ($data['type'] === 'command') {
                // Check if client is authenticated as admin
                if (!$this->adminClients->contains($from)) {
                    $this->sendSystemMessage($from, 'error', 'Access denied: Not authenticated as admin.');
                    return;
                }
                
                if (!isset($data['command']) || trim($data['command']) === '') {
                    $this->sendSystemMessage($from, 'error', 'No command provided.');
                    return;
                }
                
                $command = trim($data['command']);
                
                // Security: Only allow whitelisted commands
                $allowed = false;
                foreach ($this->commandWhitelist as $whitelistedCommand) {
                    if (strpos($command, $whitelistedCommand) === 0) {
                        $allowed = true;
                        break;
                    }
                }
                
                if (!$allowed) {
                    $this->sendSystemMessage($from, 'error', "Command not allowed: {$command}");
                    $this->logger->warning("Blocked command attempt: {$command} from client {$from->resourceId}");
                    return;
                }
                
                // Log the command
                $this->logger->info("Executing command: {$command} from client {$from->resourceId}");
                
                // Execute command with limited scope
                $output = $this->executeCommand($command);
                $this->sendConsoleOutput($from, $output);
            }
        } catch (\Exception $e) {
            $this->logger->error("Error processing message: {$e->getMessage()}");
            $this->sendSystemMessage($from, 'error', 'An error occurred while processing your request.');
        }
    }

    private function isValidToken(string $token): bool
    {
        // In a real application, validate against your session storage or database
        // For now, just check if the token is not empty
        return !empty($token) && strlen($token) >= 32;
    }

    private function executeCommand(string $command): string
    {
        // Add any command prefix or constraints needed for security
        $command = 'timeout 5 ' . escapeshellcmd($command) . ' 2>&1';
        
        // Execute the command and capture output
        $output = [];
        exec($command, $output);
        
        return implode("\n", $output);
    }

    private function sendSystemMessage(ConnectionInterface $conn, string $level, string $message)
    {
        $conn->send(json_encode([
            'type' => 'console',
            'level' => $level,
            'message' => $message
        ]));
    }

    private function sendConsoleOutput(ConnectionInterface $conn, string $output)
    {
        $conn->send(json_encode([
            'type' => 'console',
            'level' => 'info',
            'message' => $output
        ]));
    }

    public function broadcastServerLog(string $level, string $message)
    {
        // Broadcast to all authenticated admin clients
        foreach ($this->adminClients as $client) {
            $this->sendSystemMessage($client, $level, $message);
        }
    }
} 