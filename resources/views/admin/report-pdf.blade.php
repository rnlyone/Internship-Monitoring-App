<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Internship Report – {{ $intern->name }}</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700;800&display=swap');

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body, * { font-family: 'Lexend', 'DejaVu Sans', sans-serif !important; }

    body {
        color: #1e293b;
        font-size: 9pt;
        line-height: 1.5;
        padding: 20px 22px 60px;
        background: #f8fafc;
    }

    /* ── HEADER (KEPT AS-IS) ── */
    .doc-header {
        display: table;
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 12px;
        margin-bottom: 14px;
        background: #ffffff;
    }
    .doc-header .logo-cell,
    .doc-header .text-cell { display: table-cell; vertical-align: middle; }
    .doc-header .logo-cell { width: 34px; }
    .doc-header img { width: 24px; height: 24px; display: block; }
    .doc-header .brand { font-size: 11pt; font-weight: 800; color: #0f172a; line-height: 1.2; }
    .doc-header .tagline { font-size: 7.3pt; color: #64748b; letter-spacing: .05em; text-transform: uppercase; margin-top: 1px; }

    /* ── PAGE SHELL ── */
    .report-shell {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        overflow: hidden;
    }

    /* ── REPORT TITLE BANNER ── */
    .report-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
        padding: 16px 20px;
        position: relative;
    }
    .report-banner h1 {
        font-size: 13pt;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: .02em;
        margin-bottom: 3px;
    }
    .report-banner .sub {
        font-size: 7.8pt;
        color: #94a3b8;
        font-weight: 400;
    }
    .report-banner .meta {
        float: right;
        text-align: right;
        font-size: 7.2pt;
        color: #cbd5e1;
        line-height: 1.5;
    }
    .cf::after { content: ''; display: table; clear: both; }

    /* ── PROFILE AREA ── */
    .profile-area {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
    }
    .profile-grid {
        width: 100%;
        border-collapse: collapse;
    }
    .profile-grid td {
        padding: 3.5px 8px 3.5px 0;
        font-size: 8.6pt;
        vertical-align: top;
    }
    .profile-grid .lbl {
        width: 110px;
        color: #64748b;
        font-weight: 500;
    }
    .profile-grid .val {
        color: #0f172a;
        font-weight: 700;
    }

    /* ── GRADE CHIP ── */
    .grade-pill {
        display: inline-block;
        background: linear-gradient(135deg, #1d4ed8, #3b82f6);
        color: #fff;
        border-radius: 999px;
        padding: 4px 14px;
        font-size: 9pt;
        font-weight: 800;
        letter-spacing: .05em;
    }
    .grade-block {
        text-align: center;
        padding: 10px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
    }
    .grade-block .glabel {
        font-size: 6.8pt;
        color: #1e40af;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        margin-bottom: 6px;
    }
    .grade-big {
        font-size: 28pt;
        font-weight: 800;
        color: #1e3a8a;
        line-height: 1;
    }

    /* ── SECTION HEADING ── */
    .section-heading {
        display: table;
        width: 100%;
        margin: 0 0 10px;
    }
    .section-heading-inner {
        padding: 7px 18px;
        background: #f1f5f9;
        border-left: 4px solid #3b82f6;
    }
    .section-heading-inner span {
        font-size: 7.8pt;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .1em;
        color: #1e40af;
    }

    .section-body {
        padding: 0 18px 16px;
    }

    /* ── KPI CARDS ── */
    .kpi-row {
        width: 100%;
        border-collapse: separate;
        border-spacing: 6px;
        margin: -6px;
    }
    .kpi-row td { vertical-align: top; }
    .kpi-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 8px;
        text-align: center;
        background: #ffffff;
    }
    .kpi-value {
        font-size: 16pt;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 3px;
    }
    .kpi-label {
        font-size: 6.8pt;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        font-weight: 600;
    }

    .tone-blue   { color: #2563eb; }
    .tone-green  { color: #16a34a; }
    .tone-amber  { color: #d97706; }
    .tone-red    { color: #dc2626; }
    .tone-slate  { color: #475569; }
    .tone-cyan   { color: #0891b2; }
    .tone-violet { color: #7c3aed; }
    .tone-indigo { color: #4338ca; }

    /* ── ATTENDANCE RATE ── */
    .att-row {
        display: table;
        width: 100%;
        border-collapse: collapse;
        margin-top: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
    }
    .att-label {
        font-size: 8pt;
        color: #334155;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .bar {
        height: 9px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
    }
    .bar > span {
        display: block;
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #2563eb, #60a5fa);
    }
    .att-pct {
        font-size: 13pt;
        font-weight: 800;
        color: #2563eb;
        text-align: right;
        float: right;
        margin-top: -24px;
    }

    /* ── BREAKDOWN ── */
    .breakdown-table {
        width: 100%;
        border-collapse: collapse;
    }
    .breakdown-table td {
        padding: 5px 0;
        font-size: 8.4pt;
        vertical-align: middle;
    }
    .mini-bar {
        height: 7px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
    }
    .mini-bar span {
        display: block;
        height: 100%;
        border-radius: 999px;
    }

    /* ── DATA TABLE ── */
    .table-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        margin-top: 8px;
    }
    table.data {
        width: 100%;
        border-collapse: collapse;
        font-size: 8pt;
    }
    table.data th {
        text-align: left;
        background: #f1f5f9;
        color: #334155;
        padding: 7px 9px;
        font-size: 6.8pt;
        text-transform: uppercase;
        letter-spacing: .06em;
        font-weight: 700;
        border-bottom: 1px solid #e2e8f0;
    }
    table.data td {
        padding: 7px 9px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
        color: #1e293b;
    }
    table.data tr:last-child td { border-bottom: none; }
    table.data tbody tr:nth-child(odd) { background: #fafbfd; }

    /* ── BADGES ── */
    .badge {
        display: inline-block;
        border-radius: 999px;
        padding: 2px 8px;
        font-size: 6.5pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .b-green  { background: #dcfce7; color: #15803d; }
    .b-amber  { background: #fef3c7; color: #92400e; }
    .b-red    { background: #fee2e2; color: #991b1b; }
    .b-blue   { background: #dbeafe; color: #1e40af; }
    .b-gray   { background: #f1f5f9; color: #475569; }

    /* ── LOGBOOK ── */
    .logbox {
        margin: 3px 0;
        border-left: 3px solid #3b82f6;
        background: #f8fafc;
        border-radius: 0 6px 6px 0;
        padding: 5px 8px;
    }
    .log-meta { font-size: 6.6pt; color: #64748b; margin-bottom: 2px; font-weight: 500; }
    .log-text { font-size: 7.8pt; color: #1e293b; white-space: pre-wrap; word-break: break-word; }

    /* ── FOOTER ── */
    .footer {
        position: fixed;
        left: 22px;
        right: 22px;
        bottom: 16px;
        border-top: 1px solid #e2e8f0;
        padding-top: 5px;
        font-size: 6.8pt;
        color: #94a3b8;
    }
    .footer .left  { float: left; }
    .footer .right { float: right; }

    .page-break { page-break-before: always; }

    /* ── ISSUANCE PAGE ── */
    .issuance-page {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        min-height: 94vh;
        padding: 44px 44px 30px;
        position: relative;
        text-align: center;
    }
    .issuance-page .iss-eyebrow {
        font-size: 8pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: #3b82f6;
        margin-bottom: 10px;
    }
    .issuance-page h2 {
        font-size: 22pt;
        font-weight: 800;
        color: #0f172a;
        letter-spacing: .02em;
        margin-bottom: 6px;
    }
    .issuance-page .iss-sub {
        font-size: 9.5pt;
        color: #64748b;
        margin-bottom: 38px;
    }
    .issuance-page .iss-body {
        font-size: 11pt;
        color: #334155;
        line-height: 2;
        max-width: 82%;
        margin: 0 auto 44px;
    }
    .issuance-page .iss-name {
        font-size: 17pt;
        font-weight: 800;
        color: #0f172a;
        margin: 10px 0;
    }
    .iss-divider {
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #3b82f6, #60a5fa);
        border-radius: 999px;
        margin: 14px auto;
    }

    /* ── SIGNATURE BLOCK ── */
    .sign-block {
        width: 305px;
        margin-left: auto;
        text-align: center;
        position: relative;
        min-height: 255px;
    }
    .sign-date {
        position: relative;
        z-index: 3;
        margin-bottom: 8px;
        color: #374151;
        font-size: 10.3pt;
    }
    .sign-layer {
        position: absolute;
        left: 0;
        right: 0;
        top: 36px;
        height: 150px;
        z-index: 1;
    }
    .sign-img {
        width: 178px;
        position: absolute;
        top: 0;
        right: 56px;
        z-index: 1;
    }
    .stamp-img {
        width: 115px;
        position: absolute;
        top: 4px;
        right: 154px;
        z-index: 0;
        opacity: .9;
    }
    .sign-text-layer {
        position: relative;
        z-index: 3;
        margin-top: 85px;
    }
    .sign-name {
        font-weight: 800;
        text-decoration: underline;
        font-size: 11pt;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .sign-role {
        color: #374151;
        font-size: 9pt;
        font-weight: 600;
    }
</style>
</head>
<body>

<div class="footer cf">
    <span class="left">Internship Report • {{ $intern->name }} • Mieru Internal Document</span>
    <span class="right">Generated: {{ $generatedAt }}</span>
</div>

{{-- ═══════════════════════════════════════════════
     PAGE 1 — PERFORMANCE SUMMARY
════════════════════════════════════════════════ --}}

<div class="doc-header">
    <div class="logo-cell"><img src="{{ public_path('icons/icon-128x128.png') }}" alt="Mieru Logo"></div>
    <div class="text-cell">
        <div class="brand">Mieru Internship</div>
        <div class="tagline">Internship Performance Document</div>
    </div>
</div>

<div class="report-shell">

    {{-- BANNER --}}
    <div class="report-banner cf">
        <div class="meta">
            Generated: {{ $generatedAt }}<br>
            @if($dateFrom || $dateTo)
                Period: {{ $dateFrom ?? '—' }} to {{ $dateTo ?? '—' }}
            @else
                Period: All time
            @endif
        </div>
        <h1>Internship Performance Report</h1>
        <div class="sub">Structured summary of attendance, schedule activity, and task execution</div>
    </div>

    {{-- PROFILE + GRADE --}}
    <div class="profile-area cf">
        <div style="float:left; width:73%;">
            <table class="profile-grid">
                <tr>
                    <td class="lbl">Full Name</td>
                    <td class="val">{{ $intern->name }}</td>
                    <td class="lbl">Internship Start</td>
                    <td class="val">{{ $summary['internship_start'] }}</td>
                </tr>
                <tr>
                    <td class="lbl">Email</td>
                    <td class="val">{{ $intern->email }}</td>
                    <td class="lbl">Internship End</td>
                    <td class="val">{{ $summary['internship_end'] }}</td>
                </tr>
                <tr>
                    <td class="lbl">Role</td>
                    <td class="val">Intern</td>
                    <td class="lbl">Report Range</td>
                    <td class="val">
                        @if($dateFrom || $dateTo)
                            {{ $dateFrom ?? '—' }} — {{ $dateTo ?? '—' }}
                        @else
                            All time
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div style="float:right; width:25%;">
            <div class="grade-block">
                <div class="glabel">Final Rating</div>
                <div class="grade-big">{{ $summary['final_grade'] ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    {{-- PERFORMANCE OVERVIEW --}}
    <div class="section-heading">
        <div class="section-heading-inner"><span>Performance Overview</span></div>
    </div>
    <div class="section-body">
        <table class="kpi-row">
            <tr>
                <td><div class="kpi-card"><div class="kpi-value tone-blue">{{ $summary['total_schedules'] }}</div><div class="kpi-label">Total Schedules</div></div></td>
                <td><div class="kpi-card"><div class="kpi-value tone-violet">{{ $summary['total_hours'] }}h</div><div class="kpi-label">Total Hours</div></div></td>
                <td><div class="kpi-card"><div class="kpi-value tone-green">{{ $summary['completed'] }}</div><div class="kpi-label">Completed</div></div></td>
                <td><div class="kpi-card"><div class="kpi-value tone-amber">{{ $summary['late'] }}</div><div class="kpi-label">Late</div></div></td>
            </tr>
            <tr>
                <td><div class="kpi-card"><div class="kpi-value tone-red">{{ $summary['absence'] }}</div><div class="kpi-label">Absence</div></div></td>
                <td><div class="kpi-card"><div class="kpi-value tone-cyan">{{ $summary['ongoing'] }}</div><div class="kpi-label">Ongoing</div></div></td>
                <td><div class="kpi-card"><div class="kpi-value tone-slate">{{ $summary['not_yet'] }}</div><div class="kpi-label">Not Yet</div></div></td>
                <td><div class="kpi-card"><div class="kpi-value tone-indigo">{{ $summary['shift_logs'] }}</div><div class="kpi-label">Log Entries</div></div></td>
            </tr>
            <tr>
                <td><div class="kpi-card"><div class="kpi-value tone-green">{{ $summary['approved'] }}</div><div class="kpi-label">Approved</div></div></td>
                <td><div class="kpi-card"><div class="kpi-value tone-amber">{{ $summary['pending'] }}</div><div class="kpi-label">Pending</div></div></td>
                <td><div class="kpi-card"><div class="kpi-value tone-slate">{{ $summary['rejected'] }}</div><div class="kpi-label">Rejected</div></div></td>
                <td><div class="kpi-card"><div class="kpi-value tone-green">{{ $summary['kanban_done'] }}</div><div class="kpi-label">Done + Archive</div></div></td>
            </tr>
        </table>

        <div style="margin-top: 14px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 11px 14px;">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:130px; font-size:8pt; color:#334155; font-weight:700;">Attendance Rate</td>
                    <td style="padding: 0 12px;">
                        <div class="bar"><span style="width:{{ $summary['attendance_rate'] }}%;"></span></div>
                    </td>
                    <td style="width:52px; text-align:right; font-size:12pt; font-weight:800; color:#2563eb;">{{ $summary['attendance_rate'] }}%</td>
                </tr>
            </table>
            <table style="width:100%; border-collapse:collapse; margin-top:8px;">
                <tr>
                    <td style="font-size:7.8pt; color:#64748b;">Total assigned task cards: <strong style="color:#1e293b;">{{ $summary['kanban_assigned'] }}</strong></td>
                    <td style="text-align:right; font-size:7.4pt; color:#94a3b8;">Done/Archived cards counted as completed task output</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ATTENDANCE BREAKDOWN --}}
    @if($summary['total_schedules'] > 0)
    @php
        $total = $summary['total_schedules'];
        $bars = [
            ['label' => 'Completed', 'val' => $summary['completed'], 'color' => '#16a34a'],
            ['label' => 'Late',      'val' => $summary['late'],      'color' => '#d97706'],
            ['label' => 'Absence',   'val' => $summary['absence'],   'color' => '#dc2626'],
            ['label' => 'Not Yet',   'val' => $summary['not_yet'],   'color' => '#64748b'],
            ['label' => 'Ongoing',   'val' => $summary['ongoing'],   'color' => '#0284c7'],
        ];
    @endphp
    <div class="section-heading">
        <div class="section-heading-inner"><span>Attendance Breakdown</span></div>
    </div>
    <div class="section-body">
        <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px;">
            <table class="breakdown-table">
                @foreach($bars as $bar)
                @php $pct = $total > 0 ? round(($bar['val'] / $total) * 100, 1) : 0; @endphp
                <tr>
                    <td style="width:84px; color:#4b5563; font-weight:500;">{{ $bar['label'] }}</td>
                    <td style="width:32px; text-align:right; font-weight:700; color:#111827;">{{ $bar['val'] }}</td>
                    <td style="padding: 0 10px;"><div class="mini-bar"><span style="width:{{ $pct }}%; background: {{ $bar['color'] }}"></span></div></td>
                    <td style="width:42px; text-align:right; color:#64748b; font-size:7.8pt;">{{ $pct }}%</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    @endif

    {{-- WEEKLY BREAKDOWN --}}
    @if(count($weeklyBreakdown) > 0)
    <div class="section-heading">
        <div class="section-heading-inner"><span>Weekly Breakdown</span></div>
    </div>
    <div class="section-body">
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Week</th>
                        <th>Date Range</th>
                        <th style="text-align:center">Sessions</th>
                        <th style="text-align:center">Hours</th>
                        <th style="text-align:center">Done</th>
                        <th style="text-align:center">Late</th>
                        <th style="text-align:center">Absence</th>
                        <th style="text-align:center">Logs</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weeklyBreakdown as $week)
                    <tr>
                        <td><strong>{{ $week['week'] }}</strong></td>
                        <td style="color:#64748b;">{{ $week['date_range'] }}</td>
                        <td style="text-align:center;">{{ $week['count'] }}</td>
                        <td style="text-align:center; font-weight:700; color:#2563eb;">{{ $week['hours'] }}h</td>
                        <td style="text-align:center; color:#16a34a; font-weight:700;">{{ $week['done'] }}</td>
                        <td style="text-align:center; color:#d97706; font-weight:700;">{{ $week['late'] }}</td>
                        <td style="text-align:center; color:#dc2626; font-weight:700;">{{ $week['absence'] }}</td>
                        <td style="text-align:center;">{{ $week['logs'] }}</td>
                    </tr>
                    @endforeach
                    <tr style="background:#f1f5f9; font-weight:800;">
                        <td colspan="2" style="color:#0f172a;">Total</td>
                        <td style="text-align:center;">{{ $summary['total_schedules'] }}</td>
                        <td style="text-align:center; color:#2563eb;">{{ $summary['total_hours'] }}h</td>
                        <td style="text-align:center; color:#16a34a;">{{ $summary['completed'] }}</td>
                        <td style="text-align:center; color:#d97706;">{{ $summary['late'] }}</td>
                        <td style="text-align:center; color:#dc2626;">{{ $summary['absence'] }}</td>
                        <td style="text-align:center;">{{ $summary['shift_logs'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- TASK BOARD --}}
    @if($kanbanByCol->flatten()->count() > 0)
    <div class="section-heading">
        <div class="section-heading-inner"><span>Task Board Activity</span></div>
    </div>
    <div class="section-body">
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th style="width:28px">#</th>
                        <th>Task Title</th>
                        <th style="width:94px">Column</th>
                        <th style="width:62px">Priority</th>
                        <th style="width:84px">Due Date</th>
                        <th style="width:92px">Assignee</th>
                    </tr>
                </thead>
                <tbody>
                    @php $index = 1; @endphp
                    @foreach($kanbanColumns as $key => $label)
                        @if(isset($kanbanByCol[$key]) && $kanbanByCol[$key]->count() > 0)
                            @foreach($kanbanByCol[$key] as $card)
                            <tr>
                                <td style="color:#94a3b8;">{{ $index++ }}</td>
                                <td>
                                    <strong>{{ $card->title }}</strong>
                                    @if($card->description)
                                        <br><span style="color:#64748b; font-size:6.8pt;">{{ \Illuminate\Support\Str::limit($card->description, 90) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php $colBadge = in_array($card->column_name, ['done', 'archive']) ? 'b-green' : 'b-gray'; @endphp
                                    <span class="badge {{ $colBadge }}">{{ $label }}</span>
                                </td>
                                <td>
                                    @if($card->priority)
                                        @php $priorityMap = ['low' => 'b-blue', 'medium' => 'b-amber', 'high' => 'b-red']; @endphp
                                        <span class="badge {{ $priorityMap[$card->priority] ?? 'b-gray' }}">{{ ucfirst($card->priority) }}</span>
                                    @else
                                        <span style="color:#94a3b8;">—</span>
                                    @endif
                                </td>
                                <td>{{ $card->due_date ? \Carbon\Carbon::parse($card->due_date)->format('d M Y') : '—' }}</td>
                                <td>{{ $card->assignedUser?->name ?? '—' }}</td>
                            </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>{{-- /report-shell --}}

{{-- ═══════════════════════════════════════════════
     PAGE 2 — SHIFT & LOGBOOK RECORDS
════════════════════════════════════════════════ --}}
@if(count($scheduleRows) > 0)
<div class="page-break"></div>

<div class="doc-header">
    <div class="logo-cell"><img src="{{ public_path('icons/icon-128x128.png') }}" alt="Mieru Logo"></div>
    <div class="text-cell">
        <div class="brand">Mieru Internship</div>
        <div class="tagline">Internship Performance Document</div>
    </div>
</div>

<div class="report-shell">
    <div class="report-banner cf">
        <div class="meta">Intern: {{ $intern->name }}</div>
        <h1>Detailed Shift &amp; Logbook Records</h1>
        <div class="sub">Complete ledger of recorded schedule sessions</div>
    </div>

    <div class="section-body" style="padding-top: 14px;">
        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th style="width:104px">Date</th>
                        <th style="width:88px">Shift</th>
                        <th style="width:32px">Hrs</th>
                        <th>Caption</th>
                        <th style="width:58px">Status</th>
                        <th style="width:58px">Approval</th>
                        <th style="width:38px">In</th>
                        <th style="width:38px">Out</th>
                        <th style="width:40px">Late</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scheduleRows as $row)
                    @php
                        $statusBadge = match($row['status']) {
                            'done'    => 'b-green',
                            'late'    => 'b-amber',
                            'absence' => 'b-red',
                            'ongoing' => 'b-blue',
                            default   => 'b-gray',
                        };
                        $approvalBadge = match($row['approval_status']) {
                            'approved' => 'b-green',
                            'pending'  => 'b-amber',
                            default    => 'b-gray',
                        };
                    @endphp
                    <tr>
                        <td style="font-size:7.4pt;">{{ $row['date'] }}</td>
                        <td style="font-size:7.4pt; font-weight:700;">{{ $row['shift'] }}</td>
                        <td style="text-align:center;">{{ $row['hours'] }}</td>
                        <td style="font-size:7.5pt; color:#4b5563;">{{ $row['caption'] }}</td>
                        <td><span class="badge {{ $statusBadge }}">{{ str_replace('_', ' ', $row['status']) }}</span></td>
                        <td><span class="badge {{ $approvalBadge }}">{{ $row['approval_status'] }}</span></td>
                        <td style="text-align:center;">{{ $row['entry_time'] }}</td>
                        <td style="text-align:center;">{{ $row['exit_time'] }}</td>
                        <td style="text-align:center; color:{{ ($row['late_minutes'] ?? 0) > 0 ? '#dc2626' : '#9ca3af' }}; font-weight:700;">
                            {{ $row['late_minutes'] !== null ? $row['late_minutes'] . 'm' : '—' }}
                        </td>
                    </tr>

                    @if(count($row['logbooks']) > 0)
                    <tr>
                        <td colspan="9" style="background:#f8fafc; padding: 6px 9px;">
                            @foreach($row['logbooks'] as $log)
                            <div class="logbox">
                                <div class="log-meta">{{ $log['created_at'] }}</div>
                                <div class="log-text">{{ $log['content'] }}</div>
                            </div>
                            @endforeach
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════════════
     PAGE 3 — PROOF OF ISSUANCE
════════════════════════════════════════════════ --}}
<div class="page-break"></div>

<div class="issuance-page">
    <div class="iss-eyebrow">Official Document</div>
    <h2>Proof of Issuance</h2>
    <div class="iss-divider"></div>
    <div class="iss-sub">Official validation that this report is issued by Mieru</div>

    <div class="iss-body">
        This document certifies that the internship report for
        <div class="iss-name">{{ $intern->name }}</div>
        has been officially issued by <strong>Mieru</strong> and can be used as an internal formal record.
    </div>

    <div class="sign-block">
        <div class="sign-date">Makassar, 6 April 2026</div>
        <div class="sign-layer">
            <img src="{{ public_path('ttdaqil.png') }}" class="sign-img" alt="Signature">
            <img src="{{ public_path('stampmieru.png') }}" class="stamp-img" alt="Stamp">
        </div>
        <div class="sign-text-layer">
            <div class="sign-name">Muhammad Aqil Amin</div>
            <div class="sign-role">Director of Mieru</div>
        </div>
    </div>
</div>

</body>
</html>
