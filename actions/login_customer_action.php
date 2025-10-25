<?php
// Suppress unintended output; return JSON only
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

ob_start();
header('Content-Type: application/json');
session_start();

require("../controllers/customer_controller.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Only POST is allowed.'
    ]);
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (!$email || !$password) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email and password are required.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please enter a valid email address.'
    ]);
    exit;
}

try {
    $controller = new customer_controller();
    $result = $controller->validate_login_ctr($email, $password);

    if ($result['status'] === 'success' && isset($result['customer'])) {
        $cust = $result['customer'];
        // Regenerate session ID to prevent fixation
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        // Set session variables
        $_SESSION['customer_id'] = $cust['customer_id'];
        $_SESSION['customer_name'] = $cust['customer_name'];
        $_SESSION['customer_email'] = $cust['customer_email'];
        $_SESSION['user_role'] = isset($cust['user_role']) ? $cust['user_role'] : 2; // default customer
        $_SESSION['logged_in'] = true;

        ob_clean();
        echo json_encode([
            'status' => 'success',
            'message' => 'Login successful.',
            'redirect' => '../index.php'
        ]);
        exit;
    }

    ob_clean();
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email or password.'
    ]);
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'status' => 'error',
        'message' => 'An unexpected error occurred. Please try again.'
    ]);
    error_log('Login error: ' . $e->getMessage());
}
