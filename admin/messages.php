<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $status = in_array($_POST['status'] ?? '', ['new', 'read', 'archived'], true) ? $_POST['status'] : 'read';
    db()->prepare('UPDATE messages SET status = ? WHERE id = ?')->execute([$status, (int) $_POST['id']]);
    flash('notice', 'Message status updated.');
    redirect('messages.php');
}
$messages = db()->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Messages'; $activePage = 'messages'; require __DIR__ . '/partials/header.php';
?>
<section class="panel"><h2>Website enquiries</h2><?php if (!$messages): ?><p class="empty">No contact messages have been received yet.</p><?php else: ?><div class="table-wrap"><table class="data-table"><thead><tr><th>Contact</th><th>Message</th><th>Status</th><th>Received</th><th></th></tr></thead><tbody><?php foreach ($messages as $message): ?><tr><td><strong><?= e($message['full_name']) ?></strong><br><a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a><?php if ($message['phone']): ?><br><a href="tel:<?= e($message['phone']) ?>"><?= e($message['phone']) ?></a><?php endif; ?></td><td><strong><?= e($message['subject']) ?></strong><br><?= nl2br(e($message['message'])) ?></td><td><span class="tag <?= e($message['status']) ?>"><?= e(ucfirst($message['status'])) ?></span></td><td><?= e($message['created_at']) ?></td><td><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= $message['id'] ?>"><select name="status" aria-label="Message status"><option value="new" <?= $message['status']==='new'?'selected':'' ?>>New</option><option value="read" <?= $message['status']==='read'?'selected':'' ?>>Read</option><option value="archived" <?= $message['status']==='archived'?'selected':'' ?>>Archived</option></select><button class="button" type="submit">Save</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/partials/footer.php';
