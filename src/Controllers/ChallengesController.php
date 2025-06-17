<?php

namespace Kpzsproductions\Challengify\Controllers;

use Kpzsproductions\Challengify\Models\Challenge;
use Kpzsproductions\Challengify\Models\Category;
use Kpzsproductions\Challengify\Models\Submission;
use Kpzsproductions\Challengify\Services\FileUploadService;
use Kpzsproductions\Challengify\Services\SecurityService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Ramsey\Uuid\Uuid;
use RuntimeException;

class ChallengesController
{
    private FileUploadService $fileUploadService;
    private SecurityService $securityService;
    
    public function __construct(
        FileUploadService $fileUploadService,
        SecurityService $securityService
    ) {
        $this->fileUploadService = $fileUploadService;
        $this->securityService = $securityService;
    }

    /**
     * Display a listing of challenges with pagination and filtering
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        // Get only active challenges
        $conditions = ['status' => 'active'];
        
        // Get challenges with filtering and pagination
        $challenges = Challenge::filterAndPaginate(
            $conditions,
            ['start_date' => 'DESC'],
            1,  // Only get one active challenge
            1
        );
        
        // Convert Challenge objects to arrays
        $challengesArray = array_map(function($challenge) {
            return $challenge->toArray();
        }, $challenges['data']);
            
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            // Return JSON response for AJAX requests
            $response = new Response();
            $response->getBody()->write(json_encode([
                'challenges' => $challengesArray,
                'pagination' => [
                    'total' => $challenges['total'],
                    'per_page' => $challenges['per_page'],
                    'current_page' => $challenges['current_page'],
                    'last_page' => $challenges['last_page']
                ]
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        }
        
        // Get the user from the request
        $user = $request->getAttribute('user');
        
        // Return view for normal requests
        return view('challenges/index', [
            'challenges' => $challengesArray,
            'pagination' => [
                'total' => $challenges['total'],
                'per_page' => $challenges['per_page'],
                'current_page' => $challenges['current_page'],
                'last_page' => $challenges['last_page']
            ],
            'user' => $user
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
            'status' => ['submitted', 'approved'] // Show both submitted and approved submissions
        ], ['created_at' => 'DESC']);
            
        $userSubmission = null;
        if (isLoggedIn()) {
            $currentUser = currentUser();
            if ($currentUser) {
                $userSubmission = Submission::findOneBy([
                    'challenge_id' => $id,
                    'user_id' => $currentUser->getId()
                ]);
            }
        }
        
        // Get the user from the request
        $user = $request->getAttribute('user');
        
        return view('challenges/show', [
            'challenge' => $challenge,
            'submissions' => $submissionsData,
            'userSubmission' => $userSubmission,
            'user' => $user
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
        $parsedBody = $this->securityService->sanitizeInput($request->getParsedBody());
        $title = $parsedBody['title'] ?? '';
        $description = $parsedBody['description'] ?? '';
        $content = $parsedBody['content'] ?? '';
        
        // CSRF validation
        $csrfToken = $parsedBody['csrf_token'] ?? '';
        if (!$this->securityService->validateToken($csrfToken, 'challenge_submission')) {
            setFlash('error', 'Invalid security token. Please try again.');
            return redirect("/challenges/{$challengeId}");
        }
        
        $errors = [];
        if (empty($title)) $errors['title'] = 'Title is required';
        if (empty($description)) $errors['description'] = 'Description is required';
        if (empty($content)) $errors['content'] = 'Content is required';
        
        // Handle file upload if present
        $filePath = null;
        $uploadedFiles = $request->getUploadedFiles();
        
        if (isset($uploadedFiles['submission_file']) && $uploadedFiles['submission_file']->getError() === UPLOAD_ERR_OK) {
            try {
                // Define allowed file types for submissions (more restrictive)
                $allowedTypes = [
                    // Images only
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                    // Documents - restrict to safer formats
                    'application/pdf', 
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain', 'text/csv'
                ];
                
                // Reduce max file size to 5MB
                $maxFileSize = 5 * 1024 * 1024;
                
                // Upload directory
                $uploadDir = __DIR__ . '/../../public/uploads/submissions';
                
                // Validate the file extension separately for extra security
                $originalFilename = $uploadedFiles['submission_file']->getClientFilename();
                $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
                
                // Define safe extensions
                $safeExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'docx', 'txt', 'csv'];
                
                if (!in_array(strtolower($extension), $safeExtensions)) {
                    throw new \RuntimeException('File type not allowed. Allowed types: images, PDF, DOCX, TXT, CSV.');
                }
                
                // Generate a random, unique filename to prevent file overwriting
                $newFilename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
                
                // Upload the file using the service
                $uploadResult = $this->fileUploadService->upload(
                    $uploadedFiles['submission_file'],
                    [
                        'directory' => $uploadDir,
                        'allowedTypes' => $allowedTypes,
                        'maxFileSize' => $maxFileSize,
                        'filename' => $newFilename
                    ]
                );
                
                // Set the public path for the file
                $filePath = '/uploads/submissions/' . $uploadResult['filename'];
                
                // Additional security: verify file content matches extension
                if (!$this->verifyFileContent($uploadDir . '/' . $uploadResult['filename'], $extension)) {
                    // If content doesn't match claimed type, delete the file
                    if (file_exists($uploadDir . '/' . $uploadResult['filename'])) {
                        unlink($uploadDir . '/' . $uploadResult['filename']);
                    }
                    throw new \RuntimeException('File content does not match its extension.');
                }
            } catch (RuntimeException $e) {
                $errors['file'] = $e->getMessage();
            }
        }
        
        // If there are errors, return to the form with error messages
        if (!empty($errors)) {
            // Get the user from the request
            $user = $request->getAttribute('user');
            
            return view('challenges/show', [
                'challenge' => $challenge,
                'errors' => $errors,
                'old' => $parsedBody,
                'user' => $user
            ]);
        }
        
        // Create or update submission
        $currentUser = currentUser();
        if (!$currentUser) {
            setFlash('error', 'User session expired. Please log in again.');
            return redirect('/login');
        }
        
        $submission = Submission::findOneBy([
            'user_id' => $currentUser->getId(),
            'challenge_id' => $challengeId
        ]);
            
        if ($submission) {
            // Update existing submission
            $submissionData = $submission->toArray();
            $submissionData['title'] = $title;
            $submissionData['description'] = $description;
            $submissionData['content'] = $content;
            if ($filePath) {
                // If there was an old file, we should delete it
                if ($submission->getFilePath() && file_exists(__DIR__ . '/../../public' . $submission->getFilePath())) {
                    unlink(__DIR__ . '/../../public' . $submission->getFilePath());
                }
                $submissionData['file_path'] = $filePath;
            }
            $submission = Submission::createFromArray($submissionData);
            $submission->setStatus('approved'); // Set to approved so it's visible
            $submission->save();
        } else {
            // Create new submission with a UUID
            $submission = new Submission(
                Uuid::uuid4()->toString(),
                $currentUser->getId(),
                $challengeId,
                $title,
                $description,
                $content,
                $filePath,
                'approved' // Set to approved by default so it's visible
            );
            $submission->save();
        }
        
        setFlash('success', 'Your submission has been received!');
        return redirect("/challenges/{$challengeId}");
    }

    /**
     * Allow a user to download their submission file
     */
    public function downloadSubmission($id)
    {
        // Ensure user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_messages'][] = [
                'type' => 'error',
                'message' => 'You must be logged in to download a submission.'
            ];
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $challenge = Challenge::find($id);
        
