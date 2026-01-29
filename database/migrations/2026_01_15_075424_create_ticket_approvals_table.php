<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('ticket_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');

            // Admin Eng Receive
            $table->boolean('admin_eng_received')->default(false);
            $table->unsignedBigInteger('admin_eng_received_by')->nullable();
            $table->timestamp('admin_eng_received_at')->nullable();

            // OM Approval
            $table->boolean('om_approved')->default(false);
            $table->unsignedBigInteger('om_approved_by')->nullable();
            $table->timestamp('om_approved_at')->nullable();

            // User Check
            $table->boolean('user_checked')->default(false);
            $table->unsignedBigInteger('user_checked_by')->nullable();
            $table->timestamp('user_checked_at')->nullable();

            // GM Approval
            $table->boolean('gm_approved')->default(false);
            $table->unsignedBigInteger('gm_approved_by')->nullable();
            $table->timestamp('gm_approved_at')->nullable();

            // Admin Check (optional)
            $table->boolean('admin_check')->default(false);
            $table->unsignedBigInteger('admin_checked_by')->nullable();
            $table->timestamp('admin_checked_at')->nullable();

            $table->text('rejection_reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('admin_eng_received_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('om_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_checked_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('gm_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('admin_checked_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ticket_approvals');
    }
};
