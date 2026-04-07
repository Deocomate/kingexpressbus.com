# KingExpressBus

Single-tenant bus ticket booking platform built with Laravel 12.

## 1. Purpose

KingExpressBus provides two server-rendered portals:
- Admin portal for managing routes, buses, trips, stops, website settings, and bookings.
- Client portal for searching trips and creating bookings.

The current architecture is single-tenant (company-specific multi-tenant tables were removed by migration refactor).

## 2. Tech Stack

- PHP 8.2+
- Laravel 12
- MySQL
- Blade (SSR)
- CKFinder (media/files)

## 3. Documentation Map

- [Project Overview and PDR](docs/project-overview-pdr.md)
- [Codebase Summary](docs/codebase-summary.md)
- [System Architecture and Full Database Catalog](docs/system-architecture.md)
- [Coding Standards](docs/code-standards.md)

## 4. Quick Start

1. Clone repository.
2. Install dependencies.
3. Configure environment.
4. Run migrations.
5. Start app.

```bash
git clone https://github.com/yourusername/kingexpressbus.git
cd kingexpressbus
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run dev
```

Run backend/frontend separately if needed:

```bash
php artisan serve
npm run dev
```

## 5. Current Functional Areas

- Authentication for Admin and Client.
- Locale switching (`en`, `vi`).
- Admin CRUD: web profiles, menus, provinces, districts, stops, routes, buses, trips, bookings.
- Admin pricing module: holiday surcharge windows (global and route-specific additive adjustments).
- Client flow: search route/trip -> create booking -> success page.

## 6. Full Database Table Inventory

Source of truth is migrations in `database/migrations`, especially:
- `2025_09_22_152829_create_database_tables.php`

### 6.1 Active Tables (Current Single-Tenant)

Framework/auth/system tables:
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

Business/domain tables:
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

### 6.2 Legacy Tables (Dropped by Single-Tenant Refactor)

- `companies`
- `company_routes`
- `company_route_stops`
- `bus_routes`

These tables are legacy only and are not created by the current migrations.

## 7. Notes For Developer AI

- Prefer reading migrations over `database/database.sql` when schema conflicts exist.
- `trips` is the current schedule table (replaces `bus_routes`).
- `route_stops` is the current route-stop mapping table (replaces `company_route_stops`).
- `bookings.trip_id` is the current FK (replaces `bookings.bus_route_id`).

## License

MIT License
