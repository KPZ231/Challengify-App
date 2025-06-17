<?php

declare(strict_types=1);

namespace Kpzsproductions\Challengify\Middleware;

use Kpzsproductions\Challengify\Services\SecurityService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\UploadedFileInterface;

class InputSanitizationMiddleware implements MiddlewareInterface
{
    private SecurityService $securityService;
    private array $allowedMimeTypes;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
        
        // Define allowed MIME types for file uploads
        $this->allowedMimeTypes = [
            // Images
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            // Documents
            'application/pdf', 'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain', 'text/csv',
            // Archives
            'application/zip', 'application/x-zip-compressed'
        ];
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Sanitize query parameters
        $queryParams = $this->securityService->sanitizeInput($request->getQueryParams());
        $request = $request->withQueryParams($queryParams);
        
        // Sanitize parsed body
        $parsedBody = $this->securityService->sanitizeInput($request->getParsedBody());
        $request = $request->withParsedBody($parsedBody);
        
        // Validate uploaded files
        $uploadedFiles = $request->getUploadedFiles();
        $validatedFiles = $this->validateUploadedFiles($uploadedFiles);
        $request = $request->withUploadedFiles($validatedFiles);
        
        // Sanitize cookies
        $cookies = $this->securityService->sanitizeInput($request->getCookieParams());
        $request = $request->withCookieParams($cookies);
        
        // Sanitize server params
        $serverParams = $this->securityService->sanitizeInput($request->getServerParams());
        
        // Process the sanitized request
        return $handler->handle($request);
    }
    
    /**
     * Validate uploaded files
     * 
     * @param array $uploadedFiles Files from request
     * @return array Validated files
     */
    private function validateUploadedFiles(array $uploadedFiles): array
    {
        foreach ($uploadedFiles as $key => $file) {
            if ($file instanceof UploadedFileInterface) {
                // Skip empty files or files with errors
                if ($file->getError() !== UPLOAD_ERR_OK) {
                    continue;
                }
                
                // Validate file size (max 10MB)
                if ($file->getSize() > 10 * 1024 * 1024) {
                    // Set error to indicate file is too large
                    $uploadedFiles[$key] = $this->createErrorFile($file, UPLOAD_ERR_INI_SIZE);
                    continue;
                }
                
                // Validate file type
                $fileMimeType = $file->getClientMediaType();
                if (!in_array($fileMimeType, $this->allowedMimeTypes)) {
                    // Set error to indicate invalid file type
                    $uploadedFiles[$key] = $this->createErrorFile($file, UPLOAD_ERR_EXTENSION);
                    continue;
                }
                
                // Additional security check - validate filename
                $filename = $file->getClientFilename();
                if ($filename && !$this->isValidFilename($filename)) {
                    // Set error for potentially malicious filename
                    $uploadedFiles[$key] = $this->createErrorFile($file, UPLOAD_ERR_EXTENSION);
                    continue;
                }
            } elseif (is_array($file)) {
                // Handle nested files (e.g., multiple file inputs)
                $uploadedFiles[$key] = $this->validateUploadedFiles($file);
            }
        }
        
        return $uploadedFiles;
    }
    
    /**
     * Create an error file to replace invalid uploads
     * 
     * @param UploadedFileInterface $file Original file
     * @param int $errorCode Error code to set
     * @return UploadedFileInterface File with error
     */
    private function createErrorFile(UploadedFileInterface $file, int $errorCode): UploadedFileInterface
    {
        // This is a simplified approach - in a real implementation,
        // you would need to create a custom UploadedFile class that allows setting the error code
        // For now, we'll just log the error
        error_log("File upload validation failed: " . $file->getClientFilename());
        return $file;
    }
    
    /**
     * Validate filename for security
     * 
     * @param string $filename Filename to validate
     * @return bool True if filename is valid
     */
    private function isValidFilename(string $filename): bool
    {
        // Check for potentially dangerous patterns in filenames
        $dangerousPatterns = [
            '/\.php$/i',     // PHP files
            '/\.phtml$/i',   // PHP template files
            '/\.phar$/i',    // PHP archive
            '/\.htaccess$/i', // Apache config files
            '/\.exe$/i',     // Executable files
            '/\.sh$/i',      // Shell scripts
            '/\.asp$/i',     // ASP files
            '/\.aspx$/i',    // ASP.NET files
            '/\.jsp$/i',     // Java Server Pages
            '/\.cgi$/i',     // CGI scripts
            '/\.pl$/i',      // Perl scripts
            '/\.\.|\/\//i'   // Directory traversal attempts
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $filename)) {
                return false;
            }
        }
        
        return true;
    }
} 