// Migration: 2025_02_04_004_fix_foreign_key_cascade.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Drop foreign keys yang salah
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['priority_id']);
        });

        Schema::table('voucher_requests', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        // Re-add foreign keys dengan ON DELETE RESTRICT
        Schema::table('tickets', function (Blueprint $table) {
            // user_id
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            // category_id
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            // priority_id
            $table->foreign('priority_id')
                ->references('id')
                ->on('priorities')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });

        Schema::table('voucher_requests', function (Blueprint $table) {
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        // Restore original foreign keys (berbahaya, tapi untuk rollback)
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['category_id']);
            $table->dropForeign(['priority_id']);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table->foreign('priority_id')
                ->references('id')
                ->on('priorities')
                ->onDelete('cascade');
        });

        Schema::table('voucher_requests', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            // Original tidak ada ON DELETE clause, jadi biarkan default
            $table->foreign('created_by')
                ->references('id')
                ->on('users');
        });
    }
};
