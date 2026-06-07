<?php
/**
 * AI-Solutions — Blog Detail Page
 */
require_once __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? 0);
$post = null;

if ($id > 0) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM blogs WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Failed to load blog detail page: " . $e->getMessage());
    }
}

if (!$post) {
    setFlash('error', 'Article not found.');
    redirect('blog.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= e($post['title']) ?> — AI-Solutions</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar" id="navbar">
  <div class="container">
    <a href="index.php" class="nav-logo">AI<span>Solutions</span></a>
    <div class="nav-links" id="navLinks">
      <a href="index.php">Homepage</a>
      <a href="blog.php" class="active">Blog</a>
      <a href="gallery.php">Gallery</a>
      <?php if (isLoggedIn()): ?>
        <a href="<?= isAdmin() ? 'dashboard.php' : 'user_dashboard.php' ?>" class="nav-cta">Workspace</a>
      <?php else: ?>
        <a href="login.php" class="nav-cta">Sign In</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<main class="container" style="padding: 60px 24px; max-width: 760px;">
  
  <div style="margin-bottom: 24px;">
    <a href="blog.php" style="color: var(--accent); font-weight: 700; text-decoration: none; font-size: 0.9rem;">← Back to Blog</a>
  </div>

  <article class="card" style="padding: 40px 32px; box-shadow: var(--shadow-lg);">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px;">
      <span style="font-size: 2.2rem; background: var(--bg-soft); padding: 8px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border);"><?= e($post['icon']) ?></span>
      <div>
        <span class="status-badge status-purple" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;"><?= e($post['category']) ?></span>
        <div style="font-size: 0.8rem; color: var(--text-light); margin-top: 4px;"><?= date('M d, Y', strtotime($post['created_at'])) ?> · Published by Administrator</div>
      </div>
    </div>

    <h1 style="font-family: 'DM Serif Display', serif; font-size: 2.3rem; font-weight: 400; margin-bottom: 24px; line-height: 1.25;"><?= e($post['title']) ?></h1>

    <div style="font-size: 1.05rem; color: var(--text); line-height: 1.75; white-space: pre-wrap;">
      <?= e($post['content']) ?>
      
      <p style="margin-top: 24px;">At AI-Solutions, we design custom intelligence workflows tailored to business requirements. Feel free to contact our engineering team directly via the contact form on our website to discover how custom models can optimize your operations pipelines.</p>
    </div>
  </article>

</main>

<footer class="footer">
  <div class="container">
    <p>&copy; <?= date('Y') ?> AI-Solutions. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
