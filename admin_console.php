<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('HTTP/1.1 403 Forbidden'); echo 'Forbidden'; exit;
}
$pdo = getPDO();
// Actions: change role, delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['action']) && $_POST['action'] === 'change_role' && !empty($_POST['user_id'])) {
        $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->execute([$_POST['role'], $_POST['user_id']]);
    }
    if (!empty($_POST['action']) && $_POST['action'] === 'delete_user' && !empty($_POST['user_id'])) {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?'); $stmt->execute([$_POST['user_id']]);
    }
    header('Location: admin_console.php'); exit;
}

$users = $pdo->query('SELECT id,first_name,last_name,email,role,created_at,last_login FROM users ORDER BY created_at DESC')->fetchAll();

function tail_file($file, $lines = 200)
{
    if (!is_readable($file)) return 'Log not available.';
    $f = fopen($file, 'rb');
    $pos = -1; $eof = ''; $line = '';
    $chunk = '';
    $output = '';
    fseek($f, 0, SEEK_END);
    $size = ftell($f);
    $buffer = '';
    $read = 0; $count = 0;
    while ($size + $pos > 0 && $count < $lines) {
        $pos -= 512; if ($size + $pos < 0) { $pos = -$size; }
        fseek($f, $pos, SEEK_END);
        $chunk = fread($f, min(512, $size));
        $buffer = $chunk . $buffer;
        $count = substr_count($buffer, "\n");
        if ($pos == -$size) break;
    }
    fclose($f);
    $linesArr = explode("\n", trim($buffer));
    $linesArr = array_slice($linesArr, -$lines);
    return implode("\n", $linesArr);
}

?><!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="assets/css/styles.css"><title>Admin Console</title></head>
<body><header class="topbar"><div class="brand">DeltaCR</div><nav class="nav"><a href="index.php">Home</a><a href="dashboard.php">Dashboard</a></nav></header>
<main style="padding:18px">
  <h1>Admin Console</h1>
  <section class="form-wrap"><h2>Users</h2>
    <table style="width:100%;border-collapse:collapse"><thead><tr><th>Email</th><th>Name</th><th>Role</th><th>Created</th><th>Last login</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach($users as $u): ?>
      <tr style="border-top:1px solid #eee"><td><?=htmlspecialchars($u['email'])?></td><td><?=htmlspecialchars($u['first_name'].' '.$u['last_name'])?></td>
      <td>
        <form method="post" style="display:inline">
          <input type="hidden" name="action" value="change_role">
          <input type="hidden" name="user_id" value="<?=htmlspecialchars($u['id'])?>">
          <select name="role">
            <option value="public" <?= $u['role']==='public'?'selected':''?>>public</option>
            <option value="client" <?= $u['role']==='client'?'selected':''?>>client</option>
            <option value="staff" <?= $u['role']==='staff'?'selected':''?>>staff</option>
            <option value="service_provider" <?= $u['role']==='service_provider'?'selected':''?>>service_provider</option>
            <option value="admin" <?= $u['role']==='admin'?'selected':''?>>admin</option>
          </select>
          <button type="submit">Update</button>
        </form>
      </td>
      <td><?=htmlspecialchars($u['created_at'])?></td><td><?=htmlspecialchars($u['last_login'])?></td>
      <td>
        <form method="post" onsubmit="return confirm('Delete user?')" style="display:inline">
          <input type="hidden" name="action" value="delete_user">
          <input type="hidden" name="user_id" value="<?=htmlspecialchars($u['id'])?>">
          <button type="submit" style="background:#d33">Delete</button>
        </form>
      </td></tr>
    <?php endforeach; ?></tbody></table>
  </section>

  <section class="form-wrap" style="margin-top:18px"><h2>Recent logs</h2>
    <pre style="max-height:480px;overflow:auto;background:#111;color:#eee;padding:12px;border-radius:6px"><?=htmlspecialchars(tail_file(LOG_PATH,300))?></pre>
  </section>
</main></body></html>
