<?php
declare(strict_types=1);
require __DIR__ . '/includes/site.php';
$content = page_content('home');
render_header([
    'title' => $content['page_title'],
    'description' => $content['meta_description'],
    'path' => '',
    'body_class' => 'home-page',
    'active' => 'home',
]);
?>
    <main>
      <section class="home-hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
          <p class="eyebrow light">
            <span></span> <?= e($content['hero_eyebrow']) ?></p>
          <?= typewriter_heading($content['hero_heading']) ?>
          <p class="intro"><?= rich($content['hero_body']) ?></p>
          <div class="hero-actions">
            <a class="button button-lime" href="truehome.php"
              >Discover Truehome <span>&rarr;</span></a
            ><a class="text-link light" href="about.php"
              >Learn About Us <span>&rarr;</span></a
            >
          </div>
        </div>
        <p class="hero-location">KAMPALA, UGANDA <span>01 / 01</span></p>
      </section>
      <section class="home-intro">
        <p class="section-label"><?= e($content['intro_label']) ?></p>
        <div>
          <h2><?= rich($content['intro_heading']) ?></h2>
          <?= paragraphs($content['intro_body'], '          ') ?>
          <a class="arrow-link" href="solutions.php"
            >Explore our solutions <span>&rarr;</span></a
          >
        </div>
      </section>
      <section class="home-cards">
        <a href="truehome.php" class="feature-card dark-card"
          ><span class="card-number">01</span>
          <p>OUR FLAGSHIP PLATFORM</p>
          <h3><b>TRUE</b>HOME</h3>
          <span class="card-arrow">&#8599;</span></a
        ><a href="about.php" class="feature-card sand-card"
          ><span class="card-number">02</span>
          <p>BUILT ON TRUST</p>
          <h3>Local insight.<br /><em>Real progress.</em></h3>
          <span class="card-arrow">&#8599;</span></a
        >
      </section>
      <section class="home-cta">
        <p class="section-label">LET&rsquo;S BUILD WHAT&rsquo;S NEXT</p>
        <h2>Property, made<br /><em>more trusted.</em></h2>
        <a class="button button-dark" href="contact.php"
          >Start a conversation <span>&rarr;</span></a
        >
      </section>
    </main>
<?php render_footer();
