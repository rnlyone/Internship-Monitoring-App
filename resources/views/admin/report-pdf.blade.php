<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Internship Report – {{ $intern->name }}</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9pt; color: #1e2a38; line-height: 1.4; }

    /* ── Header ──────────────────────────────── */
    .page-header { background: #696cff; color: #fff; padding: 18px 24px 14px; margin-bottom: 18px; }
    .page-header h1 { font-size: 16pt; font-weight: 700; margin-bottom: 2px; }
    .page-header .sub { font-size: 8pt; opacity: .85; }
    .page-header .meta-right { float: right; text-align: right; font-size: 8pt; opacity: .9; }

    /* ── Section titles ──────────────────────── */
    .section-title {
        font-size: 10pt; font-weight: 700; color: #696cff;
        border-bottom: 2px solid #696cff; padding-bottom: 4px; margin: 18px 0 10px;
        text-transform: uppercase; letter-spacing: .04em;
    }

    /* ── Intern identity card ────────────────── */
    .id-card { background: #f5f5ff; border: 1px solid #d0d0ff; border-radius: 6px; padding: 12px 16px; margin-bottom: 18px; }
    .id-card table { width: 100%; }
    .id-card td { padding: 2px 8px 2px 0; font-size: 9pt; }
    .id-card .label { color: #82868b; width: 120px; }
    .id-card .val   { font-weight: 600; }

    /* ── KPI tiles ───────────────────────────── */
    .kpi-row { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .kpi-row td { width: 16.66%; padding: 6px 4px; text-align: center; vertical-align: top; }
    .kpi-box { border-radius: 6px; padding: 8px 4px; }
    .kpi-val { font-size: 16pt; font-weight: 700; line-height: 1; }
    .kpi-lbl { font-size: 7pt; color: #6c757d; margin-top: 3px; }
    .bg-primary   { background: #ede9ff; } .c-primary   { color: #696cff; }
    .bg-success   { background: #e6f9ee; } .c-success   { color: #28c76f; }
    .bg-warning   { background: #fff4e0; } .c-warning   { color: #ff9f43; }
    .bg-danger    { background: #fde9e9; } .c-danger    { color: #ea5455; }
    .bg-info      { background: #e0f9fc; } .c-info      { color: #00cfe8; }
    .bg-secondary { background: #f0f1f3; } .c-secondary { color: #82868b; }
    .bg-purple    { background: #f3e8ff; } .c-purple    { color: #7367f0; }
    .bg-teal      { background: #e0f7f4; } .c-teal      { color: #20c997; }
    .bg-dark      { background: #e8eaed; } .c-dark      { color: #4a5568; }
    .bg-rose      { background: #fff0f3; } .c-rose      { color: #ff6f91; }

    /* ── Progress bar ────────────────────────── */
    .progress-wrap { background: #e9ecef; border-radius: 4px; height: 10px; width: 100%; margin: 4px 0; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 4px; background: #696cff; }

    /* ── Attendance breakdown visual ─────────── */
    .breakdown-row { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .breakdown-row td { padding: 4px 6px; vertical-align: middle; font-size: 8.5pt; }
    .breakdown-bar-wrap { background: #f0f0f0; border-radius: 3px; height: 10px; overflow: hidden; }
    .breakdown-bar { height: 10px; border-radius: 3px; }

    /* ── Tables ──────────────────────────────── */
    table.data-table { width: 100%; border-collapse: collapse; font-size: 8pt; margin-bottom: 12px; }
    table.data-table th { background: #696cff; color: #fff; padding: 5px 6px; text-align: left; font-weight: 600; }
    table.data-table td { padding: 4px 6px; border-bottom: 1px solid #ebebeb; vertical-align: top; }
    table.data-table tr:nth-child(even) td { background: #f9f9ff; }
    table.data-table tr:last-child td { border-bottom: none; }

    /* ── Badges ──────────────────────────────── */
    .badge { display: inline-block; padding: 1px 5px; border-radius: 10px; font-size: 7pt; font-weight: 600; }
    .b-done     { background: rgba(40,199,111,.15);  color: #28c76f; }
    .b-late     { background: rgba(255,159,67,.15);  color: #ff9f43; }
    .b-absence  { background: rgba(234,84,85,.15);   color: #ea5455; }
    .b-ongoing  { background: rgba(0,207,232,.15);   color: #00cfe8; }
    .b-not_yet  { background: rgba(130,134,139,.15); color: #82868b; }
    .b-approved { background: rgba(40,199,111,.15);  color: #28c76f; }
    .b-pending  { background: rgba(255,159,67,.15);  color: #ff9f43; }
    .b-rejected { background: rgba(130,134,139,.15); color: #82868b; }
    .b-backlog     { background: #f0f1f3; color: #6c757d; }
    .b-todo        { background: #ede9ff; color: #696cff; }
    .b-undone      { background: #fff4e0; color: #ff9f43; }
    .b-on_progress { background: #e0f9fc; color: #00cfe8; }
    .b-done-k      { background: #e6f9ee; color: #28c76f; }
    .b-archive     { background: #f0f1f3; color: #82868b; }

    /* ── Logbook entries ─────────────────────── */
    .logbook-entry { background: #fafafa; border: 1px solid #ebebeb; border-radius: 4px; padding: 6px 8px; margin-bottom: 6px; }
    .logbook-meta  { font-size: 7pt; color: #aaa; margin-bottom: 2px; }
    .logbook-text  { font-size: 8.5pt; white-space: pre-wrap; word-break: break-word; }

    /* ── Footer ──────────────────────────────── */
    .page-footer { position: fixed; bottom: 0; left: 0; right: 0; border-top: 1px solid #ddd; padding: 5px 24px; font-size: 7pt; color: #aaa; }
    .page-footer .left  { float: left; }
    .page-footer .right { float: right; }

    /* ── Page break ─────────────────────────── */
    .page-break { page-break-before: always; }

    /* ── Clearfix ─────────────────────────────── */
    .cf::after { content:''; display:table; clear:both; }
</style>
</head>
<body>

{{-- ────────────────── FOOTER (fixed, appears every page) ────────────────── --}}
<div class="page-footer cf">
    <span class="left">Internship Report &mdash; {{ $intern->name }} &mdash; Confidential</span>
    <span class="right">Generated: {{ $generatedAt }}</span>
</div>

{{-- ════════════════════ PAGE 1 — SUMMARY & OVERVIEW ════════════════════ --}}

{{-- Header --}}
<div class="page-header cf">
    <div class="meta-right">
        Generated: {{ $generatedAt }}<br>
        @if($dateFrom || $dateTo)
            Period: {{ $dateFrom ?? '—' }} to {{ $dateTo ?? '—' }}
        @else
            Period: All time
        @endif
    </div>
    <h1>Internship Progress Report</h1>
    <div class="sub">Comprehensive Performance & Activity Report &mdash; Confidential</div>
</div>

{{-- Intern identity --}}
<div class="id-card cf">
    <table>
        <tr>
            <td class="label">Full Name</td>
            <td class="val">{{ $intern->name }}</td>
            <td class="label">Internship Start</td>
            <td class="val">{{ $summary['internship_start'] }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td class="val">{{ $intern->email }}</td>
            <td class="label">Internship End</td>
            <td class="val">{{ $summary['internship_end'] }}</td>
        </tr>
        <tr>
            <td class="label">Role</td>
            <td class="val">Intern</td>
            <td class="label">Report Period</td>
            <td class="val">
                @if($dateFrom || $dateTo)
                    {{ $dateFrom ?? '—' }} &mdash; {{ $dateTo ?? '—' }}
                @else
                    All time
                @endif
            </td>
        </tr>
    </table>
</div>

{{-- KPI Row 1 — Schedule stats --}}
<div class="section-title">&#9654; Performance Overview</div>

<table class="kpi-row">
    <tr>
        <td><div class="kpi-box bg-primary">
            <div class="kpi-val c-primary">{{ $summary['total_schedules'] }}</div>
            <div class="kpi-lbl">Total Schedules</div>
        </div></td>
        <td><div class="kpi-box bg-success">
            <div class="kpi-val c-success">{{ $summary['completed'] }}</div>
            <div class="kpi-lbl">Completed</div>
        </div></td>
        <td><div class="kpi-box bg-warning">
            <div class="kpi-val c-warning">{{ $summary['late'] }}</div>
            <div class="kpi-lbl">Late</div>
        </div></td>
        <td><div class="kpi-box bg-danger">
            <div class="kpi-val c-danger">{{ $summary['absence'] }}</div>
            <div class="kpi-lbl">Absence</div>
        </div></td>
        <td><div class="kpi-box bg-secondary">
            <div class="kpi-val c-secondary">{{ $summary['not_yet'] }}</div>
            <div class="kpi-lbl">Not Yet</div>
        </div></td>
        <td><div class="kpi-box bg-info">
            <div class="kpi-val c-info">{{ $summary['ongoing'] }}</div>
            <div class="kpi-lbl">Ongoing</div>
        </div></td>
    </tr>
</table>

<table class="kpi-row">
    <tr>
        <td><div class="kpi-box bg-purple">
            <div class="kpi-val c-purple">{{ $summary['total_hours'] }}h</div>
            <div class="kpi-lbl">Total Work Hours</div>
        </div></td>
        <td><div class="kpi-box bg-success">
            <div class="kpi-val c-success">{{ $summary['approved'] }}</div>
            <div class="kpi-lbl">Approved</div>
        </div></td>
        <td><div class="kpi-box bg-warning">
            <div class="kpi-val c-warning">{{ $summary['pending'] }}</div>
            <div class="kpi-lbl">Pending Approval</div>
        </div></td>
        <td><div class="kpi-box bg-secondary">
            <div class="kpi-val c-secondary">{{ $summary['rejected'] }}</div>
            <div class="kpi-lbl">Rejected</div>
        </div></td>
        <td><div class="kpi-box bg-teal">
            <div class="kpi-val c-teal">{{ $summary['shift_logs'] }}</div>
            <div class="kpi-lbl">Log Entries</div>
        </div></td>
        <td><div class="kpi-box bg-dark">
            <div class="kpi-val c-dark">{{ $summary['kanban_assigned'] }}</div>
            <div class="kpi-lbl">Kanban Assigned</div>
        </div></td>
    </tr>
</table>

{{-- Attendance rate visual --}}
<table style="width:100%;border-collapse:collapse;margin-bottom:14px;">
    <tr>
        <td style="width:140px;font-size:8.5pt;color:#6c757d;vertical-align:middle;">Attendance Rate</td>
        <td style="vertical-align:middle;padding:0 10px;">
            <div class="progress-wrap">
                <div class="progress-fill" style="width:{{ $summary['attendance_rate'] }}%;"></div>
            </div>
        </td>
        <td style="width:50px;font-size:11pt;font-weight:700;color:#696cff;text-align:right;vertical-align:middle;">
            {{ $summary['attendance_rate'] }}%
        </td>
    </tr>
    <tr>
        <td style="width:140px;font-size:8.5pt;color:#6c757d;vertical-align:middle;">Total Log Entries</td>
        <td style="vertical-align:middle;padding:0 10px;font-size:8.5pt;">
            {{ $summary['shift_logs'] }} logbook entries across all shifts
        </td>
        <td></td>
    </tr>
</table>

{{-- Attendance breakdown bar chart (text-based) --}}
@if($summary['total_schedules'] > 0)
@php
    $total = $summary['total_schedules'];
    $bars = [
        ['label'=>'Completed', 'val'=>$summary['completed'], 'color'=>'#28c76f'],
        ['label'=>'Late',      'val'=>$summary['late'],      'color'=>'#ff9f43'],
        ['label'=>'Absence',   'val'=>$summary['absence'],   'color'=>'#ea5455'],
        ['label'=>'Not Yet',   'val'=>$summary['not_yet'],   'color'=>'#a1acb8'],
        ['label'=>'Ongoing',   'val'=>$summary['ongoing'],   'color'=>'#00cfe8'],
    ];
@endphp
<div class="section-title">&#9654; Attendance Breakdown</div>
<table class="breakdown-row">
    @foreach($bars as $bar)
    @php $pct = $total > 0 ? round($bar['val']/$total*100,1) : 0; @endphp
    <tr>
        <td style="width:90px;color:#6c757d;">{{ $bar['label'] }}</td>
        <td style="width:30px;text-align:right;font-weight:600;">{{ $bar['val'] }}</td>
        <td style="padding:0 8px;">
            <div class="breakdown-bar-wrap">
                <div class="breakdown-bar" style="width:{{ $pct }}%;background:{{ $bar['color'] }};"></div>
            </div>
        </td>
        <td style="width:40px;color:#6c757d;font-size:7.5pt;">{{ $pct }}%</td>
    </tr>
    @endforeach
</table>
@endif

{{-- Weekly breakdown table --}}
@if(count($weeklyBreakdown) > 0)
<div class="section-title">&#9654; Weekly Breakdown</div>
<table class="data-table">
    <thead>
        <tr>
            <th>Week</th>
            <th>Date Range</th>
            <th style="text-align:center">Sessions</th>
            <th style="text-align:center">Hours</th>
            <th style="text-align:center">Done</th>
            <th style="text-align:center">Late</th>
            <th style="text-align:center">Absence</th>
            <th style="text-align:center">Log Entries</th>
        </tr>
    </thead>
    <tbody>
        @foreach($weeklyBreakdown as $week)
        <tr>
            <td>{{ $week['week'] }}</td>
            <td>{{ $week['date_range'] }}</td>
            <td style="text-align:center">{{ $week['count'] }}</td>
            <td style="text-align:center">{{ $week['hours'] }}h</td>
            <td style="text-align:center;color:#28c76f;font-weight:600;">{{ $week['done'] }}</td>
            <td style="text-align:center;color:#ff9f43;font-weight:600;">{{ $week['late'] }}</td>
            <td style="text-align:center;color:#ea5455;font-weight:600;">{{ $week['absence'] }}</td>
            <td style="text-align:center">{{ $week['logs'] }}</td>
        </tr>
        @endforeach
        <tr style="font-weight:700;background:#ede9ff;">
            <td colspan="2">Total</td>
            <td style="text-align:center">{{ $summary['total_schedules'] }}</td>
            <td style="text-align:center">{{ $summary['total_hours'] }}h</td>
            <td style="text-align:center;color:#28c76f;">{{ $summary['completed'] }}</td>
            <td style="text-align:center;color:#ff9f43;">{{ $summary['late'] }}</td>
            <td style="text-align:center;color:#ea5455;">{{ $summary['absence'] }}</td>
            <td style="text-align:center">{{ $summary['shift_logs'] }}</td>
        </tr>
    </tbody>
</table>
@endif

{{-- Kanban summary --}}
@if($kanbanByCol->flatten()->count() > 0)
<div class="section-title">&#9654; Kanban / Task Board</div>
<table class="kpi-row">
    <tr>
        @php
        $kanbanKpis = [
            ['label'=>'Assigned Cards', 'val'=>$summary['kanban_assigned'], 'bg'=>'bg-primary', 'c'=>'c-primary'],
            ['label'=>'Tasks Done',     'val'=>$summary['kanban_done'],     'bg'=>'bg-success', 'c'=>'c-success'],
        ];
        @endphp
        @foreach($kanbanKpis as $kpi)
        <td style="width:50%"><div class="kpi-box {{ $kpi['bg'] }}">
            <div class="kpi-val {{ $kpi['c'] }}">{{ $kpi['val'] }}</div>
            <div class="kpi-lbl">{{ $kpi['label'] }}</div>
        </div></td>
        @endforeach
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th style="width:40px">#</th>
            <th>Card Title</th>
            <th style="width:90px">Column</th>
            <th style="width:60px">Priority</th>
            <th style="width:80px">Due Date</th>
            <th style="width:70px">Assigned To</th>
            <th style="width:80px">Created By</th>
        </tr>
    </thead>
    <tbody>
        @php $kIdx = 1; @endphp
        @foreach($kanbanColumns as $colKey => $colLabel)
            @if(isset($kanbanByCol[$colKey]) && $kanbanByCol[$colKey]->count() > 0)
                @foreach($kanbanByCol[$colKey] as $card)
                <tr>
                    <td>{{ $kIdx++ }}</td>
                    <td>
                        <strong>{{ $card->title }}</strong>
                        @if($card->description)
                            <br><span style="color:#888;font-size:7pt">{{ Str::limit($card->description, 80) }}</span>
                        @endif
                    </td>
                    <td>
                        @php
                        $badgeCls = ['backlog'=>'b-backlog','todo'=>'b-todo','undone'=>'b-undone','on_progress'=>'b-on_progress','done'=>'b-done-k','archive'=>'b-archive'];
                        @endphp
                        <span class="badge {{ $badgeCls[$card->column_name] ?? 'b-backlog' }}">{{ $colLabel }}</span>
                    </td>
                    <td>
                        @if($card->priority)
                            @php $pc=['low'=>'b-done','medium'=>'b-late','high'=>'b-absence']; @endphp
                            <span class="badge {{ $pc[$card->priority] ?? '' }}">{{ ucfirst($card->priority) }}</span>
                        @else —
                        @endif
                    </td>
                    <td>{{ $card->due_date ? \Carbon\Carbon::parse($card->due_date)->format('d M Y') : '—' }}</td>
                    <td>{{ $card->assignedUser?->name ?? '—' }}</td>
                    <td>{{ $card->creator?->name ?? '—' }}</td>
                </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
</table>
@endif

{{-- ════════════════════ PAGE 2 — SCHEDULE DETAIL ════════════════════ --}}
@if(count($scheduleRows) > 0)
<div class="page-break"></div>
<div class="page-header cf">
    <div class="meta-right">{{ $intern->name }}</div>
    <h1>Schedule Detail</h1>
    <div class="sub">All shifts and presence records &mdash; {{ count($scheduleRows) }} sessions</div>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th style="width:110px">Date</th>
            <th style="width:90px">Shift</th>
            <th style="width:35px">Hrs</th>
            <th>Caption</th>
            <th style="width:60px">Status</th>
            <th style="width:60px">Approval</th>
            <th style="width:42px">Entry</th>
            <th style="width:42px">Exit</th>
            <th style="width:45px">Late</th>
            <th style="width:25px">Logs</th>
        </tr>
    </thead>
    <tbody>
        @foreach($scheduleRows as $s)
        @php
        $sCls  = ['done'=>'b-done','late'=>'b-late','absence'=>'b-absence','ongoing'=>'b-ongoing','not_yet'=>'b-not_yet'];
        $asCls = ['approved'=>'b-approved','pending'=>'b-pending','rejected'=>'b-rejected'];
        $sLbl  = ['done'=>'Done','late'=>'Late','absence'=>'Absence','ongoing'=>'Ongoing','not_yet'=>'Not Yet'];
        @endphp
        <tr>
            <td style="font-size:7.5pt">{{ $s['date'] }}</td>
            <td style="font-size:7.5pt;font-weight:600">{{ $s['shift'] }}</td>
            <td style="text-align:center">{{ $s['hours'] }}</td>
            <td style="font-size:7.5pt;color:#555">{{ $s['caption'] }}</td>
            <td><span class="badge {{ $sCls[$s['status']] ?? '' }}">{{ $sLbl[$s['status']] ?? $s['status'] }}</span></td>
            <td><span class="badge {{ $asCls[$s['approval_status']] ?? '' }}">{{ ucfirst($s['approval_status']) }}</span></td>
            <td style="text-align:center;font-size:7.5pt">{{ $s['entry_time'] }}</td>
            <td style="text-align:center;font-size:7.5pt">{{ $s['exit_time'] }}</td>
            <td style="text-align:center;font-size:7.5pt">
                {{ $s['late_minutes'] !== null ? $s['late_minutes'].'m' : '—' }}
            </td>
            <td style="text-align:center">{{ count($s['logbooks']) }}</td>
        </tr>
        {{-- Inline logbook entries per shift --}}
        @if(count($s['logbooks']) > 0)
        <tr>
            <td colspan="10" style="padding:0 8px 8px 20px;background:#f8f7ff;">
                @foreach($s['logbooks'] as $log)
                <div class="logbook-entry">
                    <div class="logbook-meta">{{ $log['created_at'] }}</div>
                    <div class="logbook-text">{{ $log['content'] }}</div>
                </div>
                @endforeach
            </td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>
@endif


</body>
</html>
