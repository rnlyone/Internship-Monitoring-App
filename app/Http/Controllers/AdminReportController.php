<?php

namespace App\Http\Controllers;

use App\Models\KanbanCard;
use App\Models\ScheduleSlot;
use App\Models\Setting;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    /**
     * Show the reports listing page (all interns summary).
     */
    public function index()
    {
        $interns = User::where('role', 'intern')
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(function ($intern) {
                $slots = ScheduleSlot::with('shiftLogbooks')->where('user_id', $intern->id)->get();

                $firstSlot = $slots->sortBy('start_shift')->first();
                $lastSlot  = $slots->sortByDesc('start_shift')->first();

                return [
                    'id'              => $intern->id,
                    'name'            => $intern->name,
                    'email'           => $intern->email,
                    'total_schedules' => $slots->count(),
                    'total_hours'     => round($slots->sum(fn($s) => $s->duration_hours), 2),
                    'completed'       => $slots->where('status', 'done')->count(),
                    'late'            => $slots->where('status', 'late')->count(),
                    'absence'         => $slots->where('status', 'absence')->count(),
                    'logbook_entries' => $slots->sum(fn($s) => $s->shiftLogbooks->count()),
                    'internship_start' => $firstSlot?->start_shift?->format('d M Y'),
                    'internship_end'   => $lastSlot?->start_shift?->format('d M Y'),
                    'attendance_rate'  => $slots->count() > 0
                        ? round(($slots->where('status', 'done')->count() + $slots->where('status', 'late')->count()) / $slots->count() * 100, 1)
                        : 0,
                ];
            });

        return view('admin.reports', compact('interns'));
    }

    /**
     * Detailed report data for one intern (JSON).
     * ?date_from=&date_to=
     */
    public function show(Request $request, User $intern): JsonResponse
    {
        if ($intern->isAdmin()) {
            return response()->json(['message' => 'Not an intern.'], 422);
        }

        $query = ScheduleSlot::with(['entryStamp', 'exitStamp', 'shiftLogbooks'])
            ->where('user_id', $intern->id)
            ->orderBy('start_shift');

        if ($request->filled('date_from')) {
            $query->whereDate('start_shift', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_shift', '<=', $request->date_to);
        }

        $slots = $query->get();

        $schedules = $slots->map(function ($slot) {
            $entryTime = $slot->entryStamp?->stamped_at?->format('H:i');
            $exitTime  = $slot->exitStamp?->stamped_at?->format('H:i');

            $lateMinutes = null;
            if ($slot->entryStamp) {
                $diff = $slot->start_shift->diffInMinutes($slot->entryStamp->stamped_at, false);
                $lateMinutes = $diff > 0 ? (int) $diff : 0;
            }

            return [
                'id'              => $slot->id,
                'date'            => $slot->start_shift->format('D, d M Y'),
                'start_shift'     => $slot->start_shift->format('H:i'),
                'end_shift'       => $slot->end_shift->format('H:i'),
                'duration_hours'  => $slot->duration_hours,
                'caption'         => $slot->caption ?? '—',
                'status'          => $slot->status,
                'approval_status' => $slot->approval_status,
                'entry_time'      => $entryTime,
                'exit_time'       => $exitTime,
                'late_minutes'    => $lateMinutes,
                'logbook_count'   => $slot->shiftLogbooks->count(),
                'logbooks'        => $slot->shiftLogbooks->map(fn($l) => [
                    'id'         => $l->id,
                    'content'    => $l->content,
                    'created_at' => $l->created_at->format('d M Y H:i'),
                ])->values(),
            ];
        });

        $totalMinutes = $slots->sum(fn($s) => $s->start_shift->diffInMinutes($s->end_shift));

        // Weekly hours breakdown (for chart)
        $weeklyHours = $slots->groupBy(fn($s) => $s->start_shift->format('Y-W'))
            ->map(fn($week) => [
                'label' => 'Wk ' . $week->first()->start_shift->format('W'),
                'hours' => round($week->sum('duration_hours'), 2),
            ])->values();

        $summary = [
            'total_schedules'  => $slots->count(),
            'total_hours'      => round($totalMinutes / 60, 2),
            'completed'        => $slots->where('status', 'done')->count(),
            'late'             => $slots->whereIn('status', ['late'])->count(),
            'absence'          => $slots->where('status', 'absence')->count(),
            'ongoing'          => $slots->where('status', 'ongoing')->count(),
            'not_yet'          => $slots->where('status', 'not_yet')->count(),
            'approved'         => $slots->where('approval_status', 'approved')->count(),
            'pending'          => $slots->where('approval_status', 'pending')->count(),
            'rejected'         => $slots->where('approval_status', 'rejected')->count(),
            'logbook_entries'  => $slots->sum(fn($s) => $s->shiftLogbooks->count()),
            'attendance_rate'  => $slots->count() > 0
                ? round(($slots->where('status', 'done')->count() + $slots->whereIn('status', ['late'])->count()) / $slots->count() * 100, 1)
                : 0,
            'weekly_hours'     => $weeklyHours,
        ];

        return response()->json([
            'intern'    => [
                'id'    => $intern->id,
                'name'  => $intern->name,
                'email' => $intern->email,
            ],
            'summary'   => $summary,
            'schedules' => $schedules,
        ]);
    }

    /**
     * Export comprehensive PDF report for one intern.
     * GET /admin/reports/{intern}/pdf?date_from=&date_to=
     */
    public function exportPdf(Request $request, User $intern)
    {
        if ($intern->isAdmin()) {
            abort(422, 'Not an intern.');
        }

        // ── Schedule slots ────────────────────────────────
        $query = ScheduleSlot::with(['entryStamp', 'exitStamp', 'shiftLogbooks'])
            ->where('user_id', $intern->id)
            ->orderBy('start_shift');

        if ($request->filled('date_from')) {
            $query->whereDate('start_shift', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_shift', '<=', $request->date_to);
        }

        $slots = $query->get();

        // ── Kanban cards assigned to this intern ──────────
        $kanbanCards = KanbanCard::where('assigned_to', $intern->id)
            ->orWhere('created_by', $intern->id)
            ->orderBy('column_name')->orderBy('position')
            ->get();

        // ── Summary stats ─────────────────────────────────
        $totalMinutes  = $slots->sum(fn($s) => $s->start_shift->diffInMinutes($s->end_shift));
        $totalLateMin  = $slots->filter(fn($s) => $s->entryStamp)
            ->sum(function ($s) {
                $diff = $s->start_shift->diffInMinutes($s->entryStamp->stamped_at, false);
                return $diff > 0 ? (int) $diff : 0;
            });

        $summary = [
            'total_schedules'   => $slots->count(),
            'total_hours'       => round($totalMinutes / 60, 2),
            'completed'         => $slots->where('status', 'done')->count(),
            'late'              => $slots->where('status', 'late')->count(),
            'absence'           => $slots->where('status', 'absence')->count(),
            'ongoing'           => $slots->where('status', 'ongoing')->count(),
            'not_yet'           => $slots->where('status', 'not_yet')->count(),
            'approved'          => $slots->where('approval_status', 'approved')->count(),
            'pending'           => $slots->where('approval_status', 'pending')->count(),
            'rejected'          => $slots->where('approval_status', 'rejected')->count(),
            'shift_logs'        => $slots->sum(fn($s) => $s->shiftLogbooks->count()),
            'total_late_min'    => $totalLateMin,
            'attendance_rate'   => $slots->count() > 0
                ? round(($slots->where('status', 'done')->count() + $slots->where('status', 'late')->count()) / $slots->count() * 100, 1)
                : 0,
            'kanban_assigned'   => $kanbanCards->where('assigned_to', $intern->id)->count(),
            'kanban_done'       => $kanbanCards->where('assigned_to', $intern->id)->where('column_name', 'done')->count(),
            'date_from'         => $request->date_from ?? $slots->first()?->start_shift?->format('d M Y') ?? '—',
            'date_to'           => $request->date_to   ?? $slots->last()?->start_shift?->format('d M Y')  ?? '—',
            'internship_start'  => $slots->sortBy('start_shift')->first()?->start_shift?->format('d M Y') ?? '—',
            'internship_end'    => $slots->sortByDesc('start_shift')->first()?->start_shift?->format('d M Y') ?? '—',
        ];

        // ── Weekly breakdown ──────────────────────────────
        $weeklyBreakdown = $slots->groupBy(fn($s) => $s->start_shift->format('Y-W'))
            ->map(fn($week) => [
                'week'       => 'Week ' . $week->first()->start_shift->format('W') . ', ' . $week->first()->start_shift->format('Y'),
                'date_range' => $week->first()->start_shift->startOfWeek()->format('d M') . ' – ' . $week->first()->start_shift->endOfWeek()->format('d M Y'),
                'count'      => $week->count(),
                'hours'      => round($week->sum('duration_hours'), 2),
                'done'       => $week->where('status', 'done')->count(),
                'late'       => $week->where('status', 'late')->count(),
                'absence'    => $week->where('status', 'absence')->count(),
                'logs'       => $week->sum(fn($s) => $s->shiftLogbooks->count()),
            ])->values();

        // ── Detailed schedule rows ────────────────────────
        $scheduleRows = $slots->map(function ($slot) {
            $lateMinutes = null;
            if ($slot->entryStamp) {
                $diff = $slot->start_shift->diffInMinutes($slot->entryStamp->stamped_at, false);
                $lateMinutes = $diff > 0 ? (int) $diff : 0;
            }
            return [
                'date'            => $slot->start_shift->format('D, d M Y'),
                'shift'           => $slot->start_shift->format('H:i') . ' – ' . $slot->end_shift->format('H:i'),
                'hours'           => $slot->duration_hours,
                'caption'         => $slot->caption ?? '—',
                'status'          => $slot->status,
                'approval_status' => $slot->approval_status,
                'entry_time'      => $slot->entryStamp?->stamped_at?->format('H:i') ?? '—',
                'exit_time'       => $slot->exitStamp?->stamped_at?->format('H:i') ?? '—',
                'late_minutes'    => $lateMinutes,
                'logbooks'        => $slot->shiftLogbooks->map(fn($l) => [
                    'content'    => $l->content,
                    'created_at' => $l->created_at->format('d M Y H:i'),
                ])->values()->toArray(),
            ];
        })->toArray();

        // ── Kanban grouped by column ──────────────────────
        $kanbanColumns = [
            'backlog'     => 'Backlog',
            'todo'        => 'To Do',
            'undone'      => 'Undone',
            'on_progress' => 'On Progress',
            'done'        => 'Done',
            'archive'     => 'Archive',
        ];
        $kanbanByCol = $kanbanCards->groupBy('column_name');

        $pdf = Pdf::loadView('admin.report-pdf', [
            'intern'          => $intern,
            'summary'         => $summary,
            'weeklyBreakdown' => $weeklyBreakdown,
            'scheduleRows'    => $scheduleRows,
            'kanbanByCol'     => $kanbanByCol,
            'kanbanColumns'   => $kanbanColumns,
            'generatedAt'     => now()->format('d M Y H:i'),
            'dateFrom'        => $request->date_from,
            'dateTo'          => $request->date_to,
        ])->setPaper('A4', 'portrait');

        $filename = 'internship-report-' . str($intern->name)->slug() . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}

