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
$new_name = isset($_POST['name']) ? trim($_POST['name']) : '';
if ($cat_id <= 0 || $new_name === '') {
    echo json_encode(['status' => 'error', 'message' => 'Category ID and new name are required.']);
    exit;
}

$controller = new category_controller();
$result = $controller->update_category_ctr($cat_id, $new_name);

echo json_encode($result);
