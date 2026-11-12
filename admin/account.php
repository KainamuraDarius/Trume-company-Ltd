<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
$admin = require_login(); $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); $name = trim((string)$_POST['name']); $email = trim((string)$_POST['email']); $currentPassword = (string)$_POST['current_password']; $newPassword = (string)$_POST['new_password'];
    $get = db()->prepare('SELECT password_hash FROM admins WHERE id = ?'); $get->execute([$admin['id']]); $record = $get->fetch();
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = 'Enter your name and a valid email address.'; }
    elseif ($newPassword !== '' && (!password_verify($currentPassword, $record['password_hash']) || strlen($newPassword) < 12)) { $error = 'To change your password, enter your current password and a new password of at least 12 characters.'; }
    else { $hash = $newPassword !== '' ? password_hash($newPassword, PASSWORD_DEFAULT) : $record['password_hash']; try { db()->prepare('UPDATE admins SET name=?, email=?, password_hash=? WHERE id=?')->execute([$name, $email, $hash, $admin['id']]); $_SESSION['admin']['name'] = $name; $_SESSION['admin']['email'] = $email; log_activity('updated', 'account', (int)$admin['id']); flash('notice', 'Account details saved.'); redirect('account.php'); } catch (PDOException $exception) { $error = 'That email address is already in use.'; } }
}
$pageTitle = 'My account'; $activePage = 'account'; require __DIR__ . '/partials/header.php';
?>
<section class="panel"><h2>Profile and password</h2><?php if($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?><form method="post" class="form-grid"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Name<input name="name" value="<?= e($admin['name']) ?>" required></label><label>Email address<input name="email" type="email" value="<?= e($admin['email']) ?>" required></label><label>Current password<input name="current_password" type="password" autocomplete="current-password"></label><label>New password <span class="hint">Leave blank to keep it unchanged.</span><input name="new_password" type="password" minlength="12" autocomplete="new-password"></label><div class="form-actions full"><button class="button" type="submit">Save account</button></div></form></section>
<?php require __DIR__ . '/partials/footer.php';
