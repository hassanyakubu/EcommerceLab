<?php
require_once __DIR__ . '/../classes/cart_class.php';

class CartController {
    private $cart;
    
    public function __construct() {
        $this->cart = new Cart();
    }
    
    public function add_to_cart($p_id, $ip_add, $c_id = null, $qty = 1) {
        return $this->cart->add_to_cart($p_id, $ip_add, $c_id, $qty);
    }
    
    public function remove_from_cart($p_id, $c_id = null, $ip_add = null) {
        return $this->cart->remove_from_cart($p_id, $c_id, $ip_add);
    }
    
    public function update_cart_quantity($p_id, $qty, $c_id = null, $ip_add = null) {
        return $this->cart->update_cart_quantity($p_id, $qty, $c_id, $ip_add);
    }
    
    public function get_cart_items($c_id = null, $ip_add = null) {
        return $this->cart->get_cart_items($c_id, $ip_add);
    }
    
    public function empty_cart($c_id = null, $ip_add = null) {
        return $this->cart->empty_cart($c_id, $ip_add);
    }
    
    public function get_cart_total($c_id = null, $ip_add = null) {
        return $this->cart->get_cart_total($c_id, $ip_add);
    }
    
    public function count_cart_items($c_id = null, $ip_add = null) {
        return $this->cart->count_cart_items($c_id, $ip_add);
    }
    
    public function transfer_cart($ip_add, $c_id) {
        return $this->cart->transfer_cart($ip_add, $c_id);
    }
}