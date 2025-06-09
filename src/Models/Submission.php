<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Models;

use Medoo\Medoo;
use Kpzsproductions\Challengify\Services\Database;
use Kpzsproductions\Challengify\Services\Container;

class Submission
{
    private string $id;
    private string $userId;
    private string $challengeId;
    private string $title;
    private string $description;
    private string $content;
    private ?string $filePath;
    private string $status;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;
    
    private static ?Medoo $db = null;
    
    public function __construct(
        string $id,
        string $userId,
        string $challengeId,
        string $title,
        string $description,
        string $content,
        ?string $filePath = null,
        string $status = 'submitted',
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->challengeId = $challengeId;
        $this->title = $title;
        $this->description = $description;
        $this->content = $content;
        $this->filePath = $filePath;
        $this->status = $status;
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
    
    public function getChallengeId(): string
    {
        return $this->challengeId;
    }
    
    public function setChallengeId(string $challengeId): void
    {
        $this->challengeId = $challengeId;
        $this->updatedAt = new \DateTime();
    }
    
    public function getTitle(): string
    {
        return $this->title;
    }
    
    public function setTitle(string $title): void
    {
        $this->title = $title;
        $this->updatedAt = new \DateTime();
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }
    
    public function setDescription(string $description): void
    {
        $this->description = $description;
        $this->updatedAt = new \DateTime();
    }
    
    public function getContent(): string
    {
        return $this->content;
    }
    
    public function setContent(string $content): void
    {
        $this->content = $content;
        $this->updatedAt = new \DateTime();
    }
    
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }
    
    public function setFilePath(?string $filePath): void
    {
        $this->filePath = $filePath;
        $this->updatedAt = new \DateTime();
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function setStatus(string $status): void
    {
        $this->status = $status;
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
        $data = self::getDb()->get('submissions', '*', ['id' => $id]);
        
        if (!$data) {
            return null;
        }
        
        return self::createFromArray($data);
    }
    
    public static function all(): array
    {
        $data = self::getDb()->select('submissions', '*');
        
        return array_map(function ($item) {
            return self::createFromArray($item);
        }, $data);
    }
    
    public static function findBy(array $conditions, array $orderBy = []): array
    {
        $options = [];
        
        // Process conditions to handle arrays properly
        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $options[$key . '[~]'] = $value; // Use [~] for IN operator in Medoo
            } else {
                $options[$key] = $value;
            }
        }
        
        if (!empty($orderBy)) {
            $options['ORDER'] = $orderBy;
        }
        
        $data = self::getDb()->select('submissions', '*', $options);
        
        return array_map(function ($item) {
            return self::createFromArray($item);
        }, $data);
    }
    
    public static function findOneBy(array $conditions): ?self
    {
        $data = self::getDb()->get('submissions', '*', $conditions);
        
        if (!$data) {
            return null;
        }
        
        return self::createFromArray($data);
    }
        
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['user_id'],
            $data['challenge_id'],
            $data['title'],
            $data['description'],
            $data['content'],
            $data['file_path'],
            $data['status'],
            new \DateTime($data['created_at']),
            $data['updated_at'] ? new \DateTime($data['updated_at']) : null
        );
    }
    
    public function save(): bool
    {
        $data = [
            'user_id' => $this->userId,
            'challenge_id' => $this->challengeId,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'file_path' => $this->filePath,
            'status' => $this->status,
            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
        
        // Check if record exists
        $exists = self::getDb()->get('submissions', 'id', ['id' => $this->id]);
        
        if ($exists) {
            // Update
            return self::getDb()->update('submissions', $data, ['id' => $this->id])->rowCount() > 0;
        } else {
            // Insert
            $data['id'] = $this->id;
            $data['created_at'] = $this->createdAt->format('Y-m-d H:i:s');
            return self::getDb()->insert('submissions', $data)->rowCount() > 0;
        }
    }
    
    public function delete(): bool
    {
        return self::getDb()->delete('submissions', ['id' => $this->id])->rowCount() > 0;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'challenge_id' => $this->challengeId,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'file_path' => $this->filePath,
            'status' => $this->status,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Find submission by user and challenge IDs
     * 
     * @param string $userId
     * @param string $challengeId
     * @return null|Submission
     */
    public static function findByUserAndChallenge($userId, $challengeId)
    {
        $result = self::getDb()->get("submissions", [
            "id",
            "user_id",
            "challenge_id",
            "title",
            "description", 
            "content",
            "file_path",
            "status",
            "created_at",
            "updated_at"
        ], [
            "AND" => [
                "user_id" => $userId,
                "challenge_id" => $challengeId
            ]
        ]);
        
        if ($result) {
            return self::createFromArray($result);
        }
        
        return null;
    }
} 