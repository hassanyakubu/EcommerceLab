document.addEventListener('DOMContentLoaded', () => {
  const bodyEl = document.getElementById('categories-body');
  const listFeedback = document.getElementById('list-feedback');
  const createForm = document.getElementById('create-category-form');
  const createFeedback = document.getElementById('create-feedback');

  const endpoints = {
    fetch: '../actions/fetch_category_action.php',
    add: '../actions/add_category_action.php',
    update: '../actions/update_category_action.php',
    delete: '../actions/delete_category_action.php',
  };

  function sanitizeName(name) {
    return (name || '').trim();
  }

  function showMessage(el, msg, ok = true) {
    if (!el) return;
    el.textContent = msg;
    el.style.color = ok ? '#2f855a' : '#c53030';
  }

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
    } catch (e) {
      json = { status: 'error', message: 'Unexpected response from server.' };
    }
    return json;
  }

  function renderRows(rows) {
    if (!Array.isArray(rows) || rows.length === 0) {
      bodyEl.innerHTML = '<tr><td colspan="3">No categories yet.</td></tr>';
      return;
    }
    bodyEl.innerHTML = '';
    rows.forEach(row => {
      const tr = document.createElement('tr');
      const tdId = document.createElement('td');
      const tdName = document.createElement('td');
      const tdActions = document.createElement('td');
      tdActions.className = 'actions';

      tdId.textContent = row.cat_id;
      tdName.textContent = row.cat_name;

      const editBtn = document.createElement('button');
      editBtn.className = 'btn btn-secondary';
      editBtn.textContent = 'Edit';
      editBtn.addEventListener('click', async () => {
        const current = row.cat_name;
        const updated = prompt('Update category name:', current);
        if (updated === null) return; // cancelled
        const name = sanitizeName(updated);
        if (!name) {
          alert('Name cannot be empty.');
          return;
        }
        const resp = await api(endpoints.update, 'POST', { cat_id: row.cat_id, name });
        if (resp.status === 'success') {
          showMessage(listFeedback, resp.message, true);
          await loadCategories();
        } else {
          showMessage(listFeedback, resp.message || 'Failed to update category.', false);
        }
      });

      const delBtn = document.createElement('button');
      delBtn.className = 'btn btn-danger';
      delBtn.textContent = 'Delete';
      delBtn.addEventListener('click', async () => {
        if (!confirm('Are you sure you want to delete this category?')) return;
        const resp = await api(endpoints.delete, 'POST', { cat_id: row.cat_id });
        if (resp.status === 'success') {
          showMessage(listFeedback, resp.message, true);
          await loadCategories();
        } else {
          showMessage(listFeedback, resp.message || 'Failed to delete category.', false);
        }
      });

      tdActions.appendChild(editBtn);
      tdActions.appendChild(delBtn);

      tr.appendChild(tdId);
      tr.appendChild(tdName);
      tr.appendChild(tdActions);

      bodyEl.appendChild(tr);
    });
  }

  async function loadCategories() {
    bodyEl.innerHTML = '<tr><td colspan="3">Loading...</td></tr>';
    listFeedback.textContent = '';
    const resp = await api(endpoints.fetch, 'GET');
    if (resp.status === 'success') {
      renderRows(resp.data || []);
    } else {
      bodyEl.innerHTML = '<tr><td colspan="3">Unable to load categories.</td></tr>';
      showMessage(listFeedback, resp.message || 'Failed to fetch categories.', false);
    }
  }

  if (createForm) {
    createForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      createFeedback.textContent = '';
      const formData = new FormData(createForm);
      const rawName = formData.get('name');
      const name = sanitizeName(rawName);
      if (!name) {
        showMessage(createFeedback, 'Please provide a valid category name.', false);
        return;
      }
      const resp = await api(endpoints.add, 'POST', { name });
      if (resp.status === 'success') {
        showMessage(createFeedback, resp.message, true);
        createForm.reset();
        await loadCategories();
      } else {
        showMessage(createFeedback, resp.message || 'Failed to create category.', false);
      }
    });
  }

  // Initial load
  loadCategories();
});
