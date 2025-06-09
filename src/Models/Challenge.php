<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Models;

use Medoo\Medoo;
use Kpzsproductions\Challengify\Services\Database;

class Challenge
{
    private string $id;
    private string $userId;
    private string $categoryId;
    private string $title;
    private string $description;
    private string $difficulty;
    private ?string $rules;
    private ?string $submissionGuidelines;
    private \DateTime $startDate;
    private \DateTime $endDate;
    private string $status;
    private ?string $image;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;
    
    private static ?Medoo $db = null;
    
    public function __construct(
        string $id,
        string $userId,
        string $categoryId,
        string $title,
        string $description,
        string $difficulty = 'medium',
        ?string $rules = null,
        ?string $submissionGuidelines = null,
        ?\DateTime $startDate = null,
        ?\DateTime $endDate = null,
        string $status = 'draft',
        ?string $image = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->categoryId = $categoryId;
        $this->title = $title;
        $this->description = $description;
        $this->difficulty = $difficulty;
        $this->rules = $rules;
        $this->submissionGuidelines = $submissionGuidelines;
        $this->startDate = $startDate ?? new \DateTime();
        $this->endDate = $endDate ?? (new \DateTime())->modify('+7 days');
        $this->status = $status;
        $this->image = $image;
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
    
    public function getCategoryId(): string
    {
        return $this->categoryId;
    }
    
    public function setCategoryId(string $categoryId): void
    {
        $this->categoryId = $categoryId;
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
    
    public function getDifficulty(): string
    {
        return $this->difficulty;
    }
    
    public function setDifficulty(string $difficulty): void
    {
        $this->difficulty = $difficulty;
        $this->updatedAt = new \DateTime();
    }
    
    public function getRules(): ?string
    {
        return $this->rules;
    }
    
    public function setRules(?string $rules): void
    {
        $this->rules = $rules;
        $this->updatedAt = new \DateTime();
    }
    
    public function getSubmissionGuidelines(): ?string
    {
        return $this->submissionGuidelines;
    }
    
    public function setSubmissionGuidelines(?string $submissionGuidelines): void
    {
        $this->submissionGuidelines = $submissionGuidelines;
        $this->updatedAt = new \DateTime();
    }
    
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }
    
    public function setStartDate(\DateTime $startDate): void
    {
        $this->startDate = $startDate;
        $this->updatedAt = new \DateTime();
    }
    
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }
    
    public function setEndDate(\DateTime $endDate): void
    {
        $this->endDate = $endDate;
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
    
    public function getImage(): ?string
    {
        return $this->image;
    }
    
    public function setImage(?string $image): void
    {
        $this->image = $image;
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
        $data = self::getDb()->get('challenges', '*', ['id' => $id]);
        
        if (!$data) {
            return null;
        }
        
        return self::createFromArray($data);
    }
    
    public static function all(): array
    {
        $data = self::getDb()->select('challenges', '*');
        
        return array_map(function ($item) {
            return self::createFromArray($item);
        }, $data);
    }
    
    public static function where(string $column, $value): array
    {
        $data = self::getDb()->select('challenges', '*', [$column => $value]);
        
        return array_map(function ($item) {
            return self::createFromArray($item);
        }, $data);
    }
    
    public static function filterAndPaginate(array $conditions = [], array $orderBy = [], int $perPage = 10, int $page = 1): array
    {
        $db = self::getDb();
        
        // Calculate offset
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $total = $db->count('challenges', $conditions);
        
        // Build options for select query
        $options = $conditions;
        
        if (!empty($orderBy)) {
            $options['ORDER'] = $orderBy;
        }
        
        $options['LIMIT'] = [$offset, $perPage];
        
        // Get paginated data
        $data = $db->select('challenges', '*', $options);
        
        // Map to objects
        $challenges = array_map(function ($item) {
            return self::createFromArray($item);
        }, $data);
        
        // Calculate last page
        $lastPage = ceil($total / $perPage);
        
        return [
            'data' => $challenges,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage
        ];
    }
    
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['user_id'],
            $data['category_id'],
            $data['title'],
            $data['description'],
            $data['difficulty'],
            $data['rules'],
            $data['submission_guidelines'],
            new \DateTime($data['start_date']),
            new \DateTime($data['end_date']),
            $data['status'],
            $data['image'],
            new \DateTime($data['created_at']),
            $data['updated_at'] ? new \DateTime($data['updated_at']) : null
        );
    }
    
    public function save(): bool
    {
        $data = [
            'user_id' => $this->userId,
            'category_id' => $this->categoryId,
            'title' => $this->title,
            'description' => $this->description,
            'difficulty' => $this->difficulty,
            'rules' => $this->rules,
            'submission_guidelines' => $this->submissionGuidelines,
            'start_date' => $this->startDate->format('Y-m-d H:i:s'),
            'end_date' => $this->endDate->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'image' => $this->image,
            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
        
        // Check if record exists
        $exists = self::getDb()->get('challenges', 'id', ['id' => $this->id]);
        
        if ($exists) {
            // Update
            return self::getDb()->update('challenges', $data, ['id' => $this->id])->rowCount() > 0;
        } else {
            // Insert
            $data['id'] = $this->id;
            $data['created_at'] = $this->createdAt->format('Y-m-d H:i:s');
            return self::getDb()->insert('challenges', $data)->rowCount() > 0;
        }
    }
    
    public function delete(): bool
    {
        return self::getDb()->delete('challenges', ['id' => $this->id])->rowCount() > 0;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'category_id' => $this->categoryId,
            'title' => $this->title,
            'description' => $this->description,
            'difficulty' => $this->difficulty,
            'rules' => $this->rules,
            'submission_guidelines' => $this->submissionGuidelines,
            'start_date' => $this->startDate->format('Y-m-d H:i:s'),
            'end_date' => $this->endDate->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'image' => $this->image,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null
        ];
    }
} 