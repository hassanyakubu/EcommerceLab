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
    <title>Admin - Manage Brands</title>
    <link rel="stylesheet" href="../css/style.css?v=3" />
</head>
<body>
    <div class="container">
        <div class="page-header">
            <a href="../index.php" class="btn btn-outline">← Back to Home</a>
            <a href="../actions/logout_action.php" class="btn btn-danger float-right">Logout</a>
            <h2>Manage Brands</h2>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <h3>Create Brand</h3>
                    <form id="create-brand-form">
                        <label for="brand-name">Brand Name</label>
                        <input type="text" id="brand-name" name="name" class="input" placeholder="e.g., Nike" required />

                        <label for="brand-category" class="mt-10">Category</label>
                        <select id="brand-category" name="cat_id" class="input" required>
                            <option value="">Loading categories...</option>
                        </select>

                        <button type="submit" class="btn btn-primary mt-10">Add Brand</button>
                        <div id="create-feedback" class="notice"></div>
                    </form>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <h3>Your Brands (Grouped by Category)</h3>
                    <div id="brands-container">
                        <p>Loading...</p>
                    </div>
                    <div id="list-feedback" class="notice"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/brand.js"></script>
</body>
</html>