        if (!$challenge) {
            $_SESSION['flash_messages'][] = [
                'type' => 'error',
                'message' => 'Challenge not found.'
            ];
            header('Location: /challenges');
            exit;
        }

        // Find the user's submission for this challenge
        $submission = Submission::findByUserAndChallenge($userId, $id);
        
        if (!$submission || !$submission->getFilePath()) {
            $_SESSION['flash_messages'][] = [
                'type' => 'error',
                'message' => 'No file submission found.'
            ];
            header('Location: /challenges/' . $id);
            exit;
        }

        $filePath = $submission->getFilePath();
        
        // Verify file exists - need to prepend public directory path
        $absoluteFilePath = __DIR__ . '/../../public' . $filePath;
        if (!file_exists($absoluteFilePath)) {
            $_SESSION['flash_messages'][] = [
                'type' => 'error',
                'message' => 'The submission file could not be found.'
            ];
            header('Location: /challenges/' . $id);
            exit;
        }

        // Get file info
        $fileName = basename($filePath);
        $fileSize = filesize($absoluteFilePath);
        $fileType = mime_content_type($absoluteFilePath);

        // Set headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $fileType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $fileSize);
        
        // Output file and stop script execution
        readfile($absoluteFilePath);
        exit;
    }

    /**
     * Verify file content matches claimed file extension
     * 
     * @param string $filePath Full path to the file
     * @param string $extension File extension to check against
     * @return bool Whether the content matches the extension
     */
    private function verifyFileContent(string $filePath, string $extension): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }
        
        // Verify using file signatures (magic numbers)
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            return false;
        }
        
        // Read first 12 bytes for file signature
        $fileStart = fread($handle, 12);
        fclose($handle);
        
        // Convert to hex for signature comparison
        $hex = bin2hex($fileStart);
        
        // Validate based on extension and file signature
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                // JPEG signature: FF D8 FF
                return strpos($hex, 'ffd8ff') === 0;
                
            case 'png':
                // PNG signature: 89 50 4E 47 0D 0A 1A 0A
                return strpos($hex, '89504e470d0a1a0a') === 0;
                
            case 'gif':
                // GIF signature: 47 49 46 38 (GIF8)
                return strpos($hex, '47494638') === 0;
                
            case 'pdf':
                // PDF signature: 25 50 44 46 (PDF)
                return strpos($hex, '25504446') === 0;
                
            case 'docx':
                // DOCX files are ZIP files with specific content
                // ZIP signature: 50 4B 03 04
                return strpos($hex, '504b0304') === 0;
                
            case 'txt':
            case 'csv':
                // Text files don't have a specific signature
                // We'll check that it's ASCII/UTF-8 text
                return $this->isTextFile($filePath);
                
            default:
                // For unknown types, reject
                return false;
        }
    }
    
    /**
     * Check if a file contains only text characters
     * 
     * @param string $filePath Path to the file
     * @return bool Whether the file contains only text
     */
    private function isTextFile(string $filePath): bool
    {
        // Read file content
        $content = file_get_contents($filePath);
        if ($content === false) {
            return false;
        }
        
        // Check for binary content (null bytes or control characters except tabs/newlines)
        if (preg_match('/[\x00-\x08\x0B-\x0C\x0E-\x1F]/', $content)) {
            return false;
        }
        
        // Check UTF-8 validity
        return mb_check_encoding($content, 'UTF-8');
    }
} 