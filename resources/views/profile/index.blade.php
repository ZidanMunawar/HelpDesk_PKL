@extends('layouts.main')

@section('title', 'My Profile | ' . config('app.name'))

@section('page-title', 'My Profile')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Profile', 'url' => 'javascript:void(0)'],
            ['title' => 'My Profile', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        .cover-photo {
            background: linear-gradient(135deg, var(--primary) 0%, #6610f2 100%);
            height: 200px;
            border-radius: 1rem 1rem 0 0;
        }

        .profile-head {
            position: relative;
        }

        .profile-info {
            position: relative;
            margin-top: -80px;
            display: flex;
            align-items: flex-end;
            padding: 0 30px 20px;
        }

        .profile-photo {
            position: relative;
            margin-right: 20px;
        }

        .profile-photo img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 5px solid #ffffff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .change-photo-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: var(--primary);
            color: rgb(255, 136, 0);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 3px solid #fe7f00;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .change-photo-btn:hover {
            /* background: #ffffff; */
            transform: scale(1.1);
        }

        .change-photo-btn i {
            font-size: 14px;
        }

        .profile-details {
            display: flex;
            align-items: flex-end;
            flex: 1;
            gap: 30px;
        }

        .profile-name {
            padding: 0 !important;
        }

        .profile-name h4 {
            margin-bottom: 5px;
            font-size: 24px;
            font-weight: 700;
        }

        .profile-name p {
            margin: 0;
        }

        .profile-email {
            padding: 0 !important;
        }

        .profile-email h4 {
            margin-bottom: 5px;
            font-size: 16px;
        }

        .profile-email p {
            margin: 0;
            font-size: 13px;
            color: #949494;
        }

        .badge-role {
            font-size: 12px;
            padding: 6px 12px;
            font-weight: 500;
        }

        #removePhotoBtn {
            margin-left: auto;
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
        }

        .nav-tabs .nav-link {
            color: #ffffff;
            font-weight: 500;
            border: none;
            padding: 15px 25px;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
            background: transparent;
        }

        .form-group label {
            font-weight: 500;
            color: #636262;
            margin-bottom: 8px;
        }

        .profile-stats {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .profile-stats .stat-item {
            text-align: center;
            padding: 10px;
        }

        .profile-stats .stat-item h3 {
            margin: 0;
            color: var(--primary);
            font-weight: 700;
        }

        .profile-stats .stat-item span {
            color: #6e6e6e;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .profile-info {
                flex-direction: column;
                align-items: center;
                text-align: center;
                margin-top: -60px;
            }

            .profile-photo {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .profile-details {
                flex-direction: column;
                gap: 15px;
                width: 100%;
            }

            #removePhotoBtn {
                margin: 10px auto 0;
            }
        }
    </style>
@endpush


