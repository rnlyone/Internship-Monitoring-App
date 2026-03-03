<!-- Change Password -->
<div class="card mb-6">
    <h5 class="card-header">{{ __('Change Password') }}</h5>
    <div class="card-body pt-1">
        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="row">
                <div class="mb-6 col-md-6 form-password-toggle">
                    <label class="form-label" for="update_password_current_password">{{ __('Current Password') }}</label>
                    <div class="input-group input-group-merge">
                        <input class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif" type="password" name="current_password" id="update_password_current_password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" autocomplete="current-password" />
                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                    </div>
                    @if($errors->updatePassword->has('current_password'))
                        <span class="invalid-feedback d-block">{{ $errors->updatePassword->first('current_password') }}</span>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="mb-6 col-md-6 form-password-toggle">
                    <label class="form-label" for="update_password_password">{{ __('New Password') }}</label>
                    <div class="input-group input-group-merge">
                        <input class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif" type="password" id="update_password_password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" autocomplete="new-password" />
                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                    </div>
                    @if($errors->updatePassword->has('password'))
                        <span class="invalid-feedback d-block">{{ $errors->updatePassword->first('password') }}</span>
                    @endif
                </div>

                <div class="mb-6 col-md-6 form-password-toggle">
                    <label class="form-label" for="update_password_password_confirmation">{{ __('Confirm New Password') }}</label>
                    <div class="input-group input-group-merge">
                        <input class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif" type="password" name="password_confirmation" id="update_password_password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" autocomplete="new-password" />
                        <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                    </div>
                    @if($errors->updatePassword->has('password_confirmation'))
                        <span class="invalid-feedback d-block">{{ $errors->updatePassword->first('password_confirmation') }}</span>
                    @endif
                </div>
            </div>
            <h6 class="text-body">{{ __('Password Requirements:') }}</h6>
            <ul class="ps-4 mb-0">
                <li class="mb-4">Minimum 8 characters long - the more, the better</li>
                <li class="mb-4">At least one lowercase character</li>
                <li>At least one number, symbol, or whitespace character</li>
            </ul>
            <div class="mt-6">
                <button type="submit" class="btn btn-primary me-3">{{ __('Save changes') }}</button>

                @if (session('status') === 'password-updated')
                    <span class="text-success"><i class="ti ti-check me-1"></i>{{ __('Saved.') }}</span>
                @endif
            </div>
        </form>
    </div>
</div>
<!--/ Change Password -->
