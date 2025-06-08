/**
 * Demo JavaScript file to simulate login for testing the user panel
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the login page
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        // Override form submission for demo purposes
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const email = document.getElementById('email').value;
            const username = email.split('@')[0]; // Use part before @ as username
            
            // Create a sample JWT token payload
            const payload = {
                iat: Math.floor(Date.now() / 1000),
                exp: Math.floor(Date.now() / 1000) + 3600, // Expires in 1 hour
                data: {
                    id: 'demo-user-123',
                    email: email,
                    username: username,
                    role: 'user'
                }
            };
            
            // Base64 encode the payload (simplified JWT)
            const header = btoa(JSON.stringify({ alg: 'HS256', typ: 'JWT' }));
            const payloadBase64 = btoa(JSON.stringify(payload));
            const signature = 'demo_signature'; // This is not a real signature
            
            // Create a simplified JWT token
            const token = `${header}.${payloadBase64}.${signature}`;
            
            // Set the auth cookie
            document.cookie = `auth_token=${token}; path=/; max-age=3600`;
            
            // Set expiration cookie
            const expirationTime = new Date();
            expirationTime.setTime(expirationTime.getTime() + (3600 * 1000));
            document.cookie = `token_expiration=${expirationTime.toUTCString()}; path=/; max-age=3600`;
            
            // Redirect to home page
            window.location.href = '/';
        });
    }
    
    // Add a logout button functionality
    const logoutLinks = document.querySelectorAll('a[href="/logout"]');
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Clear cookies
            document.cookie = 'auth_token=; path=/; max-age=0';
            document.cookie = 'token_expiration=; path=/; max-age=0';
            
            // Redirect to home page
            window.location.href = '/';
        });
    });
}); 