<?php

require dirname(__DIR__) . '/bootstrap.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Kpzsproductions\Challengify\Services\WebSocketService;

$webSocketService = new WebSocketService();

// Check if logs directory exists, if not create it
$logsDir = dirname(__DIR__) . '/logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
}

// Create log file if it doesn't exist
$logFile = $logsDir . '/app.log';
if (!file_exists($logFile)) {
    file_put_contents($logFile, "=== Challengify Application Log Started ===\n");
}

echo "Starting WebSocket server on port 8080...\n";

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            $webSocketService
        )
    ),
    8080
);

echo "WebSocket server running on ws://0.0.0.0:8080\n";
echo "Press Ctrl+C to stop the server\n";

$server->run(); 