{{-- resources/views/auth/profile-reset-password.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | {{ config('app.name') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <!-- Styles -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        .authincation-content {
            background-color: #ffffff !important;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        /* Input groups */
        .input-group .form-control,
        .input-group .input-group-text {
            border: 1px solid #ced4da !important;
        }

        .input-group .form-control:focus {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        .input-group .input-group-text {
            background-color: #f8f9fa;
        }

        /* Password strength indicator */
        .password-strength {
            display: flex;
            gap: 5px;
            margin-top: 8px;
        }

        .strength-bar {
            height: 5px;
            flex: 1;
            background-color: #e9ecef;
            border-radius: 3px;
            transition: all 0.3s;
        }

        .strength-bar.weak {
            background-color: #dc3545;
        }

        .strength-bar.medium {
            background-color: #ffc107;
        }

        .strength-bar.strong {
            background-color: #28a745;
        }

        /* Password requirements */
        .password-requirement {
            font-size: 12px;
            margin-bottom: 4px;
            color: #6c757d;
        }

        .password-requirement.valid {
            color: #28a745;
        }

        .password-requirement.invalid {
            color: #dc3545;
        }

        .password-requirement i {
            width: 16px;
            margin-right: 6px;
        }

        /* Info alert */
        .alert-info-custom {
            background: #e6f0ff;
            border-left: 4px solid #003366;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-info-custom i {
            color: #ff6600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .authincation-content {
                margin: 15px;
            }
        }
    </style>
</head>

<body class="h-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <div class="text-center mb-3">
                                        <a href="{{ route('login') }}">
                                            <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Logo"
                                                style="width: 180px; height: auto;">
                                        </a>
                                    </div>

                                    <h4 class="text-center mb-2 text-black">Reset Password</h4>

                                    <!-- Info Alert -->
                                    <div class="alert-info-custom d-flex align-items-start">
                                        <i class="fas fa-shield-alt me-3 mt-1"></i>
                                        <div>
                                            <strong>Email:</strong> {{ $email }}<br>
                                            <span class="small">This reset link will expire in <strong>60
                                                    minutes</strong>.</span>
                                        </div>
                                    </div>

                                    <!-- Reset Password Form -->
                                    <form id="resetPasswordForm" method="POST">
                                        @csrf
                                        <input type="hidden" name="token" id="token" value="{{ $token }}">
                                        <input type="hidden" name="email" id="email"
                                            value="{{ $email }}">

                                        <!-- New Password -->
                                        <div class="form-group">
                                            <label class="mb-1 text-black"><strong>New Password</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-lock text-muted"></i>
                                                </span>
                                                <input type="password" id="password" name="password"
                                                    class="form-control border-start-0 border-end-0"
                                                    placeholder="Enter new password" required>
                                                <button class="btn btn-outline-secondary border-start-0" type="button"
                                                    id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="form-group mt-3">
                                            <label class="mb-1 text-black"><strong>Confirm Password</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-lock text-muted"></i>
                                                </span>
                                                <input type="password" id="password_confirmation"
                                                    name="password_confirmation"
                                                    class="form-control border-start-0 border-end-0"
                                                    placeholder="Confirm new password" required>
                                                <button class="btn btn-outline-secondary border-start-0" type="button"
                                                    id="toggleConfirmPassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <!-- Password Strength Indicator -->
                                        <div class="password-strength mb-2">
                                            <div class="strength-bar" id="strength-bar-1"></div>
                                            <div class="strength-bar" id="strength-bar-2"></div>
                                            <div class="strength-bar" id="strength-bar-3"></div>
                                        </div>

                                        <!-- Password Requirements -->
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <h6 class="small fw-bold mb-2">Password Requirements:</h6>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="password-requirement" id="req-length">
                                                        <i class="fas fa-circle"></i> Min 8 characters
                                                    </div>
                                                    <div class="password-requirement" id="req-lowercase">
                                                        <i class="fas fa-circle"></i> Lowercase letter
                                                    </div>
                                                    <div class="password-requirement" id="req-uppercase">
                                                        <i class="fas fa-circle"></i> Uppercase letter
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="password-requirement" id="req-number">
                                                        <i class="fas fa-circle"></i> Number
                                                    </div>
                                                    <div class="password-requirement" id="req-special">
                                                        <i class="fas fa-circle"></i> Special character
                                                    </div>
                                                    <div class="password-requirement" id="req-match">
                                                        <i class="fas fa-circle"></i> Passwords match
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit -->
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn bg-primary text-white btn-block w-100"
                                                id="submitBtn">
                                                <i class="fas fa-key me-2"></i> Reset Password
                                            </button>
                                        </div>
                                    </form>

                                    <div class="text-center mt-4">
                                        <a href="{{ route('login') }}" class="text-primary">
                                            <i class="fas fa-arrow-left me-1"></i> Back to Login
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Toastr configuration
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "5000",
                "closeButton": true,
                "progressBar": true
            };

            // Toggle password visibility
            $('#togglePassword').click(function() {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            $('#toggleConfirmPassword').click(function() {
                const passwordField = $('#password_confirmation');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            });

            // Password validation
            function validatePassword() {
                const password = $('#password').val();
                const confirm = $('#password_confirmation').val();

                // Length check
                const lengthValid = password.length >= 8;
                updateRequirement('#req-length', lengthValid);

                // Lowercase check
                const lowercaseValid = /[a-z]/.test(password);
                updateRequirement('#req-lowercase', lowercaseValid);

                // Uppercase check
                const uppercaseValid = /[A-Z]/.test(password);
                updateRequirement('#req-uppercase', uppercaseValid);

                // Number check
                const numberValid = /[0-9]/.test(password);
                updateRequirement('#req-number', numberValid);

                // Special character check
                const specialValid = /[!@#$%^&*(),.?":{}|<>]/.test(password);
                updateRequirement('#req-special', specialValid);

                // Match check
                const matchValid = password.length > 0 && confirm.length > 0 && password === confirm;
                updateRequirement('#req-match', matchValid);

                // Update strength bars
                let strength = 0;
                if (lengthValid) strength++;
                if (lowercaseValid) strength++;
                if (uppercaseValid) strength++;
                if (numberValid) strength++;
                if (specialValid) strength++;

                $('#strength-bar-1, #strength-bar-2, #strength-bar-3').removeClass('weak medium strong');

                if (password.length === 0) {
                    // No bars
                } else if (strength <= 2) {
                    $('#strength-bar-1').addClass('weak');
                } else if (strength <= 4) {
                    $('#strength-bar-1, #strength-bar-2').addClass('medium');
                } else {
                    $('#strength-bar-1, #strength-bar-2, #strength-bar-3').addClass('strong');
                }
            }

            function updateRequirement(selector, isValid) {
                const element = $(selector);
                if (isValid) {
                    element.addClass('valid').removeClass('invalid');
                    element.find('i').removeClass('fa-circle').addClass('fa-check-circle');
                } else {
                    element.addClass('invalid').removeClass('valid');
                    element.find('i').removeClass('fa-check-circle').addClass('fa-circle');
                }
            }

            $('#password, #password_confirmation').on('keyup', validatePassword);

            // Form submit
            $('#resetPasswordForm').on('submit', function(e) {
                e.preventDefault();

                // Validate all requirements
                const password = $('#password').val();
                const confirm = $('#password_confirmation').val();

                const isValid =
                    password.length >= 8 &&
                    /[a-z]/.test(password) &&
                    /[A-Z]/.test(password) &&
                    /[0-9]/.test(password) &&
                    /[!@#$%^&*(),.?":{}|<>]/.test(password) &&
                    password === confirm;

                if (!isValid) {
                    toastr.error('Please meet all password requirements');
                    return;
                }

                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Resetting...');

                $.ajax({
                    url: '{{ route('profile.password.reset.submit') }}',
                    type: 'POST',
                    data: {
                        email: $('#email').val(),
                        token: $('#token').val(),
                        password: password,
                        password_confirmation: confirm,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 3000,
                                showConfirmButton: true,
                                confirmButtonText: 'Login Now',
                                confirmButtonColor: '#003366'
                            }).then(() => {
                                window.location.href = '{{ route('login') }}';
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(
                            '<i class="fas fa-key me-2"></i> Reset Password');

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            if (errors) {
                                Object.keys(errors).forEach(key => {
                                    $(`#${key}`).addClass('is-invalid');
                                    $(`#${key}`).siblings('.invalid-feedback').text(
                                        errors[key][0]);
                                });
                                toastr.error('Please check the form for errors');
                            } else {
                                toastr.error(xhr.responseJSON.message || 'Validation error');
                            }
                        } else {
                            toastr.error(xhr.responseJSON?.message ||
                                'Failed to reset password');
                        }
                    }
                });
            });

            // Check token validity on page load
            $.ajax({
                url: '{{ route('profile.password.check-token') }}',
                type: 'POST',
                data: {
                    email: $('#email').val(),
                    token: $('#token').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (!response.valid) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Link Expired',
                            text: response.message ||
                                'This password reset link has expired. Please request a new one.',
                            confirmButtonText: 'Go to Login',
                            confirmButtonColor: '#ff6600'
                        }).then(() => {
                            window.location.href = '{{ route('login') }}';
                        });
                    }
                }
            });
        });
    </script>
</body>

</html>
