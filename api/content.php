<?php
declare(strict_types=1);
require __DIR__ . '/../admin/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
$slug = preg_replace('/[^a-z-]/', '', (string) ($_GET['page'] ?? ''));
if ($slug === '') { http_response_code(400); echo json_encode(['error' => 'A page is required.']); exit; }
$page = db()->prepare('SELECT slug, page_title, meta_description, hero_eyebrow, hero_heading, intro_label, intro_heading, intro_body FROM pages WHERE slug = ?');
$page->execute([$slug]);
$content = $page->fetch();
if (!$content) { http_response_code(404); echo json_encode(['error' => 'Page not found.']); exit; }
$settings = array_column(db()->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll(), 'setting_value', 'setting_key');
echo json_encode(['page' => $content, 'settings' => $settings], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
