document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementById('productTable')) {
    loadProductTable();
  }

  if (document.getElementById('csvFile')) {
    setupCSVMappingUI();
  }

  if (document.getElementById('manual-product-form')) {
    handleManualProductForm();
  }
});

// ------------------------------
// 1. Load Products Table
// ------------------------------
async function loadProductTable(filters = {}) {
  const table = document.getElementById('productTable');

  try {
    const params = new URLSearchParams(filters);
    const res = await fetch('/backend/products/list.php?' + params.toString());
    const json = await res.json();

    if (!json.success) {
      table.innerHTML = '<p class="text-danger">Failed to load products.</p>';
      return;
    }

    let html = `<table class="table table-bordered align-middle"><thead>
      <tr><th>Title</th><th>Type</th><th>Variants</th><th>Tags</th><th>Price</th><th>Actions</th></tr>
    </thead><tbody>`;

    for (const p of json.products) {
      const priceRange = await getPriceRange(p.id);
      html += `<tr>
        <td>${p.title}</td>
        <td>${p.type}</td>
        <td>${p.variant_count}</td>
        <td>${p.tags || ''}</td>
        <td>${priceRange}</td>
        <td>
          <button class="btn btn-sm btn-primary" onclick="openEditProductModal(${p.id})">Edit</button>
          <button class="btn btn-sm btn-danger" onclick="deleteProduct(${p.id})">Delete</button>
        </td>
      </tr>`;
    }

    html += '</tbody></table>';
    table.innerHTML = html;
  } catch (err) {
    console.error('❌ Error loading product list:', err);
    table.innerHTML = '<p class="text-danger">Server error while loading products.</p>';
  }
}

async function getPriceRange(id) {
  try {
    const res = await fetch('/api/products.php?action=price_range&id=' + id);
    const json = await res.json();
    return json.range || '–';
  } catch (e) {
    return '–';
  }
}

async function deleteProduct(id) {
  if (!confirm("Are you sure you want to delete this product?")) return;

  try {
    const res = await fetch('/backend/products/delete.php', {
      method: 'POST',
      body: new URLSearchParams({ id })
    });
    const json = await res.json();
    if (json.success) {
      loadProductTable();
    } else {
      alert('Delete failed.');
    }
  } catch (err) {
    console.error('❌ Delete Error:', err);
    alert('❌ Could not delete product. Server error.');
  }
}

// ------------------------------
// 2. CSV Mapping + Import Mode
// ------------------------------
function setupCSVMappingUI() {
  const csvInput = document.getElementById('csvFile');
  const mappingSection = document.getElementById('mapping-section');
  const mappingTable = document.getElementById('mappingTable');
  const importForm = document.getElementById('product-import-form');

  const requiredFields = ['title', 'sku', 'price', 'image', 'type'];
  let uploadedHeaders = [];

  csvInput.addEventListener('change', async function () {
    const file = this.files[0];
    if (!file) return;

    const text = await file.text();
    const firstLine = text.split('\n')[0];
    uploadedHeaders = firstLine.split(',').map(h => h.trim().replace(/['"]+/g, ''));

    mappingSection.classList.remove('d-none');
    mappingTable.innerHTML = '';

    requiredFields.forEach(field => {
      const row = document.createElement('tr');
      const labelCell = document.createElement('td');
      labelCell.textContent = field;

      const selectCell = document.createElement('td');
      const select = document.createElement('select');
      select.name = `map[${field}]`;
      select.className = 'form-select';

      const defaultOption = new Option('-- Select Column --', '');
      select.appendChild(defaultOption);

      uploadedHeaders.forEach(header => {
        const opt = new Option(header, header);
        if (header.toLowerCase().includes(field)) opt.selected = true;
        select.appendChild(opt);
      });

      selectCell.appendChild(select);
      row.appendChild(labelCell);
      row.appendChild(selectCell);
      mappingTable.appendChild(row);
    });
  });

  importForm.addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(importForm);

    // Get import mode from radio buttons
    const mode = document.querySelector('input[name="mode"]:checked')?.value || 'append';
    if (mode === 'replace' && !confirm("⚠️ This will delete all existing products. Are you sure?")) return;

    formData.append('mode', mode);

    try {
      const res = await fetch('/backend/products/import.php', {
        method: 'POST',
        body: formData
      });

      const text = await res.text();
      let result;

      try {
        result = JSON.parse(text);
      } catch (err) {
        console.error('❌ Server returned invalid JSON:\n\n', text);
        alert('❌ Import failed: Server returned invalid response.\nCheck browser console.');
        return;
      }

      if (result.success) {
        alert('✅ Products imported successfully!');
        location.reload();
      } else {
        alert('❌ Import failed: ' + (result.message || 'Unknown error'));
        console.error('❌ Import API Error:', result);
      }
    } catch (err) {
      console.error('❌ Import Request Failed:', err);
      alert('❌ Server error while importing. Check console for details.');
    }
  });
}

// ------------------------------
// 3. Manual Product Submit
// ------------------------------
function handleManualProductForm() {
  document.getElementById('manual-product-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    try {
      const res = await fetch('/backend/products/create.php', {
        method: 'POST',
        body: formData
      });

      const json = await res.json();
      alert(json.success ? '✅ Product saved' : '❌ Error: ' + (json.message || 'Unknown'));

      if (json.success) location.reload();
    } catch (err) {
      console.error('❌ Product Create Error:', err);
      alert('❌ Server error. Please try again.');
    }
  });
}

