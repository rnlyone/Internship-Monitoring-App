<x-app-layout>
@push('page-css')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<style>
.intern-checkbox-list {
    max-height: 260px;
    overflow-y: auto;
    border: 1px solid var(--bs-border-color);
    border-radius: .375rem;
    padding: .5rem;
}
.intern-checkbox-item {
    padding: .35rem .5rem;
    border-radius: .3rem;
    cursor: pointer;
    transition: background .15s;
}
.intern-checkbox-item:hover { background: rgba(var(--bs-primary-rgb),.06); }
.intern-checkbox-item input:checked ~ .form-check-label { font-weight: 600; }
.assigned-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: .7rem;
    font-weight: 600;
    background: linear-gradient(135deg,#00bad1,#0d6efd);
    color: #fff;
    white-space: nowrap;
}
</style>
@endpush

<div class="row">
    <!-- ── Left: Assignment Form ─────────────────────────────── -->
    <div class="col-xl-4 col-lg-5 mb-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <span class="avatar avatar-sm rounded flex-shrink-0" style="background:linear-gradient(135deg,#00bad1,#0d6efd)">
                    <i class="ti ti-calendar-plus text-white"></i>
                </span>
                <h5 class="card-title mb-0">Assign Schedule</h5>
            </div>
            <div class="card-body">

                @if(session('assign_result'))
                    @php $result = session('assign_result'); @endphp
                    <div class="alert alert-{{ $result['success'] ? 'success' : 'warning' }} alert-dismissible fade show mb-4" role="alert">
                        <i class="ti ti-{{ $result['success'] ? 'circle-check' : 'alert-triangle' }} me-2"></i>
                        {{ $result['message'] }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.schedule-assign.store') }}" method="POST" id="assignForm">
                    @csrf

                    <!-- Date & Time -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Start Date &amp; Time <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="startShift" name="start_shift"
                               placeholder="Pick start date/time" required autocomplete="off" />
                        @error('start_shift')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">End Date &amp; Time <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="endShift" name="end_shift"
                               placeholder="Pick end date/time" required autocomplete="off" />
                        @error('end_shift')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Caption -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Caption</label>
                        <input type="text" class="form-control" name="caption"
                               placeholder="e.g. Morning Shift, Sprint Meeting…" maxlength="255"
                               value="{{ old('caption') }}" />
                    </div>

                    <!-- Intern Multi-Select -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-semibold mb-0">Select Interns <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-text-primary py-0 px-1" id="selectAllBtn">All</button>
                                <button type="button" class="btn btn-sm btn-text-secondary py-0 px-1" id="clearAllBtn">Clear</button>
                            </div>
                        </div>
                        <div class="intern-checkbox-list" id="internList">
                            @forelse($interns as $intern)
                            <div class="intern-checkbox-item form-check mb-0">
                                <input class="form-check-input intern-check" type="checkbox"
                                       name="intern_ids[]" value="{{ $intern->id }}"
                                       id="intern_{{ $intern->id }}"
                                       {{ in_array($intern->id, old('intern_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label w-100" for="intern_{{ $intern->id }}">
                                    {{ $intern->name }}
                                    <small class="text-muted d-block" style="font-size:.7rem">{{ $intern->email }}</small>
                                </label>
                            </div>
                            @empty
                            <p class="text-muted text-center py-3 mb-0">No interns registered yet.</p>
                            @endforelse
                        </div>
                        @error('intern_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        <div class="mt-2 small text-muted" id="selectedCount">0 intern(s) selected</div>
                    </div>

                    <button type="submit" class="btn w-100" style="background:linear-gradient(135deg,#00bad1,#0d6efd);color:#fff;border:none">
                        <i class="ti ti-calendar-plus me-2"></i>Assign Schedule
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Right: Assigned Schedules Table ──────────────────── -->
    <div class="col-xl-8 col-lg-7 mb-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title mb-0">Assigned Schedules</h5>
                <div class="d-flex gap-2 align-items-center">
                    <input type="text" class="form-control form-control-sm" id="tableSearch"
                           placeholder="Search name / caption…" style="max-width:200px">
                    <span class="badge bg-label-danger" id="totalCount">{{ $recent->count() }} records</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="assignedTable">
                    <thead class="table-light">
                        <tr>
                            <th>Intern</th>
                            <th>Schedule</th>
                            <th>Caption</th>
                            <th>Status</th>
                            <th>Assigned By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent as $slot)
                        <tr class="assigned-row"
                            data-search="{{ strtolower($slot->user->name . ' ' . $slot->caption) }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar avatar-xs rounded-circle"
                                          style="background:linear-gradient(135deg,#00bad1,#0d6efd);color:#fff;font-size:.6rem;font-weight:700">
                                        {{ strtoupper(substr($slot->user->name, 0, 2)) }}
                                    </span>
                                    <div>
                                        <div class="fw-semibold" style="font-size:.85rem">{{ $slot->user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size:.8rem">
                                <div class="fw-semibold">{{ $slot->start_shift->format('D, d M Y') }}</div>
                                <small class="text-muted">
                                    {{ $slot->start_shift->format('H:i') }} – {{ $slot->end_shift->format('H:i') }}
                                    <span class="text-muted">({{ round($slot->duration_hours, 1) }}h)</span>
                                </small>
                            </td>
                            <td style="font-size:.8rem">{{ $slot->caption ?: '—' }}</td>
                            <td>
                                @php
                                    $statusMap = [
                                        'not_yet'  => ['label' => 'Not Yet',  'color' => 'primary'],
                                        'ongoing'  => ['label' => 'Ongoing',  'color' => 'warning'],
                                        'done'     => ['label' => 'Done',     'color' => 'success'],
                                        'late'     => ['label' => 'Late',     'color' => 'danger'],
                                        'absence'  => ['label' => 'Absence',  'color' => 'secondary'],
                                    ];
                                    $sm = $statusMap[$slot->status] ?? ['label' => $slot->status, 'color' => 'secondary'];
                                @endphp
                                <span class="badge bg-label-{{ $sm['color'] }}">{{ $sm['label'] }}</span>
                            </td>
                            <td style="font-size:.8rem">{{ $slot->assignedBy?->name ?? '—' }}</td>
                            <td>
                                @if(in_array($slot->status, ['not_yet']))
                                <form action="{{ route('admin.schedule-assign.destroy', $slot) }}"
                                      method="POST" class="d-inline delete-assign-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-text-danger"
                                            title="Delete">
                                        <i class="ti ti-trash ti-sm"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="ti ti-calendar-off ti-24px d-block mb-2"></i>
                                No assigned schedules yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('page-js')
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Flatpickr ────────────────────────────────────────────
    const fpStart = flatpickr('#startShift', {
        enableTime: true, dateFormat: 'Y-m-d H:i',
        time_24hr: true, minuteIncrement: 15,
    });
    const fpEnd = flatpickr('#endShift', {
        enableTime: true, dateFormat: 'Y-m-d H:i',
        time_24hr: true, minuteIncrement: 15,
    });

    // ── Select All / Clear ───────────────────────────────────
    function updateSelectedCount() {
        const n = document.querySelectorAll('.intern-check:checked').length;
        document.getElementById('selectedCount').textContent = n + ' intern(s) selected';
    }
    document.getElementById('selectAllBtn').addEventListener('click', function () {
        document.querySelectorAll('.intern-check').forEach(c => c.checked = true);
        updateSelectedCount();
    });
    document.getElementById('clearAllBtn').addEventListener('click', function () {
        document.querySelectorAll('.intern-check').forEach(c => c.checked = false);
        updateSelectedCount();
    });
    document.querySelectorAll('.intern-check').forEach(c => {
        c.addEventListener('change', updateSelectedCount);
    });
    updateSelectedCount();

    // ── Form validation ──────────────────────────────────────
    document.getElementById('assignForm').addEventListener('submit', function (e) {
        const checked = document.querySelectorAll('.intern-check:checked').length;
        if (checked === 0) {
            e.preventDefault();
            alert('Please select at least one intern.');
        }
    });

    // ── Table search ─────────────────────────────────────────
    document.getElementById('tableSearch').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        let visible = 0;
        document.querySelectorAll('.assigned-row').forEach(row => {
            const match = row.dataset.search.includes(q);
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('totalCount').textContent = visible + ' records';
    });

    // ── Delete confirmation ──────────────────────────────────
    document.querySelectorAll('.delete-assign-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!confirm('Delete this assigned schedule? The intern will lose it.')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush

</x-app-layout>
