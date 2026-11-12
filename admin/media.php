<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
$admin = require_login();
$uploadDirectory = dirname(__DIR__) . '/uploads';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (($_POST['action'] ?? '') === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $query = db()->prepare('SELECT stored_path FROM media WHERE id = ?'); $query->execute([$id]); $file = $query->fetch();
        if ($file) { $path = dirname(__DIR__) . '/' . $file['stored_path']; if (is_file($path) && str_starts_with(realpath($path) ?: '', realpath($uploadDirectory) ?: '')) { unlink($path); } db()->prepare('DELETE FROM media WHERE id = ?')->execute([$id]); log_activity('deleted', 'media', $id); }
        flash('notice', 'Media file removed.'); redirect('media.php');
    }
    $file = $_FILES['file'] ?? null;
    if (!$file || $file['error'] !== UPLOAD_ERR_OK || $file['size'] > 8 * 1024 * 1024) { flash('notice', 'Choose an image smaller than 8 MB.'); redirect('media.php'); }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if (!isset($extensions[$mime]) || !getimagesize($file['tmp_name'])) { flash('notice', 'Only valid JPG, PNG, WebP and GIF images are allowed.'); redirect('media.php'); }
    if (!is_dir($uploadDirectory)) { mkdir($uploadDirectory, 0755, true); }
    $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime]; $destination = $uploadDirectory . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) { flash('notice', 'Upload failed. Please try again.'); redirect('media.php'); }
    $storedPath = 'uploads/' . $filename;
    db()->prepare('INSERT INTO media (original_name, stored_path, mime_type, file_size, alt_text, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)')->execute([basename((string)$file['name']), $storedPath, $mime, (int)$file['size'], trim((string)($_POST['alt_text'] ?? '')), $admin['id']]);
    log_activity('uploaded', 'media'); flash('notice', 'Image uploaded. Copy its path when adding it to page content.'); redirect('media.php');
}
$media = db()->query('SELECT * FROM media ORDER BY created_at DESC')->fetchAll(); $pageTitle = 'Media library'; $activePage = 'media'; require __DIR__ . '/partials/header.php';
?>
<section class="panel"><h2>Upload image</h2><p>Accepted formats: JPG, PNG, WebP and GIF. Maximum size: 8 MB. Add meaningful alternative text for accessibility.</p><form method="post" enctype="multipart/form-data" class="form-grid"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Image file<input name="file" type="file" accept="image/jpeg,image/png,image/webp,image/gif" required></label><label>Alternative text<input name="alt_text" placeholder="Describe what the image shows"></label><div class="form-actions full"><button class="button" type="submit">Upload image</button></div></form></section>
<section class="panel"><h2>Library</h2><?php if (!$media): ?><p class="empty">No uploaded images yet.</p><?php else: ?><div class="media-grid"><?php foreach ($media as $item): ?><article class="media-card"><img src="../<?= e($item['stored_path']) ?>" alt="<?= e($item['alt_text']) ?>"><div><strong><?= e($item['original_name']) ?></strong><code><?= e($item['stored_path']) ?></code><small><?= e($item['mime_type']) ?> · <?= number_format((int)$item['file_size'] / 1024, 1) ?> KB</small><form method="post" onsubmit="return confirm('Permanently remove this file?');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $item['id'] ?>"><button class="link-danger" type="submit">Remove</button></form></div></article><?php endforeach; ?></div><?php endif; ?></section>
<?php require __DIR__ . '/partials/footer.php';
