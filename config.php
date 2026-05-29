<?php
/**
 * ============================================
 * AI-Solutions — Global Configuration
 * ============================================
 * Handles .env parsing, PDO database connection,
 * session initialization, and security helpers.
 * ============================================
 */

// ── 1. Load Environment Variables ───────────────────────────────────────────
function loadEnv(string $path): void
{
    if (!file_exists($path)) {
        return;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (str_starts_with(trim($line), '#')) {
            continue;
        }
        // Split on first '='
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key   = trim($parts[0]);
            $value = trim($parts[1]);
            // Remove surrounding quotes if present
            $value = trim($value, '"\'');
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Load .env from the same directory as this config file
loadEnv(__DIR__ . '/.env');

// ── 2. Helper: Retrieve an environment variable ────────────────────────────
function env(string $key, string $default = ''): string
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// ── 3. Database Connection (Singleton) ──────────────────────────────────────
function getDB(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dbPath = __DIR__ . '/' . env('DB_PATH', 'aisolutions.db');
        $pdo = new PDO('sqlite:' . $dbPath);
        // Configure PDO for safety and performance
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // Enable WAL mode for better concurrent read performance
        $pdo->exec('PRAGMA journal_mode=WAL');
        $pdo->exec('PRAGMA foreign_keys=ON');
    }
    return $pdo;
}

// ── 4. Session Initialization ───────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    // Harden session cookie
    session_set_cookie_params([
        'lifetime' => 0,          // Session cookie (expires on browser close)
        'path'     => '/',
        'httponly'  => true,       // Not accessible via JavaScript
        'samesite'  => 'Strict',  // CSRF mitigation
    ]);
    session_start();
}

// ── 5. Security Helpers ─────────────────────────────────────────────────────

/**
 * Escape output for safe HTML rendering (XSS prevention).
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Generate or retrieve a CSRF token for the current session.
 */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate the submitted CSRF token against the session token.
 */
function validateCsrf(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if any user is logged in.
 */
function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

/**
 * Check if the logged-in user is an admin.
 */
function isAdmin(): bool
{
    return isLoggedIn() && !empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Check if the logged-in user is a regular user.
 */
function isUser(): bool
{
    return isLoggedIn() && !empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'user';
}

/**
 * Redirect to a given URL and exit.
 */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/**
 * Set a flash message in session for one-time display.
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Retrieve and clear the flash message.
 */
function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
