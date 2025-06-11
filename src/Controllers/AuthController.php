<?php

namespace Kpzsproductions\Challengify\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\RedirectResponse;
use Kpzsproductions\Challengify\Models\User;
use Kpzsproductions\Challengify\Services\SecurityService;
use Kpzsproductions\Challengify\Services\RateLimiterService;
use Kpzsproductions\Challengify\Services\JwtService;
use Medoo\Medoo;
use Ramsey\Uuid\Uuid;

class AuthController
{
    private User $user;
    private SecurityService $securityService;
    private RateLimiterService $rateLimiterService;
    private JwtService $jwtService;
    private Medoo $db;
    
    public function __construct(
        User $user, 
        SecurityService $securityService, 
        RateLimiterService $rateLimiterService,
        JwtService $jwtService,
        Medoo $db
    ) {
        $this->user = $user;
        $this->securityService = $securityService;
        $this->rateLimiterService = $rateLimiterService;
        $this->jwtService = $jwtService;
        $this->db = $db;
    }
    
    /**
     * Load the login view
     */
    public function loadLogin(ServerRequestInterface $request): ResponseInterface
    {
        // Start session if not already started
        $this->startSession();
        
        // Extract user data from request attributes
        $user = $request->getAttribute('user', $this->user);
        
        ob_start();
        require __DIR__ . '/../Views/login.php';
        $content = ob_get_clean();
        
        $response = new Response;
        $response->getBody()->write($content);
        
        // Set security headers
        $response = $response->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('X-XSS-Protection', '1; mode=block')
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('Content-Security-Policy', 
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; " .
                "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com fonts.googleapis.com; " .
                "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com; " .
                "img-src 'self' data:;"
            );
            
        return $response;
    }

    /**
     * Load the register view
     */
    public function loadRegister(ServerRequestInterface $request): ResponseInterface 
    {
        // Start session if not already started
        $this->startSession();
        
        // Extract user data from request attributes
        $user = $request->getAttribute('user', $this->user);
        
        ob_start();
        require __DIR__ . '/../Views/register.php';
        $content = ob_get_clean();
        
        $response = new Response;
        $response->getBody()->write($content);
        
        // Set security headers
        $response = $response->withHeader('X-Frame-Options', 'DENY')
            ->withHeader('X-XSS-Protection', '1; mode=block')
            ->withHeader('X-Content-Type-Options', 'nosniff')
            ->withHeader('Content-Security-Policy', 
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; " .
                "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com fonts.googleapis.com; " .
                "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com; " .
                "img-src 'self' data:;"
            );
            
        return $response;
    }
    
