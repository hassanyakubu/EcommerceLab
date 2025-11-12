<?php
require_once __DIR__ . '/../settings/db_class.php';

class Order extends db_connection {

    // Create new order
    public function create_order($customer_id, $invoice_no, $total_amount) {
        $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status) 
                VALUES (?, ?, NOW(), 'Pending')";
        $this->db_query($sql, [$customer_id, $invoice_no]);
        return $this->db_conn()->insert_id;
    }

    // Record payment
    public function record_payment($order_id, $amount, $method = 'Simulated', $status = 'Success') {
        $sql = "INSERT INTO payment (order_id, customer_id, amt, currency, payment_date) 
                VALUES (?, (SELECT customer_id FROM orders WHERE order_id = ?), ?, 'USD', NOW())";
        return $this->db_query($sql, [$order_id, $order_id, $amount]);
    }

    // Update order status
    public function update_order_status($order_id, $status) {
        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        return $this->db_query($sql, [$status, $order_id]);
    }

    // Get a single order
    public function get_order($order_id) {
        $sql = "SELECT * FROM orders WHERE order_id = ?";
        $this->db_query($sql, [$order_id]);
        return $this->db_fetch_one();
    }

    // Get all orders for a customer
    public function get_customer_orders($customer_id) {
        $sql = "SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC";
        $this->db_query($sql, [$customer_id]);
        return $this->db_fetch_all();
    }

    // Generate unique order/invoice reference
    public function generate_order_ref() {
        return 'ORD' . date('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 5));
    }
}
?>
