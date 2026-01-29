<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('priority_id');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->string('location_manual')->nullable();
            $table->unsignedBigInteger('user_id')->comment('Department user who created');
            $table->unsignedBigInteger('assigned_to')->nullable()->comment('Technician assigned by admin_eng');
            $table->enum('status', [
                'open',
                'received',
                'pending_om',
                'in_progress',
                'pending_vr',
                'completed',
                'pending_gm',
                'closed',
                'cancelled'
            ])->default('open');
            $table->integer('current_stage')->default(1)->comment('1=Requested,2=Received,3=OM,4=InProgress,5=VR,6=Completed,7=UserCheck,8=GM,9=Closed');
            $table->enum('approval_status', ['pending_approval', 'approved', 'rejected'])->default('pending_approval');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
