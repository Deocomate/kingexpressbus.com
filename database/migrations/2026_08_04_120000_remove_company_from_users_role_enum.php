<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove vestigial 'company' value from users.role enum.
     *
     * Pre-condition verified on local DB: zero users have role = 'company'.
     * Must re-verify on production before running:
     *   SELECT id, email FROM users WHERE role = 'company';
     */
    public function up(): void
    {
        // Guard: fail loudly rather than silently orphan data if company users exist.
        $companyCount = DB::table('users')->where('role', 'company')->count();
        if ($companyCount > 0) {
            throw new RuntimeException(
                "Cannot remove 'company' from users.role enum: {$companyCount} user(s) still have role='company'. Reassign them first."
            );
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'customer', 'guest'])->default('customer')->change();
        });
    }

    /**
     * Restore the original 4-value enum.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'company', 'customer', 'guest'])->default('customer')->change();
        });
    }
};
