# Trume Admin deployment

The admin is a PHP + SQLite application. SQLite stores the website data in one protected file and is ideal for local development and a small internal team.

## 1. Enable SQLite

For local Debian development, install PHP's SQLite extension:

```bash
sudo apt-get install php8.4-sqlite3
```

Restart the PHP development server after installation. On shared hosting, confirm that `pdo_sqlite` is enabled before deployment. If it is unavailable, this project can be migrated back to MySQL before publishing.

## 2. Configure the application

Copy `admin/config.example.php` to `admin/config.php`. Replace `setup_key` with a long, unique random value. The database is stored at `admin/data/trume.sqlite`; `config.php` and the database file are deliberately ignored by Git and must never be committed.

## 3. Upload and install

Upload this project to `public_html` using cPanel File Manager or FTP. Visit:

```text
https://yourdomain.com/admin/install.php
```

Enter the setup key, administrator name, email address, and a password of at least 12 characters. The installer creates the SQLite database tables and first administrator.

After installation, remove or rename `admin/install.php` through File Manager.

## 4. Create the admin subdomain

In cPanel, create `admin.yourdomain.com` and point its document root to the project’s `admin` folder. If the domain is configured this way, the dashboard opens at:

```text
https://admin.yourdomain.com/login.php
```

Until then, it is also available at `https://yourdomain.com/admin/login.php`.

## 5. Enable the contact inbox

The public contact form posts to `api/contact.php` and saves messages in SQLite. Test it after setup. The form needs this PHP application and database; it will not work from a file opened directly on a computer.

## Security checklist

- Enable SSL for the main domain and admin subdomain before using admin accounts.
- Use a unique, long administrator password and setup key.
- Remove `install.php` after creating the first account.
- Back up `admin/data/trume.sqlite` before significant changes.
- Do not store the setup key in Git or public documentation.

## Current capabilities

- Secure administrator sign-in with password hashing, session regeneration, CSRF protection, and protected routes
- Founder profile management, including visibility and display order
- Insight article drafting and publishing
- Editable core copy for the Home, About, Solutions, Truehome, Partners, and Contact pages
- Global business/contact settings
- Secure image library with file type, content, and size validation
- Contact-message inbox and status tracking
- Personal administrator profile and password changes
- Activity tracking for key CMS changes

The public site reads selected page and contact values through `api/content.php`. Founder and insight publishing integrations are the next development phase, so team and article edits appear on their public pages automatically.


ADMIN CREDENTIALS
kainamuradarius
Godsplan@222