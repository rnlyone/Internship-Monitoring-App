<?php

namespace App\Http\Controllers;

use App\Models\ScheduleSlot;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WidgetController extends Controller
{
    /**
     * Render the compact widget page.
     */
    public function index()
    {
        return view('widget.index');
    }

    /**
     * Widget data: today's/upcoming shifts with entry/exit/logbook status.
     */
    public function data(): JsonResponse
    {
        $user = Auth::user();
        $now  = Carbon::now('Asia/Singapore');

        $shifts = ScheduleSlot::with(['entryStamp', 'exitStamp', 'shiftLogbooks'])
            ->where('user_id', $user->id)
            ->where('approval_status', 'approved')
            ->whereIn('status', ['not_yet', 'ongoing', 'done', 'late'])
            ->where('start_shift', '>=', $now->copy()->startOfDay())
            ->where('start_shift', '<=', $now->copy()->addDays(2)->endOfDay())
            ->orderBy('start_shift')
            ->limit(6)
            ->get();

        $result = $shifts->map(function ($slot) use ($now) {
            $hasEntry  = $slot->entryStamp !== null;
            $hasExit   = $slot->exitStamp  !== null;
            $isOngoing = $slot->status === 'ongoing' ||
                         ($slot->start_shift->lte($now) && $slot->end_shift->gte($now) && $hasEntry);
            $isDone    = in_array($slot->status, ['done', 'late']);

            $logbookCount = $slot->shiftLogbooks->count();
            $minutesBefore = $now->diffInMinutes($slot->start_shift, false);

            // Can stamp entry: from 30 min before start until 30 min after
            $canEntry = !$hasEntry && !$isDone
                && $slot->start_shift->copy()->subMinutes(30)->lte($now)
                && $slot->start_shift->copy()->addMinutes(30)->gte($now);

            // Can stamp exit: has entry, no exit, shift end passed by ≥75%
            $canExit = $hasEntry && !$hasExit && !$isDone;

            // Day label
            $dayLabel = 'Later';
            if ($slot->start_shift->isSameDay($now))               $dayLabel = 'Today';
            elseif ($slot->start_shift->isSameDay($now->copy()->addDay())) $dayLabel = 'Tomorrow';

            // Status label
            $statusLabel = 'Upcoming';
            if ($isOngoing)              $statusLabel = 'In Progress';
            elseif ($isDone)             $statusLabel = $slot->status === 'late' ? 'Done (Late)' : 'Done';
            elseif ($hasEntry)           $statusLabel = 'Stamped In';
            elseif ($canEntry)           $statusLabel = 'Ready to Stamp';

            return [
                'id'             => $slot->id,
                'day_label'      => $dayLabel,
                'date'           => $slot->start_shift->format('D, d M'),
                'time_start'     => $slot->start_shift->format('H:i'),
                'time_end'       => $slot->end_shift->format('H:i'),
                'caption'        => $slot->caption ?? '—',
                'status'         => $slot->status,
                'status_label'   => $statusLabel,
                'is_ongoing'     => $isOngoing,
                'is_done'        => $isDone,
                'has_entry'      => $hasEntry,
                'has_exit'       => $hasExit,
                'can_entry'      => $canEntry,
                'can_exit'       => $canExit,
                'entry_time'     => $slot->entryStamp?->stamped_at?->format('H:i'),
                'exit_time'      => $slot->exitStamp?->stamped_at?->format('H:i'),
                'logbook_count'  => $logbookCount,
                'minutes_until'  => (int) $minutesBefore,
                'start_iso'      => $slot->start_shift->format('Y-m-d\TH:i:sP'),
                'end_iso'        => $slot->end_shift->format('Y-m-d\TH:i:sP'),
            ];
        });

        return response()->json([
            'user'   => $user->name,
            'shifts' => $result,
            'now'    => $now->format('H:i'),
            'date'   => $now->format('D, d M Y'),
        ]);
    }
}
