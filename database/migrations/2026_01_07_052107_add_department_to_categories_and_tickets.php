<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Categories dapat department_id
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('description');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });

        // Tickets dapat department_id (auto dari category)
        Schema::table('tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('category_id');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
};
