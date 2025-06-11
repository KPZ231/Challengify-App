<?php
/**
 * Community View - User listing
 * 
 * @var \Kpzsproductions\Challengify\Models\User $currentUser
 * @var array $users
 * @var int $page
 * @var int $totalPages
 * @var int $totalUsers
 * @var string $search
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community - Challengify</title>
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php 
    // Make current user available for navbar
    $user = $currentUser; 
    require __DIR__ . '/partials/navbar.php'; 
    ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Community</h1>
            <p class="mt-2 text-gray-600">Connect with other users and discover new challenges together.</p>
        </div>
        
        <!-- Search form -->
        <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
            <form method="GET" action="/community" class="flex flex-col md:flex-row md:items-center gap-3">
                <div class="flex-grow">
                    <label for="search" class="sr-only">Search users</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="search" 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Search by username or bio"
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Search
                </button>
                <?php if (!empty($search)): ?>
                    <a href="/community" 
                       class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Results summary -->
        <div class="mb-4 text-sm text-gray-500">
            <?php if (!empty($search)): ?>
                <p>Showing results for "<?= htmlspecialchars($search) ?>" - <?= number_format($totalUsers) ?> users found</p>
            <?php else: ?>
                <p>Showing <?= number_format($totalUsers) ?> users</p>
            <?php endif; ?>
        </div>
        
        <!-- Users grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
            <?php foreach ($users as $user): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img class="h-12 w-12 rounded-full object-cover" 
                                         src="/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>" 
                                         alt="<?= htmlspecialchars($user['username']) ?>'s avatar">
                                <?php else: ?>
                                    <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl font-bold">
                                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="ml-4">
                                <a href="/user/<?= urlencode($user['username']) ?>" class="text-lg font-medium text-gray-900 hover:text-blue-600">
                                    <?= htmlspecialchars($user['username']) ?>
                                </a>
                                <div class="flex items-center mt-1">
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-award text-yellow-500 mr-1"></i>
                                        <?= number_format($user['reputation'] ?? 0) ?> rep
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3 text-sm text-gray-600 line-clamp-3">
                            <?php if (!empty($user['bio'])): ?>
                                <?= nl2br(htmlspecialchars(substr($user['bio'], 0, 100))) ?><?= strlen($user['bio']) > 100 ? '...' : '' ?>
                            <?php else: ?>
                                <span class="text-gray-400 italic">No bio provided</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-3 flex justify-end">
                            <a href="/user/<?= urlencode($user['username']) ?>" 
                               class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none">
                                <i class="fas fa-user mr-1"></i> View Profile
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($users)): ?>
                <div class="col-span-full bg-white rounded-lg shadow-sm p-8 text-center">
                    <div class="text-gray-500">
                        <i class="fas fa-users fa-3x mb-4"></i>
                        <p class="text-xl font-medium">No users found</p>
                        <?php if (!empty($search)): ?>
                            <p class="mt-2">Try different search terms or <a href="/community" class="text-blue-600 hover:underline">clear your search</a>.</p>
                        <?php else: ?>
                            <p class="mt-2">There are no users in the community yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="flex items-center justify-center space-x-1 mt-8">
                <?php if ($page > 1): ?>
                    <a href="/community?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Previous
                    </a>
                <?php else: ?>
                    <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
                        Previous
                    </span>
                <?php endif; ?>
                
                <?php
                $startPage = max(1, min($page - 2, $totalPages - 4));
                $endPage = min($totalPages, max(5, $page + 2));
                ?>
                
                <?php if ($startPage > 1): ?>
                    <a href="/community?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        1
                    </a>
                    <?php if ($startPage > 2): ?>
                        <span class="px-2 py-2 text-gray-500">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-md">
                            <?= $i ?>
                        </span>
                    <?php else: ?>
                        <a href="/community?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <span class="px-2 py-2 text-gray-500">...</span>
                    <?php endif; ?>
                    <a href="/community?page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        <?= $totalPages ?>
                    </a>
                <?php endif; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="/community?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Next
                    </a>
                <?php else: ?>
                    <span class="px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
                        Next
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html> 