// Settings page JavaScript functionality

document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    
    // Check if we're on the settings page
    if (tabLinks.length === 0 || tabContents.length === 0) {
        return;
    }
    
    // Function to show a specific tab
    function showTab(tabId) {
        console.log("Showing tab:", tabId);
        
        // Hide all tabs
        tabContents.forEach(tab => {
            tab.classList.add('hidden');
        });
        
        // Remove active class from all tab links
        tabLinks.forEach(link => {
            link.classList.remove('bg-blue-100', 'text-blue-700');
            link.classList.add('hover:bg-gray-100');
        });
        
        // Show the selected tab
        const selectedTab = document.getElementById(tabId);
        if (selectedTab) {
            selectedTab.classList.remove('hidden');
        } else {
            console.error("Tab not found:", tabId);
        }
        
        // Set active class on the selected tab link
        const selectedLink = document.querySelector(`[data-tab="${tabId}"]`);
        if (selectedLink) {
            selectedLink.classList.add('bg-blue-100', 'text-blue-700');
            selectedLink.classList.remove('hover:bg-gray-100');
        } else {
            console.error("Tab link not found for:", tabId);
        }
        
        // Store the active tab in localStorage for persistence
        localStorage.setItem('activeSettingsTab', tabId);
    }
    
    // Add click event listeners to tab links
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.getAttribute('data-tab');
            showTab(tabId);
            
            // Update URL hash without triggering a page reload
            history.pushState(null, null, `#${tabId}`);
        });
    });
    
    // Function to determine which tab to show on page load
    function selectInitialTab() {
        let activeTab = 'tab-notifications'; // Default tab
        
        // First priority: Check URL hash
        if (window.location.hash) {
            const hashValue = window.location.hash.substring(1);
            if (document.getElementById(hashValue)) {
                activeTab = hashValue;
                console.log("Using hash tab:", activeTab);
            }
        } 
        // Second priority: Check localStorage
        else if (localStorage.getItem('activeSettingsTab')) {
            const savedTab = localStorage.getItem('activeSettingsTab');
            if (document.getElementById(savedTab)) {
                activeTab = savedTab;
                console.log("Using localStorage tab:", activeTab);
            }
        }
        
        showTab(activeTab);
    }
    
    // Call immediately to set the correct tab on page load
    selectInitialTab();
    
    // Also handle hash changes (for browser back/forward navigation)
    window.addEventListener('hashchange', function() {
        if (window.location.hash) {
            const hashValue = window.location.hash.substring(1);
            if (document.getElementById(hashValue)) {
                showTab(hashValue);
            }
        }
    });
}); 