@section('content')
    <!-- Profile Header -->
    <div class="row">
        <div class="col-lg-12">
            <div class="profile card card-body px-0 pt-0 pb-0">
                <div class="profile-head">
                    <div class="photo-content">
                        <div class="cover-photo"></div>
                    </div>
                    <div class="profile-info">
                        <div class="profile-photo">
                            <img src="{{ auth()->user()->profile_picture_url }}" alt="{{ auth()->user()->name }}"
                                class="rounded-circle">

                            <label for="profilePictureInput" class="change-photo-btn" title="Change Photo">
                                <i class="fa fa-camera"></i>
                            </label>
                            <input type="file" id="profilePictureInput" accept="image/*" style="display: none;">
                        </div>
                        <div class="profile-details">
                            <div class="profile-name">
                                <h4 class="text-primary mb-0">{{ $user->name }}</h4>
                                <p>
                                    <span class="badge badge-role badge-{{ $user->role === 'admin' ? 'primary' : 'info' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </p>
                            </div>
                            <div class="profile-email">
                                <h4 class="text-primary mb-0">{{ $user->email }}</h4>
                                <p>Email Address</p>
                            </div>
                            @if ($user->profile_picture)
                                <button class="btn btn-danger light sharp" id="removePhotoBtn" title="Remove Photo">
                                    <i class="fa fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Profile Content -->
    <div class="row">
        <div class="col-xl-4">
            <div class="card h-auto">
                <div class="card-body">
                    <!-- Profile Stats -->
                    <div class="profile-stats">
                        <div class="row">
                            <div class="col-4 stat-item">
                                <h3 class="mb-0">{{ $user->created_at->diffInDays(now()) }}</h3>
                                <span>Days Active</span>
                            </div>
                            <div class="col-4 stat-item">
                                <h3 class="mb-0">0</h3>
                                <span>Tickets</span>
                            </div>
                            <div class="col-4 stat-item">
                                <h3 class="mb-0">0</h3>
                                <span>Resolved</span>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="profile-personal-info">
                        <h4 class="text-primary mb-4">Personal Information</h4>
                        <div class="row mb-2">
                            <div class="col-5">
                                <h5 class="f-w-500">Name <span class="pull-right">:</span></h5>
                            </div>
                            <div class="col-7"><span>{{ $user->name }}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5">
                                <h5 class="f-w-500">Email <span class="pull-right">:</span></h5>
                            </div>
                            <div class="col-7"><span>{{ $user->email }}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5">
                                <h5 class="f-w-500">Phone <span class="pull-right">:</span></h5>
                            </div>
                            <div class="col-7"><span>{{ $user->phone ?? '-' }}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5">
                                <h5 class="f-w-500">Role <span class="pull-right">:</span></h5>
                            </div>
                            <div class="col-7">
                                <span class="badge badge-{{ $user->role === 'admin' ? 'primary' : 'info' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5">
                                <h5 class="f-w-500">Status <span class="pull-right">:</span></h5>
                            </div>
                            <div class="col-7">
                                <span
                                    class="badge badge-{{ $user->status === 'active' ? 'success' : ($user->status === 'inactive' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5">
                                <h5 class="f-w-500">Joined <span class="pull-right">:</span></h5>
                            </div>
                            <div class="col-7"><span>{{ $user->created_at->format('d M Y') }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card h-auto">
                <div class="card-body">
                    <div class="profile-tab">
                        <div class="custom-tab-1">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a href="#edit-profile" data-bs-toggle="tab" class="nav-link active show">
                                        <i class="fa fa-user me-2"></i>Edit Profile
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#change-password" data-bs-toggle="tab" class="nav-link">
                                        <i class="fa fa-lock me-2"></i>Change Password
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <!-- Edit Profile Tab -->
                                <div id="edit-profile" class="tab-pane fade active show">
                                    <div class="pt-4">
                                        <div class="settings-form">
                                            <h4 class="text-primary mb-4">Profile Information</h4>
                                            <form id="updateProfileForm">
                                                @csrf
                                                <div class="row">
                                                    <div class="form-group col-md-6 mb-3">
                                                        <label>Full Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control"
                                                            value="{{ $user->name }}" required>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                    <div class="form-group col-md-6 mb-3">
                                                        <label>Email <span class="text-danger">*</span></label>
                                                        <input type="email" name="email" class="form-control"
                                                            value="{{ $user->email }}" required>
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group col-md-6 mb-3">
                                                        <label>Phone Number</label>
                                                        <input type="text" name="phone" class="form-control"
                                                            value="{{ $user->phone }}" placeholder="e.g., +62812345678">
                                                        <div class="invalid-feedback"></div>
                                                    </div>
                                                    <div class="form-group col-md-6 mb-3">
                                                        <label>Role</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ ucfirst($user->role) }}" readonly disabled>
                                                        <small class="text-muted">Role cannot be changed</small>
                                                    </div>
                                                </div>
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fa fa-save me-2"></i>Update Profile
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Change Password Tab -->
                                <div id="change-password" class="tab-pane fade">
                                    <div class="pt-4">
                                        <div class="settings-form">
                                            <h4 class="text-primary mb-4">Change Password</h4>
                                            <form id="changePasswordForm">
                                                @csrf
                                                <div class="form-group mb-3">
                                                    <label>Current Password <span class="text-danger">*</span></label>
                                                    <input type="password" name="current_password" class="form-control"
                                                        placeholder="Enter current password" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>New Password <span class="text-danger">*</span></label>
                                                    <input type="password" name="new_password" class="form-control"
                                                        placeholder="Enter new password (min. 8 characters)" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label>Confirm New Password <span class="text-danger">*</span></label>
                                                    <input type="password" name="new_password_confirmation"
                                                        class="form-control" placeholder="Confirm new password" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fa fa-lock me-2"></i>Change Password
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "5000",
                "closeButton": true,
                "progressBar": true
            };

            // Change Profile Picture (SEPARATE AJAX)
            $('#profilePictureInput').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    if (!file.type.match('image.*')) {
                        toastr.error('Please select an image file');
                        $(this).val('');
                        return;
                    }

                    // Validate file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        toastr.error('Image size must be less than 2MB');
                        $(this).val('');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('profile_picture', file);
                    formData.append('_token', '{{ csrf_token() }}');

                    // Show loading
                    Swal.fire({
                        title: 'Uploading...',
                        text: 'Please wait while we upload your photo',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: "{{ route('profile.upload-picture') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                // Update image preview
                                $('#profileImage').attr('src', response.data
                                    .profile_picture_url);
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.close();
                            $('#profilePictureInput').val('');

                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                const errorMsg = errors.profile_picture ? errors
                                    .profile_picture[0] : 'Validation error';
                                toastr.error(errorMsg);
                            } else {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to upload photo');
                            }
                        }
                    });
                }
            });

            // Remove Profile Picture
            $('#removePhotoBtn').on('click', function() {
                Swal.fire({
                    title: 'Remove Photo?',
                    text: 'Are you sure you want to remove your profile picture?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('profile.remove-picture') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Removed!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to remove photo');
                            }
                        });
                    }
                });
            });

            // Update Profile Form (WITHOUT PHOTO)
            $('#updateProfileForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize(); // Use serialize instead of FormData
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-2"></i>Updating...');

                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('profile.update') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(
                            '<i class="fa fa-save me-2"></i>Update Profile');

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(
                            '<i class="fa fa-save me-2"></i>Update Profile');

                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('[name="' + key + '"]').addClass('is-invalid')
                                    .siblings('.invalid-feedback').text(value[0]);
                            });
                            toastr.error('Please check the form for errors');
                        } else {
                            toastr.error(xhr.responseJSON?.message ||
                                'Failed to update profile');
                        }
                    }
                });
            });

            // Change Password Form
            $('#changePasswordForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-2"></i>Changing...');

                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('profile.update-password') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#changePasswordForm')[0].reset();
                        submitBtn.prop('disabled', false).html(
                            '<i class="fa fa-lock me-2"></i>Change Password');

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(
                            '<i class="fa fa-lock me-2"></i>Change Password');

                        if (xhr.status === 422) {
                            if (xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    $('[name="' + key + '"]').addClass('is-invalid')
                                        .siblings('.invalid-feedback').text(value[0]);
                                });
                            } else {
                                toastr.error(xhr.responseJSON.message);
                            }
                        } else {
                            toastr.error('Failed to change password');
                        }
                    }
                });
            });
        });
    </script>
@endpush
