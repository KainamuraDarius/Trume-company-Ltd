<?php
declare(strict_types=1);
require __DIR__ . '/includes/site.php';
$content = page_content('partners');
render_header([
    'title' => $content['page_title'],
    'description' => $content['meta_description'],
    'path' => 'partners.php',
    'body_class' => 'partners-page',
    'active' => 'partners',
]);
?>
    <main>
<?php render_hero($content, <<<'HTML'
          <a
            class="button button-dark partner-hero-button"
            href="#partner-enquiry"
            >Become a Partner <span>&rarr;</span></a
          >
HTML); ?>
      <section class="content two-column">
        <div>
          <p class="section-label"><?= e($content['intro_label']) ?></p>
          <h2><?= rich($content['intro_heading']) ?></h2>
        </div>
        <div>
          <?= paragraphs($content['intro_body'], '          ') ?>
        </div>
      </section>
      <section class="partner-areas">
        <div class="content">
          <p class="section-label">STRATEGIC PARTNERSHIPS</p>
          <h2>Reach people at the moment property decisions are made.</h2>
          <p class="partner-intro">
            Through Truehome, we see opportunities to create relevant, useful
            connections between property seekers, providers and the businesses
            that support their journey.
          </p>
          <div class="partner-grid">
            <article>
              <b>01</b>
              <h3>Financial institutions &amp; banks</h3>
              <p>
                Explore relevant connections around property finance and
                customer needs.
              </p>
            </article>
            <article>
              <b>02</b>
              <h3>Insurance companies</h3>
              <p>
                Support people looking for confidence and protection around
                their property decisions.
              </p>
            </article>
            <article>
              <b>03</b>
              <h3>Construction companies</h3>
              <p>
                Connect construction expertise and services with an engaged
                property audience.
              </p>
            </article>
            <article>
              <b>04</b>
              <h3>Furniture &amp; home services</h3>
              <p>Reach people as they prepare, move into and improve a home.</p>
            </article>
            <article>
              <b>05</b>
              <h3>Property developers</h3>
              <p>
                Present developments and opportunities to potential buyers and
                partners.
              </p>
            </article>
            <article>
              <b>06</b>
              <h3>Relocation services</h3>
              <p>
                Help people make a smoother transition as they find and settle
                into a new place.
              </p>
            </article>
          </div>
        </div>
      </section>
      <section class="content two-column partnership-benefits">
        <div>
          <p class="section-label">PARTNERSHIP OPPORTUNITIES</p>
          <h2>Build practical value together.</h2>
        </div>
        <div>
          <p>
            Potential collaborations can help partners connect with an engaged
            audience while making the property journey more useful for
            customers.
          </p>
          <ul class="partner-benefit-list">
            <li>
              Reach people searching, listing or planning property decisions.
            </li>
            <li>
              Develop relevant customer experiences around the Truehome
              marketplace.
            </li>
            <li>
              Explore value-added services and partnerships as the platform
              grows.
            </li>
          </ul>
        </div>
      </section>
      <section class="content">
        <div class="contact-panel">
          <div>
            <p class="section-label">START A CONVERSATION</p>
            <h2>Build with us.</h2>
          </div>
          <div>
            <p>
              Tell us about the value you want to create for property seekers
              and providers in Uganda.
            </p>
            <a href="#partner-enquiry">Become a Partner &rarr;</a>
          </div>
        </div>
      </section>
      <section class="enquiry-section" id="partner-enquiry">
        <div class="content two-column">
          <div>
            <p class="section-label">PARTNERSHIP ENQUIRY</p>
            <h2>Let&rsquo;s explore what we can build together.</h2>
          </div>
          <form class="partner-form" action="send-message.php" method="post">
            <p id="form-status" class="form-status form-full" aria-live="polite"></p>
            <input type="hidden" name="form_type" value="partner" />
            <input
              class="form-honeypot"
              type="text"
              name="website"
              tabindex="-1"
              autocomplete="off"
              aria-hidden="true"
            />
            <label
              >Organisation name<input type="text" name="organisation" required
            /></label>
            <label>Your name<input type="text" name="name" required /></label>
            <label
              >Email address<input type="email" name="email" required
            /></label>
            <label
              >Partnership area<select name="area" required>
                <option value="">Select an area</option>
                <option>Finance or banking</option>
                <option>Insurance</option>
                <option>Construction</option>
                <option>Furniture or home services</option>
                <option>Property development</option>
                <option>Relocation services</option>
                <option>Other strategic opportunity</option>
              </select></label
            >
            <label class="form-full"
              >How would you like to work with Trume?<textarea
                name="message"
                rows="5"
                required
              ></textarea>
            </label>
            <button class="button button-dark form-full" type="submit">
              Send partnership enquiry <span>&rarr;</span>
            </button>
          </form>
        </div>
      </section>
    </main>
<?php render_footer();
