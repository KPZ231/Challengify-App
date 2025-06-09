<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Controllers;

use Kpzsproductions\Challengify\Models\Challenge;
use Kpzsproductions\Challengify\Models\Category;
use Kpzsproductions\Challengify\Models\User;
use Medoo\Medoo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\RedirectResponse;

class AdminController
{
    private Medoo $db;
    
    public function __construct(Medoo $db)
    {
        $this->db = $db;
    }
    
    /**
     * Admin dashboard
     */
    public function dashboard(ServerRequestInterface $request): ResponseInterface
    {
        // Get counts for various entities
        $challengesCount = $this->db->count('challenges');
        $usersCount = $this->db->count('users');
        $submissionsCount = $this->db->count('submissions');
        $categoriesCount = $this->db->count('categories');
        
        // Get latest challenges
        $latestChallenges = $this->db->select(
            'challenges',
            ['id', 'title', 'status', 'created_at'],
            ['ORDER' => ['created_at' => 'DESC'], 'LIMIT' => 5]
        );
        
        // Get latest users
        $latestUsers = $this->db->select(
            'users',
            ['id', 'username', 'email', 'created_at'],
            ['ORDER' => ['created_at' => 'DESC'], 'LIMIT' => 5]
        );
        
        return view('admin/dashboard', [
            'challengesCount' => $challengesCount,
            'usersCount' => $usersCount,
            'submissionsCount' => $submissionsCount,
            'categoriesCount' => $categoriesCount,
            'latestChallenges' => $latestChallenges,
            'latestUsers' => $latestUsers
        ]);
    }
    
