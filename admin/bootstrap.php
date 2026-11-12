<?php
declare(strict_types=1);

const ADMIN_ROOT = __DIR__;

function config(): array
{
    static $config;
    if ($config !== null) {
        return $config;
    }

    $path = ADMIN_ROOT . '/config.php';
    if (!is_file($path)) {
        http_response_code(503);
        exit('Admin is not configured. Copy admin/config.example.php to admin/config.php.');
    }

    $config = require $path;
    return $config;
}

function db(): PDO
{
    static $pdo;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $settings = config();
    if (empty($settings['database_path'])) {
        http_response_code(503);
        exit('Admin is not configured for SQLite. Update admin/config.php from admin/config.example.php.');
    }
    $databasePath = $settings['database_path'];
    $directory = dirname($databasePath);
    if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
        throw new RuntimeException('Unable to create the SQLite database directory.');
    }
    $pdo = new PDO('sqlite:' . $databasePath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $schema = file_get_contents(ADMIN_ROOT . '/schema.sql');
    $pdo->exec((string) $schema);
    ensure_admin_access_columns($pdo);
    seed_default_content($pdo);
    return $pdo;
}

function ensure_admin_access_columns(PDO $pdo): void
{
    $columns = array_column($pdo->query('PRAGMA table_info(admins)')->fetchAll(), 'name');
    if (!in_array('role', $columns, true)) {
        $pdo->exec("ALTER TABLE admins ADD COLUMN role TEXT NOT NULL DEFAULT 'admin'");
    }
    if (!in_array('is_active', $columns, true)) {
        $pdo->exec('ALTER TABLE admins ADD COLUMN is_active INTEGER NOT NULL DEFAULT 1');
    }
    $ownerCount = (int) $pdo->query("SELECT COUNT(*) FROM admins WHERE role = 'owner'")->fetchColumn();
    if ($ownerCount === 0) {
        $firstId = $pdo->query('SELECT id FROM admins ORDER BY id LIMIT 1')->fetchColumn();
        if ($firstId !== false) {
            $pdo->prepare("UPDATE admins SET role = 'owner' WHERE id = ?")->execute([$firstId]);
        }
    }
}

function seed_default_content(PDO $pdo): void
{
    $defaults = [
        'company_email' => 'ramzyhadah@gmail.com',
        'company_phone' => '+256 778 332 065',
        'company_address' => 'National ICT Innovation Hub, Nakawa, Kampala, Uganda',
        'truehome_url' => 'https://truehome.com.ug',
        'linkedin_url' => 'https://www.linkedin.com/company/truehome-ug/',
        'whatsapp_url' => 'https://wa.me/256778332065',
    ];
    $setting = $pdo->prepare('INSERT OR IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)');
    foreach ($defaults as $key => $value) {
        $setting->execute([$key, $value]);
    }
    $pages = [
        ['home', 'Trume | Home', 'Trume Company Limited transforms Uganda\'s property market through technology.', 'A Ugandan property technology company', 'Transforming Uganda\'s property market through technology.', 'WELCOME TO TRUME', 'One trusted place for every property journey.', 'From the first search to the final decision, we are creating a clearer, more connected property experience for people across Uganda.'],
        ['about', 'About Us | Trume', '', 'ABOUT TRUME', 'Building trust into every property decision.', 'OUR COMPANY', 'A Ugandan company making property work better.', 'Trume is building digital property solutions rooted in trust, transparency and local relevance.'],
        ['solutions', 'Our Solutions | Trume', '', 'OUR SOLUTIONS', 'Better tools for a better property market.', 'PROPERTY TECHNOLOGY SOLUTIONS', 'Practical technology for real property needs.', 'We build digital products that make property discovery and management clearer and more connected.'],
        ['truehome', 'Truehome | Trume', '', 'TRUEHOME', 'Find a place you can trust.', 'WHAT IS TRUEHOME?', 'One trusted space for property discovery.', 'Truehome connects property seekers, landlords, agents and developers in one structured digital marketplace.'],
        ['partners', 'Our Partners | Trume', '', 'WORK WITH US', 'Better connections create better possibilities.', 'STRATEGIC PARTNERSHIPS', 'Reach people at the moment property decisions are made.', 'We collaborate with organisations building practical value around the property journey.'],
        ['contact', 'Contact Us | Trume', '', 'CONTACT US', 'Let\'s make property better, together.', 'GET IN TOUCH', 'We would love to hear from you.', 'Whether you have a question, want to partner with us or want to learn more about Truehome, the Trume team is ready to connect.'],
    ];
    $page = $pdo->prepare('INSERT OR IGNORE INTO pages (slug, page_title, meta_description, hero_eyebrow, hero_heading, intro_label, intro_heading, intro_body) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    foreach ($pages as $values) {
        $page->execute($values);
    }
}

function log_activity(string $action, string $entityType, ?int $entityId = null): void
{
    $admin = current_admin();
    db()->prepare('INSERT INTO activity_log (admin_id, action, entity_type, entity_id) VALUES (?, ?, ?, ?)')
        ->execute([$admin['id'] ?? null, $action, $entityType, $entityId]);
}

function start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('trume_admin');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    start_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    start_session();
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Your session has expired. Please go back and try again.');
    }
}

function current_admin(): ?array
{
    start_session();
    $admin = $_SESSION['admin'] ?? null;
    if (!is_array($admin) || empty($admin['id']) || empty($admin['email'])) {
        unset($_SESSION['admin']);
        return null;
    }
    $statement = db()->prepare('SELECT id, name, email, role, is_active FROM admins WHERE id = ? LIMIT 1');
    $statement->execute([(int) $admin['id']]);
    $account = $statement->fetch();
    if (!$account || !(int) $account['is_active']) {
        unset($_SESSION['admin']);
        return null;
    }
    $_SESSION['admin'] = $account;
    return $account;
}

function require_login(): array
{
    $admin = current_admin();
    if ($admin === null) {
        header('Location: login.php');
        exit;
    }
    return $admin;
}

function require_owner(): array
{
    $admin = require_login();
    if (($admin['role'] ?? '') !== 'owner') {
        flash('notice', 'Only the account owner can manage administrator accounts.');
        redirect('index.php');
    }
    return $admin;
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    start_session();
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}
