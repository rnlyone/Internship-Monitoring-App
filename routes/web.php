<?php

use App\Http\Controllers\AdminPerformanceController;
use App\Http\Controllers\AdminScheduleAssignController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminApprovalController;
use App\Http\Controllers\AdminLogbookController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PresenceStampController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShiftLogbookController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/dashboard/upcoming-shifts', [DashboardController::class, 'upcomingShifts'])->name('dashboard.upcoming-shifts');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Schedules (calendar)
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/events', [ScheduleController::class, 'events'])->name('schedules.events');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::get('/schedules/weekly-hours', [ScheduleController::class, 'weeklyHours'])->name('schedules.weekly-hours');

    // Presence stamps
    Route::post('/presence/{schedule}/entry', [PresenceStampController::class, 'entry'])->name('presence.entry');
    Route::post('/presence/{schedule}/exit', [PresenceStampController::class, 'exit'])->name('presence.exit');
    Route::get('/presence/upcoming', [PresenceStampController::class, 'upcoming'])->name('presence.upcoming');
    Route::post('/presence/auto-update', [PresenceStampController::class, 'autoUpdateStatuses'])->name('presence.auto-update');

    // Shift logbooks
    Route::get('/logbooks/{schedule}', [ShiftLogbookController::class, 'index'])->name('logbooks.index');
    Route::post('/logbooks/{schedule}', [ShiftLogbookController::class, 'store'])->name('logbooks.store');
    Route::put('/logbooks/entry/{logbook}', [ShiftLogbookController::class, 'update'])->name('logbooks.update');
    Route::delete('/logbooks/entry/{logbook}', [ShiftLogbookController::class, 'destroy'])->name('logbooks.destroy');

    // Kanban board (all roles view/move; admin create/edit/delete)
    Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban.index');
    Route::post('/kanban/cards', [KanbanController::class, 'store'])->name('kanban.store');
    Route::post('/kanban/reorder', [KanbanController::class, 'reorder'])->name('kanban.reorder');
    Route::get('/kanban/cards/{card}', [KanbanController::class, 'show'])->name('kanban.show');
    Route::put('/kanban/cards/{card}', [KanbanController::class, 'update'])->name('kanban.update');
    Route::delete('/kanban/cards/{card}', [KanbanController::class, 'destroy'])->name('kanban.destroy');

    // Widget (compact PWA page)
    Route::get('/widget', [WidgetController::class, 'index'])->name('widget.index');
    Route::get('/widget/data', [WidgetController::class, 'data'])->name('widget.data');

    // Admin-only: Settings + Approvals
    Route::middleware('admin')->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/settings/data', [SettingController::class, 'get'])->name('settings.data');

        // Schedule approvals
        Route::get('/admin/approvals', [AdminApprovalController::class, 'index'])->name('admin.approvals.index');
        Route::get('/admin/approvals/list', [AdminApprovalController::class, 'list'])->name('admin.approvals.list');
        Route::post('/admin/approvals/bulk-approve', [AdminApprovalController::class, 'bulkApprove'])->name('admin.approvals.bulk-approve');
        Route::post('/admin/approvals/bulk-reject', [AdminApprovalController::class, 'bulkReject'])->name('admin.approvals.bulk-reject');
        Route::post('/admin/approvals/{schedule}/approve', [AdminApprovalController::class, 'approve'])->name('admin.approvals.approve');
        Route::post('/admin/approvals/{schedule}/reject', [AdminApprovalController::class, 'reject'])->name('admin.approvals.reject');
        Route::delete('/admin/approvals/{schedule}', [AdminApprovalController::class, 'destroy'])->name('admin.approvals.destroy');

        // Logbook review
        Route::get('/admin/logbooks', [AdminLogbookController::class, 'index'])->name('admin.logbooks.index');
        Route::get('/admin/logbooks/list', [AdminLogbookController::class, 'list'])->name('admin.logbooks.list');

        // Internship reports
        Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/admin/reports/{intern}', [AdminReportController::class, 'show'])->name('admin.reports.show');
        Route::get('/admin/reports/{intern}/pdf', [AdminReportController::class, 'exportPdf'])->name('admin.reports.pdf');

        // User management
        Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/list', [AdminUserController::class, 'list'])->name('admin.users.list');
        Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
        Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

        // Schedule assignment (admin assigns to interns)
        Route::get('/admin/schedule-assign', [AdminScheduleAssignController::class, 'index'])->name('admin.schedule-assign.index');
        Route::post('/admin/schedule-assign', [AdminScheduleAssignController::class, 'store'])->name('admin.schedule-assign.store');
        Route::delete('/admin/schedule-assign/{schedule}', [AdminScheduleAssignController::class, 'destroy'])->name('admin.schedule-assign.destroy');

        // Performance monitoring
        Route::get('/admin/performance', [AdminPerformanceController::class, 'index'])->name('admin.performance.index');
        Route::get('/admin/performance/data', [AdminPerformanceController::class, 'data'])->name('admin.performance.data');
    });
});

require __DIR__.'/auth.php';
