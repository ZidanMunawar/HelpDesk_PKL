// Migration: 2025_02_04_003_fix_activity_logs_timestamps.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Update existing NULL values to current timestamp
        DB::table('activity_logs')
            ->whereNull('created_at')
            ->update(['created_at' => now()]);

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'))->change();
        });
    }

    public function down()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->default(null)->change();
        });
    }
};
