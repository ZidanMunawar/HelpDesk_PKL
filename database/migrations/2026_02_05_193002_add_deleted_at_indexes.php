// Migration: 2025_02_04_010_add_deleted_at_indexes.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        $tablesWithSoftDeletes = [
            'users',
            'categories',
            'departments',
            'priorities',
            'locations',
            'tickets',
            'ticket_comments',
            'voucher_requests'
        ];

        foreach ($tablesWithSoftDeletes as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->index('deleted_at');
            });
        }
    }

    public function down()
    {
        $tablesWithSoftDeletes = [
            'users',
            'categories',
            'departments',
            'priorities',
            'locations',
            'tickets',
            'ticket_comments',
            'voucher_requests'
        ];

        foreach ($tablesWithSoftDeletes as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex(['deleted_at']);
            });
        }
    }
};
