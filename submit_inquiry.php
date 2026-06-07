<?php
/**
 * ============================================
 * AI-Solutions — Submit Inquiry Handler
 * ============================================
 * Validates and saves Contact/Demo form data
 * to the SQLite 'inquiries' table.
 * ============================================
 */
require_once __DIR__ . '/config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

if (!isLoggedIn()) {
    setFlash('error', 'Authentication required. Please sign in to submit inquiries.');
    redirect('login.php');
}

$referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';

// ── CSRF Validation ─────────────────────────────────────────────────────────
$token = $_POST['csrf_token'] ?? '';
if (!validateCsrf($token)) {
    setFlash('error', 'Invalid form submission. Please try again.');
    redirect($referer);
}

// ── Retrieve & Sanitize Input ───────────────────────────────────────────────
$name      = trim($_POST['name']      ?? '');
$email     = trim($_POST['email']     ?? '');
$phone     = trim($_POST['phone']     ?? '');
$company   = trim($_POST['company']   ?? '');
$country   = trim($_POST['country']   ?? '');
$job_title = trim($_POST['job_title'] ?? '');
$details   = trim($_POST['details']   ?? '');

// ── Validation ──────────────────────────────────────────────────────────────
$errors = [];

if ($name === '') {
    $errors[] = 'Name is required.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if ($job_title === '') {
    $errors[] = 'Job title is required.';
}
if ($details === '') {
    $errors[] = 'Please describe your project or demo requirements.';
}
// Optional: basic phone format check (digits, spaces, plus, dashes)
if ($phone !== '' && !preg_match('/^[\d\s\+\-\(\)]{7,20}$/', $phone)) {
    $errors[] = 'Phone number format is invalid.';
}

if (!empty($errors)) {
    setFlash('error', implode(' ', $errors));
    redirect($referer);
}

// ── Insert into Database (Prepared Statement) ──────────────────────────────
try {
    $db     = getDB();
    $userId = isLoggedIn() ? $_SESSION['user_id'] : null;

    $stmt = $db->prepare("
        INSERT INTO inquiries (user_id, name, email, phone, company, country, job_title, details, status)
        VALUES (:user_id, :name, :email, :phone, :company, :country, :job_title, :details, 'pending')
    ");
    $stmt->execute([
        ':user_id'   => $userId,
        ':name'      => $name,
        ':email'     => $email,
        ':phone'     => $phone,
        ':company'   => $company,
        ':country'   => $country,
        ':job_title' => $job_title,
        ':details'   => $details,
    ]);

    // Regenerate CSRF token after successful submission
    unset($_SESSION['csrf_token']);

    setFlash('success', 'Thank you! Your operations inquiry has been submitted successfully.');
} catch (PDOException $e) {
    // Log error in production; show generic message to user
    error_log('Inquiry insert failed: ' . $e->getMessage());
    setFlash('error', 'Something went wrong. Please try again later.');
}

// Redirect back to dashboard if submitted by logged-in user or from requests, otherwise to homepage
if (isLoggedIn()) {
    redirect('user_dashboard.php');
} else {
    redirect('index.php');
}
