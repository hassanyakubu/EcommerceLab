<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

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
    'product_id' => $_POST['product_id'] ?? null,
    'cat_id' => $_POST['cat_id'] ?? null,
    'brand_id' => $_POST['brand_id'] ?? null,
    'title' => $_POST['title'] ?? null,
    'price' => $_POST['price'] ?? null,
    'desc' => $_POST['desc'] ?? null,
    'keywords' => $_POST['keywords'] ?? null,
];

$controller = new product_controller();
$result = $controller->update_product_ctr($data);

echo json_encode($result);
