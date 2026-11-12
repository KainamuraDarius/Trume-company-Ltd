# Trume Website Handbook

This handbook explains how the Trume Company Limited website works and how to look after it.
Read the part that matches your job:

| You are… | Read |
|---|---|
| Updating text, founders, articles, photos or contact details | [Content guide](content-guide.md) |
| Looking after the hosting, uploading updates, backups or fixing problems | [Technical guide](technical-guide.md) |
| Something is broken and you need a quick answer | [Troubleshooting](technical-guide.md#troubleshooting) |

## The essentials

| What | Where |
|---|---|
| Public website | https://trumecompanyltd.truehome.com.ug |
| Admin (where content is edited) | https://trume-admin.truehome.com.ug |
| Hosting control panel | cPanel at Afriregister (account `rmsugnxf`) |
| Website code | GitHub: `KainamuraDarius/Trume-company-Ltd` |
| Admin code | GitHub: `KainamuraDarius/Trume-Admin` |
| All content and messages | One database file on the server: `/home4/rmsugnxf/trume-data/trume.sqlite` |

## How it fits together, in one picture

```
   Visitors                                  Trume team
      │                                          │
      ▼                                          ▼
 trumecompanyltd.truehome.com.ug         trume-admin.truehome.com.ug
 (public website, reads content)          (admin, edits content)
      │                                          │
      └──────────────►  trume.sqlite  ◄──────────┘
                    (one shared database,
                     outside public_html)
```

- Everything you change in the admin appears on the website **immediately**. There is no separate
  "publish" step for the whole site.
- Messages sent through the website's Contact and Partners forms are saved in the admin under
  **Messages** and are also emailed to the company email address.
- If the database is ever missing or damaged, the website still shows its original built-in text,
  so visitors never see a broken page. (Admin changes would not show until the database is restored.)

## Who to contact

- **Hosting, domains, email, SSL certificates:** Afriregister support (they run the server).
- **Admin account problems:** the admin *owner* account can create and disable other accounts.
  If the owner is locked out, see [Forgotten owner password](technical-guide.md#forgotten-owner-password).
