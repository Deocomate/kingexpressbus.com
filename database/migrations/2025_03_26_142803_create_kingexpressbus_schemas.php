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
            $table->string('logo');
            $table->string('title');
            $table->text('description');
            $table->string('email');
            $table->string('phone');
            $table->string('hotline');
            $table->string('phone_detail');
            $table->string('web_link');
            $table->string('facebook');
            $table->string('zalo');
            $table->string('address');
            $table->text('map');
            $table->longText('policy');
            $table->text('detail');
            $table->timestamps();
        });

        // Create menus table
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->integer('priority')->default(0);
            $table->unsignedBigInteger('parent_id');
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('menus')
                ->onDelete("cascade");
        });

        // Create provinces table
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['thanhpho', 'tinh']);
            $table->string('title');
            $table->string('description');
            $table->string('thumbnail');
            $table->json('images');
            $table->longText('detail');
            $table->integer('priority')->default(0);
            $table->string('slug');
            $table->timestamps();
        });

        // Create districts table
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('province_id');
            $table->string('name');
            $table->enum('type', ['quan', 'huyen', 'thanhpho', 'thixa', 'benxe', 'sanbay', 'diadiemdulich']);
            $table->string('title');
            $table->string('description');
            $table->string('thumbnail');
            $table->json('images');
            $table->longText('detail');
            $table->integer('priority')->default(0);
            $table->string('slug');
            $table->timestamps();

            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->onDelete('cascade');
        });

        // Create routes table
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('province_id_start');
            $table->unsignedBigInteger('province_id_end');
            $table->string('title');
            $table->string('description');
            $table->string('thumbnail');
            $table->json('images');
            $table->integer('distance');
            $table->string('duration');
            $table->integer('start_price');
            $table->longText('detail');
            $table->integer('priority')->default(0);
            $table->string('slug');
            $table->timestamps();

            $table->foreign('province_id_start')
                ->references('id')
                ->on('provinces')
                ->onDelete('cascade');

            $table->foreign('province_id_end')
                ->references('id')
                ->on('provinces')
                ->onDelete('cascade');
        });

        // Create buses table
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('thumbnail');
            $table->json('images');
            $table->string('name');
            $table->string('model_name');
            $table->enum('type', ['sleeper', 'cabin', 'doublecabin', 'limousine']);
            $table->integer('number_of_seats');
            $table->json('services');
            $table->integer('floors');
            $table->integer('seat_row_number');
            $table->integer('seat_column_number');
            $table->longText('detail');
            $table->integer('priority')->default(0);
            $table->string('slug');
            $table->timestamps();
        });

        // Create bus_routes table
        Schema::create('bus_routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_id');
            $table->unsignedBigInteger('route_id');
            $table->string('title');
            $table->string('description');
            $table->time('start_at');
            $table->time('end_at');
            $table->text('detail');
            $table->integer('priority')->default(0);
            $table->string('slug');
            $table->timestamps();

            $table->foreign('bus_id')
                ->references('id')
                ->on('buses')
                ->onDelete('cascade');

            $table->foreign('route_id')
                ->references('id')
                ->on('routes')
                ->onDelete('cascade');
        });

        // Create stops table
        Schema::create('stops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_route_id');
            $table->unsignedBigInteger('district_id');
            $table->string('title');
            $table->time('stop_at');

            $table->foreign('bus_route_id')
                ->references('id')
                ->on('bus_routes')
                ->onDelete('cascade');

            $table->foreign('district_id')
                ->references('id')
                ->on('districts')
                ->onDelete('cascade');
        });

        // Create customers table
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fullname');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('address');
            $table->string('password');
            $table->boolean('is_registered')->default(false);
            $table->timestamps();
        });

        // Create bookings table
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id');
            $table->unsignedBigInteger('bus_route_id');
            $table->date('booking_date');
            $table->json('seats');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->enum('payment_method', ['online', 'offline'])->default('offline');
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->timestamps();

            $table->foreign('customer_id')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->foreign('bus_route_id')
                ->references('id')
                ->on('bus_routes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
