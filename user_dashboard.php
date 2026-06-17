<?php
/**
 * AI-Solutions — User Dashboard
 */
require_once __DIR__ . '/config.php';

if (!isLoggedIn()) {
    setFlash('error', 'Please sign in to access your account.');
    redirect('login.php');
}
if (isAdmin()) {
    redirect('dashboard.php');
}

$username = $_SESSION['username'] ?? 'User';
$userId   = $_SESSION['user_id'];
$flash    = getFlash();

try {
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM inquiries WHERE user_id = :user_id ORDER BY submitted_at DESC");
    $stmt->execute([':user_id' => $userId]);
    $inquiries = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('User dashboard error: ' . $e->getMessage());
    $inquiries = [];
}

$totalInquiries = count($inquiries);
$verifiedCount  = 0;
foreach ($inquiries as $inq) {
    if ($inq['status'] === 'verified') $verifiedCount++;
}
$pendingCount = $totalInquiries - $verifiedCount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>My Account — AI-Solutions</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dash-layout">

  <!-- Navbar -->
  <nav class="navbar" id="navbar">
    <div class="container">
      <a href="index.php" class="nav-logo">AI<span>Solutions</span></a>
      <div class="nav-links" id="navLinks">
        <a href="index.php">Homepage</a>
        <a href="blog.php">Blog</a>
        <a href="gallery.php">Gallery</a>
        <a href="logout.php" class="nav-cta" style="background:transparent; color:var(--text-mid); border:1px solid var(--border);">Sign Out</a>
      </div>
    </div>
  </nav>

  <div class="dash-main">
    <div class="dash-content">
      
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:32px; flex-wrap:wrap; gap:12px;">
        <div>
          <h1 style="font-family:'DM Serif Display', serif; font-size:2.2rem; font-weight:400; line-height:1.2;">My Workspace</h1>
          <p style="color:var(--text-light); font-size:.9rem; margin-top:4px;">Welcome back, <strong><?= e($username) ?></strong></p>
        </div>
        <a href="index.php#contact" class="btn btn-primary">+ Submit New Inquiry</a>
      </div>

      <?php if ($flash): ?>
        <div class="flash flash-<?= e($flash['type']) ?>" style="margin-bottom:32px;"><?= e($flash['message']) ?></div>
      <?php endif; ?>

      <div class="stat-cards" style="margin-bottom:32px;">
        <div class="stat-card accent">
          <div class="label">Total Submitted</div>
          <div class="value"><?= $totalInquiries ?></div>
        </div>
        <div class="stat-card yellow">
          <div class="label">Pending Review</div>
          <div class="value"><?= $pendingCount ?></div>
        </div>
        <div class="stat-card green">
          <div class="label">Verified Requests</div>
          <div class="value"><?= $verifiedCount ?></div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h2>Your Project Inquiries</h2>
        </div>

        <?php if ($totalInquiries > 0): ?>
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Status</th>
                <th>Company / Job Title</th>
                <th>Details</th>
                <th>Submitted</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($inquiries as $row): ?>
              <tr>
                <td><span class="status-badge status-<?= e($row['status']) ?>"><?= ucfirst(e($row['status'])) ?></span></td>
                <td>
                  <span style="font-weight:600;"><?= e($row['company'] ?: '—') ?></span>
                  <span style="display:block;font-size:.78rem;color:var(--text-light);"><?= e($row['job_title'] ?: '—') ?></span>
                </td>
                <td style="max-width:280px;white-space:pre-wrap;word-break:break-word;font-size:.85rem;color:var(--text-mid);"><?= e($row['details']) ?></td>
                <td style="white-space:nowrap;font-size:.8rem;color:var(--text-light);"><?= date('M d, Y', strtotime($row['submitted_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <div style="text-align:center;padding:48px 24px;color:var(--text-mid);">
          <svg viewBox="0 0 24 24" style="width:48px; height:48px; stroke:var(--text-light); fill:none; stroke-width:2; margin-bottom:12px; display:inline-block;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <p style="font-weight:600;">No requests yet.</p>
          <p style="font-size:.88rem;margin-top:4px;margin-bottom:20px;">Submit your first inquiry to get started.</p>
          <a href="index.php#contact" class="btn btn-primary">Submit a Request</a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>
</body>
</html>
