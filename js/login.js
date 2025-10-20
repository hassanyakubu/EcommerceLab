document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const btn = document.getElementById('loginBtn');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');

    // Handle submit with client-side validation then AJAX
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();

        const emailEl = document.getElementById('email');
        const passEl = document.getElementById('password');
        const email = emailEl.value.trim();
        const password = passEl.value;

        // Basic validations
        let valid = true;
        if (!email) {
            showFieldError('email', 'Email is required.');
            valid = false;
        } else if (!isValidEmail(email)) {
            showFieldError('email', 'Please enter a valid email address.');
            valid = false;
        }
        if (!password) {
            showFieldError('password', 'Password is required.');
            valid = false;
        }
        if (!valid) return;

        // UI loading state
        btn.disabled = true;
        btn.textContent = 'Signing In...';

        // Prepare request payload
        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);

        // Send AJAX POST to server-side action
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../actions/login_customer_action.php', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                // Reset button state regardless of outcome
                btn.disabled = false;
                btn.textContent = 'Sign In';

                if (xhr.status === 200) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.status === 'success') {
                            // Show success then redirect
                            successMessage.style.display = 'block';
                            errorMessage.style.display = 'none';
                            setTimeout(() => {
                                window.location.href = res.redirect || '../index.php';
                            }, 800);
                        } else {
                            // Server-side validation/auth failure
                            showGlobalError(res.message || 'Invalid email or password.');
                        }
                    } catch (err) {
                        // Safety for non-JSON responses
                        showGlobalError('Invalid response from server. Please try again.');
                    }
                } else {
                    // Network/server error
                    showGlobalError('Server error. Please try again later.');
                }
            }
        };
        xhr.send(formData);
    });

    // Helpers: clear individual and global errors
    function clearErrors() {
        ['email', 'password'].forEach(name => {
            const input = document.getElementById(name);
            const error = document.getElementById(name + '_error');
            if (input) input.classList.remove('error');
            if (error) { error.style.display = 'none'; error.textContent = ''; }
        });
        if (errorMessage) errorMessage.style.display = 'none';
        if (successMessage) successMessage.style.display = 'none';
    }

    function showFieldError(fieldName, message) {
        const input = document.getElementById(fieldName);
        const error = document.getElementById(fieldName + '_error');
        if (input) input.classList.add('error');
        if (error) { error.textContent = message; error.style.display = 'block'; }
    }

    function showGlobalError(message) {
        if (errorText) errorText.textContent = message;
        if (errorMessage) errorMessage.style.display = 'block';
        if (successMessage) successMessage.style.display = 'none';
    }

    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
