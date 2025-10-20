<?php
require_once __DIR__ . '/../classes/brand_class.php';

class brand_controller
{
    private $model;

    public function __construct()
    {
        $this->model = new brand_class();
    }

    public function add_brand_ctr($name, $cat_id, $user_id)
    {
        if (empty($name) || empty($cat_id) || empty($user_id)) {
            return ['status' => 'error', 'message' => 'Brand name and category are required.'];
        }
        $ok = $this->model->add_brand($name, (int)$cat_id, (int)$user_id);
        if ($ok) {
            return ['status' => 'success', 'message' => 'Brand created successfully.'];
        }
        return ['status' => 'error', 'message' => 'Failed to create brand. Ensure the brand name is unique within the selected category.'];
    }

    public function fetch_user_brands_ctr($user_id)
    {
        $rows = $this->model->get_brands_by_user_grouped((int)$user_id);
        return ['status' => 'success', 'data' => $rows ?: []];
    }

    public function update_brand_ctr($brand_id, $new_name, $user_id)
    {
        if (empty($brand_id) || empty($new_name) || empty($user_id)) {
            return ['status' => 'error', 'message' => 'Brand ID and new name are required.'];
        }
        $ok = $this->model->update_brand((int)$brand_id, $new_name, (int)$user_id);
        if ($ok) {
            return ['status' => 'success', 'message' => 'Brand updated successfully.'];
        }
        return ['status' => 'error', 'message' => 'Failed to update brand. Name must be unique within the category.'];
    }

    public function delete_brand_ctr($brand_id, $user_id)
    {
        if (empty($brand_id) || empty($user_id)) {
            return ['status' => 'error', 'message' => 'Brand ID is required.'];
        }
        $ok = $this->model->delete_brand((int)$brand_id, (int)$user_id);
        if ($ok) {
            return ['status' => 'success', 'message' => 'Brand deleted successfully.'];
        }
        return ['status' => 'error', 'message' => 'Failed to delete brand.'];
    }
}
