<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/home/hassan.yakubu/public_html/logs/php_error.log');
error_reporting(E_ALL);

error_log("UPLOAD DEBUG: Using uploadsRoot = " . $uploadsRoot);
error_log("UPLOAD DEBUG: Trying to move uploaded file to " . $destPath);

// Ensure clean JSON only
//error_reporting(0);
//ini_set('display_errors', 0);
ini_set('log_errors', 1);
if (session_status() === PHP_SESSION_NONE) { session_start(); }
ob_start();

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
        exit;
    }

    if (!isLoggedIn()) {
        echo json_encode(['status' => 'error', 'message' => 'Not authenticated.']);
        exit;
    }

    if (!isAdmin()) {
        echo json_encode(['status' => 'error', 'message' => 'Forbidden. Admins only.']);
        exit;
    }

    if (!isset($_POST['product_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Product ID is required.']);
        exit;
    }

    $product_id = (int)$_POST['product_id'];
    if ($product_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product ID.']);
        exit;
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'No image uploaded or upload error.']);
        exit;
    }

    // Ensure uploads directory exists (create if necessary)
    $uploadsRoot = '/home/hassan.yakubu/public_html/uploads';
    error_log("UPLOAD DEBUG: Using uploadsRoot = " . $uploadsRoot);
    if (!is_dir($uploadsRoot)) {
        if (!mkdir($uploadsRoot, 0755, true)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare uploads directory.']);
            exit;
        }
    }
    $uploadsDir = realpath($uploadsRoot);
    if ($uploadsDir === false) {
        echo json_encode(['status' => 'error', 'message' => 'Uploads directory is not available.']);
        exit;
    }

    $user_id = getCurrentUserId();

    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);
    $allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/gif' => 'gif', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        echo json_encode(['status' => 'error', 'message' => 'Unsupported image type.']);
        exit;
    }
    $ext = $allowed[$mime];

    // Build path uploads/u{user_id}/p{product_id}/
    $subDir = 'u' . $user_id . '/p' . $product_id;
    $targetDir = $uploadsDir . DIRECTORY_SEPARATOR . $subDir;
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create upload subdirectory.']);
            exit;
        }
    }

    // Generate safe filename
    $base = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
    $base = preg_replace('/[^a-zA-Z0-9._-]/', '_', strtolower($base));
    if ($base === '' || $base === '_' ) { $base = 'image'; }
    $filename = $base . '_' . time() . '.' . $ext;

    $destPath = $targetDir . DIRECTORY_SEPARATOR . $filename;

    // Ensure destination remains inside uploads dir after resolution
    $resolvedDest = realpath(dirname($destPath));
    if ($resolvedDest === false || strpos($resolvedDest, $uploadsDir) !== 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid upload path.']);
        exit;
    }

    error_log("UPLOAD DEBUG: Trying to move uploaded file to " . $destPath);
    
    // Extra debugging around file move
    error_log("UPLOAD DEBUG: tmp_name=" . $_FILES['image']['tmp_name'] . " destPath=$destPath");
    if (move_uploaded_file($_FILES['image']['tmp_name'], $destPath)) {
        error_log("UPLOAD SUCCESS: saved to $destPath");
    } else {
        error_log("UPLOAD FAIL: unable to move file to $destPath");
        echo json_encode(['status' => 'error', 'message' => 'Failed to store uploaded image.']);
        exit;
    }

    // Build relative path to store in DB (e.g., uploads/u{uid}/p{pid}/file.ext)
    $relativePath = 'uploads/' . str_replace(DIRECTORY_SEPARATOR, '/', $subDir) . '/' . $filename;

    $controller = new product_controller();
    $resp = $controller->set_product_image_ctr($product_id, $relativePath);
    if (($resp['status'] ?? '') !== 'success') {
        echo json_encode(['status' => 'error', 'message' => 'Image stored but DB update failed.']);
        exit;
    }

    if (ob_get_length() !== false) { ob_end_clean(); }
    echo json_encode(['status' => 'success', 'message' => 'Image uploaded successfully.', 'path' => $relativePath]);
    exit;

} catch (Throwable $e) {
    if (ob_get_length() !== false) { ob_end_clean(); }
    echo json_encode(['status' => 'error', 'message' => 'Unexpected server error.']);
    error_log('upload_product_image_action error: ' . $e->getMessage());
    exit;
}
