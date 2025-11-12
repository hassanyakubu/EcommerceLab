<?php 
require_once __DIR__ . '/../settings/core.php'; 
if (!isLoggedIn()) { 
    header('Location: login.php?redirect=checkout.php');
    exit; 
}

// Get cart items
$cartController = new CartController();
$cart_items = $cartController->get_cart_items($_SESSION['customer_id']);
$cart_total = $cartController->get_cart_total($_SESSION['customer_id']);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Checkout</title>
  <link rel="stylesheet" href="../css/style.css?v=<?=time()?>" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
  <div class="container">
    <div class="page-header">
      <a href="cart.php" class="btn btn-outline">← Back to Cart</a>
      <h2>Checkout</h2>
    </div>

    <div class="checkout-grid">
      <div class="checkout-form">
        <div class="card">
          <div class="card-header">
            <h3>Billing Details</h3>
          </div>
          <div class="card-body">
            <form id="checkout-form">
              <div class="form-group">
                <label>Full Name</label>
                <input type="text" class="input" value="<?= htmlspecialchars($_SESSION['customer_name'] ?? '') ?>" readonly>
              </div>
              
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="input" value="<?= htmlspecialchars($_SESSION['customer_email'] ?? '') ?>" readonly>
              </div>
              
              <div class="form-group">
                <label>Phone</label>
                <input type="tel" class="input" value="<?= htmlspecialchars($_SESSION['customer_contact'] ?? '') ?>" required>
              </div>
              
              <div class="form-group">
                <label>Address</label>
                <input type="text" class="input" required>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label>City</label>
                  <input type="text" class="input" required>
                </div>
                <div class="form-group">
                  <label>Country</label>
                  <select class="input" required>
                    <option value="Ghana" selected>Ghana</option>
                    <option value="Nigeria">Nigeria</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="order-summary">
        <div class="card">
          <div class="card-header">
            <h3>Order Summary</h3>
          </div>
          <div class="card-body">
            <div class="order-items">
              <?php foreach ($cart_items as $item): ?>
                <div class="order-item">
                  <div class="item-name">
                    <?= htmlspecialchars($item['product_title']) ?> × <?= $item['qty'] ?>
                  </div>
                  <div class="item-price">
                    ₵<?= number_format($item['product_price'] * $item['qty'], 2) ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="order-totals">
              <div class="total-row">
                <span>Subtotal</span>
                <span>₵<?= number_format($cart_total, 2) ?></span>
              </div>
              <div class="total-row">
                <span>Shipping</span>
                <span>₵0.00</span>
              </div>
              <div class="total-row grand-total">
                <span>Total</span>
                <span>₵<?= number_format($cart_total, 2) ?></span>
              </div>
            </div>

            <button id="place-order" class="btn btn-primary btn-block">
              Place Order
            </button>

            <div class="payment-methods">
              <p><i class="bi bi-shield-lock"></i> Secure Payment</p>
              <div class="payment-icons">
                <i class="bi bi-credit-card"></i>
                <i class="bi bi-paypal"></i>
                <i class="bi bi-phone"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Payment Modal -->
  <div class="modal" id="paymentModal" tabindex="-1">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Processing Your Order</h3>
      </div>
      <div class="modal-body text-center">
        <div id="payment-processing">
          <div class="spinner"></div>
          <p>Please wait while we process your payment...</p>
        </div>
        
        <div id="payment-success" class="d-none">
          <div class="success-icon">
            <i class="bi bi-check-circle"></i>
          </div>
          <h4>Payment Successful!</h4>
          <p>Your order has been placed successfully.</p>
          <p>Order ID: <strong id="order-id"></strong></p>
          <p>Total Paid: <strong id="order-total"></strong></p>
          <a href="orders.php" class="btn btn-primary mt-3">
            View Orders
          </a>
        </div>
        
        <div id="payment-failed" class="d-none">
          <div class="error-icon">
            <i class="bi bi-x-circle"></i>
          </div>
          <h4>Payment Failed</h4>
          <p id="error-message" class="error-text"></p>
          <button class="btn btn-primary" data-dismiss="modal">
            Try Again
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="../js/checkout.js"></script>
</body>
</html>