<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
$owner = require_owner();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? 'create';
    if ($action === 'create') {
        $name = trim((string)($_POST['name'] ?? '')); $email = trim((string)($_POST['email'] ?? '')); $password = (string)($_POST['password'] ?? '');
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 12) { $error = 'Enter a name, valid email address, and a password of at least 12 characters.'; }
        else { try { db()->prepare("INSERT INTO admins (name, email, password_hash, role) VALUES (?, ?, ?, 'admin')")->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]); log_activity('created', 'administrator'); flash('notice', 'Administrator account created. Share the sign-in URL and temporary password securely.'); redirect('admins.php'); } catch (PDOException $exception) { $error = 'That email address already has an account.'; } }
    }
    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0); $active = (int)($_POST['is_active'] ?? 0);
        if ($id !== (int)$owner['id']) { db()->prepare("UPDATE admins SET is_active = ? WHERE id = ? AND role = 'admin'")->execute([$active ? 0 : 1, $id]); log_activity($active ? 'deactivated' : 'reactivated', 'administrator', $id); flash('notice', 'Administrator access updated.'); }
        redirect('admins.php');
    }
}
$admins = db()->query('SELECT id, name, email, role, is_active, created_at, last_login_at FROM admins ORDER BY CASE role WHEN "owner" THEN 0 ELSE 1 END, created_at')->fetchAll();
$pageTitle = 'Administrators'; $activePage = 'admins'; require __DIR__ . '/partials/header.php';
?>
<section class="panel"><h2>Create administrator</h2><p>Only you, the account owner, can create or disable administrator accounts. New accounts can manage site content but cannot create other administrators.</p><?php if($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?><form method="post" class="form-grid"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create"><label>Full name<input name="name" required></label><label>Email address<input name="email" type="email" required></label><label class="full">Temporary password <span class="hint">At least 12 characters. Share it securely and ask the person to change it after first sign-in.</span><input name="password" type="password" minlength="12" autocomplete="new-password" required></label><div class="form-actions full"><button class="button" type="submit">Create administrator</button></div></form></section>
<section class="panel"><h2>Account access</h2><div class="table-wrap"><table class="data-table"><thead><tr><th>Administrator</th><th>Role</th><th>Status</th><th>Last sign-in</th><th></th></tr></thead><tbody><?php foreach($admins as $admin): ?><tr><td><strong><?= e($admin['name']) ?></strong><br><span class="hint"><?= e($admin['email']) ?></span></td><td><span class="tag <?= $admin['role'] === 'owner' ? '' : 'draft' ?>"><?= e(ucfirst($admin['role'])) ?></span></td><td><span class="tag <?= $admin['is_active'] ? '' : 'draft' ?>"><?= $admin['is_active'] ? 'Active' : 'Disabled' ?></span></td><td><?= e($admin['last_login_at'] ?: 'Never') ?></td><td><?php if($admin['role'] === 'admin'): ?><form method="post" class="inline-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $admin['id'] ?>"><input type="hidden" name="is_active" value="<?= $admin['is_active'] ?>"><button class="<?= $admin['is_active'] ? 'link-danger' : '' ?>" type="submit"><?= $admin['is_active'] ? 'Disable' : 'Enable' ?></button></form><?php else: ?><span class="hint">Owner account</span><?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div></section>
<?php require __DIR__ . '/partials/footer.php';
