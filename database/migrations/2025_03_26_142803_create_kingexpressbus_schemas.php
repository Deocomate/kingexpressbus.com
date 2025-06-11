<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create web_info table
        Schema::create('web_info', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('hotline')->nullable();
            $table->string('phone_detail')->nullable();
            $table->string('web_link')->nullable();
            $table->string('facebook')->nullable();
            $table->string('zalo')->nullable();
            $table->string('address')->nullable();
            $table->text('map')->nullable();
            $table->longText('policy')->nullable();
            $table->text('detail')->nullable();
            $table->nullableTimestamps();
        });

        // Create menus table
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('url')->nullable();
            $table->integer('priority')->default(0)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->nullableTimestamps();
            $table->foreign('parent_id')->references('id')->on('menus')->onDelete("cascade");
        });

        // Create provinces table
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->enum('type', ['thanhpho', 'tinh'])->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable();
            $table->longText('detail')->nullable();
            $table->integer('priority')->default(0)->nullable();
            $table->string('slug')->nullable();
            $table->nullableTimestamps();
        });

        // Create districts table
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->string('name')->nullable();
            $table->enum('type', ['quan', 'huyen', 'thanhpho', 'thixa', 'benxe', 'sanbay', 'diadiemdulich'])->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable();
            $table->longText('detail')->nullable();
            $table->integer('priority')->default(0)->nullable();
            $table->string('slug')->nullable();
            $table->nullableTimestamps();
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
        });

        // Create routes table
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('province_id_start')->nullable();
            $table->unsignedBigInteger('province_id_end')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable();
            $table->integer('distance')->nullable();
            $table->string('duration')->nullable();
            $table->integer('start_price')->nullable();
            $table->longText('detail')->nullable();
            $table->integer('priority')->default(0)->nullable();
            $table->string('slug')->nullable();
            $table->nullableTimestamps();
            $table->foreign('province_id_start')->references('id')->on('provinces')->onDelete('cascade');
            $table->foreign('province_id_end')->references('id')->on('provinces')->onDelete('cascade');
        });

        // Create buses table
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable();
            $table->string('name')->nullable();
            $table->string('model_name')->nullable();
            $table->enum('type', ['sleeper', 'cabin', 'doublecabin', 'limousine'])->nullable();
            $table->integer('number_of_seats')->nullable();
            $table->json('services')->nullable();
            $table->integer('floors')->nullable();
            $table->integer('seat_row_number')->nullable();
            $table->integer('seat_column_number')->nullable();
            $table->longText('detail')->nullable();
            $table->integer('priority')->default(0)->nullable();
            $table->string('slug')->nullable();
            $table->nullableTimestamps();
        });

        // Create stops table (REVISED)
        Schema::create('stops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route_id')->nullable(); // Foreign key to routes table
            $table->unsignedBigInteger('district_id')->nullable();
            $table->string('title')->nullable(); // e.g., "Văn phòng ABC", "Ngã tư Sở"
            $table->nullableTimestamps();

            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
        });

        // Create bus_routes table
        Schema::create('bus_routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_id')->nullable();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->time('start_at')->nullable();
            $table->time('end_at')->nullable();
            $table->unsignedBigInteger('price')->default(0)->nullable();
            $table->text('detail')->nullable();
            $table->integer('priority')->default(0)->nullable();
            $table->string('slug')->nullable();
            $table->nullableTimestamps();
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
        });

        // Create customers table
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fullname')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('password')->nullable();
            $table->boolean('is_registered')->default(false)->nullable();
            $table->nullableTimestamps();
        });

        // Create bookings table
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedBigInteger('bus_route_id')->nullable();
            $table->date('booking_date')->nullable();
            $table->json('seats')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending')->nullable();
            $table->enum('payment_method', ['online', 'offline'])->default('offline')->nullable();
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid')->nullable();
            $table->nullableTimestamps();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('bus_route_id')->references('id')->on('bus_routes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('stops');
        Schema::dropIfExists('bus_routes');
        Schema::dropIfExists('buses');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('web_info');
    }
};
