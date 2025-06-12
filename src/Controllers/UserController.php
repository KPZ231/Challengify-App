<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\RedirectResponse;
use Kpzsproductions\Challengify\Models\User;
use Kpzsproductions\Challengify\Services\SecurityService;
use Kpzsproductions\Challengify\Services\FileUploadService;
use Kpzsproductions\Challengify\Services\TranslationService;
use Kpzsproductions\Challengify\Services\PrivacyService;
use Medoo\Medoo;

class UserController
{
    private User $user;
    private SecurityService $securityService;
    private FileUploadService $fileUploadService;
    private TranslationService $translationService;
    private PrivacyService $privacyService;
    private Medoo $db;

    public function __construct(
        User $user,
        SecurityService $securityService,
        FileUploadService $fileUploadService,
        TranslationService $translationService,
        PrivacyService $privacyService,
        Medoo $db
    ) {
        $this->user = $user;
        $this->securityService = $securityService;
        $this->fileUploadService = $fileUploadService;
        $this->translationService = $translationService;
        $this->privacyService = $privacyService;
        $this->db = $db;
    }

    /**
     * Display user profile
     * Shows user info, submissions, badges, and reputation
     */
    public function profile(ServerRequestInterface $request): ResponseInterface
    {
        // Create new response
        $response = new Response();
        
        // Extract user data from request attributes (set by SessionAuthMiddleware)
        $user = $request->getAttribute('user', $this->user);
        
        if (!$user->isLoggedIn()) {
            return new RedirectResponse('/login');
        }
        
        // Get user submissions
        $submissions = $this->db->select(
            'submissions', 
            [
                '[>]challenges' => ['challenge_id' => 'id']
            ],
            [
                'submissions.id',
                'submissions.title',
                'submissions.status',
                'submissions.created_at',
                'challenges.title(challenge_name)',
                'challenges.id(challenge_id)'
            ],
            [
                'submissions.user_id' => $user->getId(),
                'ORDER' => ['submissions.created_at' => 'DESC']
            ]
        );

        // Get user badges
        $badges = $this->db->select(
            'user_badges', 
            [
                '[>]badges' => ['badge_id' => 'id']
            ],
            [
                'badges.id',
                'badges.name',
                'badges.description',
                'badges.image',
                'user_badges.awarded_at'
            ],
            [
                'user_badges.user_id' => $user->getId(),
                'ORDER' => ['user_badges.awarded_at' => 'DESC']
            ]
        );

        // Get reputation level
        $reputationPoints = $this->db->get(
            'users',
            'reputation',
            ['id' => $user->getId()]
        ) ?? 0;
        
        // Define reputation levels
        $reputationLevels = [
            0 => 'Beginner',
            100 => 'Novice',
            500 => 'Intermediate',
            1000 => 'Advanced',
            2500 => 'Expert',
            5000 => 'Master'
        ];
        
        // Calculate current level
        $currentLevel = 'Beginner';
        foreach ($reputationLevels as $points => $level) {
            if ($reputationPoints >= $points) {
                $currentLevel = $level;
            } else {
                break;
            }
        }

        // Get username change count
        $usernameChangeCount = $this->db->get(
            'users',
            'username_changes',
            ['id' => $user->getId()]
        ) ?? 0;
        
        // Set CSRF token for forms
        $csrfToken = $this->securityService->generateToken();
        
        // Prepare flash messages
        $flashMessage = $_SESSION['flash_message'] ?? null;
        $flashType = $_SESSION['flash_type'] ?? 'info';
        
        // Clear flash messages
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);

        // Start output buffering
        ob_start();
        
        // Include the view file
        require __DIR__ . '/../Views/profile.php';
        
        // Get buffered content
        $content = ob_get_clean();
        
        // Write content to response body
        $response->getBody()->write($content);
        
        // Set security headers
        $response = $response->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('X-XSS-Protection', '1; mode=block')
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('Content-Security-Policy', 
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; " .
                "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com fonts.googleapis.com; " .
                "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com; " .
                "img-src 'self' data:;"
            );

