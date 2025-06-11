<?php
/**
 * Public User Profile View
 * 
 * @var \Kpzsproductions\Challengify\Models\User $profileUser
 * @var \Kpzsproductions\Challengify\Models\User $currentUser
 * @var array $submissions
 * @var array $badges
 * @var int $reputationPoints
 * @var string $currentLevel
 * @var bool $isFollowing
 * @var int $postsCount
 * @var int $commentsCount
 * @var int $followersCount
 * @var int $followingCount
 * @var array $recentActivity
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($profileUser->getUsername()) ?> - Profile - Challengify</title>
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
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="mb-4 p-4 rounded-md text-white <?= $_SESSION['flash_type'] === 'success' ? 'bg-green-500' : 'bg-red-500' ?>">
                <?= $_SESSION['flash_message'] ?>
            </div>
            <?php 
            // Clear flash messages
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
            ?>
        <?php endif; ?>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:flex">
                <!-- Left sidebar - User info -->
                <div class="md:w-1/3 p-6 bg-gray-50 border-r">
                    <div class="text-center">
                        <div class="relative inline-block mb-4">
                            <?php if ($profileUser->getAvatarFilename()): ?>
                                <img src="<?= $profileUser->getAvatar() ?>" 
                                     alt="Profile Picture" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-blue-500">
                            <?php else: ?>
                                <div class="w-32 h-32 rounded-full bg-blue-500 flex items-center justify-center mx-auto border-4 border-blue-600">
                                    <span class="text-4xl font-bold text-white">
                                        <?= strtoupper(substr($profileUser->getUsername(), 0, 1)) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <h2 class="text-2xl font-bold"><?= htmlspecialchars($profileUser->getUsername()) ?></h2>
                        <p class="mt-2">
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                <?= htmlspecialchars($currentLevel) ?>
                            </span>
                        </p>
                        
                        <?php if ($currentUser->getId() !== $profileUser->getId()): ?>
                            <div class="mt-4">
                                <a href="/user/<?= urlencode($profileUser->getUsername()) ?>/follow" 
                                   class="inline-block px-4 py-2 rounded-full text-sm font-medium 
                                          <?= $isFollowing ? 'bg-gray-300 hover:bg-gray-400 text-gray-700' : 'bg-blue-500 hover:bg-blue-600 text-white' ?>">
                                    <?php if ($isFollowing): ?>
                                        <i class="fas fa-user-minus mr-1"></i> Unfollow
                                    <?php else: ?>
                                        <i class="fas fa-user-plus mr-1"></i> Follow
                                    <?php endif; ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <!-- User bio -->
                        <div class="mt-6 text-left">
                            <h3 class="text-md font-semibold mb-2">About</h3>
                            <div class="bg-white p-3 rounded-md shadow-sm mb-3 text-sm text-gray-700">
                                <?php if ($profileUser->getBio()): ?>
                                    <p><?= nl2br(htmlspecialchars($profileUser->getBio())) ?></p>
                                <?php else: ?>
                                    <p class="text-gray-500 italic">No bio provided</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- User stats -->
                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <div class="bg-white p-3 rounded-md shadow-sm text-center">
                                <div class="text-2xl font-bold text-blue-600"><?= $postsCount ?></div>
                                <div class="text-xs text-gray-500">Posts</div>
                            </div>
                            <div class="bg-white p-3 rounded-md shadow-sm text-center">
                                <div class="text-2xl font-bold text-blue-600"><?= $commentsCount ?></div>
                                <div class="text-xs text-gray-500">Comments</div>
                            </div>
                            <div class="bg-white p-3 rounded-md shadow-sm text-center">
                                <div class="text-2xl font-bold text-blue-600"><?= $followersCount ?></div>
                                <div class="text-xs text-gray-500">Followers</div>
                            </div>
                            <div class="bg-white p-3 rounded-md shadow-sm text-center">
                                <div class="text-2xl font-bold text-blue-600"><?= $followingCount ?></div>
                                <div class="text-xs text-gray-500">Following</div>
                            </div>
                        </div>
                        
                        <!-- Reputation bar -->
                        <div class="mt-6">
                            <div class="flex justify-between items-center text-xs mb-1">
                                <span class="text-gray-500">Reputation</span>
                                <span class="text-gray-700 font-medium"><?= number_format($reputationPoints) ?> points</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <?php 
                                $levelPoints = 0;
                                $nextLevel = 0;
                                
                                foreach ([0, 100, 500, 1000, 2500, 5000] as $threshold) {
                                    if ($reputationPoints >= $threshold) {
                                        $levelPoints = $threshold;
                                    } else {
                                        $nextLevel = $threshold;
                                        break;
                                    }
                                }
                                
                                $percentage = 0;
                                if ($nextLevel > $levelPoints) {
                                    $percentage = min(100, (($reputationPoints - $levelPoints) / ($nextLevel - $levelPoints)) * 100);
                                } else {
                                    $percentage = 100;
                                }
                                ?>
                                <div class="h-2.5 bg-blue-600 rounded-full" style="width: <?= $percentage ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- Badges -->
                        <div class="mt-6 text-left">
                            <h3 class="text-md font-semibold mb-2">Badges</h3>
                            <?php if (!empty($badges)): ?>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($badges as $badge): ?>
                                        <div class="bg-white p-2 rounded-md shadow-sm" title="<?= htmlspecialchars($badge['description']) ?>">
                                            <img src="/images/badges/<?= $badge['image'] ?? 'default-badge.svg' ?>" 
                                                 alt="<?= htmlspecialchars($badge['name']) ?>" 
                                                 class="w-8 h-8">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500 text-sm italic">No badges earned yet</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Right content area -->
                <div class="md:w-2/3 p-6">
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold mb-4">Recent Activity</h3>
                        
                        <?php if (!empty($recentActivity)): ?>
                            <div class="space-y-4">
                                <?php foreach ($recentActivity as $activity): ?>
                                    <div class="bg-white p-4 rounded-md shadow-sm">
                                        <div class="flex items-start gap-3">
                                            <div class="rounded-full bg-blue-100 p-2 flex-shrink-0">
                                                <?php if ($activity['type'] === 'submission'): ?>
                                                    <i class="fas fa-file-alt text-blue-600"></i>
                                                <?php elseif ($activity['type'] === 'comment'): ?>
                                                    <i class="fas fa-comment text-green-600"></i>
                                                <?php elseif ($activity['type'] === 'badge'): ?>
                                                    <i class="fas fa-award text-yellow-600"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div>
                                                    <span class="font-medium">
                                                        <?= htmlspecialchars($activity['action']) ?> 
                                                        <?php if ($activity['type'] === 'submission'): ?>
                                                            a solution to 
                                                        <?php elseif ($activity['type'] === 'comment'): ?>
                                                            on 
                                                        <?php endif; ?>
                                                    </span>
                                                    
                                                    <?php if ($activity['type'] === 'badge'): ?>
                                                        <span class="font-bold text-yellow-600">
                                                            <?= htmlspecialchars($activity['title']) ?>
                                                        </span>
                                                        <span class="font-medium"> badge</span>
                                                    <?php else: ?>
                                                        <span class="font-bold text-blue-600">
                                                            <?= htmlspecialchars($activity['challenge_name']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1">
                                                    <?= (new \DateTime($activity['created_at']))->format('M j, Y - H:i') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="bg-white p-4 rounded-md shadow-sm text-gray-500 text-center italic">
                                No recent activity to show
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Submissions section -->
                    <div>
                        <h3 class="text-xl font-semibold mb-4">Submissions</h3>
                        
                        <?php if (!empty($submissions)): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Challenge
                                            </th>
                                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Submission Title
                                            </th>
                                            <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <?php foreach ($submissions as $submission): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="py-3 px-4">
                                                    <a href="/challenges/<?= $submission['challenge_id'] ?>" class="text-blue-600 hover:underline">
                                                        <?= htmlspecialchars($submission['challenge_name']) ?>
                                                    </a>
                                                </td>
                                                <td class="py-3 px-4">
                                                    <a href="/submissions/<?= $submission['id'] ?>" class="text-blue-600 hover:underline">
                                                        <?= htmlspecialchars($submission['title']) ?>
                                                    </a>
                                                </td>
                                                <td class="py-3 px-4 text-sm text-gray-500">
                                                    <?= (new \DateTime($submission['created_at']))->format('M j, Y') ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="bg-white p-4 rounded-md shadow-sm text-gray-500 text-center italic">
                                No approved submissions yet
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html> 