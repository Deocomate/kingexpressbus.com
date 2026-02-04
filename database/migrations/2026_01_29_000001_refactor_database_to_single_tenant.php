<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Refactor from Multi-tenant to Single-tenant architecture:
     * - Remove companies table
     * - Merge company_routes into routes
     * - Rename bus_routes to trips
     * - Create route_stops from company_route_stops
     * - Update bookings table
     * - Remove company_id from buses
     */
    public function up(): void
    {
        // Step 1: Add new columns to routes table (from company_routes)
        Schema::table('routes', function (Blueprint $table) {
            $table->unsignedBigInteger('price_default')->default(0)->after('distance_km')->comment('Giá mặc định');
            $table->boolean('available_hotel_pickup')->default(false)->after('content')->comment('Cờ bật/tắt đón tại khách sạn');
        });

        // Step 2: Create route_stops table (replace company_route_stops)
        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('stop_id')->constrained('stops')->onDelete('cascade');
            $table->enum('stop_type', ['pickup', 'dropoff', 'both'])->default('both')->comment('Loại điểm dừng');
            $table->integer('priority')->default(0)->comment('Thứ tự ưu tiên');
            $table->timestamps();

            $table->index(['route_id', 'priority']);
        });

        // Step 3: Migrate data from company_route_stops to route_stops
        // Map company_route_id -> route_id through company_routes table
        DB::statement("
            INSERT INTO route_stops (route_id, stop_id, stop_type, priority, created_at, updated_at)
            SELECT DISTINCT
                cr.route_id,
                crs.stop_id,
                crs.stop_type,
                crs.priority,
                NOW(),
                NOW()
            FROM company_route_stops crs
            JOIN company_routes cr ON crs.company_route_id = cr.id
        ");

        // Step 4: Create trips table (rename from bus_routes with new structure)
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedBigInteger('price')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0)->comment('Thứ tự ưu tiên');
            $table->timestamps();

            $table->index(['route_id', 'is_active']);
            $table->index(['start_time']);
        });

        // Step 5: Migrate data from bus_routes to trips
        // Map company_route_id -> route_id through company_routes table
        DB::statement("
            INSERT INTO trips (id, bus_id, route_id, start_time, end_time, price, is_active, priority, created_at, updated_at)
            SELECT
                br.id,
                br.bus_id,
                cr.route_id,
                br.start_time,
                br.end_time,
                br.price,
                br.is_active,
                br.priority,
                br.created_at,
                br.updated_at
            FROM bus_routes br
            JOIN company_routes cr ON br.company_route_id = cr.id
        ");

        // Step 6: Update bookings table - rename bus_route_id to trip_id
        Schema::table('bookings', function (Blueprint $table) {
            // First drop the foreign key
            $table->dropForeign(['bus_route_id']);
            // Rename column
            $table->renameColumn('bus_route_id', 'trip_id');
        });

        // Add foreign key to trips table
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('trip_id')->references('id')->on('trips')->onDelete('cascade');
        });

        // Step 7: Update buses table - remove company_id
        Schema::table('buses', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        // Step 8: Drop old tables (order matters due to foreign keys)
        Schema::dropIfExists('bus_routes');
        Schema::dropIfExists('company_route_stops');
        Schema::dropIfExists('company_routes');
        Schema::dropIfExists('companies');

        // Step 9: Update users table - remove company role references if needed
        // The role column still exists but 'company' role is no longer valid
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate companies table
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('name', 1000);
            $table->string('slug')->unique();
            $table->string('title', 1000)->nullable();
            $table->string('description', 1000)->nullable();
            $table->string('thumbnail_url', 1000)->nullable();
            $table->json('image_list_url')->nullable();
            $table->longText('content')->nullable();
            $table->string('phone', 1000)->nullable();
            $table->string('hotline', 1000)->nullable();
            $table->string('email', 1000)->nullable();
            $table->string('address', 1000)->nullable();
            $table->integer('priority')->default(0);
            $table->timestamps();
        });

        // Recreate company_routes table
        Schema::create('company_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->string('name', 1000);
            $table->string('slug')->unique();
            $table->string('title', 1000)->nullable();
            $table->string('description', 1000)->nullable();
            $table->string('duration', 1000)->nullable();
            $table->integer('distance_km')->nullable();
            $table->string('thumbnail_url', 1000)->nullable();
            $table->json('image_list_url')->nullable();
            $table->longText('content')->nullable();
            $table->boolean('available_hotel_pickup')->default(false);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });

        // Recreate company_route_stops table
        Schema::create('company_route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_route_id')->constrained('company_routes')->onDelete('cascade');
            $table->foreignId('stop_id')->constrained('stops')->onDelete('cascade');
            $table->enum('stop_type', ['pickup', 'dropoff', 'both'])->default('both');
            $table->integer('priority')->default(0);
        });

        // Add company_id back to buses
        Schema::table('buses', function (Blueprint $table) {
            $table->foreignId('company_id')->after('id')->constrained('companies')->onDelete('cascade');
        });

        // Recreate bus_routes table
        Schema::create('bus_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade');
            $table->foreignId('company_route_id')->constrained('company_routes')->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedBigInteger('price')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });

        // Revert bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['trip_id']);
            $table->renameColumn('trip_id', 'bus_route_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('bus_route_id')->references('id')->on('bus_routes')->onDelete('cascade');
        });

        // Drop new tables
        Schema::dropIfExists('trips');
        Schema::dropIfExists('route_stops');

        // Remove new columns from routes
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn(['price_default', 'available_hotel_pickup']);
        });
    }
};
