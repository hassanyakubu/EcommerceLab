<?php
require_once __DIR__ . '/../settings/core.php';

if (!isLoggedIn()) {
    header('Location: ../view/login.php');
    exit;
}

if (!isAdmin()) {
    header('Location: ../view/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin - Add/Edit Products</title>
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body>
    <div class="container">
        <div class="page-header">
            <a href="../index.php" class="btn btn-outline">← Back to Home</a>
            <a href="../actions/logout_action.php" class="btn btn-danger float-right">Logout</a>
            <h2>Manage Products</h2>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <h3>Add / Edit Product</h3>
                    <form id="product-form">
                        <input type="hidden" id="product-id" name="product_id" />

                        <label for="product-cat">Category</label>
                        <select id="product-cat" name="cat_id" class="input" required>
                            <option value="">Loading categories...</option>
                        </select>

                        <label for="product-brand" class="mt-10">Brand</label>
                        <select id="product-brand" name="brand_id" class="input" required>
                            <option value="">Select a category first</option>
                        </select>

                        <label for="product-title" class="mt-10">Title</label>
                        <input type="text" id="product-title" name="title" class="input" placeholder="e.g., Air Max 270" required />

                        <label for="product-price" class="mt-10">Price</label>
                        <input type="number" min="0" step="0.01" id="product-price" name="price" class="input" placeholder="0.00" required />

                        <label for="product-desc" class="mt-10">Description</label>
                        <textarea id="product-desc" name="desc" class="input" rows="3" placeholder="Short description (optional)"></textarea>

                        <label for="product-keywords" class="mt-10">Keywords</label>
                        <input type="text" id="product-keywords" name="keywords" class="input" placeholder="e.g., running, sneakers" />

                        <div class="mt-10">
                            <button type="submit" class="btn btn-primary" id="save-product-btn">Save Product</button>
                            <button type="button" class="btn btn-outline" id="reset-product-btn">Reset</button>
                        </div>
                        <div id="form-feedback" class="notice"></div>
                    </form>

                    <div class="card mt-10">
                        <h4>Upload Product Image</h4>
                        <form id="image-upload-form" enctype="multipart/form-data">
                            <input type="hidden" id="img-product-id" name="product_id" />
                            <input type="file" id="product-image" name="image" accept="image/*" />
                            <button type="submit" class="btn btn-secondary mt-10">Upload Image</button>
                            <div id="image-feedback" class="notice"></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card">
                    <h3>Products</h3>
                    <div id="products-container"><p>Loading...</p></div>
                    <div id="list-feedback" class="notice"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/product.js"></script>
</body>
</html>
