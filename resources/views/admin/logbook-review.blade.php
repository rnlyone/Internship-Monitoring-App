<x-app-layout>
@push('page-css')
<style>
.logbook-card { border-left: 3px solid #696cff; }
.intern-avatar { width: 36px; height: 36px; font-size: 14px; }
.content-preview { white-space: pre-wrap; word-break: break-word; }
.filter-row .form-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #a1acb8; }
#loadingRow { display: none; }
#emptyRow  { display: none; }
</style>
@endpush

<div class="row mb-4">
    <!-- Page header -->
    <div class="col-12">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="mb-1"><i class="ti ti-notebook me-2"></i>Logbook Review</h4>
                <p class="text-muted mb-0">Browse and search all intern logbook entries.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-primary fs-6" id="totalBadge">—</span>
                <span class="text-muted small">entries found</span>
            </div>
        </div>
    </div>
</div>

<!-- Filter card -->
<div class="card mb-5">
    <div class="card-body">
        <div class="row g-3 filter-row align-items-end">
            <div class="col-md-4 col-sm-6">
                <label class="form-label">Intern</label>
                <select class="form-select" id="filterIntern">
                    <option value="">All Interns</option>
                    @foreach($interns as $intern)
                        <option value="{{ $intern->id }}">{{ $intern->name }} &lt;{{ $intern->email }}&gt;</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label">Date From</label>
                <input type="date" class="form-control" id="filterDateFrom">
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label">Date To</label>
                <input type="date" class="form-control" id="filterDateTo">
            </div>
            <div class="col-md-2 col-sm-6 d-flex gap-2">
                <button class="btn btn-primary flex-fill" id="btnSearch">
                    <i class="ti ti-search me-1"></i>Search
                </button>
                <button class="btn btn-outline-secondary" id="btnReset" title="Reset filters">
                    <i class="ti ti-refresh"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Results card -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="ti ti-list me-2"></i>Logbook Entries</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:180px">Intern</th>
                    <th style="width:200px">Schedule</th>
                    <th style="width:120px">Written At</th>
                    <th>Logbook Content</th>
                </tr>
            </thead>
            <tbody id="logbookTableBody">
                <!-- Loading skeleton -->
                <tr id="loadingRow">
                    <td colspan="4" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading…</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Fetching logbook entries…</p>
                    </td>
                </tr>
                <!-- Empty state -->
                <tr id="emptyRow">
                    <td colspan="4" class="text-center py-5">
                        <i class="ti ti-notebook ti-48px text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">No logbook entries found for the selected filters.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@push('page-js')
<script>
(function () {
    const tbody       = document.getElementById('logbookTableBody');
    const loadingRow  = document.getElementById('loadingRow');
    const emptyRow    = document.getElementById('emptyRow');
    const totalBadge  = document.getElementById('totalBadge');
    const btnSearch   = document.getElementById('btnSearch');
    const btnReset    = document.getElementById('btnReset');
    const selIntern   = document.getElementById('filterIntern');
    const inputFrom   = document.getElementById('filterDateFrom');
    const inputTo     = document.getElementById('filterDateTo');

    const statusColors = {
        done:    'success',
        late:    'warning',
        absence: 'danger',
        ongoing: 'info',
        not_yet: 'secondary',
    };

    function statusLabel(s) {
        const map = { done: 'Done', late: 'Late', absence: 'Absence', ongoing: 'Ongoing', not_yet: 'Not Yet' };
        return map[s] ?? s;
    }

    function initials(name) {
        return name.split(' ').slice(0, 2).map(p => p[0]).join('').toUpperCase();
    }

    function avatarColor(name) {
        const colors = ['primary','success','danger','warning','info','secondary'];
        let h = 0;
        for (let c of name) h = (h * 31 + c.charCodeAt(0)) & 0xffffffff;
        return colors[Math.abs(h) % colors.length];
    }

    function renderRows(logbooks) {
        // Remove existing data rows (keep loading + empty sentinel rows)
        tbody.querySelectorAll('tr.data-row').forEach(r => r.remove());

        if (!logbooks.length) {
            emptyRow.style.display = '';
            loadingRow.style.display = 'none';
            return;
        }

        emptyRow.style.display = 'none';
        loadingRow.style.display = 'none';

        logbooks.forEach(l => {
            const color = avatarColor(l.intern_name);
            const statusColor = statusColors[l.schedule_status] ?? 'secondary';
            const row = document.createElement('tr');
            row.className = 'data-row';
            row.innerHTML = `
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm intern-avatar">
                            <span class="avatar-initial rounded-circle bg-label-${color}">${initials(l.intern_name)}</span>
                        </div>
                        <div>
                            <div class="fw-semibold small">${escHtml(l.intern_name)}</div>
                            <div class="text-muted" style="font-size:.7rem">${escHtml(l.intern_email)}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="small fw-semibold">${escHtml(l.schedule_start)} – ${escHtml(l.schedule_end)}</div>
                    ${l.schedule_caption ? `<div class="text-muted" style="font-size:.7rem">${escHtml(l.schedule_caption)}</div>` : ''}
                    <span class="badge bg-label-${statusColor} mt-1" style="font-size:.65rem">${statusLabel(l.schedule_status)}</span>
                </td>
                <td>
                    <span class="small text-muted">${escHtml(l.created_at)}</span>
                </td>
                <td>
                    <div class="content-preview small">${escHtml(l.content)}</div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    function escHtml(str) {
        if (str == null) return '—';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    async function loadLogbooks() {
        // Show loading state
        tbody.querySelectorAll('tr.data-row').forEach(r => r.remove());
        emptyRow.style.display = 'none';
        loadingRow.style.display = '';
        totalBadge.textContent = '—';
        btnSearch.disabled = true;

        const params = new URLSearchParams();
        if (selIntern.value)  params.set('user_id',   selIntern.value);
        if (inputFrom.value)  params.set('date_from', inputFrom.value);
        if (inputTo.value)    params.set('date_to',   inputTo.value);

        try {
            const res  = await fetch(`{{ route('admin.logbooks.list') }}?${params}`);
            const data = await res.json();
            totalBadge.textContent = data.total;
            renderRows(data.logbooks);
        } catch (err) {
            console.error(err);
            loadingRow.style.display = 'none';
            emptyRow.style.display = '';
        } finally {
            btnSearch.disabled = false;
        }
    }

    btnSearch.addEventListener('click', loadLogbooks);
    btnReset.addEventListener('click', () => {
        selIntern.value = '';
        inputFrom.value = '';
        inputTo.value   = '';
        loadLogbooks();
    });

    // Load on page open
    loadLogbooks();
})();
</script>
@endpush
</x-app-layout>
