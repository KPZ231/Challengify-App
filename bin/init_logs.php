<?php

// Path to logs directory
$logsDir = dirname(__DIR__) . '/logs';

// Create logs directory if it doesn't exist
if (!is_dir($logsDir)) {
    if (mkdir($logsDir, 0755, true)) {
        echo "Created logs directory: $logsDir\n";
    } else {
        echo "Failed to create logs directory: $logsDir\n";
        exit(1);
    }
} else {
    echo "Logs directory already exists: $logsDir\n";
}

// Create initial log file
$logFile = $logsDir . '/app.log';
if (!file_exists($logFile)) {
    $timestamp = date('Y-m-d H:i:s');
    $initialContent = "=== Challengify Application Log Started at $timestamp ===\n";
    if (file_put_contents($logFile, $initialContent)) {
        echo "Created log file: $logFile\n";
    } else {
        echo "Failed to create log file: $logFile\n";
        exit(1);
    }
} else {
    echo "Log file already exists: $logFile\n";
}

echo "Logs initialization complete.\n"; 