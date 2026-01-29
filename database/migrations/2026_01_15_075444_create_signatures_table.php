<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('signature_type', ['reporter', 'technician', 'approver']);
            $table->integer('stage')->default(1)->comment('1=Requested,2=Received,3=OM,4=Completed,5=Checked,6=GM');
            $table->string('signature_path');
            $table->timestamp('signed_at')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('ticket_id');
            $table->index('user_id');
            $table->index('signature_type');

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('signatures');
    }
};
