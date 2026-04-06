// Migration: 2025_02_04_008_add_is_followup_to_ticket_comments.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('ticket_comments', function (Blueprint $table) {
            $table->boolean('is_followup')
                ->default(false)
                ->after('is_internal')
                ->comment('Mark if comment requires follow-up action');
        });
    }

    public function down()
    {
        Schema::table('ticket_comments', function (Blueprint $table) {
            $table->dropColumn('is_followup');
        });
    }
};
