<?php

use App\Support\PriorityOrder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        PriorityOrder::invertStoredPriorities('route_stops', 'route_id');
        PriorityOrder::invertStoredPriorities('menus', 'parent_id');
    }

    public function down(): void
    {
        throw new RuntimeException('Irreversible data migration: priority inversion cannot be safely reversed. Restore from a database backup if needed.');
    }
};
