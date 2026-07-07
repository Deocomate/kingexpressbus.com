# Client Frontend Design Guidelines

## Scope

These guidelines apply to the public client portal only. Filament admin screens keep their own design system.

## Build

- Client assets are bundled through Vite from `resources/css/app.css` and `resources/js/app.js`.
- Do not add CDN Tailwind, Google Fonts, Font Awesome, flatpickr, toastr, or page-level framework scripts.
- Runtime globals required by Blade are exposed in `resources/js/app.js`: `flatpickr`, `kingSearchBar`, `Swal`, and `toastr`.

## Visual System (Flat Enterprise / OTA)

- **Geometry:** 2px corners everywhere (`rounded-sm`, token `control`/`panel` = 2px). No pill shapes on cards, chips, buttons, or avatars. Only loading spinners may use `rounded-full`.
- **Page background:** `bg-page` (`#FFFDF7`).
- **Surfaces:** `bg-surface` / white with `border border-line` or `border-line-strong`. No drop shadows for structure — borders define edges.
- **Overlays only:** dropdowns, drawers, and modals may use `shadow-card`.
- **Brand color:** yellow-orange (`brand-500` / `brand-600`) is for **primary actions and active states only** — not decoration. Primary button: flat `bg-brand-500 text-ink`, hover `bg-brand-600`. No multi-stop gradients on buttons or bands.
- **Text:** `text-ink`; secondary copy: `text-muted`.
- **Dark surfaces:** footer, hero scrims, and focused CTA bands (`bg-contrast-900`).
- **Spacing:** tighter vertical rhythm — `ksb-section` (`py-8 md:py-10 lg:py-12`), `ksb-section-compact` (`py-5`), compact search-first hero (`ksb-home-hero` min-height ~380–520px).
- **Motion:** functional transitions only (hover/focus, dropdown/drawer enter). No ambient drift, marquee, sheen, pulse, or scroll-shrink gimmicks.
- **Typography:** Be Vietnam Pro, weights 400–800.

## Header (two-tier)

- **Top bar** (`kx-header-top`, `lg+` only): hotline, locale switcher, login/register or account menu — right-aligned, `h-9`, `bg-page`.
- **Main bar** (`h-14`): logo + brand | center nav (`xl+`) | primary CTA (`sm+`) | hamburger (`<xl`).
- **Mobile**: drawer holds nav, CTA, auth, locale; hotline pinned in drawer footer. Do not pack utility items into the main row on desktop.

## Components

Prefer these primitives before adding page-specific utility bundles:

- Buttons: `kx-btn-primary`, `kx-btn-secondary`, `kx-btn-ghost`.
- Panels/cards: `kx-panel`, `kx-panel-strong`, `kx-card`, `kx-surface`.
- Form controls: `kx-form-control`.
- Labels and chips: `kx-section-label`, `kx-badge`, `kx-chip`, `kx-chip-active`.
- Price text: `kx-price` or `ksb-price`.
- CMS content: `kx-prose`.
- Section wrappers: `ksb-section`, `ksb-section-compact`, `ksb-section-hero`, `ksb-section-cta`.

The `ksb-*` component classes remain supported in the Vite bundle. Do not reintroduce `public/client/css/custom.css` or `public/client/js/client-ui.js`.

## Copy and i18n

- Every user-visible string in client Blade must use `__('client...')` — no hardcoded English or Vietnamese literals in views.
- `lang/vi/client.php` and `lang/en/client.php` must stay in parity (identical key sets).
- Tone: professional, trustworthy, declarative. No slang, emoji, hype, or exclamation-heavy phrasing.
- Keep placeholders (`:hotline`, `:name`, etc.) and inline HTML in translation values intact when editing copy.

## Interaction Contracts

- Search bar must keep `x-data="kingSearchBar(...)"`, hidden input names, and query param names.
- Booking create must keep all existing `id`, `name`, `.payment-method-input`, `.stop-card`, and `data-summary` hooks because the inline booking script depends on them.
- Route detail must keep gallery/modal/filter hooks such as `data-trip-modal-target`, `data-gallery-preview`, `data-lightbox-*`, and mobile filter panel IDs.
- Profile tabs must keep `.tab-btn`, `.tab-content`, and `data-target`.
- Reveal hook: `.ksb-reveal` / `.is-visible` (queried by `resources/js/modules/reveal.js`) — do not rename.

## QA Checklist

- `npm run build`
- `php artisan test --filter=ClientUiRefactorTest`
- Grep gates (client views/components):
  - No `rounded-xl|2xl|3xl|full|panel|control` except spinner `rounded-full`
  - No decorative `bg-gradient` / `from-brand` / `to-amber` washes
  - No `ksb-marquee`, `soft-pulse`, or removed hero ambient keyframes
  - No CDN/legacy client assets: `rg "cdn.tailwindcss|custom.css|client-ui.js|fonts.googleapis|fonts.gstatic" resources/views public routes`
- Smoke-test home search, routes list/detail, booking create, SePay redirect, auth forms, profile tabs, locale switch, header drawer, and CMS page rendering in both `vi` and `en`.
