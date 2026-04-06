<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Internship Certificate – {{ $intern->name }}</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700;800&display=swap');

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body, * { font-family: 'Lexend', 'DejaVu Sans', sans-serif !important; }
    body {
        color: #111827;
        background: #ffffff;
        font-size: 11pt;
    }

    .doc-header {
        display: table;
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        background: #ffffff;
        padding: 8px 12px;
        margin-bottom: 14px;
    }

    .doc-header .logo-cell,
    .doc-header .text-cell {
        display: table-cell;
        vertical-align: middle;
    }

    .doc-header .logo-cell { width: 36px; }

    .doc-header img {
        width: 24px;
        height: 24px;
        display: block;
    }

    .doc-header .brand {
        font-size: 11pt;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .doc-header .tagline {
        font-size: 7.4pt;
        color: #64748b;
        letter-spacing: .05em;
        text-transform: uppercase;
        margin-top: 1px;
    }

    .page {
        min-height: 100vh;
        padding: 52px;
        background: #ffffff;
    }

    .certificate {
        border: 2px solid #d1d5db;
        border-radius: 16px;
        padding: 44px 42px 36px;
        min-height: 92vh;
        position: relative;
        background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
    }

    .top-line {
        text-align: center;
        color: #6b7280;
        font-size: 9.3pt;
        letter-spacing: .1em;
        text-transform: uppercase;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .title {
        text-align: center;
        font-size: 32pt;
        color: #0f172a;
        font-weight: 800;
        letter-spacing: .03em;
        margin-bottom: 6px;
    }

    .subtitle {
        text-align: center;
        color: #4b5563;
        font-size: 11pt;
        margin-bottom: 34px;
    }

    .statement {
        text-align: center;
        color: #374151;
        font-size: 12.5pt;
        line-height: 1.9;
        margin-bottom: 14px;
    }

    .intern-name {
        text-align: center;
        font-size: 30pt;
        font-weight: 800;
        color: #1d4ed8;
        margin: 12px 0 16px;
    }

    .period-card {
        width: 82%;
        margin: 0 auto;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        background: #ffffff;
        padding: 16px;
        text-align: center;
    }

    .period-label {
        color: #6b7280;
        font-size: 8.2pt;
        text-transform: uppercase;
        letter-spacing: .09em;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .period-value {
        font-size: 13.5pt;
        color: #0f172a;
        font-weight: 800;
    }

    .sign-wrap {
        margin-top: 68px;
        width: 305px;
        margin-left: auto;
        text-align: center;
        position: relative;
        min-height: 255px;
    }

    .place-date {
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

    .signature {
        width: 178px;
        position: absolute;
        right: 56px;
        top: -25;
        z-index: 1;
    }

    .stamp {
        width: 115px;
        position: absolute;
        top: -35px;
        right: 154px;
        z-index: 0;
        opacity: .9;
    }

    .signatory {
        position: relative;
        z-index: 3;
        margin-top: 65px;
    }

    .officer {
        position: relative;
        font-size: 11pt;
        font-weight: 800;
        color: #0f172a;
        text-decoration: underline;
        margin-bottom: 5px;
    }

    .role {
        position: relative;
        font-size: 9.3pt;
        color: #374151;
        font-weight: 700;
    }

    .note {
        position: absolute;
        left: 42px;
        right: 42px;
        bottom: 18px;
        text-align: center;
        font-size: 8pt;
        color: #9ca3af;
    }
</style>
</head>
<body>
<div class="page">
    <div class="doc-header">
        <div class="logo-cell"><img src="{{ public_path('icons/icon-128x128.png') }}" alt="Mieru Logo"></div>
        <div class="text-cell">
            <div class="brand">Mieru Internship</div>
            <div class="tagline">Official Internship Certificate</div>
        </div>
    </div>

    <div class="certificate">
        <div class="top-line">Mieru • Tamangapa Raya 43</div>
        <div class="title">INTERNSHIP CERTIFICATE</div>
        <div class="subtitle">Official document of internship completion</div>

        <div class="statement">This certificate is presented to</div>
        <div class="intern-name">{{ $intern->name }}</div>

        <div class="statement" style="margin-bottom: 18px;">
            for completing the internship program at <strong>Mieru</strong>
            with satisfactory participation and contribution.
        </div>

        <div class="period-card">
            <div class="period-label">Internship Period</div>
            <div class="period-value">{{ $period['from'] }} — {{ $period['to'] }}</div>
        </div>

        <div class="sign-wrap">
            <div class="place-date">{{ $issuedPlace }}, {{ $issuedDate }}</div>

            <div class="sign-layer">
                <img src="{{ public_path('ttdaqil.png') }}" class="signature" alt="Signature Muhammad Aqil Amin">
                <img src="{{ public_path('stampmieru.png') }}" class="stamp" alt="Mieru Stamp">
            </div>

            <div class="signatory">
                <div class="officer">Muhammad Aqil Amin</div>
                <div class="role">Director of Mieru</div>
            </div>
        </div>

        <div class="note">This certificate is generated by Mieru Internship Reporting System.</div>
    </div>
</div>
</body>
</html>
