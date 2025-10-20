<!DOC                    
                    
                    <?php if (isAdmin()): ?>
                        <li><a href="admin/category.php" class="btn btn-secondary">Category</a></li>
                        <li><a href="admin/brand.php" class="btn btn-secondary">Brand</a></li>
                        <li><a href="admin/product.php" class="btn btn-secondary">Add Product</a></li>
                    <?php endif; ?>
                    <li><a href="actions/logout_action.php" class="btn btn-danger">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <h2>Welcome to Our E-Commerce Platform</h2>
            <p>This is a simple e-commerce platform. You can register for a new account or login if you already have one.</p>
            
            <div style="text-align: center; margin-top: 30px;">
                <?php if (!isLoggedIn()): ?>
                    <a href="view/register.php" class="btn btn-primary" style="margin-right: 10px;">Create Account</a>
                    <a href="view/login.php" class="btn btn-secondary">Sign In</a>
                <?php else: ?>
                    <?php if (isAdmin()): ?>
                        <a href="admin/category.php" class="btn btn-secondary" style="margin-right: 10px;">Manage Categories</a>
                        <a href="admin/brand.php" class="btn btn-secondary" style="margin-right: 10px;">Manage Brands</a>
                        <a href="admin/product.php" class="btn btn-secondary" style="margin-right: 10px;">Add Product</a>
                    <?php endif; ?>
                    <a href="actions/logout_action.php" class="btn btn-danger">Logout</a>
                <?php endif; ?>