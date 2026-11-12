<?php
declare(strict_types=1);
require __DIR__ . '/includes/site.php';
$content = page_content('solutions');
render_header([
    'title' => $content['page_title'],
    'description' => $content['meta_description'],
    'path' => 'solutions.php',
    'body_class' => 'solutions-page',
    'active' => 'solutions',
]);
?>
    <main>
<?php render_hero($content); ?>
      <section class="solution-band">
        <div class="truehome-lockup"><b>TRUE</b>HOME</div>
        <h2>One marketplace. <em>More confidence.</em></h2>
        <p>
          Our flagship platform brings property seekers, owners, agents and
          developers together in one clear, convenient digital experience.
        </p>
        <a class="button button-lime" href="truehome.php"
          >Explore Truehome &rarr;</a
        >
      </section>
      <section class="content two-column overview">
        <div>
          <p class="section-label"><?= e($content['intro_label']) ?></p>
          <h2><?= rich($content['intro_heading']) ?></h2>
        </div>
        <div>
          <?= paragraphs($content['intro_body'], '          ') ?>
        </div>
      </section>
      <section class="content">
        <p class="section-label">OUR CURRENT SOLUTION</p>
        <h2>A digital property marketplace, built for confidence.</h2>
        <ul class="detail-list">
          <li>
            <b>01</b>
            <div>
              <h3>Property listing and discovery</h3>
              <p>
                Truehome provides a central, searchable place for people to
                discover homes, rentals, land and other property opportunities.
              </p>
            </div>
          </li>
          <li>
            <b>02</b>
            <div>
              <h3>Clearer property information</h3>
              <p>
                Structured listing details help property seekers decide whether
                a property suits their needs before arranging a visit.
              </p>
            </div>
          </li>
          <li>
            <b>03</b>
            <div>
              <h3>A connected marketplace</h3>
              <p>
                The platform creates a direct, convenient connection between
                property seekers and landlords, owners, agents and developers.
              </p>
            </div>
          </li>
          <li>
            <b>04</b>
            <div>
              <h3>Real estate digital innovation</h3>
              <p>
                A trust-first digital experience designed to replace scattered,
                informal and time-consuming property discovery methods.
              </p>
            </div>
          </li>
        </ul>
      </section>
      <section class="future-section">
        <div class="content two-column">
          <div>
            <p class="section-label">FUTURE DIGITAL SERVICES</p>
            <h2>Growing carefully, with value in mind.</h2>
          </div>
          <div>
            <p>
              As Truehome grows, Trume plans to explore additional value-added
              services and partnerships. These future services will be shaped by
              the company&rsquo;s final business plans and the needs of property
              seekers and providers.
            </p>
            <p class="future-note">
              Future offerings have not yet been announced as available
              services.
            </p>
          </div>
        </div>
      </section>
    </main>
<?php render_footer();
