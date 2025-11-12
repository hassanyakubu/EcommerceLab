<?php
require_once __DIR__ . '/../classes/order_class.php';

class OrderController {
    private $order;
    
    public function __construct() {
        $this->order = new Order();
    }
    
    public function create_order($customer_id, $invoice_no, $order_status = "Pending") {
        return $this->order->create_order($customer_id, $invoice_no, $order_status);
    }
    
    public function add_order_details($order_id, $product_id, $qty) {
        return $this->order->add_order_details($order_id, $product_id, $qty);
    }
    
    public function record_payment($amount, $customer_id, $order_id, $currency = 'GHS') {
        return $this->order->record_payment($amount, $customer_id, $order_id, $currency);
    }
    
    public function get_order_details($order_id) {
        return $this->order->get_order_details($order_id);
    }
    
    public function get_customer_orders($customer_id) {
        return $this->order->get_customer_orders($customer_id);
    }
    
    public function generate_invoice_no() {
        return $this->order->generate_invoice_no();
    }
    
    public function process_checkout($customer_id, $cart_items) {
        return $this->order->process_checkout($customer_id, $cart_items);
    }
}