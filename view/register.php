<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration - E-Commerce Platform</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-MW3p+1rQe2b/nGq8q5tDkzKc6C0f8Qd1XvQ3tQ2vYkq5lOjVv+L9pQH2GkR8o3sYQ6s1m1m2yQ1M8r1G8j3z7A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-plus"></i> Create Account</h1>
            <p>Join our e-commerce platform today</p>
        </div>

        <!-- Success Message -->
        <div class="message message-success" id="successMessage" style="display: none;">
            Registration successful! Redirecting to login page...
        </div>

        <!-- Error Message -->
        <div class="message message-error" id="errorMessage" style="display: none;">
            <span id="errorText"></span>
        </div>

        <form id="registerForm" novalidate>
            <div class="form-group">
                <label for="full_name">Full Name <span class="required">*</span></label>
                <input type="text" id="full_name" name="full_name" required maxlength="100">
                <div class="error-message" id="full_name_error"></div>
            </div>

            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" required maxlength="255">
                <div class="error-message" id="email_error"></div>
            </div>

            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" required maxlength="255">
                <div class="error-message" id="password_error"></div>
            </div>

            <div class="form-group">
                <label for="country">Country <span class="required">*</span></label>
                <input type="text" id="country" name="country" required maxlength="100">
                <div class="error-message" id="country_error"></div>
            </div>

            <div class="form-group">
                <label for="city">City <span class="required">*</span></label>
                <input type="text" id="city" name="city" required maxlength="100">
                <div class="error-message" id="city_error"></div>
            </div>

            <div class="form-group">
                <label for="contact_number">Contact Number <span class="required">*</span></label>
                <input type="tel" id="contact_number" name="contact_number" required maxlength="20">
                <div class="error-message" id="contact_number_error"></div>
            </div>


            <button type="submit" class="btn btn-primary" id="registerBtn">
                Create Account
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <p>Already have an account? <a href="login.php">Sign in here</a></p>
        </div>
    </div>

    <script src="../js/register.js"></script>
</body>
</html>
