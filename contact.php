<?php
declare(strict_types=1);
require __DIR__ . '/includes/site.php';
$content = page_content('contact');
render_header([
    'title' => $content['page_title'],
    'description' => $content['meta_description'],
    'path' => 'contact.php',
    'body_class' => 'contact-page',
    'active' => 'contact',
]);
?>
    <main>
<?php render_hero($content); ?>
      <section class="content contact-intro">
        <p class="section-label"><?= e($content['intro_label']) ?></p>
        <h2><?= rich($content['intro_heading']) ?></h2>
        <?= paragraphs($content['intro_body'], '        ') ?>
      </section>
      <section class="content contact-layout">
        <aside class="contact-details">
          <div>
            <p class="contact-detail-label">OFFICE</p>
            <address>
              <?= implode('<br />', array_map('e', address_lines())) ?>

            </address>
            <a
              href="https://www.google.com/maps/search/?api=1&amp;query=<?= e(urlencode(setting('company_address'))) ?>"
              target="_blank"
              rel="noopener noreferrer"
              >Get directions &nearr;</a
            >
          </div>
          <div>
            <p class="contact-detail-label">OFFICIAL EMAIL</p>
            <a href="mailto:<?= e(setting('company_email')) ?>"><?= e(setting('company_email')) ?></a>
          </div>
          <div>
            <p class="contact-detail-label">PHONE</p>
            <p>
<?php if (setting('contact_person') !== ''): ?>
              <strong><?= e(setting('contact_person')) ?></strong>
<?php endif; ?>
<?php foreach (split_list(setting('contact_phones')) as $phone): ?>
              <br /><a href="<?= e(tel_href($phone)) ?>"><?= e($phone) ?></a>
<?php endforeach; ?>
            </p>
          </div>
          <div>
            <p class="contact-detail-label">ONLINE</p>
            <a
              href="<?= e(setting('truehome_url')) ?>"
              target="_blank"
              rel="noopener noreferrer"
              ><?= e(preg_replace('#^https?://(www\.)?#', '', rtrim(setting('truehome_url'), '/'))) ?> &nearr;</a
            >
            <div class="social-links">
              <a
                href="<?= e(setting('linkedin_url')) ?>"
                target="_blank"
                rel="noopener noreferrer"
                >LinkedIn &nearr;</a
              >
              <a
                href="<?= e(setting('whatsapp_url')) ?>"
                target="_blank"
                rel="noopener noreferrer"
                >WhatsApp &nearr;</a
              >
            </div>
          </div>
        </aside>
        <form
          id="contact-form"
          class="contact-form"
          action="send-message.php"
          method="post"
        >
          <p class="section-label">SEND A MESSAGE</p>
          <p id="form-status" class="form-status" aria-live="polite"></p>
          <input
            class="form-honeypot"
            type="text"
            name="website"
            tabindex="-1"
            autocomplete="off"
            aria-hidden="true"
          />
          <label
            >Full name<input type="text" name="full_name" autocomplete="name" required
          /></label>
          <label
            >Email address<input type="email" name="email" autocomplete="email" required
          /></label>
          <label>Phone number (optional)<input type="tel" name="phone" autocomplete="tel" /></label>
          <label>Subject<input type="text" name="subject" required /></label>
          <label
            >Message<textarea name="message" rows="6" required></textarea>
          </label>
          <button class="button button-dark" type="submit">
            Send message <span>&rarr;</span>
          </button>
          <small>We will get back to you by email.</small>
        </form>
      </section>
      <section class="content map-section">
        <p class="section-label">OUR LOCATION</p>
        <div class="map-frame">
          <iframe
            title="Map to <?= e(short_address()) ?>"
            src="https://www.google.com/maps?q=<?= e(rawurlencode(setting('company_address'))) ?>&amp;output=embed"
            loading="lazy"
          ></iframe>
        </div>
      </section>
      <section class="content">
        <div class="contact-panel">
          <div>
            <p class="section-label">TRUME COMPANY LIMITED</p>
            <h2>Technology, real estate and digital innovation.</h2>
          </div>
          <div>
            <p>
              Visit the Truehome marketplace to begin your property journey or
              contact us to explore a partnership.
            </p>
            <a
              href="<?= e(setting('truehome_url')) ?>"
              target="_blank"
              rel="noopener noreferrer"
              >Explore Truehome &rarr;</a
            >
          </div>
        </div>
      </section>
    </main>
<?php render_footer(true);
