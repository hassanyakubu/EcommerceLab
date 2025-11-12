<?php
require_once __DIR__ . '/../controllers/cart_controller.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, 
        ['options' => ['default' => 1, 'min_range' => 1]]);
    
    if (!$product_id) {
        throw new Exception('Invalid product ID');
    }

    session_start();
    $customer_id = $_SESSION['customer_id'] ?? null;
    $ip_address = $_SERVER['REMOTE_ADDR'];

    $cartController = new CartController();
    $result = $cartController->add_to_cart($product_id, $ip_address, $customer_id, $quantity);

    if ($result) {
        $cart_count = $cartController->count_cart_items($customer_id, $ip_address);
        echo json_encode([
            'status' => 'success',
            'message' => 'Product added to cart',
            'cart_count' => $cart_count
        ]);
    } else {
        throw new Exception('Failed to add product to cart');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}