<x-app-layout>
@push('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-calendar.css') }}" />
<style>
.fc .fc-timegrid-slot { height: 3em; }
.weekly-hours-bar { transition: width 0.5s ease; }
</style>
@endpush

<div class="card app-calendar-wrapper">
    <div class="row g-0">
        <!-- Calendar Sidebar -->
        <div class="col app-calendar-sidebar border-end" id="app-calendar-sidebar">
            <div class="border-bottom p-6 my-sm-0 mb-4">
                <button class="btn btn-primary btn-toggle-sidebar w-100" id="addScheduleBtn">
                    <i class="ti ti-plus ti-16px me-2"></i>
                    <span class="align-middle">Add Schedule</span>
                </button>
            </div>
            <div class="px-6 pt-2">
                <!-- Weekly Hours Summary -->
                <div class="mb-4">
                    <h6 class="mb-2">Weekly Hours</h6>
                    <div class="d-flex justify-content-between small mb-1">
                        <span id="usedHoursLabel">0 hrs used</span>
                        <span id="maxHoursLabel">{{ $maxHours }} hrs max</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar weekly-hours-bar" id="hoursProgressBar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="text-muted" id="remainingHoursLabel">-- hrs remaining</small>
                </div>
            </div>
            <hr class="mb-6 mx-n4 mt-3">
            <div class="px-6 pb-2">
                <div>
                    <h5>Filter</h5>
                </div>
                <div class="form-check form-check-secondary mb-5 ms-2">
                    <input class="form-check-input" type="checkbox" id="filterAllUsers" checked>
                    <label class="form-check-label" for="filterAllUsers">View All Interns</label>
                </div>
                <div class="mb-4">
                    <h6 class="mb-2">Status</h6>
                    <div class="form-check form-check-primary mb-2 ms-2">
                        <input class="form-check-input status-filter" type="checkbox" value="not_yet" id="filterNotYet" checked>
                        <label class="form-check-label" for="filterNotYet">Not Yet</label>
                    </div>
                    <div class="form-check form-check-warning mb-2 ms-2">
                        <input class="form-check-input status-filter" type="checkbox" value="ongoing" id="filterOngoing" checked>
                        <label class="form-check-label" for="filterOngoing">Ongoing</label>
                    </div>
                    <div class="form-check form-check-success mb-2 ms-2">
                        <input class="form-check-input status-filter" type="checkbox" value="done" id="filterDone" checked>
                        <label class="form-check-label" for="filterDone">Done</label>
                    </div>
                    <div class="form-check form-check-danger mb-2 ms-2">
                        <input class="form-check-input status-filter" type="checkbox" value="late" id="filterLate" checked>
                        <label class="form-check-label" for="filterLate">Late</label>
                    </div>
                    <div class="form-check mb-2 ms-2">
                        <input class="form-check-input status-filter" type="checkbox" value="absence" id="filterAbsence" checked>
                        <label class="form-check-label" for="filterAbsence">Absence</label>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Calendar Sidebar -->

        <!-- Calendar & Modal -->
        <div class="col app-calendar-content">
            <div class="card shadow-none border-0">
                <div class="card-body pb-0">
                    <div id="calendar"></div>
                </div>
            </div>
            <div class="app-overlay"></div>
        </div>
    </div>
</div>

<!-- Add/Edit Schedule Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalTitle">Add Schedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="scheduleForm">
                    <input type="hidden" id="scheduleId">
                    <div class="mb-4">
                        <label class="form-label" for="scheduleStartDate">Start Date & Time</label>
                        <input type="text" class="form-control" id="scheduleStartDate" placeholder="Select start date/time" />
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="scheduleEndDate">End Date & Time</label>
                        <input type="text" class="form-control" id="scheduleEndDate" placeholder="Select end date/time" />
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="scheduleCaption">Caption</label>
                        <input type="text" class="form-control" id="scheduleCaption" placeholder="What will you be working on?" maxlength="255" />
                    </div>
                    <div id="scheduleHoursWarning" class="alert alert-warning d-none">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <span id="scheduleHoursWarningText"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-danger d-none" id="deleteScheduleBtn">
                    <i class="ti ti-trash me-1"></i>Delete
                </button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveScheduleBtn">
                    <i class="ti ti-device-floppy me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Detail Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="eventDetailBody"></div>
            <div class="modal-footer" id="eventDetailFooter"></div>
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

