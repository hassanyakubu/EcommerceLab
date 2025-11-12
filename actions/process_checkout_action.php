<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode(['status' => 'error', 'message' => 'Cart is empty']);
        exit;
    }

    // Calculate total
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // Simulate order processing
    $order_id = 'ORD' . rand(1000, 9999); // In real app, insert into DB

    // Clear cart
    $_SESSION['cart'] = [];

    echo json_encode([
        'status' => 'success',
        'order_id' => $order_id,
        'total_amount' => $total_amount
    ]);
    exit;
}
?>
