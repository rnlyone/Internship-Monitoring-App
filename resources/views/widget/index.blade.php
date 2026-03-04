<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<meta name="theme-color" content="#7367f0"/>
<meta name="apple-mobile-web-app-capable" content="yes"/>
<title>Log Mieru Widget</title>
<link rel="icon" href="/icons/icon-192x192.png"/>
<style>
:root{--primary:#7367f0;--success:#28c76f;--danger:#ea5455;--warning:#ff9f43;--info:#00bad1;--bg:#f5f5f9;--card:#fff;--muted:#6c757d;--text:#2c2c2c;--border:#e7e7e8;--radius:12px}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Public Sans',system-ui,-apple-system,sans-serif;background:var(--bg);color:var(--text);-webkit-user-select:none;user-select:none;overflow-x:hidden}
.widget-wrap{max-width:420px;margin:0 auto;padding:12px 12px 80px}
.header{display:flex;align-items:center;justify-content:space-between;padding:12px 0;margin-bottom:8px}
.header-left h1{font-size:1.1rem;font-weight:700}
.header-left p{font-size:.72rem;color:var(--muted)}
.header-right{display:flex;align-items:center;gap:8px}
.header-right .clock{font-size:.85rem;font-weight:600;color:var(--primary);background:rgba(115,103,240,.1);padding:4px 10px;border-radius:20px}
.home-btn{width:32px;height:32px;border-radius:50%;border:none;background:rgba(115,103,240,.1);color:var(--primary);font-size:1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;text-decoration:none}

.empty{text-align:center;padding:60px 16px;color:var(--muted)}
.empty svg{width:56px;height:56px;fill:var(--border);margin-bottom:12px}
.empty h2{font-size:1rem;margin-bottom:4px;color:var(--text)}
.empty p{font-size:.8rem}

.shift-card{background:var(--card);border-radius:var(--radius);padding:14px;margin-bottom:10px;border:1px solid var(--border);transition:box-shadow .15s}
.shift-card.active{border-color:var(--primary);box-shadow:0 4px 16px rgba(115,103,240,.18)}
.shift-card.done{opacity:.65}
.shift-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.day-pill{font-size:.65rem;font-weight:600;padding:2px 8px;border-radius:20px;text-transform:uppercase;letter-spacing:.5px}
.day-today{background:rgba(234,84,85,.12);color:var(--danger)}
.day-tomorrow{background:rgba(255,159,67,.12);color:var(--warning)}
.day-later{background:rgba(108,117,125,.1);color:var(--muted)}
.status-pill{font-size:.62rem;font-weight:600;padding:2px 8px;border-radius:20px}
.st-upcoming{background:rgba(0,186,209,.1);color:var(--info)}
.st-ready{background:rgba(115,103,240,.12);color:var(--primary)}
.st-inprogress{background:rgba(40,199,111,.12);color:var(--success)}
.st-done{background:rgba(108,117,125,.1);color:var(--muted)}
.st-late{background:rgba(255,159,67,.12);color:var(--warning)}

.shift-time{font-weight:700;font-size:.95rem}
.shift-time small{font-weight:400;color:var(--muted);font-size:.72rem;margin-left:4px}
.shift-caption{font-size:.75rem;color:var(--muted);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.shift-meta{display:flex;gap:12px;margin-top:8px;flex-wrap:wrap}
.meta-tag{font-size:.68rem;display:flex;align-items:center;gap:3px;padding:2px 8px;border-radius:16px;background:var(--bg)}
.meta-tag.entry{color:var(--success)}
.meta-tag.exit{color:var(--info)}
.meta-tag.logbook{color:#6610f2}

.actions{display:flex;gap:6px;margin-top:10px}
.btn{border:none;border-radius:8px;padding:8px 14px;font-size:.78rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:5px;justify-content:center;flex:1;transition:opacity .15s}
.btn:active{opacity:.7}
.btn:disabled{opacity:.4;cursor:not-allowed}
.btn-entry{background:var(--primary);color:#fff}
.btn-exit{background:var(--success);color:#fff}
.btn-logbook{background:rgba(102,16,242,.12);color:#6610f2;flex:0 0 auto;padding:8px 12px}

/* Logbook panel */
.logbook-panel{display:none;margin-top:8px;background:var(--bg);border-radius:8px;padding:10px}
.logbook-panel.open{display:block}
.logbook-panel textarea{width:100%;border:1px solid var(--border);border-radius:8px;padding:8px;font-size:.8rem;font-family:inherit;resize:vertical;min-height:60px;outline:none}
.logbook-panel textarea:focus{border-color:var(--primary)}
.logbook-panel .lb-actions{display:flex;gap:6px;margin-top:6px;justify-content:flex-end}
.btn-sm{padding:5px 12px;font-size:.72rem;border-radius:6px;border:none;cursor:pointer;font-weight:600}
.btn-save{background:var(--primary);color:#fff}
.btn-cancel{background:var(--border);color:var(--text)}
.logbook-entries{margin-top:6px}
.lb-entry{background:var(--card);border-radius:6px;padding:6px 8px;margin-bottom:4px;font-size:.72rem;border:1px solid var(--border)}
.lb-entry small{color:var(--muted)}

.toast{position:fixed;bottom:16px;left:50%;transform:translateX(-50%) translateY(80px);background:var(--text);color:#fff;padding:10px 20px;border-radius:10px;font-size:.82rem;z-index:999;opacity:0;transition:all .3s ease}
.toast.show{transform:translateX(-50%) translateY(0);opacity:1}
.toast.success{background:var(--success)}
.toast.error{background:var(--danger)}

.countdown{font-size:.68rem;font-weight:700;margin-left:4px}
.cd-urgent{color:var(--danger)}
.cd-soon{color:var(--warning)}
.cd-normal{color:var(--info)}

@media(prefers-color-scheme:dark){
:root{--bg:#1a1a2e;--card:#25293c;--text:#cfd3ec;--border:#3b3f5c;--muted:#8a8ea8}
}
</style>
</head>
<body>
<div class="widget-wrap">
    <div class="header">
        <div class="header-left">
            <h1 id="userName">Log Mieru</h1>
            <p id="dateLabel">Loading…</p>
        </div>
        <div class="header-right">
            <span class="clock" id="clock">--:--</span>
            <a href="/dashboard" class="home-btn" title="Dashboard">⬅</a>
        </div>
    </div>
    <div id="shiftsList"></div>
</div>

<div class="toast" id="toast"></div>

<script>
(function(){
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const container = document.getElementById('shiftsList');
    let openLogbook = null; // currently open logbook shift ID

    // ── Clock ────────────────────────────────────────────
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent =
            now.toLocaleTimeString('en-GB', {hour:'2-digit',minute:'2-digit',timeZone:'Asia/Singapore'});
    }
    updateClock();
    setInterval(updateClock, 10000);

    // ── Toast ────────────────────────────────────────────
    function toast(msg, type='success') {
        const el = document.getElementById('toast');
        el.textContent = msg;
        el.className = 'toast ' + type + ' show';
        setTimeout(() => el.className = 'toast', 2500);
    }

    // ── Countdown text ───────────────────────────────────
    function countdown(minutesBefore) {
        if (minutesBefore <= 0) return '';
        const h = Math.floor(minutesBefore/60);
        const m = minutesBefore % 60;
        const txt = h > 0 ? h+'h '+m+'m' : m+'m';
        const cls = minutesBefore <= 15 ? 'cd-urgent' : (minutesBefore <= 60 ? 'cd-soon' : 'cd-normal');
        return `<span class="countdown ${cls}">in ${txt}</span>`;
    }

    // ── Render ───────────────────────────────────────────
    function render(data) {
        document.getElementById('userName').textContent = data.user;
        document.getElementById('dateLabel').textContent = data.date;

        if (!data.shifts || data.shifts.length === 0) {
            container.innerHTML = `
                <div class="empty">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                    <h2>All clear!</h2>
                    <p>No upcoming shifts to show.</p>
                </div>`;
            return;
        }

        container.innerHTML = data.shifts.map(s => {
            const dayClass = s.day_label === 'Today' ? 'day-today' : (s.day_label === 'Tomorrow' ? 'day-tomorrow' : 'day-later');

            let statusClass = 'st-upcoming';
            if (s.status_label === 'Ready to Stamp') statusClass = 'st-ready';
            else if (s.status_label === 'In Progress' || s.status_label === 'Stamped In') statusClass = 'st-inprogress';
            else if (s.status_label.startsWith('Done')) statusClass = s.status === 'late' ? 'st-late' : 'st-done';

            const cardClass = s.is_ongoing ? 'active' : (s.is_done ? 'done' : '');

            // Meta tags
            let meta = '';
            if (s.has_entry)  meta += `<span class="meta-tag entry">✓ Entry ${s.entry_time}</span>`;
            if (s.has_exit)   meta += `<span class="meta-tag exit">✓ Exit ${s.exit_time}</span>`;
            meta += `<span class="meta-tag logbook">📝 ${s.logbook_count} log${s.logbook_count !== 1 ? 's' : ''}</span>`;
            if (!s.has_entry && !s.is_done && s.minutes_until > 0) meta += countdown(s.minutes_until);

            // Action buttons
            let actions = '';
            if (s.can_entry) {
                actions += `<button class="btn btn-entry" onclick="stampEntry('${s.id}')">☑ Stamp Entry</button>`;
            }
            if (s.can_exit) {
                actions += `<button class="btn btn-exit" onclick="stampExit('${s.id}')">☑ Stamp Exit</button>`;
            }
            if (s.has_entry && !s.has_exit || s.is_done) {
                actions += `<button class="btn btn-logbook" onclick="toggleLogbook('${s.id}')">📝</button>`;
            }

            // Logbook panel
            const lbOpen = openLogbook === s.id ? 'open' : '';
            const logbookPanel = `
                <div class="logbook-panel ${lbOpen}" id="lb-${s.id}">
                    <div class="logbook-entries" id="lb-entries-${s.id}"></div>
                    <textarea id="lb-text-${s.id}" placeholder="What did you work on?"></textarea>
                    <div class="lb-actions">
                        <button class="btn-sm btn-cancel" onclick="toggleLogbook(null)">Cancel</button>
                        <button class="btn-sm btn-save" onclick="saveLogbook('${s.id}')">Save</button>
                    </div>
                </div>`;

            return `
            <div class="shift-card ${cardClass}">
                <div class="shift-top">
                    <span class="day-pill ${dayClass}">${s.day_label}</span>
                    <span class="status-pill ${statusClass}">${s.status_label}</span>
                </div>
                <div class="shift-time">${s.time_start} – ${s.time_end} <small>${s.date}</small></div>
                <div class="shift-caption">${s.caption}</div>
                <div class="shift-meta">${meta}</div>
                ${actions ? '<div class="actions">' + actions + '</div>' : ''}
                ${logbookPanel}
            </div>`;
        }).join('');

        // Load logbook entries if panel is open
        if (openLogbook) loadLogbookEntries(openLogbook);
    }

    // ── API calls ────────────────────────────────────────
    function loadData() {
        fetch('/widget/data', { headers: { Accept: 'application/json' }, credentials: 'same-origin' })
            .then(r => { if (!r.ok) throw r; return r.json(); })
            .then(render)
            .catch(() => {
                container.innerHTML = `
                    <div class="empty">
                        <h2>Connection error</h2>
                        <p>Couldn't load shift data. Pull down to retry.</p>
                    </div>`;
            });
    }

    window.stampEntry = function(id) {
        fetch('/presence/' + id + '/entry', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':csrf, Accept:'application/json' },
            credentials: 'same-origin',
        })
        .then(r => r.json().then(d => ({ok:r.ok,data:d})))
        .then(({ok,data}) => {
            toast(data.message, ok ? 'success' : 'error');
            if (ok) loadData();
        });
    };

    window.stampExit = function(id) {
        fetch('/presence/' + id + '/exit', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':csrf, Accept:'application/json' },
            credentials: 'same-origin',
        })
        .then(r => r.json().then(d => ({ok:r.ok,data:d})))
        .then(({ok,data}) => {
            toast(data.message, ok ? 'success' : 'error');
            if (ok) loadData();
        });
    };

    window.toggleLogbook = function(id) {
        openLogbook = openLogbook === id ? null : id;
        // Re-render to toggle panel
        document.querySelectorAll('.logbook-panel').forEach(p => p.classList.remove('open'));
        if (id) {
            const panel = document.getElementById('lb-' + id);
            if (panel) {
                panel.classList.add('open');
                loadLogbookEntries(id);
            }
        }
    };

    function loadLogbookEntries(id) {
        fetch('/logbooks/' + id, { headers: { Accept: 'application/json' }, credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                const el = document.getElementById('lb-entries-' + id);
                if (!el) return;
                if (!data.logbooks || data.logbooks.length === 0) {
                    el.innerHTML = '<div style="font-size:.7rem;color:var(--muted);margin-bottom:6px">No entries yet.</div>';
                    return;
                }
                el.innerHTML = data.logbooks.map(l =>
                    `<div class="lb-entry"><small>${l.created_at}</small><br>${l.content.replace(/\n/g,'<br>')}</div>`
                ).join('');
            });
    }

    window.saveLogbook = function(id) {
        const textarea = document.getElementById('lb-text-' + id);
        const content = textarea?.value?.trim();
        if (!content) return;

        fetch('/logbooks/' + id, {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':csrf, Accept:'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ content }),
        })
        .then(r => r.json().then(d => ({ok:r.ok,data:d})))
        .then(({ok,data}) => {
            toast(data.message, ok ? 'success' : 'error');
            if (ok) {
                textarea.value = '';
                loadLogbookEntries(id);
                loadData();
            }
        });
    };

    // ── Init ─────────────────────────────────────────────
    loadData();
    setInterval(loadData, 30000);
})();
</script>
</body>
</html>
