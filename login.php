<?php
/**
 * AI-Solutions — Login
 */
require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? 'dashboard.php' : 'user_dashboard.php');
}

$flash = getFlash();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrf($token)) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if ($username === '' || $password === '') {
            $error = 'Both fields are required.';
        } else {
            try {
                $db   = getDB();
                $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = :username LIMIT 1");
                $stmt->execute([':username' => $username]);
                $user = $stmt->fetch();
                if ($user && password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['username']  = $user['username'];
                    $_SESSION['user_role'] = $user['role'];
                    unset($_SESSION['csrf_token']);
                    redirect($user['role'] === 'admin' ? 'dashboard.php' : 'user_dashboard.php');
                } else {
                    $error = 'Invalid username or password.';
                }
            } catch (PDOException $e) {
                error_log('Login error: ' . $e->getMessage());
                $error = 'A system error occurred. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sign In — AI-Solutions</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <a href="index.php" class="nav-logo" style="display:inline-block;margin-bottom:24px;">AI<span>Solutions</span></a>
    <h1>Sign In</h1>
    <p class="subtitle">Enter your credentials to access your account.</p>

    <?php if ($error): ?>
      <div class="flash flash-error" style="margin:14px 0;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($flash): ?>
      <div class="flash flash-<?= e($flash['type']) ?>" style="margin:14px 0;"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" style="margin-top:8px;">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="your_username" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary btn-submit" style="margin-top:6px;">Sign In</button>
    </form>
    <p style="margin-top:20px;font-size:.85rem;color:var(--text-mid);">
      No account? <a href="register.php" style="font-weight:700;color:var(--accent);">Register here</a>
    </p>
    <p style="margin-top:8px;font-size:.82rem;">
      <a href="index.php" style="color:var(--text-light);">← Back to Homepage</a>
    </p>
  </div>
</div>
</body>
</html>
