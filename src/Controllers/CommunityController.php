<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Kpzsproductions\Challengify\Models\User;
use Medoo\Medoo;

class CommunityController
{
    private User $user;
    private Medoo $db;
    
    public function __construct(
        User $user,
        Medoo $db
    ) {
        $this->user = $user;
        $this->db = $db;
    }
    
    /**
     * Display the community page with users list
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        // Create new response
        $response = new Response();
        
        // Get current user from session if logged in
        $currentUser = $request->getAttribute('user', $this->user);
        
        // Get pagination parameters
        $page = (int) ($request->getQueryParams()['page'] ?? 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        // Get search parameter
        $search = $request->getQueryParams()['search'] ?? '';
        
        // Get filter parameter for showing private profiles (for admins only)
        $showPrivate = false;
        if (isset($request->getQueryParams()['show_private']) && $currentUser->isAdmin()) {
            $showPrivate = (bool)$request->getQueryParams()['show_private'];
        }
        
        // Base query conditions
        $conditions = [];
        
        // Hide users with private profiles unless explicitly shown by admin
        if (!$showPrivate) {
            $conditions['profile_visibility[!]'] = 'private';
        }
        
        // Add search condition if provided
        if (!empty($search)) {
            $conditions['OR'] = [
                'username[~]' => $search,
                'bio[~]' => $search
            ];
            
            // If we're searching, we need to maintain the visibility filter
            if (!$showPrivate) {
                $conditions['AND'] = [
                    'profile_visibility[!]' => 'private'
                ];
            }
        }
        
        // Count total users with the search condition
        $totalUsers = $this->db->count('users', $conditions);
        
        // Calculate total pages
        $totalPages = ceil($totalUsers / $perPage);
        
        // Get users with pagination
        $users = $this->db->select(
            'users',
            [
                'id',
                'username',
                'avatar',
                'bio',
                'reputation',
                'created_at',
                'profile_visibility'
            ],
            array_merge($conditions, [
                'LIMIT' => [$offset, $perPage],
                'ORDER' => ['reputation' => 'DESC']
            ])
        ) ?: [];
        
        // Track user preference
        if (isset($request->getQueryParams()['show_private']) && $currentUser->isAdmin()) {
            $_SESSION['admin_show_private_profiles'] = $showPrivate;
        }
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        require __DIR__ . '/../Views/community.php';
        
        // Get buffered content
        $content = ob_get_clean();
        
        // Write content to response body
        $response->getBody()->write($content);
        
        return $response;
    }
} 