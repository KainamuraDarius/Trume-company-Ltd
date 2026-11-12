# Technical guide

For the person who looks after hosting, uploads updates, takes backups and fixes problems.
Assumes you can use cPanel File Manager and basic Git. No framework or build step is involved.

---

## 1. Architecture

Two small PHP 8 applications on the same cPanel account share one SQLite database file.

| Part | Address | Server folder | Code |
|---|---|---|---|
| Public website | https://trumecompanyltd.truehome.com.ug | `/home4/rmsugnxf/public_html/trumecompanyltd.truehome.com.ug` | `Trume-company-Ltd` repo |
| Admin | https://trume-admin.truehome.com.ug | `/home4/rmsugnxf/public_html/trume-admin.truehome.com.ug` | `Trume-Admin` repo |
| Database | — | `/home4/rmsugnxf/trume-data/trume.sqlite` | created by the admin |

```
/home4/rmsugnxf/
├── trume-data/                         shared data (NOT inside public_html, cannot be downloaded)
│   ├── trume.sqlite                    all content, messages, accounts
│   ├── trume.sqlite-wal / -shm         SQLite working files (may exist; never delete while sites run)
└── public_html/
    ├── trumecompanyltd.truehome.com.ug/   public website
    │   ├── config.php                  server-only settings (not in Git)
    │   ├── includes/site.php           helpers: database reading, formatting, header/footer
    │   ├── includes/defaults.php       built-in fallback content
    │   ├── index.php, about.php …      page templates
    │   ├── article.php?slug=…          single article page
    │   ├── send-message.php            handles Contact and Partners forms
    │   ├── sitemap.php                 sitemap for Google (robots.txt points to it)
    │   ├── *.html                      old addresses; they just redirect to the .php pages
    │   └── uploads/                    images uploaded from the admin
    └── trume-admin.truehome.com.ug/    admin
        ├── config.php                  server-only settings (not in Git)
        ├── bootstrap.php               database, security, uploads, helpers
        ├── schema.sql                  database tables (applied automatically)
        └── pages.php, founders.php …   one file per admin screen
```

### Who does what
- **Admin** owns the database: it creates it, creates/updates tables (`schema.sql`, safe to re-run)
  and fills it with the website's default content on first run (from the website's `includes/defaults.php`).
  It saves uploaded images into the **website's** `uploads/` folder.
- **Website** only reads the database (plus inserts form messages). It never creates the database.
  If the database is missing or unreadable it silently uses `includes/defaults.php`.

### What is editable vs. in code
| Editable in admin (database) | Only in code |
|---|---|
| Each page's hero eyebrow, heading, hero text, intro label/heading/text, tab title, meta description | Page layout and all other sections (service lists, stats, cards, etc.) |
| Founders, articles, images | Navigation, design (`styles.css`), behaviour (`script.js`) |
| Company email, phones, address, contact person, Truehome/LinkedIn/WhatsApp links | Footer layout, form fields |

To make a currently fixed piece of text editable, add a column/setting in the admin, a default in
`includes/defaults.php`, and use it in the page template.

### Database tables (summary)
`pages`, `founders`, `articles`, `site_settings`, `media`, `messages`, `admins`,
`activity_log`, `login_attempts`, `app_meta`. Full definitions: `schema.sql` in the admin repo.

### Text format stored in the database
`*word*` → highlighted `<em>`, blank line → new paragraph, line starting `## ` → `<h2>`,
comma-separated lists for phone numbers and address. All output is HTML-escaped.

---

## 2. Configuration files

Neither `config.php` is in Git. They exist only on the server (and on each developer's computer
with local paths). **Never upload a local `config.php` to the server** — this broke the admin once.

**Website** `config.php`:
```php
<?php
return [
    'database_path' => '/home4/rmsugnxf/trume-data/trume.sqlite',
    'site_url' => 'https://trumecompanyltd.truehome.com.ug',
];
```

**Admin** `config.php`:
```php
<?php
return [
    'database_path' => '/home4/rmsugnxf/trume-data/trume.sqlite',
    'public_site_path' => '/home4/rmsugnxf/public_html/trumecompanyltd.truehome.com.ug',
    'public_site_url' => 'https://trumecompanyltd.truehome.com.ug',
    'setup_key' => 'a-long-random-value',   // only used by install.php
    'site_name' => 'Trume Company Limited',
];
```
Generate a setup key with `openssl rand -hex 24` or any long random string.

