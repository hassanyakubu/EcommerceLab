document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('product-form');
  const fieldId = document.getElementById('product-id');
  const fieldCat = document.getElementById('product-cat');
  const fieldBrand = document.getElementById('product-brand');
  const fieldTitle = document.getElementById('product-title');
  const fieldPrice = document.getElementById('product-price');
  const fieldDesc = document.getElementById('product-desc');
  const fieldKeywords = document.getElementById('product-keywords');
  const btnReset = document.getElementById('reset-product-btn');
  const formFeedback = document.getElementById('form-feedback');

  const imgForm = document.getElementById('image-upload-form');
  const imgProductId = document.getElementById('img-product-id');
  const imgInput = document.getElementById('product-image');
  const imgFeedback = document.getElementById('image-feedback');

  const container = document.getElementById('products-container');
  const listFeedback = document.getElementById('list-feedback');

  const endpoints = {
    cats: '../actions/fetch_category_action.php',
    brands: '../actions/fetch_brand_action.php',
    add: '../actions/add_product_action.php',
    update: '../actions/update_product_action.php',
    upload: '../actions/upload_product_image_action.php',
    fetch: '../actions/fetch_products_action.php',
  };

  const IMG_PREFIX = '/~hassan.yakubu/';

  let allBrands = [];

  function showMessage(el, msg, ok = true) {
    if (!el) return;
    el.textContent = msg;
    el.style.color = ok ? '#2f855a' : '#c53030';
  }

  async function api(url, method = 'GET', data = null, isForm = false) {
    const options = { method };
    if (data && !isForm) {
      options.headers = { 'Content-Type': 'application/x-www-form-urlencoded' };
      const params = new URLSearchParams();
      Object.entries(data).forEach(([k, v]) => params.append(k, v));
      options.body = params.toString();
    } else if (data && isForm) {
      options.body = data; // FormData
    }
    const res = await fetch(url, options);
    let json;
    try { json = await res.json(); } catch(e) { json = { status: 'error', message: 'Unexpected server response.' }; }
    return json;
  }

  async function loadCategories() {
    fieldCat.innerHTML = '<option value="">Loading...</option>';
    const resp = await api(endpoints.cats);
    if (resp.status === 'success') {
      const rows = resp.data || [];
      if (rows.length === 0) {
        fieldCat.innerHTML = '<option value="">No categories available</option>';
        return;
      }
      fieldCat.innerHTML = '<option value="">Select category...</option>';
      rows.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.cat_id; opt.textContent = c.cat_name;
        fieldCat.appendChild(opt);
      });
    } else {
      fieldCat.innerHTML = '<option value="">Failed to load categories</option>';
      showMessage(formFeedback, resp.message || 'Failed to load categories.', false);
    }
  }

  async function loadBrands() {
    fieldBrand.innerHTML = '<option value="">Loading...</option>';
    const resp = await api(endpoints.brands);
    if (resp.status === 'success') {
      allBrands = resp.data || [];
      populateBrandOptions();
    } else {
      fieldBrand.innerHTML = '<option value="">Failed to load brands</option>';
      showMessage(formFeedback, resp.message || 'Failed to load brands.', false);
    }
  }

  function populateBrandOptions() {
    const catId = parseInt(fieldCat.value || '0', 10);
    fieldBrand.innerHTML = '';
    if (!catId) {
      fieldBrand.innerHTML = '<option value="">Select a category first</option>';
      return;
    }
    const filtered = allBrands.filter(b => parseInt(b.cat_id, 10) === catId);
    if (filtered.length === 0) {
      fieldBrand.innerHTML = '<option value="">No brands for selected category</option>';
      return;
    }
    fieldBrand.innerHTML = '<option value="">Select brand...</option>';
    filtered.forEach(b => {
      const opt = document.createElement('option');
      opt.value = b.brand_id; opt.textContent = b.brand_name;
      fieldBrand.appendChild(opt);
    });
  }

  function clearForm() {
    fieldId.value = '';
    fieldCat.value = '';
    populateBrandOptions();
    fieldTitle.value = '';
    fieldPrice.value = '';
    fieldDesc.value = '';
    fieldKeywords.value = '';
    imgProductId.value = '';
    imgInput.value = '';
    formFeedback.textContent = '';
    imgFeedback.textContent = '';
  }

  function groupProducts(rows) {
    const map = new Map();
    rows.forEach(r => {
      const key = `${r.cat_name}|${r.brand_name}`;
      if (!map.has(key)) map.set(key, []);
      map.get(key).push(r);
    });
    return map;
  }

  function renderProducts(rows) {
    if (!Array.isArray(rows) || rows.length === 0) {
      container.innerHTML = '<p>No products yet.</p>';
      return;
    }
    const groups = groupProducts(rows);
    container.innerHTML = '';
    groups.forEach((items, key) => {
      const [catName, brandName] = key.split('|');
      const section = document.createElement('div');
      section.className = 'card mt-10';
      const header = document.createElement('h4');
      header.textContent = `Category: ${catName} • Brand: ${brandName}`;
      section.appendChild(header);

      const table = document.createElement('table');
      table.className = 'table';
      const thead = document.createElement('thead');
      thead.innerHTML = '<tr><th>ID</th><th>Title</th><th>Price</th><th>Image</th><th>Actions</th></tr>';
      table.appendChild(thead);
      const tbody = document.createElement('tbody');

      items.forEach(p => {
        const tr = document.createElement('tr');
        const tdId = document.createElement('td'); tdId.textContent = p.product_id;
        const tdTitle = document.createElement('td'); tdTitle.textContent = p.product_title;
        const tdPrice = document.createElement('td'); tdPrice.textContent = Number(p.product_price).toFixed(2);
        const tdImg = document.createElement('td');
        if (p.product_image) {
          const img = document.createElement('img');
          img.src = IMG_PREFIX + p.product_image;
          img.alt = p.product_title;
          img.style.maxWidth = '60px';
          img.style.maxHeight = '60px';
          tdImg.appendChild(img);
        } else {
          tdImg.textContent = '—';
        }
        const tdActions = document.createElement('td'); tdActions.className = 'actions';

        const editBtn = document.createElement('button');
        editBtn.className = 'btn btn-secondary';
        editBtn.textContent = 'Edit';
        editBtn.addEventListener('click', () => {
          fieldId.value = p.product_id;
          fieldCat.value = p.cat_id;
          populateBrandOptions();
          fieldBrand.value = p.brand_id;
          fieldTitle.value = p.product_title;
          fieldPrice.value = p.product_price;
          fieldDesc.value = p.product_desc || '';
          fieldKeywords.value = p.product_keywords || '';
          imgProductId.value = p.product_id;
          showMessage(formFeedback, 'Loaded product into form. You can now update and save.', true);
        });

        tdActions.appendChild(editBtn);

        tr.appendChild(tdId);
        tr.appendChild(tdTitle);
        tr.appendChild(tdPrice);
        tr.appendChild(tdImg);
        tr.appendChild(tdActions);
        tbody.appendChild(tr);
      });

      table.appendChild(tbody);
      section.appendChild(table);
      container.appendChild(section);
    });
  }

  async function loadProducts() {
    container.innerHTML = '<p>Loading...</p>';
    listFeedback.textContent = '';
    const resp = await api(endpoints.fetch);
    if (resp.status === 'success') {
      renderProducts(resp.data || []);
    } else {
      container.innerHTML = '<p>Failed to load products.</p>';
      showMessage(listFeedback, resp.message || 'Failed to fetch products.', false);
    }
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    formFeedback.textContent = '';
    const payload = {
      product_id: fieldId.value || undefined,
      cat_id: fieldCat.value,
      brand_id: fieldBrand.value,
      title: fieldTitle.value.trim(),
      price: fieldPrice.value,
      desc: fieldDesc.value.trim() || undefined,
      keywords: fieldKeywords.value.trim() || undefined,
    };
    if (!payload.cat_id || !payload.brand_id || !payload.title || payload.price === '') {
      showMessage(formFeedback, 'Please fill all required fields.', false);
      return;
    }
    const isUpdate = !!fieldId.value;
    const resp = await api(isUpdate ? endpoints.update : endpoints.add, 'POST', payload);
    if (resp.status === 'success') {
      showMessage(formFeedback, resp.message, true);
      // If created, sync product id to image form
      if (!isUpdate && resp.product_id) {
        fieldId.value = resp.product_id;
        imgProductId.value = resp.product_id;
      }
      await loadProducts();
    } else {
      showMessage(formFeedback, resp.message || 'Failed to save product.', false);
    }
  });

  btnReset.addEventListener('click', () => {
    clearForm();
  });

  fieldCat.addEventListener('change', () => {
    populateBrandOptions();
  });

  imgForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    imgFeedback.textContent = '';
    if (!imgProductId.value) {
      showMessage(imgFeedback, 'Please save or load a product first.', false);
      return;
    }
    if (!imgInput.files || imgInput.files.length === 0) {
      showMessage(imgFeedback, 'Please choose an image to upload.', false);
      return;
    }
    const fd = new FormData();
    fd.append('product_id', imgProductId.value);
    fd.append('image', imgInput.files[0]);
    const resp = await api(endpoints.upload, 'POST', fd, true);
    if (resp.status === 'success') {
      showMessage(imgFeedback, 'Image uploaded.', true);
      await loadProducts();
    } else {
      showMessage(imgFeedback, resp.message || 'Failed to upload image.', false);
    }
  });

  // Initial loads
  Promise.resolve()
    .then(loadCategories)
    .then(loadBrands)
    .then(loadProducts);
});
