## Database ER Diagram (Mermaid)

```mermaid
erDiagram
    USERS {
        bigInt id PK
        string name
        string email
        string phone
        string address
        enum role
    }

    PROVINCES {
        bigInt id PK
        string name
        string slug
    }

    DISTRICTS {
        bigInt id PK
        bigInt province_id FK
        bigInt district_type_id FK
        string name
    }

    DISTRICT_TYPES {
        bigInt id PK
        string name
    }

    STOPS {
        bigInt id PK
        bigInt district_id FK
        string name
        string address
    }

    ROUTES {
        bigInt id PK
        bigInt province_start_id FK
        bigInt province_end_id FK
        string name
        string slug
        int distance_km
        int price_default
        boolean available_hotel_pickup
    }

    ROUTE_STOPS {
        bigInt id PK
        bigInt route_id FK
        bigInt stop_id FK
        enum stop_type
        int priority
    }

    BUSES {
        bigInt id PK
        string name
        int seat_count
        json seat_map
    }

    TRIPS {
        bigInt id PK
        bigInt bus_id FK
        bigInt route_id FK
        time start_time
        time end_time
        int price
        boolean is_active
    }

    BOOKINGS {
        bigInt id PK
        string booking_code
        bigInt user_id FK NULL
        bigInt trip_id FK
        date booking_date
        string customer_name
        string customer_phone
        bigInt pickup_stop_id FK
        bigInt dropoff_stop_id FK
        int quantity
        int total_price
        enum status
        enum payment_method
        enum payment_status
    }

    WEB_PROFILES {
        bigInt id PK
        string profile_name
    }

    MENUS {
        bigInt id PK
        string name
        bigInt parent_id FK NULL
    }

    USERS ||--o{ BOOKINGS : makes
    TRIPS ||--o{ BOOKINGS : "has"
    BUSES ||--o{ TRIPS : runs
    ROUTES ||--o{ TRIPS : "defines"
    ROUTES ||--o{ ROUTE_STOPS : "has"
    STOPS ||--o{ ROUTE_STOPS : "used_in"
    PROVINCES ||--o{ DISTRICTS : contains
    DISTRICTS ||--o{ STOPS : contains
    ROUTES }|..|{ PROVINCES : "start/end"
    STOPS ||--o{ BOOKINGS : "pickup/dropoff"

```

Short notes
- This diagram reflects the current single-tenant schema after the 2026 refactor: company-related tables were merged/removed and are not shown.
- Key relationships:
  - trips link buses and routes; bookings link to trips and two stops (pickup/dropoff).
  - route_stops is a pivot with attributes stop_type and priority.
- Constraints and indexes (not shown visually) include foreign keys with cascade deletes and indexes on (route_id, priority), (route_id, is_active), and trip start_time.

