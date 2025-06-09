    <!-- Footer -->
    <footer class="bg-white py-4 mt-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-600 text-sm mb-4 md:mb-0">
                    &copy; <?= date('Y') ?> Challengify Admin Panel. All rights reserved.
                </div>
                <div class="text-gray-600 text-sm">
                    Version 1.0.0
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Extra JavaScripts -->
    <script>
        // Function to format dates
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString();
        }
        
        // Function to display flash messages
        function showFlashMessage(type, message) {
            const flashContainer = document.createElement('div');
            flashContainer.className = `fixed top-4 right-4 p-4 rounded-md text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} z-50`;
            flashContainer.innerHTML = message;
            document.body.appendChild(flashContainer);
            
            // Remove after 5 seconds
            setTimeout(() => {
                flashContainer.remove();
            }, 5000);
        }
    </script>
</body>
</html> 