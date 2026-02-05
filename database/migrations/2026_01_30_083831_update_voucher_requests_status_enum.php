<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus semua komentar dari SQL statement
        DB::statement("ALTER TABLE voucher_requests
            MODIFY status ENUM(
                'pending',
                'admin_approved',
                'om_approved',
                'gm_approved',
                'paid',
                'rejected'
            ) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke status lama
        DB::statement("ALTER TABLE voucher_requests
            MODIFY status ENUM('pending', 'approved', 'rejected', 'paid') NOT NULL DEFAULT 'pending'");
    }
};
