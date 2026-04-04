<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bảng lưu thông tin chung của website
        Schema::create('web_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('profile_name', 1000)->comment('Tên định danh cho cấu hình');
            $table->boolean('is_default')->default(false)->comment('Cấu hình mặc định đang được sử dụng');
            $table->string('title', 1000)->nullable()->comment('Tiêu đề website (SEO)');
            $table->string('description', 1000)->nullable()->comment('Mô tả website (SEO)');
            $table->string('logo_url', 1000)->nullable();
            $table->string('favicon_url', 1000)->nullable();
            $table->string('email', 1000)->nullable();
            $table->string('phone', 1000)->nullable();
            $table->string('hotline', 1000)->nullable();
            $table->string('whatsapp', 1000)->nullable();
            $table->string('address', 1000)->nullable();
            $table->string('facebook_url', 1000)->nullable();
            $table->string('zalo_url', 1000)->nullable();
            $table->longText('map_embedded')->nullable()->comment('Mã nhúng Google Maps');
            $table->longText('policy_content')->nullable()->comment('Nội dung chính sách');
            $table->longText('introduction_content')->nullable()->comment('Nội dung giới thiệu');
            $table->timestamps();
            $table->index('is_default');
        });

        // Bảng quản lý menu
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1000);
            $table->string('url', 1000)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
            $table->string('type', 1000)->default('custom_link')->comment('Loại menu: custom_link, route, page...');
            $table->unsignedBigInteger('related_id')->nullable()->comment('ID liên kết với type (vd: route_id)');
            $table->timestamps();
            $table->foreign('parent_id')->references('id')->on('menus')->onDelete('cascade');
        });

        // Bảng tỉnh/thành phố
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1000);
            $table->string('slug')->unique();
            $table->string('title', 1000)->nullable()->comment('Tiêu đề (SEO)');
            $table->string('description', 1000)->nullable()->comment('Mô tả (SEO)');
            $table->string('thumbnail_url', 1000)->nullable();
            $table->json('image_list_url')->nullable();
            $table->longText('content')->nullable();
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
            $table->timestamps();
        });

        // Bảng loại địa điểm: Bến xe, điểm đón trả, văn phòng...
        Schema::create('district_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1000);
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
        });

        // Bảng dịch vụ tiện ích trên xe
        Schema::create('bus_services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('icon', 100)->nullable()->comment('Font Awesome icon class, e.g., fas fa-wifi');
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
        });

        // Bảng quận/huyện hoặc các địa điểm lớn
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->onDelete('cascade');
            $table->foreignId('district_type_id')->constrained('district_types')->onDelete('cascade');
            $table->string('name', 1000);
            $table->string('slug')->unique();
            $table->string('title', 1000)->nullable()->comment('Tiêu đề (SEO)');
            $table->string('description', 1000)->nullable()->comment('Mô tả (SEO)');
            $table->string('thumbnail_url', 1000)->nullable();
            $table->json('image_list_url')->nullable();
            $table->longText('content')->nullable();
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
            $table->timestamps();
        });

        // Bảng các điểm dừng/đón trả cụ thể
        Schema::create('stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
            $table->string('name', 1000)->comment('Tên địa điểm cụ thể, vd: 123 Nguyễn Trãi');
            $table->string('address', 1000);
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
            $table->timestamps();
        });

        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_start_id')->constrained('provinces')->onDelete('cascade');
            $table->foreignId('province_end_id')->constrained('provinces')->onDelete('cascade');
            $table->string('name', 1000)->comment('Tên tuyến đường, vd: Tuyến Hà Nội - Lào Cai');
            $table->string('slug')->unique();
            $table->string('title', 1000)->nullable()->comment('Tiêu đề (SEO)');
            $table->string('description', 1000)->nullable()->comment('Mô tả (SEO)');
            $table->string('duration', 1000)->nullable()->comment('Thời gian di chuyển dự kiến');
            $table->integer('distance_km')->nullable()->comment('Khoảng cách dự kiến');
            $table->unsignedBigInteger('price_default')->default(0)->comment('Giá mặc định');
            $table->string('thumbnail_url', 1000)->nullable();
            $table->json('image_list_url')->nullable();
            $table->longText('content')->nullable();
            $table->boolean('available_hotel_pickup')->default(false)->comment('Cờ bật/tắt đón tại khách sạn');
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
            $table->timestamps();
        });

        // Bảng thông tin các xe
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1000)->comment('Tên xe hoặc tên gợi nhớ');
            $table->string('model_name', 1000)->nullable()->comment('Dòng xe');
            $table->integer('seat_count')->comment('Số ghế');
            $table->json('seat_map')->nullable()->comment('Sơ đồ ghế ngồi');
            $table->json('services')->nullable()->comment('Các dịch vụ: wifi, nước uống...');
            $table->string('thumbnail_url', 1000)->nullable();
            $table->json('image_list_url')->nullable();
            $table->longText('content')->nullable();
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
            $table->timestamps();
        });

        // Bảng các điểm dừng cho tuyến đường
        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('stop_id')->constrained('stops')->onDelete('cascade');
            $table->enum('stop_type', ['pickup', 'dropoff', 'both'])->default('both')->comment('Loại điểm dừng: đón, trả, cả hai');
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
            $table->timestamps();

            $table->index(['route_id', 'priority']);
        });

        // Bảng chuyến xe: Gắn một xe cụ thể với một tuyến đường, có giờ chạy và giá vé
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedBigInteger('price')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
            $table->timestamps();

            $table->index(['route_id', 'is_active']);
            $table->index(['start_time']);
        });

        // Bảng đặt vé
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Null nếu là khách vãng lai');
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->date('booking_date');
            $table->string('customer_name', 1000);
            $table->string('customer_email', 1000)->nullable();
            $table->string('customer_phone', 1000);
            $table->foreignId('pickup_stop_id')->nullable()->constrained('stops')->onDelete('cascade');
            $table->foreignId('dropoff_stop_id')->constrained('stops')->onDelete('cascade');
            $table->integer('quantity')->default(1)->comment("Số lượng vé đặt");
            $table->unsignedBigInteger('total_price');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->enum('payment_method', ['online_banking', 'cash_on_pickup'])->default('cash_on_pickup');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('trips');
        Schema::dropIfExists('route_stops');
        Schema::dropIfExists('buses');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('stops');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('bus_services');
        Schema::dropIfExists('district_types');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('web_profiles');
    }
};
