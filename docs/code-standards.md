# Coding Standards & Conventions

## 1. Baseline Standards

- PHP version: 8.2+
- Laravel conventions + PSR-12 style
- Keep controllers thin and move business logic into service classes

## 2. Naming Conventions

### 2.1 PHP Classes And Files

- Controllers: `PascalCase` + `Controller` suffix
    - Example: `TripController`, `BookingController`
- Models: singular `PascalCase`
    - Example: `Route`, `Trip`, `Booking`
- Services: `PascalCase` + `Service` suffix
    - Example: `TripService`, `BookingService`
- Migrations: timestamp + `snake_case`
    - Example: `2025_09_22_152829_create_database_tables.php`

### 2.2 Database

- Table names: plural `snake_case`
    - Example: `route_stops`, `bus_services`, `personal_access_tokens`
- Column names: `snake_case`
    - Example: `start_time`, `price_default`, `available_hotel_pickup`
- Foreign keys: `singular_table_id`
    - Example: `trip_id`, `pickup_stop_id`, `province_start_id`

### 2.3 Routes

- Route names: dot notation with kebab-like resource segments
    - Example: `admin.trips.index`, `client.routes.search`
- URI paths: lowercase/kebab style where applicable
    - Example: `/tuyen-duong`, `/dat-ve`, `/quan-tri/chuyen-xe`

## 3. Architecture Conventions

### 3.1 Layers

- Controller layer: request validation + orchestration only
- Service layer: business rules and workflow orchestration
- Model layer: relationships, casting, query scopes

### 3.2 Current Domain Conventions

- Active scheduling table is `trips` (not `bus_routes`)
- Active route-stop mapping is `route_stops` (not `company_route_stops`)
- Booking foreign key is `bookings.trip_id`

### 3.3 Views

Keep Blade views grouped by feature area:
- `resources/views/admin/`
- `resources/views/client/`
- shared components under dedicated partial/component folders

## 4. Documentation Conventions (For AI + Developers)

- Use migration files as schema source-of-truth.
- If SQL dump and migrations differ, document migration-based reality first.
- Always separate active tables from legacy tables in docs.
- Prefer short sections with explicit headings and bullet lists.

## 5. Git Conventions

- Commit messages: imperative and descriptive
    - Example: `Update docs with full active database schema`
- Branch naming:
    - `feature/<short-description>`
    - `fix/<short-description>`
