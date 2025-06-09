<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Models;

use Medoo\Medoo;
use Kpzsproductions\Challengify\Services\Database;

class Contact
{
    private string $id;
    private string $name;
    private string $email;
    private string $subject;
    private string $message;
    private string $ip;
    private bool $isRead;
    private \DateTime $createdAt;

    public function __construct(
        string $id,
        string $name,
        string $email,
        string $subject,
        string $message,
        string $ip,
        bool $isRead = false,
        ?\DateTime $createdAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->message = $message;
        $this->ip = $ip;
        $this->isRead = $isRead;
        $this->createdAt = $createdAt ?? new \DateTime();
    }

    /**
     * Create a new contact message in the database
     */
    public static function create(
        string $name,
        string $email,
        string $subject,
        string $message,
        string $ip
    ): self {
        $db = Database::getInstance();
        
        $id = bin2hex(random_bytes(16));
        $now = date('Y-m-d H:i:s');
        
        $db->insert('contacts', [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'ip' => $ip,
            'is_read' => 0,
            'created_at' => $now
        ]);
        
        return new self(
            $id,
            $name,
            $email,
            $subject,
            $message,
            $ip,
            false,
            new \DateTime($now)
        );
    }

    /**
     * Find a contact message by ID
     */
    public static function find(string $id): ?self
    {
        $db = Database::getInstance();
        $contact = $db->get('contacts', '*', ['id' => $id]);

        if (!$contact) {
            return null;
        }

        return new self(
            $contact['id'],
            $contact['name'],
            $contact['email'],
            $contact['subject'],
            $contact['message'],
            $contact['ip'],
            (bool)$contact['is_read'],
            new \DateTime($contact['created_at'])
        );
    }

    /**
     * Get all contact messages
     */
    public static function all(int $limit = 50, int $offset = 0): array
    {
        $db = Database::getInstance();
        $contacts = $db->select('contacts', '*', [
            'ORDER' => ['created_at' => 'DESC'],
            'LIMIT' => [$offset, $limit]
        ]);

        $result = [];
        foreach ($contacts as $contact) {
            $result[] = new self(
                $contact['id'],
                $contact['name'],
                $contact['email'],
                $contact['subject'],
                $contact['message'],
                $contact['ip'],
                (bool)$contact['is_read'],
                new \DateTime($contact['created_at'])
            );
        }

        return $result;
    }

    /**
     * Mark the message as read
     */
    public function markAsRead(): void
    {
        if (!$this->isRead) {
            $db = Database::getInstance();
            $db->update('contacts', [
                'is_read' => 1
            ], [
                'id' => $this->id
            ]);
            $this->isRead = true;
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'ip' => $this->ip,
            'is_read' => $this->isRead,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }
} 