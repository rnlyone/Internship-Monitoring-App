<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('logbooks');
        Schema::dropIfExists('schedule_slots');

        Schema::create('schedule_slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_shift');
            $table->dateTime('end_shift');
            $table->string('caption')->nullable();
            $table->enum('status', ['not_yet', 'ongoing', 'done', 'late', 'absence'])->default('not_yet');
            $table->timestamps();

            $table->index(['user_id', 'start_shift']);
        });

        Schema::create('presence_stamps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('schedule_slot_id');
            $table->foreign('schedule_slot_id')->references('id')->on('schedule_slots')->onDelete('cascade');
            $table->timestamp('stamped_at');
            $table->enum('type', ['entry', 'exit']);
            $table->timestamps();

            $table->index(['schedule_slot_id', 'type']);
        });

        Schema::create('shift_logbooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('schedule_slot_id');
            $table->foreign('schedule_slot_id')->references('id')->on('schedule_slots')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();

            $table->index('schedule_slot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_logbooks');
        Schema::dropIfExists('presence_stamps');
        Schema::dropIfExists('schedule_slots');
    }
};
