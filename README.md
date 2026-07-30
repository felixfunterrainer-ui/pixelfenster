# PixelFenster — Website

Static landing page + PHP contact-form backend, deployed as a Coolify application (Dockerfile build pack) on the same VPS as Ornatis, bound to `pixelfenster.at`.

## Structure

- `index.html` — landing page (self-contained HTML/CSS/JS)
- `impressum-agb.html` — Impressum, AGB, Datenschutzerklärung (generated from the legal draft; see TODOs below)
- `send.php` — receives the contact quiz payload and emails it via Zoho Mail SMTP (PHPMailer)
- `assets/hero-window.jpg` — hero image
- `composer.json` — PHPMailer dependency for `send.php`
- `Dockerfile` — `php:8.3-apache`, installs Composer deps, serves `index.html` by default
- `fonts/` — self-hosted Inter + Plus Jakarta Sans woff2 files (no third-party font CDN requests)
- `robots.txt`, `sitemap.xml` — search engine indexing

## Deploy (Coolify)

1. Coolify → New Resource → Application → Git repository → `https://github.com/felixfunterrainer-ui/pixelfenster`, branch `main`.
2. Build pack: **Dockerfile** (auto-detected).
3. Domain: `pixelfenster.at`. Coolify/Traefik handles routing and TLS, same as Ornatis — no changes needed to Ornatis or the host Apache.
4. Environment variable: `ZOHO_SMTP_PASSWORD` (used by `send.php`, never hardcode it) — add once Zoho SMTP is set up.
5. Deploy. Future updates: `git push` from this folder, then redeploy in Coolify (or enable auto-deploy on push).

## Outstanding before going fully live

- **Zoho Mail SMTP** — `send.php` is pre-configured for `smtp.zoho.eu` / `office@pixelfenster.at`. Set `ZOHO_SMTP_PASSWORD` on the server and test a submission. (Planned as a follow-up.)
- **CORS** — `send.php` currently allows `Access-Control-Allow-Origin: *`. Tighten to the production domain once it's live.
