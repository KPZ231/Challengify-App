<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Services;

use Kpzsproductions\Challengify\Models\User;
use Medoo\Medoo;

class PrivacyService
{
    private Medoo $db;
    
    public function __construct(Medoo $db)
    {
        $this->db = $db;
    }
    
    /**
     * Check if current user can view a target user's profile
     * based on the target user's privacy settings
     */
    public function canViewProfile(User $currentUser, User $targetUser): bool
    {
        // If viewing your own profile, always allow
        if ($currentUser->getId() === $targetUser->getId()) {
            return true;
        }
        
        // Get profile visibility setting
        $visibility = $targetUser->getProfileVisibility();
        
        switch ($visibility) {
            case 'public':
                return true;
                
            case 'followers':
                // Check if current user is following the target user
                return $currentUser->isLoggedIn() && $currentUser->isFollowing($targetUser->getId());
                
            case 'private':
                // Only the user themselves can see their profile
                return false;
                
            default:
                return true; // Default to public if setting is invalid
        }
    }
    
    /**
     * Check if user can view another user's content
     * based on content type and visibility settings
     */
    public function canViewContent(User $currentUser, User $contentOwner, string $contentType): bool
    {
        // For public content, check if profile is viewable
        return $this->canViewProfile($currentUser, $contentOwner);
    }
    
    /**
     * Check if a user should receive a notification
     * based on their notification settings
     */
    public function shouldSendNotification(User $user, string $notificationType): bool
    {
        switch ($notificationType) {
            case 'email':
                return $user->getNotificationEmail();
                
            case 'push':
                return $user->getNotificationPush();
                
            case 'sms':
                return $user->getNotificationSms();
                
            case 'weekly_summary':
                return $user->getWeeklySummary();
                
            case 'monthly_summary':
                return $user->getMonthlySummary();
                
            default:
                return true; // Default to sending notifications if type is unknown
        }
    }
    
    /**
     * Get the best time to send a notification based on user's settings
     */
    public function getNotificationTime(User $user): string
    {
        return $user->getNotificationTime() ?? '18:00';
    }
} 