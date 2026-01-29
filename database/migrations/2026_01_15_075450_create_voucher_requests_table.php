<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('voucher_requests', function (Blueprint $table) {
            $table->id();
            $table->string('vr_number', 50)->unique();
            $table->unsignedBigInteger('ticket_id');
            $table->decimal('total_amount', 15, 2)->default(0.00);
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->unsignedBigInteger('created_by');

            // Admin Eng Approval
            $table->boolean('admin_approved')->default(false);
            $table->unsignedBigInteger('admin_approved_by')->nullable();
            $table->timestamp('admin_approved_at')->nullable();

            // OM Approval
            $table->boolean('om_approved')->default(false);
            $table->unsignedBigInteger('om_approved_by')->nullable();
            $table->timestamp('om_approved_at')->nullable();

            // GM Approval
            $table->boolean('gm_approved')->default(false);
            $table->unsignedBigInteger('gm_approved_by')->nullable();
            $table->timestamp('gm_approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('vr_number');
            $table->index('ticket_id');
            $table->index('status');

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('admin_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('om_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('gm_approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('voucher_requests');
    }
};
