#!/usr/bin/env python3
"""
Database Migration Script
Migrate from old multi-tenant schema (db_kingexpressbus_old) to new single-tenant schema (db_kingexpressbus)

OLD SCHEMA (Multi-tenant):
- companies -> REMOVED
- company_routes -> merged into routes
- company_route_stops -> becomes route_stops
- bus_routes -> becomes trips
- buses.company_id -> REMOVED
- bookings.bus_route_id -> renamed to trip_id

NEW SCHEMA (Single-tenant):
- routes (+ price_default, available_hotel_pickup)
- route_stops (linked to routes)
- trips (replaces bus_routes, linked to routes)
- buses (no company_id)
- bookings (trip_id instead of bus_route_id)
"""

import mysql.connector
from mysql.connector import Error
from datetime import datetime
import json
import sys

# Database configuration
OLD_DB_CONFIG = {
    'host': 'localhost',
    'port': 3306,
    'user': 'root',
    'password': '',
    'database': 'db_kingexpressbus_old'
}

NEW_DB_CONFIG = {
    'host': 'localhost',
    'port': 3306,
    'user': 'root',
    'password': '',
    'database': 'db_kingexpressbus'
}


class DatabaseMigrator:
    """Handles migration from old multi-tenant to new single-tenant database"""

    def __init__(self):
        self.old_conn = None
        self.new_conn = None
        self.old_cursor = None
        self.new_cursor = None

        # Mapping tables for foreign key resolution
        self.company_route_to_route = {}  # Maps old company_route_id -> route_id
        self.bus_route_to_trip = {}  # Maps old bus_route_id -> new trip_id (usually same)

    def connect(self):
        """Establish connections to both databases"""
        try:
            print("Connecting to OLD database...")
            self.old_conn = mysql.connector.connect(**OLD_DB_CONFIG)
            self.old_cursor = self.old_conn.cursor(dictionary=True)
            print(f"  Connected to {OLD_DB_CONFIG['database']}")

            print("Connecting to NEW database...")
            self.new_conn = mysql.connector.connect(**NEW_DB_CONFIG)
            self.new_cursor = self.new_conn.cursor(dictionary=True)
            print(f"  Connected to {NEW_DB_CONFIG['database']}")

            return True
        except Error as e:
            print(f"Error connecting to database: {e}")
            return False

    def close(self):
        """Close all database connections"""
        if self.old_cursor:
            self.old_cursor.close()
        if self.new_cursor:
            self.new_cursor.close()
        if self.old_conn:
            self.old_conn.close()
        if self.new_conn:
            self.new_conn.close()
        print("Database connections closed.")

    def truncate_new_table(self, table_name):
        """Truncate a table in the new database (disable FK checks temporarily)"""
        try:
            self.new_cursor.execute("SET FOREIGN_KEY_CHECKS = 0")
            self.new_cursor.execute(f"TRUNCATE TABLE `{table_name}`")
            self.new_cursor.execute("SET FOREIGN_KEY_CHECKS = 1")
            self.new_conn.commit()
        except Error as e:
            print(f"  Warning: Could not truncate {table_name}: {e}")

    def get_table_count(self, cursor, table_name):
        """Get row count from a table"""
        try:
            cursor.execute(f"SELECT COUNT(*) as cnt FROM `{table_name}`")
            result = cursor.fetchone()
            return result['cnt'] if result else 0
        except Error:
            return 0

    def table_exists(self, cursor, table_name):
        """Check if a table exists in the database"""
        try:
            cursor.execute(f"SHOW TABLES LIKE '{table_name}'")
            result = cursor.fetchone()
            return result is not None
        except Error:
            return False

    def migrate_simple_table(self, table_name, columns=None, transform_fn=None):
        """Migrate a table that has same structure in both databases"""
        print(f"\n[MIGRATING] {table_name}...")

        try:
            # Check if table exists in old database
            if not self.table_exists(self.old_cursor, table_name):
                print(f"  Table {table_name} does not exist in old database, skipping")
                return 0

            # Check if table exists in new database
            if not self.table_exists(self.new_cursor, table_name):
                print(f"  Table {table_name} does not exist in new database, skipping")
                return 0

            # Get data from old database
            if columns:
                cols = ', '.join([f'`{c}`' for c in columns])
                self.old_cursor.execute(f"SELECT {cols} FROM `{table_name}`")
            else:
                self.old_cursor.execute(f"SELECT * FROM `{table_name}`")

            rows = self.old_cursor.fetchall()

            if not rows:
                print(f"  No data to migrate in {table_name}")
                return 0

            # Truncate new table first
            self.truncate_new_table(table_name)

            # Get column names from the first row
            col_names = list(rows[0].keys())
            placeholders = ', '.join(['%s'] * len(col_names))
            col_str = ', '.join([f'`{c}`' for c in col_names])

            insert_sql = f"INSERT INTO `{table_name}` ({col_str}) VALUES ({placeholders})"

            count = 0
            for row in rows:
                # Apply transformation if provided
                if transform_fn:
                    row = transform_fn(row)
                    if row is None:
                        continue

                values = [row[col] for col in col_names]
                self.new_cursor.execute(insert_sql, values)
                count += 1

            self.new_conn.commit()
            print(f"  Migrated {count} rows to {table_name}")
            return count

        except Error as e:
            print(f"  Error migrating {table_name}: {e}")
            self.new_conn.rollback()
            return 0

    def migrate_users(self):
        """Migrate users table - convert 'company' role users to 'customer'"""
        print("\n[MIGRATING] users...")

        try:
            self.old_cursor.execute("SELECT * FROM users")
            rows = self.old_cursor.fetchall()

            if not rows:
                print("  No users to migrate")
                return 0

            self.truncate_new_table('users')

            count = 0
            for row in rows:
                # Convert 'company' role to 'customer' since company role is removed
                if row['role'] == 'company':
                    row['role'] = 'customer'
                    print(f"  User {row['id']} ({row['email']}): role 'company' -> 'customer'")

                col_names = list(row.keys())
                placeholders = ', '.join(['%s'] * len(col_names))
                col_str = ', '.join([f'`{c}`' for c in col_names])

                insert_sql = f"INSERT INTO `users` ({col_str}) VALUES ({placeholders})"
                values = [row[col] for col in col_names]

                self.new_cursor.execute(insert_sql, values)
                count += 1

            self.new_conn.commit()
            print(f"  Migrated {count} users")
            return count

        except Error as e:
            print(f"  Error migrating users: {e}")
            self.new_conn.rollback()
            return 0

    def migrate_routes_with_company_data(self):
        """
        Migrate routes and merge company_routes data.
        For each route, take the first company_route's additional data (price, hotel_pickup flag).
        """
        print("\n[MIGRATING] routes (merged with company_routes data)...")

        try:
            # Get all routes from old database
            self.old_cursor.execute("SELECT * FROM routes")
            routes = self.old_cursor.fetchall()

            if not routes:
                print("  No routes to migrate")
                return 0

            # Build mapping: route_id -> first company_route data
            self.old_cursor.execute("""
                SELECT cr.*,
                       (SELECT price FROM bus_routes br WHERE br.company_route_id = cr.id LIMIT 1) as default_price
                FROM company_routes cr
            """)
            company_routes = self.old_cursor.fetchall()

            # Map route_id to company_route data (take first one for each route)
            route_extra_data = {}
            for cr in company_routes:
                route_id = cr['route_id']
                if route_id not in route_extra_data:
                    route_extra_data[route_id] = {
                        'price_default': cr.get('default_price') or 0,
                        'available_hotel_pickup': cr.get('available_hotel_pickup', False)
                    }
                # Also build company_route_id -> route_id mapping
                self.company_route_to_route[cr['id']] = route_id

            self.truncate_new_table('routes')

            count = 0
            for route in routes:
                route_id = route['id']

                # Get extra data from company_routes if exists
                extra = route_extra_data.get(route_id, {
                    'price_default': 0,
                    'available_hotel_pickup': False
                })

                # Insert with new columns
                insert_sql = """
                    INSERT INTO routes (
                        id, province_start_id, province_end_id, name, slug, title, description,
                        duration, distance_km, price_default, thumbnail_url, image_list_url,
                        content, available_hotel_pickup, priority, created_at, updated_at
                    ) VALUES (
                        %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
                    )
                """

                # Handle JSON fields
                image_list_url = route.get('image_list_url')
                if image_list_url and isinstance(image_list_url, str):
                    pass  # Already string
                elif image_list_url:
                    image_list_url = json.dumps(image_list_url)

                values = (
                    route['id'],
                    route['province_start_id'],
                    route['province_end_id'],
                    route['name'],
                    route['slug'],
                    route.get('title'),
                    route.get('description'),
                    route.get('duration'),
                    route.get('distance_km'),
                    extra['price_default'],
                    route.get('thumbnail_url'),
                    image_list_url,
                    route.get('content'),
                    1 if extra['available_hotel_pickup'] else 0,
                    route.get('priority', 0),
                    route.get('created_at'),
                    route.get('updated_at')
                )

                self.new_cursor.execute(insert_sql, values)
                count += 1

            self.new_conn.commit()
            print(f"  Migrated {count} routes (with merged company_routes data)")
            print(f"  Built mapping for {len(self.company_route_to_route)} company_routes -> routes")
            return count

        except Error as e:
            print(f"  Error migrating routes: {e}")
            self.new_conn.rollback()
            return 0

    def migrate_buses_without_company(self):
        """Migrate buses table without company_id"""
        print("\n[MIGRATING] buses (without company_id)...")

        try:
            self.old_cursor.execute("SELECT * FROM buses")
            rows = self.old_cursor.fetchall()

            if not rows:
                print("  No buses to migrate")
                return 0

            self.truncate_new_table('buses')

            insert_sql = """
                INSERT INTO buses (
                    id, name, model_name, seat_count, seat_map, services,
                    thumbnail_url, image_list_url, content, priority, created_at, updated_at
                ) VALUES (
                    %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
                )
            """

            count = 0
            for row in rows:
                # Handle JSON fields
                seat_map = row.get('seat_map')
                if seat_map and not isinstance(seat_map, str):
                    seat_map = json.dumps(seat_map)

                services = row.get('services')
                if services and not isinstance(services, str):
                    services = json.dumps(services)

                image_list_url = row.get('image_list_url')
                if image_list_url and not isinstance(image_list_url, str):
                    image_list_url = json.dumps(image_list_url)

                values = (
                    row['id'],
                    row['name'],
                    row.get('model_name'),
                    row['seat_count'],
                    seat_map,
                    services,
                    row.get('thumbnail_url'),
                    image_list_url,
                    row.get('content'),
                    row.get('priority', 0),
                    row.get('created_at'),
                    row.get('updated_at')
                )

                self.new_cursor.execute(insert_sql, values)
                count += 1

            self.new_conn.commit()
            print(f"  Migrated {count} buses (company_id removed)")
            return count

        except Error as e:
            print(f"  Error migrating buses: {e}")
            self.new_conn.rollback()
            return 0

    def migrate_route_stops(self):
        """
        Migrate company_route_stops to route_stops.
        Map company_route_id to route_id using the mapping built earlier.
        Deduplicate stops for the same route.
        """
        print("\n[MIGRATING] company_route_stops -> route_stops...")

        try:
            self.old_cursor.execute("SELECT * FROM company_route_stops")
            rows = self.old_cursor.fetchall()

            if not rows:
                print("  No route_stops to migrate")
                return 0

            self.truncate_new_table('route_stops')

            insert_sql = """
                INSERT INTO route_stops (route_id, stop_id, stop_type, priority, created_at, updated_at)
                VALUES (%s, %s, %s, %s, %s, %s)
            """

            # Track unique (route_id, stop_id) pairs to avoid duplicates
            seen = set()
            count = 0
            now = datetime.now()

            for row in rows:
                company_route_id = row['company_route_id']

                # Get route_id from mapping
                route_id = self.company_route_to_route.get(company_route_id)
                if route_id is None:
                    print(f"  Warning: company_route_id {company_route_id} not found in mapping, skipping")
                    continue

                key = (route_id, row['stop_id'])
                if key in seen:
                    continue  # Skip duplicate
                seen.add(key)

                values = (
                    route_id,
                    row['stop_id'],
                    row['stop_type'],
                    row['priority'],
                    now,
                    now
                )

                self.new_cursor.execute(insert_sql, values)
                count += 1

            self.new_conn.commit()
            print(f"  Migrated {count} route_stops (deduplicated from {len(rows)} company_route_stops)")
            return count

        except Error as e:
            print(f"  Error migrating route_stops: {e}")
            self.new_conn.rollback()
            return 0

    def migrate_trips(self):
        """
        Migrate bus_routes to trips.
        Map company_route_id to route_id using the mapping built earlier.
        Keep the same ID to preserve booking references.
        """
        print("\n[MIGRATING] bus_routes -> trips...")

        try:
            self.old_cursor.execute("SELECT * FROM bus_routes")
            rows = self.old_cursor.fetchall()

            if not rows:
                print("  No trips to migrate")
                return 0

            self.truncate_new_table('trips')

            insert_sql = """
                INSERT INTO trips (
                    id, bus_id, route_id, start_time, end_time, price,
                    is_active, priority, created_at, updated_at
                ) VALUES (
                    %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
                )
            """

            count = 0
            for row in rows:
                company_route_id = row['company_route_id']

                # Get route_id from mapping
                route_id = self.company_route_to_route.get(company_route_id)
                if route_id is None:
                    print(f"  Warning: company_route_id {company_route_id} not found in mapping, skipping")
                    continue

                # Keep same ID for booking reference
                self.bus_route_to_trip[row['id']] = row['id']

                values = (
                    row['id'],
                    row['bus_id'],
                    route_id,
                    row['start_time'],
                    row['end_time'],
                    row['price'],
                    row['is_active'],
                    row.get('priority', 0),
                    row.get('created_at'),
                    row.get('updated_at')
                )

                self.new_cursor.execute(insert_sql, values)
                count += 1

            self.new_conn.commit()
            print(f"  Migrated {count} trips (from bus_routes)")
            return count

        except Error as e:
            print(f"  Error migrating trips: {e}")
            self.new_conn.rollback()
            return 0

    def migrate_bookings(self):
        """
        Migrate bookings table.
        Rename bus_route_id to trip_id (values stay the same since trip IDs match).
        """
        print("\n[MIGRATING] bookings (bus_route_id -> trip_id)...")

        try:
            self.old_cursor.execute("SELECT * FROM bookings")
            rows = self.old_cursor.fetchall()

            if not rows:
                print("  No bookings to migrate")
                return 0

            self.truncate_new_table('bookings')

            insert_sql = """
                INSERT INTO bookings (
                    id, booking_code, user_id, trip_id, booking_date,
                    customer_name, customer_email, customer_phone,
                    pickup_stop_id, dropoff_stop_id, quantity, total_price,
                    status, payment_method, payment_status, notes,
                    created_at, updated_at
                ) VALUES (
                    %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
                )
            """

            count = 0
            skipped = 0
            for row in rows:
                bus_route_id = row['bus_route_id']

                # Get trip_id (same as bus_route_id in our migration)
                trip_id = self.bus_route_to_trip.get(bus_route_id)
                if trip_id is None:
                    print(f"  Warning: bus_route_id {bus_route_id} not found in mapping, skipping booking {row['id']}")
                    skipped += 1
                    continue

                values = (
                    row['id'],
                    row['booking_code'],
                    row.get('user_id'),
                    trip_id,
                    row['booking_date'],
                    row['customer_name'],
                    row.get('customer_email'),
                    row['customer_phone'],
                    row.get('pickup_stop_id'),
                    row['dropoff_stop_id'],
                    row.get('quantity', 1),
                    row['total_price'],
                    row['status'],
                    row['payment_method'],
                    row['payment_status'],
                    row.get('notes'),
                    row.get('created_at'),
                    row.get('updated_at')
                )

                self.new_cursor.execute(insert_sql, values)
                count += 1

            self.new_conn.commit()
            print(f"  Migrated {count} bookings")
            if skipped > 0:
                print(f"  Skipped {skipped} bookings due to missing trip references")
            return count

        except Error as e:
            print(f"  Error migrating bookings: {e}")
            self.new_conn.rollback()
            return 0

    def run_migration(self):
        """Execute the full migration process"""
        print("=" * 60)
        print("DATABASE MIGRATION: Multi-tenant -> Single-tenant")
        print("=" * 60)
        print(f"From: {OLD_DB_CONFIG['database']}")
        print(f"To:   {NEW_DB_CONFIG['database']}")
        print("=" * 60)

        if not self.connect():
            return False

        try:
            # Step 1: Migrate tables with no changes
            print("\n" + "=" * 60)
            print("STEP 1: Migrate unchanged tables")
            print("=" * 60)

            self.migrate_simple_table('web_profiles')
            self.migrate_simple_table('menus')
            self.migrate_simple_table('provinces')
            self.migrate_simple_table('district_types')
            self.migrate_simple_table('bus_services')
            self.migrate_simple_table('districts')
            self.migrate_simple_table('stops')

            # Also migrate Laravel system tables if they exist
            self.migrate_simple_table('password_reset_tokens')
            self.migrate_simple_table('sessions')

            # Step 2: Migrate users (convert company role)
            print("\n" + "=" * 60)
            print("STEP 2: Migrate users (convert 'company' role)")
            print("=" * 60)

            self.migrate_users()

            # Step 3: Migrate routes with merged company_routes data
            print("\n" + "=" * 60)
            print("STEP 3: Migrate routes (merge company_routes data)")
            print("=" * 60)

            self.migrate_routes_with_company_data()

            # Step 4: Migrate buses without company_id
            print("\n" + "=" * 60)
            print("STEP 4: Migrate buses (remove company_id)")
            print("=" * 60)

            self.migrate_buses_without_company()

            # Step 5: Migrate route_stops from company_route_stops
            print("\n" + "=" * 60)
            print("STEP 5: Migrate company_route_stops -> route_stops")
            print("=" * 60)

            self.migrate_route_stops()

            # Step 6: Migrate trips from bus_routes
            print("\n" + "=" * 60)
            print("STEP 6: Migrate bus_routes -> trips")
            print("=" * 60)

            self.migrate_trips()

            # Step 7: Migrate bookings
            print("\n" + "=" * 60)
            print("STEP 7: Migrate bookings (bus_route_id -> trip_id)")
            print("=" * 60)

            self.migrate_bookings()

            # Step 8: Summary
            print("\n" + "=" * 60)
            print("MIGRATION SUMMARY")
            print("=" * 60)

            summary_tables = [
                'web_profiles', 'menus', 'provinces', 'district_types',
                'bus_services', 'districts', 'stops', 'users',
                'routes', 'buses', 'route_stops', 'trips', 'bookings',
                'password_reset_tokens', 'sessions'
            ]

            print(f"{'Table':<20} {'Old DB':<10} {'New DB':<10}")
            print("-" * 40)

            for table in summary_tables:
                # Handle special cases for renamed/transformed tables
                if table == 'route_stops':
                    old_count = self.get_table_count(self.old_cursor, 'company_route_stops')
                    new_count = self.get_table_count(self.new_cursor, table)
                    print(f"{'company_route_stops -> route_stops':<35} {old_count:<10} {new_count:<10}")
                elif table == 'trips':
                    old_count = self.get_table_count(self.old_cursor, 'bus_routes')
                    new_count = self.get_table_count(self.new_cursor, table)
                    print(f"{'bus_routes -> trips':<35} {old_count:<10} {new_count:<10}")
                else:
                    old_count = self.get_table_count(self.old_cursor, table)
                    new_count = self.get_table_count(self.new_cursor, table)
                    print(f"{table:<35} {old_count:<10} {new_count:<10}")

            print("\n" + "=" * 60)
            print("TABLES REMOVED (data merged or not needed):")
            print("=" * 60)
            print("- companies (multi-tenant data - business info can be added to web_profiles)")
            print("- company_routes (merged into routes)")
            print("- company_route_stops (transformed to route_stops)")
            print("- bus_routes (transformed to trips)")

            print("\n" + "=" * 60)
            print("MIGRATION COMPLETED SUCCESSFULLY!")
            print("=" * 60)

            return True

        except Exception as e:
            print(f"\nMigration failed: {e}")
            return False
        finally:
            self.close()


