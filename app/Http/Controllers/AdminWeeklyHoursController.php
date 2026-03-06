<?php

namespace App\Http\Controllers;

use App\Models\ScheduleSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminWeeklyHoursController extends Controller
{
    /**
     * Show the weekly hours review page.
     */
    public function index()
    {
        return view('admin.weekly-hours');
    }

    /**
     * Return per-intern weekly hours and submission data as JSON.
     *
     * Query param: ?week=YYYY-MM-DD  (any date within the desired ISO week)
     * Defaults to the current week.
     */
    public function data(Request $request): JsonResponse
    {
        $tz       = 'Asia/Singapore';
        $inputDate = $request->get('week');

        $weekStart = $inputDate
            ? Carbon::parse($inputDate, $tz)->startOfWeek(Carbon::MONDAY)
            : Carbon::now($tz)->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // All interns
        $interns = User::where('role', 'intern')->orderBy('name')->get(['id', 'name', 'email']);

        // All slots in this week (any approval status except we do want to see all)
        $allSlots = ScheduleSlot::with('user:id,name')
            ->whereIn('user_id', $interns->pluck('id'))
            ->where('start_shift', '>=', $weekStart)
            ->where('start_shift', '<=', $weekEnd)
            ->orderBy('start_shift')
            ->get();

        // ---- KPI totals ----
        $totalHours    = round($allSlots->sum('duration_hours'), 2);
        $approvedHours = round(
            $allSlots->where('approval_status', 'approved')->sum('duration_hours'), 2
        );
        $pendingCount  = $allSlots->where('approval_status', 'pending')->count();
        $rejectedCount = $allSlots->where('approval_status', 'rejected')->count();

        // ---- Per-intern rows ----
        $internRows = $interns->map(function ($intern) use ($allSlots) {
            $slots = $allSlots->where('user_id', $intern->id)->values();

            $internTotalHours    = round($slots->sum('duration_hours'), 2);
            $internApprovedHours = round(
                $slots->where('approval_status', 'approved')->sum('duration_hours'), 2
            );
            $internPending  = $slots->where('approval_status', 'pending')->count();
            $internApproved = $slots->where('approval_status', 'approved')->count();
            $internRejected = $slots->where('approval_status', 'rejected')->count();

            $shiftRows = $slots->map(fn($slot) => [
                'id'              => $slot->id,
                'start_shift'     => $slot->start_shift->setTimezone('Asia/Singapore')->format('Y-m-d\TH:i:sP'),
                'end_shift'       => $slot->end_shift->setTimezone('Asia/Singapore')->format('Y-m-d\TH:i:sP'),
                'duration_hours'  => $slot->duration_hours,
                'caption'         => $slot->caption,
                'status'          => $slot->status,
                'approval_status' => $slot->approval_status,
            ]);

            return [
                'id'              => $intern->id,
                'name'            => $intern->name,
                'email'           => $intern->email,
                'total_shifts'    => $slots->count(),
                'total_hours'     => $internTotalHours,
                'approved_hours'  => $internApprovedHours,
                'pending_count'   => $internPending,
                'approved_count'  => $internApproved,
                'rejected_count'  => $internRejected,
                'shifts'          => $shiftRows,
            ];
        });

        // ---- Chart data: daily hours breakdown per intern ----
        $days = [];
        for ($d = 0; $d < 7; $d++) {
            $days[] = $weekStart->copy()->addDays($d)->format('D, d M');
        }

        $chartSeries = $interns->map(function ($intern) use ($allSlots, $weekStart) {
            $data = [];
            for ($d = 0; $d < 7; $d++) {
                $dayStart = $weekStart->copy()->addDays($d)->startOfDay();
                $dayEnd   = $dayStart->copy()->endOfDay();
                $hours = $allSlots
                    ->where('user_id', $intern->id)
                    ->filter(fn($s) => $s->start_shift->between($dayStart, $dayEnd))
                    ->sum('duration_hours');
                $data[] = round($hours, 2);
            }
            return ['name' => $intern->name, 'data' => $data];
        })->values();

        return response()->json([
            'week_start'     => $weekStart->toDateString(),
            'week_end'       => $weekEnd->toDateString(),
            'week_label'     => 'Week ' . $weekStart->isoWeek() . ' — ' .
                                $weekStart->format('d M') . ' – ' . $weekEnd->format('d M Y'),
            'kpi' => [
                'total_interns'    => $interns->count(),
                'total_hours'      => $totalHours,
                'approved_hours'   => $approvedHours,
                'pending_count'    => $pendingCount,
                'rejected_count'   => $rejectedCount,
                'total_shifts'     => $allSlots->count(),
            ],
            'interns'        => $internRows->values(),
            'chart_days'     => $days,
            'chart_series'   => $chartSeries,
        ]);
    }
}
