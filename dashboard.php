<?php
/**
 * AI-Solutions — Admin Dashboard
 */
require_once __DIR__ . '/config.php';

if (!isAdmin()) {
    setFlash('error', 'Please log in as an administrator.');
    redirect('login.php');
}

$flash = getFlash();
$tab = $_GET['tab'] ?? 'inquiries';

// ── Handle Post Request Operations ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrf($token)) {
        setFlash('error', 'Invalid security token.');
    } else {
        $action = $_POST['action'];
        $db = getDB();

        if ($action === 'verify') {
            $inquiryId = (int)($_POST['inquiry_id'] ?? 0);
            if ($inquiryId > 0) {
                try {
                    $stmt = $db->prepare("UPDATE inquiries SET status = 'verified' WHERE id = :id");
                    $stmt->execute([':id' => $inquiryId]);
                    setFlash('success', 'Inquiry #' . $inquiryId . ' marked as verified.');
                } catch (PDOException $e) {
                    error_log('Verify error: ' . $e->getMessage());
                    setFlash('error', 'Failed to verify inquiry.');
                }
            }
        }

        elseif ($action === 'add_blog') {
            $title = trim($_POST['title'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $icon = trim($_POST['icon'] ?? '🧠');
            $content = trim($_POST['content'] ?? '');

            if ($title === '' || $category === '' || $content === '') {
                setFlash('error', 'All fields are required.');
            } else {
                try {
                    $stmt = $db->prepare("INSERT INTO blogs (title, category, icon, content) VALUES (:title, :category, :icon, :content)");
                    $stmt->execute([
                        ':title' => $title,
                        ':category' => $category,
                        ':icon' => $icon,
                        ':content' => $content
                    ]);
                    setFlash('success', 'Blog post added successfully.');
                } catch (PDOException $e) {
                    error_log('Add blog error: ' . $e->getMessage());
                    setFlash('error', 'Failed to add blog post.');
                }
            }
        }

        elseif ($action === 'delete_blog') {
            $id = (int)($_POST['blog_id'] ?? 0);
            if ($id > 0) {
                try {
                    $stmt = $db->prepare("DELETE FROM blogs WHERE id = :id");
                    $stmt->execute([':id' => $id]);
                    setFlash('success', 'Blog post deleted successfully.');
                } catch (PDOException $e) {
                    error_log('Delete blog error: ' . $e->getMessage());
                    setFlash('error', 'Failed to delete blog post.');
                }
            }
        }

        elseif ($action === 'add_gallery') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? '📷');
            $color_theme = trim($_POST['color_theme'] ?? 'purple');

            if ($title === '' || $description === '') {
                setFlash('error', 'All fields are required.');
            } else {
                try {
                    $stmt = $db->prepare("INSERT INTO gallery (title, description, icon, color_theme) VALUES (:title, :description, :icon, :color_theme)");
                    $stmt->execute([
                        ':title' => $title,
                        ':description' => $description,
                        ':icon' => $icon,
                        ':color_theme' => $color_theme
                    ]);
                    setFlash('success', 'Gallery item added successfully.');
                } catch (PDOException $e) {
                    error_log('Add gallery error: ' . $e->getMessage());
                    setFlash('error', 'Failed to add gallery item.');
                }
            }
        }

        elseif ($action === 'delete_gallery') {
            $id = (int)($_POST['gallery_id'] ?? 0);
            if ($id > 0) {
                try {
                    $stmt = $db->prepare("DELETE FROM gallery WHERE id = :id");
                    $stmt->execute([':id' => $id]);
                    setFlash('success', 'Gallery item deleted successfully.');
                } catch (PDOException $e) {
                    error_log('Delete gallery error: ' . $e->getMessage());
                    setFlash('error', 'Failed to delete gallery item.');
                }
            }
        }
    }
    redirect('dashboard.php?tab=' . urlencode($tab));
}

