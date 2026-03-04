<?php

namespace App\Http\Controllers;

use App\Models\KanbanCard;
use App\Models\ScheduleSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AdminPerformanceController extends Controller
{
    public function index()
    {
        return view('admin.performance');
    }

    public function data(): JsonResponse
    {
        $now = Carbon::now('Asia/Singapore');

        $interns = User::where('role', 'intern')->orderBy('name')->get();

        // Collect all schedule slots for all interns at once
        $allSlots = ScheduleSlot::with(['entryStamp', 'shiftLogbooks'])
            ->whereIn('user_id', $interns->pluck('id'))
            ->where('approval_status', '!=', 'rejected')
            ->get();

        // Collect all kanban cards
        $allKanban = KanbanCard::whereIn('assigned_to', $interns->pluck('id'))
            ->orWhereIn('created_by', $interns->pluck('id'))
            ->get();

        // -- Weekly labels (last 8 weeks) --
        $weekLabels = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekLabels[] = 'Wk ' . $now->copy()->subWeeks($i)->format('W');
        }

        $internsData = $interns->map(function ($intern) use ($allSlots, $allKanban, $now, $weekLabels) {
            $slots = $allSlots->where('user_id', $intern->id);

            // Past/completed slots only (not future not_yet)
            $pastSlots = $slots->filter(fn($s) => $s->start_shift->lte($now));
            $total     = $pastSlots->count();
            $done      = $pastSlots->where('status', 'done')->count();
            $late      = $pastSlots->where('status', 'late')->count();
            $absence   = $pastSlots->where('status', 'absence')->count();
            $ongoing   = $pastSlots->where('status', 'ongoing')->count();
            $attended  = $done + $late;

            // Future scheduled
            $upcoming = $slots->filter(fn($s) => $s->start_shift->gt($now))->count();

            // Hours
            $totalHours     = round($slots->sum('duration_hours'), 2);
            $completedHours = round($pastSlots->whereIn('status', ['done', 'late'])->sum('duration_hours'), 2);

            // Attendance & punctuality
            $attendanceRate  = $total > 0 ? round($attended / $total * 100, 1) : 0;
            $punctualityRate = $attended > 0 ? round($done / $attended * 100, 1) : 0;
            $absenceRate     = $total > 0 ? round($absence / $total * 100, 1) : 0;

            // Avg late minutes
            $lateMinutes = $pastSlots->where('status', 'late')
                ->filter(fn($s) => $s->entryStamp)
                ->map(function ($s) {
                    $diff = $s->start_shift->diffInMinutes($s->entryStamp->stamped_at, false);
                    return $diff > 0 ? (int) $diff : 0;
                });
            $avgLateMinutes = $lateMinutes->count() > 0 ? round($lateMinutes->avg(), 1) : 0;

            // Logbook engagement
            $logbookEntries    = $slots->sum(fn($s) => $s->shiftLogbooks->count());
            $logbookRate       = $attended > 0 ? round($logbookEntries / $attended * 100, 1) : 0;

            // Kanban
            $kanban         = $allKanban->where('assigned_to', $intern->id);
            $kanbanTotal    = $kanban->count();
            $kanbanDone     = $kanban->where('column_name', 'done')->count();
            $kanbanRate     = $kanbanTotal > 0 ? round($kanbanDone / $kanbanTotal * 100, 1) : 0;

            // Weekly hours (last 8 weeks)
            $weeklyHours = [];
            for ($i = 7; $i >= 0; $i--) {
                $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
                $weekEnd   = $weekStart->copy()->endOfWeek();
                $wHours    = $slots->filter(fn($s) => $s->start_shift->between($weekStart, $weekEnd))
                    ->sum('duration_hours');
                $weeklyHours[] = round($wHours, 2);
            }

            // Monthly hours (last 6 months)
            $monthlyHours = [];
            $monthLabels  = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = $now->copy()->subMonths($i);
                $monthLabels[] = $month->format('M Y');
                $mHours = $slots->filter(fn($s) => $s->start_shift->format('Y-m') === $month->format('Y-m'))
                    ->sum('duration_hours');
                $monthlyHours[] = round($mHours, 2);
            }

            // Performance score (composite 0-100)
            $score = round(
                ($attendanceRate  * 0.35) +
                ($punctualityRate * 0.25) +
                (min($logbookRate, 100) * 0.20) +
                ($kanbanRate      * 0.20),
                1
            );

            // Internship date range
            $firstSlot = $slots->sortBy('start_shift')->first();
            $lastSlot  = $slots->sortByDesc('start_shift')->first();

            return [
                'id'               => $intern->id,
                'name'             => $intern->name,
                'email'            => $intern->email,
                'total_shifts'     => $slots->count(),
                'past_shifts'      => $total,
                'upcoming_shifts'  => $upcoming,
                'total_hours'      => $totalHours,
                'completed_hours'  => $completedHours,
                'done'             => $done,
                'late'             => $late,
                'absence'          => $absence,
                'ongoing'          => $ongoing,
                'attended'         => $attended,
                'attendance_rate'  => $attendanceRate,
                'punctuality_rate' => $punctualityRate,
                'absence_rate'     => $absenceRate,
                'avg_late_minutes' => $avgLateMinutes,
                'logbook_entries'  => $logbookEntries,
                'logbook_rate'     => $logbookRate,
                'kanban_total'     => $kanbanTotal,
                'kanban_done'      => $kanbanDone,
                'kanban_rate'      => $kanbanRate,
                'weekly_hours'     => $weeklyHours,
                'monthly_hours'    => $monthlyHours,
                'performance_score' => $score,
                'internship_start' => $firstSlot?->start_shift?->format('d M Y') ?? '—',
                'internship_end'   => $lastSlot?->start_shift?->format('d M Y') ?? '—',
            ];
        })->values();

        // Global summary
        $avgAttendance   = $interns->count() > 0 ? round($internsData->avg('attendance_rate'), 1) : 0;
        $avgPunctuality  = $interns->count() > 0 ? round($internsData->avg('punctuality_rate'), 1) : 0;
        $totalHoursAll   = round($internsData->sum('total_hours'), 2);
        $totalLogbooks   = $internsData->sum('logbook_entries');
        $bestPerformer   = $internsData->sortByDesc('performance_score')->first();

        // Global weekly trend (sum across all interns per week)
        $globalWeekly = array_fill(0, 8, 0);
        foreach ($internsData as $intern) {
            foreach ($intern['weekly_hours'] as $wi => $wh) {
                $globalWeekly[$wi] += $wh;
            }
        }
        $globalWeekly = array_map(fn($v) => round($v, 2), $globalWeekly);

        // Week labels
        $weekLabels = [];
        for ($i = 7; $i >= 0; $i--) {
            $weekLabels[] = 'Wk ' . Carbon::now('Asia/Singapore')->subWeeks($i)->format('W');
        }

        // Month labels (last 6)
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthLabels[] = Carbon::now('Asia/Singapore')->subMonths($i)->format('M Y');
        }

        return response()->json([
            'interns'       => $internsData,
            'week_labels'   => $weekLabels,
            'month_labels'  => $monthLabels,
            'global_weekly' => $globalWeekly,
            'summary' => [
                'total_interns'    => $interns->count(),
                'avg_attendance'   => $avgAttendance,
                'avg_punctuality'  => $avgPunctuality,
                'total_hours'      => $totalHoursAll,
                'total_logbooks'   => $totalLogbooks,
                'best_performer'   => $bestPerformer ? $bestPerformer['name'] : '—',
                'best_score'       => $bestPerformer ? $bestPerformer['performance_score'] : 0,
            ],
        ]);
    }
}
