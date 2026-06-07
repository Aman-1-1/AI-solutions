<?php
/**
 * AI-Solutions — Blogs list page
 */
require_once __DIR__ . '/config.php';

try {
    $db = getDB();
    $blogs = $db->query("SELECT * FROM blogs ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Failed to load blogs page: " . $e->getMessage());
    $blogs = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Blog & Insights — AI-Solutions</title>
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

<main class="container" style="padding: 60px 24px; max-width: 960px;">
  
  <div style="margin-bottom: 48px; text-align: center;">
    <div class="hero-badge"><span class="dot"></span> AI Insights & Articles</div>
    <h1 style="font-family: 'DM Serif Display', serif; font-size: 2.5rem; font-weight: 400; margin-top: 12px; margin-bottom: 8px;">The AI-Solutions Blog</h1>
    <p style="color: var(--text-mid); font-size: 1rem; max-width: 600px; margin: 0 auto;">Articles, guides, and updates from our development teams working on the frontier of AI integrations.</p>
  </div>

  <div class="features-grid">
    <?php if (!empty($blogs)): ?>
      <?php foreach ($blogs as $post): ?>
        <div class="feature-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
          <div style="background: var(--bg-soft); padding: 40px; text-align: center; font-size: 2.8rem; border-bottom: 1px solid var(--border);"><?= e($post['icon']) ?></div>
          <div style="padding: 24px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <p style="font-size: .72rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;"><?= e($post['category']) ?></p>
              <h3 style="margin-bottom: 10px; font-size: 1.2rem;"><?= e($post['title']) ?></h3>
              <p style="font-size: .88rem; color: var(--text-mid); margin-bottom: 20px; line-height: 1.5;"><?= e($post['content']) ?></p>
            </div>
            <a href="blog_detail.php?id=<?= $post['id'] ?>" style="font-size: .85rem; font-weight: 600; color: var(--accent); align-self: flex-start;">Read full article →</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 60px 24px; color: var(--text-light);">
        <div style="font-size: 2.5rem; margin-bottom: 12px;">📭</div>
        <p style="font-weight: 600;">No articles posted yet.</p>
      </div>
    <?php endif; ?>
  </div>

</main>

<footer class="footer">
  <div class="container">
    <p>&copy; <?= date('Y') ?> AI-Solutions. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
