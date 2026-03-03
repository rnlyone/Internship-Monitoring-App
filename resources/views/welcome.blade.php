<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-wide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets/') }}/" data-template="vertical-menu-template" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Navbar -->
    <nav class="layout-navbar shadow-none py-0">
        <div class="container">
            <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-8">
                <div class="navbar-brand app-brand demo d-flex py-0 me-4 me-xl-8">
                    <a href="/" class="app-brand-link gap-2">
                        <span class="app-brand-logo demo">
                            <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="#7367F0" />
                                <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                                <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="#7367F0" />
                            </svg>
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold ms-1">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                </div>
                <div class="landing-menu-overlay d-lg-none"></div>
                @if (Route::has('login'))
                    <ul class="navbar-nav flex-row align-items-center ms-auto">
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                                    <span class="tf-icons ti ti-layout-dashboard me-md-1"></span>
                                    <span class="d-none d-md-block">Dashboard</span>
                                </a>
                            </li>
                        @else
                            <li class="nav-item me-2">
                                <a href="{{ route('login') }}" class="btn btn-label-primary">
                                    <span class="tf-icons ti ti-login me-md-1"></span>
                                    <span class="d-none d-md-block">Login</span>
                                </a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-primary">
                                        <span class="tf-icons ti ti-user-plus me-md-1"></span>
                                        <span class="d-none d-md-block">Register</span>
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>
                @endif
            </div>
        </div>
    </nav>
    <!-- / Navbar -->

    <!-- Hero Section -->
    <section class="section-py first-section-pt" style="padding-top: 100px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-lg-start mb-5 mb-lg-0">
                    <h1 class="display-5 fw-bold mb-4 text-primary">{{ config('app.name', 'Laravel') }}</h1>
                    <p class="lead mb-5 text-body">Welcome to {{ config('app.name', 'Laravel') }}. A modern, powerful application built with Laravel and styled with the Vuexy Admin Template.</p>
                    <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg"><i class="ti ti-layout-dashboard me-2"></i>Go to Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg"><i class="ti ti-login me-2"></i>Get Started</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-label-primary btn-lg"><i class="ti ti-user-plus me-2"></i>Register</a>
                            @endif
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="{{ asset('assets/img/illustrations/page-misc-launching-soon.png') }}" alt="hero-illustration" class="img-fluid" style="max-height: 400px;">
                </div>
            </div>
        </div>
    </section>
    <!-- / Hero Section -->

    <!-- Features Section -->
    <section class="section-py bg-body" style="padding-top: 60px; padding-bottom: 60px;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="h3 fw-bold">Everything you need</h2>
                <p class="text-body">Powerful features to manage your workflow efficiently</p>
            </div>
            <div class="row g-6">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border shadow-none">
                        <div class="card-body text-center">
                            <div class="avatar avatar-lg mx-auto mb-4">
                                <span class="avatar-initial rounded bg-label-primary"><i class="ti ti-shield-lock ti-28px"></i></span>
                            </div>
                            <h5 class="card-title">Secure Authentication</h5>
                            <p class="card-text">Built-in authentication system with login, registration, password reset and email verification.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border shadow-none">
                        <div class="card-body text-center">
                            <div class="avatar avatar-lg mx-auto mb-4">
                                <span class="avatar-initial rounded bg-label-success"><i class="ti ti-chart-bar ti-28px"></i></span>
                            </div>
                            <h5 class="card-title">Rich Dashboard</h5>
                            <p class="card-text">Beautiful analytics dashboard with cards, charts, and data visualizations powered by Vuexy.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border shadow-none">
                        <div class="card-body text-center">
                            <div class="avatar avatar-lg mx-auto mb-4">
                                <span class="avatar-initial rounded bg-label-info"><i class="ti ti-settings ti-28px"></i></span>
                            </div>
                            <h5 class="card-title">Account Management</h5>
                            <p class="card-text">Full profile management with account settings, password updates, and account deletion.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- / Features Section -->

    <!-- Footer -->
    <footer class="landing-footer bg-body">
        <div class="footer-bottom py-4">
            <div class="container d-flex flex-wrap justify-content-between flex-md-row flex-column text-center text-md-start">
                <div class="text-body mb-2 mb-md-0">&copy; <script>document.write(new Date().getFullYear())</script> {{ config('app.name', 'Laravel') }}. All rights reserved.</div>
            </div>
        </div>
    </footer>
    <!-- / Footer -->

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
