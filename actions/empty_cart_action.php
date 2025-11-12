<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['cart'] = [];

    echo json_encode([
        'status' => 'success',
        'cart_count' => 0,
        'cart_total' => 0
    ]);
    exit;
}
?>
