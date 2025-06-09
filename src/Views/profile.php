<?php
/**
 * User Profile View
 * 
 * @var \Kpzsproductions\Challengify\Models\User $user
 * @var array $submissions
 * @var array $badges
 * @var int $reputationPoints
 * @var string $currentLevel
 * @var int $usernameChangeCount
 * @var string $csrfToken
 * @var string|null $flashMessage
 * @var string $flashType
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Challengify</title>
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <?php require __DIR__ . '/partials/navbar.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <?php if ($flashMessage): ?>
            <div class="mb-4 p-4 rounded-md text-white <?= $flashType === 'success' ? 'bg-green-500' : 'bg-red-500' ?>">
                <?= $flashMessage ?>
            </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:flex">
                <!-- Left sidebar - User info -->
                <div class="md:w-1/3 p-6 bg-gray-50 border-r">
                    <div class="text-center">
                        <div class="relative inline-block mb-4">
                            <?php if ($user->getAvatarFilename()): ?>
                                <img src="<?= $user->getAvatar() ?>" 
                                     alt="Profile Picture" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-blue-500">
                            <?php else: ?>
                                <div class="w-32 h-32 rounded-full bg-blue-500 flex items-center justify-center mx-auto border-4 border-blue-600">
                                    <span class="text-4xl font-bold text-white">
                                        <?= strtoupper(substr($user->getUsername(), 0, 1)) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <h2 class="text-2xl font-bold"><?= htmlspecialchars($user->getUsername()) ?></h2>
                        <p class="text-gray-600"><?= htmlspecialchars($user->getEmail()) ?></p>
                        <p class="mt-2">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                <?= htmlspecialchars($user->getRole()) ?>
                            </span>
                        </p>
                        
                        <!-- Reputation display -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold">Reputation Level</h3>
                            <div class="flex items-center justify-center mt-2">
                                <span class="text-3xl font-bold text-yellow-600"><?= $currentLevel ?></span>
                            </div>
                            <div class="mt-2">
                                <div class="h-2.5 w-full bg-gray-200 rounded-full">
                                    <?php
                                    // Calculate percentage based on progression to next level
                                    $levels = array_keys($reputationLevels);
                                    $currentLevelIndex = 0;
                                    $nextLevelPoints = 0;
                                    
                                    foreach ($levels as $index => $points) {
                                        if ($reputationPoints >= $points) {
                                            $currentLevelIndex = $index;
                                        } else {
                                            $nextLevelPoints = $points;
                                            break;
                                        }
                                    }
                                    
                                    $currentLevelPoints = $levels[$currentLevelIndex];
                                    $percentage = 100;
                                    
                                    if ($nextLevelPoints > 0) {
                                        $percentage = min(100, 
                                            (($reputationPoints - $currentLevelPoints) / 
                                            ($nextLevelPoints - $currentLevelPoints)) * 100
                                        );
                                    }
                                    ?>
                                    <div class="h-2.5 bg-blue-600 rounded-full" style="width: <?= $percentage ?>%"></div>
                                </div>
                                <div class="text-sm text-center mt-1">
                                    <?= $reputationPoints ?> reputation points
                                </div>
                            </div>
                        </div>
                        
                        <!-- Update profile section -->
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold mb-4">Update Profile</h3>
                            
                            <div class="mb-6">
                                <h4 class="text-md font-medium mb-2">Change Avatar</h4>
                                <form action="/profile/update-avatar" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <div class="flex items-center justify-center">
                                        <label class="block">
                                            <span class="sr-only">Choose profile photo</span>
                                            <input type="file" name="avatar" class="block w-full text-sm text-gray-500
                                                file:mr-4 file:py-2 file:px-4 file:rounded-full
                                                file:border-0 file:text-sm file:font-semibold
                                                file:bg-blue-50 file:text-blue-700
                                                hover:file:bg-blue-100">
                                        </label>
                                    </div>
                                    <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 w-full">
                                        Upload New Avatar
                                    </button>
                                </form>
                            </div>
                            
                            <div class="mb-6">
                                <h4 class="text-md font-medium mb-2">Change Username</h4>
                                <p class="text-xs text-gray-500 mb-2">You can change your username <?= 3 - $usernameChangeCount ?> more times</p>
                                <form action="/profile/update-username" method="post">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <input type="text" name="username" placeholder="New username" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           <?= $usernameChangeCount >= 3 ? 'disabled' : '' ?>>
                                    <button type="submit" 
                                            class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 w-full disabled:bg-gray-400"
                                            <?= $usernameChangeCount >= 3 ? 'disabled' : '' ?>>
                                        Update Username
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right content area -->
                <div class="md:w-2/3 p-6">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <ul class="flex flex-wrap -mb-px">
                            <li class="mr-2">
                                <a href="#submissions" class="inline-block p-4 border-b-2 border-blue-600 rounded-t-lg text-blue-600">
                                    <i class="fas fa-file-alt mr-2"></i>My Submissions
                                </a>
                            </li>
                            <li class="mr-2">
                                <a href="#badges" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">
                                    <i class="fas fa-medal mr-2"></i>Badges
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Submissions tab content -->
                    <div id="submissions" class="pt-6">
                        <h3 class="text-xl font-semibold mb-4">Your Submissions</h3>
                        
                        <?php if (empty($submissions)): ?>
                            <div class="text-center py-8 bg-gray-50 rounded-md">
                                <p class="text-gray-500">You haven't made any submissions yet.</p>
                                <a href="/challenges" class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Browse Challenges
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Challenge</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($submissions as $submission): ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <?= htmlspecialchars($submission['title']) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <a href="/challenges/<?= $submission['challenge_id'] ?>" class="text-blue-600 hover:underline">
                                                        <?= htmlspecialchars($submission['challenge_name']) ?>
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <?php
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    switch ($submission['status']) {
                                                        case 'approved':
                                                            $statusClass = 'bg-green-100 text-green-800';
                                                            break;
                                                        case 'rejected': 
                                                            $statusClass = 'bg-red-100 text-red-800';
                                                            break;
                                                        case 'pending':
                                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $statusClass ?>">
                                                        <?= ucfirst(htmlspecialchars($submission['status'])) ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?= (new DateTime($submission['created_at']))->format('M j, Y') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Badges tab content (hidden by default) -->
                    <div id="badges" class="pt-6 hidden">
                        <h3 class="text-xl font-semibold mb-4">Your Badges</h3>
                        
                        <?php if (empty($badges)): ?>
                            <div class="text-center py-8 bg-gray-50 rounded-md">
                                <p class="text-gray-500">You haven't earned any badges yet.</p>
                                <p class="text-sm text-gray-500 mt-2">Complete challenges to earn badges!</p>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php foreach ($badges as $badge): ?>
                                    <div class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                        <div class="p-4 flex items-center">
                                            <div class="flex-shrink-0 mr-4">
                                                <?php if ($badge['image']): ?>
                                                    <img src="<?= $badge['image'] ?>" alt="<?= htmlspecialchars($badge['name']) ?>" class="w-12 h-12">
                                                <?php else: ?>
                                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-500">
                                                        <i class="fas fa-medal text-lg"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold"><?= htmlspecialchars($badge['name']) ?></h4>
                                                <p class="text-sm text-gray-600"><?= htmlspecialchars($badge['description']) ?></p>
                                                <p class="text-xs text-gray-500 mt-1">Earned on <?= (new DateTime($badge['awarded_at']))->format('M j, Y') ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Simple tab switching logic
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('ul.flex li a');
            const content = document.querySelectorAll('.md\\:w-2\\/3 > div[id]');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all tabs
                    tabs.forEach(t => {
                        t.classList.remove('border-blue-600', 'text-blue-600');
                        t.classList.add('border-transparent');
                    });
                    
                    // Add active class to current tab
                    this.classList.remove('border-transparent');
                    this.classList.add('border-blue-600', 'text-blue-600');
                    
                    // Hide all content
                    content.forEach(c => {
                        c.classList.add('hidden');
                    });
                    
                    // Show current content
                    const target = document.querySelector(this.getAttribute('href'));
                    target.classList.remove('hidden');
                });
            });
        });
    </script>
    
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html> 