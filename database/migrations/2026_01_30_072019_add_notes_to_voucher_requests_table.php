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
        Schema::table('voucher_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('voucher_requests', 'notes')) {
                $table->text('notes')->nullable()->after('total_amount');
            }

            // Optional: tambah kolom rejection_reason jika belum ada
            if (!Schema::hasColumn('voucher_requests', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('gm_approved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_requests', function (Blueprint $table) {
            if (Schema::hasColumn('voucher_requests', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('voucher_requests', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
