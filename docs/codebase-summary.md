# Codebase Summary

## 1. Architecture Overview
The project follows the standard **Laravel 11/12** MVC architecture. It is a monolithic application serving two distinct user panels (Admin, Client) via server-side rendering (Blade templates implied, though API structure is also present but currently commented out).

## 2. Directory Structure

### `app/`
-   **Http/Controllers:** Organized by functional modules:
    -   `Admin/`: Controllers for platform administration (RouteController, BusController, TripController, etc.).
    -   `Client/`: Controllers for the public facing website (HomeController, BookingController).
    -   `Auth/`: Authentication logic (Login, Register, Logout).
    -   `System/`: Utility controllers (e.g., CkFinderController).
-   **Http/Middleware:** Custom middleware for role-based access:
    -   `Roles/AdminAuthMiddleware`
    -   `Roles/CustomerAuthMiddleware`
-   **Models:** Eloquent models representing the database entities (User, Bus, Route, Booking, etc.).

### `routes/`
-   **`web.php`:** Main entry point for all application routes.
    -   **Prefix `admin.`**: Protected by Admin middleware.
    -   **Prefix `client.`**: Public and Customer middleware routes.
-   **`api.php`**: Currently commented out/placeholder.

### `database/`
-   **Migrations:** robust schema definition handling relational integrity (Foreign Keys with Cascades).

## 3. Key Functionalities & Logic

### Authentication
-   The system uses Laravel's built-in Auth mechanisms but separates sessions/guards for different user types (likely configured in `config/auth.php` - *to be verified*).

### Content Management
-   **CKFinder Integration:** The project includes CKFinder for handling file uploads (images for buses, routes), integrated via `CkFinderController`.

### Localization
-   Route `/locale/{locale}` handles session-based language switching ('en', 'vi').

## 4. Dependencies
-   `laravel/framework`: ^12.0 (Bleeding edge/Latest)
-   `ckfinder/ckfinder-laravel-package`: ^5.0 (File management)
-   `resend/resend-php`: Email service integration.
-   `laravel/sanctum`: API token management (installed but API routes are currently inactive).
