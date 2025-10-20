document.addEventListener('DOMContentLoaded', () => {
  const createForm = document.getElementById('create-brand-form');
  const nameInput = document.getElementById('brand-name');
  const catSelect = document.getElementById('brand-category');
  const createFeedback = document.getElementById('create-feedback');
  const listFeedback = document.getElementById('list-feedback');
  const container = document.getElementById('brands-container');

  const endpoints = {
    cats: '../actions/fetch_category_action.php',
    fetch: '../actions/fetch_brand_action.php',
    add: '../actions/add_brand_action.php',
    update: '../actions/update_brand_action.php',
    delete: '../actions/delete_brand_action.php',
  };

  function showMessage(el, msg, ok = true) {
    if (!el) return;
    el.textContent = msg;
    el.style.color = ok ? '#2f855a' : '#c53030';
  }

  function sanitize(s) { return (s || '').trim(); }

  async function api(url, method = 'GET', data = null) {
    const options = { method };
    if (data) {
      options.headers = { 'Content-Type': 'application/x-www-form-urlencoded' };
      const params = new URLSearchParams();
      Object.entries(data).forEach(([k, v]) => params.append(k, v));
      options.body = params.toString();
    }
    const res = await fetch(url, options);
    let json;
    try {
      json = await res.json();
    } catch(e) {
      json = { status: 'error', message: 'Unexpected server response.' };
    }
    return json;
  }

  async function loadCategories() {
    if (!catSelect) return;
    catSelect.innerHTML = '<option value="">Loading...</option>';
    const resp = await api(endpoints.cats, 'GET');
    if (resp.status === 'success' && Array.isArray(resp.data)) {
      if (resp.data.length === 0) {
        catSelect.innerHTML = '<option value="">No categories. Create one first.</option>';
        return;
      }
      catSelect.innerHTML = '<option value="">Select category...</option>';
      resp.data.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.cat_id;
        opt.textContent = c.cat_name;
        catSelect.appendChild(opt);
      });
    } else {
      catSelect.innerHTML = '<option value="">Unable to load categories</option>';
      showMessage(createFeedback, resp.message || 'Failed to load categories.', false);
    }
  }

  function groupByCategory(rows) {
    const map = new Map();
    rows.forEach(r => {
      const key = `${r.cat_id}|${r.cat_name}`;
      if (!map.has(key)) map.set(key, []);
      map.get(key).push(r);
    });
    return map;
  }

  function renderBrands(rows) {
    if (!container) return;
    if (!Array.isArray(rows) || rows.length === 0) {
      container.innerHTML = '<p>No brands yet.</p>';
      return;
    }
    const groups = groupByCategory(rows);
    container.innerHTML = '';
    groups.forEach((items, key) => {
      const [cat_id, cat_name] = key.split('|');
      const section = document.createElement('div');
      section.className = 'card mt-10';
      const header = document.createElement('h4');
      header.textContent = `Category: ${cat_name}`;
      section.appendChild(header);

      const table = document.createElement('table');
      table.className = 'table';
      const thead = document.createElement('thead');
      thead.innerHTML = '<tr><th>ID</th><th>Name</th><th>Actions</th></tr>';
      table.appendChild(thead);
      const tbody = document.createElement('tbody');

      items.forEach(row => {
        const tr = document.createElement('tr');
        const tdId = document.createElement('td'); tdId.textContent = row.brand_id;
        const tdName = document.createElement('td'); tdName.textContent = row.brand_name;
        const tdActions = document.createElement('td'); tdActions.className = 'actions';

        const editBtn = document.createElement('button');
        editBtn.className = 'btn btn-secondary';
        editBtn.textContent = 'Edit';
        editBtn.addEventListener('click', async () => {
          const updated = prompt('Update brand name:', row.brand_name);
          if (updated === null) return;
          const name = sanitize(updated);
          if (!name) { alert('Name cannot be empty.'); return; }
          const resp = await api(endpoints.update, 'POST', { brand_id: row.brand_id, name });
          if (resp.status === 'success') { showMessage(listFeedback, resp.message, true); await loadBrands(); }
          else { showMessage(listFeedback, resp.message || 'Failed to update brand.', false); }
        });

        const delBtn = document.createElement('button');
        delBtn.className = 'btn btn-danger';
        delBtn.textContent = 'Delete';
        delBtn.addEventListener('click', async () => {
          if (!confirm('Are you sure you want to delete this brand?')) return;
          const resp = await api(endpoints.delete, 'POST', { brand_id: row.brand_id });
          if (resp.status === 'success') { showMessage(listFeedback, resp.message, true); await loadBrands(); }
          else { showMessage(listFeedback, resp.message || 'Failed to delete brand.', false); }
        });

        tdActions.appendChild(editBtn);
        tdActions.appendChild(delBtn);

        tr.appendChild(tdId);
        tr.appendChild(tdName);
        tr.appendChild(tdActions);
        tbody.appendChild(tr);
      });

      table.appendChild(tbody);
      section.appendChild(table);
      container.appendChild(section);
    });
  }

  async function loadBrands() {
    if (container) container.innerHTML = '<p>Loading...</p>';
    listFeedback.textContent = '';
    const resp = await api(endpoints.fetch, 'GET');
    if (resp.status === 'success') {
      renderBrands(resp.data || []);
    } else {
      container.innerHTML = '<p>Unable to load brands.</p>';
      showMessage(listFeedback, resp.message || 'Failed to fetch brands.', false);
    }
  }

  if (createForm) {
    createForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      createFeedback.textContent = '';
      const name = sanitize(nameInput.value);
      const cat_id = parseInt(catSelect.value, 10);
      if (!name) { showMessage(createFeedback, 'Please provide a valid brand name.', false); return; }
      if (!cat_id) { showMessage(createFeedback, 'Please select a category.', false); return; }
      const resp = await api(endpoints.add, 'POST', { name, cat_id });
      if (resp.status === 'success') {
        showMessage(createFeedback, resp.message, true);
        createForm.reset();
        await loadBrands();
      } else {
        showMessage(createFeedback, resp.message || 'Failed to create brand.', false);
      }
    });
  }

  // Initial loads
  loadCategories().then(loadBrands);
});
