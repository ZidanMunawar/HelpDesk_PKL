<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('voucher_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voucher_request_id');
            $table->string('item_name');
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->string('vendor')->nullable();
            $table->timestamps();

            $table->index('voucher_request_id');
            $table->foreign('voucher_request_id')->references('id')->on('voucher_requests')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('voucher_items');
    }
};
