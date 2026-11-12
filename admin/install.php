<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$error = '';
$configured = false;
try {
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    db()->exec((string) $schema);
    $configured = (int) db()->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0;
} catch (Throwable $exception) {
    $error = 'Database connection failed. Check admin/config.php and create the database in cPanel first.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$configured && !$error) {
    $settings = config();
    $setupKey = (string) ($_POST['setup_key'] ?? '');
    $email = trim((string) ($_POST['email'] ?? ''));
    $name = trim((string) ($_POST['name'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if (!hash_equals($settings['setup_key'], $setupKey)) {
        $error = 'The setup key is incorrect.';
    } elseif ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) {
        $error = 'Enter your name, a valid email address, and a password of at least 12 characters.';
    } else {
        $statement = db()->prepare("INSERT INTO admins (name, email, password_hash, role) VALUES (?, ?, ?, 'owner')");
        $statement->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        flash('notice', 'Administrator created. You can now sign in.');
        redirect('login.php');
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Install Trume Admin</title><link rel="stylesheet" href="assets/admin.css"></head><body class="login"><main class="login-card"><p class="eyebrow">TRUME ADMIN SETUP</p><h1>Create the first administrator</h1><?php if ($configured): ?><p>This admin is already installed. <a href="login.php">Sign in</a>.</p><?php else: ?><?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?><form method="post"><label>Setup key<input name="setup_key" type="password" required></label><br><label>Full name<input name="name" required></label><br><label>Email address<input name="email" type="email" required></label><br><label>Password<input name="password" type="password" minlength="12" required></label><p class="hint">Use a unique password with at least 12 characters.</p><button class="button" type="submit">Create administrator</button></form><?php endif; ?></main></body></html>
