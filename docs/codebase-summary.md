# Codebase Summary

## 1. Architecture Snapshot

- Pattern: Laravel MVC monolith.
- Runtime style: server-side rendered web app (Blade views).
- Product shape: two main panels (`admin`, `client`) with shared domain models.
- Tenant model: single-tenant (legacy multi-tenant tables are deprecated by migration refactor).

## 2. Runtime Areas

### 2.1 Admin Area

Two admin UIs currently run in parallel (strangler):
- Filament panel at `/admin` (legacy; removal is Phase 10 of the Blade migration plan)
- Blade + Tailwind + Alpine admin at `/quan-tri` — feature parity hardened (Phase 9); maintainer UI guide: `docs/admin-ui-guidelines.md`

Main responsibilities:
- Website configuration (`web_profiles`, `menus`) — Blade module at `/quan-tri/cau-hinh-website` (`?section=profile|menus`; nested SortableJS tree ≤4 levels)
- Location data (`provinces`, `district_types`, `districts`, `stops`)
- Route and fleet management (`routes`, `route_stops`, `buses`, `bus_services`)
- Schedule / trip blocks (`trips`, trip blocks)
- Booking operations (`bookings`) — Blade module at `/quan-tri/dat-ve` (5 status tabs, actions via `BookingService`)
- Dashboard stats/charts at `/quan-tri` (cached 60s via `ClientCache::ADMIN_DASHBOARD_STATS`)
- Holiday surcharge management (`holiday_surcharges`, `holiday_surcharge_routes`)

### 2.2 Client Area

Main responsibilities:
- Home/search pages
- Route/trip detail pages
- Booking submission flow
- Locale switching (`en`, `vi`)
- Profile/account pages under customer-auth middleware

Client UI assets are built with Vite + Tailwind from `resources/css/app.css` and `resources/js/app.js`. Shared client design guidance lives in `docs/design-guidelines.md`.

### 2.3 Authentication & Middleware

- Blade admin auth: `App\Http\Middleware\AdminAuthMiddleware` (prefix `/quan-tri`; priority before route-model binding)
- Filament admin panel auth remains separate until cutover
- Customer auth middleware: under `App\Http\Middleware\Roles\` (client portal)

## 3. Important Directories

- `app/Http/Controllers/Admin`: Blade admin feature controllers (`/quan-tri`)
- `app/Support/Admin`: table engine, option sources, dashboard data, delete guard, upload staging, menu tree builder
- `resources/views/admin`: Blade admin views
- `routes/admin/`: one route file per admin module
- `app/Http/Controllers/Client`: client feature controllers
- `app/Http/Controllers/Auth`: authentication controllers
- `app/Services`: business logic services for booking, bus, route, trip, and home
- `app/Models`: Eloquent models (Booking, Bus, District, Province, Route, Stop, Trip, User, etc.)
- `resources/views/client`: public client Blade views
- `resources/css` and `resources/js`: Vite-built client + admin frontend assets
- `assets/client`: committed client static images and icons (served via `public/assets/client`)
- `assets/admin`: admin static assets; Filament vendor assets publish to `public/assets/admin/filament`
- `storage/app/public`: user uploads (`buses/`, `routes/`, `media/`, etc.)
- `routes/web.php`: primary client/public route definitions
- `database/migrations`: schema source of truth

## 4. Current Database Coverage (All Active Tables)

Framework/system:
- `migrations`
- `users`
- `password_reset_tokens`
- `sessions`
- `cache`
- `cache_locks`
- `jobs`
- `job_batches`
- `failed_jobs`
- `personal_access_tokens`

Domain/business:
- `web_profiles`
- `menus`
- `provinces`
- `district_types`
- `districts`
- `stops`
- `routes`
- `route_stops`
- `bus_services`
- `buses`
- `trips`
- `bookings`
- `holiday_surcharges`
- `holiday_surcharge_routes`

Legacy historical tables (dropped in single-tenant refactor):
- `companies`
- `company_routes`
- `company_route_stops`
- `bus_routes`

## 5. Key Data Flow

- Admin creates and maintains location + route + bus + trip data.
- Admin configures holiday surcharge windows and optional route-level surcharge overrides.
- Client searches trips and submits booking requests.
- Booking references trip and stop points, resolves effective price by travel date, then moves through status/payment lifecycle.

## 6. Dependencies

- `laravel/framework` ^12.0
- `resend/resend-php` ^0.11
- `laravel/sanctum` ^4.2
