<?php
require_once __DIR__ . '/../settings/db_class.php';

class Cart extends db_connection {
    // Add item to cart
    public function add_to_cart($p_id, $ip_add, $c_id = null, $qty = 1) {
        // Sanitize inputs
        $p_id = $this->db_conn()->real_escape_string($p_id);
        $ip_add = $this->db_conn()->real_escape_string($ip_add);
        $qty = (int)$qty;
        $c_id = $c_id ? (int)$c_id : null;
        
        // Check if product already exists in cart
        $check_sql = "SELECT * FROM cart WHERE p_id = '$p_id' AND ";
        $check_sql .= $c_id ? "c_id = '$c_id'" : "ip_add = '$ip_add'";
        
        $this->db_query($check_sql);
        if ($this->db_count() > 0) {
            // Update quantity if product exists
            $update_sql = "UPDATE cart SET qty = qty + $qty WHERE p_id = '$p_id' AND ";
            $update_sql .= $c_id ? "c_id = '$c_id'" : "ip_add = '$ip_add'";
            return $this->db_query($update_sql);
        } else {
            // Add new item to cart
            $c_id_value = $c_id ? "'$c_id'" : "NULL";
            $insert_sql = "INSERT INTO cart (p_id, ip_add, c_id, qty) 
                          VALUES ('$p_id', '$ip_add', $c_id_value, '$qty')";
            return $this->db_query($insert_sql);
        }
    }

    // Remove item from cart
    public function remove_from_cart($p_id, $c_id = null, $ip_add = null) {
        $p_id = $this->db_conn()->real_escape_string($p_id);
        $sql = "DELETE FROM cart WHERE p_id = '$p_id' AND ";
        $sql .= $c_id ? "c_id = '" . (int)$c_id . "'" : "ip_add = '" . $this->db_conn()->real_escape_string($ip_add) . "'";
        return $this->db_query($sql);
    }

    // Update cart item quantity
    public function update_cart_quantity($p_id, $qty, $c_id = null, $ip_add = null) {
        $p_id = $this->db_conn()->real_escape_string($p_id);
        $qty = (int)$qty;
        $sql = "UPDATE cart SET qty = '$qty' WHERE p_id = '$p_id' AND ";
        $sql .= $c_id ? "c_id = '" . (int)$c_id . "'" : "ip_add = '" . $this->db_conn()->real_escape_string($ip_add) . "'";
        return $this->db_query($sql);
    }

    // Get user's cart items
    public function get_cart_items($c_id = null, $ip_add = null) {
        $sql = "SELECT p.*, c.qty, p.product_price * c.qty as subtotal 
                FROM cart c 
                JOIN products p ON c.p_id = p.product_id 
                WHERE ";
        $sql .= $c_id ? "c.c_id = '" . (int)$c_id . "'" : "c.ip_add = '" . $this->db_conn()->real_escape_string($ip_add) . "'";
        
        $this->db_query($sql);
        if ($this->db_count() > 0) {
            return $this->db_fetch_all();
        }
        return [];
    }

    // Empty user's cart
    public function empty_cart($c_id = null, $ip_add = null) {
        $sql = "DELETE FROM cart WHERE ";
        $sql .= $c_id ? "c_id = '" . (int)$c_id . "'" : "ip_add = '" . $this->db_conn()->real_escape_string($ip_add) . "'";
        return $this->db_query($sql);
    }

    // Get cart total
    public function get_cart_total($c_id = null, $ip_add = null) {
        $sql = "SELECT SUM(p.product_price * c.qty) as total 
                FROM cart c 
                JOIN products p ON c.p_id = p.product_id 
                WHERE ";
        $sql .= $c_id ? "c.c_id = '" . (int)$c_id . "'" : "c.ip_add = '" . $this->db_conn()->real_escape_string($ip_add) . "'";
        
        $this->db_query($sql);
        $result = $this->db_fetch();
        return $result ? $result['total'] : 0;
    }

    // Count items in cart
    public function count_cart_items($c_id = null, $ip_add = null) {
        $sql = "SELECT SUM(qty) as count FROM cart WHERE ";
        $sql .= $c_id ? "c_id = '" . (int)$c_id . "'" : "ip_add = '" . $this->db_conn()->real_escape_string($ip_add) . "'";
        
        $this->db_query($sql);
        $result = $this->db_fetch();
        return $result ? $result['count'] : 0;
    }

    // Transfer cart from guest to user after login
    public function transfer_cart($ip_add, $c_id) {
        // Check if user already has items in cart
        $check_sql = "SELECT * FROM cart WHERE c_id = '" . (int)$c_id . "'";
        $this->db_query($check_sql);
        
        if ($this->db_count() > 0) {
            // Merge quantities if same product exists in both carts
            $merge_sql = "UPDATE cart c1
                         INNER JOIN cart c2 ON c1.p_id = c2.p_id 
                         SET c1.qty = c1.qty + c2.qty
                         WHERE c1.c_id = '" . (int)$c_id . "' 
                         AND c2.ip_add = '" . $this->db_conn()->real_escape_string($ip_add) . "'";
            $this->db_query($merge_sql);
            
            // Delete the merged guest cart items
            $delete_merged = "DELETE c2 FROM cart c2 
                            INNER JOIN cart c1 ON c1.p_id = c2.p_id 
                            WHERE c1.c_id = '" . (int)$c_id . "' 
                            AND c2.ip_add = '" . $this->db_conn()->real_escape_string($ip_add) . "'";
            $this->db_query($delete_merged);
        }
        
        // Transfer remaining guest cart items to user
        $transfer_sql = "UPDATE cart SET c_id = '" . (int)$c_id . "', ip_add = '' 
                        WHERE ip_add = '" . $this->db_conn()->real_escape_string($ip_add) . "'";
        return $this->db_query($transfer_sql);
    }
}
?>