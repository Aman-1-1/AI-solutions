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
      <a href="<?= isAdmin() ? 'dashboard.php?tab=blogs' : 'blog.php' ?>">Blog</a>
      <a href="<?= isAdmin() ? 'dashboard.php?tab=gallery' : 'gallery.php' ?>" class="<?= !isAdmin() ? 'active' : '' ?>">Gallery</a>
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
          $has_image = !empty($item['image_path']);
          $img_src = $has_image ? e($item['image_path']) : '';
          if ($has_image) {
              $media_html = '<img src="' . $img_src . '" alt="' . e($item['title']) . '">';
          } else {
              $media_html = '<div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--accent-soft) 0%, var(--bg-soft) 100%);"><svg class="svg-icon" style="width:40px; height:40px; color:var(--accent);" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg></div>';
          }
        ?>
        <div class="feature-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; cursor: pointer;" onclick="openContentModal('gallery', <?= htmlspecialchars(json_encode($item['title']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($item['description']), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($media_html), ENT_QUOTES, 'UTF-8') ?>, 'Gallery Item')">
          <div class="gallery-img-container">
            <?php if ($has_image): ?>
              <img src="<?= e($item['image_path']) ?>" alt="<?= e($item['title']) ?>" class="gallery-img">
            <?php else: ?>
              <!-- Clean premium SVG graphic placeholder as fallback -->
              <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, var(--accent-soft) 0%, var(--bg-soft) 100%);">
                <svg class="svg-icon" style="width:40px; height:40px; color:var(--accent);" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
              </div>
            <?php endif; ?>
          </div>
          <div style="padding: 20px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
            <strong style="font-size: 1.05rem; color: var(--text); margin-bottom: 6px; display: block;"><?= e($item['title']) ?></strong>
            <p style="font-size: .85rem; color: var(--text-mid); line-height: 1.5;"><?= e($item['description']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 60px 24px; color: var(--text-light);">
        <svg viewBox="0 0 24 24" style="width:48px; height:48px; stroke:var(--text-light); fill:none; stroke-width:2; margin-bottom:12px; display:inline-block;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
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
