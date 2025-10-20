<?php
require("../settings/db_class.php");

class customer_class extends db_connection
{
    public function add_customer($customer_data)
    {
        $db = $this->db_conn();
        
        // Sanitize input data to prevent SQL injection
        $customer_name = mysqli_real_escape_string($db, $customer_data['full_name']);
        $customer_email = mysqli_real_escape_string($db, $customer_data['email']);
        $customer_pass = mysqli_real_escape_string($db, $customer_data['password']);
        $customer_country = mysqli_real_escape_string($db, $customer_data['country']);
        $customer_city = mysqli_real_escape_string($db, $customer_data['city']);
        $customer_contact = mysqli_real_escape_string($db, $customer_data['contact_number']);
        $user_role = mysqli_real_escape_string($db, $customer_data['user_role']);
        
        // Check if email already exists
        $check_email_sql = "SELECT * FROM customer WHERE customer_email = '$customer_email'";
        $email_exists = $this->db_fetch_one($check_email_sql);
        
        if ($email_exists) {
            return false;
        }
        
        // Hash password for security
        $hashed_password = password_hash($customer_pass, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
                VALUES ('$customer_name', '$customer_email', '$hashed_password', '$customer_country', '$customer_city', '$customer_contact', '$user_role')";
        
        return $this->db_query($sql);
    }
    
    public function get_customer_by_email($email)
    {
        $db = $this->db_conn();
        $email = mysqli_real_escape_string($db, $email);
        
        $sql = "SELECT * FROM customer WHERE customer_email = '$email'";
        return $this->db_fetch_one($sql);
    }
    
    // Fetch a customer by primary key (ID).
    public function get_customer_by_id($customer_id)
    {
        $db = $this->db_conn();
        $customer_id = mysqli_real_escape_string($db, $customer_id);
        
        $sql = "SELECT * FROM customer WHERE customer_id = '$customer_id'";
        return $this->db_fetch_one($sql);
    }
    
    public function get_all_customers()
    {
        $sql = "SELECT customer_id, customer_name, customer_email, customer_country, customer_city, customer_contact, user_role FROM customer";
        return $this->db_fetch_all($sql);
    }
    
    public function edit_customer($customer_id, $customer_data)
    {
        $db = $this->db_conn();
        
        $full_name = mysqli_real_escape_string($db, $customer_data['full_name']);
        $email = mysqli_real_escape_string($db, $customer_data['email']);
        $country = mysqli_real_escape_string($db, $customer_data['country']);
        $city = mysqli_real_escape_string($db, $customer_data['city']);
        $contact_number = mysqli_real_escape_string($db, $customer_data['contact_number']);
        $user_role = mysqli_real_escape_string($db, $customer_data['user_role']);
        $check_email_sql = "SELECT * FROM customer WHERE customer_email = '$email' AND customer_id != '$customer_id'";
        $email_exists = $this->db_fetch_one($check_email_sql);
        
        if ($email_exists) {
            return false;
        }
        
        $sql = "UPDATE customer SET 
                customer_name = '$full_name',
                customer_email = '$email',
                customer_country = '$country',
                customer_city = '$city',
                customer_contact = '$contact_number',
                user_role = '$user_role'
                WHERE customer_id = '$customer_id'";
        
        return $this->db_query($sql);
    }
    
    // Update password by hashing the new value.
    public function update_customer_password($customer_id, $new_password)
    {
        $db = $this->db_conn();
        $customer_id = mysqli_real_escape_string($db, $customer_id);
        $new_password = mysqli_real_escape_string($db, $new_password);
        
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE customer SET customer_pass = '$hashed_password' WHERE customer_id = '$customer_id'";
        return $this->db_query($sql);
    }
    
    // Permanently delete a customer by ID.
    public function delete_customer($customer_id)
    {
        $db = $this->db_conn();
        $customer_id = mysqli_real_escape_string($db, $customer_id);
        
        $sql = "DELETE FROM customer WHERE customer_id = '$customer_id'";
        return $this->db_query($sql);
    }
    
    public function validate_login($email, $password)
    {
        $customer = $this->get_customer_by_email($email);
        
        // Verify password and remove password from returned data
        if ($customer && password_verify($password, $customer['customer_pass'])) {
            unset($customer['customer_pass']);
            return $customer;
        }
        
        return false;
    }
    
    // Check if an email is not in use (true means available).
    public function is_email_available($email)
    {
        $db = $this->db_conn();
        $email = mysqli_real_escape_string($db, $email);
        
        $sql = "SELECT customer_id FROM customer WHERE customer_email = '$email'";
        $result = $this->db_query($sql);
        
        if (!$result) {
            return false; // Database error
        }
        
        return $this->db_count() === 0; // Email is available if count is 0
    }
}
?>
