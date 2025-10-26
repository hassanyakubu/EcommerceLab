<?php require_once __DIR__ . '/../settings/core.php'; if (!isLoggedIn()) { header('Location: login.php'); exit; } ?>
<?php $id = isset($_GET['id']) ? (int)$_GET['id'] : 0; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Product Details</title>
  <link rel="stylesheet" href="../css/style.css?v=3" />
  <style>
    .layout { display:grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    @media (max-width: 800px) { .layout { grid-template-columns: 1fr; } }
    .imgbox img { width:100%; border-radius:8px; background:#f8f8f8; }
    .meta { color:#666; font-size:14px; margin-top:6px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="page-header">
      <a href="all_product.php" class="btn btn-outline">← All Products</a>
      <h2>Product Details</h2>
    </div>

    <div id="wrap" class="layout">
      <div class="imgbox"><img id="pimg" alt="product image"/></div>
      <div>
        <div class="meta">Product ID: <span id="pid"></span></div>
        <h3 id="ptitle"></h3>
        <div><strong id="pprice"></strong></div>
        <div class="meta" style="margin-top:6px;">Category: <span id="pcat"></span> • Brand: <span id="pbrand"></span></div>
        <p id="pdesc" style="margin-top:12px;"></p>
        <div class="meta">Keywords: <span id="pkeys"></span></div>
        <div style="margin-top:12px;">
          <a href="#" class="btn btn-secondary">Add to Cart</a>
        </div>
      </div>
    </div>

    <div id="feedback" class="notice" style="margin-top:12px;"></div>
  </div>

  <script>
    const API = '../actions/product_actions.php';
    const IMG_PREFIX = '/~hassan.yakubu/';
    const id = <?php echo json_encode($id); ?>;
    const pid = document.getElementById('pid');
    const ptitle = document.getElementById('ptitle');
    const pprice = document.getElementById('pprice');
    const pcat = document.getElementById('pcat');
    const pbrand = document.getElementById('pbrand');
    const pdesc = document.getElementById('pdesc');
    const pkeys = document.getElementById('pkeys');
    const pimg = document.getElementById('pimg');
    const feedback = document.getElementById('feedback');

    async function load() {
      const url = `${API}?action=single&id=${id}`;
      const res = await fetch(url);
      const data = await res.json();
      if (data.status !== 'success' || !data.data) {
        feedback.textContent = data.message || 'Product not found.';
        return;
      }
      const p = data.data;
      pid.textContent = p.product_id;
      ptitle.textContent = p.product_title;
      pprice.textContent = '$' + Number(p.product_price).toFixed(2);
      pcat.textContent = p.cat_name;
      pbrand.textContent = p.brand_name;
      pdesc.textContent = p.product_desc || '';
      pkeys.textContent = p.product_keywords || '';
      pimg.src = p.product_image ? (IMG_PREFIX + p.product_image) : 'https://placehold.co/600x600?text=No+Image';
      pimg.alt = p.product_title;
    }

    if (id > 0) { load(); } else { feedback.textContent = 'Invalid product ID.'; }
  </script>
</body>
</html>