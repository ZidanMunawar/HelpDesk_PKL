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
        Schema::table('ticket_approvals', function (Blueprint $table) {
            // Add VR related columns
            if (!Schema::hasColumn('ticket_approvals', 'needs_vr')) {
                $table->boolean('needs_vr')->default(false)->after('admin_check');
            }

            if (!Schema::hasColumn('ticket_approvals', 'vr_reason')) {
                $table->text('vr_reason')->nullable()->after('needs_vr');
            }

            if (!Schema::hasColumn('ticket_approvals', 'vr_created_at')) {
                $table->timestamp('vr_created_at')->nullable()->after('vr_reason');
            }

            if (!Schema::hasColumn('ticket_approvals', 'vr_created_by')) {
                $table->bigInteger('vr_created_by')->unsigned()->nullable()->after('vr_created_at');
                $table->foreign('vr_created_by')->references('id')->on('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('ticket_approvals', 'rejection_note')) {
                $table->text('rejection_note')->nullable()->after('rejection_reason');
            }

            if (!Schema::hasColumn('ticket_approvals', 'admin_checked_by')) {
                $table->bigInteger('admin_checked_by')->unsigned()->nullable()->change();
            }

            // Change default values
            if (Schema::hasColumn('ticket_approvals', 'admin_eng_received')) {
                $table->boolean('admin_eng_received')->default(false)->change();
            }

            if (Schema::hasColumn('ticket_approvals', 'om_approved')) {
                $table->boolean('om_approved')->default(false)->change();
            }

            if (Schema::hasColumn('ticket_approvals', 'user_checked')) {
                $table->boolean('user_checked')->default(false)->change();
            }

            if (Schema::hasColumn('ticket_approvals', 'gm_approved')) {
                $table->boolean('gm_approved')->default(false)->change();
            }

            if (Schema::hasColumn('ticket_approvals', 'admin_check')) {
                $table->boolean('admin_check')->default(false)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_approvals', function (Blueprint $table) {
            $table->dropForeign(['vr_created_by']);
            $table->dropColumn([
                'needs_vr',
                'vr_reason',
                'vr_created_at',
                'vr_created_by',
                'rejection_note'
            ]);
        });
    }
};
