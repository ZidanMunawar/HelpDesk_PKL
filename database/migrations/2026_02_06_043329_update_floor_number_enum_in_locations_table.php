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
        // Ubah floor_number ENUM - hapus '4', tambahin '3' kalau belum ada
        DB::statement("ALTER TABLE `locations` MODIFY `floor_number` ENUM('GF','M','3','3A','5','6','7','8','9') DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback ke ENUM original
        DB::statement("ALTER TABLE `locations` MODIFY `floor_number` ENUM('GF','M','3A','4','5','6','7','8','9') DEFAULT NULL");
    }
};
