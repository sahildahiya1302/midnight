<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /backend/auth/login.php');
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
  .files{width:200px;background:#f4f4f4;overflow:auto;padding:10px;}
  .editor{flex:1;display:flex;flex-direction:column;}
  textarea{flex:1;border:none;outline:none;font-family:monospace;padding:10px;}
  button{padding:6px 12px;margin-top:4px;}
</style>
</head>
<body>
<div class="files">
  <p><strong>Files</strong></p>
  <ul>
    <?php foreach (glob('../../themes/default/**/*.*') as $file): ?>
      <li><a href="?file=<?php echo urlencode($file); ?>"><?php echo basename($file); ?></a></li>
    <?php endforeach; ?>
  </ul>
</div>
<div class="editor">
  <?php
  $file = $_GET['file'] ?? '';
  $content = '';
  if ($file && is_file($file)) {
      $content = file_get_contents($file);
  }
  ?>
  <form method="post">
    <textarea name="code" id="codeArea"><?php echo htmlspecialchars($content); ?></textarea>
    <button type="submit">Save</button>
  </form>
</div>
</body>
</html>
