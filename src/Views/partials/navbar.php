<nav class="ch-navbar bg-white shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-3">
            <!-- Left side -->
            <div class="flex items-center space-x-8">
                <a href="/" class="ch-navbar-brand text-xl font-bold ch-text-primary">
                    <i class="fas fa-code mr-2"></i>Challengify
                </a>
                
                <div class="hidden md:flex space-x-6">
                    <a href="/categories" class="ch-nav-link hover:text-blue-700">
                        <i class="fas fa-folder mr-1"></i>Categories
                    </a>
                    <a href="/current-challenge" class="ch-nav-link hover:text-blue-700">
                        <i class="fas fa-trophy mr-1"></i>Current Challenge
                    </a>
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
                                <span class="font-medium"><?= htmlspecialchars($user->getUsername()) ?></span>
                                <i class="fas fa-chevron-down chevron ml-1"></i>
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
                    <a href="/login" class="ch-btn ch-btn-outline-primary">
                        <i class="fas fa-sign-in-alt mr-1"></i>Login
                    </a>
                    <a href="/register" class="ch-btn ch-btn-primary">
                        <i class="fas fa-user-plus mr-1"></i>Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
