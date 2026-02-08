<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';
if (empty($_SESSION['user_id'])) {
    header('Location: login.php'); exit;
}
$role = $_SESSION['role'] ?? 'public';
$first = htmlspecialchars($_SESSION['first_name'] ?? '');
// Load role-specific info - simple example using README content link
$readmeLink = 'README.md';
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/styles.css">
  <script src="assets/js/app.js"></script>
  <title>Dashboard</title>
</head>
<body>
  <header class="topbar"><div class="brand">DeltaCR</div>
    <nav class="nav"><a href="index.php">Home</a><a href="logout.php">Sign out</a></nav></header>

  <main style="padding:20px">
    <h1>Welcome, <?=$first?>!</h1>
    <p>Role: <strong><?=htmlspecialchars($role)?></strong></p>

    <div class="notice">This dashboard contains a guided tour for new users and role-specific content drawn from the project README.</div>

    <?php if($role === 'admin'): ?>
      <section><h2>Admin Console</h2>
        <p>Manage users and system settings. <a href="admin_console.php">Open Admin Console</a></p>
        <p><strong>Logs:</strong> <?=LOG_PATH?> — <a href="admin_console.php#logs">view</a></p>
      </section>
    <?php elseif($role === 'client'): ?>
      <section><h2>Client Dashboard</h2><p>Access client resources, projects and support tools. <a href="roles/client.php">Read client docs</a></p></section>
    <?php elseif($role === 'staff'): ?>
      <section><h2>Staff Dashboard</h2><p>Work queues, cases and internal notes. <a href="roles/staff.php">Read staff docs</a></p></section>
    <?php elseif($role === 'service_provider'): ?>
      <section><h2>Service Provider</h2><p>Service scheduling and invoices. <a href="roles/service_provider.php">Read provider docs</a></p></section>
    <?php else: ?>
      <section><h2>Public</h2><p>Public content and resources. <a href="roles/public.php">Read public docs</a></p></section>
    <?php endif; ?>

    <section style="margin-top:20px">
      <h3>Project docs</h3>
      <p>Full feature list and content: <a href="<?=$readmeLink?>">Repository README</a></p>
    </section>
  </main>

  <div class="modal" id="tour">
    <div class="panel">
      <button class="close" onclick="document.getElementById('tour').classList.remove('show')">Close</button>
      <h3>Welcome to DeltaCR</h3>
      <p>Quick tour: the menu at top provides navigation. Role-specific features appear on this dashboard.</p>
    </div>
  </div>

  <script>document.getElementById('tour').classList.add('show');</script>
</body>
</html>
