# Codebase Summary

## 1. Architecture Snapshot

- Pattern: Laravel MVC monolith.
- Runtime style: server-side rendered web app (Blade views).
- Product shape: two main panels (`admin`, `client`) with shared domain models.
- Tenant model: single-tenant (legacy multi-tenant tables are deprecated by migration refactor).

## 2. Runtime Areas

### 2.1 Admin Area

Main responsibilities:
- Website configuration (`web_profiles`, `menus`)
- Location data (`provinces`, `district_types`, `districts`, `stops`)
- Route and fleet management (`routes`, `route_stops`, `buses`, `bus_services`)
- Schedule management (`trips`)
- Booking operations (`bookings`)
- Holiday surcharge management (`holiday_surcharges`, `holiday_surcharge_routes`)

### 2.2 Client Area

Main responsibilities:
- Home/search pages
- Route/trip detail pages
- Booking submission flow
- Locale switching (`en`, `vi`)
- Profile/account pages under customer-auth middleware

### 2.3 Authentication & Middleware

- Admin auth middleware: `App\Http\Middleware\Roles\AdminAuthMiddleware`
- Customer auth middleware: `App\Http\Middleware\Roles\CustomerAuthMiddleware`

## 3. Important Directories

- `app/Http/Controllers/Admin`: admin feature controllers
- `app/Http/Controllers/Client`: client feature controllers
- `app/Http/Controllers/Auth`: authentication controllers
- `app/Services`: business logic services for booking, bus, route, trip, and home
- `app/Models`: Eloquent models (Booking, Bus, District, Province, Route, Stop, Trip, User, etc.)
- `routes/web.php`: primary route definitions
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
- `ckfinder/ckfinder-laravel-package` ^5.0
- `resend/resend-php` ^0.11
- `laravel/sanctum` ^4.2
