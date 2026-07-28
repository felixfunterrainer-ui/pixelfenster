# PixelFenster — Website

Static landing page + PHP contact-form backend. Deploy by pulling this repo onto the VPS document root; `index.html` is the entry point.

## Structure

- `index.html` — landing page (self-contained HTML/CSS/JS)
- `impressum-agb.html` — Impressum, AGB, Datenschutzerklärung (generated from the legal draft; see TODOs below)
- `send.php` — receives the contact quiz payload and emails it via Zoho Mail SMTP (PHPMailer)
- `assets/hero-window.jpg` — hero image
- `composer.json` — PHPMailer dependency for `send.php`

## Deploy

1. Pull/clone this repo into the web root on the VPS.
2. On the server: `composer install` (creates `vendor/`, gitignored).
3. Set the Zoho mailbox password as an environment variable on the server: `ZOHO_SMTP_PASSWORD` (used by `send.php`, never hardcode it).
4. Point the domain/webserver at this directory; no build step needed.

## Outstanding before going fully live

- **Zoho Mail SMTP** — `send.php` is pre-configured for `smtp.zoho.eu` / `office@pixelfenster.at`. Set `ZOHO_SMTP_PASSWORD` on the server and test a submission. (Planned as a follow-up.)
- **Legal placeholders** — `impressum-agb.html` has several bracketed placeholders (highlighted in orange on the page) — company legal form, address, Firmenbuchnummer, UID, notice periods, etc. Have these reviewed by a Steuerberater/Rechtsanwalt before publishing, per the draft's own disclaimer.
- **Self-hosted fonts** — both HTML files reference `fonts/*.woff2`, which aren't in this repo yet. Until added, browsers fall back to system fonts automatically — nothing breaks, it's a visual nice-to-have.
- **CORS** — `send.php` currently allows `Access-Control-Allow-Origin: *`. Tighten to the production domain once it's live.
