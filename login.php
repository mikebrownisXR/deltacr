<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Provide a valid email.';
    else {
        try {
            $pdo = getPDO();
            $stmt = $pdo->prepare('SELECT id,first_name,password,role FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $u = $stmt->fetch();
            if ($u && password_verify($password, $u['password'])) {
                $_SESSION['user_id'] = $u['id'];
                $_SESSION['role'] = $u['role'];
                $_SESSION['first_name'] = $u['first_name'];
                // update last_login
                $upd = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
                $upd->execute([$u['id']]);
                header('Location: dashboard.php'); exit;
            } else {
                $error = 'Invalid credentials.';
            }
        } catch (Exception $e) {
            log_error('Login error: ' . $e->getMessage());
            $error = 'Login failed.';
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/styles.css">
  <title>Login</title>
</head>
<body>
  <header class="topbar"><div class="brand">DeltaCR</div></header>
  <main class="form-wrap">
    <h2>Sign in</h2>
    <?php if($error): ?><div class="notice"><?=htmlspecialchars($error)?></div><?php endif; ?>
    <form method="post">
      <label>Email<input type="email" name="email" required></label>
      <label>Password<input type="password" name="password" required></label>
      <div style="display:flex;gap:8px;align-items:center;margin-top:10px">
        <button type="submit">Sign in</button>
        <a href="forgot.php">Forgot password?</a>
      </div>
    </form>
  </main>
</body>
</html>
