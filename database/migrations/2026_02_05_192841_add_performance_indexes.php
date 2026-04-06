// Migration: 2025_02_04_005_add_performance_indexes.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // activity_logs
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index(['ticket_id', 'created_at']);
            $table->index('created_at');
        });

        // notifications
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'is_read', 'created_at']);
        });

        // tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->index(['status', 'priority_id', 'created_at']);
            $table->index(['assigned_to', 'status']);
        });

        // ticket_comments
        Schema::table('ticket_comments', function (Blueprint $table) {
            $table->index(['ticket_id', 'created_at']);
        });

        // voucher_requests
        Schema::table('voucher_requests', function (Blueprint $table) {
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        // activity_logs
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['ticket_id', 'created_at']);
            $table->dropIndex(['created_at']);
        });

        // notifications
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_read', 'created_at']);
        });

        // tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['status', 'priority_id', 'created_at']);
            $table->dropIndex(['assigned_to', 'status']);
        });

        // ticket_comments
        Schema::table('ticket_comments', function (Blueprint $table) {
            $table->dropIndex(['ticket_id', 'created_at']);
        });

        // voucher_requests
        Schema::table('voucher_requests', function (Blueprint $table) {
            $table->dropIndex(['status', 'created_at']);
        });
    }
};
