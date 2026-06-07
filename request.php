<?php
/**
 * AI-Solutions — New Inquiry Console
 */
require_once __DIR__ . '/config.php';

// Access control: redirect guest users to login
if (!isLoggedIn()) {
    setFlash('error', 'Authentication required. Please sign in to submit a request.');
    redirect('login.php');
}

$username = $_SESSION['username'];
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>New Request — AI-Solutions</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar" id="navbar">
  <div class="container">
    <a href="index.php" class="nav-logo">AI<span>Solutions</span></a>
    <div class="nav-links" id="navLinks">
      <a href="index.php">Homepage</a>
      <?php if (isAdmin()): ?>
        <a href="dashboard.php">Dashboard</a>
      <?php else: ?>
        <a href="user_dashboard.php">My Account</a>
      <?php endif; ?>
      <a href="logout.php" class="nav-cta">Sign Out</a>
    </div>
  </div>
</nav>

<main class="container" style="padding: 60px 24px; max-width: 680px;">
  
  <div style="margin-bottom: 36px; text-align: center;">
    <div class="hero-badge"><span class="dot"></span> Secure Request Channel</div>
    <h1 style="font-family: 'DM Serif Display', serif; font-size: 2.2rem; font-weight: 400; margin-top: 12px; margin-bottom: 8px;">Submit New Inquiry</h1>
    <p style="color: var(--text-mid); font-size: .95rem;">Describe your project requirements below, and we will get back to you within 24 hours.</p>
  </div>

  <?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>" style="margin-bottom: 24px;"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <div class="contact-form">
    <form action="submit_inquiry.php" method="POST" id="inquiryForm">
      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
      
      <div class="form-grid">
        <div class="form-group">
          <label for="name">Name *</label>
          <input type="text" id="name" name="name" placeholder="Jane Doe" value="<?= e($username) ?>" required>
        </div>
        <div class="form-group">
          <label for="email">Email *</label>
          <input type="email" id="email" name="email" placeholder="jane@company.com" required>
        </div>
        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="tel" id="phone" name="phone" placeholder="+1 (555) 0199">
        </div>
        <div class="form-group">
          <label for="company">Company Name</label>
          <input type="text" id="company" name="company" placeholder="Your Company">
        </div>
        <div class="form-group">
          <label for="country">Country</label>
          <input type="text" id="country" name="country" placeholder="United States">
        </div>
        <div class="form-group">
          <label for="job_title">Job Title *</label>
          <input type="text" id="job_title" name="job_title" placeholder="Product Manager" required>
        </div>
        <div class="form-group full-width">
          <label for="details">Job Details / Requirements *</label>
          <textarea id="details" name="details" placeholder="Describe what you need help with, your timeline, and any specific requirements..." required></textarea>
        </div>
        <div class="form-group full-width" style="margin-top: 10px;">
          <button type="submit" class="btn btn-primary btn-submit">Submit Request</button>
        </div>
      </div>
    </form>
  </div>

  <p style="text-align: center; margin-top: 28px; font-size: 0.85rem; color: var(--text-light);">
    All data is kept confidential and processed securely. <br>
    <a href="index.php" style="color: var(--text-mid); font-weight: 600; text-decoration: underline; margin-top: 8px; display: inline-block;">← Back to Homepage</a>
  </p>

</main>

<footer class="footer">
  <div class="container">
    <p>&copy; <?= date('Y') ?> AI-Solutions. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
