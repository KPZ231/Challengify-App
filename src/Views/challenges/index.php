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

    <!-- Header Section -->
    <header class="ch-bg-gradient text-white py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl">
                <h1 class="text-3xl md:text-4xl font-bold mb-4">Explore Challenges</h1>
                <p class="text-lg mb-6">Discover creative challenges designed to spark your imagination and build new skills in just 15-30 minutes a day.</p>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Category Filter -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Filter by Category</h2>
            <div class="flex flex-wrap gap-2">
                <a href="/challenges" class="ch-badge <?= !$selectedCategory ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-600 hover:bg-blue-200' ?>">
                    All Categories
                </a>
                <?php foreach ($categories as $category): ?>
                <a href="/challenges?category=<?= $category->getId() ?>" 
                   class="ch-badge <?= $selectedCategory == $category->getId() ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-600 hover:bg-blue-200' ?>">
                    <?= e($category->getName()) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Challenges List -->
        <div class="challenges-container">
            <?php if (empty($challenges)): ?>
                <div class="bg-blue-50 p-6 rounded-lg text-center">
                    <h3 class="text-xl font-semibold mb-2">No challenges found</h3>
                    <p>There are no active challenges in this category at the moment. Please check back later or try another category.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="challenges-grid">
                    <?php foreach ($challenges as $challenge): ?>
                        <div class="ch-card shadow-md overflow-hidden">
                            <?php if ($challenge->getImage()): ?>
                                <img src="<?= e($challenge->getImage()) ?>" alt="<?= e($challenge->getTitle()) ?>" class="w-full h-48 object-cover">
                            <?php else: ?>
                                <div class="w-full h-48 bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-trophy text-blue-300 text-4xl"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="ch-badge bg-blue-100 text-blue-600">
                                        <?php 
                                        foreach ($categories as $category) {
                                            if ($category->getId() === $challenge->getCategoryId()) {
                                                echo e($category->getName());
                                                break;
                                            }
                                        }
                                        ?>
                                    </span>
                                    <span class="ch-badge <?= $challenge->getDifficulty() === 'easy' ? 'bg-green-100 text-green-600' : ($challenge->getDifficulty() === 'medium' ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600') ?>">
                                        <?= ucfirst(e($challenge->getDifficulty())) ?>
                                    </span>
                                </div>
                                
                                <h3 class="text-xl font-semibold mb-2"><?= e($challenge->getTitle()) ?></h3>
                                <p class="text-gray-600 mb-4"><?= truncate(e($challenge->getDescription()), 120) ?></p>
                                
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-gray-500">
                                        <span><i class="far fa-calendar-alt mr-1"></i>Ends: <?= formatDate($challenge->getEndDate()->format('Y-m-d H:i:s'), 'M j, Y') ?></span>
                                    </div>
                                    <a href="/challenges/<?= $challenge->getId() ?>" class="ch-btn ch-btn-sm ch-btn-primary">View Challenge</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['last_page'] > 1): ?>
            <div class="flex justify-center mt-8">
                <div class="ch-pagination">
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="/challenges?page=<?= $pagination['current_page'] - 1 ?><?= $selectedCategory ? '&category=' . $selectedCategory : '' ?>" 
                           class="ch-pagination-item">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php 
                    $startPage = max(1, $pagination['current_page'] - 2);
                    $endPage = min($pagination['last_page'], $pagination['current_page'] + 2);
                    
                    if ($startPage > 1): ?>
                        <a href="/challenges?page=1<?= $selectedCategory ? '&category=' . $selectedCategory : '' ?>" 
                           class="ch-pagination-item">1</a>
                        <?php if ($startPage > 2): ?>
                            <span class="ch-pagination-ellipsis">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <a href="/challenges?page=<?= $i ?><?= $selectedCategory ? '&category=' . $selectedCategory : '' ?>" 
                           class="ch-pagination-item <?= $i === $pagination['current_page'] ? 'ch-pagination-active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($endPage < $pagination['last_page']): ?>
                        <?php if ($endPage < $pagination['last_page'] - 1): ?>
                            <span class="ch-pagination-ellipsis">...</span>
                        <?php endif; ?>
                        <a href="/challenges?page=<?= $pagination['last_page'] ?><?= $selectedCategory ? '&category=' . $selectedCategory : '' ?>" 
                           class="ch-pagination-item"><?= $pagination['last_page'] ?></a>
                    <?php endif; ?>
                    
                    <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                        <a href="/challenges?page=<?= $pagination['current_page'] + 1 ?><?= $selectedCategory ? '&category=' . $selectedCategory : '' ?>" 
                           class="ch-pagination-item">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <!-- AJAX Script for Dynamic Loading -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get elements
            const categoryLinks = document.querySelectorAll('.ch-badge[href^="/challenges?category="]');
            const paginationLinks = document.querySelectorAll('.ch-pagination-item');
            const challengesGrid = document.getElementById('challenges-grid');
            
            // Function to load challenges via AJAX
            function loadChallenges(url) {
                // Add AJAX header
                const ajaxUrl = url + (url.includes('?') ? '&' : '?') + 'ajax=1';
                
                // Show loading state
                challengesGrid.innerHTML = '<div class="col-span-3 text-center py-8"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading challenges...</p></div>';
                
                fetch(ajaxUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Update challenges grid
                    challengesGrid.innerHTML = '';
                    
                    if (data.challenges.length === 0) {
                        challengesGrid.innerHTML = `
                            <div class="col-span-3 bg-blue-50 p-6 rounded-lg text-center">
                                <h3 class="text-xl font-semibold mb-2">No challenges found</h3>
                                <p>There are no active challenges in this category at the moment. Please check back later or try another category.</p>
                            </div>
                        `;
                        return;
                    }
                    
                    data.challenges.forEach(challenge => {
                        // Find category name
                        let categoryName = '';
                        document.querySelectorAll('.ch-badge[href^="/challenges?category="]').forEach(link => {
                            if (link.href.includes(challenge.category_id)) {
                                categoryName = link.textContent.trim();
                            }
                        });
                        
                        // Determine difficulty class
                        let difficultyClass = '';
                        if (challenge.difficulty === 'easy') {
                            difficultyClass = 'bg-green-100 text-green-600';
                        } else if (challenge.difficulty === 'medium') {
                            difficultyClass = 'bg-yellow-100 text-yellow-600';
                        } else {
                            difficultyClass = 'bg-red-100 text-red-600';
                        }
                        
                        // Create challenge card
                        const card = document.createElement('div');
                        card.className = 'ch-card shadow-md overflow-hidden';
                        
                        // Format end date
                        const endDate = new Date(challenge.end_date);
                        const formattedDate = endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        
                        card.innerHTML = `
                            ${challenge.image 
                                ? `<img src="${challenge.image}" alt="${challenge.title}" class="w-full h-48 object-cover">` 
                                : `<div class="w-full h-48 bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-trophy text-blue-300 text-4xl"></i>
                                   </div>`
                            }
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="ch-badge bg-blue-100 text-blue-600">${categoryName}</span>
                                    <span class="ch-badge ${difficultyClass}">${challenge.difficulty.charAt(0).toUpperCase() + challenge.difficulty.slice(1)}</span>
                                </div>
                                
                                <h3 class="text-xl font-semibold mb-2">${challenge.title}</h3>
                                <p class="text-gray-600 mb-4">${challenge.description.length > 120 ? challenge.description.substring(0, 120) + '...' : challenge.description}</p>
                                
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-gray-500">
                                        <span><i class="far fa-calendar-alt mr-1"></i>Ends: ${formattedDate}</span>
                                    </div>
                                    <a href="/challenges/${challenge.id}" class="ch-btn ch-btn-sm ch-btn-primary">View Challenge</a>
                                </div>
                            </div>
                        `;
                        
                        challengesGrid.appendChild(card);
                    });
                    
                    // Update URL without reloading page
                    history.pushState(null, '', url);
                })
                .catch(error => {
                    console.error('Error loading challenges:', error);
                    challengesGrid.innerHTML = `
                        <div class="col-span-3 bg-red-50 p-6 rounded-lg text-center">
                            <h3 class="text-xl font-semibold mb-2">Error loading challenges</h3>
                            <p>There was a problem loading the challenges. Please try again later.</p>
                        </div>
                    `;
                });
            }
            
            // Add event listeners for category links
            categoryLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadChallenges(this.getAttribute('href'));
                    
                    // Update active category
                    document.querySelectorAll('.ch-badge').forEach(badge => {
                        badge.classList.remove('bg-blue-600', 'text-white');
                        badge.classList.add('bg-blue-100', 'text-blue-600', 'hover:bg-blue-200');
                    });
                    this.classList.remove('bg-blue-100', 'text-blue-600', 'hover:bg-blue-200');
                    this.classList.add('bg-blue-600', 'text-white');
                });
            });
            
            // Add event listeners for pagination links
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    loadChallenges(this.getAttribute('href'));
                    
                    // Scroll to top of challenges container
                    document.querySelector('.challenges-container').scrollIntoView({ behavior: 'smooth' });
                });
            });
        });
    </script>
</body>
</html> 