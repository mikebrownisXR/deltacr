<?php
session_start();
require_once __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>DeltaCR — Landing</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <header class="topbar">
    <div class="brand">DeltaCR</div>
    <nav class="nav">
      <a href="index.php">Home</a>
      <a href="register.php">Create Account</a>
      <a href="login.php">Login</a>
      <a href="README.md">Docs</a>
    </nav>
    <div class="role-select">
      <?php if(!isset($_SESSION['user_id'])): ?>
        <a class="btn" href="register.php">Get Started</a>
      <?php else: ?>
        <a class="btn" href="dashboard.php">Dashboard</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="hero">
    <div class="hero-inner">
      <h1>DeltaCR — Collaborative Resource Center</h1>
      <p>A responsive, role-based platform for public, clients, staff, service providers, and admins.</p>
      <div class="cta">
        <a href="register.php" class="primary">Create an account</a>
        <a href="login.php" class="secondary">Sign in</a>
      </div>
    </div>
  </main>

  <section class="features">
    <h2>For each user type</h2>
    <div class="cards">
      <div class="card"><h3>Public</h3><p>Browse public information and resources.</p></div>
      <div class="card"><h3>Client</h3><p>View client-specific resources, projects and support.</p></div>
      <div class="card"><h3>Staff</h3><p>Manage cases, workflows and internal notes.</p></div>
      <div class="card"><h3>Service Provider</h3><p>Manage services, schedules and invoices.</p></div>
      <div class="card"><h3>Admin</h3><p>Manage users, system settings and view logs.</p></div>
    </div>
  </section>

  <footer class="site-footer">
    <small>See the project documentation in the repository README for full content.</small>
  </footer>
</body>
</html>
