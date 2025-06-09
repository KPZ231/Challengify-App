<?php require_once __DIR__ . '/../admin/partials/header.php'; ?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">System Logs</h1>
        <div class="flex space-x-2">
            <a href="/admin/console" class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-md flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                </svg>
                View Console
            </a>
            <button id="refreshLogs" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                </svg>
                Refresh Logs
            </button>
        </div>
    </div>
    
    <!-- Log Filter -->
    <div class="bg-white p-4 rounded-lg shadow-md mb-6">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="log-filter" class="block text-sm font-medium text-gray-700 mb-1">Filter</label>
                <div class="flex">
                    <input type="text" id="log-filter" placeholder="Filter logs by content" class="block w-full rounded-l-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <button id="clear-filter" class="bg-gray-200 hover:bg-gray-300 rounded-r-md px-4 text-gray-700">
                        Clear
                    </button>
                </div>
            </div>
            <div>
                <label for="log-level" class="block text-sm font-medium text-gray-700 mb-1">Log Level</label>
                <select id="log-level" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Levels</option>
                    <option value="error">Error</option>
                    <option value="warning">Warning</option>
                    <option value="info">Info</option>
                    <option value="debug">Debug</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Logs Display -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <pre id="log-container" class="p-4 font-mono text-sm bg-gray-900 text-gray-100 min-h-[500px] max-h-[800px] overflow-y-auto"><?php
if (!empty($logs)) {
    foreach ($logs as $log) {
        // Parse log level from log line if present
        if (strpos($log, 'ERROR') !== false) {
            echo '<span class="text-red-400">' . htmlspecialchars($log) . '</span>';
        } elseif (strpos($log, 'WARNING') !== false) {
            echo '<span class="text-yellow-400">' . htmlspecialchars($log) . '</span>';
        } elseif (strpos($log, 'INFO') !== false) {
            echo '<span class="text-blue-400">' . htmlspecialchars($log) . '</span>';
        } elseif (strpos($log, 'DEBUG') !== false) {
            echo '<span class="text-gray-400">' . htmlspecialchars($log) . '</span>';
        } else {
            echo htmlspecialchars($log);
        }
    }
} else {
    echo '<span class="text-gray-500">No logs found</span>';
}
?></pre>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logFilter = document.getElementById('log-filter');
        const logLevel = document.getElementById('log-level');
        const clearFilter = document.getElementById('clear-filter');
        const refreshLogs = document.getElementById('refreshLogs');
        const logContainer = document.getElementById('log-container');
        
        // Filter logs based on input
        function filterLogs() {
            const filterText = logFilter.value.toLowerCase();
            const level = logLevel.value;
            
            // Get all log lines
            const logLines = logContainer.getElementsByTagName('span');
            
            for (let i = 0; i < logLines.length; i++) {
                const line = logLines[i];
                const text = line.textContent.toLowerCase();
                
                // Apply text filter
                const matchesText = filterText === '' || text.includes(filterText);
                
                // Apply level filter
                let matchesLevel = true;
                if (level !== 'all') {
                    matchesLevel = text.includes(level.toUpperCase());
                }
                
                // Show or hide based on combined filters
                if (matchesText && matchesLevel) {
                    line.style.display = '';
                } else {
                    line.style.display = 'none';
                }
            }
        }
        
        // Refresh logs by reloading the page
        refreshLogs.addEventListener('click', function() {
            window.location.reload();
        });
        
        // Clear filter
        clearFilter.addEventListener('click', function() {
            logFilter.value = '';
            filterLogs();
        });
        
        // Apply filters on input
        logFilter.addEventListener('input', filterLogs);
        logLevel.addEventListener('change', filterLogs);
    });
</script>

<?php require_once __DIR__ . '/../admin/partials/footer.php'; ?> 