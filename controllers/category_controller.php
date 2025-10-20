<?php
require_once __DIR__ . '/../classes/category_class.php';

class category_controller
{
    private $model;

    public function __construct()
    {
        $this->model = new category_class();
    }

    public function add_category_ctr($name)
    {
        if (empty($name)) {
            return ['status' => 'error', 'message' => 'Category name is required.'];
        }
        $ok = $this->model->add_category($name);
        if ($ok) {
            return ['status' => 'success', 'message' => 'Category created successfully.'];
        }
        return ['status' => 'error', 'message' => 'Failed to create category.'];
    }

    public function fetch_all_categories_ctr()
    {
        $rows = $this->model->get_all_categories();
        return ['status' => 'success', 'data' => $rows ?: []];
    }

    public function update_category_ctr($cat_id, $new_name)
    {
        if (empty($cat_id) || empty($new_name)) {
            return ['status' => 'error', 'message' => 'Category ID and new name are required.'];
        }
        $ok = $this->model->update_category((int)$cat_id, $new_name);
        if ($ok) {
            return ['status' => 'success', 'message' => 'Category updated successfully.'];
        }
        return ['status' => 'error', 'message' => 'Failed to update category.'];
    }

    public function delete_category_ctr($cat_id)
    {
        if (empty($cat_id)) {
            return ['status' => 'error', 'message' => 'Category ID is required.'];
        }
        $ok = $this->model->delete_category((int)$cat_id);
        if ($ok) {
            return ['status' => 'success', 'message' => 'Category deleted successfully.'];
        }
        return ['status' => 'error', 'message' => 'Failed to delete category.'];
    }
}
