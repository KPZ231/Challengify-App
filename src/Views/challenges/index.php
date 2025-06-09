<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse and participate in creative challenges on Challengify">
    <meta name="keywords" content="challenges, creative challenges, daily challenges, creativity, community challenges">
    <title>Challenges | Challengify</title>
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/navbar.php'; ?>
    <?php include __DIR__ . '/../partials/cookie-accept.php'; ?>

    <!-- Header Section -->
    <header class="ch-bg-gradient text-white py-8 md:py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl">
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4">Active Challenge</h1>
                <p class="text-base sm:text-lg mb-6">Discover our current active challenge designed to spark your imagination and build new skills.</p>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6 md:py-8">
        <!-- Challenges List -->
        <div class="challenges-container">
            <?php if (empty($challenges)): ?>
                <div class="bg-blue-50 p-4 sm:p-6 rounded-lg text-center">
                    <h3 class="text-xl font-semibold mb-2">No active challenges</h3>
                    <p>There are no active challenges at the moment. Please check back later.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-6 max-w-4xl mx-auto" id="challenges-grid">
                    <?php foreach ($challenges as $challenge): ?>
                        <div class="ch-card shadow-md overflow-hidden">
                            <?php if ($challenge['image']): ?>
                                <img src="<?= e($challenge['image']) ?>" alt="<?= e($challenge['title']) ?>" class="w-full h-48 sm:h-64 object-cover">
                            <?php else: ?>
                                <div class="w-full h-48 sm:h-64 bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-trophy text-blue-300 text-4xl"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-4 sm:p-6">
                                <h3 class="text-xl sm:text-2xl font-semibold mb-2 sm:mb-3"><?= e($challenge['title']) ?></h3>
                                <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base"><?= nl2br(e($challenge['description'])) ?></p>
                                
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                    <div class="text-xs sm:text-sm text-gray-500">
                                        <div class="mb-2 sm:mb-0">
                                            <i class="far fa-calendar-alt mr-1"></i>Ends: <?= formatDate($challenge['end_date'], 'M j, Y') ?>
                                        </div>
                                        <span class="ch-badge <?= $challenge['difficulty'] === 'easy' ? 'bg-green-100 text-green-600' : ($challenge['difficulty'] === 'medium' ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600') ?>">
                                            <?= ucfirst(e($challenge['difficulty'])) ?>
                                        </span>
                                    </div>
                                    <a href="/challenges/<?= $challenge['id'] ?>" class="ch-btn ch-btn-primary w-full sm:w-auto text-center">View Challenge</a>
                                </div>
                                
                                <?php if (isLoggedIn()): ?>
                                <div class="mt-4 sm:mt-6 pt-4 border-t border-gray-100">
                                    <p class="text-gray-600 mb-2 text-sm"><i class="fas fa-info-circle mr-2"></i>You are logged in and can submit your entry for this challenge.</p>
                                    <a href="/challenges/<?= $challenge['id'] ?>" class="ch-btn ch-btn-outline-primary w-full text-center">Submit Your Entry</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html> 