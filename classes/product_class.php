<?php
require_once __DIR__ . '/../settings/db_class.php';

class product_class extends db_connection
{
    public function add_product($cat_id, $brand_id, $title, $price, $desc = null, $image = null, $keywords = null)
    {
        $db = $this->db_conn();
        $cat_id = (int)$cat_id;
        $brand_id = (int)$brand_id;
        $title = mysqli_real_escape_string($db, trim($title ?? ''));
        $price = (float)$price;
        $desc = isset($desc) ? mysqli_real_escape_string($db, trim($desc)) : null;
        $image = isset($image) ? mysqli_real_escape_string($db, trim($image)) : null;
        $keywords = isset($keywords) ? mysqli_real_escape_string($db, trim($keywords)) : null;
        if ($cat_id <= 0 || $brand_id <= 0 || $title === '' || $price < 0) { return [false, null]; }

        // Optional: ensure brand belongs to category (if brands.cat_id exists)
        $chk = $this->db_fetch_one("SELECT brand_id FROM brands WHERE brand_id='$brand_id' AND cat_id='$cat_id'");
        if (!$chk) { return [false, null]; }

        $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords)
                VALUES ('$cat_id', '$brand_id', '$title', '$price', " .
                ($desc !== null ? "'$desc'" : "NULL") . ", " .
                ($image !== null ? "'$image'" : "NULL") . ", " .
                ($keywords !== null ? "'$keywords'" : "NULL") . ")";
        $ok = $this->db_query($sql);
        if (!$ok) { return [false, null]; }
        $row = $this->db_fetch_one("SELECT LAST_INSERT_ID() AS id");
        $newId = $row && isset($row['id']) ? (int)$row['id'] : null;
        return [$ok, $newId];
    }

    public function update_product($product_id, $cat_id, $brand_id, $title, $price, $desc = null, $keywords = null)
    {
        $db = $this->db_conn();
        $product_id = (int)$product_id;
        $cat_id = (int)$cat_id;
        $brand_id = (int)$brand_id;
        $title = mysqli_real_escape_string($db, trim($title ?? ''));
        $price = (float)$price;
        $desc = isset($desc) ? mysqli_real_escape_string($db, trim($desc)) : null;
        $keywords = isset($keywords) ? mysqli_real_escape_string($db, trim($keywords)) : null;
        if ($product_id <= 0 || $cat_id <= 0 || $brand_id <= 0 || $title === '' || $price < 0) { return false; }

        $chk = $this->db_fetch_one("SELECT brand_id FROM brands WHERE brand_id='$brand_id' AND cat_id='$cat_id'");
        if (!$chk) { return false; }

        $set = [
            "product_cat='$cat_id'",
            "product_brand='$brand_id'",
            "product_title='$title'",
            "product_price='$price'",
            ($desc !== null ? "product_desc='$desc'" : "product_desc=NULL"),
            ($keywords !== null ? "product_keywords='$keywords'" : "product_keywords=NULL"),
        ];
        $sql = "UPDATE products SET " . implode(',', $set) . " WHERE product_id='$product_id'";
        return $this->db_query($sql);
    }

    public function set_product_image($product_id, $image_path)
    {
        $db = $this->db_conn();
        $product_id = (int)$product_id;
        $image = mysqli_real_escape_string($db, trim($image_path ?? ''));
        if ($product_id <= 0 || $image === '') { return false; }
        $sql = "UPDATE products SET product_image='$image' WHERE product_id='$product_id'";
        return $this->db_query($sql);
    }

    public function get_product($product_id)
    {
        $product_id = (int)$product_id;
        if ($product_id <= 0) { return false; }
        $sql = "SELECT p.*, c.cat_name, b.brand_name
                FROM products p
                JOIN categories c ON c.cat_id = p.product_cat
                JOIN brands b ON b.brand_id = p.product_brand
                WHERE p.product_id='$product_id'";
        return $this->db_fetch_one($sql);
    }

    public function get_all_products_joined()
    {
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_desc, p.product_image, p.product_keywords,
                       c.cat_id, c.cat_name, b.brand_id, b.brand_name
                FROM products p
                JOIN categories c ON c.cat_id = p.product_cat
                JOIN brands b ON b.brand_id = p.product_brand
                ORDER BY c.cat_name ASC, b.brand_name ASC, p.product_title ASC";
        return $this->db_fetch_all($sql);
    }
}
