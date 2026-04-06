// Migration: 2025_02_04_006_remove_created_at_from_signatures.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('signatures', function (Blueprint $table) {
            $table->dropColumn('created_at');
        });
    }

    public function down()
    {
        Schema::table('signatures', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable();
        });
    }
};