    /**
     * List all challenges
     */
    public function challenges(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $total = $this->db->count('challenges');
        
        // Get paginated results
        $challenges = $this->db->select(
            'challenges',
            ['id', 'title', 'status', 'start_date', 'end_date', 'created_at'],
            ['ORDER' => ['created_at' => 'DESC'], 'LIMIT' => [$offset, $perPage]]
        );
        
        return view('admin/challenges', [
            'challenges' => $challenges,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]
        ]);
    }
    
    /**
     * Show form to create a new challenge
     */
    public function createChallengeForm(ServerRequestInterface $request): ResponseInterface
    {
        // Get categories from database
        $categories = $this->db->select('categories', [
            'id',
            'name',
            'description'
        ], ['ORDER' => ['name' => 'ASC']]);
        
        return view('admin/challenge-form', [
            'action' => 'create',
            'categories' => $categories
        ]);
    }
    
    /**
     * Process challenge creation
     */
    public function createChallenge(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('user');
        if (!$user || $user->getRole() !== 'admin') {
            return redirect('/');
        }
        
        $parsedBody = $request->getParsedBody();
        $userId = $user->getId();
        $title = $parsedBody['title'] ?? '';
        $description = $parsedBody['description'] ?? '';
        $difficulty = $parsedBody['difficulty'] ?? 'medium';
        $rules = $parsedBody['rules'] ?? null;
        $submissionGuidelines = $parsedBody['submission_guidelines'] ?? null;
        $startDate = $parsedBody['start_date'] ?? date('Y-m-d H:i:s');
        $endDate = $parsedBody['end_date'] ?? date('Y-m-d H:i:s', strtotime('+7 days'));
        $status = $parsedBody['status'] ?? 'draft';
        
        // Get category ID from form or use default if not set
        $categoryId = $parsedBody['category_id'] ?? 'ec602ddd-44a8-11f0-aafb-74563c6dd840'; // Creative Writing as fallback
        
        // Get categories for the form (needed in case of validation errors)
        $categories = $this->db->select('categories', [
            'id',
            'name',
            'description'
        ], ['ORDER' => ['name' => 'ASC']]);
        
        // Validation
        $errors = [];
        if (empty($title)) $errors['title'] = 'Title is required';
        if (empty($description)) $errors['description'] = 'Description is required';
        
        if (!empty($errors)) {
            return view('admin/challenge-form', [
                'action' => 'create',
                'errors' => $errors,
                'old' => $parsedBody,
                'categories' => $categories
            ]);
        }
        
        // Generate UUID for new challenge
        $challengeId = generateUuid();
        
        // Handle file upload if present
        $image = null;
        $uploadedFiles = $request->getUploadedFiles();
        if (isset($uploadedFiles['image']) && $uploadedFiles['image']->getError() === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/challenges/';
            $fileName = Uuid::uuid4()->toString() . '.' . pathinfo(
                $uploadedFiles['image']->getClientFilename(),
                PATHINFO_EXTENSION
            );
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $uploadedFiles['image']->moveTo($uploadDir . $fileName);
            $image = $fileName;
        }
        
        // Create challenge
        $this->db->insert('challenges', [
            'id' => $challengeId,
            'user_id' => $userId,
            'category_id' => $categoryId,
            'title' => $title,
            'description' => $description,
            'difficulty' => $difficulty,
            'rules' => $rules,
            'submission_guidelines' => $submissionGuidelines,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
            'image' => $image,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        setFlash('success', 'Challenge created successfully');
        return new RedirectResponse('/admin/challenges');
    }
    
    /**
     * Show form to edit a challenge
     */
    public function editChallengeForm(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $id = $args['id'];
        
        $challenge = $this->db->get('challenges', '*', ['id' => $id]);
        if (!$challenge) {
            setFlash('error', 'Challenge not found');
            return new RedirectResponse('/admin/challenges');
        }
        
        // Get categories from database
        $categories = $this->db->select('categories', [
            'id',
            'name',
            'description'
        ], ['ORDER' => ['name' => 'ASC']]);
        
        return view('admin/challenge-form', [
            'action' => 'edit',
            'challenge' => $challenge,
            'categories' => $categories
        ]);
    }
    
    /**
     * Process challenge update
     */
    public function updateChallenge(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $id = $args['id'];
        
        $challenge = $this->db->get('challenges', '*', ['id' => $id]);
        if (!$challenge) {
            setFlash('error', 'Challenge not found');
            return new RedirectResponse('/admin/challenges');
        }
        
        $parsedBody = $request->getParsedBody();
        $title = $parsedBody['title'] ?? '';
        $description = $parsedBody['description'] ?? '';
        $difficulty = $parsedBody['difficulty'] ?? 'medium';
        $categoryId = $parsedBody['category_id'] ?? $challenge['category_id']; // Use submitted category or keep existing one
        $rules = $parsedBody['rules'] ?? null;
        $submissionGuidelines = $parsedBody['submission_guidelines'] ?? null;
        $startDate = $parsedBody['start_date'] ?? date('Y-m-d H:i:s');
        $endDate = $parsedBody['end_date'] ?? date('Y-m-d H:i:s', strtotime('+7 days'));
        $status = $parsedBody['status'] ?? 'draft';
        
        // Validation
        $errors = [];
        if (empty($title)) $errors['title'] = 'Title is required';
        if (empty($description)) $errors['description'] = 'Description is required';
        
        if (!empty($errors)) {
            return view('admin/challenge-form', [
                'action' => 'edit',
                'challenge' => $challenge,
                'errors' => $errors,
                'old' => $parsedBody
            ]);
        }
        
        // Handle file upload if present
        $image = $challenge['image'];
        $uploadedFiles = $request->getUploadedFiles();
        if (isset($uploadedFiles['image']) && $uploadedFiles['image']->getError() === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../uploads/challenges/';
            $fileName = Uuid::uuid4()->toString() . '.' . pathinfo(
                $uploadedFiles['image']->getClientFilename(),
                PATHINFO_EXTENSION
            );
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $uploadedFiles['image']->moveTo($uploadDir . $fileName);
            
            // Remove old image if exists
            if ($challenge['image'] && file_exists($uploadDir . $challenge['image'])) {
                unlink($uploadDir . $challenge['image']);
            }
            
            $image = $fileName;
        }
        
        // Update challenge
        $this->db->update('challenges', [
            'title' => $title,
            'description' => $description,
            'difficulty' => $difficulty,
            'category_id' => $categoryId,
            'rules' => $rules,
            'submission_guidelines' => $submissionGuidelines,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
            'image' => $image,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
        
        setFlash('success', 'Challenge updated successfully');
        return new RedirectResponse('/admin/challenges');
    }
    
    /**
     * Delete a challenge
     */
    public function deleteChallenge(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $id = $args['id'];
        
        $challenge = $this->db->get('challenges', '*', ['id' => $id]);
        if (!$challenge) {
            setFlash('error', 'Challenge not found');
            return new RedirectResponse('/admin/challenges');
        }
        
        // Delete challenge image if exists
        if ($challenge['image']) {
            $uploadDir = __DIR__ . '/../../uploads/challenges/';
            if (file_exists($uploadDir . $challenge['image'])) {
                unlink($uploadDir . $challenge['image']);
            }
        }
        
        // Delete challenge
        $this->db->delete('challenges', ['id' => $id]);
        
        setFlash('success', 'Challenge deleted successfully');
        return new RedirectResponse('/admin/challenges');
    }
    
    /**
     * List all users
     */
    public function users(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $total = $this->db->count('users');
        
        // Get paginated results
        $users = $this->db->select(
            'users',
            ['id', 'username', 'email', 'role', 'created_at'],
            ['ORDER' => ['created_at' => 'DESC'], 'LIMIT' => [$offset, $perPage]]
        );
        
        return view('admin/users', [
            'users' => $users,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]
        ]);
    }
    
    /**
     * Update user role
     */
    public function updateUserRole(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $id = $args['id'];
        $parsedBody = $request->getParsedBody();
        $role = $parsedBody['role'] ?? 'user';
        
        $user = $this->db->get('users', '*', ['id' => $id]);
        if (!$user) {
            setFlash('error', 'User not found');
            return new RedirectResponse('/admin/users');
        }
        
        // Update user role
        $this->db->update('users', [
            'role' => $role,
            'updated_at' => date('Y-m-d H:i:s')
        ], ['id' => $id]);
        
        setFlash('success', 'User role updated successfully');
        return new RedirectResponse('/admin/users');
    }
    
    /**
     * View system logs
     */
    public function logs(ServerRequestInterface $request): ResponseInterface
    {
        $logFile = __DIR__ . '/../../logs/app.log';
        $logs = [];
        
        if (file_exists($logFile)) {
            $logs = file($logFile);
            $logs = array_reverse($logs); // Most recent first
            $logs = array_slice($logs, 0, 100); // Limit to 100 entries
        }
        
        return view('admin/logs', [
            'logs' => $logs
        ]);
    }
    
    /**
     * View server console (real-time server logs)
     */
    public function console(ServerRequestInterface $request): ResponseInterface
    {
        return view('admin/console', []);
    }
} 