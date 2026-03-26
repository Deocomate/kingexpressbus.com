# Project Overview & Product Development Requirements (PDR)

## 1. Product Overview

KingExpressBus is a single-tenant bus booking system built on Laravel 12.

It serves two audiences:
- Admin: controls master data and operations.
- Client (customer): searches trips and creates bookings.

Core domain is route-trip-booking management with supporting location data and website configuration.

## 2. Stakeholders And Roles

- Administrator (Admin): full CRUD on operational data and platform settings.
- Customer (Client): public browsing + booking + account profile actions.

## 3. Functional Requirements

### 3.1 Authentication & Access Control

- Separate admin and client authentication flows.
- Public registration for clients.
- Middleware-based role protection:
    - `AdminAuthMiddleware`
    - `CustomerAuthMiddleware`

### 3.2 Admin Capabilities

- Dashboard and management screens.
- Website configuration via `web_profiles` and `menus`.
- Location management:
    - `provinces`
    - `district_types`
    - `districts`
    - `stops`
- Transport and schedule management:
    - `routes`
    - `route_stops`
    - `buses`
    - `bus_services`
    - `trips`
- Booking management:
    - `bookings`

### 3.3 Client Capabilities

- Search trips by origin/destination/date.
- View route and trip details.
- Booking flow:
    - choose trip
    - provide customer info
    - choose pickup/dropoff points
    - submit booking
- Localization support (`vi`, `en`).

## 4. Non-Functional Requirements

- Performance: fast search and booking response.
- Security: role-protected admin/client areas.
- Maintainability: thin controllers, service-layer business logic.
- SEO/content: route/location metadata and static content pages.

## 5. Database Scope (Complete Inventory)

The documentation scope includes all current active tables in the application schema.

### 5.1 Framework/System Tables

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

### 5.2 Business Tables

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

### 5.3 Legacy (Historical, Not Active In Current Single-Tenant Runtime)

- `companies`
- `company_routes`
- `company_route_stops`
- `bus_routes`

These legacy tables exist for migration history/rollback and are not part of the current target runtime schema.

## 6. Product Roadmap (High-Level)

- Phase 1: core booking and admin management.
- Phase 2: richer seat selection and payment enhancements.
- Phase 3: API/mobile expansion and loyalty features.
