<?php require_once __DIR__ . '/../settings/core.php'; if (!isLoggedIn()) { header('Location: login.php'); exit; } ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>All Products</title>
  <link rel="stylesheet" href="../css/style.css?v=3" />
</head>
<body>
  <div class="container">
    <div class="filters" style="margin-top:6px;">
      <input type="text" id="searchInput" class="input" placeholder="Search products by title or keywords" />
      <button id="searchBtn" class="btn btn-primary">Search</button>
    </div>

    <div class="page-header">
      <a href="../index.php" class="btn btn-outline">← Home</a>
      <h2>All Products</h2>
    </div>

    <div class="filters">
      <select id="catFilter" class="input">
        <option value="">All Categories</option>
      </select>
      <select id="brandFilter" class="input">
        <option value="">All Brands</option>
      </select>
    </div>

    <div id="products" class="grid"></div>
    <div id="pagination" class="pagination"></div>
    <div id="feedback" class="notice"></div>
  </div>

  <script>
    const API = '../actions/product_actions.php';
    const productsEl = document.getElementById('products');
    const pagEl = document.getElementById('pagination');
    const feedbackEl = document.getElementById('feedback');
    const catEl = document.getElementById('catFilter');
    const brandEl = document.getElementById('brandFilter');
    const qEl = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');

    let state = { page: 1, per_page: 10, cat_id: '', brand_id: '', q: '' };

    function qs(params) {
      return Object.entries(params)
        .filter(([,v]) => v !== '' && v !== null && v !== undefined)
        .map(([k,v]) => encodeURIComponent(k) + '=' + encodeURIComponent(v)).join('&');
    }

    async function fetchJSON(action, params = {}) {
      const url = `${API}?action=${action}&${qs(params)}`;
      const res = await fetch(url);
      return res.json();
    }

    function renderProducts(data) {
      productsEl.innerHTML = '';
      (data || []).forEach(p => {
        const img = p.product_image ? ('../' + p.product_image) : 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400"><rect width="100%" height="100%" fill="%23f3f4f6"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="%239ca3af" font-family="Arial,Helvetica,sans-serif" font-size="20">No Image</text></svg>';
        const el = document.createElement('div');
        el.className = 'card';
        el.innerHTML = `
          <a href="single_product.php?id=${p.product_id}">
            <img src="${img}" alt="${p.product_title}"/>
          </a>
          <h4 style="margin:8px 0 4px">${p.product_title}</h4>
          <div><strong>$${Number(p.product_price).toFixed(2)}</strong></div>
          <div class="meta">Category: ${p.cat_name} • Brand: ${p.brand_name}</div>
          <div class="meta">ID: ${p.product_id}</div>
          <div style="margin-top:8px;">
            <a href="#" class="btn btn-secondary btn-sm">Add to Cart</a>
          </div>
        `;
        productsEl.appendChild(el);
      });
    }

    function renderPagination(total, page, perPage) {
      pagEl.innerHTML = '';
      const pages = Math.max(1, Math.ceil(total / perPage));
      if (pages <= 1) return;
      for (let i=1; i<=pages; i++) {
        const btn = document.createElement('button');
        btn.className = 'btn ' + (i === page ? 'btn-primary' : 'btn-outline');
        btn.textContent = i;
        btn.onclick = () => { state.page = i; load(); };
        pagEl.appendChild(btn);
      }
    }

    async function loadFilters() {
      const [cats, brands] = await Promise.all([
        fetchJSON('categories'),
        fetchJSON('brands')
      ]);
      if (cats.status === 'success') {
        cats.data.forEach(c => {
          const opt = document.createElement('option');
          opt.value = c.cat_id; opt.textContent = c.cat_name;
          catEl.appendChild(opt);
        });
      }
      if (brands.status === 'success') {
        brands.data.forEach(b => {
          const opt = document.createElement('option');
          opt.value = b.brand_id; opt.textContent = b.brand_name;
          brandEl.appendChild(opt);
        });
      }
    }

    async function load() {
      feedbackEl.textContent = '';
      let action = 'list';
      const params = { page: state.page, per_page: state.per_page };
      if (state.q) { action = 'search'; params.q = state.q; }
      if (state.cat_id) { action = 'filter_cat'; params.cat_id = state.cat_id; }
      if (state.brand_id) { action = 'filter_brand'; params.brand_id = state.brand_id; }

      const resp = await fetchJSON(action, params);
      if (resp.status !== 'success') {
        feedbackEl.textContent = resp.message || 'Failed to load products.';
        productsEl.innerHTML = '';
        pagEl.innerHTML = '';
        return;
      }
      renderProducts(resp.data);
      renderPagination(resp.total || 0, resp.page || 1, resp.per_page || state.per_page);
      if (!resp.data || resp.data.length === 0) {
        feedbackEl.textContent = 'No products found.';
      } else {
        feedbackEl.textContent = '';
      }
    }

    // Debounce helper
    function debounce(fn, wait = 400) {
      let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
    }

    catEl.addEventListener('change', () => { state.cat_id = catEl.value; state.page = 1; load(); });
    brandEl.addEventListener('change', () => { state.brand_id = brandEl.value; state.page = 1; load(); });
    searchBtn.addEventListener('click', () => { state.q = qEl.value.trim(); state.page = 1; load(); });
    qEl.addEventListener('keydown', (e) => { if (e.key === 'Enter') { state.q = qEl.value.trim(); state.page = 1; load(); }});
    qEl.addEventListener('input', debounce(() => { state.q = qEl.value.trim(); state.page = 1; load(); }, 500));

    (async function init() {
      await loadFilters();
      await load();
    })();
  </script>
</body>
</html>