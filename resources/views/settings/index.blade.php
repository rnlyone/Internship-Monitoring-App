<x-app-layout>
<div class="row">
    <div class="col-md-6">
        <div class="card mb-6">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-settings me-2"></i>Application Settings
                </h5>
                <p class="card-subtitle mt-1">Configure internship monitoring parameters</p>
            </div>
            <div class="card-body">
                <div class="mb-5">
                    <label class="form-label" for="maxHoursInput">Maximum Working Hours Per Week</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="maxHoursInput" value="{{ $maxHours }}" min="1" max="168" step="0.5">
                        <span class="input-group-text">hours</span>
                    </div>
                    <div class="form-text">This limits how many hours each intern can schedule per week (Monday–Sunday).</div>
                </div>

                <div class="mb-5">
                    <label class="form-label d-block">Schedule Submission</label>
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="submissionOpenToggle" {{ $scheduleSubmissionOpen ? 'checked' : '' }}>
                        </div>
                        <div>
                            <span id="submissionStatusLabel" class="fw-semibold {{ $scheduleSubmissionOpen ? 'text-success' : 'text-danger' }}">
                                {{ $scheduleSubmissionOpen ? 'Open — Interns can submit schedules' : 'Closed — Schedule submission is disabled' }}
                            </span>
                            <div class="form-text mb-0">Toggle to allow or block interns from submitting new schedules.</div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-primary" id="saveSettingsBtn">
                    <i class="ti ti-device-floppy me-1"></i>Save Settings
                </button>
                <span class="text-success d-none ms-3" id="savedIndicator">
                    <i class="ti ti-check me-1"></i>Saved!
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-info-circle me-2"></i>Settings Info
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-primary">
                    <div class="d-flex align-items-start">
                        <i class="ti ti-bulb me-2 mt-1"></i>
                        <div>
                            <h6 class="alert-heading mb-1">How it works</h6>
                            <ul class="mb-0 ps-3">
                                <li>Interns cannot create schedules that exceed the maximum weekly hours.</li>
                                <li>The week is counted Monday through Sunday.</li>
                                <li>Changing this value affects future schedule creation only.</li>
                                <li>Existing schedules that exceed the new limit will not be removed.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <div class="d-flex align-items-start">
                        <i class="ti ti-clock me-2 mt-1"></i>
                        <div>
                            <h6 class="alert-heading mb-1">Presence Rules</h6>
                            <ul class="mb-0 ps-3">
                                <li>Entry button appears 30 minutes before schedule starts.</li>
                                <li>Entry button disappears 15 minutes after start (reminder card hidden).</li>
                                <li>After 15 minutes: status becomes <strong>Late</strong>.</li>
                                <li>After 30 minutes: status becomes <strong>Absence</strong>.</li>
                                <li>Exit is prevented until the full shift duration is served (adjusted for late entry).</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    document.getElementById('saveSettingsBtn').addEventListener('click', function() {
        const maxHours = document.getElementById('maxHoursInput').value;
        const submissionOpen = document.getElementById('submissionOpenToggle').checked;
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

        fetch('{{ route("settings.update") }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                max_working_hours_per_week: maxHours,
                schedule_submission_open: submissionOpen,
            }),
        })
        .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
        .then(({ ok, data }) => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Save Settings';

            if (ok) {
                const indicator = document.getElementById('savedIndicator');
                indicator.classList.remove('d-none');

                const label = document.getElementById('submissionStatusLabel');
                label.textContent = data.schedule_submission_open
                    ? 'Open — Interns can submit schedules'
                    : 'Closed — Schedule submission is disabled';
                label.className = 'fw-semibold ' + (data.schedule_submission_open ? 'text-success' : 'text-danger');

                setTimeout(() => indicator.classList.add('d-none'), 3000);
            } else {
                alert(data.message || 'Failed to save settings.');
            }
        });
    });
});
</script>
@endpush
</x-app-layout>
