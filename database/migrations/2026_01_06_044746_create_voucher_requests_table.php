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
        Schema::create('voucher_requests', function (Blueprint $table) {
            $table->id();
            $table->string('vr_number', 50)->unique(); // VR-2026-001
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');

            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('vr_number');
            $table->index('ticket_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_requests');
    }
};
