<?php
/**
 * AI-Solutions — User Registration
 */
require_once __DIR__ . '/config.php';

// If already logged in, redirect based on role
if (isLoggedIn()) {
    redirect(isAdmin() ? 'dashboard.php' : 'user_dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrf($token)) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $username        = trim($_POST['username'] ?? '');
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($username === '' || $password === '' || $confirmPassword === '') {
            $error = 'All fields are required.';
        } elseif (strlen($username) < 3) {
            $error = 'Username must be at least 3 characters.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            try {
                $db = getDB();

                // Check if username is taken
                $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
                $stmt->execute([':username' => $username]);
                if ((int)$stmt->fetchColumn() > 0) {
                    $error = 'Username is already taken.';
                } else {
                    // Create standard user account
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'user')");
                    $stmt->execute([
                        ':username' => $username,
                        ':password' => $hashedPassword,
                    ]);

                    setFlash('success', 'Registration successful! You can now log in.');
                    redirect('login.php');
                }
            } catch (PDOException $e) {
                error_log('Registration error: ' . $e->getMessage());
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
  <title>Create Account — AI-Solutions</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <a href="index.php" class="nav-logo" style="display:inline-block;margin-bottom:24px;">AI<span>Solutions</span></a>
    <h1>Create Account</h1>
    <p class="subtitle">Sign up to submit inquiries and track requests.</p>

    <?php if ($error): ?>
      <div class="flash flash-error" style="margin:14px 0;"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php" style="margin-top:8px;">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <div class="form-group" style="margin-bottom:14px;">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Choose a username" required autofocus>
      </div>
      <div class="form-group" style="margin-bottom:14px;">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
      </div>
      <div class="form-group" style="margin-bottom:14px;">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-submit" style="margin-top:6px;">Register Account</button>
    </form>
    <p style="margin-top:20px;font-size:.85rem;color:var(--text-mid);">
      Already have an account? <a href="login.php" style="font-weight:700;color:var(--accent);">Sign In</a>
    </p>
    <p style="margin-top:8px;font-size:.82rem;">
      <a href="index.php" style="color:var(--text-light);">← Back to Homepage</a>
    </p>
  </div>
</div>
</body>
</html>
