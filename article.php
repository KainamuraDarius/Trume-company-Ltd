<?php
declare(strict_types=1);
require __DIR__ . '/includes/site.php';

$slug = preg_replace('/[^a-z0-9-]/', '', strtolower((string) ($_GET['slug'] ?? '')));
$article = $slug !== '' ? article($slug) : null;

if ($article === null) {
    http_response_code(404);
    readfile(__DIR__ . '/404.html');
    exit;
}

$description = trim((string) ($article['meta_description'] ?? '')) ?: $article['excerpt'];
render_header([
    'title' => plain($article['title']) . ' | Trume Insights',
    'description' => $description,
    'path' => 'article.php?slug=' . rawurlencode($article['slug']),
    'body_class' => 'article-page',
    'active' => 'insights',
    'type' => 'article',
    'image' => !empty($article['image_path']) && !preg_match('#^https?://#', $article['image_path'])
        ? $article['image_path'] : 'assets/og-image.jpg',
]);
?>
    <main>
      <section class="page-hero">
        <div>
          <p class="eyebrow">
            <span></span> <?= e($article['category']) ?> · <?= e(display_date($article['published_at'])) ?>

          </p>
          <h1><?= rich($article['title']) ?></h1>
        </div>
      </section>
      <article class="article-content">
<?php if (!empty($article['image_path'])): ?>
        <img
          class="article-feature"
          src="<?= e(asset_url($article['image_path'])) ?>"
          alt="<?= e($article['image_alt'] ?? '') ?>"
        />
<?php endif; ?>
        <div class="article-body">
          <?= paragraphs($article['body']) ?>

          <a class="arrow-link article-back" href="insights.php"
            >Back to Insights <span>&larr;</span></a
          >
        </div>
      </article>
    </main>
<?php render_footer();
