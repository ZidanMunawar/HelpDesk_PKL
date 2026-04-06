<?php
// database/migrations/xxxx_add_admin_followup_to_ticket_approvals.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminFollowupToTicketApprovals extends Migration
{
    public function up()
    {
        Schema::table('ticket_approvals', function (Blueprint $table) {
            $table->boolean('needs_admin_followup')->default(false)->after('needs_vr');
            $table->timestamp('admin_followup_added_at')->nullable()->after('needs_admin_followup');
            $table->unsignedBigInteger('admin_followup_added_by')->nullable()->after('admin_followup_added_at');

            $table->foreign('admin_followup_added_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('ticket_approvals', function (Blueprint $table) {
            $table->dropForeign(['admin_followup_added_by']);
            $table->dropColumn(['needs_admin_followup', 'admin_followup_added_at', 'admin_followup_added_by']);
        });
    }
}
