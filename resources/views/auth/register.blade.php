<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | {{ config('app.name') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Styles -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet">

    <style>
        .authincation-content {
            background-color: #ffffff !important;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .captcha-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }

        .captcha-img {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }

        .btn-reload {
            background: none;
            border: none;
            color: #ff8000;
            cursor: pointer;
            font-size: 18px;
        }

        .btn-reload:hover {
            color: #000000;
        }

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

        .input-group> :not(:first-child):not(.dropdown-menu) {
            margin-left: -1px;
        }

        /* Password Strength Indicator */
        .password-strength {
            height: 5px;
            background-color: #e0e0e0;
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            transition: all 0.3s;
            width: 0%;
        }

        .strength-weak {
            background-color: #dc3545;
            width: 33%;
        }

        .strength-medium {
            background-color: #ffc107;
            width: 66%;
        }

        .strength-strong {
            background-color: #28a745;
            width: 100%;
        }

        .password-strength-text {
            font-size: 12px;
            margin-top: 3px;
        }

        /* Department Select Hidden by Default */
        #department-container {
            display: none;
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
                                    <h4 class="text-center mb-4 text-black">Create your account</h4>

                                    <!-- Error Messages -->
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>

                                        <!-- CHECK IF EMAIL ERROR = SOFT DELETED OR UNVERIFIED -->
                                        @if ($errors->has('email'))
                                            @php
                                                // Check for unverified OR soft deleted user
                                                $problematicUser = \App\Models\User::withTrashed()
                                                    ->where('email', old('email'))
                                                    ->where(function ($query) {
                                                        $query
                                                            ->whereNull('email_verified_at')
                                                            ->orWhereNotNull('deleted_at');
                                                    })
                                                    ->first();
                                            @endphp

                                            @if ($problematicUser)
                                                <div class="alert alert-warning alert-dismissible fade show">
                                                    <h6 class="mb-2">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>Account Issue
                                                        Detected
                                                    </h6>
                                                    <p class="mb-3 small">
                                                        @if ($problematicUser->trashed())
                                                            This email was previously associated with a
                                                            <strong>deactivated account</strong>.
                                                        @else
                                                            This email has an <strong>unverified account</strong>
                                                            created on
                                                            <strong>{{ $problematicUser->created_at->format('d M Y H:i') }}</strong>.
                                                        @endif
                                                        <br>
                                                        You can reset this account to register again with the same
                                                        email.
                                                    </p>

                                                    <form method="POST" action="{{ route('auth.reset-unverified') }}"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to permanently delete the previous account?');">
                                                        @csrf
                                                        <input type="hidden" name="email"
                                                            value="{{ $problematicUser->email }}">
                                                        <button type="submit" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-sync-alt me-1"></i> Reset & Register Again
                                                        </button>
                                                    </form>

                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="alert"></button>
                                                </div>
                                            @endif
                                        @endif
                                    @endif


                                    <form method="POST" action="{{ route('register') }}" id="register-form">
                                        @csrf

                                        <!-- Full Name -->
                                        <div class="form-group">
                                            <label class="mb-1 text-black"><strong>Full Name</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-user text-muted"></i>
                                                </span>
                                                <input type="text"
                                                    class="form-control @error('name') is-invalid @enderror border-start-0"
                                                    name="name" value="{{ old('name') }}"
                                                    placeholder="Masukkan nama lengkap" required>
                                            </div>
                                            @error('name')
                                                <span class="invalid-feedback d-block">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Email -->
                                        <div class="form-group mt-3">
                                            <label class="mb-1 text-black"><strong>Email</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-envelope text-muted"></i>
                                                </span>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror border-start-0"
                                                    name="email" value="{{ old('email') }}"
                                                    placeholder="Masukkan email" required>
                                            </div>
                                            @error('email')
                                                <span class="invalid-feedback d-block">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Phone Number -->
                                        <div class="form-group mt-3">
                                            <label class="mb-1 text-black"><strong>Phone Number</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-phone text-muted"></i>
                                                </span>
                                                <input type="text"
                                                    class="form-control @error('phone') is-invalid @enderror border-start-0"
                                                    name="phone" value="{{ old('phone') }}" placeholder="Opsional"
                                                    maxlength="15">
                                            </div>
                                            @error('phone')
                                                <span class="invalid-feedback d-block">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Checkbox: Saya Teknisi -->
                                        <div class="form-group mt-3">
                                            <div class="form-check custom-checkbox">
                                                <input type="checkbox" class="form-check-input" id="is_technician"
                                                    name="is_technician" value="1"
                                                    {{ old('is_technician') ? 'checked' : '' }}>
                                                <label class="form-check-label text-black" for="is_technician">
                                                    <strong>Saya adalah Teknisi</strong>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Department Select (Hidden by default) -->
                                        <div class="form-group mt-3" id="department-container">
                                            <label class="mb-1 text-black"><strong>Pilih Department</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-building text-muted"></i>
                                                </span>
                                                <select
                                                    class="form-control @error('department_id') is-invalid @enderror border-start-0"
                                                    name="department_id" id="department_id">
                                                    <option value="">-- Pilih Department --</option>
                                                    @foreach ($departments as $dept)
                                                        <option value="{{ $dept->id }}"
                                                            {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                                            {{ $dept->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('department_id')
                                                <span class="invalid-feedback d-block">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Password -->
                                        <div class="form-group mt-3">
                                            <label class="mb-1 text-black"><strong>Password</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-lock text-muted"></i>
                                                </span>
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror border-start-0"
                                                    name="password" id="password"
                                                    placeholder="Buat password kuat (min 8 karakter)" required>
                                                <button class="btn btn-outline-secondary border-start-0"
                                                    type="button" id="toggle-password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <!-- Password Strength Indicator -->
                                            <div class="password-strength">
                                                <div class="password-strength-bar" id="strength-bar"></div>
                                            </div>
                                            <small class="password-strength-text" id="strength-text"></small>
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
                                                <input type="password" class="form-control"
                                                    name="password_confirmation" id="password-confirm"
                                                    placeholder="Ulangi password" required>
                                                <button class="btn btn-outline-secondary border-start-0"
                                                    type="button" id="toggle-password-confirm">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Captcha -->
                                        <div class="form-group mt-3">
                                            <label class="mb-1 text-black"><strong>Captcha</strong></label>
                                            <input type="text"
                                                class="form-control @error('captcha') is-invalid @enderror"
                                                name="captcha" placeholder="Ketik kode captcha" required
                                                autocomplete="off">
                                            <div class="captcha-container">
                                                <div class="captcha-img d-inline-block">
                                                    {!! captcha_img() !!}
                                                </div>
                                                <button type="button" class="btn-reload ms-2" id="reload-captcha"
                                                    style="vertical-align: top; padding: 6px 10px;">
                                                    ↻
                                                </button>
                                            </div>
                                            @error('captcha')
                                                <span class="invalid-feedback d-block mt-1">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Submit -->
                                        <div class="text-center mt-4">
                                            <button type="submit"
                                                class="btn bg-primary text-white btn-block w-100">Sign Up</button>
                                        </div>
                                    </form>
                                    <!-- Success Message -->
                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show">
                                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    {{-- <!-- Error Messages -->
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="alert"></button>
                                        </div>

                                        <!-- CHECK IF EMAIL ERROR = UNVERIFIED ACCOUNT EXISTS -->
                                        @if ($errors->has('email'))
                                            @php
                                                $unverifiedUser = \App\Models\User::where('email', old('email'))
                                                    ->whereNull('email_verified_at')
                                                    ->first();
                                            @endphp

                                            @if ($unverifiedUser)
                                                <div class="alert alert-warning alert-dismissible fade show">
                                                    <h6 class="mb-2">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>Unverified
                                                        Account Detected
                                                    </h6>
                                                    <p class="mb-3 small">
                                                        We found an unverified account with
                                                        <strong>{{ $unverifiedUser->email }}</strong> created on
                                                        <strong>{{ $unverifiedUser->created_at->format('d M Y H:i') }}</strong>.
                                                        <br>
                                                        You can reset this account to register again with the same
                                                        email.
                                                    </p>

                                                    <form method="POST"
                                                        action="{{ route('auth.reset-unverified') }}"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete the previous unverified account?');">
                                                        @csrf
                                                        <input type="hidden" name="email"
                                                            value="{{ $unverifiedUser->email }}">
                                                        <button type="submit" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-sync-alt me-1"></i> Reset & Register Again
                                                        </button>
                                                    </form>

                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="alert"></button>
                                                </div>
                                            @endif
                                        @endif
                                    @endif --}}

                                    <div class="new-account mt-3 text-center">
                                        <p class="text-black">Already have an account?
                                            <a class="text-primary" href="{{ route('login') }}">Sign in</a>
                                        </p>
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
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>

    <script>
        // ========== Reload Captcha ==========
        document.getElementById('reload-captcha').addEventListener('click', function() {
            fetch('{{ route('reload.captcha') }}')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('.captcha-img').innerHTML = data.captcha;
                })
                .catch(err => console.error('Reload captcha error:', err));
        });

        // ========== Toggle Password Visibility ==========
        const togglePassword = document.getElementById('toggle-password');
        const toggleConfirm = document.getElementById('toggle-password-confirm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password-confirm');

        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                const type = password.type === 'password' ? 'text' : 'password';
                password.type = type;
                this.innerHTML = type === 'password' ?
                    '<i class="fas fa-eye"></i>' :
                    '<i class="fas fa-eye-slash"></i>';
            });
        }

        if (toggleConfirm && confirmPassword) {
            toggleConfirm.addEventListener('click', function() {
                const type = confirmPassword.type === 'password' ? 'text' : 'password';
                confirmPassword.type = type;
                this.innerHTML = type === 'password' ?
                    '<i class="fas fa-eye"></i>' :
                    '<i class="fas fa-eye-slash"></i>';
            });
        }

        // ========== Show/Hide Department Dropdown ==========
        const isTechnicianCheckbox = document.getElementById('is_technician');
        const departmentContainer = document.getElementById('department-container');
        const departmentSelect = document.getElementById('department_id');

        isTechnicianCheckbox.addEventListener('change', function() {
            if (this.checked) {
                departmentContainer.style.display = 'block';
                departmentSelect.required = true;
            } else {
                departmentContainer.style.display = 'none';
                departmentSelect.required = false;
                departmentSelect.value = '';
            }
        });

        // Check on page load (for old() values)
        if (isTechnicianCheckbox.checked) {
            departmentContainer.style.display = 'block';
            departmentSelect.required = true;
        }

        // ========== Password Strength Indicator ==========
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);

            // Remove all classes
            strengthBar.classList.remove('strength-weak', 'strength-medium', 'strength-strong');

            if (password.length === 0) {
                strengthBar.style.width = '0%';
                strengthText.textContent = '';
                strengthText.className = 'password-strength-text';
                return;
            }

            if (strength.score <= 2) {
                strengthBar.classList.add('strength-weak');
                strengthText.textContent = 'Weak - ' + strength.feedback;
                strengthText.className = 'password-strength-text text-danger';
            } else if (strength.score <= 4) {
                strengthBar.classList.add('strength-medium');
                strengthText.textContent = 'Medium - ' + strength.feedback;
                strengthText.className = 'password-strength-text text-warning';
            } else {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = 'Strong - Good password!';
                strengthText.className = 'password-strength-text text-success';
            }
        });

        function checkPasswordStrength(password) {
            let score = 0;
            let feedback = '';

            // Length check
            if (password.length >= 8) score++;
            if (password.length >= 12) score++;

            // Character variety
            if (/[a-z]/.test(password)) score++; // lowercase
            if (/[A-Z]/.test(password)) score++; // uppercase
            if (/[0-9]/.test(password)) score++; // numbers
            if (/[^a-zA-Z0-9]/.test(password)) score++; // special chars

            // Feedback
            if (password.length < 8) {
                feedback = 'Too short (min 8 characters)';
            } else if (score <= 2) {
                feedback = 'Add uppercase, numbers, or symbols';
            } else if (score <= 4) {
                feedback = 'Add more variety';
            } else {
                feedback = 'Excellent!';
            }

            return {
                score: score,
                feedback: feedback
            };
        }
    </script>
</body>

</html>
