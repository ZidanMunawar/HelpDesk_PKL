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
        // Drop code column from categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique('categories_code_unique'); // Drop unique constraint first
            $table->dropColumn('code');
        });

        // Drop code column from departments table
        Schema::table('departments', function (Blueprint $table) {
            $table->dropUnique('departments_code_unique'); // Drop unique constraint first
            $table->dropColumn('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add code column back to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('code')->unique()->after('name');
        });

        // Add code column back to departments table
        Schema::table('departments', function (Blueprint $table) {
            $table->string('code', 50)->unique()->after('name');
        });
    }
};
