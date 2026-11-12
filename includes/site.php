<?php
// Shared helpers for the public website.
// Content comes from the shared SQLite database written by Trume Admin.
// If the database is missing or unreadable, the site falls back to includes/defaults.php,
// so visitors always see a working page.
declare(strict_types=1);

const SITE_ROOT = __DIR__ . '/..';

function site_config(): array
{
    static $config;
    if ($config === null) {
        $path = SITE_ROOT . '/config.php';
        $config = is_file($path) ? (array) require $path : [];
        $config += [
            'database_path' => '',
            'site_url' => 'https://trumecompanyltd.truehome.com.ug',
        ];
    }
    return $config;
}

function defaults(): array
{
    static $defaults;
    return $defaults ??= require __DIR__ . '/defaults.php';
}

/** Returns the shared database, or null when it is not available. */
function site_db(): ?PDO
{
    static $pdo = false;
    if ($pdo !== false) {
        return $pdo;
    }
    $pdo = null;
    $path = site_config()['database_path'];
    // Never create an empty database here: the admin owns the schema.
    if ($path === '' || !is_file($path)) {
        return null;
    }
    try {
        $pdo = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA busy_timeout = 5000');
        $pdo->query('SELECT 1 FROM pages LIMIT 1');
    } catch (Throwable $exception) {
        error_log('Trume site: database unavailable: ' . $exception->getMessage());
        $pdo = null;
    }
    return $pdo;
}

function db_rows(string $sql, array $params = []): ?array
{
    $db = site_db();
    if ($db === null) {
        return null;
    }
    try {
        $statement = $db->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    } catch (Throwable $exception) {
        error_log('Trume site: query failed: ' . $exception->getMessage());
        return null;
    }
}

function page_content(string $slug): array
{
    $fallback = defaults()['pages'][$slug] ?? [];
    $rows = db_rows('SELECT * FROM pages WHERE slug = ?', [$slug]);
    if (!$rows) {
        return $fallback;
    }
    // Empty database fields fall back to the default text, so a page never loses its heading.
    $merged = $fallback;
    foreach ($rows[0] as $key => $value) {
        if (is_string($value) && trim($value) !== '') {
            $merged[$key] = $value;
        }
    }
    return $merged;
}

function setting(string $key): string
{
    static $settings;
    if ($settings === null) {
        $settings = defaults()['settings'];
        foreach (db_rows('SELECT setting_key, setting_value FROM site_settings') ?? [] as $row) {
            if (trim((string) $row['setting_value']) !== '') {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        }
    }
    return (string) ($settings[$key] ?? '');
}

function founders(): array
{
    return db_rows('SELECT * FROM founders WHERE is_published = 1 ORDER BY display_order, name')
        ?? defaults()['founders'];
}

function articles(): array
{
    $rows = db_rows("SELECT * FROM articles WHERE status = 'published' ORDER BY published_at DESC, id DESC");
    if ($rows !== null) {
        return $rows;
    }
    $articles = defaults()['articles'];
    usort($articles, static fn ($a, $b) => strcmp($b['published_at'], $a['published_at']));
    return $articles;
}

function article(string $slug): ?array
{
    foreach (articles() as $article) {
        if ($article['slug'] === $slug) {
            return $article;
        }
    }
    return null;
}

// ---------------------------------------------------------------- formatting

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Escapes text and turns *highlighted words* into <em>highlighted words</em>. */
function rich(string $text): string
{
    return preg_replace('/\*(.+?)\*/s', '<em>$1</em>', e($text));
}

/** Plain text with the *highlight* markers removed. */
function plain(string $text): string
{
    return str_replace('*', '', $text);
}

/** Splits text on blank lines into <p> paragraphs; "## " lines become <h2> subheadings. */
function paragraphs(string $text, string $indent = '          '): string
{
    $blocks = preg_split('/\R\s*\R/', trim($text)) ?: [];
    $html = [];
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }
        $html[] = str_starts_with($block, '## ')
            ? '<h2>' . rich(substr($block, 3)) . '</h2>'
            : '<p>' . nl2br(rich($block), false) . '</p>';
    }
    return implode("\n" . $indent, $html);
}

function split_list(string $value): array
{
    return array_values(array_filter(array_map('trim', explode(',', $value)), 'strlen'));
}

function tel_href(string $phone): string
{
    return 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
}

function display_date(?string $date): string
{
    $time = $date ? strtotime($date) : false;
    return $time ? strtoupper(date('j F Y', $time)) : '';
}

/** Turns a stored path into a URL usable from any public page. */
function asset_url(string $path): string
{
    return preg_match('#^https?://#', $path) ? $path : ltrim($path, '/');
}

function absolute_url(string $path = ''): string
{
    return rtrim(site_config()['site_url'], '/') . '/' . ltrim($path, '/');
}

// ---------------------------------------------------------------- layout

const NAV_ITEMS = [
    'home' => ['./', 'Home'],
    'about' => ['about.php', 'About Us'],
    'solutions' => ['solutions.php', 'Our Solutions'],
    'truehome' => ['truehome.php', 'Truehome'],
    'partners' => ['partners.php', 'Our Partners'],
    'insights' => ['insights.php', 'Insights'],
    'contact' => ['contact.php', 'Contact Us'],
];

