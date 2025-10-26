document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const registerBtn = document.getElementById('registerBtn');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');

    // Handle form submission
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        clearAllErrors();
        
        if (validateForm()) {
            showLoadingState();
            submitForm();
        }
    });

    const inputs = registerForm.querySelectorAll('input[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                this.classList.remove('error');
                const errorElement = document.getElementById(this.name + '_error');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
            }
        });
    });

    function validateForm() {
        let isValid = true;
        
        const fields = [
            { name: 'full_name', label: 'Full Name', minLength: 2, maxLength: 100 },
            { name: 'email', label: 'Email Address', type: 'email', maxLength: 255 },
            { name: 'password', label: 'Password', minLength: 6, maxLength: 255 },
            { name: 'country', label: 'Country', minLength: 2, maxLength: 100 },
            { name: 'city', label: 'City', minLength: 2, maxLength: 100 },
            { name: 'contact_number', label: 'Contact Number', type: 'phone', maxLength: 20 }
        ];

        fields.forEach(field => {
            const input = document.getElementById(field.name);
            if (!validateField(input, field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    function validateField(input, fieldConfig = null) {
        const value = input.value.trim();
        const fieldName = input.name;
        const fieldLabel = fieldConfig ? fieldConfig.label : fieldName.replace('_', ' ');

        if (!value) {
            showFieldError(fieldName, `${fieldLabel} is required.`);
            return false;
        }

        switch (fieldName) {
            case 'full_name':
                if (value.length < 2) {
                    showFieldError(fieldName, 'Full name must be at least 2 characters long.');
                    return false;
                }
                if (value.length > 100) {
                    showFieldError(fieldName, 'Full name must not exceed 100 characters.');
                    return false;
                }
                if (!/^[a-zA-Z\s\-']+$/.test(value)) {
                    showFieldError(fieldName, 'Full name can only contain letters, spaces, hyphens, and apostrophes.');
                    return false;
                }
                break;

            case 'email':
                if (!isValidEmail(value)) {
                    showFieldError(fieldName, 'Please enter a valid email address.');
                    return false;
                }
                if (value.length > 255) {
                    showFieldError(fieldName, 'Email must not exceed 255 characters.');
                    return false;
                }
                break;

            case 'password':
                if (value.length < 6) {
                    showFieldError(fieldName, 'Password must be at least 6 characters long.');
                    return false;
                }
                if (value.length > 255) {
                    showFieldError(fieldName, 'Password must not exceed 255 characters.');
                    return false;
                }
                break;

            case 'country':
            case 'city':
                if (value.length < 2) {
                    showFieldError(fieldName, `${fieldLabel} must be at least 2 characters long.`);
                    return false;
                }
                if (value.length > 100) {
                    showFieldError(fieldName, `${fieldLabel} must not exceed 100 characters.`);
                    return false;
                }
                if (!/^[a-zA-Z\s\-]+$/.test(value)) {
                    showFieldError(fieldName, `${fieldLabel} can only contain letters, spaces, and hyphens.`);
                    return false;
                }
                break;

            case 'contact_number':
                if (!isValidPhoneNumber(value)) {
                    showFieldError(fieldName, 'Please enter a valid contact number.');
                    return false;
                }
                if (value.length > 20) {
                    showFieldError(fieldName, 'Contact number must not exceed 20 characters.');
                    return false;
                }
                break;
        }

        clearFieldError(fieldName);
        return true;
    }

    function submitForm() {
        const formData = new FormData(registerForm);
        
        // Send AJAX request to server
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../actions/register_customer_action.php', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                hideLoadingState();
                
                if (xhr.status === 200) {
                    try {
                        console.log('Server response:', xhr.responseText);
                        const response = JSON.parse(xhr.responseText);
                        handleResponse(response);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        console.error('Response text:', xhr.responseText);
                        showGlobalError('Invalid response from server. Please try again.');
                    }
                } else {
                    showGlobalError('Server error. Please try again later.');
                }
            }
        };
        
        xhr.send(formData);
    }

    function handleResponse(response) {
        if (response.status === 'success') {
            showSuccessMessage(response.message);
            
            // Redirect to login page after 2 seconds
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            showGlobalError(response.message);
        }
    }

    function showLoadingState() {
        registerBtn.disabled = true;
        registerBtn.textContent = 'Creating Account...';
    }

    function hideLoadingState() {
        registerBtn.disabled = false;
        registerBtn.textContent = 'Create Account';
    }

    function showFieldError(fieldName, message) {
        const input = document.getElementById(fieldName);
        const errorElement = document.getElementById(fieldName + '_error');
        
        if (input && errorElement) {
            input.classList.add('error');
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    function clearFieldError(fieldName) {
        const input = document.getElementById(fieldName);
        const errorElement = document.getElementById(fieldName + '_error');
        
        if (input && errorElement) {
            input.classList.remove('error');
            errorElement.style.display = 'none';
        }
    }

    function clearAllErrors() {
        const inputs = registerForm.querySelectorAll('input');
        inputs.forEach(input => {
            clearFieldError(input.name);
        });
        
        errorMessage.style.display = 'none';
        successMessage.style.display = 'none';
    }

    function showGlobalError(message) {
        errorText.textContent = message;
        errorMessage.style.display = 'block';
        successMessage.style.display = 'none';
    }

    function showSuccessMessage(message) {
        const span = successMessage.querySelector('span');
        if (span) {
            span.textContent = message;
        } else {
            successMessage.textContent = message;
        }
        successMessage.style.display = 'block';
        errorMessage.style.display = 'none';
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function isValidPhoneNumber(phone) {
        const phoneRegex = /^[0-9+\-\s()]+$/;
        return phoneRegex.test(phone) && phone.length >= 7;
    }

});