// ── Fetch Counter Metrics & Tab Data ────────────────────────────────────────
try {
    $db = getDB();
    $inquiriesCount = (int)$db->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
    $blogsCount = (int)$db->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
    $galleryCount = (int)$db->query("SELECT COUNT(*) FROM gallery")->fetchColumn();

    if ($tab === 'blogs') {
        $blogs = $db->query("SELECT * FROM blogs ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($tab === 'gallery') {
        $gallery = $db->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $db->query("SELECT i.*, u.username FROM inquiries i LEFT JOIN users u ON i.user_id = u.id ORDER BY i.submitted_at DESC");
        $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalInquiries = count($inquiries);
        $pendingCount   = 0;
        $verifiedCount  = 0;
        foreach ($inquiries as $inq) {
            if ($inq['status'] === 'verified') $verifiedCount++;
            else $pendingCount++;
        }
    }
} catch (PDOException $e) {
    error_log('Dashboard data load error: ' . $e->getMessage());
    $inquiriesCount = 0;
    $blogsCount = 0;
    $galleryCount = 0;
    $inquiries = [];
    $blogs = [];
    $gallery = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Dashboard — AI-Solutions</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dash-layout">

  <aside class="dash-sidebar">
    <div class="sidebar-logo">AI<span>Solutions</span></div>
    <nav class="sidebar-nav">
      <a href="index.php">🏠 &nbsp;Homepage</a>
      <a href="dashboard.php?tab=inquiries" class="<?= $tab === 'inquiries' ? 'active' : '' ?>">📊 &nbsp;Inquiries (<?= $inquiriesCount ?>)</a>
      <a href="dashboard.php?tab=blogs" class="<?= $tab === 'blogs' ? 'active' : '' ?>">✍️ &nbsp;Blogs (<?= $blogsCount ?>)</a>
      <a href="dashboard.php?tab=gallery" class="<?= $tab === 'gallery' ? 'active' : '' ?>">🖼️ &nbsp;Gallery (<?= $galleryCount ?>)</a>
    </nav>
    <div class="sidebar-footer">
      <p style="font-size:.78rem;color:var(--text-light);margin-bottom:8px;">
        Signed in as <strong style="color:var(--text)"><?= e($_SESSION['username'] ?? 'Admin') ?></strong>
      </p>
      <a href="logout.php" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;">Sign Out</a>
    </div>
  </aside>

  <div class="dash-main">
    <div class="dash-topbar">
      <h1>Admin Dashboard</h1>
      <span class="user-info">Administrator</span>
    </div>

    <div class="dash-content">
      <?php if ($flash): ?>
        <div class="flash flash-<?= e($flash['type']) ?>" style="margin-bottom:20px;"><?= e($flash['message']) ?></div>
      <?php endif; ?>

      <?php if ($tab === 'inquiries'): ?>
        <!-- ── TAB: INQUIRIES ────────────────────────────── -->
        <div class="stat-cards">
          <div class="stat-card accent">
            <div class="label">Total</div>
            <div class="value"><?= $inquiriesCount ?></div>
          </div>
          <div class="stat-card yellow">
            <div class="label">Pending</div>
            <div class="value"><?= $pendingCount ?></div>
          </div>
          <div class="stat-card green">
            <div class="label">Verified</div>
            <div class="value"><?= $verifiedCount ?></div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h2>All Inquiries</h2>
          </div>
          <?php if ($inquiriesCount > 0): ?>
          <div class="table-wrap">
            <table class="data-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>User</th>
                  <th>Name / Job Title</th>
                  <th>Company</th>
                  <th>Details</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($inquiries as $row): ?>
                <tr>
                  <td style="color:var(--text-light);font-weight:600;"><?= e((string)$row['id']) ?></td>
                  <td>
                    <?php if ($row['username']): ?>
                      <span class="user-pill"><?= e($row['username']) ?></span>
                    <?php else: ?>
                      <span class="user-pill-guest">Guest</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <strong><?= e($row['name']) ?></strong>
                    <span style="display:block;font-size:.78rem;color:var(--accent);font-weight:600;"><?= e($row['job_title'] ?: '—') ?></span>
                    <span style="font-size:.78rem;color:var(--text-light)">
                      <a href="mailto:<?= e($row['email']) ?>"><?= e($row['email']) ?></a>
                      <?= $row['phone'] ? ' · ' . e($row['phone']) : '' ?>
                    </span>
                  </td>
                  <td>
                    <span style="font-weight:600;"><?= e($row['company'] ?: '—') ?></span>
                    <span style="display:block;font-size:.78rem;color:var(--text-light)"><?= e($row['country'] ?: '—') ?></span>
                  </td>
                  <td style="max-width:220px;white-space:pre-wrap;word-break:break-word;font-size:.85rem;color:var(--text-mid);"><?= e($row['details']) ?></td>
                  <td style="white-space:nowrap;font-size:.8rem;color:var(--text-light);"><?= date('M d, Y', strtotime($row['submitted_at'])) ?></td>
                  <td><span class="status-badge status-<?= e($row['status']) ?>"><?= ucfirst(e($row['status'])) ?></span></td>
                  <td>
                    <?php if ($row['status'] === 'pending'): ?>
                      <form method="POST" action="dashboard.php?tab=inquiries" style="margin:0">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <input type="hidden" name="action" value="verify">
                        <input type="hidden" name="inquiry_id" value="<?= e((string)$row['id']) ?>">
                        <button type="submit" class="btn btn-success btn-sm">Verify</button>
                      </form>
                    <?php else: ?>
                      <span style="color:var(--green);font-size:.8rem;font-weight:700;">✓ Done</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php else: ?>
          <div style="text-align:center;padding:48px 24px;color:var(--text-mid);">
            <div style="font-size:2rem;margin-bottom:10px;">📭</div>
            <p style="font-weight:600;">No inquiries yet.</p>
            <p style="font-size:.88rem;margin-top:4px;">User submissions will appear here.</p>
          </div>
          <?php endif; ?>
        </div>

      <?php elseif ($tab === 'blogs'): ?>
        <!-- ── TAB: BLOGS ────────────────────────────────── -->
        <div style="display: grid; grid-template-columns: 1.1fr 2fr; gap: 24px;" class="dash-grid-responsive">
          <div class="card">
            <div class="card-header">
              <h2>Add Blog Post</h2>
            </div>
            <form method="POST" action="dashboard.php?tab=blogs" style="margin-top: 10px;">
              <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
              <input type="hidden" name="action" value="add_blog">
              
              <div class="form-group" style="margin-bottom: 12px;">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" placeholder="Blog Title" required>
              </div>

              <div class="form-group" style="margin-bottom: 12px;">
                <label for="category">Category *</label>
                <input type="text" id="category" name="category" placeholder="e.g. AI Article, Company News" required>
              </div>

              <div class="form-group" style="margin-bottom: 12px;">
                <label for="icon">Icon Emoji *</label>
                <input type="text" id="icon" name="icon" placeholder="e.g. 🧠, 🚀, 📋" value="🧠" required>
              </div>

              <div class="form-group" style="margin-bottom: 16px;">
                <label for="content">Short Content / Summary *</label>
                <textarea id="content" name="content" placeholder="A short description or summary of the blog post..." style="min-height: 100px;" required></textarea>
              </div>

              <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Publish Post</button>
            </form>
          </div>

          <div class="card">
            <div class="card-header">
              <h2>Published Posts</h2>
            </div>
            <?php if (!empty($blogs)): ?>
            <div class="table-wrap">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Post</th>
                    <th>Category</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($blogs as $post): ?>
                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 1.5rem;"><?= e($post['icon']) ?></span>
                        <div>
                          <strong><?= e($post['title']) ?></strong>
                          <p style="font-size: .8rem; color: var(--text-mid); max-width: 320px; white-space: pre-wrap; margin-top: 4px;"><?= e($post['content']) ?></p>
                        </div>
                      </div>
                    </td>
                    <td><span class="user-pill"><?= e($post['category']) ?></span></td>
                    <td>
                      <form method="POST" action="dashboard.php?tab=blogs" style="margin: 0;" onsubmit="return confirm('Delete this blog post?');">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <input type="hidden" name="action" value="delete_blog">
                        <input type="hidden" name="blog_id" value="<?= e((string)$post['id']) ?>">
                        <button type="submit" class="btn btn-outline btn-sm" style="color: var(--red); border-color: var(--red);">Delete</button>
                      </form>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php else: ?>
            <div style="text-align: center; padding: 40px; color: var(--text-light);">
              <p>No posts published yet.</p>
            </div>
            <?php endif; ?>
          </div>
        </div>

      <?php elseif ($tab === 'gallery'): ?>
        <!-- ── TAB: GALLERY ──────────────────────────────── -->
        <div style="display: grid; grid-template-columns: 1.1fr 2fr; gap: 24px;" class="dash-grid-responsive">
          <div class="card">
            <div class="card-header">
              <h2>Add Gallery Item</h2>
            </div>
            <form method="POST" action="dashboard.php?tab=gallery" style="margin-top: 10px;">
              <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
              <input type="hidden" name="action" value="add_gallery">
              
              <div class="form-group" style="margin-bottom: 12px;">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" placeholder="Event / Activity Name" required>
              </div>

              <div class="form-group" style="margin-bottom: 12px;">
                <label for="icon">Icon Emoji *</label>
                <input type="text" id="icon" name="icon" placeholder="e.g. 💻, 📢, 🤝, 💡" value="📷" required>
              </div>

              <div class="form-group" style="margin-bottom: 12px;">
                <label for="color_theme">Color Theme *</label>
                <select id="color_theme" name="color_theme" style="width: 100%; padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--white); font-family: inherit;">
                  <option value="purple">Purple</option>
                  <option value="yellow">Yellow</option>
                  <option value="green">Green</option>
                  <option value="red">Red</option>
                </select>
              </div>

              <div class="form-group" style="margin-bottom: 16px;">
                <label for="description">Short Description *</label>
                <textarea id="description" name="description" placeholder="Describe the team activity..." style="min-height: 80px;" required></textarea>
              </div>

              <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Add Item</button>
            </form>
          </div>

          <div class="card">
            <div class="card-header">
              <h2>Gallery Items</h2>
            </div>
            <?php if (!empty($gallery)): ?>
            <div class="table-wrap">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Theme</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($gallery as $item): ?>
                  <tr>
                    <td>
                      <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 1.5rem;"><?= e($item['icon']) ?></span>
                        <div>
                          <strong><?= e($item['title']) ?></strong>
                          <p style="font-size: .8rem; color: var(--text-mid); max-width: 320px; white-space: pre-wrap; margin-top: 4px;"><?= e($item['description']) ?></p>
                        </div>
                      </div>
                    </td>
                    <td><span class="status-badge status-<?= e($item['color_theme']) ?>"><?= ucfirst(e($item['color_theme'])) ?></span></td>
                    <td>
                      <form method="POST" action="dashboard.php?tab=gallery" style="margin: 0;" onsubmit="return confirm('Delete this gallery item?');">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <input type="hidden" name="action" value="delete_gallery">
                        <input type="hidden" name="gallery_id" value="<?= e((string)$item['id']) ?>">
                        <button type="submit" class="btn btn-outline btn-sm" style="color: var(--red); border-color: var(--red);">Delete</button>
                      </form>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php else: ?>
            <div style="text-align: center; padding: 40px; color: var(--text-light);">
              <p>No gallery items added yet.</p>
            </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>

</div>
</body>
</html>
