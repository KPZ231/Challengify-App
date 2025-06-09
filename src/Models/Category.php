<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Models;

use Medoo\Medoo;
use Kpzsproductions\Challengify\Services\Database;

class Category
{
    private string $id;
    private string $name;
    private ?string $description;
    private string $slug;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;
    
    private static ?Medoo $db = null;
    
    public function __construct(
        string $id,
        string $name,
        string $slug,
        ?string $description = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->slug = $slug;
        $this->createdAt = $createdAt ?? new \DateTime();
        $this->updatedAt = $updatedAt;
    }
    
    // Getters and setters
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function setName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new \DateTime();
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function setDescription(?string $description): void
    {
        $this->description = $description;
        $this->updatedAt = new \DateTime();
    }
    
    public function getSlug(): string
    {
        return $this->slug;
    }
    
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
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
        $data = self::getDb()->get('categories', '*', ['id' => $id]);
        
        if (!$data) {
            return null;
        }
        
        return self::createFromArray($data);
    }
    
    public static function all(): array
    {
        $data = self::getDb()->select('categories', '*');
        
        return array_map(function ($item) {
            return self::createFromArray($item);
        }, $data);
    }
    
      
    public static function createFromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            new \DateTime($data['created_at']),
            $data['updated_at'] ? new \DateTime($data['updated_at']) : null
        );
    }
    
    public function save(): bool
    {
        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ];
        
        // Check if record exists
        $exists = self::getDb()->get('categories', 'id', ['id' => $this->id]);
        
        if ($exists) {
            // Update
            return self::getDb()->update('categories', $data, ['id' => $this->id])->rowCount() > 0;
        } else {
            // Insert
            $data['id'] = $this->id;
            $data['created_at'] = $this->createdAt->format('Y-m-d H:i:s');
            return self::getDb()->insert('categories', $data)->rowCount() > 0;
        }
    }
    
    public function delete(): bool
    {
        return self::getDb()->delete('categories', ['id' => $this->id])->rowCount() > 0;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d H:i:s') : null
        ];
    }
} 