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

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
if ($name === '') {
    echo json_encode(['status' => 'error', 'message' => 'Category name is required.']);
    exit;
}

$controller = new category_controller();
$result = $controller->add_category_ctr($name);

echo json_encode($result);
