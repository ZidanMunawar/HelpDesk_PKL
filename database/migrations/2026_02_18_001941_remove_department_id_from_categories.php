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
        // Hapus foreign key constraint terlebih dahulu
        Schema::table('categories', function (Blueprint $table) {
            // Hapus foreign key jika ada
            $table->dropForeign(['department_id']);
        });

        // Kemudian hapus kolom department_id
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tambahkan kembali kolom department_id
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('description');

            // Tambahkan kembali foreign key constraint
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null');
        });
    }
};
