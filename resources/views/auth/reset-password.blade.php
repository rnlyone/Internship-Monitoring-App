<x-guest-layout>
    <div class="authentication-wrapper authentication-cover">
        <!-- Logo -->
        <a href="/" class="app-brand auth-cover-brand">
            <span class="app-brand-logo demo">
                <svg width="28" height="22" viewBox="0 0 523.096 401.249" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M55.4517 16.0557C25.0817 16.0557 0.461914 40.8559 0.461914 71.4485C0.461914 253.205 146.732 400.547 327.166 400.547C357.536 400.547 382.155 375.747 382.155 345.154C382.155 314.562 357.536 289.761 327.166 289.761C207.472 289.761 110.441 192.02 110.441 71.4485C110.441 40.8559 85.8216 16.0557 55.4517 16.0557Z" fill="#7367F0"/>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M418.67 55.6451C441.252 62.5899 458.862 82.4786 455.346 105.998C454.326 112.823 452.797 119.657 450.736 126.456C428.037 201.354 349.36 243.535 275.007 220.668C200.654 197.803 158.78 118.549 181.48 43.651C183.54 36.8524 186.063 30.3233 189 24.088C199.123 2.60107 224.759 -3.9887 247.341 2.95602L418.67 55.6451Z" fill="#7367F0"/>
                    <path d="M412.191 265.091C412.191 295.94 437.018 320.949 467.643 320.949C498.268 320.949 523.095 295.94 523.095 265.091C523.095 234.241 498.268 209.232 467.643 209.232C437.018 209.232 412.191 234.241 412.191 265.091Z" fill="#7367F0"/>
                </svg>
            </span>
            <span class="app-brand-text demo text-heading fw-bold">{{ config('app.name', 'Laravel') }}</span>
        </a>
        <!-- /Logo -->
        <div class="authentication-inner row m-0">
            <!-- /Left Text -->
            <div class="d-none d-lg-flex col-lg-8 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/illustrations/auth-reset-password-illustration-light.png') }}" alt="auth-reset-password-cover" class="my-5 auth-illustration" data-app-light-img="illustrations/auth-reset-password-illustration-light.png" data-app-dark-img="illustrations/auth-reset-password-illustration-dark.png">
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" alt="auth-reset-password-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
                </div>
            </div>
            <!-- /Left Text -->

            <!-- Reset Password -->
            <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-6 p-sm-12">
                <div class="w-px-400 mx-auto mt-12 pt-5">
                    <h4 class="mb-1">Reset Password 🔒</h4>
                    <p class="mb-6"><span class="fw-medium">Your new password must be different from previously used passwords</span></p>

                    <form id="formAuthentication" class="mb-6" action="{{ route('password.store') }}" method="POST">
                        @csrf
                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="mb-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $request->email) }}" placeholder="Enter your email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6 form-password-toggle">
                            <label class="form-label" for="password">New Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6 form-password-toggle">
                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                            </div>
                        </div>
                        <button class="btn btn-primary d-grid w-100 mb-6">Set new password</button>
                        <div class="text-center">
                            <a href="{{ route('login') }}">
                                <i class="ti ti-chevron-left scaleX-n1-rtl me-1_5"></i>
                                Back to login
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Reset Password -->
        </div>
    </div>
</x-guest-layout>
