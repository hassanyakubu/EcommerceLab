<?php
require_once __DIR__ . '/../settings/db_class.php';

class brand_class extends db_connection
{
    public function add_brand($name, $cat_id, $created_by)
    {
        $db = $this->db_conn();
        $name = mysqli_real_escape_string($db, trim($name ?? ''));
        $cat_id = (int)$cat_id;
        $created_by = (int)$created_by;
        if ($name === '' || $cat_id <= 0 || $created_by <= 0) { return false; }

        // Uniqueness per user per category
        $exists = $this->db_fetch_one(
            "SELECT brand_id FROM brands WHERE LOWER(brand_name)=LOWER('$name') AND cat_id='$cat_id' AND created_by='$created_by'"
        );
        if ($exists) { return false; }

        $sql = "INSERT INTO brands (brand_name, cat_id, created_by) VALUES ('$name', '$cat_id', '$created_by')";
        return $this->db_query($sql);
    }

    public function get_brands_by_user_grouped($user_id)
    {
        $user_id = (int)$user_id;
        if ($user_id <= 0) { return []; }
        $sql = "SELECT b.brand_id, b.brand_name, b.cat_id, c.cat_name
                FROM brands b
                JOIN categories c ON c.cat_id = b.cat_id
                WHERE b.created_by = '$user_id'
                ORDER BY c.cat_name ASC, b.brand_name ASC";
        return $this->db_fetch_all($sql);
    }

    public function update_brand($brand_id, $new_name, $user_id)
    {
        $db = $this->db_conn();
        $brand_id = (int)$brand_id;
        $user_id = (int)$user_id;
        $new_name = mysqli_real_escape_string($db, trim($new_name ?? ''));
        if ($brand_id <= 0 || $user_id <= 0 || $new_name === '') { return false; }

        // Ensure ownership and check uniqueness in same (cat, user)
        $row = $this->db_fetch_one("SELECT cat_id FROM brands WHERE brand_id='$brand_id' AND created_by='$user_id'");
        if (!$row || !isset($row['cat_id'])) { return false; }
        $cat_id = (int)$row['cat_id'];

        $dupe = $this->db_fetch_one(
            "SELECT brand_id FROM brands 
             WHERE LOWER(brand_name)=LOWER('$new_name') AND cat_id='$cat_id' AND created_by='$user_id' AND brand_id <> '$brand_id'"
        );
        if ($dupe) { return false; }

        $sql = "UPDATE brands SET brand_name='$new_name' WHERE brand_id='$brand_id' AND created_by='$user_id'";
        return $this->db_query($sql);
    }

    public function delete_brand($brand_id, $user_id)
    {
        $brand_id = (int)$brand_id;
        $user_id = (int)$user_id;
        if ($brand_id <= 0 || $user_id <= 0) { return false; }
        $sql = "DELETE FROM brands WHERE brand_id='$brand_id' AND created_by='$user_id'";
        return $this->db_query($sql);
    }

    public function get_brand($brand_id, $user_id)
    {
        $brand_id = (int)$brand_id;
        $user_id = (int)$user_id;
        if ($brand_id <= 0 || $user_id <= 0) { return false; }
        $sql = "SELECT brand_id, brand_name, cat_id, created_by FROM brands WHERE brand_id='$brand_id' AND created_by='$user_id'";
        return $this->db_fetch_one($sql);
    }
}
