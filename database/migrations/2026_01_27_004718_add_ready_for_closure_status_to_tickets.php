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
        // Update enum status di tabel tickets
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM(
            'open',
            'received',
            'pending_om',
            'in_progress',
            'pending_vr',
            'completed',
            'pending_gm',
            'ready_for_closure',
            'closed',
            'cancelled'
        ) NOT NULL DEFAULT 'open'");

        // Update juga di ticket_approvals jika ada kolom status yang sama
        try {
            DB::statement("ALTER TABLE ticket_approvals MODIFY COLUMN status ENUM(
                'pending',
                'approved',
                'rejected'
            ) NOT NULL DEFAULT 'pending'");
        } catch (\Exception $e) {
            // Ignore jika kolom tidak ada atau tidak perlu diubah
        }

        // Update stage mapping (opsional - untuk referensi)
        // current_stage sudah 1-9, jadi cukup update mapping di controller saja
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke enum sebelumnya (tanpa ready_for_closure)
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM(
            'open',
            'received',
            'pending_om',
            'in_progress',
            'pending_vr',
            'completed',
            'pending_gm',
            'closed',
            'cancelled'
        ) NOT NULL DEFAULT 'open'");
    }
};
