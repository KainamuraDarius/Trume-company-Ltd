<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$editing = ['id' => '', 'name' => '', 'role_title' => '', 'focus' => '', 'biography' => '', 'image_path' => '', 'email' => '', 'phone' => '', 'display_order' => 0, 'is_published' => 1];
if ($id) {
    $statement = db()->prepare('SELECT * FROM founders WHERE id = ?');
    $statement->execute([$id]);
    $editing = $statement->fetch() ?: $editing;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        db()->prepare('DELETE FROM founders WHERE id = ?')->execute([(int) $_POST['id']]);
        flash('notice', 'Founder removed.');
        redirect('founders.php');
    }
    $values = [trim((string) $_POST['name']), trim((string) $_POST['role_title']), trim((string) $_POST['focus']), trim((string) $_POST['biography']), trim((string) $_POST['image_path']), trim((string) $_POST['email']) ?: null, trim((string) $_POST['phone']) ?: null, (int) $_POST['display_order'], isset($_POST['is_published']) ? 1 : 0];
    if ($values[0] === '' || $values[1] === '' || $values[2] === '' || $values[3] === '' || $values[4] === '') {
        flash('notice', 'Name, role, focus, biography and image path are required.');
        redirect('founders.php' . ($id ? '?id=' . $id : ''));
    }
    if ($id) {
        $values[] = $id;
        db()->prepare('UPDATE founders SET name=?, role_title=?, focus=?, biography=?, image_path=?, email=?, phone=?, display_order=?, is_published=? WHERE id=?')->execute($values);
        flash('notice', 'Founder updated.');
    } else {
        db()->prepare('INSERT INTO founders (name, role_title, focus, biography, image_path, email, phone, display_order, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)')->execute($values);
        flash('notice', 'Founder added.');
    }
    redirect('founders.php');
}
$founders = db()->query('SELECT * FROM founders ORDER BY display_order, name')->fetchAll();
$pageTitle = $id ? 'Edit founder' : 'Founders';
$activePage = 'founders';
require __DIR__ . '/partials/header.php';
?>
<div class="page-actions"><p class="hint">Use a relative path such as <code>assets/Nelson.jpeg</code> for photos already on the site.</p><?php if ($id): ?><a class="button secondary" href="founders.php">Add another founder</a><?php endif; ?></div>
<section class="panel"><h2><?= $id ? 'Update profile' : 'Add founder' ?></h2><form method="post" class="form-grid"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Name<input name="name" value="<?= e($editing['name']) ?>" required></label><label>Role title<input name="role_title" value="<?= e($editing['role_title']) ?>" required></label><label>Focus summary<input name="focus" value="<?= e($editing['focus']) ?>" required></label><label>Photo path<input name="image_path" value="<?= e($editing['image_path']) ?>" placeholder="assets/person.jpeg" required></label><label>Email<input name="email" type="email" value="<?= e($editing['email']) ?>"></label><label>Phone<input name="phone" value="<?= e($editing['phone']) ?>"></label><label>Display order<input name="display_order" type="number" min="0" value="<?= e((string) $editing['display_order']) ?>"></label><label><span>Publication</span><span><input name="is_published" type="checkbox" value="1" <?= $editing['is_published'] ? 'checked' : '' ?>> Show this profile publicly</span></label><label class="full">Biography<textarea name="biography" required><?= e($editing['biography']) ?></textarea></label><div class="form-actions full"><button class="button" type="submit">Save founder</button><?php if ($id): ?><a href="founders.php">Cancel</a><?php endif; ?></div></form></section>
<section class="panel"><h2>All founders</h2><?php if (!$founders): ?><p class="empty">No founder profiles have been added yet.</p><?php else: ?><div class="table-wrap"><table class="data-table"><thead><tr><th>Name</th><th>Role</th><th>Visibility</th><th></th></tr></thead><tbody><?php foreach ($founders as $founder): ?><tr><td><strong><?= e($founder['name']) ?></strong><br><span class="hint"><?= e($founder['focus']) ?></span></td><td><?= e($founder['role_title']) ?></td><td><span class="tag <?= $founder['is_published'] ? '' : 'draft' ?>"><?= $founder['is_published'] ? 'Published' : 'Hidden' ?></span></td><td><a href="founders.php?id=<?= $founder['id'] ?>">Edit</a> · <form class="inline-form" method="post" onsubmit="return confirm('Remove this founder?');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $founder['id'] ?>"><button type="submit">Remove</button></form></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php require __DIR__ . '/partials/footer.php';
