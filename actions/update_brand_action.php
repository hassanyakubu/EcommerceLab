<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

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

$brand_id = isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$user_id = getCurrentUserId();

$controller = new brand_controller();
$result = $controller->update_brand_ctr($brand_id, $name, $user_id);

echo json_encode($result);
