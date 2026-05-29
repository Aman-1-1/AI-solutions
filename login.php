<?php
/**
 * ============================================
 * AI-Solutions — Admin Login
 * ============================================
 * Secure login page using PHP sessions and
 * bcrypt password verification.
 * ============================================
 */
require_once __DIR__ . '/config.php';

// If already logged in, redirect based on role
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('dashboard.php');
    } else {
        redirect('user_dashboard.php');
    }
}

$flash = getFlash();
$error = '';

// ── Handle Login Submission ─────────────────────────────────────────────────
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
                    // Regenerate session ID to prevent fixation
                    session_regenerate_id(true);
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['username']  = $user['username'];
                    $_SESSION['user_role']  = $user['role'];
                    // Clear CSRF token so a fresh one is generated
                    unset($_SESSION['csrf_token']);

                    if ($user['role'] === 'admin') {
                        redirect('dashboard.php');
                    } else {
                        redirect('user_dashboard.php');
                    }
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
    <a href="index.php" class="nav-logo" style="display:inline-block;margin-bottom:24px;font-size:1.6rem">AI-Solutions<span>.</span></a>
    <h1>Sign In</h1>
    <p class="subtitle">Enter your credentials to access your account.</p>

    <?php if ($error): ?>
      <div class="flash flash-error"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($flash): ?>
      <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Username" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary btn-submit">Sign In →</button>
    </form>
    <p style="margin-top:24px;font-size:.8rem;color:var(--text-secondary)">
      Don't have an account? <a href="register.php" style="font-weight:600">Create one</a>
    </p>
    <p style="margin-top:16px;font-size:.8rem;color:var(--text-muted)"><a href="index.php">← Back to Homepage</a></p>
  </div>
</div>
</body>
</html>
