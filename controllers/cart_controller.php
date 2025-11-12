<?php
require_once __DIR__ . '/../models/Cart.php';

class CartController {
    private $cartModel;

    public function __construct() {
        $this->cartModel = new Cart();
    }

    // Add item to cart
    public function add_to_cart($product_id, $ip_add, $customer_id = null, $qty = 1) {
        return $this->cartModel->add_to_cart($product_id, $ip_add, $customer_id, $qty);
    }

    // Remove item from cart
    public function remove_from_cart($product_id, $customer_id = null, $ip_add = null) {
        return $this->cartModel->remove_from_cart($product_id, $customer_id, $ip_add);
    }

    // Update cart quantity
    public function update_cart_quantity($product_id, $qty, $customer_id = null, $ip_add = null) {
        return $this->cartModel->update_cart_quantity($product_id, $qty, $customer_id, $ip_add);
    }

    // Get all cart items
    public function get_cart_items($customer_id = null, $ip_add = null) {
        return $this->cartModel->get_cart_items($customer_id, $ip_add);
    }

    // Get total amount
    public function get_cart_total($customer_id = null, $ip_add = null) {
        return $this->cartModel->get_cart_total($customer_id, $ip_add);
    }

    // Count total items
    public function count_cart_items($customer_id = null, $ip_add = null) {
        return $this->cartModel->count_cart_items($customer_id, $ip_add);
    }

    // Empty cart
    public function empty_cart($customer_id = null, $ip_add = null) {
        return $this->cartModel->empty_cart($customer_id, $ip_add);
    }

    // Transfer guest cart to logged-in user
    public function transfer_cart($ip_add, $customer_id) {
        return $this->cartModel->transfer_cart($ip_add, $customer_id);
    }
}
?>
