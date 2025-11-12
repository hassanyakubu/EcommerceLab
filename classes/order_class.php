<?php
require_once __DIR__ . '/../settings/db_class.php';

class Order extends db_connection {
    // Create a new order
    public function create_order($customer_id, $invoice_no, $order_status = "Pending") {
        $customer_id = (int)$customer_id;
        $invoice_no = $this->db_conn()->real_escape_string($invoice_no);
        $order_status = $this->db_conn()->real_escape_string($order_status);
        
        $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status) 
                VALUES ('$customer_id', '$invoice_no', CURDATE(), '$order_status')";
        
        if ($this->db_query($sql)) {
            return $this->db_conn()->insert_id;
        }
        return false;
    }

    // Add order details
    public function add_order_details($order_id, $product_id, $qty) {
        $order_id = (int)$order_id;
        $product_id = (int)$product_id;
        $qty = (int)$qty;
        
        // Get product price
        $price_sql = "SELECT product_price FROM products WHERE product_id = '$product_id'";
        $this->db_query($price_sql);
        $product = $this->db_fetch();
        
        if (!$product) {
            return false;
        }
        
        $price = $product['product_price'];
        
        $sql = "INSERT INTO orderdetails (order_id, product_id, qty) 
                VALUES ('$order_id', '$product_id', '$qty')";
        
        if ($this->db_query($sql)) {
            return true;
        }
        return false;
    }

    // Record payment
    public function record_payment($amount, $customer_id, $order_id, $currency = 'GHS') {
        $amount = (float)$amount;
        $customer_id = (int)$customer_id;
        $order_id = (int)$order_id;
        $currency = $this->db_conn()->real_escape_string($currency);
        
        $sql = "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date) 
                VALUES ('$amount', '$customer_id', '$order_id', '$currency', CURDATE())";
        
        return $this->db_query($sql);
    }

    // Get order details
    public function get_order_details($order_id) {
        $order_id = (int)$order_id;
        
        $sql = "SELECT o.*, p.*, od.qty, (p.product_price * od.qty) as subtotal 
                FROM orders o
                JOIN orderdetails od ON o.order_id = od.order_id
                JOIN products p ON od.product_id = p.product_id
                WHERE o.order_id = '$order_id'";
        
        $this->db_query($sql);
        return $this->db_fetch_all();
    }

    // Get customer orders
    public function get_customer_orders($customer_id) {
        $customer_id = (int)$customer_id;
        
        $sql = "SELECT o.*, 
                (SELECT SUM(p.product_price * od.qty) 
                 FROM orderdetails od 
                 JOIN products p ON od.product_id = p.product_id 
                 WHERE od.order_id = o.order_id) as total
                FROM orders o
                WHERE o.customer_id = '$customer_id'
                ORDER BY o.order_date DESC";
        
        $this->db_query($sql);
        return $this->db_fetch_all();
    }

    // Generate invoice number
    public function generate_invoice_no() {
        return 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());
    }

    // Process checkout (complete order)
    public function process_checkout($customer_id, $cart_items) {
        // Start transaction
        $this->db_conn()->begin_transaction();
        
        try {
            $invoice_no = $this->generate_invoice_no();
            $order_id = $this->create_order($customer_id, $invoice_no, 'Paid');
            
            if (!$order_id) {
                throw new Exception("Failed to create order");
            }
            
            $total_amount = 0;
            
            foreach ($cart_items as $item) {
                if (!$this->add_order_details($order_id, $item['p_id'], $item['qty'])) {
                    throw new Exception("Failed to add order details");
                }
                $total_amount += $item['product_price'] * $item['qty'];
            }
            
            if (!$this->record_payment($total_amount, $customer_id, $order_id)) {
                throw new Exception("Failed to record payment");
            }
            
            // Commit transaction
            $this->db_conn()->commit();
            return [
                'success' => true,
                'order_id' => $order_id,
                'invoice_no' => $invoice_no,
                'total_amount' => $total_amount
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db_conn()->rollback();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
?>