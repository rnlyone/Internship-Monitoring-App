<x-app-layout>
@push('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
<style>
.perf-kpi-card { transition: transform .18s, box-shadow .18s; cursor: default; }
.perf-kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(115,103,240,.18); }
.score-badge { font-size: 1.35rem; font-weight: 700; }
.rank-1 { color: #f4b400; }
.rank-2 { color: #9aa0a6; }
.rank-3 { color: #c06000; }
.intern-tab.active { background: var(--bs-primary) !important; color: #fff !important; }
.intern-tab { cursor: pointer; }
#radarInternSelect { min-width: 180px; }
</style>
@endpush

<div class="row g-6">

    <!-- Page Header -->
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 class="mb-1"><i class="ti ti-chart-bar me-2 text-primary"></i>Internship Performance Monitor</h4>
                <p class="text-muted mb-0">Comprehensive performance analytics for all interns</p>
            </div>
            <button class="btn btn-outline-primary btn-sm" id="refreshBtn">
                <i class="ti ti-refresh me-1"></i>Refresh
            </button>
        </div>
    </div>

    <!-- KPI Summary Row -->
    <div class="col-sm-6 col-xl-2">
        <div class="card perf-kpi-card h-100">
            <div class="card-body text-center py-4">
                <div class="avatar avatar-md mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="ti ti-users ti-md"></i></span>
                </div>
                <h3 class="mb-0" id="kpiInterns">—</h3>
                <p class="text-muted small mb-0">Total Interns</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card perf-kpi-card h-100">
            <div class="card-body text-center py-4">
                <div class="avatar avatar-md mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-success"><i class="ti ti-circle-check ti-md"></i></span>
                </div>
                <h3 class="mb-0" id="kpiAttendance">—</h3>
                <p class="text-muted small mb-0">Avg Attendance</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card perf-kpi-card h-100">
            <div class="card-body text-center py-4">
                <div class="avatar avatar-md mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-info"><i class="ti ti-clock-check ti-md"></i></span>
                </div>
                <h3 class="mb-0" id="kpiPunctuality">—</h3>
                <p class="text-muted small mb-0">Avg Punctuality</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card perf-kpi-card h-100">
            <div class="card-body text-center py-4">
                <div class="avatar avatar-md mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-warning"><i class="ti ti-clock ti-md"></i></span>
                </div>
                <h3 class="mb-0" id="kpiHours">—</h3>
                <p class="text-muted small mb-0">Total Hours</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card perf-kpi-card h-100">
            <div class="card-body text-center py-4">
                <div class="avatar avatar-md mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-secondary"><i class="ti ti-notebook ti-md"></i></span>
                </div>
                <h3 class="mb-0" id="kpiLogbooks">—</h3>
                <p class="text-muted small mb-0">Total Logbooks</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-2">
        <div class="card perf-kpi-card h-100" style="border: 2px solid var(--bs-primary);">
            <div class="card-body text-center py-4">
                <div class="avatar avatar-md mb-3 mx-auto">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="ti ti-trophy ti-md"></i></span>
                </div>
                <h6 class="mb-0 fw-bold text-primary" id="kpiBest">—</h6>
                <p class="text-muted small mb-0">Top Performer</p>
            </div>
        </div>
    </div>

    <!-- Leaderboard Table -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0"><i class="ti ti-list-numbers me-2"></i>Performance Leaderboard</h5>
                <span class="badge bg-label-primary" id="leaderboardBadge">0 interns</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Intern</th>
                            <th>Score</th>
                            <th>Attendance</th>
                            <th>Punctuality</th>
                            <th>Hours</th>
                            <th>Shifts (Past)</th>
                            <th>Late</th>
                            <th>Absent</th>
                            <th>Logbooks</th>
                            <th>Kanban</th>
                            <th>Avg Late</th>
                        </tr>
                    </thead>
                    <tbody id="leaderboardBody">
                        <tr><td colspan="12" class="text-center text-muted py-4"><i class="ti ti-loader ti-spin me-2"></i>Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Attendance & Punctuality Comparison -->
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-chart-bar me-2"></i>Attendance vs Punctuality Rate</h5>
                <p class="text-muted small mb-0">% per intern (all-time)</p>
            </div>
            <div class="card-body">
                <div id="chartAttendance"></div>
            </div>
        </div>
    </div>

    <!-- Hours Logged -->
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-clock-hour-4 me-2"></i>Total Hours Logged per Intern</h5>
                <p class="text-muted small mb-0">Scheduled vs completed hours</p>
            </div>
            <div class="card-body">
                <div id="chartHours"></div>
            </div>
        </div>
    </div>

    <!-- Status Stacked Bar -->
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-chart-bar-popular me-2"></i>Shift Status Breakdown</h5>
                <p class="text-muted small mb-0">Done / Late / Absent per intern</p>
            </div>
            <div class="card-body">
                <div id="chartStatus"></div>
            </div>
        </div>
    </div>

    <!-- Logbook Engagement -->
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-notebook me-2"></i>Logbook Engagement</h5>
                <p class="text-muted small mb-0">Entries per intern (% of attended shifts)</p>
            </div>
            <div class="card-body">
                <div id="chartLogbook"></div>
            </div>
        </div>
    </div>

    <!-- Weekly Trend -->
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0"><i class="ti ti-trending-up me-2"></i>Weekly Hours Trend</h5>
                    <p class="text-muted small mb-0">Last 8 weeks — per intern</p>
                </div>
            </div>
            <div class="card-body">
                <div id="chartWeekly"></div>
            </div>
        </div>
    </div>

    <!-- Radar: Multi-dimension per intern -->
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="mb-0"><i class="ti ti-radar me-2"></i>Performance Radar</h5>
                    <p class="text-muted small mb-0">5-dimension score</p>
                </div>
                <select class="form-select form-select-sm" id="radarInternSelect" style="width:auto"></select>
            </div>
            <div class="card-body">
                <div id="chartRadar"></div>
            </div>
        </div>
    </div>

    <!-- Scatter: Hours vs Logbooks -->
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-chart-dots me-2"></i>Engagement Scatter</h5>
                <p class="text-muted small mb-0">Completed hours vs logbook entries</p>
            </div>
            <div class="card-body">
                <div id="chartScatter"></div>
            </div>
        </div>
    </div>

    <!-- Kanban Completion -->
    <div class="col-xl-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="ti ti-layout-kanban me-2"></i>Kanban Task Completion</h5>
                <p class="text-muted small mb-0">Done / Total assigned per intern</p>
            </div>
            <div class="card-body">
                <div id="chartKanban"></div>
            </div>
        </div>
    </div>

</div>

@push('page-js')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const COLORS = ['#7367f0','#28c76f','#ff9f43','#ea5455','#00bad1','#4b4b4b','#6610f2','#e83e8c'];
    let charts = {};
    let perfData = null;

    // ── Helpers ─────────────────────────────────────────────────────────────
    function pct(v) { return v.toFixed(1) + '%'; }

    function rankIcon(i) {
        if (i === 0) return '<span class="rank-1 me-1">🥇</span>';
        if (i === 1) return '<span class="rank-2 me-1">🥈</span>';
        if (i === 2) return '<span class="rank-3 me-1">🥉</span>';
        return `<span class="text-muted me-1">${i+1}</span>`;
    }

    function scoreBadgeColor(s) {
        if (s >= 80) return 'success';
        if (s >= 60) return 'primary';
        if (s >= 40) return 'warning';
        return 'danger';
    }

    function destroyChart(key) {
        if (charts[key]) { charts[key].destroy(); delete charts[key]; }
    }

    // ── Base chart options ───────────────────────────────────────────────────
    const baseOpts = {
        chart: { fontFamily: 'Public Sans, sans-serif', toolbar: { show: false } },
        grid: { borderColor: 'rgba(75,75,75,.1)', strokeDashArray: 4 },
        tooltip: { theme: document.documentElement.classList.contains('dark-style') ? 'dark' : 'light' },
    };

    // ── Load & render ────────────────────────────────────────────────────────
    function load() {
        fetch('{{ route("admin.performance.data") }}', { headers: { Accept: 'application/json' } })
            .then(r => r.json())
            .then(render);
    }

    function render(data) {
        perfData = data;
        const interns = data.interns;
        const names   = interns.map(i => i.name);

        // ── KPI Cards ──────────────────────────────────────────────────
        document.getElementById('kpiInterns').textContent    = data.summary.total_interns;
        document.getElementById('kpiAttendance').textContent = pct(data.summary.avg_attendance);
        document.getElementById('kpiPunctuality').textContent= pct(data.summary.avg_punctuality);
        document.getElementById('kpiHours').textContent      = data.summary.total_hours + ' hrs';
        document.getElementById('kpiLogbooks').textContent   = data.summary.total_logbooks;
        document.getElementById('kpiBest').textContent       = data.summary.best_performer + ' (' + data.summary.best_score + ')';

        // ── Leaderboard ────────────────────────────────────────────────
        const sorted = [...interns].sort((a,b) => b.performance_score - a.performance_score);
        document.getElementById('leaderboardBadge').textContent = interns.length + ' intern' + (interns.length !== 1 ? 's' : '');
        if (interns.length === 0) {
            document.getElementById('leaderboardBody').innerHTML =
                '<tr><td colspan="12" class="text-center text-muted py-4">No intern data found.</td></tr>';
        } else {
            document.getElementById('leaderboardBody').innerHTML = sorted.map((intern, idx) => `
                <tr>
                    <td>${rankIcon(idx)}</td>
                    <td>
                        <div class="fw-semibold">${intern.name}</div>
                        <small class="text-muted">${intern.email}</small>
                    </td>
                    <td><span class="badge bg-label-${scoreBadgeColor(intern.performance_score)} score-badge">${intern.performance_score}</span></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:6px;min-width:60px">
                                <div class="progress-bar bg-success" style="width:${intern.attendance_rate}%"></div>
                            </div>
                            <span class="small">${pct(intern.attendance_rate)}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:6px;min-width:60px">
                                <div class="progress-bar bg-info" style="width:${intern.punctuality_rate}%"></div>
                            </div>
                            <span class="small">${pct(intern.punctuality_rate)}</span>
                        </div>
                    </td>
                    <td><strong>${intern.completed_hours}</strong> <small class="text-muted">/ ${intern.total_hours}h</small></td>
                    <td>${intern.past_shifts}</td>
                    <td><span class="badge bg-label-warning">${intern.late}</span></td>
                    <td><span class="badge bg-label-danger">${intern.absence}</span></td>
                    <td><span class="badge bg-label-secondary">${intern.logbook_entries}</span></td>
                    <td>${intern.kanban_done}/${intern.kanban_total} <small class="text-muted">(${pct(intern.kanban_rate)})</small></td>
                    <td>${intern.avg_late_minutes > 0 ? intern.avg_late_minutes + ' min' : '<span class="text-muted">—</span>'}</td>
                </tr>
            `).join('');
        }

        if (interns.length === 0) return;

        // ── Chart 1: Attendance vs Punctuality ────────────────────────
        destroyChart('attendance');
        charts['attendance'] = new ApexCharts(document.getElementById('chartAttendance'), {
            ...baseOpts,
            chart: { ...baseOpts.chart, type: 'bar', height: 300 },
            series: [
                { name: 'Attendance Rate', data: interns.map(i => i.attendance_rate) },
                { name: 'Punctuality Rate', data: interns.map(i => i.punctuality_rate) },
            ],
            xaxis: { categories: names, labels: { style: { fontSize: '12px' } } },
            yaxis: { min: 0, max: 100, labels: { formatter: v => v + '%' } },
            colors: ['#28c76f', '#00bad1'],
            plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            legend: { position: 'top' },
        });
        charts['attendance'].render();

        // ── Chart 2: Total Hours ──────────────────────────────────────
        destroyChart('hours');
        charts['hours'] = new ApexCharts(document.getElementById('chartHours'), {
            ...baseOpts,
            chart: { ...baseOpts.chart, type: 'bar', height: 300 },
            series: [
                { name: 'Total Scheduled', data: interns.map(i => i.total_hours) },
                { name: 'Completed Hours', data: interns.map(i => i.completed_hours) },
            ],
            xaxis: { categories: names, labels: { style: { fontSize: '12px' } } },
            yaxis: { labels: { formatter: v => v + 'h' } },
            colors: ['#7367f0', '#ff9f43'],
            plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
            dataLabels: { enabled: false },
            legend: { position: 'top' },
        });
        charts['hours'].render();

        // ── Chart 3: Status Stacked Bar ───────────────────────────────
        destroyChart('status');
        charts['status'] = new ApexCharts(document.getElementById('chartStatus'), {
            ...baseOpts,
            chart: { ...baseOpts.chart, type: 'bar', height: 300, stacked: true },
            series: [
                { name: 'Done',    data: interns.map(i => i.done) },
                { name: 'Late',    data: interns.map(i => i.late) },
                { name: 'Absent',  data: interns.map(i => i.absence) },
            ],
            xaxis: { categories: names },
            colors: ['#28c76f', '#ff9f43', '#ea5455'],
            plotOptions: { bar: { borderRadius: 4, horizontal: false } },
            dataLabels: { enabled: false },
            legend: { position: 'top' },
        });
        charts['status'].render();

        // ── Chart 4: Logbook Engagement ───────────────────────────────
        destroyChart('logbook');
        charts['logbook'] = new ApexCharts(document.getElementById('chartLogbook'), {
            ...baseOpts,
            chart: { ...baseOpts.chart, type: 'bar', height: 300 },
            series: [
                { name: 'Total Entries',       data: interns.map(i => i.logbook_entries) },
                { name: 'Logbook Rate (%)',     data: interns.map(i => i.logbook_rate) },
            ],
            xaxis: { categories: names },
            yaxis: [
                { title: { text: 'Entries' } },
                { opposite: true, title: { text: '%' }, labels: { formatter: v => v + '%' } },
            ],
            colors: ['#6610f2', '#e83e8c'],
            plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
            dataLabels: { enabled: false },
            legend: { position: 'top' },
        });
        charts['logbook'].render();

        // ── Chart 5: Weekly Trend ─────────────────────────────────────
        destroyChart('weekly');
        charts['weekly'] = new ApexCharts(document.getElementById('chartWeekly'), {
            ...baseOpts,
            chart: { ...baseOpts.chart, type: 'area', height: 300 },
            series: interns.map((intern, idx) => ({
                name: intern.name,
                data: intern.weekly_hours,
            })),
            xaxis: { categories: data.week_labels },
            yaxis: { labels: { formatter: v => v + 'h' } },
            colors: COLORS,
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.2, opacityTo: 0 } },
            dataLabels: { enabled: false },
            legend: { position: 'top' },
        });
        charts['weekly'].render();

        // ── Chart 6: Radar ────────────────────────────────────────────
        const radarSelect = document.getElementById('radarInternSelect');
        radarSelect.innerHTML = interns.map((i, idx) => `<option value="${idx}">${i.name}</option>`).join('');
        radarSelect.addEventListener('change', () => renderRadar(parseInt(radarSelect.value)));
        renderRadar(0);

        // ── Chart 7: Scatter ──────────────────────────────────────────
        destroyChart('scatter');
        charts['scatter'] = new ApexCharts(document.getElementById('chartScatter'), {
            ...baseOpts,
            chart: { ...baseOpts.chart, type: 'scatter', height: 300 },
            series: interns.map((intern, idx) => ({
                name: intern.name,
                data: [{ x: intern.completed_hours, y: intern.logbook_entries }],
            })),
            xaxis: { title: { text: 'Completed Hours' }, labels: { formatter: v => v + 'h' } },
            yaxis: { title: { text: 'Logbook Entries' } },
            colors: COLORS,
            markers: { size: 10 },
            legend: { position: 'top' },
            tooltip: {
                custom: function({ seriesIndex, dataPointIndex, w }) {
                    const i = interns[seriesIndex];
                    return `<div class="p-2"><strong>${i.name}</strong><br>${i.completed_hours}h worked · ${i.logbook_entries} entries</div>`;
                }
            }
        });
        charts['scatter'].render();

        // ── Chart 8: Kanban ───────────────────────────────────────────
        destroyChart('kanban');
        const internWithKanban = interns.filter(i => i.kanban_total > 0);
        if (internWithKanban.length > 0) {
            charts['kanban'] = new ApexCharts(document.getElementById('chartKanban'), {
                ...baseOpts,
                chart: { ...baseOpts.chart, type: 'bar', height: 300 },
                series: [
                    { name: 'Done',      data: interns.map(i => i.kanban_done) },
                    { name: 'Remaining', data: interns.map(i => i.kanban_total - i.kanban_done) },
                ],
                xaxis: { categories: names },
                colors: ['#28c76f', '#ea5455'],
                plotOptions: { bar: { borderRadius: 4, horizontal: true, stacked: true } },
                dataLabels: { enabled: false },
                legend: { position: 'top' },
            });
        } else {
            charts['kanban'] = new ApexCharts(document.getElementById('chartKanban'), {
                ...baseOpts,
                chart: { ...baseOpts.chart, type: 'bar', height: 300 },
                series: [
                    { name: 'Done',      data: interns.map(i => i.kanban_done) },
                    { name: 'Total',     data: interns.map(i => i.kanban_total) },
                ],
                xaxis: { categories: names },
                colors: ['#28c76f', '#7367f0'],
                plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                dataLabels: { enabled: false },
                legend: { position: 'top' },
                noData: { text: 'No Kanban cards assigned yet.' },
            });
        }
        charts['kanban'].render();
    }

    function renderRadar(internIdx) {
        destroyChart('radar');
        if (!perfData || !perfData.interns[internIdx]) return;
        const intern = perfData.interns[internIdx];
        charts['radar'] = new ApexCharts(document.getElementById('chartRadar'), {
            ...baseOpts,
            chart: { ...baseOpts.chart, type: 'radar', height: 320, toolbar: { show: false } },
            series: [{ name: intern.name, data: [
                intern.attendance_rate,
                intern.punctuality_rate,
                Math.min(intern.logbook_rate, 100),
                intern.kanban_rate,
                intern.performance_score,
            ]}],
            xaxis: { categories: ['Attendance', 'Punctuality', 'Logbooks', 'Kanban', 'Overall Score'] },
            yaxis: { min: 0, max: 100 },
            fill: { opacity: 0.2 },
            stroke: { width: 2 },
            markers: { size: 4 },
            colors: ['#7367f0'],
            dataLabels: { enabled: true, formatter: v => v + '%' },
        });
        charts['radar'].render();
    }

    // ── Init ─────────────────────────────────────────────────────────────────
    load();
    document.getElementById('refreshBtn').addEventListener('click', load);
});
</script>
@endpush
</x-app-layout>