@push('page-js')
<script src="{{ asset('assets/vendor/libs/fullcalendar/fullcalendar.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const currentUserId = {{ Auth::id() }};
    const isAdmin = {{ Auth::user()->isAdmin() ? 'true' : 'false' }};
    let filterAllUsers = true;
    let calendar;

    // Flatpickr init
    const startPicker = flatpickr('#scheduleStartDate', {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        time_24hr: true,
        minuteIncrement: 15,
        onChange: function() { validateHours(); }
    });
    const endPicker = flatpickr('#scheduleEndDate', {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        time_24hr: true,
        minuteIncrement: 15,
        onChange: function() { validateHours(); }
    });

    // --- Weekly Hours ---
    function loadWeeklyHours() {
        const view = calendar.view;
        const date = view.currentStart.toISOString().split('T')[0];
        fetch(`{{ route('schedules.weekly-hours') }}?date=${date}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const pct = Math.min(100, (data.used_hours / data.max_hours) * 100);
                document.getElementById('usedHoursLabel').textContent = data.used_hours + ' hrs used';
                document.getElementById('maxHoursLabel').textContent = data.max_hours + ' hrs max';
                document.getElementById('remainingHoursLabel').textContent = data.remaining_hours + ' hrs remaining';
                const bar = document.getElementById('hoursProgressBar');
                bar.style.width = pct + '%';
                bar.className = 'progress-bar weekly-hours-bar bg-' + (pct >= 90 ? 'danger' : pct >= 70 ? 'warning' : 'success');
            });
    }

    function validateHours() {
        const start = document.getElementById('scheduleStartDate').value;
        const end = document.getElementById('scheduleEndDate').value;
        const warning = document.getElementById('scheduleHoursWarning');
        if (!start || !end) { warning.classList.add('d-none'); return; }

        const diffMs = new Date(end) - new Date(start);
        if (diffMs <= 0) {
            warning.classList.remove('d-none');
            document.getElementById('scheduleHoursWarningText').textContent = 'End time must be after start time.';
            return;
        }
        const newHours = (diffMs / 3600000).toFixed(2);
        document.getElementById('scheduleHoursWarningText').textContent = `This schedule is ${newHours} hours.`;
        warning.classList.remove('d-none');
        warning.className = 'alert alert-info';
    }

    // --- Calendar ---
    const calendarEl = document.getElementById('calendar');
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
        selectable: true,
        height: 'auto',
        nowIndicator: true,
        select: function(info) {
            openAddModal(info.startStr, info.endStr);
        },
        eventClick: function(info) {
            showEventDetail(info.event);
        },
        datesSet: function() {
            loadWeeklyHours();
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            let url = '{{ route("schedules.events") }}?start=' + fetchInfo.startStr + '&end=' + fetchInfo.endStr;
            if (!filterAllUsers) url += '&user_id=' + currentUserId;
            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(events => {
                    // Apply status filters
                    const checkedStatuses = Array.from(document.querySelectorAll('.status-filter:checked')).map(el => el.value);
                    const filtered = events.filter(e => checkedStatuses.includes(e.extendedProps.status));
                    successCallback(filtered);
                })
                .catch(err => failureCallback(err));
        }
    });
    calendar.render();

    // --- Sidebar Filters ---
    document.getElementById('filterAllUsers').addEventListener('change', function() {
        filterAllUsers = this.checked;
        calendar.refetchEvents();
    });
    document.querySelectorAll('.status-filter').forEach(el => {
        el.addEventListener('change', () => calendar.refetchEvents());
    });

    // --- Add Schedule Modal ---
    const scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));

    document.getElementById('addScheduleBtn').addEventListener('click', function() {
        openAddModal();
    });

    function openAddModal(start, end) {
        document.getElementById('scheduleModalTitle').textContent = 'Add Schedule';
        document.getElementById('scheduleId').value = '';
        document.getElementById('scheduleCaption').value = '';
        document.getElementById('deleteScheduleBtn').classList.add('d-none');
        document.getElementById('scheduleHoursWarning').classList.add('d-none');

        if (start) startPicker.setDate(start, true);
        else startPicker.clear();
        if (end) endPicker.setDate(end, true);
        else endPicker.clear();

        scheduleModal.show();
    }

    function openEditModal(eventData) {
        const props = eventData.extendedProps;
        document.getElementById('scheduleModalTitle').textContent = 'Edit Schedule';
        document.getElementById('scheduleId').value = props.schedule_id;
        document.getElementById('scheduleCaption').value = props.caption || '';
        document.getElementById('deleteScheduleBtn').classList.remove('d-none');
        document.getElementById('scheduleHoursWarning').classList.add('d-none');

        startPicker.setDate(eventData.start, true);
        endPicker.setDate(eventData.end, true);

        scheduleModal.show();
    }

    // --- Save Schedule ---
    document.getElementById('saveScheduleBtn').addEventListener('click', function() {
        const id = document.getElementById('scheduleId').value;
        const data = {
            start_shift: document.getElementById('scheduleStartDate').value,
            end_shift: document.getElementById('scheduleEndDate').value,
            caption: document.getElementById('scheduleCaption').value,
        };

        const url = id ? `/schedules/${id}` : '{{ route("schedules.store") }}';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(data),
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            if (ok) {
                showToast(data.message, 'success');
                scheduleModal.hide();
                calendar.refetchEvents();
                loadWeeklyHours();
            } else {
                showToast(data.message || 'Validation error', 'danger');
            }
        });
    });

    // --- Delete Schedule ---
    document.getElementById('deleteScheduleBtn').addEventListener('click', function() {
        const id = document.getElementById('scheduleId').value;
        if (!id || !confirm('Delete this schedule?')) return;

        fetch(`/schedules/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            showToast(data.message, ok ? 'success' : 'danger');
            if (ok) {
                scheduleModal.hide();
                calendar.refetchEvents();
                loadWeeklyHours();
            }
        });
    });

    // --- Event Detail Modal ---
    let _countdownTimer = null;

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

    function showEventDetail(event) {
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

        document.getElementById('eventDetailBody').innerHTML = `
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">${props.user_name}</h6>
                    ${statusBadge[props.status] || ''}
                </div>
                ${props.is_assigned ? `<div class="mb-2"><span class="badge rounded-pill" style="background:#e83e8c;color:#fff;font-size:.72rem"><i class="ti ti-user-check me-1"></i>Assigned by ${props.assigned_by_name ?? 'Admin'}</span></div>` : ''}
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
            <div id="detailCountdownWrap" class="mb-3"></div>
            <div class="d-flex gap-2">
                <span class="badge bg-label-${props.has_entry ? 'success' : 'secondary'}">
                    <i class="ti ti-${props.has_entry ? 'check' : 'x'} me-1"></i>Entry
                </span>
                <span class="badge bg-label-${props.has_exit ? 'success' : 'secondary'}">
                    <i class="ti ti-${props.has_exit ? 'check' : 'x'} me-1"></i>Exit
                </span>
            </div>
            ${props.approval_status !== 'approved' ? `
            <div class="mt-3">
                ${props.approval_status === 'pending'
                    ? '<div class="alert alert-warning py-2 mb-0"><i class="ti ti-clock me-1"></i><strong>Pending Approval</strong> — Awaiting admin review. Presence stamps are disabled until approved.</div>'
                    : '<div class="alert alert-secondary py-2 mb-0"><i class="ti ti-x me-1"></i><strong>Rejected</strong> — This schedule was rejected. You may delete and resubmit.</div>'}
            </div>` : ''}
        `;

        // Live countdown
        if (_countdownTimer) clearInterval(_countdownTimer);
        function tickCountdown() {
            const wrap = document.getElementById('detailCountdownWrap');
            if (!wrap) { clearInterval(_countdownTimer); return; }
            const now = Date.now();
            const diffStart = startDate.getTime() - now;
            const diffEnd   = endDate.getTime() - now;
            let html = '';
            if (diffStart > 0) {
                html = `<div class="alert alert-primary py-2 mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-hourglass ti-md"></i>
                    <div><small class="d-block text-muted">Starts in</small>
                    <span class="fw-bold" id="cdValue">${formatCountdown(diffStart)}</span></div></div>`;
            } else if (diffEnd > 0) {
                html = `<div class="alert alert-warning py-2 mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-clock-play ti-md"></i>
                    <div><small class="d-block text-muted">In progress — ends in</small>
                    <span class="fw-bold">${formatCountdown(diffEnd)}</span></div></div>`;
            } else {
                html = `<div class="alert alert-secondary py-2 mb-0 d-flex align-items-center gap-2">
                    <i class="ti ti-clock-off ti-md"></i>
                    <span class="fw-bold">Schedule has ended</span></div>`;
                clearInterval(_countdownTimer);
            }
            wrap.innerHTML = html;
        }
        tickCountdown();
        _countdownTimer = setInterval(tickCountdown, 1000);

        let footerHtml = '';
        // Owner can edit self-signed schedules that are not finalized (assigned schedules are not editable by intern)
        if (props.user_id == currentUserId && ['not_yet'].includes(props.status) && !props.is_assigned) {
            footerHtml += `<button class="btn btn-outline-primary" id="editEventBtn"><i class="ti ti-pencil me-1"></i>Edit</button>`;
        }
        // Owner can open logbook if entry was stamped
        if (props.user_id == currentUserId && props.has_entry) {
            footerHtml += `<button class="btn btn-outline-info" id="logbookEventBtn"><i class="ti ti-notebook me-1"></i>Logbook</button>`;
        }
        footerHtml += `<button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>`;
        document.getElementById('eventDetailFooter').innerHTML = footerHtml;

        const detailModal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
        detailModal.show();

        document.getElementById('eventDetailModal').addEventListener('hidden.bs.modal', () => {
            if (_countdownTimer) { clearInterval(_countdownTimer); _countdownTimer = null; }
        }, { once: true });

        // Bind edit button
        document.getElementById('editEventBtn')?.addEventListener('click', function() {
            detailModal.hide();
            setTimeout(() => openEditModal(event), 300);
        });

        // Bind logbook button
        document.getElementById('logbookEventBtn')?.addEventListener('click', function() {
            detailModal.hide();
            setTimeout(() => openLogbook(props.schedule_id), 300);
        });
    }

    // --- Logbook ---
    let currentLogbookScheduleId = null;
    const logbookModalEl = new bootstrap.Modal(document.getElementById('logbookModal'));

    function openLogbook(scheduleId) {
        currentLogbookScheduleId = scheduleId;
        document.getElementById('logbookContent').value = '';
        loadLogbookEntries(scheduleId);
        logbookModalEl.show();
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

    // --- Toast ---
    function showToast(message, type) {
        const container = document.getElementById('toastContainer') || createToastContainer();
        const id = 'toast-' + Date.now();
        container.insertAdjacentHTML('beforeend', `
        <div id="${id}" class="bs-toast toast fade show bg-${type}" role="alert">
            <div class="toast-header">
                <i class="ti ti-${type === 'success' ? 'check' : 'alert-circle'} me-2 text-${type}"></i>
                <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body text-white">${message}</div>
        </div>`);
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
});
</script>
@endpush
</x-app-layout>