    /**
     * Process login form submission
     */
    public function processLogin(ServerRequestInterface $request): ResponseInterface
    {
        // Start session if not already started
        $this->startSession();
        
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? '0.0.0.0';
        
        // Apply rate limiting for login attempts
        if ($this->rateLimiterService->tooManyAttempts('login', $ip)) {
            return new RedirectResponse('/login?error=too_many_attempts');
        }
        
        // Get and sanitize input data
        $data = $this->securityService->sanitizeInput($request->getParsedBody());
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $rememberMe = isset($data['remember_me']) ? true : false;
        
        // Validate input
        if (empty($email) || empty($password)) {
            $this->rateLimiterService->tooManyAttempts('login', $ip);
            return new RedirectResponse('/login?error=missing_fields');
        }
        
        // Find user by email
        $user = $this->db->get('users', '*', ['email' => $email]);
        
        // Check if user exists and password is correct
        if (!$user || !$this->securityService->verifyPassword($password, $user['password'])) {
            $this->rateLimiterService->tooManyAttempts('login', $ip);
            return new RedirectResponse('/login?error=invalid_credentials');
        }
        
        // Reset login attempts on successful login
        $this->rateLimiterService->resetAttempts('login', $ip);
        
        // Set up the User object with the logged-in user data
        $userObj = new User(
            $user['id'],
            $user['username'],
            $user['email'],
            '',  // Don't store the password in the session
            $user['role'],
            $user['avatar'] ?? null,
            $user['bio'] ?? null,
            new \DateTime($user['created_at']),
            new \DateTime($user['updated_at'] ?? $user['created_at'])
        );
        $userObj->setLoggedIn(true);
        // Create session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_logged_in'] = true;
        $_SESSION['last_activity'] = time();
        
        // Generate JWT token for API access if needed
        if ($rememberMe) {
            $token = $this->jwtService->generate([
                'user_id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role']
            ]);
            
            // Set a secure cookie with the token (30 days expiration)
            setcookie(
                'remember_token',
                $token,
                [
                    'expires' => time() + (86400 * 30),
                    'path' => '/',
                    'domain' => '',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]
            );
        }
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Redirect to dashboard
        return new RedirectResponse('/');
    }
    
    /**
     * Process registration form submission
     */
    public function processRegister(ServerRequestInterface $request): ResponseInterface
    {
        // Start session if not already started
        $this->startSession();
        
        // Regenerate session ID for security on registration attempts
        session_regenerate_id(true);
        
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? '0.0.0.0';
        
        // Apply rate limiting for registration
        if ($this->rateLimiterService->tooManyAttempts('register', $ip)) {
            return new RedirectResponse('/register?error=too_many_attempts');
        }
        
        // Get and sanitize input data
        $data = $this->securityService->sanitizeInput($request->getParsedBody());
        $username = $data['username'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $passwordConfirm = $data['password_confirm'] ?? '';
        
        // Validate input
        if (empty($username) || empty($email) || empty($password) || empty($passwordConfirm)) {
            $this->rateLimiterService->tooManyAttempts('register', $ip);
            return new RedirectResponse('/register?error=missing_fields');
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->rateLimiterService->tooManyAttempts('register', $ip);
            return new RedirectResponse('/register?error=invalid_email');
        }
        
        // Validate password strength
        if (!$this->validatePasswordStrength($password)) {
            $this->rateLimiterService->tooManyAttempts('register', $ip);
            return new RedirectResponse('/register?error=weak_password');
        }
        
        // Check if passwords match
        if ($password !== $passwordConfirm) {
            $this->rateLimiterService->tooManyAttempts('register', $ip);
            return new RedirectResponse('/register?error=passwords_mismatch');
        }
        
        // Check if email already exists
        $existingUser = $this->db->get('users', '*', ['email' => $email]);
        if ($existingUser) {
            $this->rateLimiterService->tooManyAttempts('register', $ip);
            return new RedirectResponse('/register?error=email_exists');
        }
        
        // Check if username already exists
        $existingUsername = $this->db->get('users', '*', ['username' => $username]);
        if ($existingUsername) {
            $this->rateLimiterService->tooManyAttempts('register', $ip);
            return new RedirectResponse('/register?error=username_exists');
        }
        
        // Hash password
        $hashedPassword = $this->securityService->hashPassword($password);
        
        // Generate a UUID for the new user
        $userId = Uuid::uuid4()->toString();
        
        // Insert new user
        $result = $this->db->insert('users', [
            'id' => $userId,
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => 'user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        if (!$result) {
            $this->rateLimiterService->tooManyAttempts('register', $ip);
            return new RedirectResponse('/register?error=database_error');
        }
        
        // Reset registration attempts on successful registration
        $this->rateLimiterService->resetAttempts('register', $ip);
        
        // Redirect to login page
        return new RedirectResponse('/login?registered=true');
    }
    
    /**
     * Log out the current user
     */
    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        // Start session if not already started
        $this->startSession();
        
        // Clear session data
        $_SESSION = [];
        
        // Clear the remember me cookie if exists
        if (isset($_COOKIE['remember_token'])) {
            setcookie(
                'remember_token',
                '',
                [
                    'expires' => time() - 3600,
                    'path' => '/',
                    'domain' => '',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]
            );
        }
        
        // Clear the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                [
                    'expires' => time() - 3600,
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly'],
                    'samesite' => 'Strict'
                ]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        return new RedirectResponse('/login');
    }
    
    /**
     * Start the session if not already started
     */
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.use_strict_mode', 1);
            ini_set('session.use_cookies', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 1);
            ini_set('session.cookie_samesite', 'Strict');
            
            // Configure secure session cookie parameters
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            $httpOnly = true;
            $sameSite = 'Strict';
            $path = '/';
            
            // Set secure cookie parameters
            session_set_cookie_params([
                'lifetime' => 7200, // 2 hours
                'path' => $path, 
                'domain' => '', // current domain
                'secure' => $secure,
                'httponly' => $httpOnly,
                'samesite' => $sameSite
            ]);
            
            session_start();
        }
    }
    
    /**
     * Validate password strength
     */
    private function validatePasswordStrength(string $password): bool
    {
        // Password must be at least 8 characters
        if (strlen($password) < 8) {
            return false;
        }
        
        // Password must contain at least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        
        // Password must contain at least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }
        
        // Password must contain at least one number
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        // Password must contain at least one special character
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
}

?>