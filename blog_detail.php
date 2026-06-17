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
      <?php
        $cat = strtolower($post['category'] ?? '');
        $svg_path = '<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>'; // doc template
        if (str_contains($cat, 'agent') || str_contains($cat, 'assistant') || str_contains($cat, 'ai')) {
            $svg_path = '<path d="M12 2a10 10 0 0 0-10 10c0 5.523 4.477 10 10 10s10-4.477 10-10A10 10 0 0 0 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/><path d="M12 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-4 7h8v2H8z"/>';
        } elseif (str_contains($cat, 'dev') || str_contains($cat, 'code') || str_contains($cat, 'soft') || str_contains($cat, 'engine')) {
            $svg_path = '<path d="M16.5 9.4 12 14.1l-4.5-4.7L6 10.8l6 6.3 6-6.3-1.5-1.4z"/><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12z"/>';
        }
      ?>
      <div style="background: var(--bg-soft); padding: 12px; border-radius: var(--radius-sm); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center;">
        <svg class="svg-icon" style="width:24px; height:24px; color:var(--accent);" viewBox="0 0 24 24"><?= $svg_path ?></svg>
      </div>
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
