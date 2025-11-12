<?php
require_once __DIR__ . '/../controllers/cart_controller.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    if (!$product_id) {
        throw new Exception('Invalid product ID');
    }

    session_start();
    $customer_id = $_SESSION['customer_id'] ?? null;
    $ip_address = $customer_id ? null : $_SERVER['REMOTE_ADDR'];

    $cartController = new CartController();
    $result = $cartController->remove_from_cart($product_id, $customer_id, $ip_address);

    if ($result) {
        $cart_count = $cartController->count_cart_items($customer_id, $ip_address);
        $cart_total = $cartController->get_cart_total($customer_id, $ip_address);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Product removed from cart',
            'cart_count' => $cart_count,
            'cart_total' => $cart_total
        ]);
    } else {
        throw new Exception('Failed to remove product from cart');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}