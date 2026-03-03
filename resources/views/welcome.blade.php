<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'Mieru Log') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --purple:      #7367F0;
            --purple-dark: #5a52d5;
            --dark:        #080812;
            --dark-2:      #0f0f1a;
            --dark-3:      #161626;
            --border:      rgba(255,255,255,0.07);
            --muted:       rgba(255,255,255,0.45);
            --body:        rgba(255,255,255,0.75);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--dark);
            color: #fff;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Navbar ─────────────────────────────────────── */
        .site-nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            padding: 22px 0;
            transition: all 0.3s ease;
        }
        .site-nav.scrolled {
            background: rgba(8, 8, 18, 0.88);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
        }
        .nav-inner {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: -0.01em;
        }
        .brand-mark {
            width: 32px; height: 32px;
            background: var(--purple);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .nav-buttons { display: flex; gap: 8px; align-items: center; }

        .btn-ghost {
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--body);
            text-decoration: none;
            border: 1px solid var(--border);
            background: transparent;
            transition: all 0.2s;
        }
        .btn-ghost:hover { color: #fff; background: rgba(255,255,255,0.06); border-color: rgba(255,255,255,0.14); }

        .btn-fill {
            padding: 7px 18px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
            background: var(--purple);
            border: 1px solid var(--purple);
            transition: all 0.2s;
        }
        .btn-fill:hover {
            background: var(--purple-dark);
            border-color: var(--purple-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 18px rgba(115,103,240,0.35);
        }

        /* ── Hero ───────────────────────────────────────── */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 140px 0 100px;
            position: relative;
            overflow: hidden;
        }
        /* Dot grid */
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background-image: radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: radial-gradient(ellipse 70% 70% at 50% 30%, black 30%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 70% 70% at 50% 30%, black 30%, transparent 100%);
        }
        /* Purple bloom */
        .hero::after {
            content: '';
            position: absolute;
            top: -160px; left: 50%;
            transform: translateX(-50%);
            width: 900px; height: 700px;
            background: radial-gradient(ellipse, rgba(115,103,240,0.16) 0%, transparent 65%);
            pointer-events: none;
        }
        .hero-inner {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 28px;
            position: relative;
            z-index: 1;
            text-align: center;
        }
        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 14px 5px 9px;
            border-radius: 100px;
            border: 1px solid rgba(115,103,240,0.3);
            background: rgba(115,103,240,0.08);
            font-size: 0.78rem;
            font-weight: 500;
            color: #b3acf7;
            margin-bottom: 32px;
        }
        .pill-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--purple);
            box-shadow: 0 0 7px var(--purple);
        }
        .hero h1 {
            font-size: clamp(2.8rem, 6.5vw, 5rem);
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -0.035em;
            margin-bottom: 24px;
        }
        .hero h1 em {
            font-style: normal;
            background: linear-gradient(135deg, #c4bfff 0%, #7367F0 45%, #5a52d5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-sub {
            font-size: 1.05rem;
            color: var(--muted);
            max-width: 480px;
            margin: 0 auto 44px;
            line-height: 1.75;
        }
        .hero-cta {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-cta-primary {
            padding: 13px 28px;
            border-radius: 10px;
            font-size: 0.925rem;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
            background: var(--purple);
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s;
        }
        .btn-cta-primary:hover {
            background: var(--purple-dark);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(115,103,240,0.4);
        }
        .btn-cta-outline {
            padding: 13px 28px;
            border-radius: 10px;
            font-size: 0.925rem;
            font-weight: 500;
            color: var(--body);
            text-decoration: none;
            background: transparent;
            border: 1px solid var(--border);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s;
        }
        .btn-cta-outline:hover { color: #fff; background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); }

        .hero-scroll {
            margin-top: 72px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 0.7rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .scroll-bar {
            width: 1px; height: 44px;
            background: linear-gradient(to bottom, rgba(115,103,240,0.7), transparent);
        }

        /* ── Features ──────────────────────────────────── */
        .section {
            padding: 100px 0;
        }
        .section-inner {
            max-width: 1120px;
            margin: 0 auto;
            padding: 0 28px;
        }
        .section.alt { background: var(--dark-2); }
        .section.alt::before {
            content: '';
            display: block;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, var(--purple) 50%, transparent 100%);
            margin-bottom: -1px;
        }

        .sh { /* section header */
            margin-bottom: 60px;
        }
        .sh-eyebrow {
            font-size: 0.73rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--purple);
            margin-bottom: 12px;
        }
        .sh h2 {
            font-size: clamp(1.7rem, 3vw, 2.3rem);
            font-weight: 700;
            letter-spacing: -0.025em;
            line-height: 1.2;
            margin-bottom: 12px;
        }
        .sh p {
            font-size: 0.95rem;
            color: var(--muted);
            max-width: 420px;
            line-height: 1.7;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            background: var(--border); /* shows as dividers */
            gap: 1px;
        }
        .feat {
            background: var(--dark-2);
            padding: 34px 30px;
            transition: background 0.2s;
        }
        .feat:hover { background: var(--dark-3); }
        .feat-icon {
            width: 40px; height: 40px;
            border-radius: 9px;
            background: rgba(115,103,240,0.1);
            border: 1px solid rgba(115,103,240,0.18);
            display: flex; align-items: center; justify-content: center;
            color: #a99ff5;
            margin-bottom: 18px;
            flex-shrink: 0;
        }
        .feat h3 {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }
        .feat p {
            font-size: 0.82rem;
            color: var(--muted);
            line-height: 1.65;
        }

        /* ── Stats ─────────────────────────────────────── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            max-width: 740px;
            margin: 0 auto;
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }
        .stat {
            padding: 36px 28px;
            text-align: center;
            border-right: 1px solid var(--border);
        }
        .stat:last-child { border-right: none; }
        .stat-num {
            font-size: 2.4rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #c4bfff, #7367F0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-desc {
            font-size: 0.8rem;
            color: var(--muted);
            line-height: 1.5;
        }

        /* ── CTA ────────────────────────────────────────── */
        .cta-section {
            padding: 110px 28px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            bottom: -220px; left: 50%;
            transform: translateX(-50%);
            width: 700px; height: 500px;
            background: radial-gradient(ellipse, rgba(115,103,240,0.13) 0%, transparent 70%);
        }
        .cta-inner { position: relative; z-index: 1; }
        .cta-inner h2 {
            font-size: clamp(1.8rem, 3.5vw, 2.5rem);
            font-weight: 700;
            letter-spacing: -0.025em;
            margin-bottom: 12px;
        }
        .cta-inner p {
            font-size: 0.92rem;
            color: var(--muted);
            margin-bottom: 36px;
        }

        /* ── Footer ─────────────────────────────────────── */
        .site-footer {
            padding: 28px 28px;
            border-top: 1px solid var(--border);
            text-align: center;
        }
        .site-footer p {
            font-size: 0.8rem;
            color: var(--muted);
        }

        /* ── Responsive ─────────────────────────────────── */
        @media (max-width: 860px) {
            .features-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 600px) {
            .features-grid { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: 1fr; }
            .stat { border-right: none; border-bottom: 1px solid var(--border); }
            .stat:last-child { border-bottom: none; }
            .hero-cta { flex-direction: column; align-items: center; }
            .btn-cta-primary, .btn-cta-outline { width: 100%; justify-content: center; max-width: 270px; }
        }
    </style>
</head>

<body>

    <!-- ── Navbar ───────────────────────────────── -->
    <nav class="site-nav" id="siteNav">
        <div class="nav-inner">
            <a href="/" class="brand">
                <div class="brand-mark">
                    <svg width="17" height="13" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="white"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="white" opacity="0.75"/>
                    </svg>
                </div>
                {{ config('app.name', 'Mieru Log') }}
            </a>
            @if (Route::has('login'))
                <div class="nav-buttons">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-fill">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-fill">Sign in</a>
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <!-- ── Hero ─────────────────────────────────── -->
    <section class="hero">
        <div class="hero-inner">
            <div class="hero-pill">
                <span class="pill-dot"></span>
                Internship Monitoring System
            </div>

            <h1>
                Welcome Aboard<br>
                <em>Log Mieru.</em>
            </h1>

            <p class="hero-sub">
                Schedule shifts, stamp attendance, write logbooks,
                and generate reports — all in one clean workspace.
            </p>

            <div class="hero-cta">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-cta-primary">
                        Open Dashboard
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-cta-primary">
                        Get started
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>

                @endauth
            </div>

            <div class="hero-scroll">
                <div class="scroll-bar"></div>
                scroll
            </div>
        </div>
    </section>

    <!-- ── Features ─────────────────────────────── -->
    <section class="section alt">
        <div class="section-inner">
            <div class="sh">
                <p class="sh-eyebrow">Features</p>
                <h2>Built for modern internship teams</h2>
                <p>Everything you need to run your internship program — from day one to the final report.</p>
            </div>

            <div class="features-grid">
                <div class="feat">
                    <div class="feat-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    </div>
                    <h3>Schedule Management</h3>
                    <p>Create and manage shift schedules with an interactive calendar. Status updates — done, late, absence — happen automatically.</p>
                </div>

                <div class="feat">
                    <div class="feat-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                    </div>
                    <h3>Presence Stamps</h3>
                    <p>Clock in and out per shift. The system auto-detects on-time, late, and absent status without any manual intervention.</p>
                </div>

                <div class="feat">
                    <div class="feat-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <h3>Shift Logbooks</h3>
                    <p>Document activities for every shift. Admins can review all intern logs from a central panel with search and filtering.</p>
                </div>

                <div class="feat">
                    <div class="feat-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                    </div>
                    <h3>Internship Reports</h3>
                    <p>Visual analytics: KPI tiles, donut charts, weekly hours bar chart. Export a comprehensive PDF report in a single click.</p>
                </div>

                <div class="feat">
                    <div class="feat-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="4" height="18" rx="1"/><rect x="10" y="3" width="4" height="14" rx="1"/><rect x="17" y="3" width="4" height="10" rx="1"/></svg>
                    </div>
                    <h3>Kanban Board</h3>
                    <p>Drag-and-drop task management across five stages. Assign tasks, set priorities, due dates, and custom card colors.</p>
                </div>

                <div class="feat">
                    <div class="feat-icon">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    </div>
                    <h3>Approval Workflows</h3>
                    <p>Review, approve, or reject schedules individually or in bulk. Full audit trail with pending, approved, and rejected states.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ── Stats ─────────────────────────────────── -->
    <section class="section">
        <div class="section-inner">
            <div class="stats-row">
                <div class="stat">
                    <div class="stat-num">2</div>
                    <div class="stat-desc">User roles —<br>Admin &amp; Intern</div>
                </div>
                <div class="stat">
                    <div class="stat-num">5</div>
                    <div class="stat-desc">Kanban stages —<br>Backlog to Done</div>
                </div>
                <div class="stat">
                    <div class="stat-num">∞</div>
                    <div class="stat-desc">Shifts, logbooks &amp;<br>schedule slots</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CTA ───────────────────────────────────── -->
    <section class="section alt cta-section">
        <div class="cta-inner">
            <h2>Ready to get started?</h2>
            <p>Sign in to your account or create a new one to begin.</p>
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-cta-primary" style="display:inline-flex;">
                    Open Dashboard
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-cta-primary" style="display:inline-flex;">
                    Sign in now
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            @endauth
        </div>
    </section>

    <!-- ── Footer ────────────────────────────────── -->
    <footer class="site-footer">
        <p>&copy; <script>document.write(new Date().getFullYear())</script> {{ config('app.name', 'Mieru Log') }}. All rights reserved.</p>
    </footer>

    <script>
        const nav = document.getElementById('siteNav');
        window.addEventListener('scroll', () => {
            nav.classList.toggle('scrolled', window.scrollY > 20);
        }, { passive: true });
    </script>

</body>
</html>
