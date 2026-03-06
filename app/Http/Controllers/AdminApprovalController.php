<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\ScheduleSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminApprovalController extends Controller
{
    /**
     * Show the approvals page.
     */
    public function index()
    {
        $pendingCount    = ScheduleSlot::where('approval_status', 'pending')->count();
        $approvedCount   = ScheduleSlot::where('approval_status', 'approved')->count();
        $rejectedCount   = ScheduleSlot::where('approval_status', 'rejected')->count();
        $rescheduleCount = ScheduleSlot::where('reschedule_status', 'pending')->count();

        return view('admin.approvals', compact('pendingCount', 'approvedCount', 'rejectedCount', 'rescheduleCount'));
    }

    /**
     * Fetch schedules by approval status (JSON).
     */
    public function list(Request $request): JsonResponse
    {
        $status = $request->get('approval_status', 'pending');

        $schedules = ScheduleSlot::with('user:id,name')
            ->where('approval_status', $status)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($slot) => [
                'id'              => $slot->id,
                'user_name'       => $slot->user->name,
                'user_id'         => $slot->user_id,
                'start_shift'     => $slot->start_shift->format('Y-m-d\TH:i:sP'),
                'end_shift'       => $slot->end_shift->format('Y-m-d\TH:i:sP'),
                'caption'         => $slot->caption,
                'duration_hours'  => $slot->duration_hours,
                'status'          => $slot->status,
                'approval_status' => $slot->approval_status,
                'created_at'      => $slot->created_at->format('Y-m-d\TH:i:sP'),
            ]);

        return response()->json(['schedules' => $schedules]);
    }

    /**
     * Approve a single schedule.
     */
    public function approve(ScheduleSlot $schedule): JsonResponse
    {
        $schedule->update(['approval_status' => 'approved']);

        Notification::notify($schedule->user_id, 'schedule_approved', [
            'title'        => 'Schedule Approved',
            'message'      => 'Your schedule on ' . $schedule->start_shift->format('D, d M Y') . ' has been approved.',
            'url'          => route('schedules.index'),
            'related_type' => 'schedule',
            'related_id'   => (string) $schedule->id,
        ]);

        return response()->json(['message' => 'Schedule approved successfully.']);
    }

    /**
     * Reject a single schedule.
     */
    public function reject(ScheduleSlot $schedule): JsonResponse
    {
        $schedule->update(['approval_status' => 'rejected']);

        Notification::notify($schedule->user_id, 'schedule_rejected', [
            'title'        => 'Schedule Rejected',
            'message'      => 'Your schedule on ' . $schedule->start_shift->format('D, d M Y') . ' was rejected.',
            'url'          => route('schedules.index'),
            'related_type' => 'schedule',
            'related_id'   => (string) $schedule->id,
        ]);

        return response()->json(['message' => 'Schedule rejected.']);
    }

    /**
     * Bulk approve selected schedules.
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'string']);

        $slots = ScheduleSlot::whereIn('id', $request->ids)
            ->where('approval_status', 'pending')
            ->get();

        foreach ($slots as $slot) {
            $slot->update(['approval_status' => 'approved']);
            Notification::notify($slot->user_id, 'schedule_approved', [
                'title'        => 'Schedule Approved',
                'message'      => 'Your schedule on ' . $slot->start_shift->format('D, d M Y') . ' has been approved.',
                'url'          => route('schedules.index'),
                'related_type' => 'schedule',
                'related_id'   => (string) $slot->id,
            ]);
        }

        $count = $slots->count();
        return response()->json(['message' => "{$count} schedule(s) approved successfully."]);
    }

    /**
     * Bulk reject selected schedules.
     */
    public function bulkReject(Request $request): JsonResponse
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'string']);

        $slots = ScheduleSlot::whereIn('id', $request->ids)
            ->where('approval_status', 'pending')
            ->get();

        foreach ($slots as $slot) {
            $slot->update(['approval_status' => 'rejected']);
            Notification::notify($slot->user_id, 'schedule_rejected', [
                'title'        => 'Schedule Rejected',
                'message'      => 'Your schedule on ' . $slot->start_shift->format('D, d M Y') . ' was rejected.',
                'url'          => route('schedules.index'),
                'related_type' => 'schedule',
                'related_id'   => (string) $slot->id,
            ]);
        }

        $count = $slots->count();
        return response()->json(['message' => "{$count} schedule(s) rejected."]);
    }

    /**
     * Permanently delete a schedule (admin only).
     */
    public function destroy(ScheduleSlot $schedule): JsonResponse
    {
        $schedule->delete();

        return response()->json(['message' => 'Schedule deleted successfully.']);
    }

    /**
     * Return all pending reschedule requests (JSON).
     */
    public function rescheduleList(): JsonResponse
    {
        $schedules = ScheduleSlot::with('user:id,name')
            ->where('reschedule_status', 'pending')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(fn($slot) => [
                'id'              => $slot->id,
                'user_name'       => $slot->user->name,
                'user_id'         => $slot->user_id,
                // Current (original) approved times
                'start_shift'     => $slot->start_shift->format('Y-m-d\TH:i:sP'),
                'end_shift'       => $slot->end_shift->format('Y-m-d\TH:i:sP'),
                'caption'         => $slot->caption,
                'duration_hours'  => $slot->duration_hours,
                // Proposed new times
                'pending_start'   => $slot->pending_start->format('Y-m-d\TH:i:sP'),
                'pending_end'     => $slot->pending_end->format('Y-m-d\TH:i:sP'),
                'pending_caption' => $slot->pending_caption,
                'pending_hours'   => round($slot->pending_start->diffInMinutes($slot->pending_end) / 60, 2),
                'status'          => $slot->status,
                'updated_at'      => $slot->updated_at->format('Y-m-d\TH:i:sP'),
            ]);

        return response()->json(['schedules' => $schedules]);
    }

    /**
     * Approve a reschedule request — swap pending times into actual times.
     */
    public function approveReschedule(ScheduleSlot $schedule): JsonResponse
    {
        if ($schedule->reschedule_status !== 'pending') {
            return response()->json(['message' => 'No pending reschedule for this schedule.'], 422);
        }

        $schedule->update([
            'start_shift'       => $schedule->pending_start,
            'end_shift'         => $schedule->pending_end,
            'caption'           => $schedule->pending_caption ?? $schedule->caption,
            'pending_start'     => null,
            'pending_end'       => null,
            'pending_caption'   => null,
            'reschedule_status' => null,
        ]);

        Notification::notify($schedule->user_id, 'reschedule_approved', [
            'title'        => 'Reschedule Approved',
            'message'      => 'Your reschedule request for ' . $schedule->start_shift->format('D, d M Y') . ' has been approved.',
            'url'          => route('schedules.index'),
            'related_type' => 'schedule',
            'related_id'   => (string) $schedule->id,
        ]);

        return response()->json(['message' => 'Reschedule approved. Schedule updated to new times.']);
    }

    /**
     * Reject a reschedule request — discard pending fields, keep originals.
     */
    public function rejectReschedule(ScheduleSlot $schedule): JsonResponse
    {
        if ($schedule->reschedule_status !== 'pending') {
            return response()->json(['message' => 'No pending reschedule for this schedule.'], 422);
        }

        $schedule->update([
            'pending_start'     => null,
            'pending_end'       => null,
            'pending_caption'   => null,
            'reschedule_status' => 'rejected',
        ]);

        Notification::notify($schedule->user_id, 'reschedule_rejected', [
            'title'        => 'Reschedule Rejected',
            'message'      => 'Your reschedule request for ' . $schedule->start_shift->format('D, d M Y') . ' was rejected. Your original schedule remains active.',
            'url'          => route('schedules.index'),
            'related_type' => 'schedule',
            'related_id'   => (string) $schedule->id,
        ]);

        return response()->json(['message' => 'Reschedule rejected. Original schedule kept.']);
    }
}
