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

        Schema::create('holiday_surcharge_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('holiday_surcharge_id')
                ->constrained('holiday_surcharges')
                ->onDelete('cascade');
            $table->foreignId('route_id')
                ->constrained('routes')
                ->onDelete('cascade');
            $table->unsignedBigInteger('route_surcharge_amount')->default(0);
            $table->timestamps();

            $table->unique(['holiday_surcharge_id', 'route_id'], 'uniq_holiday_surcharge_route');
            $table->index('route_id', 'idx_holiday_surcharge_routes_route_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holiday_surcharge_routes');
        Schema::dropIfExists('holiday_surcharges');
    }
};
