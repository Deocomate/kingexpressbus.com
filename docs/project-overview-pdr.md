# Project Overview & Product Development Requirements (PDR)

## 1. Project Overview
**KingExpressBus** is a single-tenant bus ticket booking platform. The system facilitates the management of routes, trips, buses, and bookings, providing distinct interfaces for platform administrators and end-users (customers).

### Key Objectives
-   **Centralized Management:** Allow platform admins to manage locations, routes, buses, trips, and platform settings.
-   **User Convenience:** Provide a seamless booking experience for customers with search, seat selection, and payment options.

## 2. Stakeholders & Roles
-   **Administrator (Admin):** Platform owner with full access to system configurations, master data (locations), and oversight of all bookings.
-   **Customer (Client):** End-users who search for trips, book tickets, and manage their travel history.

## 3. Product Development Requirements (PDR)

### 3.1 Functional Requirements

#### A. Authentication & Authorization
-   **Multi-guard Auth:** Distinct authentication flows for Admin and Customer.
-   **Registration:** Public registration for Customers. Admin account creation is internal.
-   **Profile Management:** Users can update personal information and passwords.

#### B. Admin Module
-   **Dashboard:** System-wide statistics.
-   **Website Configuration:** Manage logo, SEO settings, contact info, and menus via `web_profiles` and `menus`.
-   **Location Management:** CRUD for Provinces, Districts, Stops, and District Types.
-   **Route Management:** Define routes (Start Province -> End Province).
-   **Fleet Management:** Manage buses, seat maps, and amenities.
-   **Trip Scheduling:** Create trips with time, price, and availability.
-   **Booking Oversight:** View and manage all bookings across the platform.

#### C. Client (Customer) Module
-   **Search & Discovery:** Search trips by origin, destination, and date.
-   **Route Details:** View bus details, amenities, photos, and policies.
-   **Booking Flow:**
    -   Select Route -> Select Seat (implied by schema) -> Enter Info -> Payment.
    -   Support for multiple payment methods (Online Banking, Cash on Pickup).
-   **Localization:** Switch between Vietnamese (vi) and English (en).
-   **Static Pages:** About Us, Contact, Articles.

### 3.2 Non-Functional Requirements
-   **Performance:** Optimized for fast search results.
-   **SEO:** Dynamic meta tags for routes and locations.
-   **Scalability:** Database design supports routes, trips, and future expansions.
-   **Security:** Role-based access control (RBAC) middleware (`AdminAuthMiddleware`, `CustomerAuthMiddleware`).

## 4. Roadmap (Inferred)
-   **Phase 1 (MVP):** Core booking flow, admin panel, manual payment confirmation.
-   **Phase 2:** Advanced seat selection (visual), Payment Gateway integration.
-   **Phase 3:** Mobile App API, Loyalty program.
