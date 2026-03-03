<x-app-layout>
@push('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}">
<style>
/* ── Board layout ─────────────────────────────────── */
.kanban-wrapper {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    overflow-x: auto;
    padding-bottom: 1.5rem;
    min-height: calc(100vh - 240px);
}
.kanban-col {
    flex: 0 0 272px;
    width: 272px;
    background: rgba(105,108,255,.04);
    border: 1px solid rgba(105,108,255,.1);
    border-radius: .6rem;
    padding: .75rem;
}
.kanban-col-header {
    padding: .25rem .25rem .65rem;
    margin-bottom: .65rem;
    border-bottom: 2px solid transparent; /* overridden per column */
}
/* Column accent borders */
.col-backlog   { --cc: #a1acb8; border-color: rgba(161,172,184,.2); background: rgba(161,172,184,.04); }
.col-todo      { --cc: #696cff; border-color: rgba(105,108,255,.15); background: rgba(105,108,255,.04); }
.col-undone    { --cc: #ff9f43; border-color: rgba(255,159,67,.15);  background: rgba(255,159,67,.04); }
.col-on_progress { --cc: #00cfe8; border-color: rgba(0,207,232,.15); background: rgba(0,207,232,.04); }
.col-done      { --cc: #28c76f; border-color: rgba(40,199,111,.15);  background: rgba(40,199,111,.04); }
.col-archive   { --cc: #82868b; border-color: rgba(130,134,139,.15); background: rgba(130,134,139,.04); }
.kanban-col .kanban-col-header { border-bottom-color: var(--cc, #a1acb8); }
.col-dot { width: 9px; height: 9px; border-radius: 50%; background: var(--cc, #a1acb8); flex-shrink: 0; }

/* ── Cards list ───────────────────────────────────── */
.kanban-list { min-height: 60px; }

/* ── Card ─────────────────────────────────────────── */
.kanban-card {
    background: #fff;
    border-radius: .45rem;
    margin-bottom: .55rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
    border-left: 3px solid #696cff;
    cursor: pointer;
    transition: transform .15s, box-shadow .15s;
    user-select: none;
}
.kanban-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(0,0,0,.12);
}
.kanban-card.sortable-ghost { opacity: .35; }
.kanban-card.sortable-chosen { box-shadow: 0 6px 20px rgba(105,108,255,.22); }

/* Drag handle */
.drag-handle {
    cursor: grab;
    color: #ccc;
    font-size: 1rem;
    line-height: 1;
    padding: 1px 2px;
    flex-shrink: 0;
}
.drag-handle:hover { color: #888; }
.drag-handle:active { cursor: grabbing; }

/* Description clamp */
.desc-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Priority badges */
.prio-low    { background: rgba(40,199,111,.12);  color: #28c76f; }
.prio-medium { background: rgba(255,159,67,.12);  color: #ff9f43; }
.prio-high   { background: rgba(234,84,85,.12);   color: #ea5455; }

/* Three-dots menu button */
.kb-dots-btn { background: none; border: none; line-height: 1; padding: 2px 4px; color: #ccc; border-radius: .25rem; transition: color .12s, background .12s; }
.kb-dots-btn:hover, .kb-dots-btn:focus, .kb-dots-btn.show { color: #555; background: rgba(0,0,0,.06); outline: none; }

/* Add card button */
.btn-add-card {
    width: 100%;
    border: 1.5px dashed rgba(0,0,0,.15);
    background: transparent;
    color: #a1acb8;
    border-radius: .4rem;
    padding: .35rem .5rem;
    font-size: .78rem;
    cursor: pointer;
    transition: all .15s;
    margin-top: .35rem;
    text-align: left;
}
.btn-add-card:hover {
    border-color: var(--cc, #696cff);
    color: var(--cc, #696cff);
    background: rgba(105,108,255,.05);
}

/* Overdue date */
.due-overdue { color: #ea5455 !important; }

/* Saving indicator */
#kbSaving {
    position: fixed;
    bottom: 1.5rem;
    right: 1.5rem;
    background: rgba(40,199,111,.92);
    color: #fff;
    border-radius: .4rem;
    padding: .35rem .8rem;
    font-size: .8rem;
    display: none;
    align-items: center;
    gap: .4rem;
    z-index: 9999;
    box-shadow: 0 4px 12px rgba(40,199,111,.3);
}

/* Color swatch */
.color-swatch-preview {
    width: 22px; height: 22px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px rgba(0,0,0,.18);
    display: inline-block;
    flex-shrink: 0;
}

/* Avatar xs */
.avatar-xs-custom {
    width: 22px; height: 22px;
    border-radius: 50%;
    font-size: .58rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    flex-shrink: 0;
}

/* Detail modal color bar */
.detail-color-bar { height: 4px; border-radius: .45rem .45rem 0 0; }

/* ── Color swatches ───────────────────────────────── */
.kb-swatch {
    width: 28px; height: 28px;
    border-radius: 50%;
    cursor: pointer;
    border: none;
    outline: 3px solid transparent;
    outline-offset: 2px;
    transition: transform .12s, outline-color .12s;
    flex-shrink: 0;
    display: inline-block;
    vertical-align: middle;
}
.kb-swatch:hover { transform: scale(1.18); }
.kb-swatch.selected {
    outline-color: #696cff;
    transform: scale(1.18);
}
.kb-swatch-custom {
    background: #e8eaf0 !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}
.kb-swatch-custom input[type="color"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    border: none;
    padding: 0;
}
.selected-color-preview {
    width: 18px; height: 18px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px rgba(0,0,0,.18);
    flex-shrink: 0;
    display: inline-block;
}
</style>
@endpush

{{-- Pass initial data to JS --}}
@php
$kanbanMeta = [
    'isAdmin' => auth()->user()->isAdmin(),
    'userId'  => auth()->id(),
    'routes'  => [
        'store'   => route('kanban.store'),
        'reorder' => route('kanban.reorder'),
        'base'    => url('/kanban/cards'),
    ],
];
@endphp
<script id="kanbanData" type="application/json">@json($cardsData)</script>
<script id="kanbanMeta" type="application/json">@json($kanbanMeta)</script>

<!-- ── Page header ─────────────────────────────────── -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-5">
    <div>
        <h4 class="mb-1"><i class="ti ti-layout-kanban me-2"></i>Kanban Board</h4>
        <p class="text-muted mb-0">
            @if(auth()->user()->isAdmin())
                Create &amp; manage cards. Everyone can drag cards between columns.
            @else
                Drag cards between columns to update their status.
            @endif
        </p>
    </div>
    @if(auth()->user()->isAdmin())
    <button class="btn btn-primary" onclick="openCreate(null)">
        <i class="ti ti-plus me-1"></i>New Card
    </button>
    @endif
</div>

<!-- ── Board ───────────────────────────────────────── -->
<div class="kanban-wrapper" id="kanbanBoard"></div>

<!-- ── Saving indicator ────────────────────────────── -->
<div id="kbSaving"><i class="ti ti-loader-2 ti-spin"></i> Saving…</div>

<!-- ═══════════════════════════════════════════════════
     Create / Edit Modal  (admin only)
═════════════════════════════════════════════════════ -->
<div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cfModalTitle">New Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cardForm" novalidate>
                <input type="hidden" id="cfId">
                <div class="modal-body">

                    {{-- Title --}}
                    <div class="mb-4">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="cfTitle"
                               placeholder="Card title…" maxlength="255">
                        <div class="invalid-feedback" id="cfTitleErr"></div>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="cfDesc" rows="3"
                                  placeholder="Optional details…" maxlength="5000"></textarea>
                    </div>

                    {{-- Column + Priority --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label">Column <span class="text-danger">*</span></label>
                            <select class="form-select" id="cfColumn">
                                <option value="backlog">Backlog</option>
                                <option value="todo">To Do</option>
                                <option value="undone">Undone</option>
                                <option value="on_progress">On Progress</option>
                                <option value="done">Done</option>
                                <option value="archive">Archive</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Priority</label>
                            <select class="form-select" id="cfPriority">
                                <option value="">No priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                    </div>

                    {{-- Due date + Assign to --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label">Due Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="cfDueDate"
                                       placeholder="Pick a date…" autocomplete="off" readonly
                                       style="cursor:pointer;background:#fff">
                                <button type="button" class="btn btn-outline-secondary"
                                        id="cfDueDateBtn" tabindex="-1">
                                    <i class="ti ti-calendar"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary"
                                        id="cfDueDateClear" tabindex="-1" title="Clear date">
                                    <i class="ti ti-x" style="font-size:.75rem"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Assign To</label>
                            <select class="form-select" id="cfAssignTo">
                                <option value="">Unassigned</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Card color --}}
                    <div>
                        <label class="form-label">Card Color</label>
                        <input type="hidden" id="cfColor" value="#696cff">
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-2" id="cfSwatchGrid">
                            <span class="kb-swatch selected" data-color="#696cff" style="background:#696cff" title="Purple"   onclick="selectSwatch(this)"></span>
                            <span class="kb-swatch"          data-color="#7367f0" style="background:#7367f0" title="Violet"   onclick="selectSwatch(this)"></span>
                            <span class="kb-swatch"          data-color="#28c76f" style="background:#28c76f" title="Green"    onclick="selectSwatch(this)"></span>
                            <span class="kb-swatch"          data-color="#ff9f43" style="background:#ff9f43" title="Orange"   onclick="selectSwatch(this)"></span>
                            <span class="kb-swatch"          data-color="#ea5455" style="background:#ea5455" title="Red"      onclick="selectSwatch(this)"></span>
                            <span class="kb-swatch"          data-color="#00cfe8" style="background:#00cfe8" title="Cyan"     onclick="selectSwatch(this)"></span>
                            <span class="kb-swatch"          data-color="#ff6f91" style="background:#ff6f91" title="Pink"     onclick="selectSwatch(this)"></span>
                            <span class="kb-swatch"          data-color="#ffd460" style="background:#ffd460" title="Yellow"   onclick="selectSwatch(this)"></span>
                            <span class="kb-swatch"          data-color="#a1acb8" style="background:#a1acb8" title="Gray"     onclick="selectSwatch(this)"></span>
                            <span class="kb-swatch"          data-color="#4a5568" style="background:#4a5568" title="Dark"     onclick="selectSwatch(this)"></span>
                            <span class="kb-swatch kb-swatch-custom"              title="Custom color…">
                                <i class="ti ti-palette" style="font-size:.8rem;color:#888;pointer-events:none;position:relative;z-index:1"></i>
                                <input type="color" id="cfColorCustom" value="#696cff" onchange="setColorFromCustom(this.value)" oninput="setColorFromCustom(this.value)">
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="selected-color-preview" id="cfColorSwatch" style="background:#696cff"></span>
                            <code class="small text-muted" id="cfColorHex">#696cff</code>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="cfSubmit">
                        <span class="spinner-border spinner-border-sm d-none me-1"
                              id="cfSpinner"></span>
                        <span id="cfLabel">Create Card</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════
     Card Detail Modal  (all roles)
═════════════════════════════════════════════════════ -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden">
            <div class="detail-color-bar" id="detailBar"></div>
            <div class="modal-header border-0 pb-1">
                <div>
                    <h5 class="modal-title mb-0" id="detailTitle">—</h5>
                    <small class="text-muted" id="detailColLabel">—</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                {{-- Priority + Color --}}
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="badge rounded-pill" id="detailPrio" style="font-size:.68rem">—</span>
                    <div class="color-swatch-preview" id="detailColorSwatch"></div>
                    <span class="text-muted small" id="detailColorHex">—</span>
                </div>

                {{-- Description --}}
                <div id="detailDescBlock" class="mb-4">
                    <div class="text-muted small text-uppercase fw-semibold mb-1"
                         style="letter-spacing:.06em">Description</div>
                    <div id="detailDesc" style="white-space:pre-wrap;font-size:.88rem"></div>
                </div>

                {{-- Metadata grid --}}
                <div class="row g-3">
                    <div class="col-6">
                        <div class="text-muted small text-uppercase fw-semibold mb-1"
                             style="letter-spacing:.06em">Due Date</div>
                        <div id="detailDue" class="small">—</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small text-uppercase fw-semibold mb-1"
                             style="letter-spacing:.06em">Assigned To</div>
                        <div id="detailAssigned" class="small">—</div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small text-uppercase fw-semibold mb-1"
                             style="letter-spacing:.06em">Created By</div>
                        <div id="detailCreator" class="small">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="detailFooter">
                <button class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════
     Delete Confirm Modal
═════════════════════════════════════════════════════ -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="ti ti-trash ti-48px text-danger d-block mb-2"></i>
                <h5>Delete Card?</h5>
                <p class="text-muted small mb-0">
                    "<strong id="delCardTitle"></strong>" will be permanently removed.
                </p>
            </div>
            <div class="modal-footer justify-content-center gap-2">
                <button class="btn btn-sm btn-outline-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm btn-danger" id="confirmDelBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1"
                          id="delSpinner"></span>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Toast ─────────────────────────────────────────── -->
<div class="bs-toast toast fade bg-body border-0 shadow position-fixed bottom-0 end-0 m-3"
     id="kbToast" style="z-index:10000" role="alert">
    <div class="toast-header border-0">
        <i class="ti me-2" id="kbToastIcon"></i>
        <span class="fw-semibold me-auto" id="kbToastTitle"></span>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body small" id="kbToastBody"></div>
</div>

@push('page-js')
<script src="{{ asset('assets/vendor/libs/sortablejs/sortable.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script>
(function () {
    'use strict';

    /* ── Bootstrap instances ──────────────────────── */
    const cardModal   = new bootstrap.Modal('#cardModal');
    const detailModal = new bootstrap.Modal('#detailModal');
    const deleteModal = new bootstrap.Modal('#deleteModal');
    const toast       = new bootstrap.Toast('#kbToast', { delay: 3200 });

    /* ── Metadata ─────────────────────────────────── */
    const INITIAL = JSON.parse(document.getElementById('kanbanData').textContent);
    const META    = JSON.parse(document.getElementById('kanbanMeta').textContent);
    const IS_ADMIN = META.isAdmin;
    const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
    const ROUTES  = META.routes;

    const COLUMNS = [
        { key: 'backlog',      label: 'Backlog',      color: '#a1acb8' },
        { key: 'todo',         label: 'To Do',         color: '#696cff' },
        { key: 'undone',       label: 'Undone',        color: '#ff9f43' },
        { key: 'on_progress',  label: 'On Progress',   color: '#00cfe8' },
        { key: 'done',         label: 'Done',          color: '#28c76f' },
        { key: 'archive',      label: 'Archive',       color: '#82868b' },
    ];

    /* ── State ────────────────────────────────────── */
    // state[colKey] = [ card, ... ]  (arrays of plain objects)
    let state = {};
    COLUMNS.forEach(c => { state[c.key] = INITIAL[c.key] ?? []; });

    let _deleteId  = null;
    let _saveTimer = null;
    let _fpDue     = null;

    /* ── Helpers ──────────────────────────────────── */
    function esc(s) {
        if (s == null) return '';
        return String(s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function initials(name) {
        return (name || '?').split(' ').slice(0, 2).map(p => p[0]).join('').toUpperCase();
    }
    function avatarBg(name) {
        const list = ['primary','success','danger','warning','info'];
        let h = 0;
        for (const c of (name || '')) h = (h * 31 + c.charCodeAt(0)) & 0xffffffff;
        return list[Math.abs(h) % list.length];
    }
    function prioClass(p)  { return { low:'prio-low', medium:'prio-medium', high:'prio-high' }[p] ?? 'bg-label-secondary'; }
    function prioLabel(p)  { return { low:'Low', medium:'Medium', high:'High' }[p] ?? 'No priority'; }
    function colMeta(key)  { return COLUMNS.find(c => c.key === key) ?? { label: key, color: '#a1acb8' }; }
    function isOverdue(d)  { return d && new Date(d) < new Date(new Date().toDateString()); }
    function fmtDate(str)  {
        if (!str) return '';
        const d = new Date(str);
        return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }
    function findCard(id) {
        for (const col of Object.keys(state)) {
            const c = state[col].find(x => x.id == id);
            if (c) return c;
        }
        return null;
    }
    function showToast(msg, type = 'success') {
        document.getElementById('kbToastIcon').className =
            type === 'success' ? 'ti ti-circle-check me-2 text-success'
                               : 'ti ti-circle-x me-2 text-danger';
        document.getElementById('kbToastTitle').textContent = type === 'success' ? 'Done' : 'Error';
        document.getElementById('kbToastBody').textContent  = msg;
        toast.show();
    }
    function updateCount(colKey) {
        const el = document.getElementById(`kbCount-${colKey}`);
        if (el) el.textContent = (state[colKey] ?? []).length;
    }

    /* ── Card element builder ─────────────────────── */
    function makeCardEl(card) {
        const color    = card.color || '#696cff';
        const hasDesc  = card.description?.trim();
        const overdue  = isOverdue(card.due_date);
        const dueFmt   = card.due_date_fmt ?? fmtDate(card.due_date);
        const dueCls   = overdue ? 'due-overdue' : 'text-muted';

        const div = document.createElement('div');
        div.className = 'kanban-card';
        div.dataset.id = card.id;
        div.style.borderLeftColor = color;

        div.innerHTML = `
            <div class="p-3">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <div>
                        ${card.priority
                            ? `<span class="badge rounded-pill ${prioClass(card.priority)}"
                                    style="font-size:.6rem">${prioLabel(card.priority)}</span>`
                            : ''}
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <div class="dropdown">
                            <button class="kb-dots-btn" data-bs-toggle="dropdown" data-bs-boundary="viewport"
                                    onclick="event.stopPropagation()" aria-label="Card options">
                                <i class="ti ti-dots-vertical" style="font-size:.9rem"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end py-1" style="min-width:130px;font-size:.8rem">
                                <li>
                                    <a class="dropdown-item" href="#"
                                       onclick="event.preventDefault();event.stopPropagation();openDetail(${card.id})">
                                        <i class="ti ti-eye me-2 text-muted"></i>View
                                    </a>
                                </li>
                                ${IS_ADMIN ? `
                                <li>
                                    <a class="dropdown-item" href="#"
                                       onclick="event.preventDefault();event.stopPropagation();openEdit(${card.id})">
                                        <i class="ti ti-pencil me-2 text-primary"></i>Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#"
                                       onclick="event.preventDefault();event.stopPropagation();openDelete(${card.id},'${esc(card.title).replace(/'/g,"\\'")}')">
                                        <i class="ti ti-trash me-2"></i>Delete
                                    </a>
                                </li>` : ''}
                            </ul>
                        </div>
                        <i class="ti ti-grid-dots drag-handle" title="Drag"></i>
                    </div>
                </div>
                <div class="fw-semibold mb-1" style="font-size:.82rem;line-height:1.35">
                    ${esc(card.title)}
                </div>
                ${hasDesc
                    ? `<div class="text-muted desc-clamp mb-2" style="font-size:.73rem;line-height:1.4">${esc(card.description)}</div>`
                    : ''}
                <div class="d-flex align-items-center justify-content-between mt-1 gap-1">
                    <span class="${dueCls}" style="font-size:.68rem">
                        ${dueFmt
                            ? `<i class="ti ti-calendar-event me-1"></i>${esc(dueFmt)}`
                            : ''}
                    </span>
                    ${card.assigned_name
                        ? `<span class="avatar-xs-custom bg-label-${avatarBg(card.assigned_name)}"
                                title="${esc(card.assigned_name)}">${initials(card.assigned_name)}</span>`
                        : ''}
                </div>
            </div>`;

        div.addEventListener('click', function (e) {
            if (e.target.closest('.dropdown') || e.target.closest('.drag-handle')) return;
            openDetail(card.id);
        });

        return div;
    }

    /* ── Board render ─────────────────────────────── */
    function renderBoard() {
        const board = document.getElementById('kanbanBoard');
        board.innerHTML = '';

        COLUMNS.forEach(col => {
            const cards = state[col.key] ?? [];

            const wrap = document.createElement('div');
            wrap.className = `kanban-col col-${col.key}`;
            wrap.dataset.col = col.key;
            wrap.innerHTML = `
                <div class="kanban-col-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="col-dot"></span>
                            <span class="fw-semibold" style="font-size:.82rem">${col.label}</span>
                            <span class="badge bg-label-secondary" id="kbCount-${col.key}"
                                  style="font-size:.6rem">${cards.length}</span>
                        </div>
                        ${IS_ADMIN
                            ? `<button class="btn btn-icon btn-text-secondary btn-xs"
                                       onclick="openCreate('${col.key}')" title="Add card">
                                   <i class="ti ti-plus" style="font-size:.85rem"></i>
                               </button>`
                            : ''}
                    </div>
                </div>
                <div class="kanban-list" id="kbList-${col.key}" data-col="${col.key}"></div>
                ${IS_ADMIN
                    ? `<button class="btn-add-card" onclick="openCreate('${col.key}')">
                           <i class="ti ti-plus me-1"></i>Add card
                       </button>`
                    : ''}`;

            board.appendChild(wrap);

            const list = wrap.querySelector(`#kbList-${col.key}`);
            cards.forEach(c => list.appendChild(makeCardEl(c)));

            new Sortable(list, {
                group: 'kanban',
                animation: 200,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: onDragEnd,
            });
        });
    }

    /* ── Drag & drop ──────────────────────────────── */
    function onDragEnd() {
        // Snapshot ALL card objects into a flat map BEFORE we start mutating state.
        // Without this, cards dragged out of their old column disappear from state[]
        // before the new column is processed, causing findCard() to return null and
        // dropping those cards from the save payload entirely.
        const cardMap = {};
        COLUMNS.forEach(col => {
            (state[col.key] ?? []).forEach(c => { cardMap[c.id] = c; });
        });

        // Rebuild state from DOM using the snapshot map
        COLUMNS.forEach(col => {
            const list = document.getElementById(`kbList-${col.key}`);
            if (!list) return;
            state[col.key] = Array.from(list.querySelectorAll(':scope > .kanban-card'))
                .map(el => {
                    const card = cardMap[parseInt(el.dataset.id)];
                    if (card) card.column_name = col.key;
                    return card;
                })
                .filter(Boolean);
            updateCount(col.key);
        });

        clearTimeout(_saveTimer);
        _saveTimer = setTimeout(persistBoard, 700);
    }

    async function persistBoard() {
        const saving = document.getElementById('kbSaving');
        saving.style.display = 'flex';

        const columns = {};
        COLUMNS.forEach(col => { columns[col.key] = (state[col.key] ?? []).map(c => c.id); });

        try {
            const res = await fetch(ROUTES.reorder, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ columns }),
            });
            if (!res.ok) {
                console.error('Board save failed, status:', res.status);
                showToast('Board save failed — please refresh.', 'error');
            }
        } catch (err) {
            console.error('Board save failed:', err);
            showToast('Board save failed — check connection.', 'error');
        } finally {
            setTimeout(() => { saving.style.display = 'none'; }, 800);
        }
    }

    /* ── Create modal ─────────────────────────────── */
    window.openCreate = function (colKey) {
        document.getElementById('cfModalTitle').textContent = 'New Card';
        document.getElementById('cfLabel').textContent      = 'Create Card';
        document.getElementById('cfId').value       = '';
        document.getElementById('cfTitle').value    = '';
        document.getElementById('cfDesc').value     = '';
        document.getElementById('cfColumn').value   = colKey ?? 'backlog';
        document.getElementById('cfPriority').value = '';
        document.getElementById('cfAssignTo').value = '';
        setColor('#696cff');
        clearCfErrors();
        if (_fpDue) _fpDue.clear();
        cardModal.show();
    };

    /* ── Edit modal ───────────────────────────────── */
    window.openEdit = async function (id) {
        try {
            const res  = await fetch(`${ROUTES.base}/${id}`);
            const card = await res.json();

            document.getElementById('cfModalTitle').textContent = 'Edit Card';
            document.getElementById('cfLabel').textContent      = 'Save Changes';
            document.getElementById('cfId').value       = card.id;
            document.getElementById('cfTitle').value    = card.title;
            document.getElementById('cfDesc').value     = card.description ?? '';
            document.getElementById('cfColumn').value   = card.column_name;
            document.getElementById('cfPriority').value = card.priority ?? '';
            document.getElementById('cfAssignTo').value = card.assigned_to ?? '';
            setColor(card.color || '#696cff');
            clearCfErrors();
            if (_fpDue) _fpDue.setDate(card.due_date ?? '', false, 'Y-m-d');

            detailModal.hide();
            cardModal.show();
        } catch (err) {
            showToast('Failed to load card.', 'error');
        }
    };

    /* ── Detail modal ─────────────────────────────── */
    window.openDetail = async function (id) {
        try {
            const res  = await fetch(`${ROUTES.base}/${id}`);
            const card = await res.json();
            const col  = colMeta(card.column_name);

            document.getElementById('detailBar').style.background   = card.color || '#696cff';
            document.getElementById('detailTitle').textContent      = card.title;
            document.getElementById('detailColLabel').textContent   = col.label;

            // Priority badge
            const pb = document.getElementById('detailPrio');
            if (card.priority) {
                pb.className   = `badge rounded-pill ${prioClass(card.priority)}`;
                pb.textContent = prioLabel(card.priority);
            } else {
                pb.className   = 'badge bg-label-secondary';
                pb.textContent = 'No priority';
            }

            // Color
            document.getElementById('detailColorSwatch').style.background = card.color || '#696cff';
            document.getElementById('detailColorHex').textContent          = card.color || '#696cff';

            // Description
            const descBlock = document.getElementById('detailDescBlock');
            if (card.description?.trim()) {
                document.getElementById('detailDesc').textContent = card.description;
                descBlock.style.display = '';
            } else {
                descBlock.style.display = 'none';
            }

            // Due date
            const dueEl  = document.getElementById('detailDue');
            const dueFmt = card.due_date_fmt ?? fmtDate(card.due_date);
            if (dueFmt) {
                const overdue = isOverdue(card.due_date);
                dueEl.innerHTML = `<span class="${overdue ? 'text-danger fw-semibold' : ''}">
                    ${overdue ? '<i class="ti ti-alert-circle me-1"></i>' : ''}${esc(dueFmt)}</span>`;
            } else {
                dueEl.textContent = '—';
            }

            document.getElementById('detailAssigned').textContent = card.assigned_name || '—';
            document.getElementById('detailCreator').textContent  = card.creator_name  || '—';

            // Footer buttons
            const footer = document.getElementById('detailFooter');
            footer.innerHTML = `<button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>`;
            if (IS_ADMIN) {
                footer.innerHTML += `
                    <button class="btn btn-sm btn-outline-primary" onclick="openEdit(${card.id})">
                        <i class="ti ti-pencil me-1"></i>Edit
                    </button>
                    <button class="btn btn-sm btn-danger"
                            onclick="openDelete(${card.id},'${esc(card.title).replace(/'/g,"\\'")}')">
                        <i class="ti ti-trash me-1"></i>Delete
                    </button>`;
            }

            detailModal.show();
        } catch (err) {
            showToast('Failed to load card details.', 'error');
        }
    };

    /* ── Delete ───────────────────────────────────── */
    window.openDelete = function (id, title) {
        _deleteId = id;
        document.getElementById('delCardTitle').textContent = title;
        detailModal.hide();
        deleteModal.show();
    };

    document.getElementById('confirmDelBtn').addEventListener('click', async function () {
        if (!_deleteId) return;
        const spinner = document.getElementById('delSpinner');
        this.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const res  = await fetch(`${ROUTES.base}/${_deleteId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const data = await res.json();
            if (!res.ok) { showToast(data.message ?? 'Delete failed.', 'error'); return; }

            // Remove from state + DOM
            for (const col of Object.keys(state)) {
                state[col] = state[col].filter(c => c.id != _deleteId);
            }
            document.querySelectorAll(`.kanban-card[data-id="${_deleteId}"]`)
                .forEach(el => el.remove());
            COLUMNS.forEach(c => updateCount(c.key));

            deleteModal.hide();
            showToast(data.message, 'success');
        } catch (err) {
            showToast('Request failed.', 'error');
        } finally {
            this.disabled = false;
            spinner.classList.add('d-none');
            _deleteId = null;
        }
    });

    /* ── Form submit (create / update) ───────────── */
    function clearCfErrors() {
        document.getElementById('cfTitle').classList.remove('is-invalid');
        document.getElementById('cfTitleErr').textContent = '';
    }

    document.getElementById('cardForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        clearCfErrors();

        const id     = document.getElementById('cfId').value;
        const isEdit = !!id;
        const url    = isEdit ? `${ROUTES.base}/${id}` : ROUTES.store;
        const method = isEdit ? 'PUT' : 'POST';

        const body = {
            title:       document.getElementById('cfTitle').value.trim(),
            description: document.getElementById('cfDesc').value.trim() || null,
            column_name: document.getElementById('cfColumn').value,
            priority:    document.getElementById('cfPriority').value || null,
            due_date:    document.getElementById('cfDueDate').value || null,
            assigned_to: document.getElementById('cfAssignTo').value || null,
            color:       document.getElementById('cfColor').value || '#696cff',
        };

        if (!body.title) {
            document.getElementById('cfTitle').classList.add('is-invalid');
            document.getElementById('cfTitleErr').textContent = 'Title is required.';
            return;
        }

        const spinner = document.getElementById('cfSpinner');
        const btn     = document.getElementById('cfSubmit');
        spinner.classList.remove('d-none');
        btn.disabled = true;

        try {
            const res  = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify(body),
            });
            const data = await res.json();

            if (!res.ok) {
                if (res.status === 422 && data.errors?.title) {
                    document.getElementById('cfTitle').classList.add('is-invalid');
                    document.getElementById('cfTitleErr').textContent = data.errors.title[0];
                } else {
                    showToast(data.message ?? 'An error occurred.', 'error');
                }
                return;
            }

            const card = data.card;
            if (isEdit) {
                // Update state and re-render (handles column change too)
                for (const col of Object.keys(state)) {
                    const idx = state[col].findIndex(c => c.id == id);
                    if (idx !== -1) {
                        if (state[col][idx].column_name !== card.column_name) {
                            // Moved to different column
                            state[col].splice(idx, 1);
                            state[card.column_name] = state[card.column_name] ?? [];
                            state[card.column_name].push(card);
                        } else {
                            state[col][idx] = card;
                        }
                        break;
                    }
                }
                renderBoard();
            } else {
                // Add to state + DOM
                state[card.column_name] = state[card.column_name] ?? [];
                state[card.column_name].push(card);
                const list = document.getElementById(`kbList-${card.column_name}`);
                if (list) list.appendChild(makeCardEl(card));
                updateCount(card.column_name);
            }

            showToast(data.message, 'success');
            cardModal.hide();
        } catch (err) {
            showToast('Request failed.', 'error');
        } finally {
            spinner.classList.add('d-none');
            btn.disabled = false;
        }
    });

    /* ── Color helpers ────────────────────────────── */
    const PRESET_COLORS = [
        '#696cff','#7367f0','#28c76f','#ff9f43',
        '#ea5455','#00cfe8','#ff6f91','#ffd460',
        '#a1acb8','#4a5568',
    ];

    /* ── Colour helpers — exposed on window so onclick="" attributes work ── */
    function setColor(hex) {
        hex = hex.toLowerCase();
        document.getElementById('cfColor').value                    = hex;
        document.getElementById('cfColorSwatch').style.background   = hex;
        document.getElementById('cfColorHex').textContent           = hex;
        document.getElementById('cfColorCustom').value              = hex;
        document.querySelectorAll('#cfSwatchGrid .kb-swatch[data-color]').forEach(s => {
            s.classList.toggle('selected', s.dataset.color === hex);
        });
    }
    window.setColor = setColor;

    /* Called by onclick="selectSwatch(this)" on every preset <span> swatch */
    window.selectSwatch = function (el) {
        const color = el.dataset.color;
        if (color) setColor(color);
    };

    /* Called by oninput/onchange on the native <input type="color"> */
    window.setColorFromCustom = function (hex) {
        document.querySelectorAll('#cfSwatchGrid .kb-swatch[data-color]').forEach(s => s.classList.remove('selected'));
        hex = hex.toLowerCase();
        document.getElementById('cfColor').value                  = hex;
        document.getElementById('cfColorSwatch').style.background = hex;
        document.getElementById('cfColorHex').textContent         = hex;
    };

    /* ── Swatch init — no delegation needed; each swatch calls window.selectSwatch directly ── */
    function initSwatches() {
        /* intentionally empty — wired via onclick/oninput HTML attributes */
    }

    /* ── Flatpickr init ───────────────────────────── */
    function initFlatpickr() {
        _fpDue = flatpickr('#cfDueDate', {
            dateFormat: 'Y-m-d',
            allowInput: false,
            disableMobile: true,
            appendTo: document.body,
            onReady(_, __, fp) {
                // Clicking the calendar icon button also opens the picker
                document.getElementById('cfDueDateBtn').addEventListener('click', () => fp.open());
            },
        });
        document.getElementById('cfDueDateClear').addEventListener('click', () => {
            if (_fpDue) _fpDue.clear();
        });
    }

    /* ── Boot ─────────────────────────────────────── */
    renderBoard();
    initSwatches();
    initFlatpickr();
})();
</script>
@endpush
</x-app-layout>
