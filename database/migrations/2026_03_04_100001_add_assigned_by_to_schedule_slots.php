<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_slots', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_by')->nullable()->after('approval_status');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
            $table->index('assigned_by');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_slots', function (Blueprint $table) {
            $table->dropForeign(['assigned_by']);
            $table->dropIndex(['assigned_by']);
            $table->dropColumn('assigned_by');
        });
    }
};
