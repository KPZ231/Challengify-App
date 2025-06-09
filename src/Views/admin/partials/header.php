<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Challengify</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.10.3/dist/cdn.min.js"></script>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= csrf_token() ?>">
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Admin Navbar -->
    <nav class="bg-gray-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="/admin" class="text-xl font-bold">
                            Challengify <span class="text-blue-400">Admin</span>
                        </a>
                    </div>
                    <div class="ml-10 flex items-center space-x-4">
                        <a href="/admin" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Dashboard</a>
                        <a href="/admin/challenges" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Challenges</a>
                        <a href="/admin/users" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Users</a>
                        <a href="/admin/logs" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Logs</a>
                        <a href="/admin/console" class="px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-700">Console</a>
                    </div>
                </div>
                <div class="flex items-center">
                    <div x-data="{ open: false }" class="ml-3 relative">
                        <div>
                            <button @click="open = !open" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white" id="user-menu" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                    <span class="text-xs font-bold text-white">
                                        <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                                    </span>
                                </div>
                            </button>
                        </div>
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" 
                             role="menu" 
                             aria-orientation="vertical" 
                             aria-labelledby="user-menu">
                            <a href="/" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Return to Site</a>
                            <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                            <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Sign out</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Nav -->
    <div class="md:hidden bg-gray-800 text-white p-3">
        <div class="flex justify-between">
            <div x-data="{ open: false }">
                <button @click="open = !open" class="text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5">
                    <a href="/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                    <a href="/admin/challenges" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Challenges</a>
                    <a href="/admin/users" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Users</a>
                    <a href="/admin/logs" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logs</a>
                    <a href="/admin/console" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Console</a>
                </div>
            </div>
            <div>
                <a href="/admin" class="text-lg font-semibold">Challengify Admin</a>
            </div>
        </div>
    </div>
</body>
</html> 