<?php
require_once __DIR__ . '/../settings/db_class.php';

class Cart extends db_connection {

    // Add item to cart
    public function add_to_cart($product_id, $ip_add, $customer_id = null, $qty = 1) {
        $qty = (int)$qty;
        $product_id = (int)$product_id;

        if ($customer_id) {
            $existing = $this->db_query("SELECT * FROM cart WHERE p_id = ? AND c_id = ?", [$product_id, $customer_id]);
        } else {
            $existing = $this->db_query("SELECT * FROM cart WHERE p_id = ? AND ip_add = ?", [$product_id, $ip_add]);
        }

        if ($this->db_count() > 0) {
            // Update quantity
            if ($customer_id) {
                return $this->db_query("UPDATE cart SET qty = qty + ? WHERE p_id = ? AND c_id = ?", [$qty, $product_id, $customer_id]);
            } else {
                return $this->db_query("UPDATE cart SET qty = qty + ? WHERE p_id = ? AND ip_add = ?", [$qty, $product_id, $ip_add]);
            }
        } else {
            // Insert new
            return $this->db_query(
                "INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (?, ?, ?, ?)",
                [$product_id, $ip_add, $customer_id, $qty]
            );
        }
    }

    // Remove item from cart
    public function remove_from_cart($product_id, $customer_id = null, $ip_add = null) {
        $product_id = (int)$product_id;
        if ($customer_id) {
            return $this->db_query("DELETE FROM cart WHERE p_id = ? AND c_id = ?", [$product_id, $customer_id]);
        } else {
            return $this->db_query("DELETE FROM cart WHERE p_id = ? AND ip_add = ?", [$product_id, $ip_add]);
        }
    }

    // Update quantity
    public function update_cart_quantity($product_id, $qty, $customer_id = null, $ip_add = null) {
        $product_id = (int)$product_id;
        $qty = (int)$qty;

        if ($customer_id) {
            return $this->db_query("UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?", [$qty, $product_id, $customer_id]);
        } else {
            return $this->db_query("UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ?", [$qty, $product_id, $ip_add]);
        }
    }

    // Get all cart items
    public function get_cart_items($customer_id = null, $ip_add = null) {
        if ($customer_id) {
            $sql = "SELECT p.*, c.qty, p.product_price * c.qty AS subtotal 
                    FROM cart c JOIN products p ON c.p_id = p.product_id
                    WHERE c.c_id = ?";
            $this->db_query($sql, [$customer_id]);
        } else {
            $sql = "SELECT p.*, c.qty, p.product_price * c.qty AS subtotal 
                    FROM cart c JOIN products p ON c.p_id = p.product_id
                    WHERE c.ip_add = ?";
            $this->db_query($sql, [$ip_add]);
        }

        return $this->db_count() > 0 ? $this->db_fetch_all() : [];
    }

    // Get total
    public function get_cart_total($customer_id = null, $ip_add = null) {
        if ($customer_id) {
            $sql = "SELECT SUM(p.product_price * c.qty) AS total
                    FROM cart c JOIN products p ON c.p_id = p.product_id
                    WHERE c.c_id = ?";
            $this->db_query($sql, [$customer_id]);
        } else {
            $sql = "SELECT SUM(p.product_price * c.qty) AS total
                    FROM cart c JOIN products p ON c.p_id = p.product_id
                    WHERE c.ip_add = ?";
            $this->db_query($sql, [$ip_add]);
        }

        $result = $this->db_fetch();
        return $result ? $result['total'] : 0;
    }

    // Count items
    public function count_cart_items($customer_id = null, $ip_add = null) {
        if ($customer_id) {
            $sql = "SELECT SUM(qty) AS count FROM cart WHERE c_id = ?";
            $this->db_query($sql, [$customer_id]);
        } else {
            $sql = "SELECT SUM(qty) AS count FROM cart WHERE ip_add = ?";
            $this->db_query($sql, [$ip_add]);
        }

        $result = $this->db_fetch();
        return $result ? $result['count'] : 0;
    }

    // Empty cart
    public function empty_cart($customer_id = null, $ip_add = null) {
        if ($customer_id) {
            return $this->db_query("DELETE FROM cart WHERE c_id = ?", [$customer_id]);
        } else {
            return $this->db_query("DELETE FROM cart WHERE ip_add = ?", [$ip_add]);
        }
    }

    // Transfer guest cart to logged-in user
    public function transfer_cart($ip_add, $customer_id) {
        // Merge quantities if product exists
        $sql_merge = "UPDATE cart c1
                      JOIN cart c2 ON c1.p_id = c2.p_id
                      SET c1.qty = c1.qty + c2.qty
                      WHERE c1.c_id = ? AND c2.ip_add = ?";
        $this->db_query($sql_merge, [$customer_id, $ip_add]);

        // Delete merged guest items
        $sql_delete = "DELETE c2 FROM cart c2
                       JOIN cart c1 ON c1.p_id = c2.p_id
                       WHERE c1.c_id = ? AND c2.ip_add = ?";
        $this->db_query($sql_delete, [$customer_id, $ip_add]);

        // Transfer remaining items
        return $this->db_query("UPDATE cart SET c_id = ?, ip_add = '' WHERE ip_add = ?", [$customer_id, $ip_add]);
    }
}
?>
