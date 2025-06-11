<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Models;

use Medoo\Medoo;
use Kpzsproductions\Challengify\Services\Database;


class User
{
    private string $id;
    private string $username;
    private string $email;
    private string $password;
    private string $role;
    private ?string $avatar;
    private ?string $bio;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private bool $isLoggedIn = false;
    
    // Notification settings
    private bool $notificationEmail;
    private bool $notificationPush;
    private bool $notificationSms;
    private string $notificationTime;
    private bool $weeklySummary;
    private bool $monthlySummary;
    
    // Privacy settings
    private string $profileVisibility;
    private string $messagingPermission;
    
    // Language and timezone settings
    private string $language;
    private string $timezone;
    private bool $autoTimezone;

    public function __construct(
        string $id,
        string $username,
        string $email, 
        string $password,
        string $role = 'user',
        ?string $avatar = null,
        ?string $bio = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null,
        bool $notificationEmail = true,
        bool $notificationPush = false,
        bool $notificationSms = false,
        string $notificationTime = '18:00',
        bool $weeklySummary = true,
        bool $monthlySummary = false,
        string $profileVisibility = 'public',
        string $messagingPermission = 'all',
        string $language = 'en',
        string $timezone = 'UTC',
        bool $autoTimezone = true
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->avatar = $avatar;
        $this->bio = $bio;
        $this->createdAt = $createdAt ?? new \DateTime();
        $this->updatedAt = $updatedAt ?? new \DateTime();
        $this->notificationEmail = $notificationEmail;
        $this->notificationPush = $notificationPush;
        $this->notificationSms = $notificationSms;
        $this->notificationTime = $notificationTime;
        $this->weeklySummary = $weeklySummary;
        $this->monthlySummary = $monthlySummary;
        $this->profileVisibility = $profileVisibility;
        $this->messagingPermission = $messagingPermission;
        $this->language = $language;
        $this->timezone = $timezone;
        $this->autoTimezone = $autoTimezone;
    }

    public static function find(string $id): ?self
    {
        $db = Database::getInstance();
        $user = $db->get('users', '*', ['id' => $id]);

        if (!$user) {
            return null;
        }

        return new self(
            $user['id'],
            $user['username'],
            $user['email'],
            $user['password'],
            $user['role'],
            $user['avatar'] ?? null,
            $user['bio'] ?? null,
            new \DateTime($user['created_at']),
            new \DateTime($user['updated_at']),
            (bool)($user['notification_email'] ?? true),
            (bool)($user['notification_push'] ?? false),
            (bool)($user['notification_sms'] ?? false),
            $user['notification_time'] ?? '18:00',
            (bool)($user['weekly_summary'] ?? true),
            (bool)($user['monthly_summary'] ?? false),
            $user['profile_visibility'] ?? 'public',
            $user['messaging_permission'] ?? 'all',
            $user['language'] ?? 'en',
            $user['timezone'] ?? 'UTC',
            (bool)($user['auto_timezone'] ?? true)
        );
    }

