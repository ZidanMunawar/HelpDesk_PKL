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
        Schema::table('ticket_comments', function (Blueprint $table) {
            // Tambah composite index untuk filtering follow-up comments
            $table->index(['ticket_id', 'is_followup'], 'ticket_comments_ticket_id_is_followup_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_comments', function (Blueprint $table) {
            // Drop index saat rollback
            $table->dropIndex('ticket_comments_ticket_id_is_followup_index');
        });
    }
};
