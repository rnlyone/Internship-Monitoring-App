<div class="card mb-6">
    <!-- Account -->
    <div class="card-body">
        <div class="d-flex align-items-start align-items-sm-center gap-6">
            <div class="avatar avatar-xl">
                <span class="avatar-initial rounded bg-label-primary">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
            </div>
            <div>
                <h5 class="mb-0">{{ $user->name }}</h5>
                <span class="text-muted">{{ $user->email }}</span>
            </div>
        </div>
    </div>
    <div class="card-body pt-4">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="row">
                <div class="mb-4 col-md-6">
                    <label for="name" class="form-label">{{ __('Name') }}</label>
                    <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4 col-md-6">
                    <label for="email" class="form-label">{{ __('E-mail') }}</label>
                    <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <div class="mt-2">
                            <div class="alert alert-warning py-2 mb-0">
                                <small>
                                    {{ __('Your email address is unverified.') }}
                                    <button form="send-verification" class="btn btn-link btn-sm p-0 align-baseline">
                                        {{ __('Click here to re-send the verification email.') }}
                                    </button>
                                </small>
                            </div>

                            @if (session('status') === 'verification-link-sent')
                                <div class="alert alert-success py-2 mt-2 mb-0">
                                    <small>{{ __('A new verification link has been sent to your email address.') }}</small>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-primary me-3">{{ __('Save changes') }}</button>

                @if (session('status') === 'profile-updated')
                    <span class="text-success"><i class="ti ti-check me-1"></i>{{ __('Saved.') }}</span>
                @endif
            </div>
        </form>
    </div>
    <!-- /Account -->
</div>
