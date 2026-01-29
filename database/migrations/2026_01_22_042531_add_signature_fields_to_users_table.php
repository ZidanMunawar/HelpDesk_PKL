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
        Schema::table('users', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::hasColumn('users', 'signature_path')) {
                $table->string('signature_path', 255)->nullable()->comment('Path to stored signature image')->after('profile_picture');
            }

            if (!Schema::hasColumn('users', 'has_signature')) {
                $table->boolean('has_signature')->default(false)->comment('1 if user has uploaded signature')->after('signature_path');
            }

            if (!Schema::hasColumn('users', 'signature_updated_at')) {
                $table->timestamp('signature_updated_at')->nullable()->comment('When signature was last updated')->after('has_signature');
            }
        });

        // Update existing users who already have signature_path
        DB::statement("UPDATE users SET has_signature = 1 WHERE signature_path IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['signature_path', 'has_signature', 'signature_updated_at']);
        });
    }
};
