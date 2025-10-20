<?php
// Suppress all output except what we explicitly echo
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any unexpected output
ob_start();

header('Content-Type: application/json');
require_once __DIR__ . '/../controllers/customer_controller.php';

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Only POST requests are allowed.'
    ]);
    exit;
}

// Check if required fields are present
$required_fields = ['full_name', 'email', 'password', 'country', 'city', 'contact_number'];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        echo json_encode([
            'status' => 'error',
            'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.'
        ]);
        exit;
    }
}

// Prepare customer data (user role always set to 2 = customer)
$customer_data = [
    'full_name' => trim($_POST['full_name']),
    'email' => trim($_POST['email']),
    'password' => $_POST['password'],
    'country' => trim($_POST['country']),
    'city' => trim($_POST['city']),
    'contact_number' => trim($_POST['contact_number']),
    'user_role' => 2
];

if (strlen($customer_data['full_name']) < 2) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Full name must be at least 2 characters long.'
    ]);
    exit;
}

if (strlen($customer_data['full_name']) > 100) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Full name must not exceed 100 characters.'
    ]);
    exit;
}

if (strlen($customer_data['email']) > 255) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email must not exceed 255 characters.'
    ]);
    exit;
}

if (strlen($customer_data['password']) < 6) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Password must be at least 6 characters long.'
    ]);
    exit;
}

if (strlen($customer_data['password']) > 255) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Password must not exceed 255 characters.'
    ]);
    exit;
}

if (strlen($customer_data['country']) > 100) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Country must not exceed 100 characters.'
    ]);
    exit;
}

if (strlen($customer_data['city']) > 100) {
    echo json_encode([
        'status' => 'error',
        'message' => 'City must not exceed 100 characters.'
    ]);
    exit;
}

if (strlen($customer_data['contact_number']) > 20) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Contact number must not exceed 20 characters.'
    ]);
    exit;
}

if (!filter_var($customer_data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please enter a valid email address.'
    ]);
    exit;
}

if (!preg_match('/^[0-9+\-\s()]+$/', $customer_data['contact_number'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please enter a valid contact number.'
    ]);
    exit;
}


try {
    // Process registration through controller
    $customer_controller = new customer_controller();
    $result = $customer_controller->register_customer_ctr($customer_data);
    
    // Clean any output buffer and send JSON response
    if (ob_get_level() > 0) { ob_end_clean(); }
    echo json_encode($result);
    exit;
    
} catch (Exception $e) {
    // Handle unexpected errors
    if (ob_get_level() > 0) { ob_end_clean(); }
    echo json_encode([
        'status' => 'error',
        'message' => 'An unexpected error occurred. Please try again.'
    ]);
    error_log("Registration error: " . $e->getMessage());
    exit;
}