/**
 * Prints the document head and site header.
 * $page keys: title, description, path, body_class, active, type (website|article), image
 */
function render_header(array $page): void
{
    $title = $page['title'];
    $description = $page['description'] ?? '';
    $url = absolute_url($page['path'] ?? '');
    $image = absolute_url($page['image'] ?? 'assets/og-image.jpg');
    $active = $page['active'] ?? '';
    ?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= e($description) ?>" />
    <meta name="theme-color" content="#17281e" />
    <link rel="canonical" href="<?= e($url) ?>" />
    <link rel="icon" href="favicon.svg" type="image/svg+xml" />
    <link rel="icon" href="favicon.ico" sizes="32x32" />
    <link rel="apple-touch-icon" href="apple-touch-icon.png" />
    <meta property="og:type" content="<?= e($page['type'] ?? 'website') ?>" />
    <meta property="og:site_name" content="Trume Company Limited" />
    <meta property="og:title" content="<?= e($title) ?>" />
    <meta property="og:description" content="<?= e($description) ?>" />
    <meta property="og:url" content="<?= e($url) ?>" />
    <meta property="og:image" content="<?= e($image) ?>" />
    <meta property="og:locale" content="en_UG" />
    <meta name="twitter:card" content="summary_large_image" />
    <title><?= e($title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&amp;family=Manrope:wght@400;500;600;700;800&amp;family=Playfair+Display:ital,wght@0,600;0,700;1,600&amp;display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body class="<?= e($page['body_class'] ?? '') ?>">
    <header class="site-header">
      <a class="brand" href="./"
        ><span>TRU</span>ME<span class="brand-dot">.</span></a
      ><button
        class="menu-toggle"
        aria-label="Open navigation"
        aria-expanded="false"
      >
        <i></i><i></i>
      </button>
      <nav class="nav">
<?php foreach (NAV_ITEMS as $key => [$href, $label]): ?>
        <a<?= $key === $active ? ' class="active"' : '' ?> href="<?= $href ?>"><?= e($label) ?></a>
<?php endforeach; ?>
      </nav>
    </header>
<?php
}

function render_footer(bool $onContactPage = false): void
{
    $email = setting('company_email');
    $phone = setting('company_phone');
    ?>
    <footer>
      <div class="footer-main">
        <a class="brand" href="./"
          ><span>TRU</span>ME<span class="brand-dot">.</span></a
        >
        <div class="footer-contact">
          <span>GET IN TOUCH</span
          ><a href="mailto:<?= e($email) ?>"><?= e($email) ?></a
          ><a href="<?= e(tel_href($phone)) ?>"><?= e($phone) ?></a
          ><small><?= e(short_address()) ?></small>
        </div>
<?php if ($onContactPage): ?>
        <a href="./">Back home &uarr;</a>
<?php else: ?>
        <a href="contact.php">Contact us &#8599;</a>
<?php endif; ?>
      </div>
      <div class="footer-bottom">
        <span>&copy; <span id="year"><?= date('Y') ?></span> Trume Company Limited</span
        ><span>Kampala, Uganda</span>
      </div>
    </footer>
    <script src="script.js"></script>
  </body>
</html>
<?php
}

/** "National ICT Innovation Hub, Nakawa, Kampala, Uganda" -> ["National ICT Innovation Hub", "Nakawa, Kampala, Uganda"] */
function address_lines(): array
{
    $parts = split_list(setting('company_address'));
    return count($parts) > 1 ? [$parts[0], implode(', ', array_slice($parts, 1))] : $parts;
}

/** Home page heading typed out letter by letter by script.js; *words* are highlighted. */
function typewriter_heading(string $heading): string
{
    $pieces = preg_split('/(\*[^*]+\*)/', $heading, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
    $html = '';
    foreach ($pieces as $piece) {
        $isHighlight = str_starts_with($piece, '*') && str_ends_with($piece, '*');
        $tag = $isHighlight ? 'em' : 'span';
        $html .= '<' . $tag . ' data-type="' . e($isHighlight ? trim($piece, '*') : $piece) . '"></' . $tag . '>';
    }
    return '<h1 class="typewriter-heading" aria-label="' . e(plain($heading)) . '">'
        . $html . '<span class="typewriter-cursor" aria-hidden="true"></span></h1>';
}

/** "National ICT Innovation Hub, Nakawa, Kampala, Uganda" -> "National ICT Innovation Hub, Nakawa" */
function short_address(): string
{
    return implode(', ', array_slice(split_list(setting('company_address')), 0, 2));
}

/** Standard page hero used by most pages. $extraHtml is trusted markup from the page template. */
function render_hero(array $content, string $extraHtml = ''): void
{
    ?>
      <section class="page-hero">
        <div>
<?php if (($content['hero_eyebrow'] ?? '') !== ''): ?>
          <p class="eyebrow"><span></span> <?= e($content['hero_eyebrow']) ?></p>
<?php endif; ?>
          <h1><?= rich($content['hero_heading'] ?? '') ?></h1>
<?= $extraHtml ?>
        </div>
      </section>
<?php
}
