// Migration: 2025_02_04_001_remove_updated_at_from_immutable_tables.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // activity_logs
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });

        // comment_attachments
        Schema::table('comment_attachments', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });

        // ticket_attachments
        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });

        // signatures
        Schema::table('signatures', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }

    public function down()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });

        Schema::table('comment_attachments', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });

        Schema::table('ticket_attachments', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });

        Schema::table('signatures', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });
    }
};
