<?php
// Ensure clean JSON only
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ob_start();

header('Content-Type: application/json');
require_once __DIR__ . '/../controllers/product_controller.php';
require_once __DIR__ . '/../controllers/category_controller.php';

try {
    $action   = $_GET['action'] ?? $_POST['action'] ?? '';
    $page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage  = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    $cat_id   = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : 0;
    $brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;
    $query    = isset($_GET['q']) ? (string)$_GET['q'] : '';
    $id       = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $pc = new product_controller();
    $cc = new category_controller();

    switch ($action) {
        case 'list':
            $out = $pc->list_products_ctr($page, $perPage); break;
        case 'search':
            $out = $pc->search_products_ctr($query, $page, $perPage); break;
        case 'filter_cat':
            $out = $pc->filter_by_category_ctr($cat_id, $page, $perPage); break;
        case 'filter_brand':
            $out = $pc->filter_by_brand_ctr($brand_id, $page, $perPage); break;
        case 'single':
            $out = $pc->single_product_ctr($id); break;
        case 'brands':
            $out = $pc->get_all_brands_public_ctr(); break;
        case 'categories':
            $out = $cc->fetch_all_categories_ctr(); break;
        default:
            $out = ['status' => 'error', 'message' => 'Unknown action.'];
    }

    if (ob_get_length() !== false) { ob_end_clean(); }
    echo json_encode($out);
    exit;

} catch (Throwable $e) {
    if (ob_get_length() !== false) { ob_end_clean(); }
    echo json_encode(['status' => 'error', 'message' => 'Unexpected server error.']);
    error_log('product_actions error: ' . $e->getMessage());
    exit;
}