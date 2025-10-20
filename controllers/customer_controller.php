<?php
require("../classes/customer_class.php");

class customer_controller
{
    private $customer_class;
    
    public function __construct()
    {
        $this->customer_class = new customer_class();
    }
    
    public function register_customer_ctr($kwargs)
    {
        // Validate required fields
        $required_fields = ['full_name', 'email', 'password', 'country', 'city', 'contact_number'];
        
        foreach ($required_fields as $field) {
            if (empty($kwargs[$field])) {
                return [
                    'status' => 'error',
                    'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.'
                ];
            }
        }
        
        // Validate email format
        if (!filter_var($kwargs['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => 'error',
                'message' => 'Please enter a valid email address.'
            ];
        }
        
        // Validate password strength
        if (strlen($kwargs['password']) < 6) {
            return [
                'status' => 'error',
                'message' => 'Password must be at least 6 characters long.'
            ];
        }
        
        // Validate contact number format
        if (!preg_match('/^[0-9+\-\s()]+$/', $kwargs['contact_number'])) {
            return [
                'status' => 'error',
                'message' => 'Please enter a valid contact number.'
            ];
        }
        
        // User role is always set to 2 (customer) - no user input allowed
        $kwargs['user_role'] = 2;
        
        // Check if email is already registered
        if (!$this->customer_class->is_email_available($kwargs['email'])) {
            return [
                'status' => 'error',
                'message' => 'This email is already registered. Please use a different email.'
            ];
        }
        
        $result = $this->customer_class->add_customer($kwargs);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Registration successful! You can now log in.'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Registration failed. Please try again.'
            ];
        }
    }
    
    public function get_customer_by_email_ctr($email)
    {
        if (empty($email)) {
            return false;
        }
        
        return $this->customer_class->get_customer_by_email($email);
    }
    
    public function get_customer_by_id_ctr($customer_id)
    {
        if (empty($customer_id)) {
            return false;
        }
        
        return $this->customer_class->get_customer_by_id($customer_id);
    }
    
    public function edit_customer_ctr($customer_id, $customer_data)
    {
        if (empty($customer_id)) {
            return [
                'status' => 'error',
                'message' => 'Customer ID is required.'
            ];
        }
        
        if (isset($customer_data['email']) && !filter_var($customer_data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => 'error',
                'message' => 'Please enter a valid email address.'
            ];
        }
        
        $result = $this->customer_class->edit_customer($customer_id, $customer_data);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Customer information updated successfully.'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to update customer information. Email might already be in use.'
            ];
        }
    }
    
    public function delete_customer_ctr($customer_id)
    {
        if (empty($customer_id)) {
            return [
                'status' => 'error',
                'message' => 'Customer ID is required.'
            ];
        }
        
        $result = $this->customer_class->delete_customer($customer_id);
        
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'Customer deleted successfully.'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to delete customer.'
            ];
        }
    }
    
    public function validate_login_ctr($email, $password)
    {
        if (empty($email) || empty($password)) {
            return [
                'status' => 'error',
                'message' => 'Email and password are required.'
            ];
        }
        
        $customer = $this->customer_class->validate_login($email, $password);
        
        if ($customer) {
            return [
                'status' => 'success',
                'message' => 'Login successful.',
                'customer' => $customer
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Invalid email or password.'
            ];
        }
    }
    
    public function get_all_customers_ctr()
    {
        return $this->customer_class->get_all_customers();
    }
}
?>
