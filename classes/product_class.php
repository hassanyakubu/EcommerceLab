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
        // Run INSERT on the same connection and get insert id reliably
        if (!mysqli_query($db, $sql)) { return [false, null]; }
        $newId = mysqli_insert_id($db);
        return [true, $newId > 0 ? $newId : null];
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

    // Storefront helpers
    public function view_all_products($limit = 10, $offset = 0)
    {
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_desc, p.product_image, p.product_keywords,
                       c.cat_id, c.cat_name, b.brand_id, b.brand_name
                FROM products p
                JOIN categories c ON c.cat_id = p.product_cat
                JOIN brands b ON b.brand_id = p.product_brand
                ORDER BY p.product_id DESC
                LIMIT $limit OFFSET $offset";
        return $this->db_fetch_all($sql);
    }

    public function count_all_products()
    {
        $row = $this->db_fetch_one("SELECT COUNT(*) AS cnt FROM products");
        return $row ? (int)$row['cnt'] : 0;
    }

    public function search_products($query, $limit = 10, $offset = 0)
    {
        $db = $this->db_conn();
        $q = mysqli_real_escape_string($db, trim((string)$query));
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        if ($q === '') { return []; }
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_desc, p.product_image, p.product_keywords,
                       c.cat_id, c.cat_name, b.brand_id, b.brand_name
                FROM products p
                JOIN categories c ON c.cat_id = p.product_cat
                JOIN brands b ON b.brand_id = p.product_brand
                WHERE p.product_title LIKE '%$q%' OR p.product_keywords LIKE '%$q%'
                ORDER BY p.product_title ASC
                LIMIT $limit OFFSET $offset";
        return $this->db_fetch_all($sql);
    }

    public function count_search_products($query)
    {
        $db = $this->db_conn();
        $q = mysqli_real_escape_string($db, trim((string)$query));
        if ($q === '') { return 0; }
        $row = $this->db_fetch_one("SELECT COUNT(*) AS cnt FROM products WHERE product_title LIKE '%$q%' OR product_keywords LIKE '%$q%'");
        return $row ? (int)$row['cnt'] : 0;
    }

    public function filter_products_by_category($cat_id, $limit = 10, $offset = 0)
    {
        $cat_id = (int)$cat_id;
        if ($cat_id <= 0) { return []; }
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_desc, p.product_image, p.product_keywords,
                       c.cat_id, c.cat_name, b.brand_id, b.brand_name
                FROM products p
                JOIN categories c ON c.cat_id = p.product_cat
                JOIN brands b ON b.brand_id = p.product_brand
                WHERE c.cat_id = '$cat_id'
                ORDER BY p.product_title ASC
                LIMIT $limit OFFSET $offset";
        return $this->db_fetch_all($sql);
    }

    public function count_by_category($cat_id)
    {
        $cat_id = (int)$cat_id;
        if ($cat_id <= 0) { return 0; }
        $row = $this->db_fetch_one("SELECT COUNT(*) AS cnt FROM products WHERE product_cat='$cat_id'");
        return $row ? (int)$row['cnt'] : 0;
    }

    public function filter_products_by_brand($brand_id, $limit = 10, $offset = 0)
    {
        $brand_id = (int)$brand_id;
        if ($brand_id <= 0) { return []; }
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_desc, p.product_image, p.product_keywords,
                       c.cat_id, c.cat_name, b.brand_id, b.brand_name
                FROM products p
                JOIN categories c ON c.cat_id = p.product_cat
                JOIN brands b ON b.brand_id = p.product_brand
                WHERE b.brand_id = '$brand_id'
                ORDER BY p.product_title ASC
                LIMIT $limit OFFSET $offset";
        return $this->db_fetch_all($sql);
    }

    public function count_by_brand($brand_id)
    {
        $brand_id = (int)$brand_id;
        if ($brand_id <= 0) { return 0; }
        $row = $this->db_fetch_one("SELECT COUNT(*) AS cnt FROM products WHERE product_brand='$brand_id'");
        return $row ? (int)$row['cnt'] : 0;
    }

    public function view_single_product($id)
    {
        return $this->get_product((int)$id);
    }

    public function get_all_brands_public()
    {
        $sql = "SELECT brand_id, brand_name FROM brands ORDER BY brand_name ASC";
        return $this->db_fetch_all($sql);
    }
}
