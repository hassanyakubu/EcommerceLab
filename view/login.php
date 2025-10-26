<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - E-Commerce Platform</title>
    <link rel="stylesheet" href="../css/style.css?v=3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-sign-in-alt"></i> Welcome Back</h1>
            <p>Sign in to your account</p>
        </div>

        <!-- Success Message -->
        <div class="message message-success" id="successMessage" style="display: none;">
            <span>Login successful! Redirecting...</span>
        </div>

        <!-- Error Message -->
        <div class="message message-error" id="errorMessage" style="display: none;">
            <span id="errorText"></span>
        </div>

        <form id="loginForm" novalidate>
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" required>
                <div class="error-message" id="email_error"></div>
            </div>

            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" required>
                <div class="error-message" id="password_error"></div>
            </div>

            <button type="submit" class="btn btn-primary" id="loginBtn">
                Sign In
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <p>Don't have an account? <a href="register.php">Create one here</a></p>
        </div>
    </div>

    <script src="../js/login.js"></script>
</body>
</html>
