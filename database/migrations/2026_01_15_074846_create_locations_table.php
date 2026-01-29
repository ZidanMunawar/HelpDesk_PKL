<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('location_type', ['room', 'floor', 'department', 'facility', 'area'])->default('area');

            // Floor number dengan enum (support 2 hotel)
            $table->enum('floor_number', [
                'GF',      // Ground Floor (Harris & Pop)
                'M',       // Mezzanine (Pop only)
                '3A',      // Lantai 3A (Harris & Pop)
                '4',       // Lantai 4 (Pop only)
                '5',       // Lantai 5 (Harris & Pop)
                '6',       // Lantai 6 (Harris & Pop)
                '7',       // Lantai 7 (Harris & Pop)
                '8',       // Lantai 8 (Harris & Pop)
                '9'        // Lantai 9 (Harris only)
            ])->nullable();

            // Hotel identifier
            $table->enum('hotel', ['harris', 'pop'])->default('harris')->comment('Harris Hotel or Pop Hotel');

            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Index untuk search cepat
            $table->index(['hotel', 'floor_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('locations');
    }
};
