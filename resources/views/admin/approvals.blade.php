<x-app-layout>
@push('page-css')
<style>
.approval-row { transition: opacity 0.3s ease; }
.approval-row.processing { opacity: 0.5; pointer-events: none; }
.badge-approval-pending  { background: rgba(255,159,67,.15); color: #ff9f43; }
.badge-approval-approved { background: rgba(40,199,111,.15); color: #28c76f; }
.badge-approval-rejected { background: rgba(130,134,139,.15); color: #82868b; }
.reschedule-row { transition: opacity 0.3s ease; }
.reschedule-row.processing { opacity: 0.5; pointer-events: none; }
.time-arrow { color: #7367f0; font-size: 1.1rem; }
</style>
@endpush

{{-- ===== STAT CARDS ===== --}}
<div class="row">
    <div class="col-md-3 col-sm-6 mb-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="avatar avatar-lg">
                    <span class="avatar-initial rounded-circle bg-label-warning">
                        <i class="ti ti-clock-check ti-28px"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0" id="statPending">{{ $pendingCount }}</h5>
                    <small class="text-muted">Pending Approval</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="avatar avatar-lg">
                    <span class="avatar-initial rounded-circle bg-label-success">
                        <i class="ti ti-circle-check ti-28px"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0" id="statApproved">{{ $approvedCount }}</h5>
                    <small class="text-muted">Approved</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="avatar avatar-lg">
                    <span class="avatar-initial rounded-circle bg-label-secondary">
                        <i class="ti ti-circle-x ti-28px"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0" id="statRejected">{{ $rejectedCount }}</h5>
                    <small class="text-muted">Rejected</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="avatar avatar-lg">
                    <span class="avatar-initial rounded-circle bg-label-info">
                        <i class="ti ti-calendar-event ti-28px"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0" id="statReschedule">{{ $rescheduleCount }}</h5>
                    <small class="text-muted">Reschedule Requests</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== CARD 1: SCHEDULE SUBMISSIONS ===== --}}
<div class="card mb-6">
    <div class="card-header d-flex flex-wrap gap-3 align-items-center justify-content-between">
        <div>
            <h5 class="card-title mb-0">
                <i class="ti ti-calendar-check me-2"></i>Schedule Submissions
            </h5>
            <p class="card-subtitle mt-1">Review and approve intern schedule submissions</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <div id="bulkActions" class="d-none d-flex gap-2">
                <span class="text-muted align-self-center small" id="selectedCount">0 selected</span>
                <button class="btn btn-sm btn-success" id="bulkApproveBtn">
                    <i class="ti ti-check me-1"></i>Approve Selected
                </button>
                <button class="btn btn-sm btn-secondary" id="bulkRejectBtn">
                    <i class="ti ti-x me-1"></i>Reject Selected
                </button>
            </div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-primary tab-btn active" data-status="pending">
                    Pending <span class="badge bg-white text-primary ms-1" id="tabBadgePending">{{ $pendingCount }}</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-success tab-btn" data-status="approved">
                    Approved <span class="badge bg-success ms-1" id="tabBadgeApproved">{{ $approvedCount }}</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary tab-btn" data-status="rejected">
                    Rejected <span class="badge bg-secondary ms-1" id="tabBadgeRejected">{{ $rejectedCount }}</span>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40"><input type="checkbox" class="form-check-input" id="selectAll"></th>
                        <th>Intern</th>
                        <th>Schedule</th>
                        <th>Duration</th>
                        <th>Caption</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th class="text-center" id="actionsHeader">Actions</th>
                    </tr>
                </thead>
                <tbody id="approvalTableBody">
                    <tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== CARD 2: RESCHEDULE REQUESTS ===== --}}
