<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\ScheduleSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminScheduleAssignController extends Controller
{
    /**
     * Show the assign-schedule page.
     */
    public function index()
    {
        $interns = User::where('role', 'intern')->orderBy('name')->get(['id', 'name', 'email']);

        $recent = ScheduleSlot::with(['user:id,name', 'assignedBy:id,name'])
            ->whereNotNull('assigned_by')
            ->orderByDesc('start_shift')
            ->limit(100)
            ->get();

        return view('admin.assign-schedule', compact('interns', 'recent'));
    }

    /**
     * Assign the same schedule slot to one or more interns.
     */
    public function store(Request $request)
    {
        $request->validate([
            'intern_ids'    => 'required|array|min:1',
            'intern_ids.*'  => 'exists:users,id',
            'start_shift'   => 'required|date',
            'end_shift'     => 'required|date|after:start_shift',
            'caption'       => 'nullable|string|max:255',
        ]);

        $start   = Carbon::parse($request->start_shift)->timezone('Asia/Singapore');
        $end     = Carbon::parse($request->end_shift)->timezone('Asia/Singapore');
        $adminId = Auth::id();

        $created = 0;
        $skipped = [];

        foreach ($request->intern_ids as $internId) {
            $intern = User::find($internId);
            if (! $intern || ! $intern->isIntern()) {
                $skipped[] = 'ID ' . $internId . ' (not an intern)';
                continue;
            }

            // Check for overlapping schedules for this intern
            $overlap = ScheduleSlot::where('user_id', $internId)
                ->where(function ($q) use ($start, $end) {
                    $q->where('start_shift', '<', $end)
                      ->where('end_shift', '>', $start);
                })->exists();

            if ($overlap) {
                $skipped[] = $intern->name . ' (schedule overlap)';
                continue;
            }

            $slot = ScheduleSlot::create([
                'user_id'         => $internId,
                'start_shift'     => $start,
                'end_shift'       => $end,
                'caption'         => $request->caption,
                'status'          => 'not_yet',
                'approval_status' => 'approved', // admin-assigned → auto-approved
                'assigned_by'     => $adminId,
            ]);

            // Notify intern (non-fatal — slot is already saved)
            try {
                Notification::notify((int) $internId, 'schedule_assigned', [
                    'title'        => 'New Schedule Assigned',
                    'message'      => 'A schedule was assigned to you for ' . $start->format('D, d M Y') . ($request->caption ? ' — ' . $request->caption : '') . '.',
                    'url'          => route('schedules.index'),
                    'related_type' => 'schedule',
                    'related_id'   => (string) $slot->id,
                ]);
            } catch (\Exception $e) {
                \Log::warning('schedule_assigned notification failed for user ' . $internId . ': ' . $e->getMessage());
            }

            $created++;
        }

        $msg = "Successfully assigned {$created} schedule(s).";
        if (! empty($skipped)) {
            $msg .= ' Skipped: ' . implode(', ', $skipped) . '.';
        }

        return back()->with('assign_result', [
            'success' => $created > 0,
            'message' => $msg,
        ]);
    }

    /**
     * Delete an admin-assigned schedule slot.
     */
    public function destroy(ScheduleSlot $schedule)
    {
        if (! $schedule->assigned_by) {
            return back()->with('assign_result', [
                'success' => false,
                'message' => 'This schedule was not admin-assigned and cannot be deleted here.',
            ]);
        }

        $schedule->delete();

        return back()->with('assign_result', [
            'success' => true,
            'message' => 'Assigned schedule deleted successfully.',
        ]);
    }
}
