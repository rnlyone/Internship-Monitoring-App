<?php

namespace App\Http\Controllers;

use App\Models\PresenceStamp;
use App\Models\ScheduleSlot;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresenceStampController extends Controller
{
    /**
     * Stamp entry presence.
     */
    public function entry(ScheduleSlot $schedule): JsonResponse
    {
        $user = Auth::user();

        if ($schedule->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($schedule->entryStamp) {
            return response()->json(['message' => 'Already stamped entry.'], 422);
        }

        $now = Carbon::now();
        $start = $schedule->start_shift;
        $minutesAfterStart = $start->diffInMinutes($now, false);

        // Can only stamp entry from 30 min before to 30 min after start
        if ($now->lt($start->copy()->subMinutes(30))) {
            return response()->json(['message' => 'Too early to stamp entry.'], 422);
        }

        // Determine status
        if ($minutesAfterStart > 30) {
            // Already handled by cron/auto — but allow manual stamp too
            $schedule->update(['status' => 'absence']);
            return response()->json(['message' => 'Entry window expired. Marked as absence.'], 422);
        }

        $status = 'ongoing';
        if ($minutesAfterStart > 15) {
            $status = 'late';
        }

        $schedule->update(['status' => $status]);

        $stamp = PresenceStamp::create([
            'schedule_slot_id' => $schedule->id,
            'stamped_at'       => $now,
            'type'             => 'entry',
        ]);

        return response()->json([
            'message' => $status === 'late' ? 'Stamped entry (late).' : 'Stamped entry successfully.',
            'stamp'   => $stamp,
            'status'  => $status,
        ]);
    }

    /**
     * Stamp exit presence.
     */
    public function exit(ScheduleSlot $schedule): JsonResponse
    {
        $user = Auth::user();

        if ($schedule->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (!$schedule->entryStamp) {
            return response()->json(['message' => 'Must stamp entry first.'], 422);
        }

        if ($schedule->exitStamp) {
            return response()->json(['message' => 'Already stamped exit.'], 422);
        }

        $now = Carbon::now();
        $entryTime = $schedule->entryStamp->stamped_at;
        $scheduledDuration = $schedule->start_shift->diffInMinutes($schedule->end_shift);

        // Calculate how late the entry was
        $lateMinutes = max(0, $schedule->start_shift->diffInMinutes($entryTime, false));

        // Exit is allowed only after: schedule end time + late minutes
        $earliestExit = $schedule->end_shift->copy()->addMinutes($lateMinutes);

        if ($now->lt($earliestExit)) {
            $remaining = $now->diffInMinutes($earliestExit);
            return response()->json([
                'message' => "Cannot exit yet. Please wait {$remaining} more minute(s). Earliest exit: " . $earliestExit->format('H:i'),
            ], 422);
        }

        $stamp = PresenceStamp::create([
            'schedule_slot_id' => $schedule->id,
            'stamped_at'       => $now,
            'type'             => 'exit',
        ]);

        // Mark as done if it was ongoing, keep late if it was late
        if ($schedule->status === 'ongoing') {
            $schedule->update(['status' => 'done']);
        } elseif ($schedule->status === 'late') {
            $schedule->update(['status' => 'done']);
        }

        return response()->json([
            'message' => 'Stamped exit successfully.',
            'stamp'   => $stamp,
        ]);
    }

    /**
     * Get upcoming schedules that need presence attention (for dashboard).
     * Returns schedules within -30min to +30min of now, that are not yet stamped.
     */
    public function upcoming(): JsonResponse
    {
        $user = Auth::user();
        $now = Carbon::now();

        $schedules = ScheduleSlot::with('entryStamp', 'exitStamp')
            ->where('user_id', $user->id)
            ->where('status', 'not_yet')
            ->where('start_shift', '<=', $now->copy()->addMinutes(30))
            ->where('start_shift', '>=', $now->copy()->subMinutes(30))
            ->orderBy('start_shift')
            ->get()
            ->map(function ($slot) use ($now) {
                $minutesUntilStart = $now->diffInMinutes($slot->start_shift, false);
                return [
                    'id'                  => $slot->id,
                    'start_shift'         => $slot->start_shift->format('Y-m-d\TH:i:s'),
                    'end_shift'           => $slot->end_shift->format('Y-m-d\TH:i:s'),
                    'caption'             => $slot->caption,
                    'status'              => $slot->status,
                    'minutes_until_start' => round($minutesUntilStart),
                    'has_entry'           => $slot->entryStamp !== null,
                    'can_stamp_entry'     => $slot->entryStamp === null,
                ];
            });

        // Also get active schedules needing exit
        $active = ScheduleSlot::with('entryStamp', 'exitStamp')
            ->where('user_id', $user->id)
            ->whereIn('status', ['ongoing', 'late'])
            ->whereNull('id') // Will be replaced by proper subquery below
            ->get();

        // Schedules with entry but no exit
        $needingExit = ScheduleSlot::with('entryStamp', 'exitStamp')
            ->where('user_id', $user->id)
            ->whereIn('status', ['ongoing', 'late'])
            ->whereHas('entryStamp')
            ->whereDoesntHave('exitStamp')
            ->get()
            ->map(function ($slot) use ($now) {
                $entryTime = $slot->entryStamp->stamped_at;
                $lateMinutes = max(0, $slot->start_shift->diffInMinutes($entryTime, false));
                $earliestExit = $slot->end_shift->copy()->addMinutes($lateMinutes);

                return [
                    'id'              => $slot->id,
                    'start_shift'     => $slot->start_shift->format('Y-m-d\TH:i:s'),
                    'end_shift'       => $slot->end_shift->format('Y-m-d\TH:i:s'),
                    'caption'         => $slot->caption,
                    'status'          => $slot->status,
                    'entry_time'      => $entryTime->format('Y-m-d\TH:i:s'),
                    'earliest_exit'   => $earliestExit->format('Y-m-d\TH:i:s'),
                    'can_exit'        => $now->gte($earliestExit),
                    'needs_exit'      => true,
                ];
            });

        return response()->json([
            'upcoming'    => $schedules,
            'needing_exit' => $needingExit,
        ]);
    }

    /**
     * Auto-update statuses for overdue schedules (called by cron or eager check).
     */
    public function autoUpdateStatuses(): JsonResponse
    {
        $now = Carbon::now();

        // Mark as late: 15 min after start, no entry
        $lateSlots = ScheduleSlot::where('status', 'not_yet')
            ->where('start_shift', '<', $now->copy()->subMinutes(15))
            ->where('start_shift', '>=', $now->copy()->subMinutes(30))
            ->whereDoesntHave('entryStamp')
            ->get();

        foreach ($lateSlots as $slot) {
            $slot->update(['status' => 'late']);
        }

        // Mark as absence: 30 min after start, no entry
        $absenceSlots = ScheduleSlot::where('status', 'not_yet')
            ->where('start_shift', '<', $now->copy()->subMinutes(30))
            ->whereDoesntHave('entryStamp')
            ->get();

        foreach ($absenceSlots as $slot) {
            $slot->update(['status' => 'absence']);
        }

        // Also handle late ones without entry after 30 min
        $lateAbsence = ScheduleSlot::where('status', 'late')
            ->where('start_shift', '<', $now->copy()->subMinutes(30))
            ->whereDoesntHave('entryStamp')
            ->get();

        foreach ($lateAbsence as $slot) {
            $slot->update(['status' => 'absence']);
        }

        return response()->json(['message' => 'Statuses updated.']);
    }
}
