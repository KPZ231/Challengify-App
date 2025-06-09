<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Services;

use Psr\Http\Message\UploadedFileInterface;
use Ramsey\Uuid\Uuid;
use RuntimeException;

class FileUploadService
{
    private string $uploadDir;
    private array $allowedTypes;
    private int $maxFileSize;

    public function __construct(string $uploadDir, array $allowedTypes = [], int $maxFileSize = 10485760)
    {
        $this->uploadDir = rtrim($uploadDir, '/');
        $this->allowedTypes = $allowedTypes ?: [
            'image/jpeg',
            'image/png',
            'image/gif',
            'video/mp4',
            'video/quicktime',
            'video/x-msvideo'
        ];
        $this->maxFileSize = $maxFileSize; // Default: 10MB
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function upload(UploadedFileInterface $file, array $options = []): array
    {
        // Override defaults with provided options
        $directory = $options['directory'] ?? $this->uploadDir;
        $allowedTypes = $options['allowedTypes'] ?? $this->allowedTypes;
        $maxFileSize = $options['maxFileSize'] ?? $this->maxFileSize;
        
        // Create directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $this->validateFile($file, $maxFileSize, $allowedTypes);
        
        // Generate unique filename
        $extension = $this->getExtension($file->getClientFilename() ?? '');
        $filename = Uuid::uuid4()->toString() . '.' . $extension;
        $path = $directory . '/' . $filename;
        
        // Move uploaded file
        $file->moveTo($path);
        
        return [
            'filename' => $filename,
            'path' => $path,
            'type' => $file->getClientMediaType(),
            'size' => $file->getSize()
        ];
    }

    private function validateFile(UploadedFileInterface $file, int $maxFileSize = null, array $allowedTypes = null): void
    {
        $maxFileSize = $maxFileSize ?? $this->maxFileSize;
        $allowedTypes = $allowedTypes ?? $this->allowedTypes;
        
        // Check file size
        if ($file->getSize() > $maxFileSize) {
            throw new RuntimeException('File too large. Maximum size is ' . $this->formatBytes($maxFileSize));
        }
        
        // Check file type
        $mediaType = $file->getClientMediaType();
        if (!in_array($mediaType, $allowedTypes)) {
            throw new RuntimeException('Invalid file type. Allowed types: ' . implode(', ', $allowedTypes));
        }
        
        // Check for upload errors
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed with error code ' . $file->getError());
        }
    }

    private function getExtension(string $filename): string
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
} 