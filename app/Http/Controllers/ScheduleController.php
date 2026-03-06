<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\ScheduleSlot;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Show the calendar page.
     */
    public function index()
    {
        $maxHours = Setting::getValue('max_working_hours_per_week', 40);
        return view('schedules.index', compact('maxHours'));
    }

    /**
     * Fetch events for FullCalendar (JSON).
     */
    public function events(Request $request): JsonResponse
    {
        $query = ScheduleSlot::with('user:id,name', 'assignedBy:id,name', 'entryStamp', 'exitStamp');

        // If filter for specific user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Date range for calendar view
        if ($request->filled('start') && $request->filled('end')) {
            $query->where('start_shift', '>=', $request->start)
                  ->where('end_shift', '<=', $request->end);
        }

        $slots = $query->orderBy('start_shift')->get();

        $events = $slots->map(function ($slot) {
            $isAssigned = $slot->assigned_by !== null;

            if ($isAssigned) {
                // Red / pink palette for admin-assigned schedules
                $colorMap = [
                    'not_yet'  => '#00bad1',
                    'ongoing'  => '#0d6efd',
                    'done'     => '#20c997',
                    'late'     => '#6610f2',
                    'absence'  => '#82868b',
                ];
                $bgColor     = $colorMap[$slot->status] ?? '#00bad1';
                $borderColor = $bgColor;
                $textColor   = '#fff';
                $classNames  = ['fc-event-assigned'];

                if ($slot->approval_status === 'rejected') {
                    $bgColor     = '#82868b33';
                    $borderColor = '#82868b';
                    $textColor   = '#82868b';
                    $classNames  = ['fc-event-rejected'];
                }
            } else {
                $colorMap = [
                    'not_yet'  => '#7367f0',
                    'ongoing'  => '#ff9f43',
                    'done'     => '#28c76f',
                    'late'     => '#ea5455',
                    'absence'  => '#82868b',
                ];

                $bgColor     = $colorMap[$slot->status] ?? '#7367f0';
                $borderColor = $bgColor;
                $textColor   = '#fff';
                $classNames  = [];

                if ($slot->approval_status === 'pending') {
                    $bgColor     = '#ff9f4333';
                    $borderColor = '#ff9f43';
                    $textColor   = '#ff9f43';
                    $classNames  = ['fc-event-pending'];
                } elseif ($slot->approval_status === 'rejected') {
                    $bgColor     = '#82868b33';
                    $borderColor = '#82868b';
                    $textColor   = '#82868b';
                    $classNames  = ['fc-event-rejected'];
                }
            }

            return [
                'id'              => $slot->id,
                'title'           => $slot->user->name . ($slot->caption ? ' — ' . $slot->caption : '') . ($isAssigned ? ' 📌' : ''),
                'start'           => $slot->start_shift->format('Y-m-d\TH:i:sP'),
                'end'             => $slot->end_shift->format('Y-m-d\TH:i:sP'),
                'backgroundColor' => $bgColor,
                'borderColor'     => $borderColor,
                'textColor'       => $textColor,
                'classNames'      => $classNames,
                'extendedProps'   => [
                    'schedule_id'       => $slot->id,
                    'user_id'           => $slot->user_id,
                    'user_name'         => $slot->user->name,
                    'caption'           => $slot->caption,
                    'status'            => $slot->status,
                    'approval_status'   => $slot->approval_status,
                    'duration_hours'    => $slot->duration_hours,
                    'has_entry'         => $slot->entryStamp !== null,
                    'has_exit'          => $slot->exitStamp !== null,
                    'start_iso'         => $slot->start_shift->format('Y-m-d\TH:i:sP'),
                    'end_iso'           => $slot->end_shift->format('Y-m-d\TH:i:sP'),
                    'is_assigned'          => $isAssigned,
                    'assigned_by_name'     => $slot->assignedBy?->name,
                    'reschedule_status'    => $slot->reschedule_status,
                    'pending_start_iso'    => $slot->pending_start?->format('Y-m-d\TH:i:sP'),
                    'pending_end_iso'      => $slot->pending_end?->format('Y-m-d\TH:i:sP'),
                    'pending_caption'      => $slot->pending_caption,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Store a new schedule slot.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'start_shift' => 'required|date',
            'end_shift'   => 'required|date|after:start_shift',
            'caption'     => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Check if schedule submission is open (interns only)
        if ($user->isIntern() && ! (bool) Setting::getValue('schedule_submission_open', 1)) {
            return response()->json([
                'message' => 'Schedule submission is currently closed by the administrator.',
            ], 403);
        }

        $start = Carbon::parse($request->start_shift);
        $end = Carbon::parse($request->end_shift);

        // Max hours check — get week boundaries (Monday to Sunday)
        $weekStart = $start->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd   = $start->copy()->endOfWeek(Carbon::SUNDAY);

        $maxHours = (float) Setting::getValue('max_working_hours_per_week', 40);

        // Sum existing hours for this user in this week
        $existingMinutes = ScheduleSlot::where('user_id', $user->id)
            ->where('start_shift', '>=', $weekStart)
            ->where('end_shift', '<=', $weekEnd)
            ->get()
            ->sum(function ($slot) {
                return $slot->start_shift->diffInMinutes($slot->end_shift);
            });

        $newMinutes = $start->diffInMinutes($end);
        $totalMinutes = $existingMinutes + $newMinutes;
        $totalHours = round($totalMinutes / 60, 2);

        if ($totalHours > $maxHours) {
            $remainingHours = round(($maxHours * 60 - $existingMinutes) / 60, 2);
            return response()->json([
                'message' => "Cannot add schedule. This would exceed the weekly limit of {$maxHours} hours. You have {$remainingHours} hours remaining this week.",
            ], 422);
        }

        // Check overlapping schedules
        $overlap = ScheduleSlot::where('user_id', $user->id)
            ->where(function ($q) use ($start, $end) {
                $q->where(function ($q2) use ($start, $end) {
                    $q2->where('start_shift', '<', $end)
                        ->where('end_shift', '>', $start);
                });
            })->exists();

        if ($overlap) {
            return response()->json([
                'message' => 'This schedule overlaps with an existing one.',
            ], 422);
        }

        // Admins are auto-approved; interns require admin approval
        $approvalStatus = $user->isAdmin() ? 'approved' : 'pending';

        $slot = ScheduleSlot::create([
            'user_id'         => $user->id,
            'start_shift'     => $start,
            'end_shift'       => $end,
            'caption'         => $request->caption,
            'status'          => 'not_yet',
            'approval_status' => $approvalStatus,
        ]);

        $slot->load('user:id,name');

        // Notify all admins when an intern submits for approval
        if ($user->isIntern()) {
            Notification::notifyAdmins('schedule_submitted', [
                'title'        => 'New Schedule Submitted',
                'message'      => $user->name . ' submitted a schedule for ' . $start->format('D, d M Y') . '.',
                'url'          => route('admin.approvals.index'),
                'related_type' => 'schedule',
                'related_id'   => (string) $slot->id,
            ]);
        }

        $message = $user->isAdmin()
            ? 'Schedule created successfully.'
            : 'Schedule submitted for approval.';

        return response()->json([
            'message'  => $message,
            'schedule' => $slot,
        ], 201);
    }

    /**
     * Update a schedule slot.
     *
     * Admin: direct update on any schedule regardless of status.
     * Intern (approved schedule): stores a reschedule request in pending_* fields.
     * Intern (pending/rejected schedule): direct update + reset approval to pending.
     */
    public function update(Request $request, ScheduleSlot $schedule): JsonResponse
    {
        $user = Auth::user();

        // Interns can only edit their own; admins can edit any
        if ($user->isIntern() && $schedule->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'start_shift' => 'required|date',
            'end_shift'   => 'required|date|after:start_shift',
            'caption'     => 'nullable|string|max:255',
        ]);

        $start = Carbon::parse($request->start_shift);
        $end   = Carbon::parse($request->end_shift);

        // ---- ADMIN: direct update, no restrictions ----
        if ($user->isAdmin()) {
            // Overlap check (exclude self)
            $overlap = ScheduleSlot::where('user_id', $schedule->user_id)
                ->where('id', '!=', $schedule->id)
                ->where(function ($q) use ($start, $end) {
                    $q->where('start_shift', '<', $end)->where('end_shift', '>', $start);
                })->exists();

            if ($overlap) {
                return response()->json(['message' => 'This schedule overlaps with an existing one.'], 422);
            }

            $schedule->update([
                'start_shift'       => $start,
                'end_shift'         => $end,
                'caption'           => $request->caption,
                // Clear any pending reschedule when admin directly edits
                'pending_start'     => null,
                'pending_end'       => null,
                'pending_caption'   => null,
                'reschedule_status' => null,
            ]);

            $schedule->load('user:id,name');
            return response()->json(['message' => 'Schedule updated successfully.', 'schedule' => $schedule]);
        }

        // ---- INTERN: cannot edit done/absence directly ----
        if (in_array($schedule->status, ['done', 'absence'])) {
            return response()->json(['message' => 'Cannot modify a completed or absent schedule.'], 422);
        }

        // ---- INTERN on approved schedule: submit reschedule request ----
        if ($schedule->approval_status === 'approved') {
            // Overlap check against original times (exclude self)
            $overlap = ScheduleSlot::where('user_id', $schedule->user_id)
                ->where('id', '!=', $schedule->id)
                ->where(function ($q) use ($start, $end) {
                    $q->where('start_shift', '<', $end)->where('end_shift', '>', $start);
                })->exists();

            if ($overlap) {
                return response()->json(['message' => 'The requested new time overlaps with an existing schedule.'], 422);
            }

            // Don't overwrite original times — store in pending fields
            $schedule->update([
                'pending_start'     => $start,
                'pending_end'       => $end,
                'pending_caption'   => $request->caption,
                'reschedule_status' => 'pending',
            ]);

            $schedule->load('user:id,name');

            Notification::notifyAdmins('reschedule_requested', [
                'title'        => 'Reschedule Request',
                'message'      => $schedule->user->name . ' requested to reschedule their shift on ' . $schedule->start_shift->format('D, d M Y') . '.',
                'url'          => route('admin.approvals.index'),
                'related_type' => 'schedule',
                'related_id'   => (string) $schedule->id,
            ]);

            return response()->json([
                'message'  => 'Reschedule request submitted. Your original schedule remains active until the admin approves.',
                'schedule' => $schedule,
            ]);
        }

        // ---- INTERN on pending/rejected schedule: direct update, re-submit ----
        $weekStart = $start->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd   = $start->copy()->endOfWeek(Carbon::SUNDAY);
        $maxHours  = (float) Setting::getValue('max_working_hours_per_week', 40);

        $existingMinutes = ScheduleSlot::where('user_id', $schedule->user_id)
            ->where('id', '!=', $schedule->id)
            ->where('start_shift', '>=', $weekStart)
            ->where('end_shift', '<=', $weekEnd)
            ->get()
            ->sum(fn($s) => $s->start_shift->diffInMinutes($s->end_shift));

        $newMinutes = $start->diffInMinutes($end);
        if (round(($existingMinutes + $newMinutes) / 60, 2) > $maxHours) {
            return response()->json([
                'message' => "Cannot update. This would exceed the weekly limit of {$maxHours} hours.",
            ], 422);
        }

        $overlap = ScheduleSlot::where('user_id', $schedule->user_id)
            ->where('id', '!=', $schedule->id)
            ->where(function ($q) use ($start, $end) {
                $q->where('start_shift', '<', $end)->where('end_shift', '>', $start);
            })->exists();

        if ($overlap) {
            return response()->json(['message' => 'This schedule overlaps with an existing one.'], 422);
        }

        $schedule->update([
            'start_shift'     => $start,
            'end_shift'       => $end,
            'caption'         => $request->caption,
            'approval_status' => 'pending',
        ]);

        $schedule->load('user:id,name');

        Notification::notifyAdmins('schedule_submitted', [
            'title'        => 'Schedule Resubmitted',
            'message'      => $schedule->user->name . ' resubmitted a schedule for ' . $start->format('D, d M Y') . '.',
            'url'          => route('admin.approvals.index'),
            'related_type' => 'schedule',
            'related_id'   => (string) $schedule->id,
        ]);

        return response()->json([
            'message'  => 'Schedule updated and resubmitted for approval.',
            'schedule' => $schedule,
        ]);
    }

    /**
     * Delete a schedule.
     */
    public function destroy(ScheduleSlot $schedule): JsonResponse
    {
        $user = Auth::user();

        if ($user->isIntern() && $schedule->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (in_array($schedule->status, ['done', 'late', 'absence'])) {
            return response()->json(['message' => 'Cannot delete a finalized schedule.'], 422);
        }

        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted.']);
    }

    /**
     * Get weekly hours summary for current user.
     */
    public function weeklyHours(Request $request): JsonResponse
    {
        $user = Auth::user();
        $date = $request->filled('date') ? Carbon::parse($request->date) : Carbon::now();

        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd   = $date->copy()->endOfWeek(Carbon::SUNDAY);

        $totalMinutes = ScheduleSlot::where('user_id', $user->id)
            ->where('start_shift', '>=', $weekStart)
            ->where('end_shift', '<=', $weekEnd)
            ->get()
            ->sum(fn($s) => $s->start_shift->diffInMinutes($s->end_shift));

        $maxHours = (float) Setting::getValue('max_working_hours_per_week', 40);

        return response()->json([
            'used_hours'      => round($totalMinutes / 60, 2),
            'max_hours'       => $maxHours,
            'remaining_hours' => round($maxHours - ($totalMinutes / 60), 2),
            'week_start'      => $weekStart->toDateString(),
            'week_end'        => $weekEnd->toDateString(),
        ]);
    }
}
