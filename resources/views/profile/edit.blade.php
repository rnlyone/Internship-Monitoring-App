<x-app-layout>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-align-top">
                    <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-2 gap-lg-0">
                        <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="ti-sm ti ti-users me-1_5"></i> Account</a></li>
                        <li class="nav-item"><a class="nav-link" href="javascript:void(0);"><i class="ti-sm ti ti-lock me-1_5"></i> Security</a></li>
                    </ul>
                </div>

                <!-- Profile Information Card -->
                @include('profile.partials.update-profile-information-form')

                <!-- Change Password Card -->
                @include('profile.partials.update-password-form')

                <!-- Delete Account Card -->
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
