<?php
// Ensure clean JSON only
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ob_start();

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

try {
    if (!isLoggedIn()) {
        echo json_encode(['status' => 'error', 'message' => 'Not authenticated.']);
        exit;
    }

    if (!isAdmin()) {
        echo json_encode(['status' => 'error', 'message' => 'Forbidden. Admins only.']);
        exit;
    }

    $controller = new brand_controller();
    $user_id = getCurrentUserId();
    $result = $controller->fetch_user_brands_ctr($user_id);

    if (ob_get_length() !== false) { ob_end_clean(); }
    echo json_encode($result);
    exit;

} catch (Throwable $e) {
    if (ob_get_length() !== false) { ob_end_clean(); }
    echo json_encode(['status' => 'error', 'message' => 'Unexpected server error.']);
    error_log('fetch_brand_action error: ' . $e->getMessage());
    exit;
}
