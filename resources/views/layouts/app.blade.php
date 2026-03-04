<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#7367f0" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'Log Mieru') }}" />
    <meta name="description" content="Internship monitoring and logbook management system" />
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png" />
    <meta name="msapplication-TileImage" content="/icons/icon-144x144.png" />
    <meta name="msapplication-TileColor" content="#7367f0" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />

    <!-- Page CSS -->
    @stack('page-css')

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="{{ route('dashboard') }}" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="#7367F0" />
                                <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                                <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="#7367F0" />
                            </svg>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                        <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
                        <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboards -->
                    <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-smart-home"></i>
                            <div data-i18n="Dashboard">Dashboard</div>
                        </a>
                    </li>

                    <!-- Apps & Pages -->
                    <li class="menu-header small">
                        <span class="menu-header-text" data-i18n="Apps & Pages">Apps &amp; Pages</span>
                    </li>

                    <li class="menu-item {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
                        <a href="{{ route('schedules.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-calendar"></i>
                            <div data-i18n="Schedules">Schedules</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('kanban.*') ? 'active' : '' }}">
                        <a href="{{ route('kanban.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-layout-kanban"></i>
                            <div data-i18n="Kanban">Kanban Board</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <a href="{{ route('profile.edit') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-user"></i>
                            <div data-i18n="Account Settings">Account Settings</div>
                        </a>
                    </li>

                    @if(Auth::user()->isAdmin())
                    <li class="menu-header small">
                        <span class="menu-header-text" data-i18n="Administration">Administration</span>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.approvals.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-calendar-check"></i>
                            <div data-i18n="Approvals">Schedule Approvals</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.schedule-assign.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.schedule-assign.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-calendar-plus"></i>
                            <div data-i18n="Assign Schedule">Assign Schedules</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.logbooks.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.logbooks.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-notebook"></i>
                            <div data-i18n="Logbook Review">Logbook Review</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.reports.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-report"></i>
                            <div data-i18n="Internship Reports">Internship Reports</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.performance.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.performance.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-chart-bar"></i>
                            <div data-i18n="Performance Monitor">Performance Monitor</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-users"></i>
                            <div data-i18n="User Management">User Management</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <a href="{{ route('settings.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons ti ti-settings"></i>
                            <div data-i18n="Settings">Settings</div>
                        </a>
                    </li>
                    @endif
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">

                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="ti ti-menu-2 ti-md"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item navbar-search-wrapper mb-0">
                                <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                                    <i class="ti ti-search ti-md me-2 me-lg-4 ti-lg"></i>
                                    <span class="d-none d-md-inline-block text-muted fw-normal">Search (Ctrl+/)</span>
                                </a>
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Live Clock (UTC+8) -->
                            <li class="nav-item me-2 me-lg-3 d-none d-md-flex align-items-center">
                                <div class="d-flex flex-column align-items-end lh-1">
                                    <span class="fw-semibold text-heading" id="navClock" style="font-size:.95rem;letter-spacing:.5px;">--:--:--</span>
                                    <small class="text-muted" style="font-size:.7rem;">UTC+8</small>
                                </div>
                            </li>
                            <!-- / Live Clock -->

                            <!-- Notifications -->
                            <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-1">
                                <a class="nav-link btn btn-text-secondary btn-icon rounded-pill position-relative dropdown-toggle hide-arrow"
                                   href="javascript:void(0);"
                                   data-bs-toggle="dropdown"
                                   data-bs-auto-close="outside"
                                   aria-expanded="false"
                                   id="notifDropdownToggle">
                                    <i class="ti ti-bell ti-md"></i>
                                    <span id="notifBadge"
                                          class="badge bg-danger rounded-pill position-absolute"
                                          style="font-size:.58rem;padding:1px 4px;min-width:16px;top:4px;right:4px;display:none">0</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-0 shadow" style="min-width:340px;max-width:380px">
                                    <li class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                                        <h6 class="mb-0 fw-semibold">Notifications</h6>
                                        <button type="button" class="btn btn-sm btn-text-secondary p-0" id="markAllReadBtn" style="font-size:.75rem">
                                            <i class="ti ti-checks ti-sm me-1"></i>Mark all read
                                        </button>
                                    </li>
                                    <li>
                                        <div id="notifList" style="max-height:340px;overflow-y:auto">
                                            <div class="text-center py-5 text-muted" id="notifEmpty">
                                                <i class="ti ti-bell-off ti-24px d-block mb-1"></i>
                                                <small>No notifications</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!-- / Notifications -->



                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                        </span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item mt-0" href="{{ route('profile.edit') }}">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <div class="avatar avatar-online">
                                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                                                    <small class="text-muted">{{ Auth::user()->email }}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1 mx-n2"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="ti ti-user me-3 ti-md"></i><span class="align-middle">My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="ti ti-settings me-3 ti-md"></i><span class="align-middle">Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1 mx-n2"></div>
                                    </li>
                                    <li>
                                        <div class="d-grid px-2 pt-2 pb-1">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger d-flex w-100">
                                                    <small class="align-middle">Logout</small>
                                                    <i class="ti ti-logout ms-2 ti-14px"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>

                    <!-- Search Small Screens -->
                    <div class="navbar-search-wrapper search-input-wrapper d-none">
                        <input type="text" class="form-control search-input container-xxl border-0" placeholder="Search..." aria-label="Search...">
                        <i class="ti ti-x search-toggler cursor-pointer"></i>
                    </div>
                </nav>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        {{ $slot }}
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <div class="text-body">
                                    © {{ date('Y') }}, {{ config('app.name', 'Laravel') }}
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>

        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    @stack('page-js')

    <!-- Live Clock -->
    <script>
    (function () {
        function updateClock() {
            const el = document.getElementById('navClock');
            if (!el) return;
            el.textContent = new Intl.DateTimeFormat('en-GB', {
                timeZone: 'Asia/Singapore',
                hour: '2-digit', minute: '2-digit', second: '2-digit',
                hour12: false
            }).format(new Date());
        }
        updateClock();
        setInterval(updateClock, 1000);
    })();
    </script>

    <!-- Notifications JS -->
    <script>
    (function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const typeMap = {
            schedule_submitted : { icon: 'ti-calendar-plus',  color: 'warning'   },
            schedule_approved  : { icon: 'ti-circle-check',   color: 'success'   },
            schedule_rejected  : { icon: 'ti-circle-x',       color: 'danger'    },
            schedule_assigned  : { icon: 'ti-calendar-event', color: 'info'      },
            kanban_assigned    : { icon: 'ti-clipboard-check', color: 'info'     },
            kanban_done        : { icon: 'ti-checklist',       color: 'success'  },
        };

        function esc(str) {
            return String(str)
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function loadNotifications() {
            fetch('/notifications', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.ok ? r.json() : null)
                .then(data => {
                    if (!data) return;

                    const badge  = document.getElementById('notifBadge');
                    const list   = document.getElementById('notifList');

                    // Update badge
                    if (data.unread > 0) {
                        badge.textContent  = data.unread > 9 ? '9+' : data.unread;
                        badge.style.display = '';
                    } else {
                        badge.style.display = 'none';
                    }

                    // Render list
                    if (!data.notifications || data.notifications.length === 0) {
                        list.innerHTML = `<div class="text-center py-5 text-muted"><i class="ti ti-bell-off ti-24px d-block mb-1"></i><small>No notifications</small></div>`;
                        return;
                    }

                    list.innerHTML = data.notifications.map(n => {
                        const meta = typeMap[n.type] ?? { icon: 'ti-bell', color: 'secondary' };
                        const url  = (n.url && n.url !== '') ? esc(n.url) : '#';
                        return `<a href="${url}"
                                   class="dropdown-item d-flex align-items-start gap-3 py-3 px-4 border-bottom ${n.read ? '' : 'bg-lighter'}"
                                   onclick="notifMarkRead(event, ${n.id}, '${url}')">
                            <div class="avatar avatar-sm flex-shrink-0">
                                <span class="avatar-initial rounded-circle bg-label-${meta.color}">
                                    <i class="ti ${meta.icon} ti-sm"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-semibold" style="font-size:.82rem">${esc(n.title)}</div>
                                <div class="text-muted text-truncate" style="font-size:.75rem">${esc(n.message)}</div>
                                <div class="text-muted" style="font-size:.68rem">${esc(n.created_at)}</div>
                            </div>
                            ${!n.read ? '<span class="badge bg-primary rounded-pill flex-shrink-0 mt-1" style="width:8px;height:8px;padding:0"></span>' : ''}
                        </a>`;
                    }).join('');
                })
                .catch(() => {});
        }

        window.notifMarkRead = function (e, id, url) {
            e.preventDefault();
            fetch(`/notifications/${id}/read`, {
                method : 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            }).finally(() => {
                loadNotifications();
                if (url && url !== '#') window.location.href = url;
            });
        };

        document.getElementById('markAllReadBtn')?.addEventListener('click', function () {
            fetch('/notifications/read-all', {
                method : 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            }).then(() => loadNotifications());
        });

        document.getElementById('notifDropdownToggle')?.addEventListener('show.bs.dropdown', loadNotifications);

        // Initial load + poll every 30 s
        loadNotifications();
        setInterval(loadNotifications, 30000);
    })();
    </script>

    <!-- PWA Service Worker -->
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then(reg => {
                    // Check for updates every 60 s
                    setInterval(() => reg.update(), 60000);
                })
                .catch(err => console.warn('SW registration failed:', err));
        });

        // Prompt user when a new SW version is waiting
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (window._swUpdating) return;
            window._swUpdating = true;
            const toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);background:#7367f0;color:#fff;padding:.75rem 1.5rem;border-radius:.5rem;z-index:99999;box-shadow:0 4px 20px rgba(0,0,0,.25);font-family:sans-serif;cursor:pointer;';
            toast.innerHTML = '🔄 App updated — <strong>tap to reload</strong>';
            toast.onclick = () => window.location.reload();
            document.body.appendChild(toast);
        });
    }
    </script>
</body>

</html>
