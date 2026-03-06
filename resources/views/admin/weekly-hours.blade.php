<x-app-layout>
@push('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
<style>
/* Week navigator */
.week-nav-btn { min-width: 36px; }

/* KPI cards */
.wh-kpi-card { transition: transform .18s, box-shadow .18s; }
.wh-kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(115,103,240,.15); }

/* Intern table */
.intern-row-toggle { cursor: pointer; user-select: none; }
.intern-row-toggle:hover td { background: rgba(115,103,240,.04); }
.expand-icon { transition: transform .2s; }
.expand-icon.open { transform: rotate(90deg); }
.shifts-subrow { background: rgba(115,103,240,.03); }
.shifts-subrow td { padding: 0 !important; }
.shifts-inner { padding: .75rem 1.25rem .75rem 3rem; }

/* Approval badges */
.badge-ap-approved { background: rgba(40,199,111,.12); color: #28c76f; font-size: .78rem; }
.badge-ap-pending  { background: rgba(255,159,67,.12); color: #ff9f43; font-size: .78rem; }
.badge-ap-rejected { background: rgba(130,134,139,.12); color: #82868b; font-size: .78rem; }

/* Status badges */
.badge-st-done    { background: rgba(40,199,111,.1); color: #28c76f; font-size: .75rem; }
.badge-st-late    { background: rgba(255,159,67,.1); color: #ff9f43; font-size: .75rem; }
.badge-st-absence { background: rgba(255,76,81,.1); color: #ff4c51; font-size: .75rem; }
.badge-st-ongoing { background: rgba(0,207,232,.1); color: #00cfe8; font-size: .75rem; }
.badge-st-not_yet { background: rgba(168,170,174,.1); color: #a8aaae; font-size: .75rem; }

/* Progress bar */
.hours-bar-wrap { min-width: 80px; }
.progress { height: 6px; border-radius: 3px; }

/* Empty state */
.empty-state { padding: 3rem; text-align: center; color: var(--bs-secondary-color); }
</style>
@endpush

<div class="row g-6">

    <!-- ========== Page Header ========== -->
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="mb-1"><i class="ti ti-calendar-week me-2 text-primary"></i>Weekly Hours Review</h4>
                <p class="text-muted mb-0">Review each intern's scheduled working hours and submission status per week</p>
            </div>
            <!-- Week navigator -->
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary btn-sm week-nav-btn" id="prevWeekBtn">
                    <i class="ti ti-chevron-left"></i>
                </button>
                <span class="fw-semibold px-2" id="weekLabel" style="min-width:220px;text-align:center;">—</span>
                <button class="btn btn-outline-secondary btn-sm week-nav-btn" id="nextWeekBtn">
                    <i class="ti ti-chevron-right"></i>
                </button>
                <button class="btn btn-outline-primary btn-sm ms-2" id="todayBtn">
                    <i class="ti ti-calendar me-1"></i>This Week
                </button>
            </div>
        </div>
    </div>

    <!-- ========== KPI Cards ========== -->
    <div class="col-6 col-sm-4 col-xl-2">
        <div class="card wh-kpi-card h-100">
            <div class="card-body text-center py-4">
                <div class="avatar avatar-md mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="ti ti-users ti-md"></i></span>
                </div>
                <h3 class="mb-0" id="kpiInterns">—</h3>
                <p class="text-muted small mb-0">Interns</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-xl-2">
        <div class="card wh-kpi-card h-100">
            <div class="card-body text-center py-4">
                <div class="avatar avatar-md mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-info"><i class="ti ti-calendar-stats ti-md"></i></span>
                </div>
                <h3 class="mb-0" id="kpiShifts">—</h3>
                <p class="text-muted small mb-0">Total Shifts</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-xl-2">
        <div class="card wh-kpi-card h-100">
            <div class="card-body text-center py-4">
                <div class="avatar avatar-md mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-warning"><i class="ti ti-clock ti-md"></i></span>
                </div>
                <h3 class="mb-0" id="kpiTotal">—</h3>
                <p class="text-muted small mb-0">Total Hours</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-xl-2">
        <div class="card wh-kpi-card h-100">
            <div class="card-body text-center py-4">
                <div class="avatar avatar-md mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-success"><i class="ti ti-circle-check ti-md"></i></span>
                </div>
                <h3 class="mb-0" id="kpiApproved">—</h3>
                <p class="text-muted small mb-0">Approved Hrs</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-xl-2">
        <div class="card wh-kpi-card h-100">
            <div class="card-body text-center py-4">
                <div class="avatar avatar-md mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-warning"><i class="ti ti-clock-pause ti-md"></i></span>
                </div>
                <h3 class="mb-0" id="kpiPending">—</h3>
                <p class="text-muted small mb-0">Pending Shifts</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-xl-2">
        <div class="card wh-kpi-card h-100">
            <div class="card-body text-center py-4">
                <div class="avatar avatar-md mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-secondary"><i class="ti ti-circle-x ti-md"></i></span>
                </div>
                <h3 class="mb-0" id="kpiRejected">—</h3>
                <p class="text-muted small mb-0">Rejected Shifts</p>
            </div>
        </div>
    </div>

    <!-- ========== Daily Hours Bar Chart ========== -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="card-title mb-0"><i class="ti ti-chart-bar me-2"></i>Daily Hours by Intern</h5>
                    <p class="card-subtitle mt-1 text-muted">Scheduled hours per day this week</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary small" id="chartWeekLabel">—</span>
                </div>
            </div>
            <div class="card-body">
                <div id="weeklyBarChart" style="min-height:280px;">
                    <div class="d-flex align-items-center justify-content-center" style="height:280px;">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== Per-Intern Table ========== -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="card-title mb-0"><i class="ti ti-users me-2"></i>Intern Breakdown</h5>
                    <p class="card-subtitle mt-1 text-muted">Click a row to expand individual shift details</p>
                </div>
                <button class="btn btn-outline-secondary btn-sm" id="expandAllBtn">
                    <i class="ti ti-chevrons-down me-1"></i>Expand All
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="internTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width:28px;"></th>
                            <th>Intern</th>
                            <th class="text-center">Shifts</th>
                            <th class="text-center">Total Hrs</th>
                            <th class="text-center">Approved Hrs</th>
                            <th class="text-center">
                                <span class="badge-ap-approved badge rounded-pill px-2 py-1">Approved</span>
                            </th>
                            <th class="text-center">
                                <span class="badge-ap-pending badge rounded-pill px-2 py-1">Pending</span>
                            </th>
                            <th class="text-center">
                                <span class="badge-ap-rejected badge rounded-pill px-2 py-1">Rejected</span>
                            </th>
                            <th style="width:140px;">Hours Progress</th>
                        </tr>
                    </thead>
                    <tbody id="internTableBody">
                        <tr><td colspan="9" class="empty-state">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Loading…
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /row -->

@push('page-scripts')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
(function () {
    'use strict';

    // ---- State ----
    let currentWeekStart = null; // ISO date string YYYY-MM-DD
    let barChart = null;
    let expandedInterns = new Set();
    let lastData = null;

    // ---- Helpers ----
    function addWeeks(dateStr, n) {
        const d = new Date(dateStr);
        d.setDate(d.getDate() + n * 7);
        return d.toISOString().slice(0, 10);
    }

    function fmtHours(h) {
        if (h === null || h === undefined) return '—';
        const hrs  = Math.floor(h);
        const mins = Math.round((h - hrs) * 60);
        if (hrs === 0) return `${mins}m`;
        return mins > 0 ? `${hrs}h ${mins}m` : `${hrs}h`;
    }

    function fmtDateTime(iso) {
        const d = new Date(iso);
        return d.toLocaleString('en-GB', {
            weekday: 'short', day: '2-digit', month: 'short',
            hour: '2-digit', minute: '2-digit', hour12: false
        });
    }

    function fmtTime(iso) {
        const d = new Date(iso);
        return d.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: false });
    }

    function approvalBadge(status) {
        const map = {
            approved: ['badge-ap-approved', 'Approved'],
            pending:  ['badge-ap-pending',  'Pending'],
            rejected: ['badge-ap-rejected', 'Rejected'],
        };
        const [cls, lbl] = map[status] || ['badge bg-secondary', status];
        return `<span class="badge rounded-pill px-2 py-1 ${cls}">${lbl}</span>`;
    }

    function statusBadge(status) {
        const map = {
            done:     ['badge-st-done',    'Done'],
            late:     ['badge-st-late',    'Late'],
            absence:  ['badge-st-absence', 'Absence'],
            ongoing:  ['badge-st-ongoing', 'Ongoing'],
            not_yet:  ['badge-st-not_yet', 'Not Yet'],
        };
        const [cls, lbl] = map[status] || ['badge bg-secondary', status];
        return `<span class="badge rounded-pill px-2 py-1 ${cls}">${lbl}</span>`;
    }

    // ---- Fetch & render ----
    function load(weekDate) {
        // Show loading state
        document.getElementById('weekLabel').textContent = '…';
        document.getElementById('internTableBody').innerHTML =
            '<tr><td colspan="9" class="empty-state">' +
            '<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>Loading…</td></tr>';
        ['kpiInterns','kpiShifts','kpiTotal','kpiApproved','kpiPending','kpiRejected']
            .forEach(id => document.getElementById(id).textContent = '—');

        fetch(`{{ route('admin.weekly-hours.data') }}?week=${weekDate}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            lastData = data;
            currentWeekStart = data.week_start;
            renderPage(data);
        })
        .catch(() => {
            document.getElementById('internTableBody').innerHTML =
                '<tr><td colspan="9" class="empty-state text-danger">Failed to load data. Please refresh.</td></tr>';
        });
    }

    function renderPage(data) {
        // Week label
        document.getElementById('weekLabel').textContent = data.week_label;
        document.getElementById('chartWeekLabel').textContent = data.week_label;

        // KPIs
        document.getElementById('kpiInterns').textContent  = data.kpi.total_interns;
        document.getElementById('kpiShifts').textContent   = data.kpi.total_shifts;
        document.getElementById('kpiTotal').textContent    = fmtHours(data.kpi.total_hours);
        document.getElementById('kpiApproved').textContent = fmtHours(data.kpi.approved_hours);
        document.getElementById('kpiPending').textContent  = data.kpi.pending_count;
        document.getElementById('kpiRejected').textContent = data.kpi.rejected_count;

        // Chart
        renderChart(data.chart_days, data.chart_series);

        // Table
        renderTable(data.interns, data.kpi.total_hours);
    }

    // ---- Chart ----
    function renderChart(days, series) {
        const isDark = document.documentElement.classList.contains('dark-style');
        const textColor = isDark ? '#b4bdc6' : '#697a8d';
        const gridColor = isDark ? 'rgba(255,255,255,.08)' : '#eceef1';

        const options = {
            chart: {
                type: 'bar',
                height: 280,
                stacked: true,
                toolbar: { show: false },
                background: 'transparent',
                fontFamily: 'inherit',
            },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
            series: series,
            xaxis: {
                categories: days,
                labels: { style: { colors: textColor, fontSize: '12px' } },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: {
                    style: { colors: textColor },
                    formatter: val => val + 'h'
                },
            },
            grid: { borderColor: gridColor, strokeDashArray: 4 },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                labels: { colors: textColor },
                markers: { offsetX: -3 },
            },
            tooltip: {
                y: { formatter: val => fmtHours(val) }
            },
            colors: ['#7367f0','#28c76f','#ff9f43','#00cfe8','#ff4c51','#ea5455','#6f6b7d'],
            dataLabels: { enabled: false },
            fill: { opacity: 1 },
            theme: { mode: isDark ? 'dark' : 'light' },
        };

        if (barChart) {
            barChart.updateOptions({ ...options, series });
        } else {
            document.getElementById('weeklyBarChart').innerHTML = '';
            barChart = new ApexCharts(document.getElementById('weeklyBarChart'), options);
            barChart.render();
        }
    }

    // ---- Table ----
    function renderTable(interns, weekTotalHours) {
        const tbody = document.getElementById('internTableBody');

        if (!interns || interns.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="empty-state">' +
                '<i class="ti ti-calendar-off ti-xl d-block mx-auto mb-2"></i>' +
                'No schedules found for this week.</td></tr>';
            return;
        }

        // Max hours for progress bar scaling
        const maxHours = Math.max(...interns.map(i => i.total_hours), 1);

        let html = '';
        for (const intern of interns) {
            const pct = Math.min(100, Math.round((intern.total_hours / maxHours) * 100));
            const approvedPct = intern.total_hours > 0
                ? Math.min(100, Math.round((intern.approved_hours / intern.total_hours) * 100))
                : 0;

            const isOpen = expandedInterns.has(intern.id);
            const expandIcon = `<i class="ti ti-chevron-right expand-icon${isOpen ? ' open' : ''}"></i>`;

            html += `
            <tr class="intern-row-toggle" data-id="${intern.id}">
                <td class="text-center">${expandIcon}</td>
                <td>
                    <div class="fw-semibold">${escHtml(intern.name)}</div>
                    <small class="text-muted">${escHtml(intern.email)}</small>
                </td>
                <td class="text-center">
                    <span class="badge bg-label-primary rounded-pill">${intern.total_shifts}</span>
                </td>
                <td class="text-center fw-semibold">${fmtHours(intern.total_hours)}</td>
                <td class="text-center text-success fw-semibold">${fmtHours(intern.approved_hours)}</td>
                <td class="text-center">
                    <span class="badge-ap-approved badge rounded-pill px-2">${intern.approved_count}</span>
                </td>
                <td class="text-center">
                    <span class="badge-ap-pending badge rounded-pill px-2">${intern.pending_count}</span>
                </td>
                <td class="text-center">
                    <span class="badge-ap-rejected badge rounded-pill px-2">${intern.rejected_count}</span>
                </td>
                <td>
                    <div class="hours-bar-wrap">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>${fmtHours(intern.total_hours)}</span>
                            <span>${approvedPct}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-primary" style="width:${pct}%"></div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr class="shifts-subrow" id="sub-${intern.id}" style="${isOpen ? '' : 'display:none'}">
                <td colspan="9">
                    <div class="shifts-inner">
                        ${renderShifts(intern.shifts)}
                    </div>
                </td>
            </tr>`;
        }

        tbody.innerHTML = html;

        // Attach toggle listeners
        tbody.querySelectorAll('.intern-row-toggle').forEach(row => {
            row.addEventListener('click', () => toggleIntern(row.dataset.id));
        });
    }

    function renderShifts(shifts) {
        if (!shifts || shifts.length === 0) {
            return '<p class="text-muted small mb-0">No shifts this week.</p>';
        }
        let rows = shifts.map(s => `
            <tr>
                <td class="pe-3 text-nowrap text-muted small">${fmtDateTime(s.start_shift)}</td>
                <td class="pe-3 text-nowrap small">
                    <i class="ti ti-arrow-right ti-xs text-muted mx-1"></i>
                    ${fmtTime(s.end_shift)}
                    <span class="text-muted ms-1">(${fmtHours(s.duration_hours)})</span>
                </td>
                <td class="pe-3">${approvalBadge(s.approval_status)}</td>
                <td class="pe-3">${statusBadge(s.status)}</td>
                <td class="text-muted small">${escHtml(s.caption || '—')}</td>
            </tr>
        `).join('');
        return `<table class="table table-sm table-borderless mb-0"><tbody>${rows}</tbody></table>`;
    }

    function toggleIntern(id) {
        const subRow  = document.getElementById(`sub-${id}`);
        const mainRow = document.querySelector(`tr.intern-row-toggle[data-id="${id}"]`);
        const icon    = mainRow?.querySelector('.expand-icon');

        if (!subRow) return;

        if (subRow.style.display === 'none') {
            subRow.style.display = '';
            icon?.classList.add('open');
            expandedInterns.add(id);
        } else {
            subRow.style.display = 'none';
            icon?.classList.remove('open');
            expandedInterns.delete(id);
        }
    }

    // ---- Expand All ----
    let allExpanded = false;
    document.getElementById('expandAllBtn').addEventListener('click', function () {
        allExpanded = !allExpanded;
        if (lastData?.interns) {
            if (allExpanded) {
                lastData.interns.forEach(i => expandedInterns.add(i.id));
            } else {
                expandedInterns.clear();
            }
            renderTable(lastData.interns, lastData.kpi.total_hours);
        }
        this.innerHTML = allExpanded
            ? '<i class="ti ti-chevrons-up me-1"></i>Collapse All'
            : '<i class="ti ti-chevrons-down me-1"></i>Expand All';
    });

    // ---- Week navigation ----
    document.getElementById('prevWeekBtn').addEventListener('click', () => {
        load(addWeeks(currentWeekStart, -1));
    });
    document.getElementById('nextWeekBtn').addEventListener('click', () => {
        const next = addWeeks(currentWeekStart, 1);
        const today = new Date().toISOString().slice(0, 10);
        // Allow navigating up to 4 weeks into the future
        if (next <= addWeeks(today, 4)) load(next);
    });
    document.getElementById('todayBtn').addEventListener('click', () => {
        const today = new Date().toISOString().slice(0, 10);
        load(today);
    });

    // ---- XSS helper ----
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ---- Boot ----
    load(new Date().toISOString().slice(0, 10));

})();
</script>
@endpush
</x-app-layout>