    public static function findByUsername(string $username): ?self
    {
        $db = Database::getInstance();
        $user = $db->get('users', '*', ['username' => $username]);

        if (!$user) {
            return null;
        }

        return new self(
            $user['id'],
            $user['username'],
            $user['email'],
            $user['password'],
            $user['role'],
            $user['avatar'],
            $user['bio'] ?? null,
            new \DateTime($user['created_at']),
            new \DateTime($user['updated_at']),
            (bool)($user['notification_email'] ?? true),
            (bool)($user['notification_push'] ?? false),
            (bool)($user['notification_sms'] ?? false),
            $user['notification_time'] ?? '18:00',
            (bool)($user['weekly_summary'] ?? true),
            (bool)($user['monthly_summary'] ?? false),
            $user['profile_visibility'] ?? 'public',
            $user['messaging_permission'] ?? 'all',
            $user['language'] ?? 'en',
            $user['timezone'] ?? 'UTC',
            (bool)($user['auto_timezone'] ?? true)
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string 
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
        $this->updatedAt = new \DateTime();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
        $this->updatedAt = new \DateTime();
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
        $this->updatedAt = new \DateTime();
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
        $this->updatedAt = new \DateTime();
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): void
    {
        $this->bio = $bio;
        $this->updatedAt = new \DateTime();
    }

    public function getAvatar(): string
    {
        if ($this->avatar) {
            return '/uploads/avatars/' . $this->avatar;
        }
        
        return '/images/default-avatar.svg';
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get the raw avatar filename without path
     */
    public function getAvatarFilename(): ?string
    {
        return $this->avatar;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function isLoggedIn(): bool
    {
        return $this->isLoggedIn;
    }

    public function setLoggedIn(bool $isLoggedIn): void
    {
        $this->isLoggedIn = $isLoggedIn;
    }

    /**
     * Get count of followers
     */
    public function getFollowersCount(): int
    {
        $db = Database::getInstance();
        return $db->count('user_followers', ['following_id' => $this->id]);
    }

    /**
     * Get count of users being followed
     */
    public function getFollowingCount(): int
    {
        $db = Database::getInstance();
        return $db->count('user_followers', ['follower_id' => $this->id]);
    }

    /**
     * Check if this user is following another user
     */
    public function isFollowing(string $userId): bool
    {
        $db = Database::getInstance();
        return (bool)$db->get('user_followers', 'follower_id', [
            'follower_id' => $this->id,
            'following_id' => $userId
        ]);
    }

    /**
     * Follow another user
     */
    public function follow(string $userId): bool
    {
        if ($userId === $this->id) {
            return false; // Cannot follow yourself
        }
        
        if ($this->isFollowing($userId)) {
            return true; // Already following
        }

        $db = Database::getInstance();
        return $db->insert('user_followers', [
            'follower_id' => $this->id,
            'following_id' => $userId,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ])->rowCount() > 0;
    }

    /**
     * Unfollow a user
     */
    public function unfollow(string $userId): bool
    {
        $db = Database::getInstance();
        return $db->delete('user_followers', [
            'follower_id' => $this->id,
            'following_id' => $userId
        ])->rowCount() > 0;
    }

    /**
     * Get posts count for this user
     */
    public function getPostsCount(): int
    {
        $db = Database::getInstance();
        return $db->count('submissions', ['user_id' => $this->id]);
    }

    /**
     * Get comments count for this user
     */
    public function getCommentsCount(): int
    {
        $db = Database::getInstance();
        return $db->count('comments', ['user_id' => $this->id]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'avatar' => $this->avatar,
            'bio' => $this->bio,
            'is_logged_in' => $this->isLoggedIn,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s')
        ];
    }

    // Notification settings getters and setters
    public function getNotificationEmail(): bool
    {
        return $this->notificationEmail;
    }

    public function setNotificationEmail(bool $notificationEmail): void
    {
        $this->notificationEmail = $notificationEmail;
        $this->updatedAt = new \DateTime();
    }

    public function getNotificationPush(): bool
    {
        return $this->notificationPush;
    }

    public function setNotificationPush(bool $notificationPush): void
    {
        $this->notificationPush = $notificationPush;
        $this->updatedAt = new \DateTime();
    }

    public function getNotificationSms(): bool
    {
        return $this->notificationSms;
    }

    public function setNotificationSms(bool $notificationSms): void
    {
        $this->notificationSms = $notificationSms;
        $this->updatedAt = new \DateTime();
    }

    public function getNotificationTime(): string
    {
        return $this->notificationTime;
    }

    public function setNotificationTime(string $notificationTime): void
    {
        $this->notificationTime = $notificationTime;
        $this->updatedAt = new \DateTime();
    }

    public function getWeeklySummary(): bool
    {
        return $this->weeklySummary;
    }

    public function setWeeklySummary(bool $weeklySummary): void
    {
        $this->weeklySummary = $weeklySummary;
        $this->updatedAt = new \DateTime();
    }

    public function getMonthlySummary(): bool
    {
        return $this->monthlySummary;
    }

    public function setMonthlySummary(bool $monthlySummary): void
    {
        $this->monthlySummary = $monthlySummary;
        $this->updatedAt = new \DateTime();
    }

    // Privacy settings getters and setters
    public function getProfileVisibility(): string
    {
        return $this->profileVisibility;
    }

    public function setProfileVisibility(string $profileVisibility): void
    {
        $this->profileVisibility = $profileVisibility;
        $this->updatedAt = new \DateTime();
    }

    public function getMessagingPermission(): string
    {
        return $this->messagingPermission;
    }

    public function setMessagingPermission(string $messagingPermission): void
    {
        $this->messagingPermission = $messagingPermission;
        $this->updatedAt = new \DateTime();
    }

    // Language and timezone settings getters and setters
    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): void
    {
        $this->language = $language;
        $this->updatedAt = new \DateTime();
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
        $this->updatedAt = new \DateTime();
    }

    public function getAutoTimezone(): bool
    {
        return $this->autoTimezone;
    }

    public function setAutoTimezone(bool $autoTimezone): void
    {
        $this->autoTimezone = $autoTimezone;
        $this->updatedAt = new \DateTime();
    }

    /**
     * Check if user has admin role
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
