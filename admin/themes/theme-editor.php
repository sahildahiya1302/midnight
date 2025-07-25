<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../db.php';
require_once __DIR__ . '/../../functions.php';


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

$pageTitle = 'Editor';

// Load theme pages layout JSON and global settings from DB or fallback to defaults
// For demo, load from JSON files or stub data

$themeId = $_GET['theme_id'] ?? 1;
$pageType = $_GET['page'] ?? 'index';

// Load layout JSON for the page
$templateSlug = $pageType;
$layoutJsonPath = __DIR__ . "/../../themes/default/templates/{$templateSlug}.json";
$layoutJson = file_exists($layoutJsonPath) ? file_get_contents($layoutJsonPath) : '[]';
$layout = json_decode($layoutJson, true);

// Load global theme settings
$settingsJsonPath = __DIR__ . "/../../themes/default/config/settings_data.json";
$settingsJson = file_exists($settingsJsonPath) ? file_get_contents($settingsJsonPath) : '{}';
$settings = json_decode($settingsJson, true);

// Load available sections from schema files
$sectionsDir = __DIR__ . "/../../themes/default/sections";
$sectionSchemas = [];
foreach (glob($sectionsDir . "/*.schema.json") as $schemaFile) {
    $schemaContent = file_get_contents($schemaFile);
    $schema = json_decode($schemaContent, true);
    if ($schema && isset($schema['name'])) {
        $sectionSchemas[basename($schemaFile, '.schema.json')] = $schema;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Theme Editor - Pages Tab</title>
<link rel="stylesheet" href="/admin/assets/theme-editor.css" />
</head>
<body class="theme-editor">
  <div class="editor-header">
    <h1>Theme Editor</h1>
    <div class="header-actions">
      <select id="page-select" aria-label="Select page to edit">
        <option value="">-- Select Page --</option>
      </select>
      <button id="save-layout-btn">Save Draft</button>
      <button id="publish-layout-btn">Publish</button>
      <a href="/admin/themes/code-editor.php" target="_blank" class="code-link">Edit Code</a>
      <div class="device-toggle">
        <button type="button" data-width="100%" class="active" id="device-desktop">Desktop</button>
        <button type="button" data-width="768px" id="device-tablet">Tablet</button>
        <button type="button" data-width="375px" id="device-mobile">Mobile</button>
      </div>
    </div>
  </div>

  <!-- Add Section Modal -->
  <div id="add-section-modal" class="modal">
    <div class="modal-content">
      <button type="button" class="close-modal" aria-label="Close">&times;</button>
      <input type="text" id="section-search" placeholder="Search sections" />
      <div id="section-card-list">
        <?php foreach ($sectionSchemas as $key => $schema): ?>
          <div class="section-card" data-type="<?php echo htmlspecialchars($key); ?>">
            <img src="/themes/default/sections/<?php echo htmlspecialchars($key); ?>.png" alt="" onerror="this.style.display='none'" />
            <h4><?php echo htmlspecialchars($schema['name']); ?></h4>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="sidebar">
      <h2>Page Sections (<span id="page-type-label"><?php echo htmlspecialchars($pageType); ?></span>)</h2>
      <div class="sidebar-search">
        <input type="text" id="section-filter" placeholder="Search sections and blocks..." />
      </div>
      <div id="section-list" aria-live="polite" aria-relevant="additions removals">
        <!-- Sections will be loaded here -->
      </div>
      <div id="version-history">
        <h3>Version History</h3>
        <ul id="version-list"></ul>
      </div>
    </div>
    <div class="content">
      <h2>Live Preview</h2>
      <iframe id="preview-frame" src="/?preview_theme_id=<?php echo $themeId; ?>&page=<?php echo htmlspecialchars($pageType); ?>"></iframe>
    </div>
  </div>

<script>
    let selectedSectionIndex = null;
    let layoutDirty = false;
  const pageSelect = document.getElementById('page-select');
  const pageTypeLabel = document.getElementById('page-type-label');
  const sectionList = document.getElementById('section-list');
  const modal = document.getElementById('add-section-modal');
  const sectionSearch = document.getElementById('section-search');
  const searchInput = document.getElementById('section-filter');
  const previewFrame = document.getElementById('preview-frame');
  const deviceButtons = document.querySelectorAll('.device-toggle button');

  let addTargetGroup = 'template';
  let addTargetIndex = null;

  let currentLayout = [];
  let currentPage = '<?php echo htmlspecialchars($pageType); ?>';

  // Load pages for page selector
  async function loadPages() {
    const response = await fetch('/admin/api/get-pages.php');
    if (!response.ok) return;
    const pages = await response.json();
    pageSelect.innerHTML = '<option value="">-- Select Page --</option>';
    pages.forEach(page => {
      const option = document.createElement('option');
      option.value = page;
      option.textContent = (page === 'index') ? 'Home' : (page.charAt(0).toUpperCase() + page.slice(1));
      if (page === currentPage) option.selected = true;
      pageSelect.appendChild(option);
    });
  }

  // Load layout for selected page
  async function loadLayout(page) {
    if (!page) return;
    
let fileSlug = page;

    const response = await fetch(`/admin/api/get-page-layout.php?page=${fileSlug}`);
    if (!response.ok) {
      alert('Failed to load page layout');
      return;
    }
    const layout = await response.json();
    console.log('Loaded layout:', layout);
    if (!layout || layout.error) {
      alert(layout?.error || 'Invalid layout format received');
      currentLayout = [];
    } else if (Array.isArray(layout.sections)) {
      currentLayout = layout.sections;
    } else if (layout.sections && typeof layout.sections === 'object' && Array.isArray(layout.order)) {
      // Convert old format to array
      currentLayout = layout.order.map(key => layout.sections[key]).filter(Boolean);
    } else {
      alert('Invalid layout format received');
      currentLayout = [];
    }
    currentPage = page; // Move this before updatePreview
    pageTypeLabel.textContent = page.charAt(0).toUpperCase() + page.slice(1);
    renderSections();
    updatePreview();
  }

  // Categorize sections into groups
  function categorizeSections() {
    const groups = { header: [], template: [], footer: [] };
    currentLayout.forEach((sec, idx) => {
      if (sec.type.includes('header') || sec.type.includes('announcement')) {
        groups.header.push({ section: sec, index: idx });
      } else if (sec.type.includes('footer')) {
        groups.footer.push({ section: sec, index: idx });
      } else {
        groups.template.push({ section: sec, index: idx });
      }
    });
    return groups;
  }

  // Build single list item
  function buildSectionItem(section, index) {
    const li = document.createElement('li');
    li.className = 'section-item';
    if (selectedSectionIndex === index) li.classList.add('active');
    li.draggable = true;
    li.dataset.index = index;

    const handle = document.createElement('span');
    handle.textContent = '\u2837';
    handle.className = 'drag-handle';
    li.appendChild(handle);

    const nameSpan = document.createElement('span');
    nameSpan.textContent = section.type;
    li.appendChild(nameSpan);

    const actions = document.createElement('span');
    actions.className = 'actions';

    const dupBtn = document.createElement('button');
    dupBtn.textContent = '⧉';
    dupBtn.title = 'Duplicate';
    dupBtn.addEventListener('click', e => { e.stopPropagation(); duplicateSection(index); });
    actions.appendChild(dupBtn);

    const delBtn = document.createElement('button');
    delBtn.textContent = '🗑';
    delBtn.title = 'Delete';
    delBtn.addEventListener('click', e => {
      e.stopPropagation();
      if (confirm('Are you sure you want to delete this section?')) deleteSection(index);
    });
    actions.appendChild(delBtn);

    const codeBtn = document.createElement('button');
    codeBtn.textContent = '</>';
    codeBtn.title = 'Edit code';
    codeBtn.addEventListener('click', async e => {
      e.stopPropagation();
      const res = await fetch(`/admin/api/get-section-code.php?section=${section.type}`);
      if (!res.ok) return alert('Failed to load code');
      const data = await res.json();
      const updated = prompt('Edit code for ' + section.type, data.code);
      if (updated !== null) {
        await fetch('/admin/api/save-section-code.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ section: section.type, code: updated }) });
      }
    });
    actions.appendChild(codeBtn);

    li.appendChild(actions);

    if (Array.isArray(section.blocks) && section.blocks.length) {
      const bUl = document.createElement('ul');
      bUl.className = 'block-list';
      section.blocks.forEach(b => {
        const bi = document.createElement('li');
        bi.className = 'block-item';
        bi.textContent = b.type;
        bUl.appendChild(bi);
      });
      li.appendChild(bUl);
    }

    li.addEventListener('click', () => {
      sectionList.style.display = 'none';
      selectedSectionIndex = index;
      renderSections();
      showCustomizer(index);
    });

    return li;
  }

  function buildAddLine(pos) {
    const li = document.createElement('li');
    li.className = 'add-section-line';
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.textContent = '+ Add Section';
    btn.addEventListener('click', e => {
      e.stopPropagation();
      addTargetIndex = pos;
      modal.style.display = 'flex';
      sectionSearch.value = '';
      filterCards('');
    });
    li.appendChild(btn);
    return li;
  }

  function showCustomizer(index) {
    let customizer = document.getElementById('section-customizer');
    if (!customizer) {
      customizer = document.createElement('div');
      customizer.id = 'section-customizer';
      customizer.style.flexGrow = '1';
      customizer.style.display = 'block';
      customizer.style.padding = '10px';
      customizer.style.borderTop = '1px solid #ccc';
      customizer.style.overflowY = 'auto';
      customizer.style.flexBasis = '100%';
      document.querySelector('.sidebar').appendChild(customizer);
    } else {
      customizer.style.display = 'block';
    }

    let backBtn = document.getElementById('back-to-sections-btn');
    if (!backBtn) {
      backBtn = document.createElement('button');
      backBtn.id = 'back-to-sections-btn';
      backBtn.textContent = 'Back to Sections';
      backBtn.style.marginBottom = '10px';
      backBtn.style.fontWeight = 'bold';
      backBtn.addEventListener('click', () => {
        sectionList.style.display = 'block';
        customizer.style.display = 'none';
        const form = document.getElementById('customizer-form');
        if (form) form.innerHTML = '';
        selectedSectionIndex = null;
        backBtn.remove();
        renderSections();
      });
      customizer.insertBefore(backBtn, customizer.firstChild);
    }

    openCustomizerForSection(index);
  }

  function renderSections() {
    sectionList.innerHTML = '';
    const groups = categorizeSections();
    const filter = (searchInput.value || '').toLowerCase();
    ['header','template','footer'].forEach(g => {
      const arr = groups[g];
      const wrapper = document.createElement('div');
      wrapper.className = 'section-group';
      const header = document.createElement('div');
      header.className = 'section-group-header';
      header.innerHTML = `<span>${g.toUpperCase()}</span><span class="chevron">▾</span>`;
      header.addEventListener('click', () => wrapper.classList.toggle('collapsed'));
      wrapper.appendChild(header);
      const ul = document.createElement('ul');
      ul.className = 'section-group-list';
      arr.forEach((item, idx) => {
        if (idx === 0) ul.appendChild(buildAddLine(item.index));
        if (filter && !item.section.type.toLowerCase().includes(filter)) {
          return ul.appendChild(buildAddLine(item.index + 1));
        }
        ul.appendChild(buildSectionItem(item.section, item.index));
        ul.appendChild(buildAddLine(item.index + 1));
      });
      wrapper.appendChild(ul);
      sectionList.appendChild(wrapper);
    });
    addDragAndDropHandlers();
  }

  // Duplicate section function
  function duplicateSection(index) {
    if (index < 0 || index >= currentLayout.length) return;
    const sectionToDuplicate = currentLayout[index];
    const newId = `${sectionToDuplicate.type}-${Date.now()}`;
    const duplicatedSection = JSON.parse(JSON.stringify(sectionToDuplicate));
    duplicatedSection.id = newId;
    currentLayout.splice(index + 1, 0, duplicatedSection);
    renderSections();
    layoutDirty = true;
    updatePreview();
  }

  // Delete section function
  function deleteSection(index) {
    if (index < 0 || index >= currentLayout.length) return;
    currentLayout.splice(index, 1);
    renderSections();
    layoutDirty = true;
    updatePreview();
  }
  // Open customization panel for a section
  async function openCustomizerForSection(index) {
    selectedSectionIndex = index;
    const section = currentLayout[index];
    if (!section) return;
    let form = document.getElementById("customizer-form");
    let customizer = document.getElementById('section-customizer');
    if (!customizer) {
      customizer = document.createElement('div');
      customizer.id = 'section-customizer';
      customizer.style.flexGrow = '1';
      customizer.style.display = 'block';
      customizer.style.padding = '10px';
      customizer.style.borderTop = '1px solid #ccc';
      customizer.style.overflowY = 'auto';
      customizer.style.flexBasis = '100%';
      document.querySelector('.sidebar').appendChild(customizer);
    }
    if (!form) {
      form = document.createElement("form");
      form.id = "customizer-form";
      form.style.display = "flex";
      form.style.flexDirection = "column";
      form.style.gap = "10px";
      customizer.appendChild(form);
    }
    form.innerHTML = "";
    const response = await fetch(`/admin/api/get-section-schema.php?section=${section.type}`);
    if (!response.ok) {
      form.innerHTML = "<p>Failed to load section schema.</p>";
      return;
    }
    const schema = await response.json();
    if (!schema || !schema.settings) {
      form.innerHTML = "<p>No customization available.</p>";
      return;
    }
    schema.settings.forEach(setting => {
      const wrapper = document.createElement("div");
      wrapper.style.marginBottom = "10px";
      const label = document.createElement("label");
      label.textContent = setting.label || setting.id;
      label.htmlFor = setting.id;
      wrapper.appendChild(label);
      let input;
      switch (setting.type) {
        case "textarea":
          input = document.createElement("textarea");
          break;
        case "select":
          input = document.createElement("select");
          (setting.options || []).forEach(opt => {
            const optEl = document.createElement("option");
            optEl.value = opt.value;
            optEl.textContent = opt.label;
            if (section.settings[setting.id] === opt.value) optEl.selected = true;
            input.appendChild(optEl);
          });
          break;
        case "image":
          input = document.createElement("input");
          input.type = "file";
          input.accept = "image/*";
          let preview = null;
          if (section.settings[setting.id]) {
            preview = document.createElement('img');
            preview.src = section.settings[setting.id];
            preview.style.maxWidth = '100%';
            wrapper.appendChild(preview);
          }
          input.addEventListener('change', async () => {
            if (!input.files.length) return;
            const fd = new FormData();
            fd.append('image', input.files[0]);
            try {
              const r = await fetch('/backend/themes/upload-image.php', { method: 'POST', body: fd });
              const d = await r.json();
              if (d.success) {
                section.settings[setting.id] = '/uploads/themes/images/' + d.filename;
                if (!preview) {
                  preview = document.createElement('img');
                  preview.style.maxWidth = '100%';
                  wrapper.insertBefore(preview, input);
                }
                preview.src = section.settings[setting.id];
                layoutDirty = true;
                updatePreview();
              } else {
                alert(d.error || 'Upload failed');
              }
            } catch (e) {
              console.error('Image upload failed', e);
              alert('Image upload failed');
            }
          });
          break;
        case "collection_select":
          input = document.createElement("select");
          input.innerHTML = '<option value="">Loading...</option>';
          fetch('/admin/api/get-collections.php')
            .then(r => r.json())
            .then(d => {
              input.innerHTML = '<option value="">-- Select Collection --</option>';
              if (d.success) {
                d.collections.forEach(c => {
                  const opt = document.createElement('option');
                  opt.value = c.id;
                  opt.textContent = c.title || c.name;
                  if (section.settings[setting.id] == c.id) opt.selected = true;
                  input.appendChild(opt);
                });
              }
            });
          break;
        case "product_select":
        case "product":
          input = document.createElement("select");
          input.innerHTML = '<option value="">Loading...</option>';
          fetch('/admin/api/get-products.php')
            .then(r => r.json())
            .then(d => {
              input.innerHTML = '<option value="">-- Select Product --</option>';
              if (d.success) {
                d.products.forEach(p => {
                  const opt = document.createElement('option');
                  opt.value = p.id;
                  opt.textContent = p.title;
                  if (section.settings[setting.id] == p.id) opt.selected = true;
                  input.appendChild(opt);
                });
              }
            });
          break;
        case "product_set_select":
          input = document.createElement("select");
          input.innerHTML = '<option value="">Loading...</option>';
          fetch('/admin/api/get-product-sets.php')
            .then(r => r.json())
            .then(d => {
              input.innerHTML = '<option value="">-- Select Set --</option>';
              if (d.success) {
                d.sets.forEach(s => {
                  const opt = document.createElement('option');
                  opt.value = s.id;
                  opt.textContent = s.title;
                  if (section.settings[setting.id] == s.id) opt.selected = true;
                  input.appendChild(opt);
                });
              }
            });
          break;
        case "checkbox":
          input = document.createElement("input");
          input.type = "checkbox";
          input.checked = !!section.settings[setting.id];
          break;
        default:
          input = document.createElement("input");
          input.type = setting.type || "text";
          input.value = section.settings[setting.id] || "";
      }
      input.id = setting.id;
      input.name = setting.id;
      const onChange = () => {
        if (setting.type === "checkbox") {
          section.settings[setting.id] = input.checked;
        } else {
          section.settings[setting.id] = input.value;
        }
        layoutDirty = true;
        updatePreview();
      };
      input.addEventListener("input", onChange);
      input.addEventListener("change", onChange);
      wrapper.appendChild(input);
      form.appendChild(wrapper);
    });

    // Add style presets selector
    const stylePresets = ['default', 'light', 'dark', 'full-width'];
    const presetWrapper = document.createElement("div");
    presetWrapper.style.marginBottom = "10px";
    const presetLabel = document.createElement("label");
    presetLabel.textContent = "Style Preset";
    presetLabel.htmlFor = "style-preset-select";
    presetWrapper.appendChild(presetLabel);

    const presetSelect = document.createElement("select");
    presetSelect.id = "style-preset-select";
    presetSelect.name = "style-preset-select";

    stylePresets.forEach(preset => {
      const option = document.createElement("option");
      option.value = preset;
      option.textContent = preset.charAt(0).toUpperCase() + preset.slice(1);
      if (section.settings.stylePreset === preset) option.selected = true;
      presetSelect.appendChild(option);
    });

    presetSelect.addEventListener("change", () => {
      section.settings.stylePreset = presetSelect.value;
      layoutDirty = true;
      updatePreview();
    });

    presetWrapper.appendChild(presetSelect);
    form.appendChild(presetWrapper);

    // Add device visibility toggles
    const visibilityWrapper = document.createElement("div");
    visibilityWrapper.style.marginBottom = "10px";

    const visibilityLabel = document.createElement("label");
    visibilityLabel.textContent = "Visibility";
    visibilityWrapper.appendChild(visibilityLabel);

    const mobileCheckbox = document.createElement("input");
    mobileCheckbox.type = "checkbox";
    mobileCheckbox.id = "visibility-mobile";
    mobileCheckbox.name = "visibility-mobile";
    mobileCheckbox.checked = section.settings.visibility?.mobile !== false; // default true
    const mobileLabel = document.createElement("label");
    mobileLabel.htmlFor = "visibility-mobile";
    mobileLabel.textContent = "Show on Mobile";

    const desktopCheckbox = document.createElement("input");
    desktopCheckbox.type = "checkbox";
    desktopCheckbox.id = "visibility-desktop";
    desktopCheckbox.name = "visibility-desktop";
    desktopCheckbox.checked = section.settings.visibility?.desktop !== false; // default true
    const desktopLabel = document.createElement("label");
    desktopLabel.htmlFor = "visibility-desktop";
    desktopLabel.textContent = "Show on Desktop";

    mobileCheckbox.addEventListener("change", () => {
      if (!section.settings.visibility) section.settings.visibility = {};
      section.settings.visibility.mobile = mobileCheckbox.checked;
      layoutDirty = true;
      updatePreview();
    });

    desktopCheckbox.addEventListener("change", () => {
      if (!section.settings.visibility) section.settings.visibility = {};
      section.settings.visibility.desktop = desktopCheckbox.checked;
      layoutDirty = true;
      updatePreview();
    });

    visibilityWrapper.appendChild(mobileCheckbox);
    visibilityWrapper.appendChild(mobileLabel);
    visibilityWrapper.appendChild(document.createElement("br"));
    visibilityWrapper.appendChild(desktopCheckbox);
    visibilityWrapper.appendChild(desktopLabel);

    form.appendChild(visibilityWrapper);

    // ----- Blocks Management -----
    if (Array.isArray(schema.blocks) && schema.blocks.length) {
      const blocksWrapper = document.createElement('div');
      blocksWrapper.style.marginTop = '10px';

      const blocksHeader = document.createElement('h3');
      blocksHeader.textContent = 'Blocks';
      blocksWrapper.appendChild(blocksHeader);

      const blockList = document.createElement('div');
      blocksWrapper.appendChild(blockList);

      function renderBlocks() {
        blockList.innerHTML = '';

        const buildAddBlock = (pos) => {
          const wrap = document.createElement('div');
          wrap.className = 'add-block-line';
          const sel = addSelect.cloneNode(true);
          sel.value = '';
          const btn = document.createElement('button');
          btn.textContent = 'Add Block';
          btn.addEventListener('click', () => {
            const type = sel.value;
            if (!type) return;
            const bSchema = schema.blocks.find(b => b.type === type) || {settings:[]};
            const settings = {};
            (bSchema.settings || []).forEach(s => { settings[s.id] = s.default ?? ''; });
            if (!Array.isArray(section.blocks)) section.blocks = [];
            section.blocks.splice(pos, 0, {type, settings});
            layoutDirty = true;
            renderBlocks();
            updatePreview();
          });
          wrap.appendChild(sel);
          wrap.appendChild(btn);
          return wrap;
        };

        const addLineStart = buildAddBlock(0);
        blockList.appendChild(addLineStart);

        (section.blocks || []).forEach((block, bIndex) => {
          const blockDiv = document.createElement('div');
          blockDiv.style.border = '1px solid #ccc';
          blockDiv.style.padding = '5px';
          blockDiv.style.marginBottom = '5px';

          const title = document.createElement('strong');
          title.textContent = block.type;
          blockDiv.appendChild(title);

          const del = document.createElement('button');
          del.textContent = 'Delete';
          del.style.marginLeft = '10px';
          del.addEventListener('click', () => {
            section.blocks.splice(bIndex, 1);
            layoutDirty = true;
            renderBlocks();
            updatePreview();
          });
          blockDiv.appendChild(del);

          const bSchema = schema.blocks.find(b => b.type === block.type) || {settings:[]};
          (bSchema.settings || []).forEach(bs => {
            const lbl = document.createElement('label');
            lbl.textContent = bs.label || bs.id;
            lbl.style.display = 'block';
            let inp;
            if (bs.type === 'textarea') {
              inp = document.createElement('textarea');
              inp.value = block.settings[bs.id] || '';
              inp.addEventListener('input', () => {
                block.settings[bs.id] = inp.value;
                layoutDirty = true;
                updatePreview();
              });
              blockDiv.appendChild(lbl);
              blockDiv.appendChild(inp);
              return;
            }

            if (bs.type === 'image') {
              inp = document.createElement('input');
              inp.type = 'file';
              inp.accept = 'image/*';
              let imgPrev = null;
              if (block.settings[bs.id]) {
                imgPrev = document.createElement('img');
                imgPrev.src = block.settings[bs.id];
                imgPrev.style.maxWidth = '100%';
                blockDiv.appendChild(imgPrev);
              }
              inp.addEventListener('change', async () => {
                if (!inp.files.length) return;
                const fd = new FormData();
                fd.append('image', inp.files[0]);
                try {
                  const res = await fetch('/backend/themes/upload-image.php', { method: 'POST', body: fd });
                  const data = await res.json();
                  if (data.success) {
                    block.settings[bs.id] = '/uploads/themes/images/' + data.filename;
                    if (!imgPrev) {
                      imgPrev = document.createElement('img');
                      imgPrev.style.maxWidth = '100%';
                      blockDiv.insertBefore(imgPrev, inp);
                    }
                    imgPrev.src = block.settings[bs.id];
                    layoutDirty = true;
                    updatePreview();
                  } else {
                    alert(data.error || 'Upload failed');
                  }
                } catch (e) {
                  console.error('Image upload failed', e);
                  alert('Image upload failed');
                }
              });
            } else {
              inp = document.createElement('input');
              inp.type = bs.type || 'text';
              inp.value = block.settings[bs.id] || '';
              inp.addEventListener('input', () => {
                block.settings[bs.id] = inp.value;
                layoutDirty = true;
                updatePreview();
              });
            }
            blockDiv.appendChild(lbl);
            blockDiv.appendChild(inp);
          });

          blockList.appendChild(blockDiv);
          blockList.appendChild(buildAddBlock(bIndex + 1));
        });
      }
      const addSelect = document.createElement('select');
      const blank = document.createElement('option');
      blank.value = '';
      blank.textContent = '-- Block Type --';
      addSelect.appendChild(blank);
      schema.blocks.forEach(b => {
        const opt = document.createElement('option');
        opt.value = b.type;
        opt.textContent = b.name || b.type;
        addSelect.appendChild(opt);
      });
      blocksWrapper.appendChild(blockList);
      form.appendChild(blocksWrapper);
      renderBlocks();
    }

  }

  // Save button click handler
  document.getElementById('save-layout-btn').addEventListener('click', async () => {
    if (!layoutDirty) {
      alert('No changes to save.');
      return;
    }

    try {
      await saveLayout();
      alert('Layout and section settings saved successfully!');
      layoutDirty = false;
      updatePreview();

      // Save version after successful save
      await saveLayoutVersion();

    } catch (error) {
      console.error('Error while saving:', error);
      alert('Something went wrong while saving. Please try again.');
    }
  });

  // Auto-save draft every 30 seconds if layout is dirty
  setInterval(() => {
    if (layoutDirty) {
      saveLayoutVersion(true);
    }
  }, 30000);

  // Save layout version function
  async function saveLayoutVersion(isAutoSave = false) {
    const layoutObj = { sections: {}, order: [] };
    currentLayout.forEach(sec => {
      const id = sec.id || `${sec.type}-${Date.now().toString(36)}-${Math.random().toString(36).slice(2)}`;
      sec.id = id;
      layoutObj.sections[id] = { type: sec.type, settings: sec.settings || {}, blocks: sec.blocks || [] };
      layoutObj.order.push(id);
    });

    const payload = { page: currentPage, layout: layoutObj };

    if (!isAutoSave) {
      // Prompt user for version label
      const label = prompt('Enter version label (optional):');
      if (label) {
        payload.version = label.replace(/[^a-zA-Z0-9-_]/g, '_');
      }
    } else {
      // Auto-save version with timestamp
      payload.version = new Date().toISOString().replace(/[-:.TZ]/g, '');
    }

    try {
      let response = await fetch('/admin/api/save-layout-version.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      let result = await response.json();
      if (!result.success) throw new Error(result.error || 'Failed to save layout version');
      if (!isAutoSave) alert('Layout version saved: ' + (payload.version || result.version));
    } catch (error) {
      console.error('Failed to save layout version:', error);
      if (!isAutoSave) alert('Failed to save layout version. Please try again.');
    }
  }

  // Add drag and drop handlers
  function addDragAndDropHandlers() {
    let dragSrcEl = null;

    function handleDragStart(e) {
      dragSrcEl = this;
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', this.dataset.index);
      this.style.opacity = '0.4';
    }

    function handleDragOver(e) {
      if (e.preventDefault) e.preventDefault();
      e.dataTransfer.dropEffect = 'move';
      return false;
    }

    function handleDrop(e) {
      if (e.stopPropagation) e.stopPropagation();
      const srcIndex = parseInt(e.dataTransfer.getData('text/plain'), 10);
      const targetIndex = parseInt(this.dataset.index, 10);
      if (srcIndex !== targetIndex) {
        const moved = currentLayout.splice(srcIndex, 1)[0];
        currentLayout.splice(targetIndex, 0, moved);
        renderSections();
        layoutDirty = true;
        updatePreview();
      }
      return false;
    }

    function handleDragEnd(e) {
      this.style.opacity = '1';
    }

    const items = document.querySelectorAll('.section-item');
    items.forEach(item => {
      item.addEventListener('dragstart', handleDragStart, false);
      item.addEventListener('dragover', handleDragOver, false);
      item.addEventListener('drop', handleDrop, false);
      item.addEventListener('dragend', handleDragEnd, false);
    });
  }


  function addSectionOfType(sectionType, group) {
    fetch(`/admin/api/get-section-schema.php?section=${sectionType}`)
      .then(r => r.json())
      .then(schema => {
        const uniqueId = `${sectionType}-${Date.now().toString(36)}-${Math.random().toString(36).slice(2)}`;
        const presetSettings = schema?.presets?.default?.settings || {};
        const defaultSettings = {};
        (schema.settings || []).forEach(s => { defaultSettings[s.id] = presetSettings[s.id] ?? s.default ?? '' });
        const blocks = (schema.blocks || []).map(b => {
          const bs = {}; (b.settings||[]).forEach(s=>{bs[s.id]=s.default??''});
          return {type:b.type, settings: bs};
        });
        let insertIndex = addTargetIndex !== null ? addTargetIndex : currentLayout.length;
        if (insertIndex < 0 || insertIndex > currentLayout.length) insertIndex = currentLayout.length;
        if (addTargetIndex === null) {
          if (group === 'header') {
            insertIndex = currentLayout.findIndex(s => !(s.type.includes('header') || s.type.includes('announcement')));
            if (insertIndex === -1) insertIndex = 0;
          } else if (group === 'template') {
            insertIndex = currentLayout.findIndex(s => s.type.includes('footer'));
            if (insertIndex === -1) insertIndex = currentLayout.length;
          }
        }
        const newSec = { id: uniqueId, type: sectionType, settings: defaultSettings, blocks };
        currentLayout.splice(insertIndex, 0, newSec);
        renderSections(); layoutDirty = true; updatePreview();
        addTargetIndex = null;
      }).catch(err => {
        console.error('Failed adding section', err);
        alert('Could not add section');
      });
  }


  // Publish button simply calls save then reloads preview
  document.getElementById('publish-layout-btn').addEventListener('click', async () => {
    await document.getElementById('save-layout-btn').click();
    if (!layoutDirty) {
      alert('Published successfully.');
      updatePreview();
    }
  });

  document.querySelector('#add-section-modal .close-modal').addEventListener('click', () => {
    modal.style.display = 'none';
  });

  function filterCards(q) {
    document.querySelectorAll('#section-card-list .section-card').forEach(card => {
      card.style.display = card.querySelector('h4').textContent.toLowerCase().includes(q.toLowerCase()) ? 'flex' : 'none';
    });
  }
  sectionSearch.addEventListener('input', e => filterCards(e.target.value));
  searchInput.addEventListener('input', renderSections);

  document.querySelectorAll('#section-card-list .section-card').forEach(card => {
    card.addEventListener('click', () => {
      modal.style.display = 'none';
      addSectionOfType(card.dataset.type, addTargetGroup);
      addTargetIndex = null;
    });
  });


// Update live preview iframe with session layout override
function updatePreview() {
  // Send current layout to session for preview rendering
  fetch('/admin/api/set-live-preview.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      page: currentPage,
      layout: currentLayout
    })
  }).then(() => {
    // After storing in session, reload preview iframe
    const livePage = currentPage;
    previewFrame.src = `/?preview=1&page=${livePage}&t=${Date.now()}`;
  }).catch((err) => {
    console.error('Failed to update preview:', err);
    alert('Preview update failed.');
  });
}

// Page selector change handler
pageSelect.addEventListener('change', () => {
  const selectedPage = pageSelect.value;
  if (selectedPage) {
    loadLayout(selectedPage).then(() => {
      updatePreview(); // ✅ force reload preview
    });
  }
});

// Initial load
loadPages().then(() => {
  pageSelect.value = currentPage;
  pageSelect.dispatchEvent(new Event('change'));
});

  deviceButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      deviceButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      previewFrame.style.width = btn.dataset.width;
    });
  });


  // Define the missing saveLayout function
async function saveLayout() {
  const layoutObj = { sections: {}, order: [] };

  currentLayout.forEach(sec => {
    const id = sec.id || `${sec.type}-${Date.now().toString(36)}-${Math.random().toString(36).slice(2)}`;
    sec.id = id; // Ensure ID is assigned and consistent
    layoutObj.sections[id] = {
      type: sec.type,
      settings: sec.settings || {},
      blocks: sec.blocks || []
    };
    layoutObj.order.push(id);
  });

  const payload = {
    page: currentPage,
    layout: layoutObj
  };

  // Validate JSON before sending
  try {
    JSON.parse(JSON.stringify(payload));
  } catch (e) {
    alert('Layout data is invalid and could not be saved.');
    return;
  }

  try {
    const response = await fetch('/admin/api/save-layout.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const result = await response.json();
    if (!result.success) throw new Error(result.error || 'Failed to save layout');

    layoutDirty = false;
    console.log('Layout saved successfully!');
  } catch (err) {
    console.error('Error saving layout:', err);
    alert('Failed to save layout. Please try again.');
  }
}


</script>
</body>
</html>