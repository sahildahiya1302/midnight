<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
    exit;
}

// handle ajax load
if (isset($_GET['load'])) {
    $file = $_GET['load'];
    if (is_file($file)) {
        header('Content-Type: text/plain');
        echo file_get_contents($file);
    }
    exit;
}

// save request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file'], $_POST['code'])) {
    $file = $_POST['file'];
    if (is_file($file)) {
        file_put_contents($file, $_POST['code']);
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false]);
    }
    exit;
}

$pageTitle = 'Code Editor';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $pageTitle; ?></title>
<style>
  body{margin:0;font-family:Arial,sans-serif;display:flex;height:100vh;}
  .files{width:240px;background:#f4f4f4;overflow:auto;padding:10px;}
  .editor{flex:1;display:flex;flex-direction:column;overflow:hidden;}
  .tabs{display:flex;background:#e5e7eb;overflow-x:auto;}
  .tabs button{padding:6px 10px;border:none;background:none;cursor:pointer;}
  .tab-active{background:#fff;border-bottom:2px solid #008060;}
  textarea{flex:1;border:none;outline:none;font-family:monospace;padding:10px;}
  button.save-btn{padding:6px 12px;margin-top:4px;}
</style>
</head>
<body>
<div class="files">
  <p><strong>Files</strong></p>
  <ul id="file-list">
    <?php foreach (glob('../../themes/default/**/*.*') as $file): ?>
      <li><a href="#" data-file="<?php echo htmlspecialchars($file); ?>"><?php echo basename($file); ?></a></li>
    <?php endforeach; ?>
  </ul>
</div>
<div class="editor">
  <div class="tabs" id="tabs"></div>
  <textarea id="codeArea"></textarea>
  <button class="save-btn" id="saveBtn">Save</button>
</div>
<script>
  const tabs = document.getElementById('tabs');
  const codeArea = document.getElementById('codeArea');
  const saveBtn = document.getElementById('saveBtn');
  const openFiles = {};

  function openFile(path, name) {
    if (openFiles[path]) {
      setActive(path);
      return;
    }
    fetch(`?load=${encodeURIComponent(path)}`)
      .then(r => r.text())
      .then(text => {
        openFiles[path] = {name, text};
        const btn = document.createElement('button');
        btn.textContent = name + ' ✕';
        btn.dataset.path = path;
        btn.addEventListener('click', () => setActive(path));
        btn.addEventListener('dblclick', e => { e.stopPropagation(); btn.remove(); delete openFiles[path]; if (Object.keys(openFiles).length) setActive(Object.keys(openFiles)[0]); });
        tabs.appendChild(btn);
        setActive(path);
      });
  }

  function setActive(path) {
    tabs.querySelectorAll('button').forEach(b => b.classList.toggle('tab-active', b.dataset.path === path));
    codeArea.value = openFiles[path].text;
    saveBtn.dataset.path = path;
  }

  document.querySelectorAll('#file-list a').forEach(a => {
    a.addEventListener('click', e => { e.preventDefault(); openFile(a.dataset.file, a.textContent); });
  });

  saveBtn.addEventListener('click', () => {
    const path = saveBtn.dataset.path;
    fetch('', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`file=${encodeURIComponent(path)}&code=${encodeURIComponent(codeArea.value)}`})
      .then(r => r.json()).then(() => { openFiles[path].text = codeArea.value; alert('Saved'); });
  });
</script>
</body>
</html>
