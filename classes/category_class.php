<?php
require_once __DIR__ . '/../settings/db_class.php';

class category_class extends db_connection
{
    // Create a category. Enforces unique name globally.
    public function add_category($name)
    {
        $db = $this->db_conn();
        $name = mysqli_real_escape_string($db, trim($name));
        if ($name === '') { return false; }

        // Enforce global unique category name
        $check = "SELECT cat_id FROM categories WHERE LOWER(cat_name) = LOWER('$name')";
        $exists = $this->db_fetch_one($check);
        if ($exists) { return false; }

        $sql = "INSERT INTO categories (cat_name) VALUES ('$name')";
        return $this->db_query($sql);
    }

    // Get all categories
    public function get_all_categories()
    {
        $sql = "SELECT cat_id, cat_name FROM categories ORDER BY cat_name ASC";
        return $this->db_fetch_all($sql);
    }

    // Update a category name; enforce global uniqueness
    public function update_category($cat_id, $new_name)
    {
        $db = $this->db_conn();
        $cat_id = (int)$cat_id;
        $new_name = mysqli_real_escape_string($db, trim($new_name));
        if ($new_name === '' || $cat_id <= 0) { return false; }

        // Check uniqueness excluding this category
        $dupe = $this->db_fetch_one("SELECT cat_id FROM categories WHERE LOWER(cat_name) = LOWER('$new_name') AND cat_id <> '$cat_id'");
        if ($dupe) { return false; }

        $sql = "UPDATE categories SET cat_name = '$new_name' WHERE cat_id = '$cat_id'";
        return $this->db_query($sql);
    }

    // Delete a category by id
    public function delete_category($cat_id)
    {
        $cat_id = (int)$cat_id;
        if ($cat_id <= 0) { return false; }
        $sql = "DELETE FROM categories WHERE cat_id = '$cat_id'";
        return $this->db_query($sql);
    }

    // Get a single category by id
    public function get_category($cat_id)
    {
        $cat_id = (int)$cat_id;
        if ($cat_id <= 0) { return false; }
        $sql = "SELECT cat_id, cat_name FROM categories WHERE cat_id = '$cat_id'";
        return $this->db_fetch_one($sql);
    }
}
