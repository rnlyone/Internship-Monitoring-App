<?php

namespace App\Http\Controllers;

use App\Models\ScheduleSlot;
use App\Models\ShiftLogbook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftLogbookController extends Controller
{
    /**
     * Get logbook entries for a schedule slot.
     */
    public function index(ScheduleSlot $schedule): JsonResponse
    {
        $user = Auth::user();

        if ($user->isIntern() && $schedule->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $logbooks = $schedule->shiftLogbooks()->orderBy('created_at', 'desc')->get();

        return response()->json(['logbooks' => $logbooks]);
    }

    /**
     * Store a logbook entry — only after entry presence stamp.
     */
    public function store(Request $request, ScheduleSlot $schedule): JsonResponse
    {
        $user = Auth::user();

        if ($schedule->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if (!$schedule->entryStamp) {
            return response()->json(['message' => 'You must stamp entry before writing logbook.'], 422);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $logbook = ShiftLogbook::create([
            'schedule_slot_id' => $schedule->id,
            'content'          => $request->content,
        ]);

        return response()->json([
            'message' => 'Logbook entry saved.',
            'logbook' => $logbook,
        ], 201);
    }

    /**
     * Update a logbook entry.
     */
    public function update(Request $request, ShiftLogbook $logbook): JsonResponse
    {
        $user = Auth::user();
        $schedule = $logbook->scheduleSlot;

        if ($user->isIntern() && $schedule->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $logbook->update(['content' => $request->content]);

        return response()->json([
            'message' => 'Logbook entry updated.',
            'logbook' => $logbook,
        ]);
    }

    /**
     * Delete a logbook entry.
     */
    public function destroy(ShiftLogbook $logbook): JsonResponse
    {
        $user = Auth::user();
        $schedule = $logbook->scheduleSlot;

        if ($user->isIntern() && $schedule->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $logbook->delete();

        return response()->json(['message' => 'Logbook entry deleted.']);
    }
}
