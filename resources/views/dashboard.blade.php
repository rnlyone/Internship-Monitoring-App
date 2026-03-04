<x-app-layout>
@push('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-calendar.css') }}" />
<style>
.presence-card { animation: pulseGlow 2s ease-in-out infinite; }
@@keyframes pulseGlow {
    0%, 100% { box-shadow: 0 0 5px rgba(115,103,240,.3); }
    50% { box-shadow: 0 0 20px rgba(115,103,240,.6); }
}
.fc .fc-timegrid-slot { height: 3em; }
</style>
@endpush

<div class="row g-6">
    <!-- Welcome Card -->
    <div class="col-xl-4">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-7">
                    <div class="card-body text-nowrap">
                        <h5 class="card-title mb-0">Welcome {{ Auth::user()->name }}! 🎉</h5>
                        <p class="mb-2">
                            <span class="badge bg-label-{{ Auth::user()->isAdmin() ? 'danger' : 'primary' }}">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </p>
                        <h4 class="text-primary mb-1" id="weeklyHoursDisplay">-- / -- hrs</h4>
                        <p class="small text-muted mb-2">Weekly Hours Used</p>
                        <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-primary">View Schedule</a>
                    </div>
                </div>
                <div class="col-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="{{ asset('assets/img/illustrations/card-advance-sale.png') }}" height="140" alt="Welcome">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title mb-0">
                    <h5 class="mb-1">Weekly Statistics</h5>
                    <p class="card-subtitle" id="weekRange">This Week</p>
                </div>
            </div>
            <div class="card-body d-flex align-items-end">
                <div class="w-100">
                    <div class="row gy-3">
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="ti ti-calendar-event ti-26px"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-0" id="statTotal">0</h5>
                                    <span>Schedules</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="ti ti-circle-check ti-26px"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-0" id="statCompleted">0</h5>
                                    <span>Completed</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded">
                                        <i class="ti ti-clock ti-26px"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-0" id="statLate">0</h5>
                                    <span>Late</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-danger rounded">
                                        <i class="ti ti-x ti-26px"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-0" id="statAbsence">0</h5>
                                    <span>Absence</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Presence Reminder Cards (dynamically populated) -->
    <div class="col-12" id="presenceReminders"></div>

    <!-- Active Shift Needing Exit (dynamically populated) -->
    <div class="col-12" id="exitReminders"></div>

    <!-- Upcoming Shifts Reminder -->
    <div class="col-12" id="upcomingShiftsSection"></div>

    <!-- Calendar Summary -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ti ti-calendar me-2"></i>Schedule Overview</h5>
                <div class="d-flex gap-2 align-items-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="filterMySchedules">
                        <label class="form-check-label" for="filterMySchedules">My Schedules Only</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="dashboardCalendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Logbook Modal -->
<div class="modal fade" id="logbookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Shift Logbook</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="logbookEntries" class="mb-4"></div>
                <div id="logbookForm">
                    <div class="mb-3">
                        <label class="form-label">Add Logbook Entry</label>
                        <textarea class="form-control" id="logbookContent" rows="4" placeholder="What did you work on during this shift?"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" id="saveLogbookBtn">
                        <i class="ti ti-device-floppy me-1"></i>Save Entry
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Detail Modal -->
<div class="modal fade" id="scheduleDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="scheduleDetailBody"></div>
            <div class="modal-footer" id="scheduleDetailFooter"></div>
        </div>
    </div>
</div>

