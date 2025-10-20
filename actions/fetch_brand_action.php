<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

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

echo json_encode($result);
