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
    <title>Admin - Manage Categories</title>
    <link rel="stylesheet" href="../css/style.css?v=3" />
</head>
<body>
    <div class="container">
        <div class="page-header">
            <a href="../index.php" class="btn btn-outline">← Back to Home</a>
            <a href="../actions/logout_action.php" class="btn btn-danger float-right">Logout</a>
            <h2>Manage Categories</h2>
        </div>

        <div class="row">
            <div class="col">
                <div class="card">
                    <h3>Create Category</h3>
                    <form id="create-category-form">
                        <label for="category-name">Category Name</label>
                        <input type="text" id="category-name" name="name" class="input" placeholder="e.g., Electronics" required />
                        <button type="submit" class="btn btn-primary mt-10">Add Category</button>
                        <div id="create-feedback" class="notice"></div>
                    </form>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <h3>Your Categories</h3>
                    <table id="categories-table" class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categories-body">
                            <tr><td colspan="3">Loading...</td></tr>
                        </tbody>
                    </table>
                    <div id="list-feedback" class="notice"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/category.js"></script>
</body>
</html>
