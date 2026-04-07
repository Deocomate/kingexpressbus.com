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
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('base_unit_price')->default(0);
            $table->unsignedBigInteger('global_surcharge_unit')->default(0);
            $table->unsignedBigInteger('route_surcharge_unit')->default(0);
            $table->unsignedBigInteger('final_unit_price')->default(0);
            $table->unsignedBigInteger('total_surcharge_amount')->default(0);
            $table->text('surcharge_reason_snapshot')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'base_unit_price',
                'global_surcharge_unit',
                'route_surcharge_unit',
                'final_unit_price',
                'total_surcharge_amount',
                'surcharge_reason_snapshot',
            ]);
        });
    }
};
