<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last = trim($_POST['last_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $question = trim($_POST['security_question'] ?? '');
    $answer = trim($_POST['security_answer'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'public';

    if (!$first || !$last) $errors[] = 'First and last name required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (!$dob) $errors[] = 'Date of birth required.';
    if (!$question || !$answer) $errors[] = 'Security question and answer required.';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        try {
            $pdo = getPDO();
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'An account with that email already exists.';
            } else {
                $pwHash = password_hash($password, PASSWORD_DEFAULT);
                $ansHash = password_hash($answer, PASSWORD_DEFAULT);
                $ins = $pdo->prepare('INSERT INTO users (first_name,last_name,dob,email,password,security_question,security_answer_hash,role,created_at) VALUES (?,?,?,?,?,?,?,?,NOW())');
                $ins->execute([$first,$last,$dob,$email,$pwHash,$question,$ansHash,$role]);
                $id = $pdo->lastInsertId();
                // autologin
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = $role;
                $_SESSION['first_name'] = $first;
                header('Location: dashboard.php');
                exit;
            }
        } catch (Exception $e) {
            log_error('Registration failed: ' . $e->getMessage());
            $errors[] = 'Failed to create account.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/styles.css">
  <title>Create Account</title>
</head>
<body>
  <header class="topbar"><div class="brand">DeltaCR</div></header>
  <main class="form-wrap">
    <h2>Create an account</h2>
    <?php if($errors): ?><div class="notice"><?=htmlspecialchars(implode("<br>",$errors))?></div><?php endif; ?>
    <form method="post">
      <div class="form-row">
        <div class="col"><label>First name<input type="text" name="first_name" required></label></div>
        <div class="col"><label>Last name<input type="text" name="last_name" required></label></div>
      </div>
      <div class="form-row">
        <div class="col"><label>Date of birth<input type="date" name="dob" required></label></div>
        <div class="col"><label>Email<input type="email" name="email" required></label></div>
      </div>
      <label>Security question<select name="security_question">
        <option>What is your mother's maiden name?</option>
        <option>What was the name of your first pet?</option>
        <option>What city were you born in?</option>
      </select></label>
      <label>Security answer<input type="text" name="security_answer" required></label>
      <label>Password<input type="password" name="password" required></label>
      <label>Confirm password<input type="password" name="confirm_password" required></label>
      <label>Role<select name="role">
        <option value="public">Public</option>
        <option value="client">Client</option>
        <option value="staff">Staff</option>
        <option value="service_provider">Service Provider</option>
        <option value="admin">Admin</option>
      </select></label>
      <div style="margin-top:12px"><button type="submit">Create account</button></div>
    </form>
  </main>
</body>
</html>
