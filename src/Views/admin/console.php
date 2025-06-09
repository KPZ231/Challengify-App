<?php require_once __DIR__ . '/../admin/partials/header.php'; ?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Server Console</h1>
        <a href="/admin/logs" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-md flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-7-7l7 7-7 7" />
            </svg>
            View Logs
        </a>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Real-time Server Output</h2>
            <div class="flex space-x-2">
                <button id="clear-console" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-3 rounded-md text-sm">
                    Clear
                </button>
                <button id="toggle-autoscroll" class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded-md text-sm">
                    Autoscroll: On
                </button>
            </div>
        </div>
        
        <div id="console-status" class="mb-4 py-2 px-4 rounded-md bg-yellow-100 text-yellow-800">
            Connecting to console...
        </div>
        
        <div class="bg-gray-900 rounded-md">
            <div id="console-output" class="p-4 font-mono text-sm text-gray-100 h-[600px] overflow-y-auto"></div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Execute Command</h2>
        <div class="flex">
            <input type="text" id="command-input" placeholder="Enter a command to execute" class="block w-full rounded-l-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <button id="execute-command" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-r-md">Execute</button>
        </div>
        <p class="mt-2 text-sm text-gray-500">Note: Commands are executed with limited permissions for security reasons.</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const consoleOutput = document.getElementById('console-output');
        const consoleStatus = document.getElementById('console-status');
        const clearConsoleBtn = document.getElementById('clear-console');
        const toggleAutoscrollBtn = document.getElementById('toggle-autoscroll');
        const commandInput = document.getElementById('command-input');
        const executeCommandBtn = document.getElementById('execute-command');
        
        let autoscroll = true;
        let socket = null;
        let reconnectAttempts = 0;
        const maxReconnectAttempts = 5;
        
        // Function to create and setup WebSocket
        function setupWebSocket() {
            const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
            const wsUrl = `${protocol}//${window.location.host}/ws/console`;
            
            try {
                socket = new WebSocket(wsUrl);
                
                socket.onopen = function() {
                    consoleStatus.textContent = 'Connected to server console';
                    consoleStatus.classList.remove('bg-yellow-100', 'text-yellow-800', 'bg-red-100', 'text-red-800');
                    consoleStatus.classList.add('bg-green-100', 'text-green-800');
                    reconnectAttempts = 0;
                    
                    // Send authentication
                    socket.send(JSON.stringify({
                        type: 'auth',
                        token: '<?= $_SESSION['csrf_token'] ?>'
                    }));
                };
                
                socket.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        
                        if (data.type === 'console') {
                            // Format and append the console message
                            let logClass = '';
                            
                            if (data.level === 'error') {
                                logClass = 'text-red-400';
                            } else if (data.level === 'warning') {
                                logClass = 'text-yellow-400';
                            } else if (data.level === 'info') {
                                logClass = 'text-blue-400';
                            } else if (data.level === 'debug') {
                                logClass = 'text-gray-400';
                            }
                            
                            const timestamp = new Date().toISOString();
                            const logLine = document.createElement('div');
                            logLine.className = logClass;
                            logLine.innerHTML = `<span class="text-gray-500">[${timestamp}]</span> ${data.message}`;
                            consoleOutput.appendChild(logLine);
                            
                            // Autoscroll if enabled
                            if (autoscroll) {
                                consoleOutput.scrollTop = consoleOutput.scrollHeight;
                            }
                        }
                    } catch (e) {
                        console.error('Error parsing WebSocket message:', e);
                    }
                };
                
                socket.onclose = function(event) {
                    if (reconnectAttempts < maxReconnectAttempts) {
                        consoleStatus.textContent = `Connection lost. Reconnecting (${reconnectAttempts + 1}/${maxReconnectAttempts})...`;
                        consoleStatus.classList.remove('bg-green-100', 'text-green-800');
                        consoleStatus.classList.add('bg-yellow-100', 'text-yellow-800');
                        
                        // Try to reconnect
                        reconnectAttempts++;
                        setTimeout(setupWebSocket, 3000);
                    } else {
                        consoleStatus.textContent = 'Connection lost. Please refresh the page to reconnect.';
                        consoleStatus.classList.remove('bg-yellow-100', 'text-yellow-800');
                        consoleStatus.classList.add('bg-red-100', 'text-red-800');
                    }
                };
                
                socket.onerror = function(error) {
                    console.error('WebSocket error:', error);
                    consoleStatus.textContent = 'Error connecting to server console';
                    consoleStatus.classList.remove('bg-green-100', 'text-green-800', 'bg-yellow-100', 'text-yellow-800');
                    consoleStatus.classList.add('bg-red-100', 'text-red-800');
                };
            } catch (e) {
                console.error('Error setting up WebSocket:', e);
                consoleStatus.textContent = 'Failed to connect to server console';
                consoleStatus.classList.remove('bg-green-100', 'text-green-800', 'bg-yellow-100', 'text-yellow-800');
                consoleStatus.classList.add('bg-red-100', 'text-red-800');
            }
        }
        
        // Set up initial connection
        setupWebSocket();
        
        // Clear console
        clearConsoleBtn.addEventListener('click', function() {
            consoleOutput.innerHTML = '';
        });
        
        // Toggle autoscroll
        toggleAutoscrollBtn.addEventListener('click', function() {
            autoscroll = !autoscroll;
            toggleAutoscrollBtn.textContent = `Autoscroll: ${autoscroll ? 'On' : 'Off'}`;
            
            if (autoscroll) {
                consoleOutput.scrollTop = consoleOutput.scrollHeight;
            }
        });
        
        // Execute command
        executeCommandBtn.addEventListener('click', function() {
            const command = commandInput.value.trim();
            
            if (command && socket && socket.readyState === WebSocket.OPEN) {
                socket.send(JSON.stringify({
                    type: 'command',
                    command: command
                }));
                
                // Clear input
                commandInput.value = '';
            }
        });
        
        // Execute command on Enter key
        commandInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                executeCommandBtn.click();
            }
        });
        
        // Prevent page unload while connected
        window.addEventListener('beforeunload', function() {
            if (socket) {
                socket.close();
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../admin/partials/footer.php'; ?> 