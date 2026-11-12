<?php
declare(strict_types=1);
require __DIR__ . '/includes/site.php';
$content = page_content('truehome');
render_header([
    'title' => $content['page_title'],
    'description' => $content['meta_description'],
    'path' => 'truehome.php',
    'body_class' => 'truehome-page',
    'active' => 'truehome',
]);
?>
    <main>
      <section class="solution-band">
        <div class="truehome-lockup"><b>TRUE</b>HOME</div>
        <h1><?= rich($content['hero_heading']) ?></h1>
        <?= paragraphs($content['hero_body'], '        ') ?>
        <a
          class="button button-lime"
          href="<?= e(setting('truehome_url')) ?>"
          target="_blank"
          rel="noopener noreferrer"
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
      <section class="how-section">
        <div class="content">
          <p class="section-label">HOW TRUEHOME WORKS</p>
          <h2>From search to connection, in a few clear steps.</h2>
          <div class="process-grid">
            <article>
              <b>01</b>
              <h3>Search</h3>
              <p>
                Explore available property listings in one convenient place.
              </p>
            </article>
            <article>
              <b>02</b>
              <h3>Compare</h3>
              <p>
                Review clear details to identify the properties that match your
                needs.
              </p>
            </article>
            <article>
              <b>03</b>
              <h3>Connect</h3>
              <p>
                Reach the relevant landlord, owner, agent or developer directly.
              </p>
            </article>
          </div>
        </div>
      </section>
      <section class="content">
        <p class="section-label">BENEFITS OF USING TRUEHOME</p>
        <h2>More confidence at every step.</h2>
        <div class="values-grid benefits-grid">
          <article>
            <b>01</b>
            <h3>Convenience</h3>
            <p>Discover property anytime from a phone or computer.</p>
          </article>
          <article>
            <b>02</b>
            <h3>Time savings</h3>
            <p>Reduce wasted viewings with better-matched enquiries.</p>
          </article>
          <article>
            <b>03</b>
            <h3>Transparency</h3>
            <p>Access clear, structured information on each listing.</p>
          </article>
          <article>
            <b>04</b>
            <h3>Reach</h3>
            <p>Connect providers with a larger pool of genuine customers.</p>
          </article>
        </div>
      </section>
      <section class="content">
        <p class="section-label">WHO TRUEHOME SERVES</p>
        <h2>Every side of the property journey.</h2>
        <div class="serve-grid">
          <article>
            <b>01</b>
            <h3>Property seekers</h3>
            <p>
              Search, compare and shortlist homes, rentals and land from one
              place.
            </p>
          </article>
          <article>
            <b>02</b>
            <h3>Owners &amp; landlords</h3>
            <p>
              Present property to a wider, more relevant audience of serious
              customers.
            </p>
          </article>
          <article>
            <b>03</b>
            <h3>Agents &amp; brokers</h3>
            <p>
              Build credibility with professional listings and a digital
              presence.
            </p>
          </article>
          <article>
            <b>04</b>
            <h3>Developers &amp; investors</h3>
            <p>
              Share developments and opportunities with an engaged audience.
            </p>
          </article>
        </div>
      </section>
      <section class="start-section">
        <div class="content two-column">
          <div>
            <p class="section-label">HOW TO GET STARTED</p>
            <h2>Your next property move starts here.</h2>
          </div>
          <div>
            <ol class="start-list">
              <li>Visit the Truehome marketplace or download the app.</li>
              <li>
                Search available listings or create your property listing.
              </li>
              <li>
                Connect with the right people and take the next step with
                confidence.
              </li>
            </ol>
            <a
              class="button button-dark"
              href="<?= e(setting('truehome_url')) ?>"
              target="_blank"
              rel="noopener noreferrer"
              >Explore Truehome <span>&rarr;</span></a
            >
          </div>
        </div>
      </section>
      <section class="truehome-download">
        <img loading="lazy" decoding="async"
          class="download-icon"
          src="assets/truehome-app-icon.jpg"
          alt="Truehome app icon"
        />
        <div class="download-copy">
          <p class="section-label">GET TRUEHOME</p>
          <h2>Property discovery, <em>wherever you are.</em></h2>
          <p>Access Truehome on the web or download the app for your device.</p>
          <div class="download-links">
            <a
              href="https://apps.apple.com/ug/app/truehome/id6759299731"
              target="_blank"
              rel="noopener noreferrer"
              ><svg class="store-icon apple-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.08-.48-3.23 0-1.44.62-2.2.44-3.06-.35C2.79 15.23 3.51 7.54 9 7.26c1.34.07 2.28.73 3.07.79 1.18-.24 2.31-.93 3.56-.84 1.5.12 2.63.71 3.38 1.77-3.1 1.86-2.36 5.94.48 7.08-.57 1.5-1.31 2.99-2.45 4.23ZM12 7.19C11.85 4.96 13.66 3.12 15.74 2.94c.29 2.58-2.34 4.5-3.74 4.25Z" />
              </svg>Download on the App Store <span>&rarr;</span></a
            >
            <a
              href="https://play.google.com/store/apps/details?id=com.truehome.app&amp;pcampaignid=web_share"
              target="_blank"
              rel="noopener noreferrer"
              ><svg class="store-icon play-icon" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#00a0ff" d="m3 2.7 10.42 9.3L3 21.3a2 2 0 0 1-.5-1.32V4.02A2 2 0 0 1 3 2.7Z" />
                <path fill="#ff3a44" d="m13.42 12 2.57 2.3-9.63 5.48L3 21.3 13.42 12Z" />
                <path fill="#ffe000" d="M20.2 10.1 15.99 7.7 13.42 12l2.57 2.3 4.22-2.4a1.04 1.04 0 0 0 0-1.8Z" />
                <path fill="#00d475" d="M3 2.7 6.36 4.6l9.63 5.5L13.42 12 3 2.7Z" />
              </svg>Get it on Google Play <span>&rarr;</span></a
            >
            <a
              href="<?= e(setting('truehome_url')) ?>"
              target="_blank"
              rel="noopener noreferrer"
              >Visit truehome.com.ug <span>&nearr;</span></a
            >
          </div>
        </div>
      </section>
    </main>
<?php render_footer();