<div class="card">
    <div class="card-header d-flex flex-wrap gap-3 align-items-center justify-content-between">
        <div>
            <h5 class="card-title mb-0">
                <i class="ti ti-calendar-event me-2"></i>Reschedule Requests
            </h5>
            <p class="card-subtitle mt-1">Interns requesting to reschedule their approved shifts</p>
        </div>
        <span class="badge bg-label-info fs-6" id="rescheduleCountBadge">{{ $rescheduleCount }} pending</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Intern</th>
                        <th>Current Schedule</th>
                        <th class="text-center time-arrow">→</th>
                        <th>Proposed Schedule</th>
                        <th>Δ Hours</th>
                        <th>Requested</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="rescheduleTableBody">
                    <tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-info" role="status"></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('page-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let currentStatus = 'pending';
    let schedules = [];
    let selectedIds = new Set();

    // =============================================
    // CARD 1: SCHEDULE SUBMISSIONS
    // =============================================

    function loadSchedules(status) {
        currentStatus = status;
        selectedIds.clear();
        updateBulkUI();

        const tbody = document.getElementById('approvalTableBody');
        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></td></tr>`;

        fetch(`{{ route('admin.approvals.list') }}?approval_status=${status}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            schedules = data.schedules;
            renderTable(schedules);
        });
    }

    function renderTable(list) {
        const tbody = document.getElementById('approvalTableBody');
        const isPending  = currentStatus === 'pending';
        const isApproved = currentStatus === 'approved';
        const isRejected = currentStatus === 'rejected';
        document.getElementById('actionsHeader').style.display = '';

        if (list.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="8" class="text-center py-5">
                    <div class="avatar avatar-lg mb-3">
                        <span class="avatar-initial rounded-circle bg-label-secondary">
                            <i class="ti ti-calendar-off ti-28px"></i>
                        </span>
                    </div>
                    <p class="text-muted mb-0">No ${currentStatus} schedules found.</p>
                </td></tr>`;
            return;
        }

        tbody.innerHTML = list.map(s => {
            const start    = new Date(s.start_shift);
            const end      = new Date(s.end_shift);
            const startStr = start.toLocaleDateString('en-GB', { weekday:'short', day:'2-digit', month:'short', year:'numeric', timeZone:'Asia/Singapore' });
            const timeRange = start.toLocaleTimeString('en-GB', { hour:'2-digit', minute:'2-digit', timeZone:'Asia/Singapore' })
                + ' – ' + end.toLocaleTimeString('en-GB', { hour:'2-digit', minute:'2-digit', timeZone:'Asia/Singapore' });
            const submitted = new Date(s.created_at).toLocaleString('en-GB', { day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit', timeZone:'Asia/Singapore' });
            const approvalBadge = {
                pending:  '<span class="badge badge-approval-pending"><i class="ti ti-clock me-1"></i>Pending</span>',
                approved: '<span class="badge badge-approval-approved"><i class="ti ti-check me-1"></i>Approved</span>',
                rejected: '<span class="badge badge-approval-rejected"><i class="ti ti-x me-1"></i>Rejected</span>',
            };

            const actions = isPending ? `
                <div class="d-flex gap-1 justify-content-center">
                    <button class="btn btn-sm btn-icon btn-success approve-btn" data-id="${s.id}" title="Approve"><i class="ti ti-check ti-sm"></i></button>
                    <button class="btn btn-sm btn-icon btn-secondary reject-btn" data-id="${s.id}" title="Reject"><i class="ti ti-x ti-sm"></i></button>
                </div>`
            : isApproved ? `
                <div class="d-flex gap-1 justify-content-center">
                    <button class="btn btn-sm btn-icon btn-warning reject-btn" data-id="${s.id}" title="Revoke approval"><i class="ti ti-arrow-back-up ti-sm"></i></button>
                    <button class="btn btn-sm btn-icon btn-danger delete-btn" data-id="${s.id}" title="Delete schedule"><i class="ti ti-trash ti-sm"></i></button>
                </div>`
            : isRejected ? `
                <div class="d-flex gap-1 justify-content-center">
                    <button class="btn btn-sm btn-icon btn-success approve-btn" data-id="${s.id}" title="Re-approve"><i class="ti ti-check ti-sm"></i></button>
                    <button class="btn btn-sm btn-icon btn-danger delete-btn" data-id="${s.id}" title="Delete schedule"><i class="ti ti-trash ti-sm"></i></button>
                </div>`
            : '<span class="text-muted">—</span>';

            return `
            <tr class="approval-row" data-id="${s.id}">
                <td>${isPending ? `<input type="checkbox" class="form-check-input row-check" data-id="${s.id}">` : ''}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-circle bg-label-primary">${s.user_name.substring(0, 2).toUpperCase()}</span>
                        </div>
                        <span class="fw-semibold">${s.user_name}</span>
                    </div>
                </td>
                <td>
                    <div class="fw-semibold">${startStr}</div>
                    <small class="text-muted">${timeRange}</small>
                </td>
                <td><span class="badge bg-label-primary">${s.duration_hours} hrs</span></td>
                <td class="text-muted">${s.caption || '<em>No caption</em>'}</td>
                <td><small class="text-muted">${submitted}</small></td>
                <td>${approvalBadge[s.approval_status] || ''}</td>
                <td class="text-center">${actions}</td>
            </tr>`;
        }).join('');

        document.querySelectorAll('.approve-btn').forEach(btn =>
            btn.addEventListener('click', () => handleAction(btn.dataset.id, 'approve')));
        document.querySelectorAll('.reject-btn').forEach(btn =>
            btn.addEventListener('click', () => handleAction(btn.dataset.id, 'reject')));
        document.querySelectorAll('.delete-btn').forEach(btn =>
            btn.addEventListener('click', () => handleDelete(btn.dataset.id)));
        document.querySelectorAll('.row-check').forEach(cb => {
            cb.addEventListener('change', function () {
                if (this.checked) selectedIds.add(this.dataset.id);
                else selectedIds.delete(this.dataset.id);
                updateBulkUI();
                updateSelectAll();
            });
        });
    }

    function handleAction(id, action) {
        const row = document.querySelector(`#approvalTableBody tr[data-id="${id}"]`);
        if (row) row.classList.add('processing');
        const url = action === 'approve' ? `/admin/approvals/${id}/approve` : `/admin/approvals/${id}/reject`;
        fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            showToast(data.message, ok ? (action === 'approve' ? 'success' : 'secondary') : 'danger');
            if (ok) { if (row) row.remove(); refreshStats(); }
            else { if (row) row.classList.remove('processing'); }
        });
    }

    function handleDelete(id) {
        if (!confirm('Permanently delete this schedule? This cannot be undone.')) return;
        const row = document.querySelector(`#approvalTableBody tr[data-id="${id}"]`);
        if (row) row.classList.add('processing');
        fetch(`/admin/approvals/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            showToast(data.message, ok ? 'success' : 'danger');
            if (ok) { if (row) row.remove(); refreshStats(); }
            else { if (row) row.classList.remove('processing'); }
        });
    }

    document.getElementById('bulkApproveBtn').addEventListener('click', () => { if (selectedIds.size) bulkAction('approve'); });
    document.getElementById('bulkRejectBtn').addEventListener('click',  () => { if (selectedIds.size) bulkAction('reject'); });

    function bulkAction(action) {
        const ids = Array.from(selectedIds);
        const url = action === 'approve' ? '{{ route("admin.approvals.bulk-approve") }}' : '{{ route("admin.approvals.bulk-reject") }}';
        ids.forEach(id => { const row = document.querySelector(`#approvalTableBody tr[data-id="${id}"]`); if (row) row.classList.add('processing'); });
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ ids }),
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            showToast(data.message, ok ? 'success' : 'danger');
            if (ok) {
                ids.forEach(id => { const row = document.querySelector(`#approvalTableBody tr[data-id="${id}"]`); if (row) row.remove(); });
                selectedIds.clear(); updateBulkUI();
                document.getElementById('selectAll').checked = false;
                refreshStats();
            } else {
                ids.forEach(id => { const row = document.querySelector(`#approvalTableBody tr[data-id="${id}"]`); if (row) row.classList.remove('processing'); });
            }
        });
    }

    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.row-check').forEach(cb => {
            cb.checked = this.checked;
            if (this.checked) selectedIds.add(cb.dataset.id);
            else selectedIds.delete(cb.dataset.id);
        });
        updateBulkUI();
    });

    function updateSelectAll() {
        const all = document.querySelectorAll('.row-check');
        const checked = document.querySelectorAll('.row-check:checked');
        document.getElementById('selectAll').checked = all.length > 0 && all.length === checked.length;
        document.getElementById('selectAll').indeterminate = checked.length > 0 && checked.length < all.length;
    }

    function updateBulkUI() {
        const bulk  = document.getElementById('bulkActions');
        const count = selectedIds.size;
        if (count > 0 && currentStatus === 'pending') {
            bulk.classList.remove('d-none');
            document.getElementById('selectedCount').textContent = count + ' selected';
        } else { bulk.classList.add('d-none'); }
    }

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('btn-primary', 'btn-success', 'btn-secondary');
                b.classList.add('btn-outline-' + (b.dataset.status === 'approved' ? 'success' : b.dataset.status === 'rejected' ? 'secondary' : 'primary'));
            });
            this.classList.remove('btn-outline-primary', 'btn-outline-success', 'btn-outline-secondary');
            const activeMap = { pending: 'btn-primary', approved: 'btn-success', rejected: 'btn-secondary' };
            this.classList.add(activeMap[this.dataset.status]);
            loadSchedules(this.dataset.status);
        });
    });

    // =============================================
    // CARD 2: RESCHEDULE REQUESTS
    // =============================================

    function loadReschedules() {
        const tbody = document.getElementById('rescheduleTableBody');
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-info" role="status"></div></td></tr>`;

        fetch('{{ route("admin.approvals.reschedule-list") }}', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => renderRescheduleTable(data.schedules));
    }

    function renderRescheduleTable(list) {
        const tbody = document.getElementById('rescheduleTableBody');

        if (list.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="7" class="text-center py-5">
                    <div class="avatar avatar-lg mb-3">
                        <span class="avatar-initial rounded-circle bg-label-info">
                            <i class="ti ti-calendar-event ti-28px"></i>
                        </span>
                    </div>
                    <p class="text-muted mb-0">No pending reschedule requests.</p>
                </td></tr>`;
            document.getElementById('rescheduleCountBadge').textContent = '0 pending';
            return;
        }

        const fmt = (iso, opts) => new Date(iso).toLocaleString('en-GB', { timeZone: 'Asia/Singapore', ...opts });
        const fmtDate = iso => fmt(iso, { weekday:'short', day:'2-digit', month:'short', year:'numeric' });
        const fmtTime = iso => fmt(iso, { hour:'2-digit', minute:'2-digit' });

        tbody.innerHTML = list.map(s => {
            const origDate  = fmtDate(s.start_shift);
            const origTime  = fmtTime(s.start_shift) + ' – ' + fmtTime(s.end_shift);
            const propDate  = fmtDate(s.pending_start);
            const propTime  = fmtTime(s.pending_start) + ' – ' + fmtTime(s.pending_end);
            const delta     = (s.pending_hours - s.duration_hours).toFixed(2);
            const deltaHtml = delta > 0
                ? `<span class="text-danger">+${delta}h</span>`
                : delta < 0
                ? `<span class="text-success">${delta}h</span>`
                : `<span class="text-muted">±0h</span>`;
            const requested = fmt(s.updated_at, { day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit' });

            return `
            <tr class="reschedule-row" data-id="${s.id}">
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-circle bg-label-info">${s.user_name.substring(0,2).toUpperCase()}</span>
                        </div>
                        <span class="fw-semibold">${s.user_name}</span>
                    </div>
                </td>
                <td>
                    <div class="fw-semibold text-muted">${origDate}</div>
                    <small class="text-muted">${origTime}</small>
                    ${s.caption ? `<br><small class="text-muted fst-italic">${s.caption}</small>` : ''}
                </td>
                <td class="text-center"><i class="ti ti-arrow-right time-arrow ti-lg"></i></td>
                <td>
                    <div class="fw-semibold">${propDate}</div>
                    <small class="text-muted">${propTime}</small>
                    ${s.pending_caption ? `<br><small class="text-muted fst-italic">${s.pending_caption}</small>` : ''}
                </td>
                <td>${deltaHtml}<br><small class="text-muted">${s.pending_hours}h total</small></td>
                <td><small class="text-muted">${requested}</small></td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">
                        <button class="btn btn-sm btn-icon btn-success rs-approve-btn" data-id="${s.id}" title="Approve reschedule">
                            <i class="ti ti-check ti-sm"></i>
                        </button>
                        <button class="btn btn-sm btn-icon btn-secondary rs-reject-btn" data-id="${s.id}" title="Reject reschedule">
                            <i class="ti ti-x ti-sm"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        }).join('');

        document.querySelectorAll('.rs-approve-btn').forEach(btn =>
            btn.addEventListener('click', () => handleRescheduleAction(btn.dataset.id, 'approve')));
        document.querySelectorAll('.rs-reject-btn').forEach(btn =>
            btn.addEventListener('click', () => handleRescheduleAction(btn.dataset.id, 'reject')));
    }

    function handleRescheduleAction(id, action) {
        const row = document.querySelector(`#rescheduleTableBody tr[data-id="${id}"]`);
        if (row) row.classList.add('processing');

        const url = action === 'approve'
            ? `/admin/approvals/${id}/approve-reschedule`
            : `/admin/approvals/${id}/reject-reschedule`;

        fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' } })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            showToast(data.message, ok ? 'success' : 'danger');
            if (ok) {
                if (row) row.remove();
                // Update reschedule count badge
                const remaining = document.querySelectorAll('#rescheduleTableBody .reschedule-row').length;
                document.getElementById('statReschedule').textContent = remaining;
                document.getElementById('rescheduleCountBadge').textContent = remaining + ' pending';
                if (remaining === 0) renderRescheduleTable([]);
            } else {
                if (row) row.classList.remove('processing');
            }
        });
    }

    // =============================================
    // SHARED: REFRESH STATS
    // =============================================

    function refreshStats() {
        fetch('{{ route("admin.approvals.list") }}?approval_status=pending', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(d => {
                document.getElementById('statPending').textContent = d.schedules.length;
                document.getElementById('tabBadgePending').textContent = d.schedules.length;
                if (currentStatus === 'pending') renderTable(d.schedules);
            });
        fetch('{{ route("admin.approvals.list") }}?approval_status=approved', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(d => {
                document.getElementById('statApproved').textContent = d.schedules.length;
                document.getElementById('tabBadgeApproved').textContent = d.schedules.length;
                if (currentStatus === 'approved') renderTable(d.schedules);
            });
        fetch('{{ route("admin.approvals.list") }}?approval_status=rejected', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(d => {
                document.getElementById('statRejected').textContent = d.schedules.length;
                document.getElementById('tabBadgeRejected').textContent = d.schedules.length;
                if (currentStatus === 'rejected') renderTable(d.schedules);
            });
    }

    // =============================================
    // SHARED: TOAST
    // =============================================

    function showToast(message, type) {
        const id = 'toast-' + Date.now();
        const typeIcon = type === 'success' ? 'check' : type === 'danger' ? 'alert-circle' : 'info-circle';
        const html = `
        <div id="${id}" class="bs-toast toast fade show bg-${type}" role="alert">
            <div class="toast-header">
                <i class="ti ti-${typeIcon} me-2 text-${type}"></i>
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body text-white">${message}</div>
        </div>`;
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = 9999;
            document.body.appendChild(container);
        }
        container.insertAdjacentHTML('beforeend', html);
        setTimeout(() => document.getElementById(id)?.remove(), 4000);
    }

    // =============================================
    // INIT
    // =============================================
    loadSchedules('pending');
    loadReschedules();
});
</script>
@endpush
</x-app-layout>
