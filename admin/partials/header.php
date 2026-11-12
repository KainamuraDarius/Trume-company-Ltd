<?php
/** @var string $pageTitle */
/** @var string $activePage */
$admin = current_admin();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex,nofollow" />
    <title><?= e($pageTitle) ?> | Trume Admin</title>
    <link rel="stylesheet" href="assets/admin.css" />
  </head>
  <body>
    <div class="admin-shell">
      <aside class="sidebar">
        <a class="admin-brand" href="index.php"><span>TRU</span>ME<small>ADMIN</small></a>
        <nav aria-label="Admin navigation">
          <a class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" href="index.php">Dashboard</a>
          <a class="<?= $activePage === 'founders' ? 'active' : '' ?>" href="founders.php">Founders</a>
          <a class="<?= $activePage === 'articles' ? 'active' : '' ?>" href="articles.php">Insights</a>
          <a class="<?= $activePage === 'pages' ? 'active' : '' ?>" href="pages.php">Pages</a>
          <a class="<?= $activePage === 'media' ? 'active' : '' ?>" href="media.php">Media</a>
          <a class="<?= $activePage === 'messages' ? 'active' : '' ?>" href="messages.php">Messages</a>
          <a class="<?= $activePage === 'settings' ? 'active' : '' ?>" href="settings.php">Site settings</a>
          <a class="<?= $activePage === 'account' ? 'active' : '' ?>" href="account.php">My account</a>
          <?php if (($admin['role'] ?? '') === 'owner'): ?><a class="<?= $activePage === 'admins' ? 'active' : '' ?>" href="admins.php">Administrators</a><?php endif; ?>
        </nav>
        <div class="sidebar-bottom"><a href="../index.html" target="_blank" rel="noopener">View website ↗</a><a href="logout.php">Sign out</a></div>
      </aside>
      <main class="admin-main">
        <header class="admin-header"><div><p class="eyebrow">TRUME COMPANY LIMITED</p><h1><?= e($pageTitle) ?></h1></div><p class="admin-user"><?= e($admin['name'] ?? '') ?></p></header>
        <?php if ($notice = flash('notice')): ?><div class="notice" role="status"><?= e($notice) ?></div><?php endif; ?>
