// database/migrations/2025_09_22_152829_create_database_tables.php
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
        // 1. Bảng lưu thông tin chung của website
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

        // 2. Bảng quản lý menu (Đã tối ưu cho Filament Tree)
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1000);
            $table->string('url', 1000)->nullable();
            $table->bigInteger('parent_id')->nullable()->default(-1)->comment('Mặc định -1 là ROOT_PARENT_ID');
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
            $table->string('type', 1000)->default('custom_link')->comment('Loại menu: custom_link, route, page...');
            $table->unsignedBigInteger('related_id')->nullable()->comment('ID liên kết với type (vd: route_id)');
            $table->timestamps();
        });

        // 3. Bảng tỉnh/thành phố
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

        // 4. Bảng loại địa điểm: Bến xe, điểm đón trả, văn phòng...
        Schema::create('district_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1000);
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
        });

        // 5. Bảng dịch vụ tiện ích trên xe
        Schema::create('bus_services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('icon', 100)->nullable()->comment('Font Awesome icon class, e.g., fas fa-wifi');
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
        });

        // 6. Bảng quận/huyện hoặc các địa điểm lớn
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

        // 7. Bảng các điểm dừng/đón trả cụ thể
        Schema::create('stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->onDelete('cascade');
            $table->string('name', 1000)->comment('Tên địa điểm cụ thể, vd: 123 Nguyễn Trãi');
            $table->string('address', 1000);
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
            $table->timestamps();
        });

        // 8. Bảng tuyến đường
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

        // 9. Bảng phụ phí lễ tết
        Schema::create('holiday_surcharges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('reason')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('global_surcharge_amount')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'start_date', 'end_date'], 'idx_holiday_surcharges_active_date_range');
            $table->index(['priority', 'start_date'], 'idx_holiday_surcharges_priority_start');
        });

        // 10. Bảng tuyến đường áp dụng phụ phí lễ tết
        Schema::create('holiday_surcharge_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('holiday_surcharge_id')->constrained('holiday_surcharges')->cascadeOnDelete();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->unsignedBigInteger('route_surcharge_amount')->default(0);
            $table->timestamps();

            $table->unique(['holiday_surcharge_id', 'route_id'], 'uniq_holiday_surcharge_route');
            $table->index('route_id', 'idx_holiday_surcharge_routes_route_id');
        });

        // 11. Bảng thông tin các xe (Đã gỡ bỏ seat_map và services)
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1000)->comment('Tên xe hoặc tên gợi nhớ');
            $table->string('model_name', 1000)->nullable()->comment('Dòng xe');
            $table->integer('seat_count')->comment('Số ghế');
            $table->string('thumbnail_url', 1000)->nullable();
            $table->json('image_list_url')->nullable();
            $table->longText('content')->nullable();
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
            $table->timestamps();
        });

        // 12. Bảng trung gian Xe - Dịch vụ
        Schema::create('bus_bus_service', function (Blueprint $table) {
            $table->foreignId('bus_id')->constrained('buses')->cascadeOnDelete();
            $table->foreignId('bus_service_id')->constrained('bus_services')->cascadeOnDelete();

            $table->unique(['bus_id', 'bus_service_id']);
        });

        // 13. Bảng các điểm dừng cho tuyến đường
        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('stop_id')->constrained('stops')->onDelete('cascade');
            $table->enum('stop_type', ['pickup', 'dropoff', 'both'])->default('both')->comment('Loại điểm dừng: đón, trả, cả hai');
            $table->integer('priority')->default(0)->comment('Số priority càng lớn thì độ ưu tiên càng cao');
            $table->timestamps();

            $table->index(['route_id', 'priority']);
        });

        // 14. Bảng chuyến xe
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

        // 15. Bảng chặn ngày xe chạy (Block trips)
        Schema::create('trip_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('block_type')->comment('off_day, sold_out');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['trip_id', 'start_date', 'end_date']);
        });

        // 16. Bảng đặt vé (Đã gộp tất cả cột mở rộng)
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

            // Pricing Fields
            $table->unsignedBigInteger('base_unit_price')->default(0);
            $table->unsignedBigInteger('global_surcharge_unit')->default(0);
            $table->unsignedBigInteger('route_surcharge_unit')->default(0);
            $table->unsignedBigInteger('final_unit_price')->default(0);
            $table->unsignedBigInteger('total_surcharge_amount')->default(0);
            $table->unsignedBigInteger('total_price');
            $table->text('surcharge_reason_snapshot')->nullable();

            // Status & Payment Fields
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->enum('payment_method', ['online_banking', 'cash_on_pickup'])->default('cash_on_pickup');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->string('payment_transaction_id')->nullable();
            $table->json('payment_log')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop theo thứ tự ngược lại để tránh lỗi Foreign Key
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('trip_blocks');
        Schema::dropIfExists('trips');
        Schema::dropIfExists('route_stops');
        Schema::dropIfExists('bus_bus_service');
        Schema::dropIfExists('buses');
        Schema::dropIfExists('holiday_surcharge_routes');
        Schema::dropIfExists('holiday_surcharges');
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
