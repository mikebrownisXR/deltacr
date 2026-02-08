<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

$step = $_GET['step'] ?? 'start';
$error = '';
$question = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = getPDO();
    if ($_POST['stage'] === 'identify') {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $first = trim($_POST['first_name'] ?? '');
        $dob = trim($_POST['dob'] ?? '');
        $stmt = $pdo->prepare('SELECT id,security_question,security_answer_hash FROM users WHERE email = ? AND first_name = ? AND dob = ?');
        $stmt->execute([$email,$first,$dob]);
        $u = $stmt->fetch();
        if ($u) {
            // present security question
            $question = $u['security_question'];
            $_SESSION['pw_reset_user'] = $u['id'];
            $_SESSION['pw_reset_q'] = $question;
            $step = 'question';
        } else {
            $error = 'No matching account found.';
        }
    } elseif ($_POST['stage'] === 'answer') {
        $ans = trim($_POST['security_answer'] ?? '');
        $uid = $_SESSION['pw_reset_user'] ?? null;
        if (!$uid) { $error = 'Session expired. Start again.'; }
        else {
            $stmt = $pdo->prepare('SELECT security_answer_hash FROM users WHERE id = ?');
            $stmt->execute([$uid]);
            $row = $stmt->fetch();
            if ($row && password_verify($ans, $row['security_answer_hash'])) {
                $step = 'reset';
            } else { $error = 'Incorrect answer.'; }
        }
    } elseif ($_POST['stage'] === 'reset') {
        $pw = $_POST['password'] ?? '';
        $pw2 = $_POST['confirm_password'] ?? '';
        $uid = $_SESSION['pw_reset_user'] ?? null;
        if (!$uid) { $error = 'Session expired.'; }
        elseif (strlen($pw) < 8) $error = 'Password must be at least 8 chars.';
        elseif ($pw !== $pw2) $error = 'Passwords do not match.';
        else {
            $hash = password_hash($pw, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
            $upd->execute([$hash,$uid]);
            // autologin
            $_SESSION['user_id'] = $uid;
            // fetch role and first name
            $stmt = $pdo->prepare('SELECT role,first_name FROM users WHERE id = ?'); $stmt->execute([$uid]); $u = $stmt->fetch();
            $_SESSION['role'] = $u['role'] ?? 'public';
            $_SESSION['first_name'] = $u['first_name'] ?? '';
            unset($_SESSION['pw_reset_user'], $_SESSION['pw_reset_q']);
            header('Location: dashboard.php'); exit;
        }
    }
}

?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/styles.css">
  <title>Forgot password</title>
</head>
<body>
  <header class="topbar"><div class="brand">DeltaCR</div></header>
  <main class="form-wrap">
    <h2>Recover account</h2>
    <?php if($error): ?><div class="notice"><?=htmlspecialchars($error)?></div><?php endif; ?>
    <?php if($step === 'start'): ?>
      <form method="post">
        <input type="hidden" name="stage" value="identify">
        <label>Email<input type="email" name="email" required></label>
        <div class="form-row"><div class="col"><label>First name<input type="text" name="first_name" required></label></div>
        <div class="col"><label>Date of birth<input type="date" name="dob" required></label></div></div>
        <button type="submit">Continue</button>
      </form>
    <?php elseif($step === 'question'): ?>
      <form method="post">
        <input type="hidden" name="stage" value="answer">
        <p>Security question: <strong><?=htmlspecialchars($_SESSION['pw_reset_q'] ?? $question)?></strong></p>
        <label>Answer<input type="text" name="security_answer" required></label>
        <button type="submit">Submit answer</button>
      </form>
    <?php elseif($step === 'reset'): ?>
      <form method="post">
        <input type="hidden" name="stage" value="reset">
        <label>New password<input type="password" name="password" required></label>
        <label>Confirm password<input type="password" name="confirm_password" required></label>
        <button type="submit">Reset password</button>
      </form>
    <?php endif; ?>
  </main>
</body>
</html>
