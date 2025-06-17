<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign in to your Challangeo account to access your challenges, submissions, and progress.">
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="Challangeo">
    
    <!-- Canonical link -->
    <link rel="canonical" href="https://challangeo.io/login">
    
    <!-- Favicon -->
    <link rel="icon" href="/images/challangify-logo.png" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/challangify-logo/apple-touch-icon.png">
    
    <title>Login | Challangeo</title>
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="/js/validate.js" defer></script>
    <script src="/js/dropdown.js" defer></script>
</head>
<body>
    <?php include __DIR__ . '/partials/navbar.php'; ?>

    <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="/register" class="font-medium text-primary hover:text-primary-dark">
                    create a new account
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
                            'invalid_credentials' => 'Invalid email or password',
                            default => 'An error occurred'
                        };
                        echo htmlspecialchars($message);
                        ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['registered']) && $_GET['registered'] === 'true'): ?>
                    <div class="mb-4 text-sm text-green-600 bg-green-100 p-3 rounded">
                        Account created successfully! Please login.
                    </div>
                <?php endif; ?>

                <form id="login-form" class="space-y-6" action="/login" method="POST">
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
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-eye-slash text-gray-400 hover:text-gray-600"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember_me" type="checkbox"
                                class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                                Remember me
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-medium text-primary hover:text-primary-dark">
                                Forgot your password?
                            </a>
                        </div>
                    </div>

                    <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Sign In
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
