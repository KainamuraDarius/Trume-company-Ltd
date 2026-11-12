<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
require_login();

$fields = ['company_email' => 'Official email', 'company_phone' => 'Main phone number', 'company_address' => 'Office address', 'truehome_url' => 'Truehome website URL', 'linkedin_url' => 'LinkedIn URL', 'whatsapp_url' => 'WhatsApp URL'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $save = db()->prepare('INSERT INTO site_settings (setting_key, setting_value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP) ON CONFLICT(setting_key) DO UPDATE SET setting_value=excluded.setting_value, updated_at=CURRENT_TIMESTAMP');
    foreach ($fields as $key => $label) { $save->execute([$key, trim((string) ($_POST[$key] ?? ''))]); }
    log_activity('updated', 'site settings'); flash('notice', 'Site settings saved.'); redirect('settings.php');
}
$settings = array_column(db()->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll(), 'setting_value', 'setting_key');
$pageTitle = 'Site settings'; $activePage = 'settings'; require __DIR__ . '/partials/header.php';
?>
<section class="panel"><h2>Global website details</h2><p>Store the business and contact details that belong across the website.</p><form method="post" class="form-grid"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><?php foreach ($fields as $key => $label): ?><label><?= e($label) ?><input name="<?= e($key) ?>" value="<?= e($settings[$key] ?? '') ?>" <?= str_contains($key, 'url') ? 'type="url"' : ($key === 'company_email' ? 'type="email"' : '') ?>></label><?php endforeach; ?><div class="form-actions full"><button class="button" type="submit">Save site settings</button></div></form></section>
<?php require __DIR__ . '/partials/footer.php';
