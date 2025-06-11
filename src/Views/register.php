<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Challengify</title>
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="/js/validate.js" defer></script>
    <script src="/js/dropdown.js" defer></script>

    <link rel="shortcut icon" href="/images/challengify-logo.png" type="image/x-icon">
</head>
<body>
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="/login" class="font-medium text-primary hover:text-primary-dark">
                    sign in to your existing account
                </a>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <!-- Error message container -->
                <div id="form-error" class="mb-4 text-sm text-red-600 bg-red-100 p-3 rounded hidden"></div>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="mb-4 text-sm text-red-600 bg-red-100 p-3 rounded">
                        <?php 
                        $error = $_GET['error'];
                        $message = match($error) {
                            'missing_fields' => 'Please fill in all fields',
                            'invalid_email' => 'Please enter a valid email address',
                            'passwords_mismatch' => 'Passwords do not match',
                            'email_exists' => 'Email is already registered',
                            'username_exists' => 'Username is already taken',
                            default => 'An error occurred'
                        };
                        echo htmlspecialchars($message);
                        ?>
                    </div>
                <?php endif; ?>

                <form id="register-form" class="space-y-6" action="/register" method="POST">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">
                            Username
                        </label>
                        <div class="mt-1">
                            <input id="username" name="username" type="text" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email address
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <div class="mt-1 relative">
                            <input id="password" name="password" type="password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-eye-slash text-gray-400 hover:text-gray-600"></i>
                            </button>
                        </div>
                        
                        <!-- Password strength indicator -->
                        <div class="mt-2">
                            <div class="flex justify-between text-xs mb-1">
                                <span>Password strength:</span>
                                <span id="password-strength-text">None</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div id="password-strength-meter" class="h-2.5 rounded-full" style="width: 0%;"></div>
                            </div>
                        </div>
                        
                        <!-- Password requirements -->
                        <div class="mt-2 text-sm">
                            <p class="text-gray-700 font-medium mb-1">Password must have:</p>
                            <ul class="space-y-1 text-xs">
                                <li id="req-length" class="text-red-500"><i class="fas fa-times mr-1"></i> At least 8 characters</li>
                                <li id="req-lowercase" class="text-red-500"><i class="fas fa-times mr-1"></i> Lowercase letters (a-z)</li>
                                <li id="req-uppercase" class="text-red-500"><i class="fas fa-times mr-1"></i> Uppercase letters (A-Z)</li>
                                <li id="req-number" class="text-red-500"><i class="fas fa-times mr-1"></i> Numbers (0-9)</li>
                                <li id="req-special" class="text-red-500"><i class="fas fa-times mr-1"></i> Special characters (!@#$...)</li>
                            </ul>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700">
                            Confirm Password
                        </label>
                        <div class="mt-1 relative">
                            <input id="password_confirm" name="password_confirm" type="password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-eye-slash text-gray-400 hover:text-gray-600"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Register
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>