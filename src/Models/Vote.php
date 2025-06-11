<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Models;

use Medoo\Medoo;
use Kpzsproductions\Challengify\Services\Database;
use Ramsey\Uuid\Uuid;

class Vote
{
    private string $id;
    private string $userId;
    private string $submissionId;
    private string $voteType;
    private ?string $comment;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;
    
    private static ?Medoo $db = null;
    
    public function __construct(
        string $id,
        string $userId,
        string $submissionId,
        string $voteType,
        ?string $comment = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->submissionId = $submissionId;
        $this->voteType = $voteType;
        $this->comment = $comment;
        $this->createdAt = $createdAt ?? new \DateTime();
        $this->updatedAt = $updatedAt;
    }
    
    // Getters and setters
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getUserId(): string
    {
        return $this->userId;
    }
    
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
        $this->updatedAt = new \DateTime();
    }
    
    public function getSubmissionId(): string
    {
        return $this->submissionId;
    }
    
    public function setSubmissionId(string $submissionId): void
    {
        $this->submissionId = $submissionId;
        $this->updatedAt = new \DateTime();
    }
    
    public function getVoteType(): string
    {
        return $this->voteType;
    }
    
    public function setVoteType(string $voteType): void
    {
        $this->voteType = $voteType;
        $this->updatedAt = new \DateTime();
    }
    
    public function getComment(): ?string
    {
        return $this->comment;
    }
    
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
        $this->updatedAt = new \DateTime();
    }
    
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
    
    // Database operations
    private static function getDb(): Medoo
    {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }
    
    public static function find(string $id): ?self
    {
        $data = self::getDb()->get('votes', '*', ['id' => $id]);
        
        if (!$data) {
            return null;
        }
        
        return self::createFromArray($data);
    }
    
    public static function findByUserAndSubmission(string $userId, string $submissionId): ?self
    {
        $data = self::getDb()->get('votes', '*', [
            'user_id' => $userId,
            'submission_id' => $submissionId
        ]);
        
        if (!$data) {
            return null;
        }
        
        return self::createFromArray($data);
    }
    
    public static function countVotesBySubmission(string $submissionId, string $voteType = 'upvote'): int
    {
        return self::getDb()->count('votes', [
            'submission_id' => $submissionId,
            'vote_type' => $voteType
        ]);
    }
    
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['user_id'],
            $data['submission_id'],
            $data['vote_type'],
            $data['comment'],
            new \DateTime($data['created_at']),
            $data['updated_at'] ? new \DateTime($data['updated_at']) : null
        );
    }
    
    public function save(): bool
    {
        $data = [
            'user_id' => $this->userId,
            'submission_id' => $this->submissionId,
            'vote_type' => $this->voteType,
            'comment' => $this->comment,
            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
        
        // Check if record exists
        $exists = self::getDb()->get('votes', 'id', ['id' => $this->id]);
        
        if ($exists) {
            // Update
            return self::getDb()->update('votes', $data, ['id' => $this->id])->rowCount() > 0;
        } else {
            // Insert
            $data['id'] = $this->id;
            $data['created_at'] = $this->createdAt->format('Y-m-d H:i:s');
            return self::getDb()->insert('votes', $data)->rowCount() > 0;
        }
    }
    
    public function delete(): bool
    {
        return self::getDb()->delete('votes', ['id' => $this->id])->rowCount() > 0;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'submission_id' => $this->submissionId,
            'vote_type' => $this->voteType,
            'comment' => $this->comment,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null
        ];
    }
    
    /**
     * Create a new vote or update existing one
     */
    public static function createOrUpdate(string $userId, string $submissionId, string $voteType): self
    {
        // Check if vote already exists
        $vote = self::findByUserAndSubmission($userId, $submissionId);
        
        if ($vote) {
            // Update existing vote
            $vote->setVoteType($voteType);
            $vote->save();
            return $vote;
        } else {
            // Create new vote
            $vote = new self(
                Uuid::uuid4()->toString(),
                $userId,
                $submissionId,
                $voteType
            );
            $vote->save();
            return $vote;
        }
    }
} 