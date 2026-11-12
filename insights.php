<?php
declare(strict_types=1);
require __DIR__ . '/includes/site.php';
$content = page_content('insights');
render_header([
    'title' => $content['page_title'],
    'description' => $content['meta_description'],
    'path' => 'insights.php',
    'body_class' => 'insights-page',
    'active' => 'insights',
]);
?>
    <main>
<?php render_hero($content); ?>
      <section class="content insights-intro">
        <p class="section-label"><?= e($content['intro_label']) ?></p>
        <h2><?= rich($content['intro_heading']) ?></h2>
        <?= paragraphs($content['intro_body'], '        ') ?>
      </section>
      <section class="content articles-section">
        <div class="article-grid">
<?php $articles = articles(); ?>
<?php if (!$articles): ?>
          <p>New insights are on the way. Please check back soon.</p>
<?php endif; ?>
<?php foreach ($articles as $index => $article): ?>
<?php $link = 'article.php?slug=' . rawurlencode($article['slug']); ?>
          <article class="article-card<?= $index === 0 ? ' featured-article' : '' ?>">
<?php if (!empty($article['image_path'])): ?>
            <a href="<?= e($link) ?>" class="article-image"
              ><img loading="lazy" decoding="async" src="<?= e(asset_url($article['image_path'])) ?>" alt="<?= e($article['image_alt'] ?? '') ?>"
            /></a>
<?php endif; ?>
            <p class="article-meta"><?= e($article['category']) ?> · <?= e(display_date($article['published_at'])) ?></p>
            <h2>
              <a href="<?= e($link) ?>"><?= e(plain($article['title'])) ?></a>
            </h2>
            <p><?= e($article['excerpt']) ?></p>
            <a class="arrow-link" href="<?= e($link) ?>"
              >Read article <span>&rarr;</span></a
            >
          </article>
<?php endforeach; ?>
        </div>
      </section>
    </main>
<?php render_footer();
