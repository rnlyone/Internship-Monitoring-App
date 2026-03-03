<x-app-layout>
@push('page-css')
<style>
.user-row { transition: opacity .25s; }
.user-row.removing { opacity: 0; }
.role-admin  { background: rgba(105,108,255,.12); color: #696cff; }
.role-intern { background: rgba(40,199,111,.12);  color: #28c76f; }
.avatar-self { outline: 2px solid #696cff; outline-offset: 2px; }
.filter-label { font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #a1acb8; }
#emptyRow, #loadingRow { display: none; }
</style>
@endpush

<!-- ── Page header ─────────────────────────────────────── -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-5">
    <div>
        <h4 class="mb-1"><i class="ti ti-users me-2"></i>User Management</h4>
        <p class="text-muted mb-0">Create, edit, and remove user accounts.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openCreateModal()">
        <i class="ti ti-plus me-1"></i>Add User
    </button>
</div>

<!-- ── Stats row ──────────────────────────────────────── -->
<div class="row g-4 mb-5">
    <div class="col-sm-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="avatar avatar-lg">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                        <i class="ti ti-users ti-28px"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0" id="statTotal">{{ $totalUsers }}</h5>
                    <small class="text-muted">Total Users</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="avatar avatar-lg">
                    <span class="avatar-initial rounded-circle bg-label-info">
                        <i class="ti ti-shield-check ti-28px"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0" id="statAdmins">{{ $totalAdmins }}</h5>
                    <small class="text-muted">Administrators</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center gap-4">
                <div class="avatar avatar-lg">
                    <span class="avatar-initial rounded-circle bg-label-success">
                        <i class="ti ti-id-badge ti-28px"></i>
                    </span>
                </div>
                <div>
                    <h5 class="mb-0" id="statInterns">{{ $totalInterns }}</h5>
                    <small class="text-muted">Interns</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Filter + Table card ───────────────────────────── -->
<div class="card">
    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <h5 class="card-title mb-0"><i class="ti ti-list me-2"></i>All Users</h5>
        <div class="d-flex flex-wrap gap-2 align-items-end">
            <div>
                <label class="filter-label d-block">Role</label>
                <select class="form-select form-select-sm" id="filterRole" style="width:130px">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="intern">Intern</option>
                </select>
            </div>
            <div>
                <label class="filter-label d-block">Search</label>
                <input type="text" class="form-control form-control-sm" id="filterSearch"
                    placeholder="Name or email…" style="width:200px">
            </div>
            <button class="btn btn-sm btn-primary" id="btnSearch">
                <i class="ti ti-search me-1"></i>Search
            </button>
            <button class="btn btn-sm btn-outline-secondary" id="btnReset" title="Reset">
                <i class="ti ti-refresh"></i>
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:220px">User</th>
                    <th style="width:90px">Role</th>
                    <th style="width:120px">Joined</th>
                    <th style="width:110px">Schedules</th>
                    <th style="width:120px" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <tr id="loadingRow">
                    <td colspan="5" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading…</span>
                        </div>
                        <p class="text-muted mt-2 mb-0">Fetching users…</p>
                    </td>
                </tr>
                <tr id="emptyRow">
                    <td colspan="5" class="text-center py-5">
                        <i class="ti ti-users-group ti-48px text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">No users found.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ── Create / Edit Modal ───────────────────────────── -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="formUserId">
                    <input type="hidden" name="_method" id="formMethod" value="POST">

                    <div class="mb-4">
                        <label class="form-label" for="formName">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="formName" placeholder="John Doe" required>
                        <div class="invalid-feedback" id="errName"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="formEmail">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="formEmail" placeholder="user@example.com" required>
                        <div class="invalid-feedback" id="errEmail"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label" for="formRole">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="formRole" required>
                            <option value="intern">Intern</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div class="invalid-feedback" id="errRole"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label" for="formPassword">
                            Password
                            <span id="passwordHint" class="text-muted small ms-1">(leave blank to keep current)</span>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="formPassword" autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                                <i class="ti ti-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback d-block" id="errPassword"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="userFormSubmit">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="formSpinner"></span>
                        <span id="formSubmitLabel">Create User</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Delete confirm Modal ──────────────────────────── -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="ti ti-trash ti-48px text-danger mb-3 d-block"></i>
                <h5>Delete User?</h5>
                <p class="text-muted small mb-0">
                    This will permanently remove <strong id="deleteUserName">this user</strong>
                    and all their schedules and logbooks.
                </p>
            </div>
            <div class="modal-footer justify-content-center gap-2">
                <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger btn-sm" id="confirmDeleteBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="deleteSpinner"></span>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Toast ─────────────────────────────────────────── -->
<div class="bs-toast toast toast-placement-ex m-2 fade bg-body border-0 shadow position-fixed bottom-0 end-0"
     role="alert" id="mainToast" style="z-index:9999">
    <div class="toast-header border-0">
        <i class="ti ti-circle-check me-2" id="toastIcon"></i>
        <span class="fw-semibold me-auto" id="toastTitle">Done</span>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body" id="toastBody"></div>
</div>

@push('page-js')
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const tbody      = document.getElementById('userTableBody');
    const loadingRow = document.getElementById('loadingRow');
    const emptyRow   = document.getElementById('emptyRow');
    const toast      = new bootstrap.Toast(document.getElementById('mainToast'), { delay: 3500 });
    const userModal  = new bootstrap.Modal(document.getElementById('userModal'));
    const delModal   = new bootstrap.Modal(document.getElementById('deleteModal'));

    let _deleteId = null;

    // ── helpers ──────────────────────────────────────────
    function esc(s) {
        if (s == null) return '—';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function initials(name) {
        return name.split(' ').slice(0, 2).map(p => p[0]).join('').toUpperCase();
    }
    function avatarColor(name) {
        const colors = ['primary','success','danger','warning','info'];
        let h = 0;
        for (let c of name) h = (h * 31 + c.charCodeAt(0)) & 0xffffffff;
        return colors[Math.abs(h) % colors.length];
    }
    function showToast(msg, type = 'success') {
        const icon  = document.getElementById('toastIcon');
        const title = document.getElementById('toastTitle');
        const body  = document.getElementById('toastBody');
        icon.className  = type === 'success' ? 'ti ti-circle-check me-2 text-success' : 'ti ti-circle-x me-2 text-danger';
        title.textContent = type === 'success' ? 'Success' : 'Error';
        body.textContent  = msg;
        toast.show();
    }
    function clearFormErrors() {
        ['errName','errEmail','errRole','errPassword'].forEach(id => {
            document.getElementById(id).textContent = '';
        });
        ['formName','formEmail','formRole','formPassword'].forEach(id => {
            document.getElementById(id).classList.remove('is-invalid');
        });
    }
    function setFormErrors(errors) {
        const map = { name: 'formName', email: 'formEmail', role: 'formRole', password: 'formPassword' };
        const errMap = { name: 'errName', email: 'errEmail', role: 'errRole', password: 'errPassword' };
        Object.keys(errors).forEach(field => {
            if (map[field]) {
                document.getElementById(map[field]).classList.add('is-invalid');
                document.getElementById(errMap[field]).textContent = errors[field][0];
            }
        });
    }
    function updateStats(users) {
        document.getElementById('statTotal').textContent   = users.length;
        document.getElementById('statAdmins').textContent  = users.filter(u => u.role === 'admin').length;
        document.getElementById('statInterns').textContent = users.filter(u => u.role === 'intern').length;
    }

    // ── load users ───────────────────────────────────────
    async function loadUsers() {
        tbody.querySelectorAll('tr.user-row').forEach(r => r.remove());
        emptyRow.style.display   = 'none';
        loadingRow.style.display = '';

        const params = new URLSearchParams();
        const role   = document.getElementById('filterRole').value;
        const search = document.getElementById('filterSearch').value.trim();
        if (role)   params.set('role',   role);
        if (search) params.set('search', search);

        try {
            const res  = await fetch(`{{ route('admin.users.list') }}?${params}`);
            const data = await res.json();
            updateStats(data.users);
            renderRows(data.users);
        } catch (err) {
            console.error(err);
            loadingRow.style.display = 'none';
        }
    }

    function renderRows(users) {
        loadingRow.style.display = 'none';
        tbody.querySelectorAll('tr.user-row').forEach(r => r.remove());

        if (!users.length) {
            emptyRow.style.display = '';
            return;
        }
        emptyRow.style.display = 'none';

        users.forEach(u => {
            const color   = avatarColor(u.name);
            const roleCls = u.role === 'admin' ? 'role-admin' : 'role-intern';
            const roleLabel = u.role.charAt(0).toUpperCase() + u.role.slice(1);
            const tr = document.createElement('tr');
            tr.className = 'user-row';
            tr.dataset.id = u.id;
            tr.innerHTML = `
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-circle bg-label-${color} ${u.is_self ? 'avatar-self' : ''}">${initials(u.name)}</span>
                        </div>
                        <div>
                            <div class="fw-semibold small">${esc(u.name)}${u.is_self ? ' <span class="badge bg-label-secondary ms-1" style="font-size:.6rem">You</span>' : ''}</div>
                            <div class="text-muted" style="font-size:.7rem">${esc(u.email)}</div>
                        </div>
                    </div>
                </td>
                <td><span class="badge rounded-pill ${roleCls}" style="font-size:.65rem">${roleLabel}</span></td>
                <td class="small text-muted">${esc(u.created_at)}</td>
                <td><span class="badge bg-label-secondary">${u.schedule_count}</span></td>
                <td class="text-end">
                    <button class="btn btn-sm btn-icon btn-text-secondary me-1"
                            title="Edit" onclick="openEditModal(${u.id})">
                        <i class="ti ti-pencil"></i>
                    </button>
                    ${u.is_self ? '' : `
                    <button class="btn btn-sm btn-icon btn-text-danger"
                            title="Delete" onclick="openDeleteModal(${u.id}, '${esc(u.name)}')">
                        <i class="ti ti-trash"></i>
                    </button>`}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // ── create modal ─────────────────────────────────────
    window.openCreateModal = function () {
        document.getElementById('userModalTitle').textContent  = 'Add User';
        document.getElementById('formSubmitLabel').textContent = 'Create User';
        document.getElementById('formUserId').value  = '';
        document.getElementById('formMethod').value  = 'POST';
        document.getElementById('formName').value    = '';
        document.getElementById('formEmail').value   = '';
        document.getElementById('formRole').value    = 'intern';
        document.getElementById('formPassword').value = '';
        document.getElementById('passwordHint').style.display = 'none';
        document.getElementById('formPassword').required = true;
        clearFormErrors();
    };

    // ── edit modal ───────────────────────────────────────
    window.openEditModal = async function (id) {
        try {
            const res  = await fetch(`{{ url('/admin/users') }}/${id}`);
            const user = await res.json();

            document.getElementById('userModalTitle').textContent  = 'Edit User';
            document.getElementById('formSubmitLabel').textContent = 'Save Changes';
            document.getElementById('formUserId').value  = user.id;
            document.getElementById('formMethod').value  = 'PUT';
            document.getElementById('formName').value    = user.name;
            document.getElementById('formEmail').value   = user.email;
            document.getElementById('formRole').value    = user.role;
            document.getElementById('formPassword').value = '';
            document.getElementById('formPassword').required = false;
            document.getElementById('passwordHint').style.display = '';
            clearFormErrors();
            userModal.show();
        } catch (err) {
            showToast('Failed to load user data.', 'error');
        }
    };

    // ── form submit (create / update) ────────────────────
    document.getElementById('userForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        clearFormErrors();

        const id     = document.getElementById('formUserId').value;
        const method = id ? 'PUT' : 'POST';
        const url    = id ? `{{ url('/admin/users') }}/${id}` : `{{ route('admin.users.store') }}`;

        const body = {
            name:     document.getElementById('formName').value,
            email:    document.getElementById('formEmail').value,
            role:     document.getElementById('formRole').value,
            password: document.getElementById('formPassword').value || undefined,
        };

        const spinner = document.getElementById('formSpinner');
        const btn     = document.getElementById('userFormSubmit');
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
                if (res.status === 422) setFormErrors(data.errors ?? {});
                else showToast(data.message ?? 'An error occurred.', 'error');
                return;
            }

            showToast(data.message, 'success');
            userModal.hide();
            loadUsers();
        } catch (err) {
            showToast('Request failed.', 'error');
        } finally {
            spinner.classList.add('d-none');
            btn.disabled = false;
        }
    });

    // ── delete modal ─────────────────────────────────────
    window.openDeleteModal = function (id, name) {
        _deleteId = id;
        document.getElementById('deleteUserName').textContent = name;
        delModal.show();
    };

    document.getElementById('confirmDeleteBtn').addEventListener('click', async function () {
        if (!_deleteId) return;
        const spinner = document.getElementById('deleteSpinner');
        this.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const res  = await fetch(`{{ url('/admin/users') }}/${_deleteId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const data = await res.json();

            if (!res.ok) {
                showToast(data.message ?? 'Delete failed.', 'error');
                return;
            }

            // Animate row out
            const row = tbody.querySelector(`tr.user-row[data-id="${_deleteId}"]`);
            if (row) {
                row.classList.add('removing');
                setTimeout(() => row.remove(), 250);
            }

            delModal.hide();
            showToast(data.message, 'success');
            loadUsers();
        } catch (err) {
            showToast('Request failed.', 'error');
        } finally {
            this.disabled = false;
            spinner.classList.add('d-none');
            _deleteId = null;
        }
    });

    // ── password toggle ──────────────────────────────────
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('formPassword');
        const icon  = document.getElementById('togglePasswordIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('ti-eye', 'ti-eye-off');
        } else {
            input.type = 'password';
            icon.classList.replace('ti-eye-off', 'ti-eye');
        }
    });

    // ── filter events ────────────────────────────────────
    document.getElementById('btnSearch').addEventListener('click', loadUsers);
    document.getElementById('filterSearch').addEventListener('keydown', e => {
        if (e.key === 'Enter') loadUsers();
    });
    document.getElementById('btnReset').addEventListener('click', () => {
        document.getElementById('filterRole').value   = '';
        document.getElementById('filterSearch').value = '';
        loadUsers();
    });

    // Initial load
    loadUsers();
})();
</script>
@endpush
</x-app-layout>
