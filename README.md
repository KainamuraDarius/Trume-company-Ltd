# Trume Company Limited Website

The official marketing website for **Trume Company Limited**, a Ugandan property-technology company building clearer, more trusted digital property experiences. The site presents Trume's mission, solutions, partnerships, insights, and **Truehome**—its flagship property marketplace.

## Overview

Trume is focused on making it simpler to find, list, rent, buy, and manage property in Uganda. This repository contains the public-facing static website, including:

- Company, solutions, partner, contact, and insights pages
- A dedicated Truehome product page
- Four long-form insight articles
- Responsive navigation and mobile styling
- A reduced-motion-aware homepage typewriter effect

## Built with

- Semantic HTML5
- CSS3 (responsive layouts and custom properties)
- Vanilla JavaScript
- Google Fonts: Manrope, DM Mono, and Playfair Display

No build step, framework, package manager, or server-side runtime is required.

## Run locally

Clone the repository and serve the project directory with any static-file server. For example, with Python 3:

```bash
git clone <your-repository-url>
cd "Trume company Ltd"
python3 -m http.server 8000
```

Then open [http://localhost:8000](http://localhost:8000) in your browser.

You can also open `index.html` directly in a browser, though a local server more closely reflects production behaviour.

## Project structure

```text
.
├── index.html                                  # Homepage
├── about.html                                  # Company story, values, and founders
├── solutions.html                              # Property-technology solutions
├── truehome.html                               # Flagship Truehome platform
├── partners.html                               # Partnership opportunities
├── insights.html                               # Insights landing page
├── article-*.html                              # Individual insight articles
├── contact.html                                # Contact details and enquiry form
├── styles.css                                  # Shared site styles and responsive rules
├── script.js                                   # Navigation, footer year, and interactions
└── assets/
    ├── buildings/                              # Property photography and source record
    ├── Trume Company Ltd — Logo@2x.png         # Trume brand logo
    ├── truehome-app-icon.jpg                   # Truehome app icon
    └── *.jpeg                                  # Team and Kampala imagery
```

## Content maintenance

- **Navigation and footer:** duplicated across page templates. Keep the links and company details consistent when making edits.
- **Site-wide design:** update `styles.css` for shared typography, colours, layout, and responsive behaviour.
- **Site interactions:** update `script.js`. It handles the mobile menu, dynamic copyright year, reduced-motion-aware typewriter headline, and founder-card expansion.
- **Insights:** add a new article page, then add its card and link to `insights.html`.
- **Truehome links:** the product page points to [truehome.com.ug](https://truehome.com.ug), the App Store, and Google Play. Confirm these destinations when updating release information.
- **Contact form:** `contact.html` currently uses a `mailto:` action; submissions depend on the visitor having a configured email client. Replace this with a form service or backend endpoint if server-side form handling is needed.

## Accessibility notes

The site uses semantic sections, meaningful image alternative text, labelled menu controls, and supports the user’s `prefers-reduced-motion` setting for the animated homepage heading. When adding content, preserve descriptive `alt` text and visible keyboard focus styles.

## Deployment

Because this is a static site, it can be deployed to GitHub Pages, Netlify, Vercel, Cloudflare Pages, or any standard web host. Publish the repository root as the site’s public directory; no build command is necessary.

Before publishing, check that:

1. All internal links and external Truehome/app links work.
2. The site is reviewed on mobile and desktop screen sizes.
3. Contact details and social links are current.
4. Image licences and attribution records are retained.

## Image credits

Property photography sources and licence notes are recorded in [assets/buildings/SOURCES.md](assets/buildings/SOURCES.md). Do not replace or redistribute third-party images without confirming the applicable licence.

## Contact

Trume Company Limited  
National ICT Innovation Hub, Nakawa, Kampala, Uganda  
Email: [ramzyhadah@gmail.com](mailto:ramzyhadah@gmail.com)  
Phone: [+256 778 332 065](tel:+256778332065)

## Licence

No licence has been declared for this repository. Unless and until a licence is added, all rights are reserved by Trume Company Limited. Third-party assets remain subject to their respective licences.