// ------------------------------
// 4. Edit Product Submit
// ------------------------------
async function openEditProductModal(id) {
  if (!id || isNaN(id)) {
    alert('Invalid product ID');
    return;
  }

  try {
    const res = await fetch(`/backend/products/edit.php?id=${id}`); // ✅ GET, not POST
    const html = await res.text(); // ✅ Expecting HTML
    document.getElementById('modal-edit-body').innerHTML = html;

    const modal = new bootstrap.Modal(document.getElementById('modal-edit-product'));
    modal.show();
  } catch (err) {
    console.error('❌ Edit modal load failed:', err);
    alert('❌ Failed to load product');
  }
}

// ------------------------------
// 1. Load Products Table
// ------------------------------
async function loadProductTable(filters = {}) {
  const table = document.getElementById('productTable');

  try {
    const params = new URLSearchParams(filters);
    const res = await fetch('/backend/products/list.php?' + params.toString());
    const json = await res.json();

    if (!json.success) {
      table.innerHTML = '<p class="text-danger">Failed to load products.</p>';
      return;
    }

    let html = `<table class="table table-bordered align-middle"><thead>
      <tr><th>Title</th><th>Type</th><th>Variants</th><th>Tags</th><th>Price</th><th>Actions</th></tr>
    </thead><tbody>`;

    for (const p of json.products) {
      const priceRange = await getPriceRange(p.id);
      html += `<tr>
        <td>${p.title}</td>
        <td>${p.type}</td>
        <td>${p.variant_count}</td>
        <td>${p.tags || ''}</td>
        <td>${priceRange}</td>
        <td>
          <button class="btn btn-sm btn-primary" onclick="openEditProductModal(${p.id})">Edit</button>
          <button class="btn btn-sm btn-danger" onclick="deleteProduct(${p.id})">Delete</button>
        </td>
      </tr>`;
    }

    html += '</tbody></table>';
    table.innerHTML = html;
  } catch (err) {
    console.error('❌ Error loading product list:', err);
    table.innerHTML = '<p class="text-danger">Server error while loading products.</p>';
  }
}

async function getPriceRange(id) {
  try {
    const res = await fetch('/api/products.php?action=price_range&id=' + id);
    const json = await res.json();
    return json.range || '–';
  } catch (e) {
    return '–';
  }
}

async function deleteProduct(id) {
  if (!confirm("Are you sure you want to delete this product?")) return;

  try {
    const res = await fetch('/backend/products/delete.php', {
      method: 'POST',
      body: new URLSearchParams({ id })
    });
    const json = await res.json();
    if (json.success) {
      loadProductTable();
    } else {
      alert('Delete failed.');
    }
  } catch (err) {
    console.error('❌ Delete Error:', err);
    alert('❌ Could not delete product. Server error.');
  }
}

