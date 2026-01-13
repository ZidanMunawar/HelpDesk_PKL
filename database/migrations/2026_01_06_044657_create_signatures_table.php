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
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Type: reporter (user yang submit), technician (teknisi), approver (yang approve)
            $table->enum('signature_type', ['reporter', 'technician', 'approver']);

            // Path file signature (PNG)
            $table->string('signature_path');

            // Metadata
            $table->timestamp('signed_at')->useCurrent();
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            // Index
            $table->index('ticket_id');
            $table->index('user_id');
            $table->index('signature_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
