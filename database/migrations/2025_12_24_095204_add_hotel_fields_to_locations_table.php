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
        Schema::table('locations', function (Blueprint $table) {
            $table->enum('location_type', ['room', 'floor', 'department', 'facility', 'area'])->default('area')->after('name');
            $table->foreignId('parent_id')->nullable()->after('location_type')->constrained('locations')->onDelete('set null');
            $table->string('floor_number', 10)->nullable()->after('parent_id');
            $table->string('room_number', 20)->nullable()->after('floor_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['location_type', 'parent_id', 'floor_number', 'room_number']);
        });
    }
};