def verify_migration():
    """Verify migration by checking data integrity"""
    print("\n" + "#" * 60)
    print("# VERIFICATION MODE")
    print("#" * 60)

    try:
        old_conn = mysql.connector.connect(**OLD_DB_CONFIG)
        new_conn = mysql.connector.connect(**NEW_DB_CONFIG)
        old_cursor = old_conn.cursor(dictionary=True)
        new_cursor = new_conn.cursor(dictionary=True)

        print("\nChecking data integrity...")

        # Check critical tables
        checks = [
            ('provinces', 'provinces', 'id'),
            ('districts', 'districts', 'id'),
            ('stops', 'stops', 'id'),
            ('users', 'users', 'id'),
            ('routes', 'routes', 'id'),
            ('buses', 'buses', 'id'),
            ('company_route_stops', 'route_stops', None),  # Count only
            ('bus_routes', 'trips', 'id'),
            ('bookings', 'bookings', 'id'),
        ]

        all_ok = True
        for old_table, new_table, id_col in checks:
            old_cursor.execute(f"SELECT COUNT(*) as cnt FROM `{old_table}`")
            old_count = old_cursor.fetchone()['cnt']

            new_cursor.execute(f"SELECT COUNT(*) as cnt FROM `{new_table}`")
            new_count = new_cursor.fetchone()['cnt']

            status = "✓" if new_count > 0 or old_count == 0 else "✗"
            if status == "✗":
                all_ok = False

            print(f"  {status} {old_table} -> {new_table}: {old_count} -> {new_count}")

        # Check foreign key integrity
        print("\nChecking foreign key integrity...")

        # trips -> buses
        new_cursor.execute("""
            SELECT COUNT(*) as cnt FROM trips t
            LEFT JOIN buses b ON t.bus_id = b.id
            WHERE b.id IS NULL
        """)
        orphan_trips_bus = new_cursor.fetchone()['cnt']
        status = "✓" if orphan_trips_bus == 0 else "✗"
        print(f"  {status} Trips with invalid bus_id: {orphan_trips_bus}")

        # trips -> routes
        new_cursor.execute("""
            SELECT COUNT(*) as cnt FROM trips t
            LEFT JOIN routes r ON t.route_id = r.id
            WHERE r.id IS NULL
        """)
        orphan_trips_route = new_cursor.fetchone()['cnt']
        status = "✓" if orphan_trips_route == 0 else "✗"
        print(f"  {status} Trips with invalid route_id: {orphan_trips_route}")

        # bookings -> trips
        new_cursor.execute("""
            SELECT COUNT(*) as cnt FROM bookings b
            LEFT JOIN trips t ON b.trip_id = t.id
            WHERE t.id IS NULL
        """)
        orphan_bookings = new_cursor.fetchone()['cnt']
        status = "✓" if orphan_bookings == 0 else "✗"
        print(f"  {status} Bookings with invalid trip_id: {orphan_bookings}")

        # route_stops -> routes
        new_cursor.execute("""
            SELECT COUNT(*) as cnt FROM route_stops rs
            LEFT JOIN routes r ON rs.route_id = r.id
            WHERE r.id IS NULL
        """)
        orphan_route_stops = new_cursor.fetchone()['cnt']
        status = "✓" if orphan_route_stops == 0 else "✗"
        print(f"  {status} Route_stops with invalid route_id: {orphan_route_stops}")

        if all_ok and orphan_trips_bus == 0 and orphan_trips_route == 0 and orphan_bookings == 0 and orphan_route_stops == 0:
            print("\n✓ All verification checks passed!")
        else:
            print("\n✗ Some verification checks failed. Please review the data.")

        old_cursor.close()
        new_cursor.close()
        old_conn.close()
        new_conn.close()

        return all_ok

    except Error as e:
        print(f"Verification error: {e}")
        return False


def main():
    """Main entry point"""
    print("\n" + "#" * 60)
    print("# King Express Bus - Database Migration Tool")
    print("#" * 60)

    if len(sys.argv) > 1 and sys.argv[1] == '--verify':
        verify_migration()
        sys.exit(0)

    print("\nThis script will migrate data from the old multi-tenant database")
    print("to the new single-tenant database structure.")
    print("\nOLD Database: db_kingexpressbus_old")
    print("NEW Database: db_kingexpressbus")
    print("\n⚠️  WARNING: This will TRUNCATE all tables in the new database!")
    print("\nOptions:")
    print("  --verify    Only verify existing migration, don't migrate")

    response = input("\nProceed with migration? (yes/no): ").strip().lower()

    if response != 'yes':
        print("Migration cancelled.")
        sys.exit(0)

    migrator = DatabaseMigrator()
    success = migrator.run_migration()

    if success:
        print("\n" + "=" * 60)
        print("Running post-migration verification...")
        print("=" * 60)
        verify_migration()

    sys.exit(0 if success else 1)


if __name__ == '__main__':
    main()