Reference: database/migrations/* and app/Models/* for exact fields and migrations used for data migrations during refactor.

## Legacy Multi-tenant (Pre-2026) and Migration Mapping

```mermaid
flowchart LR
    subgraph Legacy[Legacy (pre-2026) - multi-tenant]
        COMPANIES[companies]
        COMPANY_ROUTES[company_routes]
        COMPANY_ROUTE_STOPS[company_route_stops]
        BUS_ROUTES[bus_routes]
    end

    subgraph Current[Current (post-2026) - single-tenant]
        ROUTES[routes]
        ROUTE_STOPS[route_stops]
        TRIPS[trips]
    end

    COMPANIES --> COMPANY_ROUTES
    COMPANY_ROUTES --> COMPANY_ROUTE_STOPS
    COMPANY_ROUTES --> BUS_ROUTES

    %% Migration arrows
    COMPANY_ROUTE_STOPS -->|migrated into| ROUTE_STOPS
    BUS_ROUTES -->|migrated into| TRIPS
    COMPANY_ROUTES -->|merged into (data merged)| ROUTES

    classDef legacy fill:#ffe6e6,stroke:#ff0000,stroke-width:1px;
    classDef current fill:#e6fff2,stroke:#009900,stroke-width:1px;
    class COMPANIES,COMPANY_ROUTES,COMPANY_ROUTE_STOPS,BUS_ROUTES legacy;
    class ROUTES,ROUTE_STOPS,TRIPS current;
```

Mapping summary (from historical single-tenant refactor, now consolidated into current schema migration):
- company_route_stops -> route_stops (INSERT ... SELECT mapping via company_routes.route_id)
- bus_routes -> trips (INSERT ... SELECT, mapping company_route_id -> route_id through company_routes)
- company_routes data attributes (available_hotel_pickup, price) were folded into routes (new columns added: price_default, available_hotel_pickup)
- companies, company_routes, company_route_stops, bus_routes were dropped after data migration

See migration file for exact SQL used during data copy and the order of operations (to respect FK constraints).

## Combined ER Diagram (Legacy + Current)

This diagram shows legacy multi-tenant tables (left, red) together with the current single-tenant schema (right, green). Dotted arrows represent data migrations performed in 2026 refactor.

```mermaid
flowchart TB
    %% Legacy (pre-2026)
    subgraph Legacy [Legacy (pre-2026) - multi-tenant]
        direction TB
        COMPANIES["companies\n(id PK, user_id, name, slug, phone, email)"]
        COMPANY_ROUTES["company_routes\n(id PK, company_id FK, route_id FK, slug, available_hotel_pickup, price, ...)"]
        COMPANY_ROUTE_STOPS["company_route_stops\n(id PK, company_route_id FK, stop_id FK, stop_type, priority)"]
        BUS_ROUTES["bus_routes\n(id PK, bus_id FK, company_route_id FK, start_time, end_time, price, is_active)"]
    end

    %% Current (post-2026)
    subgraph Current [Current (post-2026) - single-tenant]
        direction TB
        ROUTES["routes\n(id PK, province_start_id FK, province_end_id FK, slug, price_default, available_hotel_pickup, ...)"]
        ROUTE_STOPS["route_stops\n(id PK, route_id FK, stop_id FK, stop_type, priority)"]
        TRIPS["trips\n(id PK, bus_id FK, route_id FK, start_time, end_time, price, is_active)"]
        BUSES["buses\n(id PK, name, seat_count, seat_map, services, ...)"]
        STOPS["stops\n(id PK, district_id FK, name, address)"]
        BOOKINGS["bookings\n(id PK, booking_code, user_id NULLABLE FK, trip_id FK, pickup_stop_id FK, dropoff_stop_id FK, quantity, total_price, status)"]
        PROVINCES["provinces\n(id PK, name, slug)"]
        DISTRICTS["districts\n(id PK, province_id FK, district_type_id FK, name)"]
        USERS["users\n(id PK, name, email, phone, role)"]
    end

    %% Relationships (legacy)
    COMPANIES --> COMPANY_ROUTES
    COMPANY_ROUTES --> COMPANY_ROUTE_STOPS
    COMPANY_ROUTES --> BUS_ROUTES
    COMPANY_ROUTE_STOPS --> STOPS
    BUS_ROUTES --> BUSES

    %% Relationships (current)
    PROVINCES --> DISTRICTS
    DISTRICTS --> STOPS
    ROUTES --> ROUTE_STOPS
    ROUTE_STOPS --> STOPS
    ROUTES --> TRIPS
    TRIPS --> BUSES
    TRIPS --> BOOKINGS
    USERS --> BOOKINGS
    STOPS --> BOOKINGS

    %% Migration mapping (dotted)
    COMPANY_ROUTE_STOPS -.->|migrated into (INSERT SELECT)| ROUTE_STOPS
    BUS_ROUTES -.->|migrated into (INSERT SELECT)| TRIPS
    COMPANY_ROUTES -.->|merged/attributes moved into| ROUTES
    COMPANIES -.->|dropped after migration| ROUTES

    %% Styling for visual separation
    classDef legacy fill:#fff0f0,stroke:#ff5555,stroke-width:1px;
    classDef current fill:#f0fff4,stroke:#088f4f,stroke-width:1px;
    class COMPANIES,COMPANY_ROUTES,COMPANY_ROUTE_STOPS,BUS_ROUTES legacy;
    class ROUTES,ROUTE_STOPS,TRIPS,BUSES,STOPS,BOOKINGS,PROVINCES,DISTRICTS,USERS current;

    %% Legend (rendered as nodes)
    LEGEND1[["Legacy: red boxes (pre-2026)"]]
    LEGEND2[["Current: green boxes (post-2026)"]]
    LEGEND3[["Dotted arrows: data migration mapping (2026)"]]
    LEGEND1 --> LEGEND2

```

Notes:
- The combined diagram intentionally shows both sets to help understand how historical multi-tenant structures were transformed into the current single-tenant model.
- The refactor migration file was merged; use database/migrations/2025_09_22_152829_create_database_tables.php as the current schema source of truth.
