# KingExpressBus

Single-tenant bus ticket booking system for managing transportation services. Built with Laravel 12.

## Project Overview

KingExpressBus is a comprehensive solution for managing bus transportation services. It provides:
-   **Admin Portal:** For centralized management of buses, routes, trips (schedules), and bookings.
-   **Client Portal:** A user-friendly interface for customers to search for trips, view details, and book tickets.

## Architecture (Single-Tenant)

This system follows a single-tenant architecture where:
- All buses are managed directly by the system administrator
- Routes define the travel paths between provinces
- Trips are scheduled departures with specific buses, times, and prices
- Bookings are made against specific trips

## Documentation

-   [Project Overview & Requirements](docs/project-overview-pdr.md)
-   [Codebase Summary](docs/codebase-summary.md)
-   [System Architecture](docs/system-architecture.md)
-   [Coding Standards](docs/code-standards.md)

## Requirements

-   PHP >= 8.2
-   Composer
-   Node.js & NPM
-   MySQL

## Installation & Setup

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/yourusername/kingexpressbus.git
    cd kingexpressbus
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Install Frontend dependencies:**
    ```bash
    npm install
    ```

4.  **Environment Configuration:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Update `.env` with your database credentials.*

5.  **Database Migration:**
    ```bash
    php artisan migrate
    ```

6.  **Run the application:**
    ```bash
    # Run backend and frontend build in parallel
    npm run dev
    ```
    *Or run them separately:*
    ```bash
    php artisan serve
    npm run dev
    ```

## Key Features

-   **Single-Tenant System:** Centralized management by Admin.
-   **Bus Fleet Management:** Manage buses with seat maps, services, and details.
-   **Route Management:** Define routes between provinces with stops.
-   **Trip Scheduling:** Create trips with specific buses, times, and prices.
-   **Booking System:** Complete flow from search to payment status tracking.
-   **Localization:** Support for English and Vietnamese.
-   **Media Management:** Integration with CKFinder.

## Database Structure

- `routes` - Travel routes between provinces
- `route_stops` - Pickup/dropoff stops for each route
- `buses` - Fleet of buses with seat maps and services
- `trips` - Scheduled departures (bus + route + time + price)
- `bookings` - Customer reservations for trips

## License

MIT License.