        return $response;
    }
    
    /**
     * View another user's profile
     */
    public function viewProfile(ServerRequestInterface $request, array $args): ResponseInterface
    {
        // Create new response
        $response = new Response();
        
        // Extract current logged in user data from request attributes
        $currentUser = $request->getAttribute('user', $this->user);
        
        if (!$currentUser->isLoggedIn()) {
            return new RedirectResponse('/login');
        }
        
        // Get username from route args
        $username = $args['username'] ?? null;
        
        if (!$username) {
            return new RedirectResponse('/404');
        }
        
        // Find the user by username
        $profileUser = User::findByUsername($username);
        
        if (!$profileUser) {
            return new RedirectResponse('/404');
        }
        
        // Check privacy settings to see if current user can view this profile
        if (!$this->privacyService->canViewProfile($currentUser, $profileUser)) {
            $_SESSION['flash_message'] = $this->translationService->trans('This profile is private.', [], 'messages');
            $_SESSION['flash_type'] = 'error';
            return new RedirectResponse('/');
        }
        
        // Get user submissions
        $submissions = $this->db->select(
            'submissions', 
            [
                '[>]challenges' => ['challenge_id' => 'id']
            ],
            [
                'submissions.id',
                'submissions.title',
                'submissions.status',
                'submissions.created_at',
                'challenges.title(challenge_name)',
                'challenges.id(challenge_id)'
            ],
            [
                'submissions.user_id' => $profileUser->getId(),
                'submissions.status' => 'approved', // Only show approved submissions
                'ORDER' => ['submissions.created_at' => 'DESC']
            ]
        );

        // Get user badges
        $badges = $this->db->select(
            'user_badges', 
            [
                '[>]badges' => ['badge_id' => 'id']
            ],
            [
                'badges.id',
                'badges.name',
                'badges.description',
                'badges.image',
                'user_badges.awarded_at'
            ],
            [
                'user_badges.user_id' => $profileUser->getId(),
                'ORDER' => ['user_badges.awarded_at' => 'DESC']
            ]
        );

        // Get reputation level
        $reputationPoints = $this->db->get(
            'users',
            'reputation',
            ['id' => $profileUser->getId()]
        ) ?? 0;
        
        // Define reputation levels
        $reputationLevels = [
            0 => 'Beginner',
            100 => 'Novice',
            500 => 'Intermediate',
            1000 => 'Advanced',
            2500 => 'Expert',
            5000 => 'Master'
        ];
        
        // Calculate current level
        $currentLevel = 'Beginner';
        foreach ($reputationLevels as $points => $level) {
            if ($reputationPoints >= $points) {
                $currentLevel = $level;
            } else {
                break;
            }
        }
        
        // Check if current user is following this profile
        $isFollowing = $currentUser->isFollowing($profileUser->getId());
        
        // Get posts and comments counts
        $postsCount = $profileUser->getPostsCount();
        $commentsCount = $profileUser->getCommentsCount();
        $followersCount = $profileUser->getFollowersCount();
        $followingCount = $profileUser->getFollowingCount();

        // Get recent activity
        $recentActivity = $this->getUserRecentActivity($profileUser->getId());
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        require __DIR__ . '/../Views/user-profile.php';
        
        // Get buffered content
        $content = ob_get_clean();
        
        // Write content to response body
        $response->getBody()->write($content);
        
        // Set security headers
        $response = $response->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('X-XSS-Protection', '1; mode=block')
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('Content-Security-Policy', 
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; " .
                "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com fonts.googleapis.com; " .
                "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com; " .
                "img-src 'self' data:;"
            );

        return $response;
    }
    
    /**
     * Toggle following a user
     */
    public function toggleFollow(ServerRequestInterface $request, array $args): ResponseInterface
    {
        // Extract current user data
        $currentUser = $request->getAttribute('user', $this->user);
        
        if (!$currentUser->isLoggedIn()) {
            return new RedirectResponse('/login');
        }
        
        // Get username from URL
        $username = $args['username'] ?? null;
        
        if (!$username) {
            $_SESSION['flash_message'] = 'Invalid request.';
            $_SESSION['flash_type'] = 'danger';
            return new RedirectResponse('/');
        }
        
        // Find the user to follow/unfollow
        $targetUser = User::findByUsername($username);
        
        if (!$targetUser) {
            $_SESSION['flash_message'] = 'User not found.';
            $_SESSION['flash_type'] = 'danger';
            return new RedirectResponse('/');
        }
        
        // Can't follow yourself
        if ($currentUser->getId() === $targetUser->getId()) {
            $_SESSION['flash_message'] = 'You cannot follow yourself.';
            $_SESSION['flash_type'] = 'danger';
            return new RedirectResponse('/user/' . $username);
        }
        
        // Check if already following
        $isFollowing = $currentUser->isFollowing($targetUser->getId());
        
        if ($isFollowing) {
            // Unfollow
            if ($currentUser->unfollow($targetUser->getId())) {
                $_SESSION['flash_message'] = 'You have unfollowed ' . $targetUser->getUsername() . '.';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Failed to unfollow user.';
                $_SESSION['flash_type'] = 'danger';
            }
        } else {
            // Follow
            if ($currentUser->follow($targetUser->getId())) {
                $_SESSION['flash_message'] = 'You are now following ' . $targetUser->getUsername() . '.';
                $_SESSION['flash_type'] = 'success';
            } else {
                $_SESSION['flash_message'] = 'Failed to follow user.';
                $_SESSION['flash_type'] = 'danger';
            }
        }
        
        return new RedirectResponse('/user/' . $username);
    }
    
    /**
     * Get user's recent activity
     */
    private function getUserRecentActivity(string $userId): array
    {
        $activity = [];
        
        // Get recent submissions (last 5)
        $submissions = $this->db->select(
            'submissions',
            [
                '[>]challenges' => ['challenge_id' => 'id']
            ],
            [
                'submissions.id',
                'submissions.title',
                'submissions.created_at',
                'challenges.title(challenge_name)'
            ],
            [
                'submissions.user_id' => $userId,
                'submissions.status' => 'approved',
                'LIMIT' => 5,
                'ORDER' => ['submissions.created_at' => 'DESC']
            ]
        ) ?: [];
        
        // Add type and action to each submission manually
        foreach ($submissions as &$submission) {
            $submission['type'] = 'submission';
            $submission['action'] = 'submitted';
        }
        
        // Get recent comments (last 5)
        $comments = $this->db->select(
            'comments',
            [
                '[>]submissions' => ['submission_id' => 'id']
            ],
            [
                'comments.id',
                'comments.content(title)',
                'comments.created_at',
                'submissions.title(challenge_name)'
            ],
            [
                'comments.user_id' => $userId,
                'LIMIT' => 5,
                'ORDER' => ['comments.created_at' => 'DESC']
            ]
        ) ?: [];
        
        // Add type and action to each comment manually
        foreach ($comments as &$comment) {
            $comment['type'] = 'comment';
            $comment['action'] = 'commented';
        }
        
        // Get recent received badges (last 5)
        $badges = $this->db->select(
            'user_badges',
            [
                '[>]badges' => ['badge_id' => 'id']
            ],
            [
                'badges.id',
                'badges.name(title)',
                'user_badges.awarded_at(created_at)',
                'badges.description(challenge_name)'
            ],
            [
                'user_badges.user_id' => $userId,
                'LIMIT' => 5,
                'ORDER' => ['user_badges.awarded_at' => 'DESC']
            ]
        ) ?: [];
        
        // Add type and action to each badge manually
        foreach ($badges as &$badge) {
            $badge['type'] = 'badge';
            $badge['action'] = 'earned';
        }
        
        // Merge and sort all activity by date
        $activity = array_merge($submissions, $comments, $badges);
        
        // Sort by created_at descending
        usort($activity, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        // Limit to 10 most recent activities
        return array_slice($activity, 0, 10);
    }
    
    /**
     * Handle avatar upload
     */
    public function updateAvatar(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user', $this->user);
        
        if (!$user->isLoggedIn()) {
            return new RedirectResponse('/login');
        }
        
        // Verify CSRF token
        $data = $request->getParsedBody();
        if (!$this->securityService->validateToken($data['csrf_token'] ?? '')) {
            $_SESSION['flash_message'] = 'Invalid request token. Please try again.';
            $_SESSION['flash_type'] = 'danger';
            return new RedirectResponse('/profile');
        }
        
        // Get uploaded files
        $uploadedFiles = $request->getUploadedFiles();
        
        if (!isset($uploadedFiles['avatar'])) {
            $_SESSION['flash_message'] = 'No avatar file uploaded.';
            $_SESSION['flash_type'] = 'danger';
            return new RedirectResponse('/profile');
        }
        
        try {
            // Enhanced security settings for avatar uploads
            $fileData = $this->fileUploadService->upload(
                $uploadedFiles['avatar'], 
                [
                    'allowedTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                    'maxFileSize' => 2 * 1024 * 1024, // 2MB
                    'directory' => __DIR__ . '/../../public/uploads/avatars'
                ]
            );
            
            // Additional image validation - verify it's actually an image
            $filePath = $fileData['path'];
            if (!$this->validateImage($filePath)) {
                // Remove the suspicious file
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                throw new \RuntimeException('The uploaded file is not a valid image.');
            }
            
            // Delete old avatar if exists
            $oldAvatar = $user->getAvatarFilename();
            if ($oldAvatar && file_exists(__DIR__ . '/../../public/uploads/avatars/' . $oldAvatar)) {
                unlink(__DIR__ . '/../../public/uploads/avatars/' . $oldAvatar);
            }
            
            // Update user avatar in database
            $this->db->update(
                'users',
                ['avatar' => $fileData['filename']],
                ['id' => $user->getId()]
            );
            
            // Update user object
            $user->setAvatar($fileData['filename']);
            
            $_SESSION['flash_message'] = 'Avatar updated successfully.';
            $_SESSION['flash_type'] = 'success';
            
        } catch (\RuntimeException $e) {
            $_SESSION['flash_message'] = 'Error uploading avatar: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'danger';
        }
        
        return new RedirectResponse('/profile');
    }
    
    /**
     * Validate that a file is actually an image by using getimagesize()
     */
    private function validateImage(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }
        
        // Use PHP's getimagesize to verify it's actually an image
        $imageInfo = @getimagesize($filePath);
        if ($imageInfo === false) {
            return false;
        }
        
        // Verify image type is allowed
        $allowedTypes = [
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG,
            IMAGETYPE_GIF
        ];
        
        return in_array($imageInfo[2], $allowedTypes);
    }
    
    /**
     * Handle username update
     */
    public function updateUsername(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user', $this->user);
        
        if (!$user->isLoggedIn()) {
            return new RedirectResponse('/login');
        }
        
        // Verify CSRF token
        $data = $request->getParsedBody();
        if (!$this->securityService->validateToken($data['csrf_token'] ?? '')) {
            $_SESSION['flash_message'] = 'Invalid request token. Please try again.';
            $_SESSION['flash_type'] = 'danger';
            return new RedirectResponse('/profile');
        }
        
        // Sanitize input
        $newUsername = $this->securityService->sanitizeInput($data['username'] ?? '');
        
        if (empty($newUsername)) {
            $_SESSION['flash_message'] = 'Username cannot be empty.';
            $_SESSION['flash_type'] = 'danger';
            return new RedirectResponse('/profile');
        }
        
        if ($newUsername === $user->getUsername()) {
            $_SESSION['flash_message'] = 'New username must be different from current one.';
            $_SESSION['flash_type'] = 'danger';
            return new RedirectResponse('/profile');
        }
        
        // Check if username already exists
        $existingUser = $this->db->get('users', 'id', ['username' => $newUsername]);
        if ($existingUser) {
            $_SESSION['flash_message'] = 'This username is already taken.';
            $_SESSION['flash_type'] = 'danger';
            return new RedirectResponse('/profile');
        }
        
        // Check username change limit
        $usernameChangeCount = $this->db->get('users', 'username_changes', ['id' => $user->getId()]) ?? 0;
        
        if ($usernameChangeCount >= 3) {
            $_SESSION['flash_message'] = 'You have reached the maximum number of username changes (3).';
            $_SESSION['flash_type'] = 'danger';
            return new RedirectResponse('/profile');
        }
        
        // Update username
        $this->db->update(
            'users',
            [
                'username' => $newUsername,
                'username_changes' => $usernameChangeCount + 1
            ],
            ['id' => $user->getId()]
        );
        
        // Update user object
        $user->setUsername($newUsername);
        
        $_SESSION['flash_message'] = 'Username updated successfully.';
        $_SESSION['flash_type'] = 'success';
        
        return new RedirectResponse('/profile');
    }
    
    /**
     * Handle bio update
     */
    public function updateBio(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user', $this->user);
        
        if (!$user->isLoggedIn()) {
            return new RedirectResponse('/login');
        }
        
        // Verify CSRF token
        $data = $request->getParsedBody();
        if (!$this->securityService->validateToken($data['csrf_token'] ?? '')) {
            $_SESSION['flash_message'] = 'Invalid request token. Please try again.';
            $_SESSION['flash_type'] = 'danger';
            return new RedirectResponse('/profile');
        }
        
        // Sanitize input
        $bio = $this->securityService->sanitizeInput($data['bio'] ?? '');
        
        // Cap bio length to prevent abuse
        if (mb_strlen($bio) > 1000) {
            $bio = mb_substr($bio, 0, 1000);
        }
        
        // Update bio in database
        $this->db->update(
            'users',
            ['bio' => $bio],
            ['id' => $user->getId()]
        );
        
        // Update user object
        $user->setBio($bio);
        
        $_SESSION['flash_message'] = 'Bio updated successfully.';
        $_SESSION['flash_type'] = 'success';
        
        return new RedirectResponse('/profile');
    }

    /**
     * Display user settings page
     */
    public function settings(ServerRequestInterface $request): ResponseInterface
    {
        // Create new response
        $response = new Response();
        
        // Extract user data from request attributes (set by SessionAuthMiddleware)
        $user = $request->getAttribute('user', $this->user);
        
        if (!$user->isLoggedIn()) {
            return new RedirectResponse('/login');
        }
        
        // Set translation locale based on user preference
        $this->translationService->setLocale($user->getLanguage() ?? 'en');
        
        // Available languages
        $languages = [
            'en' => 'English',
            'pl' => 'Polski',
            'de' => 'Deutsch',
            'es' => 'Español',
            'fr' => 'Français'
        ];
        
        // Get list of timezones
        $timezones = \DateTimeZone::listIdentifiers();
        
        // Set CSRF token for forms
        $csrfToken = $this->securityService->generateToken();
        
        // Prepare flash messages
        $flashMessage = $_SESSION['flash_message'] ?? null;
        $flashType = $_SESSION['flash_type'] ?? 'info';
        
        // Clear flash messages
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);

        // Make translation service available in the view
        $translationService = $this->translationService;
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        require __DIR__ . '/../Views/settings.php';
        
        // Get buffered content
        $content = ob_get_clean();
        
        // Write content to response body
        $response->getBody()->write($content);
        
        // Set security headers
        $response = $response->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('X-XSS-Protection', '1; mode=block')
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('Content-Security-Policy', 
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; " .
                "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com fonts.googleapis.com; " .
                "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com; " .
                "img-src 'self' data:;"
            );

        return $response;
    }
    
    /**
     * Update notification settings
     */
    public function updateNotificationSettings(ServerRequestInterface $request): ResponseInterface
    {
        // Extract user data from request attributes (set by SessionAuthMiddleware)
        $user = $request->getAttribute('user', $this->user);
        
        if (!$user->isLoggedIn()) {
            return new RedirectResponse('/login');
        }
        
        // Parse request body
        $params = $request->getParsedBody();
        
        // Verify CSRF token
        $csrfToken = $params['csrf_token'] ?? '';
        if (!$this->securityService->validateToken($csrfToken)) {
            $_SESSION['flash_message'] = 'Invalid security token. Please try again.';
            $_SESSION['flash_type'] = 'error';
            return new RedirectResponse('/settings');
        }
        
        // Get notification channel from form (radio button)
        $notificationChannel = $params['notification_channel'] ?? 'none';
        $notificationEmail = ($notificationChannel === 'email');
        $notificationNone = ($notificationChannel === 'none');
        
        // Get other settings from form
        $notificationTime = $params['notification_time'] ?? '18:00';
        $weeklySummary = isset($params['weekly_summary']) ? true : false;
        $monthlySummary = isset($params['monthly_summary']) ? true : false;
        
        // Validate notification time (HH:MM format)
        if (!preg_match('/^([01][0-9]|2[0-3]):([0-5][0-9])$/', $notificationTime)) {
            $notificationTime = '18:00'; // Default to 6:00 PM if invalid
        }
        
        // Update database
        $this->db->update('users', [
            'notification_email' => $notificationEmail ? 1 : 0,
            'notification_push' => 0, // We're not using push notifications in this version
            'notification_sms' => 0, // We're not using SMS notifications in this version
            'notification_time' => $notificationTime,
            'weekly_summary' => $weeklySummary ? 1 : 0,
            'monthly_summary' => $monthlySummary ? 1 : 0,
            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ], [
            'id' => $user->getId()
        ]);
        
        // Update user object
        if ($notificationNone) {
            $user->setNotificationNone(true);
        } else {
            $user->setNotificationEmail($notificationEmail);
        }
        
        $user->setNotificationTime($notificationTime);
        $user->setWeeklySummary($weeklySummary);
        $user->setMonthlySummary($monthlySummary);
        
        // Update notification preferences in session
        $_SESSION['user_notification_settings'] = [
            'email' => $notificationEmail,
            'none' => $notificationNone,
            'time' => $notificationTime,
            'weekly' => $weeklySummary,
            'monthly' => $monthlySummary
        ];
        
        // Set success message
        $this->translationService->setLocale($user->getLanguage() ?? 'en');
        $_SESSION['flash_message'] = $this->translationService->trans('notifications_updated', [], 'settings');
        $_SESSION['flash_type'] = 'success';
        
        // Redirect back to settings page with tab selection preserved
        return new RedirectResponse('/settings#tab-notifications');
    }
    
    /**
     * Update privacy settings
     */
    public function updatePrivacySettings(ServerRequestInterface $request): ResponseInterface
    {
        // Extract user data from request attributes (set by SessionAuthMiddleware)
        $user = $request->getAttribute('user', $this->user);
        
        if (!$user->isLoggedIn()) {
            return new RedirectResponse('/login');
        }
        
        // Parse request body
        $params = $request->getParsedBody();
        
        // Verify CSRF token
        $csrfToken = $params['csrf_token'] ?? '';
        if (!$this->securityService->validateToken($csrfToken)) {
            $_SESSION['flash_message'] = 'Invalid security token. Please try again.';
            $_SESSION['flash_type'] = 'error';
            return new RedirectResponse('/settings');
        }
        
        // Get settings from form
        $profileVisibility = $params['profile_visibility'] ?? 'public';
        
        // Validate profile visibility
        if (!in_array($profileVisibility, ['public', 'followers', 'private'])) {
            $profileVisibility = 'public';
        }
        
        // Update database
        $this->db->update('users', [
            'profile_visibility' => $profileVisibility,
            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ], [
            'id' => $user->getId()
        ]);
        
        // Update user object
        $user->setProfileVisibility($profileVisibility);
        
        // Store privacy settings in session for immediate effect
        $_SESSION['user_privacy_settings'] = [
            'profile_visibility' => $profileVisibility
        ];
        
        // Set success message
        $this->translationService->setLocale($user->getLanguage() ?? 'en');
        $_SESSION['flash_message'] = $this->translationService->trans('privacy_updated', [], 'settings');
        $_SESSION['flash_type'] = 'success';
        
        // Redirect back to settings page with tab selection preserved
        return new RedirectResponse('/settings#tab-privacy');
    }
    
    /**
     * Update language and timezone settings
     */
    public function updateLanguageSettings(ServerRequestInterface $request): ResponseInterface
    {
        // Extract user data from request attributes (set by SessionAuthMiddleware)
        $user = $request->getAttribute('user', $this->user);
        
        if (!$user->isLoggedIn()) {
            return new RedirectResponse('/login');
        }
        
        // Parse request body
        $params = $request->getParsedBody();
        
        // Verify CSRF token
        $csrfToken = $params['csrf_token'] ?? '';
        if (!$this->securityService->validateToken($csrfToken)) {
            $_SESSION['flash_message'] = 'Invalid security token. Please try again.';
            $_SESSION['flash_type'] = 'error';
            return new RedirectResponse('/settings');
        }
        
        // Get settings from form
        $language = $params['language'] ?? 'en';
        $timezone = $params['timezone'] ?? 'UTC';
        $autoTimezone = isset($params['auto_timezone']) ? true : false;
        
        // Validate language (simple validation, would be more robust in production)
        $validLanguages = ['en', 'pl', 'de', 'es', 'fr'];
        if (!in_array($language, $validLanguages)) {
            $language = 'en';
        }
        
        // Validate timezone
        $validTimezones = \DateTimeZone::listIdentifiers();
        if (!in_array($timezone, $validTimezones)) {
            $timezone = 'UTC';
        }
        
        // If auto timezone is enabled, try to detect it from browser
        if ($autoTimezone && isset($_COOKIE['timezone'])) {
            $browserTimezone = $_COOKIE['timezone'];
            // Validate that this is actually a valid timezone
            if (in_array($browserTimezone, $validTimezones)) {
                $timezone = $browserTimezone;
            }
        }
        
        // Update database
        $this->db->update('users', [
            'language' => $language,
            'timezone' => $timezone,
            'auto_timezone' => $autoTimezone ? 1 : 0,
            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ], [
            'id' => $user->getId()
        ]);
        
        // Update user object
        $user->setLanguage($language);
        $user->setTimezone($timezone);
        $user->setAutoTimezone($autoTimezone);
        
        // Update language and timezone in session for immediate effect
        $_SESSION['user_language'] = $language;
        $_SESSION['user_timezone'] = $timezone;
        $_SESSION['user'] = serialize($user);
        
        // Set translation locale to new language
        $this->translationService->setLocale($language);
        
        // Set success message
        $_SESSION['flash_message'] = $this->translationService->trans('language_updated', [], 'settings');
        $_SESSION['flash_type'] = 'success';
        
        // Redirect back to settings page with tab selection preserved
        return new RedirectResponse('/settings#tab-language');
    }
} 