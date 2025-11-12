<?php
require_once __DIR__ . '/../controllers/cart_controller.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    session_start();
    $customer_id = $_SESSION['customer_id'] ?? null;
    $ip_address = $customer_id ? null : $_SERVER['REMOTE_ADDR'];

    $cartController = new CartController();
    $result = $cartController->empty_cart($customer_id, $ip_address);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Cart emptied successfully',
            'cart_count' => 0,
            'cart_total' => 0
        ]);
    } else {
        throw new Exception('Failed to empty cart');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}