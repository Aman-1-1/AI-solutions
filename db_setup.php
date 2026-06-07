<?php
/**
 * ============================================
 * AI-Solutions — Database Setup Script
 * ============================================
 * Run this script once from the command line to
 * create the SQLite database, tables, and seed
 * a default admin user.
 *
 * Usage:  php db_setup.php
 * ============================================
 */

require_once __DIR__ . '/config.php';

echo "╔══════════════════════════════════════════╗\n";
echo "║   AI-Solutions Database Setup            ║\n";
echo "╚══════════════════════════════════════════╝\n\n";

try {
    $db = getDB();

    // ── 1. Create 'users' table ─────────────────────────────────────────────
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            username    TEXT    NOT NULL UNIQUE,
            password    TEXT    NOT NULL,
            role        TEXT    NOT NULL DEFAULT 'user',
            created_at  TEXT    NOT NULL DEFAULT (datetime('now'))
        )
    ");
    // Migration check for users
    $stmt = $db->query("PRAGMA table_info(users)");
    $cols = array_column($stmt->fetchAll(), 'name');
    if (!in_array('role', $cols)) {
        $db->exec("ALTER TABLE users ADD COLUMN role TEXT NOT NULL DEFAULT 'user'");
        echo "[✓] Migrated 'users' table: added 'role' column.\n";
    }
    echo "[✓] Table 'users' created/verified.\n";

    // ── 2. Create 'inquiries' table ─────────────────────────────────────────
    $db->exec("
        CREATE TABLE IF NOT EXISTS inquiries (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id     INTEGER REFERENCES users(id),
            name        TEXT    NOT NULL,
            email       TEXT    NOT NULL,
            phone       TEXT    NOT NULL DEFAULT '',
            company     TEXT    NOT NULL DEFAULT '',
            country     TEXT    NOT NULL DEFAULT '',
            job_title   TEXT    NOT NULL DEFAULT '',
            details     TEXT    NOT NULL DEFAULT '',
            status      TEXT    NOT NULL DEFAULT 'pending',
            submitted_at TEXT   NOT NULL DEFAULT (datetime('now'))
        )
    ");
    // Migration check for inquiries
    $stmt = $db->query("PRAGMA table_info(inquiries)");
    $cols = array_column($stmt->fetchAll(), 'name');
    if (!in_array('user_id', $cols)) {
        $db->exec("ALTER TABLE inquiries ADD COLUMN user_id INTEGER REFERENCES users(id)");
        echo "[✓] Migrated 'inquiries' table: added 'user_id' column.\n";
    }
    if (!in_array('status', $cols)) {
        $db->exec("ALTER TABLE inquiries ADD COLUMN status TEXT NOT NULL DEFAULT 'pending'");
        echo "[✓] Migrated 'inquiries' table: added 'status' column.\n";
    }
    if (!in_array('job_title', $cols)) {
        $db->exec("ALTER TABLE inquiries ADD COLUMN job_title TEXT NOT NULL DEFAULT ''");
        echo "[✓] Migrated 'inquiries' table: added 'job_title' column.\n";
    }
    echo "[✓] Table 'inquiries' created/verified.\n";

    // ── 3.5. Create 'blogs' and 'gallery' tables ───────────────────────────
    $db->exec("
        CREATE TABLE IF NOT EXISTS blogs (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            title       TEXT    NOT NULL,
            category    TEXT    NOT NULL,
            content     TEXT    NOT NULL,
            icon        TEXT    NOT NULL DEFAULT '🧠',
            created_at  TEXT    NOT NULL DEFAULT (datetime('now'))
        )
    ");
    echo "[✓] Table 'blogs' created/verified.\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS gallery (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            title       TEXT    NOT NULL,
            description TEXT    NOT NULL,
            icon        TEXT    NOT NULL DEFAULT '📷',
            color_theme TEXT    NOT NULL DEFAULT 'purple',
            created_at  TEXT    NOT NULL DEFAULT (datetime('now'))
        )
    ");
    echo "[✓] Table 'gallery' created/verified.\n";

    // Seed blogs if empty
    $count = $db->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
    if ($count == 0) {
        $db->exec("
            INSERT INTO blogs (title, category, content, icon) VALUES 
            ('The Future of Agentic Workflows', 'AI Article', 'How autonomous, multi-step AI systems are replacing traditional software pipelines in enterprise environments.', '🧠'),
            ('AI-Solutions Agent V2 is Live', 'Company News', 'Our next-generation assistant model is now available to all clients, featuring faster responses and better context handling.', '🚀'),
            ('Why Prototyping First Saves Money', 'Guide', 'A practical look at why building a quick prototype before full development saves time, money, and frustration.', '📋')
        ");
        echo "[✓] Seeded default blogs.\n";
    }

    // Seed gallery if empty
    $count = $db->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
    if ($count == 0) {
        $db->exec("
            INSERT INTO gallery (title, description, icon, color_theme) VALUES 
            ('AI Hackathon 2026', 'Building custom AI agents in 48 hours as a team.', '💻', 'purple'),
            ('Developer Summit', 'Presenting new AI tools to industry leaders and partners.', '📢', 'yellow'),
            ('Client Workshop', 'Hands-on API integration sessions with client teams.', '🤝', 'green'),
            ('Brainstorm Meetup', 'Monthly team sessions to design upcoming AI products.', '💡', 'red')
        ");
        echo "[✓] Seeded default gallery items.\n";
    }

    // ── 4. Seed default admin user ──────────────────────────────────────────
    $adminUser = env('ADMIN_USERNAME', 'admin');
    $adminPass = env('ADMIN_PASSWORD', 'admin');

    // Check if admin already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute([':username' => $adminUser]);
    $user = $stmt->fetch();

    $hashedPassword = password_hash($adminPass, PASSWORD_BCRYPT, ['cost' => 12]);

    if (!$user) {
        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'admin')");
        $stmt->execute([
            ':username' => $adminUser,
            ':password' => $hashedPassword,
        ]);
        echo "[✓] Default admin user created.\n";
        echo "    ┌──────────────────────────────────────┐\n";
        echo "    │  Username: $adminUser\n";
        echo "    │  Password: $adminPass\n";
        echo "    │  ⚠  Change these in .env before     │\n";
        echo "    │     deploying to production!         │\n";
        echo "    └──────────────────────────────────────┘\n";
    } else {
        $stmt = $db->prepare("UPDATE users SET password = :password, role = 'admin' WHERE username = :username");
        $stmt->execute([
            ':username' => $adminUser,
            ':password' => $hashedPassword,
        ]);
        echo "[✓] Admin user '$adminUser' credentials and role updated.\n";
    }

    echo "\n[✓] Database setup complete!\n";
    echo "    Database file: " . env('DB_PATH', 'aisolutions.db') . "\n";

} catch (PDOException $e) {
    echo "[✗] Database error: " . $e->getMessage() . "\n";
    exit(1);
}
