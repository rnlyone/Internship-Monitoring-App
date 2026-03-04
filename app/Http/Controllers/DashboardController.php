<?php

namespace App\Http\Controllers;

use App\Models\ScheduleSlot;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the dashboard view.
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Get dashboard data (JSON, for async loading).
     */
    public function data(): JsonResponse
    {
        $user = Auth::user();
        $now = Carbon::now();

        $weekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd   = $now->copy()->endOfWeek(Carbon::SUNDAY);

        // Weekly stats for current user
        $mySchedules = ScheduleSlot::where('user_id', $user->id)
            ->where('start_shift', '>=', $weekStart)
            ->where('end_shift', '<=', $weekEnd)
            ->get();

        $myTotalMinutes = $mySchedules->sum(fn($s) => $s->start_shift->diffInMinutes($s->end_shift));
        $maxHours = (float) Setting::getValue('max_working_hours_per_week', 40);

        $stats = [
            'total_schedules'   => $mySchedules->count(),
            'used_hours'        => round($myTotalMinutes / 60, 2),
            'max_hours'         => $maxHours,
            'remaining_hours'   => round($maxHours - ($myTotalMinutes / 60), 2),
            'completed'         => $mySchedules->whereIn('status', ['done'])->count(),
            'late'              => $mySchedules->where('status', 'late')->count(),
            'absence'           => $mySchedules->where('status', 'absence')->count(),
            'upcoming'          => $mySchedules->where('status', 'not_yet')->count(),
        ];

        // Admin gets intern count
        if ($user->isAdmin()) {
            $stats['total_interns'] = User::where('role', 'intern')->count();
        }

        return response()->json($stats);
    }

    /**
     * Return the current user's upcoming approved shifts (next 7 days).
     */
    public function upcomingShifts(): JsonResponse
    {
        $user = Auth::user();
        $now  = Carbon::now('Asia/Singapore');

        $slots = ScheduleSlot::where('user_id', $user->id)
            ->where('approval_status', 'approved')
            ->whereIn('status', ['not_yet', 'ongoing'])
            ->where('start_shift', '>=', $now->copy()->subHours(1)) // include just-started shifts
            ->where('start_shift', '<=', $now->copy()->addDays(7))
            ->orderBy('start_shift')
            ->limit(10)
            ->get();

        $shifts = $slots->map(function ($slot) use ($now) {
            $start       = $slot->start_shift->setTimezone('Asia/Singapore');
            $end         = $slot->end_shift->setTimezone('Asia/Singapore');
            $diffSeconds = $start->diffInSeconds($now, false); // negative = future

            if ($start->isToday()) {
                $dayLabel = 'Today';
            } elseif ($start->isTomorrow()) {
                $dayLabel = 'Tomorrow';
            } else {
                $dayLabel = $start->format('D, d M');
            }

            return [
                'id'            => $slot->id,
                'day_label'     => $dayLabel,
                'date_fmt'      => $start->format('d M Y'),
                'time_start'    => $start->format('H:i'),
                'time_end'      => $end->format('H:i'),
                'duration_hours'=> $slot->duration_hours,
                'caption'       => $slot->caption,
                'status'        => $slot->status,
                'is_assigned'   => $slot->assigned_by !== null,
                'start_iso'     => $slot->start_shift->format('Y-m-d\TH:i:sP'),
                'end_iso'       => $slot->end_shift->format('Y-m-d\TH:i:sP'),
            ];
        });

        return response()->json(['shifts' => $shifts]);
    }
}
