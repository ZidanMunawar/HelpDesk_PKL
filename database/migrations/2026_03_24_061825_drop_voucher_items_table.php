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
        // Hapus foreign key dulu kalau ada
        try {
            Schema::table('voucher_items', function (Blueprint $table) {
                $table->dropForeign(['voucher_request_id']);
            });
        } catch (\Exception $e) {
            // Abaikan jika foreign key tidak ada
        }

        // Drop tabel
        Schema::dropIfExists('voucher_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan tabel (kalau perlu)
        Schema::create('voucher_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voucher_request_id');
            $table->string('item_name');
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->string('vendor')->nullable();
            $table->timestamps();

            $table->foreign('voucher_request_id')
                ->references('id')
                ->on('voucher_requests')
                ->onDelete('cascade');
        });
    }
};
