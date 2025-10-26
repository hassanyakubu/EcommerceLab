<?php
// Ensure clean JSON only
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ob_start();

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
        exit;
    }

    if (!isLoggedIn()) {
        echo json_encode(['status' => 'error', 'message' => 'Not authenticated.']);
        exit;
    }

    if (!isAdmin()) {
        echo json_encode(['status' => 'error', 'message' => 'Forbidden. Admins only.']);
        exit;
    }

    $data = [
        'cat_id' => $_POST['cat_id'] ?? null,
        'brand_id' => $_POST['brand_id'] ?? null,
        'title' => $_POST['title'] ?? null,
        'price' => $_POST['price'] ?? null,
        'desc' => $_POST['desc'] ?? null,
        'keywords' => $_POST['keywords'] ?? null,
    ];

    $controller = new product_controller();
    $result = $controller->add_product_ctr($data);

    if (ob_get_length() !== false) { ob_end_clean(); }
    echo json_encode($result);
    exit;

} catch (Throwable $e) {
    if (ob_get_length() !== false) { ob_end_clean(); }
    echo json_encode(['status' => 'error', 'message' => 'Unexpected server error.']);
    error_log('add_product_action error: ' . $e->getMessage());
    exit;
}
