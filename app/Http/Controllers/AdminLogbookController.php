<?php

namespace App\Http\Controllers;

use App\Models\ScheduleSlot;
use App\Models\ShiftLogbook;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminLogbookController extends Controller
{
    /**
     * Show the logbook review page.
     */
    public function index()
    {
        $interns = User::where('role', 'intern')->orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.logbook-review', compact('interns'));
    }

    /**
     * Fetch logbook entries (JSON) with optional filters.
     * ?user_id=&date_from=&date_to=
     */
    public function list(Request $request): JsonResponse
    {
        $query = ShiftLogbook::with([
            'scheduleSlot.user:id,name,email',
        ]);

        // Filter by intern
        if ($request->filled('user_id')) {
            $query->whereHas('scheduleSlot', fn($q) => $q->where('user_id', $request->user_id));
        }

        // Filter by date range (based on schedule start_shift)
        if ($request->filled('date_from')) {
            $query->whereHas('scheduleSlot', fn($q) =>
                $q->whereDate('start_shift', '>=', $request->date_from)
            );
        }
        if ($request->filled('date_to')) {
            $query->whereHas('scheduleSlot', fn($q) =>
                $q->whereDate('start_shift', '<=', $request->date_to)
            );
        }

        $logbooks = $query->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($l) => [
                'id'             => $l->id,
                'content'        => $l->content,
                'created_at'     => $l->created_at->format('Y-m-d H:i'),
                'intern_name'    => $l->scheduleSlot->user->name ?? '—',
                'intern_email'   => $l->scheduleSlot->user->email ?? '',
                'schedule_id'    => $l->schedule_slot_id,
                'schedule_start' => $l->scheduleSlot->start_shift->format('Y-m-d H:i'),
                'schedule_end'   => $l->scheduleSlot->end_shift->format('H:i'),
                'schedule_caption' => $l->scheduleSlot->caption ?? '',
                'schedule_status'  => $l->scheduleSlot->status,
            ]);

        return response()->json([
            'logbooks' => $logbooks,
            'total'    => $logbooks->count(),
        ]);
    }
}
