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
        Schema::create('voucher_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_request_id')->constrained('voucher_requests')->onDelete('cascade');

            $table->string('item_name');
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->string('vendor')->nullable();

            $table->timestamps();

            // Index
            $table->index('voucher_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_items');
    }
};
