<?php

namespace Kpzsproductions\Challengify\Controllers;

use Kpzsproductions\Challengify\Models\Challenge;
use Kpzsproductions\Challengify\Models\Category;
use Kpzsproductions\Challengify\Models\Submission;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;

class ChallengesController
{
    /**
     * Display a listing of challenges with pagination and filtering
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $categories = Category::all();
        $queryParams = $request->getQueryParams();
        $categoryId = $queryParams['category'] ?? null;
        $page = isset($queryParams['page']) ? (int)$queryParams['page'] : 1;
        $perPage = 10;
        
        // Build conditions array
        $conditions = ['status' => 'active'];
        if ($categoryId) {
            $conditions['category_id'] = $categoryId;
        }
        
        // Get challenges with filtering and pagination
        $challenges = Challenge::filterAndPaginate(
            $conditions,
            ['start_date' => 'DESC'],
            $perPage,
            $page
        );
            
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            // Return JSON response for AJAX requests
            $response = new Response();
            $response->getBody()->write(json_encode([
                'challenges' => $challenges['data'],
                'pagination' => [
                    'total' => $challenges['total'],
                    'per_page' => $challenges['per_page'],
                    'current_page' => $challenges['current_page'],
                    'last_page' => $challenges['last_page']
                ]
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        // Return view for normal requests
        return view('challenges/index', [
            'categories' => $categories,
            'challenges' => $challenges['data'],
            'pagination' => [
                'total' => $challenges['total'],
                'per_page' => $challenges['per_page'],
                'current_page' => $challenges['current_page'],
                'last_page' => $challenges['last_page']
            ],
            'selectedCategory' => $categoryId
        ]);
    }
    
    /**
     * Display the specified challenge with submissions
     */
    public function show(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $id = $args['id'];
        $challenge = Challenge::find($id);
        
        if (!$challenge) {
            return redirect('/challenges');
        }
        
        // Get approved submissions for this challenge
        $submissionsData = Submission::findBy([
            'challenge_id' => $id,
            'status' => 'approved'
        ], ['created_at' => 'DESC']);
            
        $userSubmission = null;
        if (isLoggedIn()) {
            $userSubmission = Submission::findOneBy([
                'challenge_id' => $id,
                'user_id' => currentUser()->getId()
            ]);
        }
        
        return view('challenges/show', [
            'challenge' => $challenge,
            'submissions' => $submissionsData,
            'userSubmission' => $userSubmission
        ]);
    }
    
    /**
     * Store a new submission for a challenge
     */
    public function submitEntry(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $challengeId = $args['id'];
        
        if (!isLoggedIn()) {
            return redirect('/login');
        }
        
        $challenge = Challenge::find($challengeId);
        if (!$challenge) {
            return redirect('/challenges');
        }
        
        // Check if challenge is active
        if ($challenge->getStatus() !== 'active') {
            setFlash('error', 'This challenge is no longer accepting submissions.');
            return redirect("/challenges/{$challengeId}");
        }
        
        // Validate request
        $parsedBody = $request->getParsedBody();
        $title = $parsedBody['title'] ?? '';
        $description = $parsedBody['description'] ?? '';
        $content = $parsedBody['content'] ?? '';
        
        $errors = [];
        if (empty($title)) $errors['title'] = 'Title is required';
        if (empty($description)) $errors['description'] = 'Description is required';
        if (empty($content)) $errors['content'] = 'Content is required';
        
        // Handle file upload if present
        $filePath = null;
        $uploadedFiles = $request->getUploadedFiles();
        if (isset($uploadedFiles['submission_file']) && $uploadedFiles['submission_file']->getError() === UPLOAD_ERR_OK) {
            $uploadDir = 'public/uploads/submissions/';
            $fileName = uniqid() . '_' . $uploadedFiles['submission_file']->getClientFilename();
            $targetFile = $uploadDir . $fileName;
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $uploadedFiles['submission_file']->moveTo($targetFile);
            $filePath = '/uploads/submissions/' . $fileName;
        }
        
        if (!empty($errors)) {
            return view('challenges/show', [
                'challenge' => $challenge,
                'errors' => $errors,
                'old' => $parsedBody
            ]);
        }
        
        // Create or update submission
        $submission = Submission::findOneBy([
            'user_id' => currentUser()->getId(),
            'challenge_id' => $challengeId
        ]);
            
        if ($submission) {
            // Update existing submission
            $submissionData = $submission->toArray();
            $submissionData['title'] = $title;
            $submissionData['description'] = $description;
            $submissionData['content'] = $content;
            if ($filePath) {
                $submissionData['file_path'] = $filePath;
            }
            $submission = Submission::createFromArray($submissionData);
            $submission->setStatus('submitted');
            $submission->save();
        } else {
            // Create new submission
            $submission = new Submission(
                generateUuid(),
                currentUser()->getId(),
                $challengeId,
                $title,
                $description,
                $content,
                $filePath,
                'submitted'
            );
            $submission->save();
        }
        
        setFlash('success', 'Your submission has been received!');
        return redirect("/challenges/{$challengeId}");
    }
} 