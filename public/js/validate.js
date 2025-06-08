// Form validation and password strength indicator
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    const togglePasswordVisibility = (passwordInput, toggleButton) => {
        toggleButton.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Update the eye icon
            const eyeIcon = toggleButton.querySelector('i');
            if (type === 'password') {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    };

    // Set up password visibility toggles
    const passwordFields = document.querySelectorAll('input[type="password"]');
    passwordFields.forEach(field => {
        const toggleButton = field.parentElement.querySelector('.toggle-password');
        if (toggleButton) {
            togglePasswordVisibility(field, toggleButton);
        }
    });

    // Password strength indicator
    const passwordStrength = {
        0: { text: 'Very Weak', color: '#ff4136' },
        1: { text: 'Weak', color: '#ff851b' },
        2: { text: 'Medium', color: '#ffdc00' },
        3: { text: 'Strong', color: '#2ecc40' },
        4: { text: 'Very Strong', color: '#01ff70' }
    };

    const checkPasswordStrength = (password) => {
        let strength = 0;
        
        // If password is 8+ characters
        if (password.length >= 8) strength += 1;
        
        // If password contains lowercase and uppercase letters
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
        
        // If password contains letters and numbers
        if (/\d/.test(password) && /[a-zA-Z]/.test(password)) strength += 1;
        
        // If password contains special characters
        if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
        
        return strength;
    };

    // Update password strength indicator
    const updateStrengthIndicator = (password, strengthMeter, strengthText) => {
        const strength = checkPasswordStrength(password);
        
        strengthMeter.style.width = `${(strength + 1) * 20}%`;
        strengthMeter.style.backgroundColor = passwordStrength[strength].color;
        strengthText.textContent = passwordStrength[strength].text;
        strengthText.style.color = passwordStrength[strength].color;
    };

    // Validate password
    const validatePassword = (password) => {
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password),
            special: /[^a-zA-Z0-9]/.test(password)
        };
        
        return requirements;
    };

    // Update password requirements list
    const updateRequirements = (password, requirementElements) => {
        const requirements = validatePassword(password);
        
        // Update each requirement element
        Object.keys(requirements).forEach(req => {
            const element = requirementElements[req];
            if (element) {
                if (requirements[req]) {
                    element.classList.add('text-green-500');
                    element.classList.remove('text-red-500');
                    element.querySelector('i').classList.remove('fa-times');
                    element.querySelector('i').classList.add('fa-check');
                } else {
                    element.classList.add('text-red-500');
                    element.classList.remove('text-green-500');
                    element.querySelector('i').classList.remove('fa-check');
                    element.querySelector('i').classList.add('fa-times');
                }
            }
        });
    };

    // Set up password strength indicator for password fields
    const passwordInput = document.getElementById('password');
    const strengthMeter = document.getElementById('password-strength-meter');
    const strengthText = document.getElementById('password-strength-text');
    const requirementElements = {
        length: document.getElementById('req-length'),
        uppercase: document.getElementById('req-uppercase'),
        lowercase: document.getElementById('req-lowercase'),
        number: document.getElementById('req-number'),
        special: document.getElementById('req-special')
    };

    if (passwordInput && strengthMeter && strengthText) {
        passwordInput.addEventListener('input', function() {
            updateStrengthIndicator(this.value, strengthMeter, strengthText);
            if (requirementElements.length) {
                updateRequirements(this.value, requirementElements);
            }
        });
    }

    // Form validation
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                showValidationError('All fields are required');
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            if (!username || !email || !password || !passwordConfirm) {
                e.preventDefault();
                showValidationError('All fields are required');
                return;
            }
            
            if (!validateEmail(email)) {
                e.preventDefault();
                showValidationError('Please enter a valid email address');
                return;
            }
            
            const passwordRequirements = validatePassword(password);
            if (!Object.values(passwordRequirements).every(Boolean)) {
                e.preventDefault();
                showValidationError('Password does not meet all requirements');
                return;
            }
            
            if (password !== passwordConfirm) {
                e.preventDefault();
                showValidationError('Passwords do not match');
                return;
            }
        });
    }

    // Helper functions
    function validateEmail(email) {
        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    function showValidationError(message) {
        const errorContainer = document.getElementById('form-error');
        if (errorContainer) {
            errorContainer.textContent = message;
            errorContainer.classList.remove('hidden');
        } else {
            alert(message);
        }
    }
}); 