<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update role enum di users table
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'manager', 'gm', 'om', 'technician', 'user') NOT NULL DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback ke enum original
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user'");
    }
};
