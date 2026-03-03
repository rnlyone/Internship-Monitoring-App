<div class="card">
    <h5 class="card-header">{{ __('Delete Account') }}</h5>
    <div class="card-body">
        <div class="mb-6 col-12 mb-0">
            <div class="alert alert-warning">
                <h5 class="alert-heading mb-1">{{ __('Are you sure you want to delete your account?') }}</h5>
                <p class="mb-0">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
            </div>
        </div>

        <button type="button" class="btn btn-danger mt-4" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
            {{ __('Delete Account') }}
        </button>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true" @if($errors->userDeletion->isNotEmpty()) data-bs-show="true" @endif>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Are you sure you want to delete your account?') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-4">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>

                    <div class="form-password-toggle">
                        <label class="form-label" for="delete_password">{{ __('Password') }}</label>
                        <div class="input-group input-group-merge">
                            <input type="password" class="form-control @if($errors->userDeletion->has('password')) is-invalid @endif" id="delete_password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
                        </div>
                        @if($errors->userDeletion->has('password'))
                            <span class="invalid-feedback d-block">{{ $errors->userDeletion->first('password') }}</span>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
@push('page-js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        modal.show();
    });
</script>
@endpush
@endif
