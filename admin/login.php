<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
start_session();
if (current_admin() !== null && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $statement = db()->prepare('SELECT id, name, email, password_hash, role FROM admins WHERE email = ? AND is_active = 1 LIMIT 1');
    $statement->execute([$email]);
    $admin = $statement->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin'] = ['id' => $admin['id'], 'name' => $admin['name'], 'email' => $admin['email'], 'role' => $admin['role']];
        db()->prepare('UPDATE admins SET last_login_at = CURRENT_TIMESTAMP WHERE id = ?')->execute([$admin['id']]);
        redirect('index.php');
    }
    $error = 'Invalid email address or password.';
}
?>
<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta name="robots" content="noindex,nofollow"><title>Sign in | Trume Admin</title><link rel="stylesheet" href="assets/admin.css"></head><body class="login"><main class="login-card"><p class="eyebrow">TRUME COMPANY LIMITED</p><h1>Admin sign in</h1><?php if ($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Email address<input name="email" type="email" autocomplete="email" required></label><br><label>Password<input name="password" type="password" autocomplete="current-password" required></label><br><button class="button" type="submit">Sign in</button></form><p class="hint">First-time setup? Open <code>install.php</code> after creating <code>config.php</code>.</p></main></body></html>
