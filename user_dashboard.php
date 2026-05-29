<?php
/**
 * ============================================
 * AI-Solutions — User Dashboard
 * ============================================
 * Password-protected page for standard users
 * to view their inquiries and status.
 * ============================================
 */
require_once __DIR__ . '/config.php';

// ── Auth Guard ──────────────────────────────────────────────────────────────
if (!isLoggedIn()) {
    setFlash('error', 'Please log in to access your dashboard.');
    redirect('login.php');
}

// If admin logs in, redirect to admin dashboard
if (isAdmin()) {
    redirect('dashboard.php');
}

$username = $_SESSION['username'] ?? 'User';
$userId   = $_SESSION['user_id'];
$flash    = getFlash();

// ── Fetch User's Inquiries ──────────────────────────────────────────────────
try {
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM inquiries WHERE user_id = :user_id ORDER BY submitted_at DESC");
    $stmt->execute([':user_id' => $userId]);
    $inquiries = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('User dashboard query error: ' . $e->getMessage());
    $inquiries = [];
}

$totalInquiries = count($inquiries);

// Count verified inquiries
$verifiedCount = 0;
foreach ($inquiries as $inq) {
    if ($inq['status'] === 'verified') {
        $verifiedCount++;
    }
}
$pendingCount = $totalInquiries - $verifiedCount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>My Dashboard — AI-Solutions</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ── Navbar ──────────────────────────────────── -->
<nav class="navbar">
  <div class="container">
    <a href="index.php" class="nav-logo">AI-Solutions<span>.</span></a>
    <div class="nav-links" id="navLinks">
      <a href="index.php">Homepage</a>
      <a href="user_dashboard.php" class="active">My Dashboard</a>
      <a href="logout.php" class="nav-cta">Logout</a>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<main class="container" style="padding-top: 100px;">
  <!-- ── Header ─────────────────────────────────── -->
  <div class="dash-header">
    <div>
      <h1>👋 Hello, <?= e($username) ?></h1>
      <p style="color:var(--text-secondary);font-size:.9rem;margin-top:4px">Welcome to your workspace. Track and manage your requests below.</p>
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
      <p>Total Submissions</p>
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

  <!-- ── Dashboard Content Grid ────────────────── -->
  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; margin-top: 20px; align-items: start;" class="dash-grid-responsive">
    
    <!-- ── Inquiries List ────────────────────────── -->
    <div>
      <h2 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <span>📋</span> Your Inquiries
      </h2>

      <?php if ($totalInquiries > 0): ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Status</th>
              <th>Company</th>
              <th>Details</th>
              <th>Submitted</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($inquiries as $row): ?>
            <tr>
              <td>
                <span class="status-badge status-<?= e($row['status']) ?>">
                  <?= ucfirst(e($row['status'])) ?>
                </span>
              </td>
              <td><?= e($row['company'] ?: '—') ?></td>
              <td style="max-width:280px;white-space:pre-wrap;word-break:break-word"><?= e($row['details']) ?></td>
              <td style="white-space:nowrap"><?= date('M d, Y H:i', strtotime($row['submitted_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="empty-state" style="border: 1px dashed var(--border-glass); border-radius: var(--radius); padding: 48px 24px;">
        <div class="icon">📩</div>
        <h3>No Inquiries Yet</h3>
        <p style="color:var(--text-muted);margin-top:8px">You haven't submitted any inquiry requests yet. Fill out the quick request form to get started!</p>
      </div>
      <?php endif; ?>
    </div>

    <!-- ── Quick Inquiry Form ────────────────────── -->
    <div class="contact-form" style="padding: 28px; width: 100%;">
      <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
        <span>⚡</span> Quick Inquiry
      </h3>
      <p style="color:var(--text-secondary);font-size:.8rem;margin-bottom:20px;">Request a demo or consultation directly from your account.</p>

      <form action="submit_inquiry.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
        
        <div class="form-group" style="margin-bottom: 14px;">
          <label for="name" style="font-size: .75rem;">Full Name *</label>
          <input type="text" id="name" name="name" placeholder="Jane Doe" value="<?= e($username) ?>" required>
        </div>

        <div class="form-group" style="margin-bottom: 14px;">
          <label for="email" style="font-size: .75rem;">Email Address *</label>
          <input type="email" id="email" name="email" placeholder="jane@company.com" required>
        </div>

        <div class="form-group" style="margin-bottom: 14px;">
          <label for="phone" style="font-size: .75rem;">Phone Number</label>
          <input type="tel" id="phone" name="phone" placeholder="+1 234 567 890">
        </div>

        <div class="form-group" style="margin-bottom: 14px;">
          <label for="company" style="font-size: .75rem;">Company</label>
          <input type="text" id="company" name="company" placeholder="Acme Corp">
        </div>

        <div class="form-group" style="margin-bottom: 14px;">
          <label for="country" style="font-size: .75rem;">Country</label>
          <input type="text" id="country" name="country" placeholder="United States">
        </div>

        <div class="form-group" style="margin-bottom: 18px;">
          <label for="details" style="font-size: .75rem;">Project / Demo Details *</label>
          <textarea id="details" name="details" placeholder="Describe what you need..." style="min-height: 80px;" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-submit" style="font-size: .85rem; padding: 12px;">Submit Request →</button>
      </form>
    </div>

  </div>
</main>

<footer class="footer" style="margin-top:60px">
  <div class="container">
    <p>&copy; <?= date('Y') ?> AI-Solutions Account Panel</p>
  </div>
</footer>

<script>
document.getElementById('hamburger').addEventListener('click', function(){
  document.getElementById('navLinks').classList.toggle('open');
});
</script>
</body>
</html>