@push('page-js')
<script src="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const currentUserId = {{ Auth::id() }};
    const isAdmin = {{ Auth::user()->isAdmin() ? 'true' : 'false' }};
    let filterMine = false;
    let calendar;

    // --- Load Dashboard Stats ---
    function loadStats() {
        fetch('{{ route("dashboard.data") }}', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                document.getElementById('weeklyHoursDisplay').textContent = data.used_hours + ' / ' + data.max_hours + ' hrs';
                document.getElementById('statTotal').textContent = data.total_schedules;
                document.getElementById('statCompleted').textContent = data.completed;
                document.getElementById('statLate').textContent = data.late;
                document.getElementById('statAbsence').textContent = data.absence;
            });
    }

    // --- Presence Reminders ---
    function loadPresenceReminders() {
        fetch('{{ route("presence.upcoming") }}', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                let html = '';

                // Upcoming schedules needing entry
                data.upcoming.forEach(s => {
                    if (!s.has_entry) {
                        const startTime = new Date(s.start_shift).toLocaleTimeString('en-GB', {hour:'2-digit', minute:'2-digit', timeZone:'Asia/Singapore'});
                        const endTime = new Date(s.end_shift).toLocaleTimeString('en-GB', {hour:'2-digit', minute:'2-digit', timeZone:'Asia/Singapore'});
                        const minText = s.minutes_until_start > 0
                            ? `Starts in ${s.minutes_until_start} minutes`
                            : `Started ${Math.abs(s.minutes_until_start)} minutes ago`;
                        const urgency = s.minutes_until_start <= 0 ? 'danger' : (s.minutes_until_start <= 15 ? 'warning' : 'primary');

                        html += `
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card border-${urgency} presence-card">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <span class="avatar avatar-lg">
                                            <span class="avatar-initial rounded-circle bg-label-${urgency}">
                                                <i class="ti ti-bell-ringing ti-32px"></i>
                                            </span>
                                        </span>
                                    </div>
                                    <h4 class="text-${urgency}">${minText}</h4>
                                    <p class="mb-1"><strong>${startTime} - ${endTime}</strong></p>
                                    <p class="text-muted small mb-3">${s.caption || 'No caption'}</p>
                                    <button class="btn btn-${urgency} btn-lg w-100 stamp-entry-btn" data-id="${s.id}">
                                        <i class="ti ti-fingerprint me-2"></i>Stamp Entry
                                    </button>
                                </div>
                            </div>
                        </div>`;
                    }
                });

                document.getElementById('presenceReminders').innerHTML = html ? '<div class="row">' + html + '</div>' : '';

                // Exit reminders
                let exitHtml = '';
                data.needing_exit.forEach(s => {
                    const startTime = new Date(s.start_shift).toLocaleTimeString('en-GB', {hour:'2-digit', minute:'2-digit', timeZone:'Asia/Singapore'});
                    const endTime = new Date(s.end_shift).toLocaleTimeString('en-GB', {hour:'2-digit', minute:'2-digit', timeZone:'Asia/Singapore'});
                    const exitTime = new Date(s.earliest_exit).toLocaleTimeString('en-GB', {hour:'2-digit', minute:'2-digit', timeZone:'Asia/Singapore'});
                    const btnClass = s.can_exit ? 'btn-success' : 'btn-secondary disabled';

                    exitHtml += `
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card border-info">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded-circle bg-label-info">
                                            <i class="ti ti-clock"></i>
                                        </span>
                                    </span>
                                    <div>
                                        <h6 class="mb-0">Active Shift</h6>
                                        <small class="text-muted">${startTime} - ${endTime}</small>
                                    </div>
                                    <span class="badge bg-label-${s.status === 'late' ? 'warning' : 'success'} ms-auto">${s.status}</span>
                                </div>
                                <p class="small mb-2">${s.caption || 'No caption'}</p>
                                <p class="small text-muted mb-3">Earliest exit: <strong>${exitTime}</strong></p>
                                <div class="d-flex gap-2">
                                    <button class="btn ${btnClass} flex-grow-1 stamp-exit-btn" data-id="${s.id}" ${s.can_exit ? '' : 'disabled'}>
                                        <i class="ti ti-logout me-1"></i>${s.can_exit ? 'Stamp Exit' : 'Cannot exit yet'}
                                    </button>
                                    <button class="btn btn-outline-primary open-logbook-btn" data-id="${s.id}" title="Write Logbook">
                                        <i class="ti ti-notebook"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });

                document.getElementById('exitReminders').innerHTML = exitHtml ? '<div class="row">' + exitHtml + '</div>' : '';

                // Bind stamp buttons
                document.querySelectorAll('.stamp-entry-btn').forEach(btn => {
                    btn.addEventListener('click', function() { stampEntry(this.dataset.id); });
                });
                document.querySelectorAll('.stamp-exit-btn').forEach(btn => {
                    btn.addEventListener('click', function() { stampExit(this.dataset.id); });
                });
                document.querySelectorAll('.open-logbook-btn').forEach(btn => {
                    btn.addEventListener('click', function() { openLogbook(this.dataset.id); });
                });
            });
    }

    // --- Upcoming Shifts Reminder ---
    function loadUpcomingShifts() {
        fetch('{{ route("dashboard.upcoming-shifts") }}', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const section = document.getElementById('upcomingShiftsSection');
                if (!data.shifts || data.shifts.length === 0) {
                    section.innerHTML = '';
                    return;
                }

                const rows = data.shifts.map((s, idx) => {
                    const startDate = new Date(s.start_iso);
                    const endDate   = new Date(s.end_iso);
                    const now       = Date.now();
                    const diffMs    = startDate.getTime() - now;
                    const isOngoing = s.status === 'ongoing' || (startDate.getTime() <= now && endDate.getTime() > now);

                    // Build countdown text
                    let countdownHtml = '';
                    if (isOngoing) {
                        const leftMs = endDate.getTime() - now;
                        countdownHtml = `<span class="badge bg-label-warning ms-2"><i class="ti ti-clock-play ti-xs me-1"></i>In progress</span>`;
                    } else if (diffMs > 0) {
                        const totalMin = Math.floor(diffMs / 60000);
                        const h = Math.floor(totalMin / 60);
                        const m = totalMin % 60;
                        const txt = h > 0 ? `${h}h ${m}m` : `${m}m`;
                        const urgency = totalMin <= 60 ? 'danger' : (totalMin <= 360 ? 'warning' : 'info');
                        countdownHtml = `<span class="badge bg-label-${urgency} ms-2"><i class="ti ti-hourglass ti-xs me-1"></i>in ${txt}</span>`;
                    }

                    const isFirst = idx === 0;
                    const assignedBadge = s.is_assigned
                        ? `<span class="badge rounded-pill ms-1" style="background:#00bad1;color:#fff;font-size:.62rem">Assigned</span>`
                        : '';

                    const dayColor = s.day_label === 'Today' ? 'danger' : (s.day_label === 'Tomorrow' ? 'warning' : 'secondary');

                    return `
                    <div class="d-flex align-items-center gap-3 py-3 ${idx < data.shifts.length - 1 ? 'border-bottom' : ''}">
                        <!-- Day label pill -->
                        <div class="text-center flex-shrink-0" style="min-width:58px">
                            <span class="badge bg-label-${dayColor} d-block mb-1" style="font-size:.7rem">${s.day_label}</span>
                            <small class="text-muted" style="font-size:.65rem">${s.date_fmt}</small>
                        </div>

                        <!-- Time block -->
                        <div class="avatar avatar-sm flex-shrink-0 ${isFirst ? '' : ''}">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                <i class="ti ti-${isOngoing ? 'clock-play' : 'alarm'} ti-sm"></i>
                            </span>
                        </div>

                        <!-- Info -->
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center flex-wrap gap-1">
                                <span class="fw-semibold" style="font-size:.88rem">${s.time_start} &ndash; ${s.time_end}</span>
                                <span class="text-muted" style="font-size:.75rem">(${s.duration_hours}h)</span>
                                ${assignedBadge}
                                ${countdownHtml}
                            </div>
                            <div class="text-muted text-truncate" style="font-size:.78rem">${s.caption || '<em>No caption</em>'}</div>
                        </div>

                        <!-- View link -->
                        <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-icon btn-text-primary flex-shrink-0" title="View on calendar">
                            <i class="ti ti-calendar-event"></i>
                        </a>
                    </div>`;
                }).join('');

                section.innerHTML = `
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar avatar-sm rounded">
                                <span class="avatar-initial bg-label-primary rounded">
                                    <i class="ti ti-calendar-time"></i>
                                </span>
                            </span>
                            <h5 class="card-title mb-0">Upcoming Shifts</h5>
                        </div>
                        <span class="badge bg-label-primary">${data.shifts.length} shift${data.shifts.length !== 1 ? 's' : ''} ahead</span>
                    </div>
                    <div class="card-body py-0 px-4">
                        ${rows}
                    </div>
                </div>`;
            })
            .catch(() => {});
    }

    // --- Stamp Entry ---
    function stampEntry(scheduleId) {
        fetch(`/presence/${scheduleId}/entry`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            showToast(data.message, ok ? 'success' : 'danger');
            if (ok) { loadPresenceReminders(); loadStats(); calendar.refetchEvents(); }
        });
    }

    // --- Stamp Exit ---
    function stampExit(scheduleId) {
        fetch(`/presence/${scheduleId}/exit`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            showToast(data.message, ok ? 'success' : 'danger');
            if (ok) { loadPresenceReminders(); loadStats(); calendar.refetchEvents(); }
        });
    }

    // --- Logbook ---
    let currentLogbookScheduleId = null;

    function openLogbook(scheduleId) {
        currentLogbookScheduleId = scheduleId;
        document.getElementById('logbookContent').value = '';
        loadLogbookEntries(scheduleId);
        new bootstrap.Modal(document.getElementById('logbookModal')).show();
    }

    function loadLogbookEntries(scheduleId) {
        fetch(`/logbooks/${scheduleId}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const container = document.getElementById('logbookEntries');
                if (data.logbooks.length === 0) {
                    container.innerHTML = '<p class="text-muted text-center">No logbook entries yet.</p>';
                    return;
                }
                let html = '';
                data.logbooks.forEach(l => {
                    const time = new Date(l.created_at).toLocaleString('en-GB', { timeZone: 'Asia/Singapore' });
                    html += `
                    <div class="card mb-2 bg-lighter">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <small class="text-muted">${time}</small>
                                <button class="btn btn-sm btn-icon btn-text-danger delete-logbook-btn" data-id="${l.id}">
                                    <i class="ti ti-trash ti-sm"></i>
                                </button>
                            </div>
                            <p class="mb-0">${l.content.replace(/\n/g, '<br>')}</p>
                        </div>
                    </div>`;
                });
                container.innerHTML = html;
                container.querySelectorAll('.delete-logbook-btn').forEach(btn => {
                    btn.addEventListener('click', function() { deleteLogbook(this.dataset.id); });
                });
            });
    }

    document.getElementById('saveLogbookBtn').addEventListener('click', function() {
        const content = document.getElementById('logbookContent').value.trim();
        if (!content) return;

        fetch(`/logbooks/${currentLogbookScheduleId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ content }),
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            showToast(data.message, ok ? 'success' : 'danger');
            if (ok) {
                document.getElementById('logbookContent').value = '';
                loadLogbookEntries(currentLogbookScheduleId);
            }
        });
    });

    function deleteLogbook(logbookId) {
        if (!confirm('Delete this logbook entry?')) return;
        fetch(`/logbooks/entry/${logbookId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            showToast(data.message, ok ? 'success' : 'danger');
            if (ok) loadLogbookEntries(currentLogbookScheduleId);
        });
    }

    // --- Calendar ---
    const calendarEl = document.getElementById('dashboardCalendar');
    calendar = new Calendar(calendarEl, {
        plugins: [timegridPlugin, listPlugin, interactionPlugin],
        timeZone: 'Asia/Singapore',
        initialView: 'timeGridWeek',
        headerToolbar: {
            start: 'prev,next today',
            center: 'title',
            end: 'timeGridWeek,timeGridDay,listWeek'
        },
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
        allDaySlot: false,
        editable: false,
        selectable: false,
        height: 'auto',
        eventClick: function(info) {
            showScheduleDetail(info.event);
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            let url = '{{ route("schedules.events") }}?start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr;
            if (filterMine) url += '&user_id=' + currentUserId;
            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(events => successCallback(events))
                .catch(err => failureCallback(err));
        }
    });
    calendar.render();

    // Filter toggle
    document.getElementById('filterMySchedules').addEventListener('change', function() {
        filterMine = this.checked;
        calendar.refetchEvents();
    });

    // --- Schedule Detail Modal ---
    let _dashCountdownTimer = null;

    function formatCountdown(diffMs) {
        if (diffMs <= 0) return null;
        const totalSec = Math.floor(diffMs / 1000);
        const h = Math.floor(totalSec / 3600);
        const m = Math.floor((totalSec % 3600) / 60);
        const s = totalSec % 60;
        const parts = [];
        if (h > 0) parts.push(h + 'h');
        if (m > 0 || h > 0) parts.push(m + 'm');
        parts.push(s + 's');
        return parts.join(' ');
    }

    function showScheduleDetail(event) {
        const props = event.extendedProps;
        // Use raw ISO strings from extendedProps — event.start/end are FullCalendar fake-local
        // dates that break toLocaleString on browsers not in Asia/Singapore.
        const startDate = new Date(props.start_iso);
        const endDate   = new Date(props.end_iso);
        const start = startDate.toLocaleString('en-GB', { timeZone: 'Asia/Singapore' });
        const end   = endDate.toLocaleString('en-GB', { timeZone: 'Asia/Singapore' });
        const statusBadge = {
            'not_yet': '<span class="badge bg-label-primary">Not Yet</span>',
            'ongoing': '<span class="badge bg-label-warning">Ongoing</span>',
            'done': '<span class="badge bg-label-success">Done</span>',
            'late': '<span class="badge bg-label-danger">Late</span>',
            'absence': '<span class="badge bg-label-secondary">Absence</span>',
        };

        document.getElementById('scheduleDetailBody').innerHTML = `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">${props.user_name}</h6>
                    ${statusBadge[props.status] || ''}
                </div>
                <p class="text-muted mb-1">${props.caption || 'No caption'}</p>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <small class="text-muted d-block">Start</small>
                    <strong>${start}</strong>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">End</small>
                    <strong>${end}</strong>
                </div>
            </div>
            <div class="mb-3">
                <small class="text-muted d-block">Duration</small>
                <strong>${props.duration_hours} hours</strong>
            </div>
            <div id="dashDetailCountdownWrap" class="mb-3"></div>
            <div class="d-flex gap-2">
                <span class="badge bg-label-${props.has_entry ? 'success' : 'secondary'}">
                    <i class="ti ti-${props.has_entry ? 'check' : 'x'} me-1"></i>Entry
                </span>
                <span class="badge bg-label-${props.has_exit ? 'success' : 'secondary'}">
                    <i class="ti ti-${props.has_exit ? 'check' : 'x'} me-1"></i>Exit
                </span>
            </div>
        `;

        // Live countdown
        if (_dashCountdownTimer) clearInterval(_dashCountdownTimer);
        function tickCountdown() {
            const wrap = document.getElementById('dashDetailCountdownWrap');
            if (!wrap) { clearInterval(_dashCountdownTimer); return; }
            const now = Date.now();
            const diffStart = startDate.getTime() - now;
            const diffEnd   = endDate.getTime() - now;
            let html = '';
            if (diffStart > 0) {
                html = `<div class="alert alert-primary py-2 mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-hourglass ti-md"></i>
                    <div><small class="d-block text-muted">Starts in</small>
                    <span class="fw-bold">${formatCountdown(diffStart)}</span></div></div>`;
            } else if (diffEnd > 0) {
                html = `<div class="alert alert-warning py-2 mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-clock-play ti-md"></i>
                    <div><small class="d-block text-muted">In progress — ends in</small>
                    <span class="fw-bold">${formatCountdown(diffEnd)}</span></div></div>`;
            } else {
                html = `<div class="alert alert-secondary py-2 mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-clock-off ti-md"></i>
                    <span class="fw-bold">Schedule has ended</span></div>`;
                clearInterval(_dashCountdownTimer);
            }
            wrap.innerHTML = html;
        }
        tickCountdown();
        _dashCountdownTimer = setInterval(tickCountdown, 1000);

        let footerHtml = '';
        if (props.user_id == currentUserId && props.has_entry) {
            footerHtml += `<button class="btn btn-outline-primary" onclick="document.getElementById('scheduleDetailModal').querySelector('[data-bs-dismiss]').click(); setTimeout(() => { document.querySelector('.open-logbook-btn[data-id=&quot;${props.schedule_id}&quot;]')?.click() || openLogbookDirect('${props.schedule_id}'); }, 300);">
                <i class="ti ti-notebook me-1"></i>Logbook
            </button>`;
        }
        footerHtml += `<button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>`;
        document.getElementById('scheduleDetailFooter').innerHTML = footerHtml;

        const detailModalEl = document.getElementById('scheduleDetailModal');
        new bootstrap.Modal(detailModalEl).show();
        detailModalEl.addEventListener('hidden.bs.modal', () => {
            if (_dashCountdownTimer) { clearInterval(_dashCountdownTimer); _dashCountdownTimer = null; }
        }, { once: true });
    }

    // Direct logbook open (when no button exists on page)
    window.openLogbookDirect = function(scheduleId) {
        openLogbook(scheduleId);
    };

    // --- Toast Helper ---
    function showToast(message, type) {
        const container = document.getElementById('toastContainer') || createToastContainer();
        const id = 'toast-' + Date.now();
        const html = `
        <div id="${id}" class="bs-toast toast fade show bg-${type}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="ti ti-${type === 'success' ? 'check' : 'alert-circle'} me-2 text-${type}"></i>
                <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body text-white">${message}</div>
        </div>`;
        container.insertAdjacentHTML('beforeend', html);
        setTimeout(() => document.getElementById(id)?.remove(), 5000);
    }

    function createToastContainer() {
        const div = document.createElement('div');
        div.id = 'toastContainer';
        div.className = 'position-fixed top-0 end-0 p-3';
        div.style.zIndex = 9999;
        document.body.appendChild(div);
        return div;
    }

    // --- Auto update presence statuses ---
    function autoUpdate() {
        fetch('{{ route("presence.auto-update") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
    }

    // --- Init ---
    loadStats();
    loadPresenceReminders();
    loadUpcomingShifts();
    autoUpdate();

    // Refresh every 60 seconds
    setInterval(() => {
        loadPresenceReminders();
        loadUpcomingShifts();
        loadStats();
        autoUpdate();
    }, 60000);
});
</script>
@endpush
</x-app-layout>
