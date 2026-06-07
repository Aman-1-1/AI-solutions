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

  <aside class="dash-sidebar">
    <div class="sidebar-logo">AI<span>Solutions</span></div>
    <nav class="sidebar-nav">
      <a href="index.php">🏠 &nbsp;Homepage</a>
      <a href="user_dashboard.php" class="active">📋 &nbsp;My Inquiries</a>
      <a href="index.php#contact">✉️ &nbsp;New Request</a>
    </nav>
    <div class="sidebar-footer">
      <p style="font-size:.78rem;color:var(--text-light);margin-bottom:8px;">
        Signed in as <strong style="color:var(--text)"><?= e($username) ?></strong>
      </p>
      <a href="logout.php" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;">Sign Out</a>
    </div>
  </aside>

  <div class="dash-main">
    <div class="dash-topbar">
      <h1>My Account</h1>
      <span class="user-info">Welcome back, <?= e($username) ?></span>
    </div>

    <div class="dash-content">
      <?php if ($flash): ?>
        <div class="flash flash-<?= e($flash['type']) ?>" style="margin-bottom:20px;"><?= e($flash['message']) ?></div>
      <?php endif; ?>

      <div class="stat-cards">
        <div class="stat-card accent">
          <div class="label">Submitted</div>
          <div class="value"><?= $totalInquiries ?></div>
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
          <h2>Your Inquiries</h2>
          <a href="index.php#contact" class="btn btn-primary btn-sm">+ New Request</a>
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
          <div style="font-size:2rem;margin-bottom:10px;">📩</div>
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
