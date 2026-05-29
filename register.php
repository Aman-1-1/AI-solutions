<?php
/**
 * ============================================
 * AI-Solutions — User Registration
 * ============================================
 * Secure registration page for regular users.
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
    <a href="index.php" class="nav-logo" style="display:inline-block;margin-bottom:24px;font-size:1.6rem">AI-Solutions<span>.</span></a>
    <h1>Create Account</h1>
    <p class="subtitle">Sign up to request demos and track your inquiry status.</p>

    <?php if ($error): ?>
      <div class="flash flash-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Choose a username" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-submit">Register Account →</button>
    </form>
    <p style="margin-top:24px;font-size:.8rem;color:var(--text-secondary)">
      Already have an account? <a href="login.php" style="font-weight:600">Sign In</a>
    </p>
    <p style="margin-top:16px;font-size:.8rem;color:var(--text-muted)"><a href="index.php">← Back to Homepage</a></p>
  </div>
</div>
</body>
</html>
