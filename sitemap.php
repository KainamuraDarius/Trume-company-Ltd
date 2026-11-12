<?php
// XML sitemap for search engines, including every published Insights article.
declare(strict_types=1);
require __DIR__ . '/includes/site.php';

header('Content-Type: application/xml; charset=UTF-8');
$urls = ['', 'about.php', 'solutions.php', 'truehome.php', 'partners.php', 'insights.php', 'contact.php'];
foreach (articles() as $article) {
    $urls[] = 'article.php?slug=' . rawurlencode($article['slug']);
}
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";
foreach ($urls as $path) {
    echo '  <url><loc>', e(absolute_url($path)), '</loc></url>', "\n";
}
echo "</urlset>\n";
