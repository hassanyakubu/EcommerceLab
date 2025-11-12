<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';

class OrderController {
    private $orderModel;
    private $cartModel;

    public function __construct() {
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
    }

    // Process checkout
    public function process_checkout($customer_id, $cart_items) {
        if (empty($cart_items)) {
            return ['message' => 'Cart is empty'];
        }

        try {
            // Generate invoice/order reference
            $invoice_no = $this->orderModel->generate_order_ref();
            $total_amount = 0;

            // Calculate total
            foreach ($cart_items as $item) {
                $total_amount += $item['product_price'] * $item['qty'];
            }

            // Create order
            $order_id = $this->orderModel->create_order($customer_id, $invoice_no, $total_amount);
            if (!$order_id) {
                return ['message' => 'Failed to create order'];
            }

            // Record payment (simulated for lab)
            $this->orderModel->record_payment($order_id, $total_amount, 'Simulated', 'Success');

            // Update order status
            $this->orderModel->update_order_status($order_id, 'Completed');

            // Save order details
            foreach ($cart_items as $item) {
                $this->orderModel->db_query(
                    "INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)",
                    [$order_id, $item['product_id'], $item['qty']]
                );
            }

            return [
                'success' => true,
                'order_id' => $order_id,
                'invoice_no' => $invoice_no,
                'total_amount' => $total_amount
            ];

        } catch (Throwable $e) {
            error_log('Checkout error: ' . $e->getMessage());
            return ['message' => 'Error processing checkout'];
        }
    }

    // Get order details
    public function get_order($order_id) {
        return $this->orderModel->get_order($order_id);
    }

    // Get all orders for a customer
    public function get_customer_orders($customer_id) {
        return $this->orderModel->get_customer_orders($customer_id);
    }
}
?>
