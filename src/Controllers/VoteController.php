<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Controllers;

use Kpzsproductions\Challengify\Models\Vote;
use Kpzsproductions\Challengify\Services\SecurityService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Ramsey\Uuid\Uuid;

class VoteController
{
    private SecurityService $securityService;
    
    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }
    
    /**
     * Handle vote submission via AJAX
     */
    public function vote(ServerRequestInterface $request): ResponseInterface
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'You must be logged in to vote'
            ], 401);
        }
        
        // Get the current user
        $currentUser = currentUser();
        if (!$currentUser) {
            return new JsonResponse([
                'success' => false,
                'message' => 'User session expired. Please log in again.'
            ], 401);
        }
        
        // Parse request body
        $parsedBody = $request->getParsedBody();
        
        // Validate CSRF token
        if (!isset($parsedBody['csrf_token']) || !$this->securityService->validateToken($parsedBody['csrf_token'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid CSRF token'
            ], 403);
        }
        
        // Validate required fields
        if (!isset($parsedBody['submission_id']) || !isset($parsedBody['vote_type'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Missing required fields'
            ], 400);
        }
        
        $submissionId = $parsedBody['submission_id'];
        $voteType = $parsedBody['vote_type'];
        
        // Validate vote type
        if (!in_array($voteType, ['upvote', 'downvote'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid vote type'
            ], 400);
        }
        
        try {
            // Create or update the vote
            $vote = Vote::createOrUpdate($currentUser->getId(), $submissionId, $voteType);
            
            // Get the updated vote count
            $voteCount = Vote::countVotesBySubmission($submissionId, 'upvote');
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Vote recorded successfully',
                'vote_count' => $voteCount
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred while processing your vote'
            ], 500);
        }
    }
    
    /**
     * Get vote count for a submission
     */
    public function getVoteCount(ServerRequestInterface $request, array $args): ResponseInterface
    {
        $submissionId = $args['id'];
        
        try {
            $voteCount = Vote::countVotesBySubmission($submissionId, 'upvote');
            
            return new JsonResponse([
                'success' => true,
                'vote_count' => $voteCount
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred while retrieving the vote count'
            ], 500);
        }
    }
    
    /**
     * Check if user has voted on a submission
     */
    public function hasVoted(ServerRequestInterface $request, array $args): ResponseInterface
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            return new JsonResponse([
                'success' => false,
                'has_voted' => false
            ]);
        }
        
        $submissionId = $args['id'];
        $currentUser = currentUser();
        
        if (!$currentUser) {
            return new JsonResponse([
                'success' => false,
                'has_voted' => false
            ]);
        }
        
        try {
            $vote = Vote::findByUserAndSubmission($currentUser->getId(), $submissionId);
            
            return new JsonResponse([
                'success' => true,
                'has_voted' => $vote !== null,
                'vote_type' => $vote ? $vote->getVoteType() : null
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'An error occurred while checking vote status'
            ], 500);
        }
    }
} 