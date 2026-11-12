<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require_login();
$slug = preg_replace('/[^a-z-]/', '', (string) ($_GET['page'] ?? ''));
$selected = null;
if ($slug !== '') { $get = db()->prepare('SELECT * FROM pages WHERE slug = ?'); $get->execute([$slug]); $selected = $get->fetch(); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(); $slug = (string) $_POST['slug'];
    $values = [trim((string)$_POST['page_title']), trim((string)$_POST['meta_description']), trim((string)$_POST['hero_eyebrow']), trim((string)$_POST['hero_heading']), trim((string)$_POST['intro_label']), trim((string)$_POST['intro_heading']), trim((string)$_POST['intro_body']), $slug];
    db()->prepare('UPDATE pages SET page_title=?, meta_description=?, hero_eyebrow=?, hero_heading=?, intro_label=?, intro_heading=?, intro_body=?, updated_at=CURRENT_TIMESTAMP WHERE slug=?')->execute($values);
    log_activity('updated', 'page: ' . $slug); flash('notice', 'Page content saved.'); redirect('pages.php?page=' . rawurlencode($slug));
}
$pages = db()->query('SELECT slug, page_title, updated_at FROM pages ORDER BY id')->fetchAll();
$pageTitle = $selected ? 'Edit ' . ucfirst($selected['slug']) . ' page' : 'Pages'; $activePage = 'pages'; require __DIR__ . '/partials/header.php';
?>
<?php if (!$selected): ?><section class="panel"><h2>Page content</h2><p>Choose a page to update its core headings, description and introductory copy.</p><div class="content-grid"><?php foreach($pages as $page): ?><a class="content-card" href="pages.php?page=<?= e($page['slug']) ?>"><strong><?= e(ucfirst($page['slug'])) ?></strong><span><?= e($page['page_title']) ?></span><small>Updated <?= e($page['updated_at']) ?></small></a><?php endforeach; ?></div></section><?php else: ?><div class="page-actions"><a href="pages.php">← All pages</a><a class="button secondary" href="../<?= e($selected['slug'] === 'home' ? 'index.html' : $selected['slug'] . '.html') ?>" target="_blank" rel="noopener">View current page ↗</a></div><section class="panel"><h2>Core content</h2><p>Keep headings concise and review the page after publishing.</p><form method="post" class="form-grid"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="slug" value="<?= e($selected['slug']) ?>"><label>Browser title<input name="page_title" value="<?= e($selected['page_title']) ?>" required></label><label>Meta description<input name="meta_description" value="<?= e($selected['meta_description']) ?>"></label><label>Hero eyebrow<input name="hero_eyebrow" value="<?= e($selected['hero_eyebrow']) ?>"></label><label>Hero heading<input name="hero_heading" value="<?= e($selected['hero_heading']) ?>"></label><label>Introduction label<input name="intro_label" value="<?= e($selected['intro_label']) ?>"></label><label>Introduction heading<input name="intro_heading" value="<?= e($selected['intro_heading']) ?>"></label><label class="full">Introduction copy<textarea name="intro_body"><?= e($selected['intro_body']) ?></textarea></label><div class="form-actions full"><button class="button" type="submit">Save page content</button></div></form></section><?php endif; ?>
<?php require __DIR__ . '/partials/footer.php';
