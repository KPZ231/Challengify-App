<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Services;

use Kpzsproductions\Challengify\Models\User;
use Kpzsproductions\Challengify\Models\Challenge;

class NotificationService
{
    private static ?NotificationService $instance = null;
    private MailService $mailService;
    
    private function __construct()
    {
        $this->mailService = MailService::getInstance();
    }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Send notification about new follower
     */
    public function notifyNewFollower(User $user, User $follower): bool
    {
        // Skip if user doesn't want email notifications
        if (!$user->getNotificationEmail()) {
            return false;
        }
        
        $subject = $follower->getUsername() . ' started following you on Challengify';
        
        $content = '
            <h2>You have a new follower!</h2>
            <p>' . htmlspecialchars($follower->getUsername()) . ' is now following you on Challengify.</p>
            <p>
                <a href="https://challengify.com/profile/' . urlencode($follower->getUsername()) . '" class="button">
                    View Profile
                </a>
            </p>
        ';
        
        $body = $this->mailService->generateEmailTemplate('New Follower on Challengify', $content);
        
        return $this->mailService->sendToUser($user, $subject, $body);
    }
    
    /**
     * Send notification about challenge ending soon (1 hour before end)
     */
    public function notifyChallengeEndingSoon(User $user, Challenge $challenge): bool
    {
        // Skip if user doesn't want email notifications
        if (!$user->getNotificationEmail()) {
            return false;
        }
        
        $subject = 'Challengify: "' . $challenge->getTitle() . '" is ending soon';
        
        $content = '
            <h2>Challenge Ending Soon</h2>
            <p>The challenge <strong>' . htmlspecialchars($challenge->getTitle()) . '</strong> is ending in about an hour!</p>
            <p>If you\'re participating, make sure to submit your entry before the deadline.</p>
            <p>
                <a href="https://challengify.com/challenges/' . $challenge->getId() . '" class="button">
                    View Challenge
                </a>
            </p>
        ';
        
        $body = $this->mailService->generateEmailTemplate('Challenge Ending Soon', $content);
        
        return $this->mailService->sendToUser($user, $subject, $body);
    }
    
    /**
     * Send notification about a new published challenge
     */
    public function notifyNewChallenge(User $user, Challenge $challenge): bool
    {
        // Skip if user doesn't want email notifications
        if (!$user->getNotificationEmail()) {
            return false;
        }
        
        $subject = 'New Challenge Published: ' . $challenge->getTitle();
        
        $content = '
            <h2>New Challenge Available!</h2>
            <p>A new challenge has been published on Challengify:</p>
            <h3>' . htmlspecialchars($challenge->getTitle()) . '</h3>
            <p>' . htmlspecialchars(substr($challenge->getDescription(), 0, 150)) . '...</p>
            <p>Difficulty: ' . ucfirst($challenge->getDifficulty()) . '</p>
            <p>Start Date: ' . $challenge->getStartDate()->format('Y-m-d H:i') . '</p>
            <p>End Date: ' . $challenge->getEndDate()->format('Y-m-d H:i') . '</p>
            <p>
                <a href="https://challengify.com/challenges/' . $challenge->getId() . '" class="button">
                    View Challenge
                </a>
            </p>
        ';
        
        $body = $this->mailService->generateEmailTemplate('New Challenge on Challengify', $content);
        
        return $this->mailService->sendToUser($user, $subject, $body);
    }
    
    /**
     * Send notifications about new follower to a user
     */
    public function sendNewFollowerNotification(string $userId, string $followerId): void
    {
        $user = User::find($userId);
        $follower = User::find($followerId);
        
        if ($user && $follower) {
            $this->notifyNewFollower($user, $follower);
        }
    }
    
    /**
     * Send notifications about ending challenges to all users who have opted in
     */
    public function sendChallengeEndingSoonNotifications(string $challengeId): void
    {
        $challenge = Challenge::find($challengeId);
        
        if (!$challenge) {
            return;
        }
        
        $db = Database::getInstance();
        $userIds = $db->select('users', 'id', ['notification_email' => 1]);
        
        foreach ($userIds as $data) {
            $user = User::find($data['id']);
            if ($user) {
                $this->notifyChallengeEndingSoon($user, $challenge);
            }
        }
    }
    
    /**
     * Send notifications about a new challenge to all users who have opted in
     */
    public function sendNewChallengeNotifications(string $challengeId): void
    {
        $challenge = Challenge::find($challengeId);
        
        if (!$challenge) {
            return;
        }
        
        $db = Database::getInstance();
        $userIds = $db->select('users', 'id', ['notification_email' => 1]);
        
        foreach ($userIds as $data) {
            $user = User::find($data['id']);
            if ($user) {
                $this->notifyNewChallenge($user, $challenge);
            }
        }
    }
} 