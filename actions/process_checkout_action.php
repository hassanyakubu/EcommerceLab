<?php
// Ensure clean JSON only
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ob_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../controllers/order_controller.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if (ob_get_length() !== false) { ob_end_clean(); }
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        exit;
    }

    if (!isset($_SESSION['customer_id'])) {
        if (ob_get_length() !== false) { ob_end_clean(); }
        echo json_encode(['status' => 'error', 'message' => 'You must be logged in to checkout']);
        exit;
    }

    $customer_id = $_SESSION['customer_id'];

    // Get cart items
    $cartController = new cart_controller();
    $cart_items = $cartController->get_cart_items($customer_id);

    if (empty($cart_items)) {
        if (ob_get_length() !== false) { ob_end_clean(); }
        echo json_encode(['status' => 'error', 'message' => 'Your cart is empty']);
        exit;
    }

    // Process checkout
    $orderController = new order_controller();
    $result = $orderController->process_checkout($customer_id, $cart_items);

    if (!empty($result['success'])) {
        // Empty the cart after successful checkout
        $cartController->empty_cart($customer_id);

        if (ob_get_length() !== false) { ob_end_clean(); }
        echo json_encode([
            'status' => 'success',
            'message' => 'Order placed successfully',
            'order_id' => $result['order_id'] ?? null,
            'invoice_no' => $result['invoice_no'] ?? null,
            'total_amount' => $result['total_amount'] ?? null
        ]);
        exit;
    }

    $message = isset($result['message']) ? $result['message'] : 'Failed to process checkout';
    if (ob_get_length() !== false) { ob_end_clean(); }
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit;

} catch (Throwable $e) {
    if (ob_get_length() !== false) { ob_end_clean(); }
    echo json_encode(['status' => 'error', 'message' => 'Unexpected server error.']);
    error_log('Checkout error: ' . $e->getMessage());
    exit;
}