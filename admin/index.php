<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require_login();
$counts = [
    'founders' => (int) db()->query('SELECT COUNT(*) FROM founders WHERE is_published = 1')->fetchColumn(),
    'articles' => (int) db()->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn(),
    'messages' => (int) db()->query("SELECT COUNT(*) FROM messages WHERE status = 'new'")->fetchColumn(),
    'media' => (int) db()->query('SELECT COUNT(*) FROM media')->fetchColumn(),
];
$latest = db()->query('SELECT full_name, subject, created_at FROM messages ORDER BY created_at DESC LIMIT 5')->fetchAll();
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require __DIR__ . '/partials/header.php';
?>
<section class="stats"><article class="stat"><span>PUBLISHED FOUNDERS</span><b><?= $counts['founders'] ?></b></article><article class="stat"><span>PUBLISHED INSIGHTS</span><b><?= $counts['articles'] ?></b></article><article class="stat"><span>NEW MESSAGES</span><b><?= $counts['messages'] ?></b></article><article class="stat"><span>MEDIA FILES</span><b><?= $counts['media'] ?></b></article></section>
<section class="quick-actions"><a href="pages.php">Edit page copy <span>→</span></a><a href="media.php">Upload media <span>→</span></a><a href="settings.php">Update site details <span>→</span></a><a href="articles.php">Write an insight <span>→</span></a></section>
<section class="panel"><h2>Latest contact messages</h2><?php if (!$latest): ?><p class="empty">No messages yet. Connect the public contact form after installation to start receiving enquiries here.</p><?php else: ?><div class="table-wrap"><table class="data-table"><thead><tr><th>From</th><th>Subject</th><th>Received</th></tr></thead><tbody><?php foreach ($latest as $message): ?><tr><td><?= e($message['full_name']) ?></td><td><?= e($message['subject']) ?></td><td><?= e($message['created_at']) ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/partials/footer.php';
