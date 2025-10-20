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

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;
$user_id = getCurrentUserId();

$controller = new brand_controller();
$result = $controller->add_brand_ctr($name, $cat_id, $user_id);

echo json_encode($result);
