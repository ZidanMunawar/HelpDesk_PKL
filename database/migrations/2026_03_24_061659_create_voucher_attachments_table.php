<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah tabel sudah ada, kalau ada drop dulu
        Schema::dropIfExists('voucher_attachments');

        Schema::create('voucher_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('voucher_request_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 100)->nullable();
            $table->integer('file_size')->nullable();
            $table->text('description')->nullable()->comment('Keterangan foto (opsional)');
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamp('created_at')->nullable();

            // Indexes
            $table->index('voucher_request_id');
            $table->index('uploaded_by');

            // Foreign Keys
            $table->foreign('voucher_request_id', 'fk_attachments_vr')
                ->references('id')
                ->on('voucher_requests')
                ->onDelete('cascade');

            $table->foreign('uploaded_by', 'fk_attachments_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        // Drop kolom total_amount dari voucher_requests jika ada
        if (Schema::hasColumn('voucher_requests', 'total_amount')) {
            Schema::table('voucher_requests', function (Blueprint $table) {
                $table->dropColumn('total_amount');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_attachments');

        // Kembalikan kolom total_amount (optional, untuk rollback)
        Schema::table('voucher_requests', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->default(0)->after('ticket_id');
        });
    }
};
