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
        Schema::table('departments', function (Blueprint $table) {
            // Kolom untuk menandai department mana yang bisa akses menu manager
            $table->boolean('has_manager_access')->default(false)->after('status');
            // Atau bisa pakai nama: is_engineering
            // $table->boolean('is_engineering')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('has_manager_access');
            // $table->dropColumn('is_engineering');
        });
    }
};
