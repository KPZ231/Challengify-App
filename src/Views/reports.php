<?php
// Reports view
$pageTitle = "Your Reports";
$cspNonce = $GLOBALS['cspNonce'] ?? '';
require_once __DIR__ . '/partials/header.php';
?>
 
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Your Activity Reports</h1>
        <p class="text-gray-600">Track your performance and engagement metrics</p>
    </div>

    <!-- Period selector form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Select Time Period</h2>
        <form method="GET" action="/reports" class="flex flex-wrap items-end gap-4">
            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                <select name="period" class="ch-form-control bg-gray-50 border border-gray-300 rounded-md w-full" id="periodSelect">
                    <option value="today" <?= $period === 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>This Week</option>
                    <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>This Month</option>
                    <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>This Year</option>
                    <option value="custom" <?= $period === 'custom' ? 'selected' : '' ?>>Custom Date</option>
                </select>
            </div>
            
            <div id="customDateContainer" class="<?= $period !== 'custom' ? 'hidden' : '' ?>">
                <label class="block text-sm font-medium text-gray-700 mb-1">Custom Date</label>
                <input type="date" name="date" value="<?= $customDate ?? date('Y-m-d') ?>" class="ch-form-control bg-gray-50 border border-gray-300 rounded-md">
            </div>
            
            <div>
                <button type="submit" class="ch-btn ch-btn-primary">Generate Report</button>
            </div>
        </form>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Completed Challenges -->
        <div class="bg-white rounded-lg shadow-md p-6 ch-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Completed Challenges</h3>
                <span class="text-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="text-3xl font-bold"><?= number_format($reportData['completed_challenges']) ?></div>
            <p class="text-gray-600 mt-2">Challenges completed during this period</p>
        </div>
        
        <!-- Likes Received -->
        <div class="bg-white rounded-lg shadow-md p-6 ch-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Likes Received</h3>
                <span class="text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </span>
            </div>
            <div class="text-3xl font-bold"><?= number_format($reportData['likes_received']) ?></div>
            <p class="text-gray-600 mt-2">Likes on your submissions and content</p>
        </div>
        
        <!-- New Followers -->
        <div class="bg-white rounded-lg shadow-md p-6 ch-card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">New Followers</h3>
                <span class="text-purple-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </span>
            </div>
            <div class="text-3xl font-bold"><?= number_format($reportData['new_followers']) ?></div>
            <p class="text-gray-600 mt-2">New followers gained during this period</p>
        </div>
    </div>

    <!-- Graph Placeholder (In a real app, you'd integrate a charting library) -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Activity Trends</h2>
        <div class="h-64 bg-gray-100 flex items-center justify-center">
            <p class="text-gray-500">Activity trend visualization would appear here</p>
        </div>
    </div>
</div>

<script nonce="<?= $cspNonce ?>">
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('periodSelect');
    const customDateContainer = document.getElementById('customDateContainer');
    
    periodSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateContainer.classList.remove('hidden');
        } else {
            customDateContainer.classList.add('hidden');
        }
    });
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?> 