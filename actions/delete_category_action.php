<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
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

$cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;
if ($cat_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Category ID is required.']);
    exit;
}

$controller = new category_controller();
$result = $controller->delete_category_ctr($cat_id);

echo json_encode($result);
