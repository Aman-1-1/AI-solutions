<?php
/**
 * ============================================
 * AI-Solutions — Admin Dashboard
 * ============================================
 * Password-protected page displaying all
 * inquiry submissions with a verification system.
 * ============================================
 */
require_once __DIR__ . '/config.php';

// ── Auth Guard ──────────────────────────────────────────────────────────────
if (!isAdmin()) {
    setFlash('error', 'Please log in as an administrator to access the dashboard.');
    redirect('login.php');
}

$flash = getFlash();

// ── Handle Inquiry Verification ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrf($token)) {
        setFlash('error', 'Invalid form submission. Please try again.');
    } else {
        $inquiryId = (int)($_POST['inquiry_id'] ?? 0);
        if ($inquiryId > 0) {
            try {
                $db = getDB();
                $stmt = $db->prepare("UPDATE inquiries SET status = 'verified' WHERE id = :id");
                $stmt->execute([':id' => $inquiryId]);
                setFlash('success', 'Inquiry #' . $inquiryId . ' successfully marked as verified.');
            } catch (PDOException $e) {
                error_log('Verify inquiry error: ' . $e->getMessage());
                setFlash('error', 'Failed to verify inquiry.');
            }
        }
    }
    redirect('dashboard.php');
}

// ── Fetch Inquiries ─────────────────────────────────────────────────────────
try {
    $db   = getDB();
    $stmt = $db->query("SELECT i.*, u.username FROM inquiries i LEFT JOIN users u ON i.user_id = u.id ORDER BY i.submitted_at DESC");
    $inquiries = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Dashboard query error: ' . $e->getMessage());
    $inquiries = [];
}

$totalInquiries = count($inquiries);

$pendingCount  = 0;
$verifiedCount = 0;
foreach ($inquiries as $inq) {
    if ($inq['status'] === 'verified') {
        $verifiedCount++;
    } else {
        $pendingCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard — AI-Solutions Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ── Navbar ──────────────────────────────────── -->
<nav class="navbar">
  <div class="container">
    <a href="index.php" class="nav-logo">AI-Solutions<span>.</span></a>
    <div class="nav-links" id="navLinks">
      <a href="index.php">Homepage</a>
      <a href="dashboard.php" class="active">Dashboard</a>
      <a href="logout.php" class="nav-cta">Logout</a>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<main class="container">
  <!-- ── Header ─────────────────────────────────── -->
  <div class="dash-header">
    <div>
      <h1>📊 Dashboard</h1>
      <p style="color:var(--text-secondary);font-size:.9rem;margin-top:4px">Welcome back, <strong><?= e($_SESSION['username'] ?? 'Admin') ?></strong></p>
    </div>
    <a href="logout.php" class="btn btn-outline" style="padding:10px 20px;font-size:.85rem">Sign Out</a>
  </div>

  <?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <!-- ── Stats Cards ────────────────────────────── -->
  <div class="dash-stats">
    <div class="dash-stat-card">
      <h3><?= $totalInquiries ?></h3>
      <p>Total Inquiries</p>
    </div>
    <div class="dash-stat-card">
      <h3><?= $pendingCount ?></h3>
      <p>Pending Review</p>
    </div>
    <div class="dash-stat-card">
      <h3><?= $verifiedCount ?></h3>
      <p>Verified Requests</p>
    </div>
  </div>

  <!-- ── Inquiries Table ────────────────────────── -->
  <?php if ($totalInquiries > 0): ?>
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Account</th>
          <th>Inquirer</th>
          <th>Company</th>
          <th>Details</th>
          <th>Submitted</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($inquiries as $row): ?>
        <tr>
          <td><?= e((string)$row['id']) ?></td>
          <td>
            <?php if ($row['username']): ?>
              <span class="user-pill">👤 <?= e($row['username']) ?></span>
            <?php else: ?>
              <span class="user-pill-guest">Guest</span>
            <?php endif; ?>
          </td>
          <td>
            <strong><?= e($row['name']) ?></strong><br>
            <span style="font-size:.75rem;color:var(--text-muted)">
              <a href="mailto:<?= e($row['email']) ?>"><?= e($row['email']) ?></a>
              <?= $row['phone'] ? ' | ' . e($row['phone']) : '' ?>
            </span>
          </td>
          <td>
            <?= e($row['company'] ?: '—') ?>
            <span style="display:block;font-size:.75rem;color:var(--text-muted)"><?= e($row['country'] ?: '—') ?></span>
          </td>
          <td style="max-width:240px;white-space:pre-wrap;word-break:break-word"><?= e($row['details']) ?></td>
          <td style="white-space:nowrap"><?= date('M d, Y H:i', strtotime($row['submitted_at'])) ?></td>
          <td>
            <span class="status-badge status-<?= e($row['status']) ?>">
              <?= ucfirst(e($row['status'])) ?>
            </span>
          </td>
          <td>
            <?php if ($row['status'] === 'pending'): ?>
              <form method="POST" action="dashboard.php" style="margin:0">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="action" value="verify">
                <input type="hidden" name="inquiry_id" value="<?= e((string)$row['id']) ?>">
                <button type="submit" class="btn btn-primary" style="padding:6px 12px;font-size:.75rem;border-radius:var(--radius-sm);white-space:nowrap">Verify ✓</button>
              </form>
            <?php else: ?>
              <span style="color:var(--success);font-size:.8rem;font-weight:600;white-space:nowrap">✓ Verified</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="empty-state">
    <div class="icon">📭</div>
    <h3>No Inquiries Yet</h3>
    <p style="color:var(--text-muted);margin-top:8px">When visitors submit the contact form, their inquiries will appear here.</p>
  </div>
  <?php endif; ?>
</main>

<footer class="footer" style="margin-top:60px">
  <div class="container">
    <p>&copy; <?= date('Y') ?> AI-Solutions Admin Panel</p>
  </div>
</footer>

<script>
document.getElementById('hamburger').addEventListener('click', function(){
  document.getElementById('navLinks').classList.toggle('open');
});
</script>
</body>
</html>