// ------------------------------
// 2. CSV Mapping + Import Mode
// ------------------------------
function setupCSVMappingUI() {
  const csvInput = document.getElementById('csvFile');
  const mappingSection = document.getElementById('mapping-section');
  const mappingTable = document.getElementById('mappingTable');
  const importForm = document.getElementById('product-import-form');

  const requiredFields = ['title', 'sku', 'price', 'image', 'type'];
  let uploadedHeaders = [];

  csvInput.addEventListener('change', async function () {
    const file = this.files[0];
    if (!file) return;

    const text = await file.text();
    const firstLine = text.split('\n')[0];
    uploadedHeaders = firstLine.split(',').map(h => h.trim().replace(/['"]+/g, ''));

    mappingSection.classList.remove('d-none');
    mappingTable.innerHTML = '';

    requiredFields.forEach(field => {
      const row = document.createElement('tr');
      const labelCell = document.createElement('td');
      labelCell.textContent = field;

      const selectCell = document.createElement('td');
      const select = document.createElement('select');
      select.name = `map[${field}]`;
      select.className = 'form-select';

      const defaultOption = new Option('-- Select Column --', '');
      select.appendChild(defaultOption);

      uploadedHeaders.forEach(header => {
        const opt = new Option(header, header);
        if (header.toLowerCase().includes(field)) opt.selected = true;
        select.appendChild(opt);
      });

      selectCell.appendChild(select);
      row.appendChild(labelCell);
      row.appendChild(selectCell);
      mappingTable.appendChild(row);
    });
  });

  importForm.addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(importForm);

    // Get import mode from radio buttons
    const mode = document.querySelector('input[name="mode"]:checked')?.value || 'append';
    if (mode === 'replace' && !confirm("⚠️ This will delete all existing products. Are you sure?")) return;

    formData.append('mode', mode);

    try {
      const res = await fetch('/backend/products/import.php', {
        method: 'POST',
        body: formData
      });

      const text = await res.text();
      let result;

      try {
        result = JSON.parse(text);
      } catch (err) {
        console.error('❌ Server returned invalid JSON:\n\n', text);
        alert('❌ Import failed: Server returned invalid response.\nCheck browser console.');
        return;
      }

      if (result.success) {
        alert('✅ Products imported successfully!');
        location.reload();
      } else {
        alert('❌ Import failed: ' + (result.message || 'Unknown error'));
        console.error('❌ Import API Error:', result);
      }
    } catch (err) {
      console.error('❌ Import Request Failed:', err);
      alert('❌ Server error while importing. Check console for details.');
    }
  });
}

// ------------------------------
// 3. Manual Product Submit
// ------------------------------
function handleManualProductForm() {
  document.getElementById('manual-product-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    try {
      const res = await fetch('/backend/products/create.php', {
        method: 'POST',
        body: formData
      });

      const json = await res.json();
      alert(json.success ? '✅ Product saved' : '❌ Error: ' + (json.message || 'Unknown'));

      if (json.success) location.reload();
    } catch (err) {
      console.error('❌ Product Create Error:', err);
      alert('❌ Server error. Please try again.');
    }
  });
}

// ------------------------------
// 4. Edit Product Submit
// ------------------------------
async function openEditProductModal(id) {
  if (!id || isNaN(id)) {
    alert('Invalid product ID');
    return;
  }

  try {
    const res = await fetch(`/backend/products/edit.php?id=${id}`); // ✅ GET, not POST
    const html = await res.text(); // ✅ Expecting HTML
    document.getElementById('modal-edit-body').innerHTML = html;

    const modal = new bootstrap.Modal(document.getElementById('modal-edit-product'));
    modal.show();
  } catch (err) {
    console.error('❌ Edit modal load failed:', err);
    alert('❌ Failed to load product');
  }
}