---

## 3. Hosting facts and quirks (Afriregister cPanel)

- **PHP 8.1 or newer is required** (server runs 8.2). Set per domain in cPanel → *MultiPHP Manager*.
  Extension `pdo_sqlite` must be enabled (it is).
- **`.htaccess` rules are not applied on this host.** Consequences:
  - Any file inside a website folder can be downloaded — that is why the database lives in
    `trume-data/` outside `public_html`. Never put the database, backups or notes inside `public_html`.
  - HTTPS is enforced with cPanel → *Domains* → **Force HTTPS Redirect** (on for both subdomains),
    not by `.htaccess`.
  - The custom 404 page and caching rules in `.htaccess` do nothing here. Harmless to keep.
  - If Afriregister ever enables `AllowOverride`, the existing `.htaccess` files start working
    automatically.
- **SSL certificates** come from cPanel AutoSSL (*SSL/TLS Status → Run AutoSSL*), renewed automatically.
- **Email:** form messages are sent with PHP `mail()` from `no-reply@truehome.com.ug`, with the
  visitor's address as Reply-To. If emails land in spam or don't arrive, create that mailbox in
  cPanel → *Email Accounts* and check cPanel → *Email Deliverability* (SPF/DKIM) for `truehome.com.ug`.

---

## 4. Making and deploying changes

### Local setup (once)
Requires PHP 8.1+ with `pdo_sqlite`.
```bash
git clone https://github.com/KainamuraDarius/Trume-company-Ltd.git "Trume company Ltd"
git clone https://github.com/KainamuraDarius/Trume-Admin.git Trume-Admin
mkdir trume-data   # local database folder, next to the two projects
```
Create each `config.php` from its `config.example.php`, using **local** absolute paths
(e.g. `/home/you/StudioProjects/trume-data/trume.sqlite`, `public_site_url` `http://localhost:8000`).

```bash
cd "Trume company Ltd" && php -S localhost:8000     # terminal 1: website
cd Trume-Admin && php -S localhost:8001             # terminal 2: admin
```
Open http://localhost:8001/install.php once to create a local owner account.
Local email sending fails — that is expected; messages still appear in the admin.

### Workflow for every change
1. Change and **test locally** (website and admin).
2. Commit and push to GitHub.
3. Upload **only the changed files** to the matching server folder in cPanel File Manager
   (*Upload*, overwrite). For many files: zip them, upload, *Extract*, delete the zip.
4. Check the live site.

### Never upload to the server
- `config.php` from your computer (server has its own)
- `README.md`, `docs/`, `.git/`, `.gitignore` (on this host they would be downloadable)
- `install.php` after the first setup
- Anything into `uploads/` except via the admin

### Changing the database structure
Add the column/table to the admin's `schema.sql` (use `CREATE TABLE IF NOT EXISTS`), and for new
columns on existing tables add an `ALTER TABLE` check in `ensure_content_columns()` in
`bootstrap.php`. It runs automatically the next time anyone opens the admin. Take a backup first.

---

## 5. Backups and restore

Everything that matters is in **two places**:
1. `/home4/rmsugnxf/trume-data/` — content, messages, accounts
2. `public_html/trumecompanyltd.truehome.com.ug/uploads/` — uploaded images

The code itself is safe on GitHub.

### Take a backup (monthly, and before big changes)
1. cPanel → File Manager → `/home4/rmsugnxf/` → right-click `trume-data` → **Compress** → Zip.
   This captures `trume.sqlite` plus any `-wal`/`-shm` files together, which is required for a
   complete copy. Do this when nobody is editing in the admin.
2. Do the same for the website's `uploads` folder.
3. **Download** both zips and store them somewhere safe (not on the server only). Then delete the
   zips from the server.

cPanel → *Backup* (full account backup) also works if available.

### Restore
1. Upload the backup zip into `/home4/rmsugnxf/` and **Extract**, overwriting `trume-data/`
   (delete any existing `trume.sqlite-wal` / `-shm` that were not part of the backup first).
