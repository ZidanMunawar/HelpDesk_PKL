<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | {{ config('app.name') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        .authincation-content {
            background-color: #ffffff !important;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .input-group .form-control,
        .input-group .input-group-text,
        .input-group .btn-outline-secondary {
            border: 1px solid #ced4da !important;
        }

        .input-group .input-group-text {
            background-color: #f8f9fa;
        }

        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            background-color: #e9ecef;
        }

        .password-strength.weak {
            background-color: #dc3545;
        }

        .password-strength.medium {
            background-color: #ffc107;
        }

        .password-strength.strong {
            background-color: #28a745;
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
                                    <p class="text-center text-muted mb-4">
                                        Enter your new password below.
                                    </p>

                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('password.update') }}">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $token }}">
                                        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                                        <!-- Email (readonly) -->
                                        <div class="form-group">
                                            <label class="mb-1 text-black"><strong>Email Address</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-envelope text-muted"></i>
                                                </span>
                                                <input type="email" class="form-control border-start-0"
                                                    value="{{ $email ?? old('email') }}" readonly>
                                            </div>
                                        </div>

                                        <!-- New Password -->
                                        <div class="form-group mt-3">
                                            <label class="mb-1 text-black"><strong>New Password</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-lock text-muted"></i>
                                                </span>
                                                <input type="password" id="password" name="password"
                                                    class="form-control @error('password') is-invalid @enderror border-start-0 border-end-0"
                                                    placeholder="Enter new password" required>
                                                <button class="btn btn-outline-secondary border-start-0" type="button"
                                                    id="toggle-password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <div class="password-strength" id="password-strength"></div>
                                            <small class="text-muted">Minimum 8 characters, mix of letters, numbers, and
                                                symbols</small>
                                            @error('password')
                                                <span class="invalid-feedback d-block">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
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
                                                    id="toggle-password-confirm">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Submit -->
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn bg-primary text-white btn-block w-100">
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

    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>

    <script>
        // Toggle Password Visibility
        function togglePassword(fieldId, buttonId) {
            const field = document.getElementById(fieldId);
            const button = document.getElementById(buttonId);

            button.addEventListener('click', function() {
                const isPassword = field.type === 'password';
                field.type = isPassword ? 'text' : 'password';
                this.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
            });
        }

        togglePassword('password', 'toggle-password');
        togglePassword('password_confirmation', 'toggle-password-confirm');

        // Password Strength Indicator
        const passwordField = document.getElementById('password');
        const strengthBar = document.getElementById('password-strength');

        passwordField.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            strengthBar.className = 'password-strength';
            if (strength === 0 || strength === 1) {
                strengthBar.classList.add('weak');
            } else if (strength === 2 || strength === 3) {
                strengthBar.classList.add('medium');
            } else if (strength === 4) {
                strengthBar.classList.add('strong');
            }
        });
    </script>
</body>

</html>
