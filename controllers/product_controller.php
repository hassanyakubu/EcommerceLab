<?php
require_once __DIR__ . '/../classes/product_class.php';

class product_controller
{
    private $model;

    public function __construct()
    {
        $this->model = new product_class();
    }

    public function add_product_ctr($data)
    {
        $cat_id = (int)($data['cat_id'] ?? 0);
        $brand_id = (int)($data['brand_id'] ?? 0);
        $title = $data['title'] ?? '';
        $price = $data['price'] ?? 0;
        $desc = $data['desc'] ?? null;
        $image = $data['image'] ?? null;
        $keywords = $data['keywords'] ?? null;

        if ($cat_id <= 0 || $brand_id <= 0 || trim($title) === '' || $price === '' || !is_numeric($price)) {
            return ['status' => 'error', 'message' => 'Missing or invalid product fields.'];
        }

        [$ok, $id] = $this->model->add_product($cat_id, $brand_id, $title, (float)$price, $desc, $image, $keywords);
        if ($ok && $id) {
            return ['status' => 'success', 'message' => 'Product created successfully.', 'product_id' => $id];
        }
        return ['status' => 'error', 'message' => 'Failed to create product. Ensure brand matches category.'];
    }

    public function update_product_ctr($data)
    {
        $product_id = (int)($data['product_id'] ?? 0);
        $cat_id = (int)($data['cat_id'] ?? 0);
        $brand_id = (int)($data['brand_id'] ?? 0);
        $title = $data['title'] ?? '';
        $price = $data['price'] ?? 0;
        $desc = $data['desc'] ?? null;
        $keywords = $data['keywords'] ?? null;

        if ($product_id <= 0 || $cat_id <= 0 || $brand_id <= 0 || trim($title) === '' || $price === '' || !is_numeric($price)) {
            return ['status' => 'error', 'message' => 'Missing or invalid product fields.'];
        }

        $ok = $this->model->update_product($product_id, $cat_id, $brand_id, $title, (float)$price, $desc, $keywords);
        if ($ok) {
            return ['status' => 'success', 'message' => 'Product updated successfully.'];
        }
        return ['status' => 'error', 'message' => 'Failed to update product. Ensure brand matches category.'];
    }

    public function set_product_image_ctr($product_id, $image_path)
    {
        $ok = $this->model->set_product_image((int)$product_id, $image_path);
        if ($ok) {
            return ['status' => 'success', 'message' => 'Image updated.'];
        }
        return ['status' => 'error', 'message' => 'Failed to update image path.'];
    }

    public function fetch_products_ctr()
    {
        $rows = $this->model->get_all_products_joined();
        return ['status' => 'success', 'data' => $rows ?: []];
    }

    public function get_product_ctr($product_id)
    {
        $row = $this->model->get_product((int)$product_id);
        if ($row) { return ['status' => 'success', 'data' => $row]; }
        return ['status' => 'error', 'message' => 'Product not found.'];
    }
}
