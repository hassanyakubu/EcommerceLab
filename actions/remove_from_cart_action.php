<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    if (!$product_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
        exit;
    }

    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Product not in cart']);
        exit;
    }

    // Recalculate cart total and count
    $cart_total = 0;
    $cart_count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cart_total += $item['price'] * $item['quantity'];
        $cart_count += $item['quantity'];
    }

    echo json_encode([
        'status' => 'success',
        'cart_total' => $cart_total,
        'cart_count' => $cart_count
    ]);
    exit;
}
?>
