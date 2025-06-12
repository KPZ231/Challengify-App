<div id="cookie-banner" class="fixed bottom-0 left-0 right-0 bg-gray-900 text-white p-3 sm:p-4 z-50 transform transition-transform duration-300" style="display: none;">
    <div class="container mx-auto px-2 sm:px-4 flex flex-col sm:flex-row items-center justify-between">
        <div class="mb-3 sm:mb-0 text-center sm:text-left">
            <p class="text-xs sm:text-sm">
                We use cookies to enhance your experience. By continuing to visit this site you agree to our use of cookies. 
                <a href="/cookie-policy" class="underline hover:text-gray-300">Learn more</a>
            </p>
        </div>
        <div class="flex gap-2 sm:gap-4">
            <button onclick="acceptCookies()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-1.5 sm:py-2 rounded-md text-xs sm:text-sm transition-colors">
                Accept
            </button>
            <button onclick="declineCookies()" class="bg-gray-700 hover:bg-gray-800 text-white px-4 sm:px-6 py-1.5 sm:py-2 rounded-md text-xs sm:text-sm transition-colors">
                Decline
            </button>
        </div>
    </div>
</div>

<script>
function showCookieBanner() {
    if (!localStorage.getItem('cookieConsent')) {
        document.getElementById('cookie-banner').style.display = 'block';
    }
}

function acceptCookies() {
    localStorage.setItem('cookieConsent', 'accepted');
    document.getElementById('cookie-banner').style.transform = 'translateY(100%)';
    setTimeout(() => {
        document.getElementById('cookie-banner').style.display = 'none';
    }, 300);
}

function declineCookies() {
    localStorage.setItem('cookieConsent', 'declined');
    document.getElementById('cookie-banner').style.transform = 'translateY(100%)';
    setTimeout(() => {
        document.getElementById('cookie-banner').style.display = 'none';
    }, 300);
}

document.addEventListener('DOMContentLoaded', showCookieBanner);
</script>
