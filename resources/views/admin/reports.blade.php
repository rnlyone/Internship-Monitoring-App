<x-app-layout>
@push('page-css')
<style>
.intern-card { cursor: pointer; transition: box-shadow .2s, transform .2s; border: 2px solid transparent; }
.intern-card:hover { box-shadow: 0 4px 20px rgba(105,108,255,.15); transform: translateY(-2px); }
.intern-card.selected { border-color: #696cff; }
.stat-mini { font-size: .75rem; color: #a1acb8; }
.attendance-bar { height: 5px; border-radius: 3px; background: #eee; overflow: hidden; }
.attendance-bar-fill { height: 100%; border-radius: 3px; background: #696cff; }

/* Report panel */
#reportPanel { display: none; }
.report-filter-label { font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #a1acb8; }

/* Summary KPI cards */
.kpi-card { border-radius: .5rem; padding: .75rem 1rem; text-align: center; }
.kpi-card .kpi-val { font-size: 1.6rem; font-weight: 700; line-height: 1; }
.kpi-card .kpi-lbl { font-size: .68rem; color: #a1acb8; margin-top: 4px; }

/* Charts section */
.chart-card { border-radius: .6rem; background: #fff; border: 1px solid rgba(0,0,0,.08); padding: 1rem; }
.chart-card .chart-title { font-size: .78rem; font-weight: 600; color: #5d596c; margin-bottom: .6rem; }

/* Schedule table */
.logbook-collapse td { background: #f8f7ff; }
.status-done    { background: rgba(40,199,111,.12); color: #28c76f; }
.status-late    { background: rgba(255,159,67,.12); color: #ff9f43; }
.status-absence { background: rgba(234,84,85,.12);  color: #ea5455; }
.status-ongoing { background: rgba(0,207,232,.12);  color: #00cfe8; }
.status-not_yet { background: rgba(130,134,139,.12); color: #82868b; }
.apv-pending    { background: rgba(255,159,67,.1);  color: #ff9f43; }
.apv-approved   { background: rgba(40,199,111,.1);  color: #28c76f; }
.apv-rejected   { background: rgba(130,134,139,.1); color: #82868b; }

/* Export button pulse while loading */
#rpExportBtn.loading { pointer-events:none; opacity:.65; }
</style>
@endpush

<!-- ── Page header ─────────────────────────────────────── -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-5">
    <div>
        <h4 class="mb-1"><i class="ti ti-report me-2"></i>Internship Reports</h4>
        <p class="text-muted mb-0">Click an intern card to view their detailed report and export PDF.</p>
    </div>
</div>

<!-- ── Intern cards grid ──────────────────────────────── -->
<div class="row g-4 mb-6" id="internGrid">
    @forelse($interns as $intern)
    @php
        $colors = ['primary','success','danger','warning','info'];
        $col    = $colors[crc32($intern['name']) % count($colors)];
        $initials = collect(explode(' ', $intern['name']))->take(2)->map(fn($p)=>strtoupper($p[0]))->join('');
    @endphp
    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
        <div class="card intern-card h-100"
             data-intern-id="{{ $intern['id'] }}"
             data-intern-name="{{ $intern['name'] }}"
             data-intern-email="{{ $intern['email'] }}"
             onclick="selectIntern(this)">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="avatar avatar-md">
                        <span class="avatar-initial rounded-circle bg-label-{{ $col }}">{{ $initials }}</span>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-semibold text-truncate">{{ $intern['name'] }}</div>
                        <div class="text-muted small text-truncate">{{ $intern['email'] }}</div>
                    </div>
                </div>
                @if($intern['internship_start'])
                <div class="text-muted mb-2" style="font-size:.72rem">
                    <i class="ti ti-calendar me-1"></i>
                    {{ $intern['internship_start'] }} — {{ $intern['internship_end'] ?? 'ongoing' }}
                </div>
                @endif
                <div class="row g-2 text-center mb-3">
                    <div class="col-4">
                        <div class="fw-bold text-primary">{{ $intern['total_schedules'] }}</div>
                        <div class="stat-mini">Schedules</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-success">{{ $intern['completed'] }}</div>
                        <div class="stat-mini">Done</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-warning">{{ $intern['late'] }}</div>
                        <div class="stat-mini">Late</div>
                    </div>
                </div>
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">{{ $intern['total_hours'] }}h total</span>
                    <span class="text-muted">{{ $intern['logbook_entries'] }} logs</span>
                </div>
                <div class="attendance-bar">
                    <div class="attendance-bar-fill" style="width:{{ $intern['attendance_rate'] }}%"></div>
                </div>
                <div class="text-muted mt-1" style="font-size:.7rem">{{ $intern['attendance_rate'] }}% attendance</div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5 text-muted">
            <i class="ti ti-users-group ti-48px d-block mb-2"></i>
            No interns registered yet.
        </div>
    </div>
    @endforelse
</div>

<!-- ── Detailed report panel ──────────────────────────── -->
<div id="reportPanel">

    <!-- Card: header + filters -->
    <div class="card mb-4">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="card-title mb-0">
                    <i class="ti ti-user-circle me-2"></i>
                    Report: <span id="rpName" class="text-primary">—</span>
                </h5>
                <small class="text-muted" id="rpEmail">—</small>
            </div>
            <div class="d-flex flex-wrap align-items-end gap-2">
                <div>
                    <label class="report-filter-label d-block">Date From</label>
                    <input type="date" class="form-control form-control-sm" id="rpDateFrom" style="width:140px">
                </div>
                <div>
                    <label class="report-filter-label d-block">Date To</label>
                    <input type="date" class="form-control form-control-sm" id="rpDateTo" style="width:140px">
                </div>
                <button class="btn btn-sm btn-primary" id="rpRefreshBtn">
                    <i class="ti ti-refresh me-1"></i>Refresh
                </button>
                <button class="btn btn-sm btn-success" id="rpExportBtn" title="Export comprehensive PDF report">
                    <i class="ti ti-file-type-pdf me-1"></i>Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- ── Visualization section ── -->
    <div id="rpVizSection" style="display:none">

        <!-- KPI tiles row -->
        <div class="row g-3 mb-4" id="rpKpiRow">
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="kpi-card bg-label-primary">
                    <div class="kpi-val text-primary" id="stTotal">—</div>
                    <div class="kpi-lbl">Total Schedules</div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="kpi-card bg-label-secondary">
                    <div class="kpi-val text-secondary" id="stHours">—</div>
                    <div class="kpi-lbl">Total Hours</div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="kpi-card bg-label-success">
                    <div class="kpi-val text-success" id="stDone">—</div>
                    <div class="kpi-lbl">Completed</div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="kpi-card bg-label-warning">
                    <div class="kpi-val text-warning" id="stLate">—</div>
                    <div class="kpi-lbl">Late</div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="kpi-card bg-label-danger">
                    <div class="kpi-val text-danger" id="stAbsence">—</div>
                    <div class="kpi-lbl">Absence</div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="kpi-card bg-label-info">
                    <div class="kpi-val text-info" id="stLogs">—</div>
                    <div class="kpi-lbl">Log Entries</div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="kpi-card bg-label-success">
                    <div class="kpi-val text-success" id="stApproved">—</div>
                    <div class="kpi-lbl">Approved</div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="kpi-card bg-label-warning">
                    <div class="kpi-val text-warning" id="stPending">—</div>
                    <div class="kpi-lbl">Pending</div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="kpi-card bg-label-primary">
                    <div class="kpi-val text-primary" id="stRate">—</div>
                    <div class="kpi-lbl">Attendance %</div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="kpi-card bg-label-info">
                    <div class="kpi-val text-info" id="stOngoing">—</div>
                    <div class="kpi-lbl">Ongoing</div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-xl-2">
                <div class="kpi-card bg-label-secondary">
                    <div class="kpi-val text-secondary" id="stNotYet">—</div>
                    <div class="kpi-lbl">Not Yet</div>
                </div>
            </div>
        </div>

        <!-- Charts row -->
        <div class="row g-4 mb-4">
            <!-- Donut: attendance breakdown -->
            <div class="col-md-5 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="chart-title mb-2 fw-semibold text-muted small text-uppercase">Attendance Breakdown</div>
                        <div style="position:relative;height:200px">
                            <canvas id="chartDonut"></canvas>
                        </div>
                        <div id="donutLegend" class="d-flex flex-wrap gap-2 justify-content-center mt-2" style="font-size:.72rem"></div>
                    </div>
                </div>
            </div>
            <!-- Bar: hours per week -->
            <div class="col-md-7 col-xl-8">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="chart-title mb-2 fw-semibold text-muted small text-uppercase">Work Hours per Week</div>
                        <div style="position:relative;height:200px">
                            <canvas id="chartBar"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval status bar -->
        <div class="card mb-4">
            <div class="card-body py-3">
                <div class="small fw-semibold text-muted text-uppercase mb-2">Schedule Approval Status</div>
                <div id="approvalBar" style="display:flex;height:18px;border-radius:6px;overflow:hidden;gap:2px"></div>
                <div id="approvalBarLegend" class="d-flex gap-3 mt-2" style="font-size:.72rem"></div>
            </div>
        </div>
    </div>

    <!-- Detail table card -->
    <div class="card mb-5">
        <div class="card-header">
            <h6 class="card-title mb-0"><i class="ti ti-table me-2"></i>Schedule Detail</h6>
        </div>
        <div class="card-body p-0">
            <div id="rpLoadingState" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading…</span></div>
                <p class="text-muted mt-2 mb-0">Loading report…</p>
            </div>
            <div id="rpEmptyState" class="text-center py-5" style="display:none">
                <i class="ti ti-calendar-off ti-48px text-muted d-block mb-2"></i>
                <p class="text-muted mb-0">No schedules found for the selected date range.</p>
            </div>
            <div class="table-responsive" id="rpTableWrapper" style="display:none">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:160px">Date</th>
                            <th style="width:130px">Shift</th>
                            <th style="width:80px">Hours</th>
                            <th>Caption</th>
                            <th style="width:90px">Status</th>
                            <th style="width:100px">Entry</th>
                            <th style="width:100px">Exit</th>
                            <th style="width:80px">Late</th>
                            <th style="width:60px">Logs</th>
                            <th style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody id="rpTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('page-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function () {
    let _currentInternId = null;
    let _expandedRows = new Set();
    let _chartDonut = null;
    let _chartBar   = null;

    // ── helpers ──────────────────────────────────────────
    function escHtml(s) {
        if (s == null) return '—';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function badge(cls, label) {
        return `<span class="badge rounded-pill ${cls}" style="font-size:.65rem">${label}</span>`;
    }
    const statusCls = { done:'status-done', late:'status-late', absence:'status-absence', ongoing:'status-ongoing', not_yet:'status-not_yet' };
    const statusLbl = { done:'Done', late:'Late', absence:'Absence', ongoing:'Ongoing', not_yet:'Not Yet' };
    const apvCls    = { approved:'apv-approved', pending:'apv-pending', rejected:'apv-rejected' };

    // ── intern card selection ────────────────────────────
    window.selectIntern = function (card) {
        document.querySelectorAll('.intern-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        _currentInternId = card.dataset.internId;
        document.getElementById('rpName').textContent  = card.dataset.internName;
        document.getElementById('rpEmail').textContent = card.dataset.internEmail;
        document.getElementById('rpDateFrom').value = '';
        document.getElementById('rpDateTo').value   = '';
        _expandedRows.clear();
        document.getElementById('reportPanel').style.display = '';
        document.getElementById('reportPanel').scrollIntoView({ behavior: 'smooth', block: 'start' });
        loadReport();
    };

    // ── load report data ─────────────────────────────────
    async function loadReport() {
        if (!_currentInternId) return;
        setLoadingState(true);
        document.getElementById('rpVizSection').style.display = 'none';

        const params = new URLSearchParams();
        const from = document.getElementById('rpDateFrom').value;
        const to   = document.getElementById('rpDateTo').value;
        if (from) params.set('date_from', from);
        if (to)   params.set('date_to',   to);

        try {
            const url = `{{ url('/admin/reports') }}/${_currentInternId}?${params}`;
            const res  = await fetch(url);
            const data = await res.json();
            renderSummary(data.summary);
            renderCharts(data.summary);
            renderSchedules(data.schedules);
            document.getElementById('rpVizSection').style.display = '';
        } catch (err) {
            console.error(err);
            setLoadingState(false);
        }
    }

    function setLoadingState(loading) {
        document.getElementById('rpLoadingState').style.display  = loading ? '' : 'none';
        document.getElementById('rpTableWrapper').style.display  = 'none';
        document.getElementById('rpEmptyState').style.display    = 'none';
    }

    function renderSummary(s) {
        document.getElementById('stTotal').textContent    = s.total_schedules;
        document.getElementById('stHours').textContent    = s.total_hours + 'h';
        document.getElementById('stDone').textContent     = s.completed;
        document.getElementById('stLate').textContent     = s.late;
        document.getElementById('stAbsence').textContent  = s.absence;
        document.getElementById('stLogs').textContent     = s.logbook_entries;
        document.getElementById('stApproved').textContent = s.approved;
        document.getElementById('stPending').textContent  = s.pending;
        document.getElementById('stRate').textContent     = s.attendance_rate + '%';
        document.getElementById('stOngoing').textContent  = s.ongoing;
        document.getElementById('stNotYet').textContent   = s.not_yet;
    }

    function renderCharts(s) {
        // ── Donut chart ───────────────────────────────────
        const donutData = {
            labels: ['Completed', 'Late', 'Absence', 'Ongoing', 'Not Yet'],
            datasets: [{
                data: [s.completed, s.late, s.absence, s.ongoing, s.not_yet],
                backgroundColor: ['#28c76f','#ff9f43','#ea5455','#00cfe8','#a1acb8'],
                borderWidth: 2,
                borderColor: '#fff',
            }],
        };

        if (_chartDonut) _chartDonut.destroy();
        _chartDonut = new Chart(document.getElementById('chartDonut'), {
            type: 'doughnut',
            data: donutData,
            options: {
                responsive: true, maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } },
                },
            },
        });

        // Legend
        const legendEl = document.getElementById('donutLegend');
        legendEl.innerHTML = '';
        const colors = ['#28c76f','#ff9f43','#ea5455','#00cfe8','#a1acb8'];
        const labels = ['Completed','Late','Absence','Ongoing','Not Yet'];
        const vals   = [s.completed, s.late, s.absence, s.ongoing, s.not_yet];
        labels.forEach((lbl, i) => {
            legendEl.innerHTML += `<span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:${colors[i]};margin-right:3px"></span>${lbl}: <strong>${vals[i]}</strong></span>`;
        });

        // ── Bar chart: hours per week ─────────────────────
        const weeks = (s.weekly_hours ?? []);
        const barData = {
            labels: weeks.map(w => w.label),
            datasets: [{
                label: 'Hours',
                data: weeks.map(w => w.hours),
                backgroundColor: 'rgba(105,108,255,.7)',
                borderRadius: 4,
                borderSkipped: false,
            }],
        };

        if (_chartBar) _chartBar.destroy();
        _chartBar = new Chart(document.getElementById('chartBar'), {
            type: 'bar',
            data: barData,
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.05)' }, ticks: { font: { size: 10 } } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                },
            },
        });

        // ── Approval bar ──────────────────────────────────
        const apvBar = document.getElementById('approvalBar');
        const apvLegend = document.getElementById('approvalBarLegend');
        const apvTotal = s.approved + s.pending;
        apvBar.innerHTML = '';
        apvLegend.innerHTML = '';

        if (apvTotal > 0) {
            const apvItems = [
                { label:'Approved', val:s.approved, color:'#28c76f' },
                { label:'Pending',  val:s.pending,  color:'#ff9f43' },
            ];
            apvItems.forEach(item => {
                if (item.val > 0) {
                    const pct = (item.val / apvTotal * 100).toFixed(1);
                    apvBar.innerHTML += `<div title="${item.label}: ${item.val}" style="flex:${item.val};background:${item.color};min-width:4px"></div>`;
                    apvLegend.innerHTML += `<span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:${item.color};margin-right:3px"></span>${item.label}: <strong>${item.val}</strong> (${pct}%)</span>`;
                }
            });
        } else {
            apvBar.innerHTML = '<div style="flex:1;background:#eee"></div>';
        }
    }

    function renderSchedules(schedules) {
        const tbody   = document.getElementById('rpTableBody');
        const wrapper = document.getElementById('rpTableWrapper');
        const empty   = document.getElementById('rpEmptyState');
        const loading = document.getElementById('rpLoadingState');

        tbody.innerHTML = '';
        loading.style.display = 'none';

        if (!schedules.length) {
            wrapper.style.display = 'none';
            empty.style.display   = '';
            return;
        }

        empty.style.display   = 'none';
        wrapper.style.display = '';

        schedules.forEach((s, idx) => {
            const sCls  = statusCls[s.status]  ?? 'apv-pending';
            const asCls = apvCls[s.approval_status] ?? 'apv-pending';
            const rowId = `rp-row-${idx}`;
            const colId = `rp-col-${idx}`;

            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.dataset.idx = idx;
            tr.innerHTML = `
                <td class="small">${escHtml(s.date)}</td>
                <td class="small fw-semibold">${escHtml(s.start_shift)} – ${escHtml(s.end_shift)}</td>
                <td class="small">${s.duration_hours}h</td>
                <td class="small">${escHtml(s.caption)}</td>
                <td>${badge(sCls, statusLbl[s.status] ?? s.status)}</td>
                <td class="small">${s.entry_time ? escHtml(s.entry_time) : '<span class="text-muted">—</span>'}</td>
                <td class="small">${s.exit_time  ? escHtml(s.exit_time)  : '<span class="text-muted">—</span>'}</td>
                <td class="small">${s.late_minutes != null ? s.late_minutes + ' min' : '<span class="text-muted">—</span>'}</td>
                <td>
                    ${s.logbook_count > 0
                        ? `<span class="badge bg-label-primary">${s.logbook_count}</span>`
                        : `<span class="text-muted small">0</span>`}
                </td>
                <td>
                    ${s.logbook_count > 0
                        ? `<button class="btn btn-sm btn-icon btn-text-secondary" onclick="toggleLogs('${colId}', this)" title="Toggle logbooks">
                               <i class="ti ti-chevron-down"></i>
                           </button>`
                        : ''}
                </td>
            `;
            tbody.appendChild(tr);

            if (s.logbook_count > 0) {
                const expandTr = document.createElement('tr');
                expandTr.id = colId;
                expandTr.className = 'logbook-collapse';
                expandTr.style.display = _expandedRows.has(idx) ? '' : 'none';
                expandTr.innerHTML = `
                    <td colspan="10" class="px-4 py-3">
                        <div class="small fw-semibold text-muted mb-2">
                            <i class="ti ti-notebook me-1"></i>Logbook Entries (${s.logbook_count})
                        </div>
                        ${s.logbooks.map(l => `
                            <div class="border rounded p-2 mb-2 bg-white">
                                <div class="text-muted" style="font-size:.7rem">${escHtml(l.created_at)}</div>
                                <div style="white-space:pre-wrap;word-break:break-word">${escHtml(l.content)}</div>
                            </div>
                        `).join('')}
                    </td>
                `;
                tbody.appendChild(expandTr);

                if (_expandedRows.has(idx)) {
                    const btn = tr.querySelector('button');
                    if (btn) btn.querySelector('i').classList.replace('ti-chevron-down', 'ti-chevron-up');
                }
            }
        });
    }

    window.toggleLogs = function (colId, btn) {
        const row = document.getElementById(colId);
        const icon = btn.querySelector('i');
        if (row.style.display === 'none') {
            row.style.display = '';
            icon.classList.replace('ti-chevron-down', 'ti-chevron-up');
        } else {
            row.style.display = 'none';
            icon.classList.replace('ti-chevron-up', 'ti-chevron-down');
        }
    };

    document.getElementById('rpRefreshBtn').addEventListener('click', loadReport);

    // ── PDF export ────────────────────────────────────────
    document.getElementById('rpExportBtn').addEventListener('click', function () {
        if (!_currentInternId) return;
        const btn = this;
        btn.classList.add('loading');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Generating…';

        const params = new URLSearchParams();
        const from = document.getElementById('rpDateFrom').value;
        const to   = document.getElementById('rpDateTo').value;
        if (from) params.set('date_from', from);
        if (to)   params.set('date_to',   to);

        const url = `{{ url('/admin/reports') }}/${_currentInternId}/pdf?${params}`;

        // Trigger download via hidden link, then restore button
        const a = document.createElement('a');
        a.href = url;
        a.download = '';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);

        setTimeout(() => {
            btn.classList.remove('loading');
            btn.innerHTML = '<i class="ti ti-file-type-pdf me-1"></i>Export PDF';
        }, 2500);
    });
})();
</script>
@endpush
</x-app-layout>
