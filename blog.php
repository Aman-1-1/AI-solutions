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
      <a href="<?= isAdmin() ? 'dashboard.php?tab=blogs' : 'blog.php' ?>" class="<?= !isAdmin() ? 'active' : '' ?>">Blog</a>
      <a href="<?= isAdmin() ? 'dashboard.php?tab=gallery' : 'gallery.php' ?>">Gallery</a>
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
        <?php
          $cat = strtolower($post['category'] ?? '');
          $svg_path = '<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>'; // doc template
          if (str_contains($cat, 'agent') || str_contains($cat, 'assistant') || str_contains($cat, 'ai')) {
              $svg_path = '<path d="M12 2a10 10 0 0 0-10 10c0 5.523 4.477 10 10 10s10-4.477 10-10A10 10 0 0 0 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/><path d="M12 6a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-4 7h8v2H8z"/>';
          } elseif (str_contains($cat, 'dev') || str_contains($cat, 'code') || str_contains($cat, 'soft') || str_contains($cat, 'engine')) {
              $svg_path = '<path d="M16.5 9.4 12 14.1l-4.5-4.7L6 10.8l6 6.3 6-6.3-1.5-1.4z"/><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12z"/>';
          }
          $media_html = '<div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background: var(--bg-soft);"><svg class="svg-icon" style="width:56px; height:56px; color:var(--accent);" viewBox="0 0 24 24">' . $svg_path . '</svg></div>';
        ?>
        <div class="feature-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; cursor: pointer;" onclick="openContentModal('blog', <?= htmlspecialchars(json_encode($post['title']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($post['content']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($media_html), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($post['category']), ENT_QUOTES, 'UTF-8') ?>)">
          <div style="background: var(--bg-soft); padding: 40px; text-align: center; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: center;">
            <svg class="svg-icon" style="width:36px; height:36px; color:var(--accent);" viewBox="0 0 24 24"><?= $svg_path ?></svg>
          </div>
          <div style="padding: 24px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <p style="font-size: .72rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;"><?= e($post['category']) ?></p>
              <h3 style="margin-bottom: 10px; font-size: 1.2rem;"><?= e($post['title']) ?></h3>
              <p style="font-size: .88rem; color: var(--text-mid); margin-bottom: 20px; line-height: 1.5;"><?= e($post['content']) ?></p>
            </div>
            <a href="blog_detail.php?id=<?= $post['id'] ?>" onclick="event.stopPropagation();" style="font-size: .85rem; font-weight: 600; color: var(--accent); align-self: flex-start;">Read full article →</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 60px 24px; color: var(--text-light);">
        <svg viewBox="0 0 24 24" style="width:48px; height:48px; stroke:var(--text-light); fill:none; stroke-width:2; margin-bottom:12px; display:inline-block;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
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

<!-- Content Modal -->
<div id="contentModal" class="modal-backdrop" onclick="closeContentModal(event)">
  <div class="modal-card" onclick="event.stopPropagation()">
    <button class="modal-close" onclick="closeContentModal(event)">&times;</button>
    <div id="modalMedia" class="modal-media"></div>
    <div class="modal-body">
      <span id="modalCategory" class="overline" style="color: var(--accent); margin-bottom: 8px; display: inline-block;"></span>
      <h3 id="modalTitle" style="font-family: 'DM Serif Display', serif; font-size: 1.8rem; font-weight: 400; margin-bottom: 12px; color: var(--text);"></h3>
      <div id="modalContent" style="font-size: 0.95rem; color: var(--text-mid); line-height: 1.6; white-space: pre-wrap;"></div>
    </div>
  </div>
</div>

<script>
function openContentModal(type, title, content, mediaHtml, category = '') {
    const modal = document.getElementById('contentModal');
    const modalMedia = document.getElementById('modalMedia');
    const modalCategory = document.getElementById('modalCategory');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    modalMedia.innerHTML = mediaHtml || '';
    if (category) {
        modalCategory.innerText = category;
        modalCategory.style.display = 'inline-block';
    } else {
        modalCategory.style.display = 'none';
    }
    modalTitle.innerText = title;
    modalContent.innerText = content;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeContentModal(event) {
    if (event) event.preventDefault();
    const modal = document.getElementById('contentModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeContentModal();
    }
});
</script>

</body>
</html>
