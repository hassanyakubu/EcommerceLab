<?php
require_once __DIR__ . '/../controllers/cart_controller.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, 
        ['options' => ['min_range' => 1]]);
    
    if (!$product_id || !$quantity) {
        throw new Exception('Invalid input');
    }

    session_start();
    $customer_id = $_SESSION['customer_id'] ?? null;
    $ip_address = $customer_id ? null : $_SERVER['REMOTE_ADDR'];

    $cartController = new CartController();
    $result = $cartController->update_cart_quantity($product_id, $quantity, $customer_id, $ip_address);

    if ($result) {
        $cart_items = $cartController->get_cart_items($customer_id, $ip_address);
        $cart_total = $cartController->get_cart_total($customer_id, $ip_address);
        
        $subtotal = 0;
        foreach ($cart_items as $item) {
            if ($item['product_id'] == $product_id) {
                $subtotal = $item['product_price'] * $item['qty'];
                break;
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Cart updated',
            'subtotal' => $subtotal,
            'cart_total' => $cart_total
        ]);
    } else {
        throw new Exception('Failed to update cart');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}