<?php
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
$isAdmin = $isLoggedIn && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
?>

<nav class="ch-navbar bg-white shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-3">
            <!-- Left side -->
            <div class="flex items-center space-x-4 md:space-x-8">
                <a href="/" class="ch-navbar-brand text-xl font-bold ch-text-primary">
                    <i class="fas fa-code mr-2"></i>Challengify
                </a>
                
                <!-- Mobile menu button -->
                <button type="button" class="mobile-menu-button md:hidden flex items-center text-gray-700 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <div class="hidden md:flex space-x-6">
                    <a href="/about" class="ch-nav-link hover:text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>About
                    </a>
                    <a href="/contact" class="ch-nav-link hover:text-blue-700">
                        <i class="fas fa-envelope mr-1"></i>Contact
                    </a>
                    <a href="/challenges" class="ch-nav-link hover:text-blue-700">
                        <i class="fas fa-trophy mr-1"></i>Challenge
                    </a>
                    <a href="/community" class="ch-nav-link hover:text-blue-700">
                        <i class="fas fa-users mr-1"></i>Community
                    </a>
                    <?php if ($isAdmin): ?>
                    <a href="/admin" class="ch-nav-link hover:text-blue-700">
                        <i class="fas fa-cog mr-1"></i>Admin Panel
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-4">
                <?php if (isset($user) && $user->isLoggedIn()): ?>
                    <div class="dropdown-container">
                        <button type="button" class="dropdown-btn">
                            <div class="flex items-center">
                                <?php
                                $avatarUrl = $user->getAvatar();
                                ?>
                                <img src="<?= $avatarUrl ?>" 
                                     alt="User avatar"
                                     class="w-8 h-8 rounded-full object-cover border border-gray-200 mr-2">
                                <span class="font-medium hidden sm:inline"><?= htmlspecialchars($user->getUsername()) ?></span>
                                <i class="fas fa-chevron-down chevron ml-1 hidden sm:inline"></i>
                            </div>
                        </button>
                        
                        <!-- Dropdown menu -->
                        <div class="dropdown-menu">
                            <a href="/profile" class="dropdown-item">
                                <i class="fas fa-user mr-2"></i>Profile
                            </a>
                            <a href="/settings" class="dropdown-item">
                                <i class="fas fa-cog mr-2"></i>Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="/logout" class="dropdown-item dropdown-item-danger">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/login" class="ch-btn ch-btn-outline-primary hidden sm:inline-block">
                        <i class="fas fa-sign-in-alt mr-1"></i>Login
                    </a>
                    <a href="/register" class="ch-btn ch-btn-primary">
                        <i class="fas fa-user-plus mr-1"></i><span class="hidden sm:inline">Register</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Mobile menu - hidden by default -->
    <div class="mobile-menu hidden md:hidden px-4 pb-4 pt-2 bg-white">
        <a href="/about" class="block py-2 px-4 text-gray-800 hover:bg-blue-50 rounded-md">
            <i class="fas fa-info-circle mr-1"></i>About
        </a>
        <a href="/contact" class="block py-2 px-4 text-gray-800 hover:bg-blue-50 rounded-md">
            <i class="fas fa-envelope mr-1"></i>Contact
        </a>
        <a href="/challenges" class="block py-2 px-4 text-gray-800 hover:bg-blue-50 rounded-md">
            <i class="fas fa-trophy mr-1"></i>Challenge
        </a>
        <a href="/community" class="block py-2 px-4 text-gray-800 hover:bg-blue-50 rounded-md">
            <i class="fas fa-users mr-1"></i>Community
        </a>
        <?php if ($isAdmin): ?>
        <a href="/admin" class="block py-2 px-4 text-gray-800 hover:bg-blue-50 rounded-md">
            <i class="fas fa-cog mr-1"></i>Admin Panel
        </a>
        <?php endif; ?>
        
        <?php if (!isset($user) || !$user->isLoggedIn()): ?>
        <div class="mt-3 flex space-x-2">
            <a href="/login" class="ch-btn ch-btn-outline-primary block sm:hidden flex-1 text-center">
                <i class="fas fa-sign-in-alt mr-1"></i>Login
            </a>
        </div>
        <?php endif; ?>
    </div>
</nav>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.querySelector('.mobile-menu-button');
        const mobileMenu = document.querySelector('.mobile-menu');
        
        if(mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
</script>
