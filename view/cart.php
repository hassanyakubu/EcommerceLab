<?php 
require_once __DIR__ . '/../settings/core.php'; 
if (!isLoggedIn()) { 
    header('Location: login.php'); 
    exit; 
} 

// Get cart items
$customer_id = $_SESSION['customer_id'] ?? null;
$ip_address = $customer_id ? null : $_SERVER['REMOTE_ADDR'];

$cartController = new CartController();
$cart_items = $cartController->get_cart_items($customer_id, $ip_address);
$cart_total = $cartController->get_cart_total($customer_id, $ip_address);
$cart_count = $cartController->count_cart_items($customer_id, $ip_address);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Your Cart</title>
  <link rel="stylesheet" href="../css/style.css?v=<?=time()?>" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
  <div class="container">
    <div class="page-header">
      <a href="../index.php" class="btn btn-outline">← Back to Shop</a>
      <h2>Your Shopping Cart</h2>
    </div>

    <?php if (empty($cart_items)): ?>
      <div class="notice">
        Your cart is empty. <a href="all_products.php">Continue shopping</a>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="cart-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Subtotal</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart_items as $item): ?>
              <tr data-product-id="<?= htmlspecialchars($item['product_id']) ?>">
                <td class="product-info">
                  <img src="<?= htmlspecialchars($item['product_image'] ?? 'https://placehold.co/100x100?text=No+Image') ?>" 
                       alt="<?= htmlspecialchars($item['product_title']) ?>" />
                  <div>
                    <h4><?= htmlspecialchars($item['product_title']) ?></h4>
                    <div class="meta">SKU: <?= htmlspecialchars($item['product_id']) ?></div>
                  </div>
                </td>
                <td class="price">₵<?= number_format($item['product_price'], 2) ?></td>
                <td>
                  <div class="quantity-controls">
                    <button class="btn btn-sm update-quantity" data-action="decrease">-</button>
                    <input type="number" class="quantity-input" 
                           value="<?= $item['qty'] ?>" min="1" 
                           data-product-id="<?= $item['product_id'] ?>">
                    <button class="btn btn-sm update-quantity" data-action="increase">+</button>
                  </div>
                </td>
                <td class="subtotal">₵<?= number_format($item['product_price'] * $item['qty'], 2) ?></td>
                <td>
                  <button class="btn btn-sm btn-danger remove-from-cart" 
                          data-product-id="<?= $item['product_id'] ?>">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-right"><strong>Total:</strong></td>
              <td colspan="2" id="cart-total">₵<?= number_format($cart_total, 2) ?></td>
            </tr>
          </tfoot>
        </table>

        <div class="cart-actions">
          <button id="empty-cart" class="btn btn-outline">
            <i class="bi bi-cart-x"></i> Empty Cart
          </button>
          <a href="checkout.php" class="btn btn-primary">
            Proceed to Checkout <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- Remove Item Modal -->
  <div class="modal" id="removeItemModal" tabindex="-1">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Remove Item</h5>
        <button type="button" class="btn-close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to remove this item from your cart?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirm-remove">Remove</button>
      </div>
    </div>
  </div>

  <script src="../js/cart.js"></script>
</body>
</html>