2. Restore `uploads/` the same way into the website folder.
3. Open the website and admin to check.

---

## 6. Accounts and security

- First account = **owner** (created by `install.php`). Owner can add/disable other accounts in
  *Administrators*. Other accounts edit content only.
- Passwords are hashed (`password_hash`), sessions are regenerated at sign-in, every form has a
  CSRF token, 5 failed sign-ins per IP → 15-minute lockout.
- Uploads are verified as real images (JPG/PNG/WebP/GIF, ≤ 8 MB) and renamed randomly.
- `install.php` only works while there are **no** accounts and requires the setup key. It must be
  deleted from the server after use anyway.
- Optional extra layer: cPanel → *Directory Privacy* on the admin folder (a second browser password).
- When someone leaves: disable their account, and if they knew the cPanel or owner password, change it.

### Forgotten owner password
There is no reset email. Using cPanel File Manager:
1. In the admin folder, create a file with a random name, e.g. `reset-7f3k9q.php`, containing:
   ```php
   <?php
   require __DIR__ . '/bootstrap.php';
   $email = 'owner@example.com';          // the owner's sign-in email
   $newPassword = 'Choose-A-New-Long-Password';
   $update = db()->prepare('UPDATE admins SET password_hash = ?, is_active = 1 WHERE email = ?');
   $update->execute([password_hash($newPassword, PASSWORD_DEFAULT), $email]);
   echo $update->rowCount() ? 'Password changed. DELETE THIS FILE NOW.' : 'No account with that email.';
   ```
2. Open `https://trume-admin.truehome.com.ug/reset-7f3k9q.php` once.
3. **Delete the file immediately**, then sign in and change the password in *My account*.

---

## 7. Troubleshooting

| Symptom | Likely cause and fix |
|---|---|
| "This site can't be reached" | Wrong address (it's `.com.ug`, not `.com`), or DNS/hosting issue → Afriregister |
| Browser says "Not secure" | Force HTTPS Redirect off, or certificate missing → cPanel *Domains* / *SSL/TLS Status → Run AutoSSL* |
| Home page shows an old design | An old `index.html` is in the website folder → delete it so `index.php` is used |
| Admin edits don't appear on the website | Website `config.php` has the wrong `database_path` → must be `/home4/rmsugnxf/trume-data/trume.sqlite` |
| Admin: "Could not open the database" | Admin `config.php` has a local computer path, or `trume-data` isn't writable. Paths must start with `/home4/rmsugnxf/` |
| install.php: "The setup key is incorrect" | Browser autofilled a saved password into the key box; paste the exact value from the admin `config.php` |
| install.php: "already installed" | An account exists. Sign in, or see *Forgotten owner password* |
| Can't sign in, no error change | Locked out after 5 attempts → wait 15 minutes |
| Image upload fails | Not a real JPG/PNG/WebP/GIF, over 8 MB, or website `uploads/` not writable (permissions 755) |
| Images missing on the website | `uploads/` folder deleted/overwritten during an upload → restore from backup |
| Contact form says sent but no email | Message is in admin *Messages*. Check spam, the official email in *Site settings*, and the email notes in section 3 |
| Contact form says "could not send" | Both database save and email failed → check database path and `error_log` files in the website folder |
| White page or "500 error" | PHP error. Check the `error_log` file in that site's folder (File Manager) and cPanel *Errors*. Confirm PHP version ≥ 8.1 |
| Text shows a stray `*` | Unpaired asterisk in the admin text — fix the text |

### Diagnosing the database connection
Create a temporary `check.php` in the admin folder:
```php
<?php
header('Content-Type: text/plain');
$c = require __DIR__ . '/config.php'; $db = $c['database_path'];
echo 'PHP ', PHP_VERSION, "\npdo_sqlite: ", extension_loaded('pdo_sqlite') ? 'yes' : 'NO', "\n";
echo "Folder: ", __DIR__, "\nDatabase: $db\nWritable: ", is_writable(dirname($db)) ? 'yes' : 'NO', "\n";
try { new PDO('sqlite:' . $db); echo "Open: OK\n"; } catch (Throwable $e) { echo 'Open: FAILED ', $e->getMessage(), "\n"; }
```
Open it in the browser, read the result, then **delete it**.
