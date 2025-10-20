<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//for header redirection
ob_start();

//function to check for login
function isLoggedIn(): bool {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['customer_id']);
}

//function to get user ID
function getCurrentUserId(): ?int {
    return isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
}

//function to check for role (admin, customer, etc)
// Define role constants to avoid magic numbers
if (!defined('ROLE_ADMIN')) {
    define('ROLE_ADMIN', 1);
}
if (!defined('ROLE_CUSTOMER')) {
    define('ROLE_CUSTOMER', 2);
}

function isAdmin(): bool {
    // Admin if logged in and role is admin
    return isLoggedIn() && isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === ROLE_ADMIN;
}

?>