<?php
declare(strict_types=1);
require __DIR__ . '/includes/site.php';
$content = page_content('about');
render_header([
    'title' => $content['page_title'],
    'description' => $content['meta_description'],
    'path' => 'about.php',
    'body_class' => 'about-page',
    'active' => 'about',
]);
?>
    <main>
<?php render_hero($content); ?>
      <section class="about-summary">
        <div class="content two-column overview">
          <div>
            <p class="section-label"><?= e($content['intro_label']) ?></p>
            <h2><?= rich($content['intro_heading']) ?></h2>
          </div>
          <div>
            <?= paragraphs($content['intro_body'], '            ') ?>
          </div>
        </div>
        <div class="content purpose-grid">
          <article>
            <p class="section-label">OUR VISION</p>
            <h2>
              To build one of Uganda&rsquo;s most trusted property technology
              platforms.
            </h2>
          </article>
          <article>
            <p class="section-label">OUR MISSION</p>
            <h2>
              To transform the property market through innovative, accessible
              and reliable digital solutions.
            </h2>
          </article>
        </div>
        <div class="content about-values">
          <p class="section-label">WHAT GUIDES US</p>
          <h2>Trust is our foundation.</h2>
          <div class="values-grid">
            <article>
              <b>01</b>
              <h3>Trust</h3>
              <p>Verified and reliable information.</p>
            </article>
            <article>
              <b>02</b>
              <h3>Innovation</h3>
              <p>Technology that solves real challenges.</p>
            </article>
            <article>
              <b>03</b>
              <h3>Transparency</h3>
              <p>Clear information and processes.</p>
            </article>
            <article>
              <b>04</b>
              <h3>Accessibility</h3>
              <p>Solutions for everyday Ugandans.</p>
            </article>
            <article>
              <b>05</b>
              <h3>Local Relevance</h3>
              <p>Built in Uganda, for Uganda.</p>
            </article>
            <article>
              <b>06</b>
              <h3>Excellence</h3>
              <p>Professional product and service standards.</p>
            </article>
          </div>
        </div>
        <div class="content two-column about-direction">
          <div>
            <p class="section-label">WHY TRUME &amp; OUR DIRECTION</p>
            <h2>Local insight, built to grow.</h2>
          </div>
          <div>
            <p>
              Trume brings property discovery online through a structured
              experience built around credibility, transparency and the real
              needs of people using it.
            </p>
            <ol class="direction-list">
              <li>
                <b>01</b>
                <div>
                  <h3>Foundation</h3>
                  <p>Build verified listings and early trust in key areas.</p>
                </div>
              </li>
              <li>
                <b>02</b>
                <div>
                  <h3>Growth</h3>
                  <p>Expand coverage and strengthen the Truehome brand.</p>
                </div>
              </li>
              <li>
                <b>03</b>
                <div>
                  <h3>Scale &amp; leadership</h3>
                  <p>
                    Add value through services and partnerships while building a
                    leading platform for Uganda.
                  </p>
                </div>
              </li>
            </ol>
          </div>
        </div>
      </section>
      <section class="founders-section" id="founders">
        <div class="content">
          <p class="section-label">THE FOUNDERS</p>
          <h2>Meet the people building Trume.</h2>
          <p class="founders-intro">
            A team bringing together technology, product development, business
            growth, financial leadership and local market understanding.
          </p>
          <div class="founder-grid">
<?php foreach (founders() as $founder): ?>
            <article class="founder-card">
              <img loading="lazy" decoding="async"
                class="founder-photo"
                src="<?= e(asset_url($founder['image_path'])) ?>"
                alt="<?= e($founder['name']) ?>"
              />
              <div class="founder-content">
                <p class="founder-role"><?= e($founder['role_title']) ?></p>
                <h3><?= e($founder['name']) ?></h3>
                <p class="founder-focus"><?= e($founder['focus']) ?></p>
                <div class="founder-bio collapsible-bio">
                  <?= paragraphs($founder['biography'], '                  ') ?>

                </div>
                <button
                  class="founder-more"
                  type="button"
                  aria-expanded="false"
                >
                  View more <span>&darr;</span>
                </button>
                <div class="founder-contact">
<?php $phones = split_list((string) ($founder['phone'] ?? '')); ?>
<?php if ($phones): ?>
                  <span><?= implode(' / ', array_map(static fn ($n) => '<a href="' . e(tel_href($n)) . '">' . e($n) . '</a>', $phones)) ?></span>
<?php endif; ?>
<?php if (!empty($founder['email'])): ?>
                  <a href="mailto:<?= e($founder['email']) ?>"><?= e($founder['email']) ?></a>
<?php endif; ?>
                </div>
              </div>
            </article>
<?php endforeach; ?>
          </div>
        </div>
      </section>
    </main>
<?php render_footer();
