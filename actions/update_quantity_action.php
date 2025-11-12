<?php
require_once __DIR__ . '/../settings/db_class.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $quantity = $_POST['quantity'] ?? null;

    if (!$product_id || !$quantity || $quantity < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    session_start();
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    // Update quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Product not in cart']);
        exit;
    }

    // Recalculate cart total
    $cart_total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cart_total += $item['price'] * $item['quantity'];
    }

    echo json_encode([
        'status' => 'success',
        'cart_total' => $cart_total
    ]);
    exit;
}
?>
