<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // 1. Hapus foreign key constraint untuk parent_id
            if (Schema::hasColumn('locations', 'parent_id')) {
                $table->dropForeign(['parent_id']);
            }

            // 2. Hapus kolom yang TIDAK digunakan
            $table->dropColumn(['parent_id', 'room_number']);

            // 3. Pertahankan location_type (rename jika perlu, atau biarkan as is)
            //    location_type sudah ada: enum('room','floor','department','facility','area')
            //    Biarkan saja, tidak perlu diubah

            // 4. Pertahankan floor_number (varchar 10)
            //    floor_number sudah ada, biarkan saja

            // 5. OPTIONAL: Ubah location_type jika ingin sederhana
            // $table->enum('location_type', ['room', 'floor', 'area', 'facility', 'other'])
            //       ->default('area')
            //       ->change();
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // 1. Tambahkan kembali kolom yang dihapus
            $table->bigInteger('parent_id')->unsigned()->nullable()->after('id');
            $table->string('room_number', 20)->nullable()->after('floor_number');

            // 2. Tambahkan foreign key kembali
            $table->foreign('parent_id')->references('id')->on('locations')->onDelete('set null');
        });
    }
};
