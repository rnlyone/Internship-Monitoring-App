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
                    <img src="{{ asset('assets/img/illustrations/auth-verify-email-illustration-light.png') }}" alt="auth-verify-email-cover" class="my-5 auth-illustration" data-app-light-img="illustrations/auth-verify-email-illustration-light.png" data-app-dark-img="illustrations/auth-verify-email-illustration-dark.png">
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" alt="auth-verify-email-cover" class="platform-bg" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
                </div>
            </div>
            <!-- /Left Text -->

            <!-- Verify email -->
            <div class="d-flex col-12 col-lg-4 align-items-center authentication-bg p-6 p-sm-12">
                <div class="w-px-400 mx-auto mt-12 mt-5">
                    <h4 class="mb-1">Verify your email ✉️</h4>
                    <p class="text-start mb-0">
                        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                    </p>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success mt-4" role="alert">
                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 my-6">
                            {{ __('Resend Verification Email') }}
                        </button>
                    </form>

                    <div class="text-center">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-text-secondary">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- / Verify email -->
        </div>
    </div>
</x-guest-layout>
