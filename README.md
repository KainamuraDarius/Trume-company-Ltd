# Trume Company Limited Website

The official marketing website for **Trume Company Limited**, a Ugandan property-technology company building clearer, more trusted digital property experiences. The site presents Trume's mission, solutions, partnerships, insights, and **Truehome**—its flagship property marketplace.

> **Looking after this website?** Start with the [handbook](docs/README.md): a [content guide](docs/content-guide.md) for editing the site and a [technical guide](docs/technical-guide.md) for hosting, backups and troubleshooting.

## Overview

Trume is focused on making it simpler to find, list, rent, buy, and manage property in Uganda. This repository contains the public website:

- Company, solutions, partner, contact, and insights pages
- A dedicated Truehome product page
- Insight articles, founder profiles and page text managed from **Trume Admin** (separate repository)
- Contact and partnership forms that email the team and save to the admin inbox
- Responsive navigation and mobile styling, and a reduced-motion-aware homepage typewriter effect

## Built with

- PHP 8 (no framework) reading a shared SQLite database
- CSS3 and vanilla JavaScript
- Google Fonts: Manrope, DM Mono, and Playfair Display

If the database is unavailable, every page falls back to the default content in `includes/defaults.php`, so the site always renders.

## Run locally

```bash
cd "Trume company Ltd"
cp config.example.php config.php   # then set database_path to your local trume-data folder
php -S localhost:8000
```

Open [http://localhost:8000](http://localhost:8000). To edit content, run Trume Admin alongside it (see its README).

## Project structure

```text
.
├── index.php, about.php, solutions.php,       # Pages (layout here, editable text from the database)
│   truehome.php, partners.php, insights.php,
│   contact.php
├── article.php                                 # Single Insights article (?slug=...)
├── send-message.php                            # Handles the contact and partnership forms
├── sitemap.php                                 # XML sitemap including published articles
├── includes/
│   ├── site.php                                # Database access, formatting helpers, header/footer
│   └── defaults.php                            # Default content (fallback and first-install seed)
├── *.html                                      # Old addresses that forward to the new .php pages
├── 404.html                                    # Not-found page
├── config.example.php                          # Copy to config.php (not committed)
├── uploads/                                    # Images uploaded from the admin (not committed)
├── styles.css, script.js
└── assets/
```

## Content maintenance

- **Text, founders, articles, contact details:** edit in Trume Admin. Changes are live immediately.
- **Layout and sections not listed in the admin:** edit the `.php` page templates.
- **Navigation, head tags and footer:** shared in `includes/site.php`.
- **Site-wide design:** `styles.css`. **Interactions:** `script.js`.
- **Truehome links:** the product page points to the App Store and Google Play; the Truehome URL is a site setting.

## Accessibility notes

The site uses semantic sections, meaningful image alternative text, labelled menu controls, and supports the user’s `prefers-reduced-motion` setting for the animated homepage heading. When adding content, preserve descriptive `alt` text and visible keyboard focus styles.

## Deployment

Hosted on cPanel at `public_html/trumecompanyltd.truehome.com.ug` with the shared database in
`/home4/rmsugnxf/trume-data/` (outside `public_html`). Upload the files, create `config.php` from
`config.example.php`, and see the Trume Admin README for the admin setup.

Before publishing, check that internal and app links work, the site looks right on mobile and desktop,
and image licences and attribution records are retained.

## Image credits

Property photography sources and licence notes are recorded in [assets/buildings/SOURCES.md](assets/buildings/SOURCES.md). Do not replace or redistribute third-party images without confirming the applicable licence.

## Contact

Trume Company Limited  
National ICT Innovation Hub, Nakawa, Kampala, Uganda  
Email: [ramzyhadah@gmail.com](mailto:ramzyhadah@gmail.com)  
Phone: [+256 778 332 065](tel:+256778332065)

## Licence

No licence has been declared for this repository. Unless and until a licence is added, all rights are reserved by Trume Company Limited. Third-party assets remain subject to their respective licences.
