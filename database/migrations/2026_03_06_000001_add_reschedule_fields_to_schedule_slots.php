<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_slots', function (Blueprint $table) {
            // Pending reschedule times (proposed by intern, awaiting admin approval)
            $table->dateTime('pending_start')->nullable()->after('end_shift');
            $table->dateTime('pending_end')->nullable()->after('pending_start');
            $table->string('pending_caption', 255)->nullable()->after('pending_end');
            // null = no pending reschedule, 'pending' = waiting review, 'rejected' = admin rejected
            $table->string('reschedule_status', 20)->nullable()->after('pending_caption');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_slots', function (Blueprint $table) {
            $table->dropColumn(['pending_start', 'pending_end', 'pending_caption', 'reschedule_status']);
        });
    }
};
