# Rafi & Sons — Development Guide

Living reference for building the eCommerce site on **CodeIgniter 4** with the **Riode Demo22** theme.  
After you share the FRS, we will break work into tasks against this document.

---

## Stack

| Item | Value |
|------|--------|
| Framework | CodeIgniter 4 (`appstarter`) |
| PHP | 8.2+ (local: Laragon 8.3) |
| Local URL | http://rafiandsons.test/ |
| Docroot | `public/` (Laragon vhost) |
| Theme source | `theme/riode/` (Riode Demo22) |
| Theme (web) | `public/theme` → junction to `theme/riode` |
| DB (planned) | MySQL via Laragon (`rafiandsons`) |

---

## Brand

| Key | Value |
|-----|--------|
| Name | **Rafi & Sons** |
| Logo (brand) | `public/assets/images/logo.png` (circular NR mark) |
| Tagline | Dream's of Life |
| Favicon | `public/assets/images/logo.png` |
| Tone | Clean retail / lifestyle eCommerce (Demo22 look) |

---

## Theme parameters (Demo22)

Use these tokens in CI views, custom CSS, and emails so the store stays consistent.

### Colors

| Token | Hex | Usage |
|-------|-----|--------|
| `--rs-primary` | `#05b895` | Links, buttons, accents, active states |
| `--rs-primary-hover` | `#06dbb1` | Primary button / link hover |
| `--rs-ink` | `#222222` | Headings, footer background, strong text |
| `--rs-body` | `#666666` | Body copy |
| `--rs-muted` | `#999999` | Secondary / helper text |
| `--rs-border` | `#e1e1e1` | Borders, dividers |
| `--rs-border-soft` | `#ebebeb` | Soft borders, inputs |
| `--rs-surface` | `#ffffff` | Header, cards, page background |
| `--rs-surface-alt` | `#f4f4f4` | Alternate section backgrounds |
| `--rs-danger` | `#b10001` | Errors / sale alerts (theme) |
| `--rs-accent-warm` | `#d26e4b` | Secondary accent (banners / badges) |
| `--rs-accent-lime` | `#a8c26e` | Secondary accent (labels) |

### Typography

| Token | Value |
|-------|--------|
| `--rs-font` | `Poppins, sans-serif` |
| Weights | `400`, `500`, `600`, `700`, `800` |
| Body size (theme base) | `1.4rem` (theme rem root ≈ 10px → ~14px) |
| Letter-spacing (header) | `-0.025em` |

Load Poppins the same way as Demo22 (`js/webfont.js` or Google Fonts).

### Buttons (theme classes)

- Primary: `.btn.btn-primary` → `#05b895` / hover `#06dbb1`
- Dark: `.btn.btn-dark`
- Outline / link variants: use existing Riode `.btn-*` classes; prefer theme CSS over new one-offs.

### Layout conventions

- Wrapper: `.page-wrapper`
- Width: `.container` (theme grid)
- Header: white (`.header`), sticky middle bar
- Footer: dark `#222` (`.footer`)
- Icons: Riode icon font (`d-icon-*`) + Font Awesome in `vendor/`

---

## Theme file map

Source of truth: `C:\laragon\www\rafiandsons\theme`

| Purpose | Path |
|---------|------|
| Home reference | `theme/riode/demo22.html` |
| Shop | `theme/riode/demo22-shop.html` |
| Product | `theme/riode/product.html` |
| Cart / Checkout | `theme/riode/cart.html`, `checkout.html` |
| Wishlist / Account | `theme/riode/wishlist.html`, `account.html` |
| About / Contact / FAQ | `about-us.html`, `contact-us.html`, `faq.html` |
| CSS | `theme/riode/css/demo22.min.css` (+ `style.min.css` if needed) |
| JS | `theme/riode/js/main.min.js`, `webfont.js` |
| Vendors | `theme/riode/vendor/` (jQuery, Owl, Magnific, FA, …) |

**Rule:** Convert HTML pages into CI4 views; keep class names and asset paths aligned with Demo22. Do not invent a parallel design system.

---

## CI4 project layout

```
rafiandsons/
├── app/                  # Controllers, Models, Views, Config
├── public/               # Web root
│   ├── index.php
│   ├── assets/           # App-specific assets
│   └── theme/            # Junction → ../theme/riode
├── theme/riode/          # Original Riode Demo22 (do not “edit in place” long-term)
├── writable/
├── vendor/
├── .env
└── create.md             # This file
```

### Local config

```env
CI_ENVIRONMENT = development
app.baseURL = 'http://rafiandsons.test/'
```

Debug toolbar is disabled in `app/Config/Filters.php` (commented `toolbar`) so it does not show on storefront pages.

---

## Coming soon (current homepage)

| Item | Detail |
|------|--------|
| Route | `GET /` → `Home::index` |
| View | `app/Views/coming_soon.php` |
| Styling | Riode Demo22 CSS + Poppins + primary `#05b895` |
| Assets | Via `base_url('theme/...')` |

When the store launches, replace this route with the real home view converted from `demo22.html`.

---

## Development rules

1. **Theme first** — Match Demo22 markup/CSS; customize with CSS variables / small overrides only when FRS requires it.
2. **One brand accent** — Primary is `#05b895`. Avoid random purple/indigo UI kits.
3. **Views** — Prefer CI4 view partials (`header`, `footer`, `nav`) extracted from theme HTML.
4. **Assets** — Serve theme files through `public/theme/` junction; app-only files go in `public/assets/`.
5. **No drive-by refactors** — Change only what the current task needs.
6. **FRS-driven** — Full eCommerce features wait for the FRS; then we plan tasks (catalog, cart, checkout, auth, admin, payments, etc.).

---

## Next step (after FRS)

When you provide the FRS we will:

1. Map FRS modules → CI4 controllers/models/routes  
2. Map each storefront page → Demo22 HTML template  
3. Split work into ordered tasks (schema → catalog → cart → checkout → admin → …)  
4. Update this file with agreed scope, milestones, and acceptance checks  

Paste or attach the FRS when ready.
