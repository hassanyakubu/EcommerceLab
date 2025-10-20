<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

if (!isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated.']);
    exit;
}

if (!isAdmin()) {
    echo json_encode(['status' => 'error', 'message' => 'Forbidden. Admins only.']);
    exit;
}

$controller = new product_controller();
$result = $controller->fetch_products_ctr();

echo json_encode($result);
