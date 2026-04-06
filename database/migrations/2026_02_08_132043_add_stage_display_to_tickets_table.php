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
        Schema::table('tickets', function (Blueprint $table) {
            // Add stage_display column
            $table->string('stage_display', 100)->nullable()->after('current_stage');

            // Add index for better performance
            $table->index(['status', 'current_stage']);
        });

        // Update existing records with initial stage_display values
        DB::statement("
            UPDATE tickets
            SET stage_display = CASE
                WHEN current_stage = 1 THEN 'Requested by User (Open)'
                WHEN current_stage = 2 THEN 'Received by Admin Engineering'
                WHEN current_stage = 3 THEN 'OM Approval'
                WHEN current_stage = 4 THEN 'In Progress / Technician Working'
                WHEN current_stage = 5 THEN 'Waiting VR Approval'
                WHEN current_stage = 6 THEN 'Completed by Technician'
                WHEN current_stage = 7 THEN 'User Check Done - Waiting GM'
                WHEN current_stage = 8 THEN 'GM Approved - Ready for Closure'
                WHEN current_stage = 9 THEN 'Closed by Admin'
                ELSE 'Unknown'
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['status', 'current_stage']);
            $table->dropColumn('stage_display');
        });
    }
};
