// Migration: 2025_02_04_009_optimize_data_types.php (SAFE VERSION)
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // LANGKAH 1: Validasi dan cleanup data users.phone
        $invalidPhones = DB::table('users')
            ->whereNotNull('phone')
            ->whereRaw('LENGTH(phone) > 20')
            ->get(['id', 'phone']);

        if ($invalidPhones->isNotEmpty()) {
            // Log atau notify ada data yang bermasalah
            $this->logInvalidPhones($invalidPhones);

            // Clean up: potong nomor telepon yang terlalu panjang
            DB::table('users')
                ->whereNotNull('phone')
                ->whereRaw('LENGTH(phone) > 20')
                ->update([
                        'phone' => DB::raw('SUBSTRING(phone, 1, 20)'),
                        'updated_at' => now()
                    ]);
        }

        // LANGKAH 2: Ubah tipe data users.phone
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->change();
        });

        // LANGKAH 3: Validasi dan cleanup activity_logs.ip_address
        $invalidIPs = DB::table('activity_logs')
            ->whereNotNull('ip_address')
            ->whereRaw('LENGTH(ip_address) > 45')
            ->get(['id', 'ip_address']);

        if ($invalidIPs->isNotEmpty()) {
            // Clean up IP addresses yang terlalu panjang
            // Jika ada IP yang >45 karakter, kemungkinan bukan IP valid
            DB::table('activity_logs')
                ->whereNotNull('ip_address')
                ->whereRaw('LENGTH(ip_address) > 45')
                ->update([
                        'ip_address' => 'INVALID_IP_TOO_LONG',
                        'updated_at' => now()
                    ]);
        }

        // LANGKAH 4: Ubah tipe data activity_logs.ip_address
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->change();
        });
    }

    private function logInvalidPhones($invalidPhones)
    {
        // Buat log file atau entry database untuk tracking
        $logMessage = "Found " . count($invalidPhones) . " phones exceeding 20 chars:\n";

        foreach ($invalidPhones as $user) {
            $logMessage .= "User ID: {$user->id}, Phone: {$user->phone}\n";
        }

        // Simpan ke file log
        \Illuminate\Support\Facades\Log::warning($logMessage);

        // Atau simpan ke table khusus untuk audit
        DB::table('data_cleanup_logs')->insert([
            'migration' => '2025_02_04_009_optimize_data_types',
            'issue_type' => 'phone_too_long',
            'affected_records' => count($invalidPhones),
            'details' => json_encode($invalidPhones->toArray()),
            'created_at' => now()
        ]);
    }

    public function down()
    {
        // Rollback: kembalikan ke ukuran semula
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 255)->nullable()->change();
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('ip_address', 255)->nullable()->change();
        });

        // Note: Data yang sudah dipotong tidak bisa dikembalikan otomatis
        // Ini adalah trade-off dari optimization
    }
};
