<?php
session_start();
require_once __DIR__ . '/../controllers/cart_controller.php';
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        exit;
    }

    $p_id = isset($_POST['p_id']) ? (int)$_POST['p_id'] : 0;
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

    if ($p_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
        exit;
    }

    $cartController = new CartController();
    $user_id = $_SESSION['customer_id'] ?? null;
    $ip_add = $_SERVER['REMOTE_ADDR'];

    $cartController->add_to_cart($p_id, $ip_add, $user_id, $qty);

    echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);

} catch (Throwable $e) {
    error_log('Add to cart error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Failed to add item']);
}
?>
