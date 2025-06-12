<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use Kpzsproductions\Challengify\Models\Challenge;
use Kpzsproductions\Challengify\Services\Database;
use Kpzsproductions\Challengify\Services\NotificationService;

// This script should be run via cron job every hour

// Check for challenges ending in the next hour
$now = new DateTime();
$oneHourLater = (new DateTime())->modify('+1 hour');

$db = Database::getInstance();
$challenges = $db->select(
    'challenges', 
    '*',
    [
        'status' => 'published',
        'end_date[<>]' => [
            $now->format('Y-m-d H:i:s'),
            $oneHourLater->format('Y-m-d H:i:s')
        ]
    ]
);

$notificationService = NotificationService::getInstance();

echo "Found " . count($challenges) . " challenges ending within the next hour.\n";

foreach ($challenges as $challengeData) {
    $challenge = Challenge::createFromArray($challengeData);
    echo "Sending notifications for challenge: " . $challenge->getTitle() . " (ID: " . $challenge->getId() . ")\n";
    $notificationService->sendChallengeEndingSoonNotifications($challenge->getId());
}

echo "Done.\n"; 