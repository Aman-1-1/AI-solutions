<?php
/**
 * AI-Solutions — Gallery page
 */
require_once __DIR__ . '/config.php';

try {
    $db = getDB();
    $gallery = $db->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Failed to load gallery page: " . $e->getMessage());
    $gallery = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gallery & Events — AI-Solutions</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar" id="navbar">
  <div class="container">
    <a href="index.php" class="nav-logo">AI<span>Solutions</span></a>
    <div class="nav-links" id="navLinks">
      <a href="index.php">Homepage</a>
      <a href="blog.php">Blog</a>
      <a href="gallery.php" class="active">Gallery</a>
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
    <div class="hero-badge"><span class="dot"></span> Team activities & events</div>
    <h1 style="font-family: 'DM Serif Display', serif; font-size: 2.5rem; font-weight: 400; margin-top: 12px; margin-bottom: 8px;">Our Photo Gallery</h1>
    <p style="color: var(--text-mid); font-size: 1rem; max-width: 600px; margin: 0 auto;">Check out snapshots from our developer summits, hackathons, team workshops, and company gatherings.</p>
  </div>

  <div class="features-grid">
    <?php if (!empty($gallery)): ?>
      <?php foreach ($gallery as $item): ?>
        <?php
          // Map color theme to background/text colors
          $bg_color = '#ede9ff';
          $text_color = 'var(--accent)';
          if ($item['color_theme'] === 'yellow') {
              $bg_color = '#fef7e6';
              $text_color = 'var(--yellow)';
          } elseif ($item['color_theme'] === 'green') {
              $bg_color = '#e6f7ee';
              $text_color = 'var(--green)';
          } elseif ($item['color_theme'] === 'red') {
              $bg_color = '#fdeaea';
              $text_color = 'var(--red)';
          }
        ?>
        <div class="feature-card" style="padding: 0; overflow: hidden; text-align: center;">
          <div style="height: 180px; background: <?= $bg_color ?>; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; border-bottom: 1px solid var(--border);">
            <span style="font-size: 2.5rem;"><?= e($item['icon']) ?></span>
            <strong style="font-size: 1.05rem; color: <?= $text_color ?>;"><?= e($item['title']) ?></strong>
          </div>
          <p style="padding: 18px; font-size: .88rem; color: var(--text-mid); line-height: 1.5;"><?= e($item['description']) ?></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 60px 24px; color: var(--text-light);">
        <div style="font-size: 2.5rem; margin-bottom: 12px;">📷</div>
        <p style="font-weight: 600;">No gallery items added yet.</p>
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
