// Migration: 2025_02_04_007_change_notifications_type_to_enum.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Ubah dari varchar ke enum
            $table->enum('type', [
                'info', 'success', 'warning', 'error',
                'approval', 'assignment', 'check', 'rejection',
                'vr_request', 'closure', 'comment'
            ])->default('info')->change();
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('type', 255)->default('info')->change();
        });
    }
};
