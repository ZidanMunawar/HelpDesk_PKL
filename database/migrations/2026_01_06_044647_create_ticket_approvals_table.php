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
        Schema::create('ticket_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');

            // Manager Approval
            $table->boolean('manager_approved')->default(false);
            $table->foreignId('manager_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('manager_approved_at')->nullable();

            // GM Approval
            $table->boolean('gm_approved')->default(false);
            $table->foreignId('gm_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('gm_approved_at')->nullable();

            // OM Approval
            $table->boolean('om_approved')->default(false);
            $table->foreignId('om_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('om_approved_at')->nullable();

            // Admin Check
            $table->boolean('admin_check')->default(false);
            $table->foreignId('admin_checked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('admin_checked_at')->nullable();

            // Rejection
            $table->text('rejection_reason')->nullable();

            // Status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();

            // Index untuk performa
            $table->index('ticket_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_approvals');
    }
};
