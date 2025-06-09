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
use Medoo\Medoo;

class UserController
{
    private User $user;
    private SecurityService $securityService;
    private FileUploadService $fileUploadService;
    private Medoo $db;

    public function __construct(
        User $user,
        SecurityService $securityService,
        FileUploadService $fileUploadService,
        Medoo $db
    ) {
        $this->user = $user;
        $this->securityService = $securityService;
        $this->fileUploadService = $fileUploadService;
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
            // Upload file with avatar-specific settings
            $fileData = $this->fileUploadService->upload(
                $uploadedFiles['avatar'], 
                [
                    'allowedTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                    'maxFileSize' => 2 * 1024 * 1024, // 2MB
                    'directory' => 'public/uploads/avatars'
                ]
            );
            
            // Delete old avatar if exists
            $oldAvatar = $user->getAvatar();
            if ($oldAvatar && file_exists('public/uploads/avatars/' . $oldAvatar)) {
                unlink('public/uploads/avatars/' . $oldAvatar);
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
} 