<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    /**
     * Show settings page (admin only).
     */
    public function index()
    {
        $maxHours = Setting::getValue('max_working_hours_per_week', 40);
        $scheduleSubmissionOpen = (bool) Setting::getValue('schedule_submission_open', 1);
        return view('settings.index', compact('maxHours', 'scheduleSubmissionOpen'));
    }

    /**
     * Update settings (async, admin only).
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'max_working_hours_per_week' => 'sometimes|numeric|min:1|max:168',
            'schedule_submission_open'   => 'sometimes|boolean',
        ]);

        if ($request->has('max_working_hours_per_week')) {
            Setting::setValue('max_working_hours_per_week', $request->max_working_hours_per_week);
        }

        if ($request->has('schedule_submission_open')) {
            Setting::setValue('schedule_submission_open', $request->schedule_submission_open ? '1' : '0');
        }

        return response()->json([
            'message'                    => 'Settings updated successfully.',
            'max_working_hours_per_week' => (float) Setting::getValue('max_working_hours_per_week', 40),
            'schedule_submission_open'   => (bool) Setting::getValue('schedule_submission_open', 1),
        ]);
    }

    /**
     * Get current settings (JSON).
     */
    public function get(): JsonResponse
    {
        return response()->json([
            'max_working_hours_per_week' => (float) Setting::getValue('max_working_hours_per_week', 40),
            'schedule_submission_open'   => (bool) Setting::getValue('schedule_submission_open', 1),
        ]);
    }
}
