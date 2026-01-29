<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | {{ config('app.name') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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

        /* Input dengan ikon */
        .input-group .form-control,
        .input-group .input-group-text {
            border: 1px solid #ced4da !important;
        }

        .input-group .form-control:focus,
        .input-group .btn-outline-secondary:focus {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }

        .input-group .input-group-text {
            background-color: #f8f9fa;
        }

        /* Captcha Container Side by Side */
        .captcha-side-container {
            display: flex;
            align-items: stretch;
            gap: 15px;
            margin: 15px 0;
        }

        .captcha-input-side {
            flex: 1;
        }

        .captcha-image-side {
            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .captcha-img {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            background-color: #f8f9fa;
            padding: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .captcha-img:hover {
            border-color: #86b7fe;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-reload {
            background: linear-gradient(135deg, #ff8000, #ffaa00);
            border: none;
            border-radius: 6px;
            width: 100%;
            padding: 8px 12px;
            color: white;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(255, 128, 0, 0.2);
        }

        .btn-reload:hover {
            background: linear-gradient(135deg, #ffaa00, #ff8000);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 128, 0, 0.3);
        }

        .btn-reload:active {
            transform: translateY(0);
            box-shadow: 0 1px 2px rgba(255, 128, 0, 0.2);
        }

        .btn-reload i {
            font-size: 12px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .captcha-side-container {
                flex-direction: column;
                gap: 10px;
            }

            .captcha-image-side {
                flex-direction: row;
                justify-content: space-between;
                width: 100%;
            }

            .captcha-img {
                flex: 1;
            }

            .btn-reload {
                width: auto;
                min-width: 100px;
            }
        }

        @media (max-width: 576px) {
            .captcha-image-side {
                flex-direction: column;
            }

            .btn-reload {
                width: 100%;
            }
        }

        /* Animations */
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .invalid-captcha {
            animation: shake 0.5s ease-in-out;
        }

        /* Loading animation */
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
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
                                    <h4 class="text-center mb-4 text-black">Sign in to your account</h4>

                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show">
                                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    @if (session('warning'))
                                        <div class="alert alert-warning alert-dismissible fade show">
                                            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    @if (session('info'))
                                        <div class="alert alert-info alert-dismissible fade show">
                                            <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    @if (session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('login') }}" id="login-form">
                                        @csrf

                                        <!-- Email -->
                                        <div class="form-group">
                                            <label class="mb-1 text-black"><strong>Email</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-envelope text-muted"></i>
                                                </span>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror border-start-0"
                                                    name="email" value="{{ old('email') }}"
                                                    placeholder="Enter your email" required autofocus>
                                            </div>
                                            @error('email')
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
                                                    name="password" id="password" placeholder="Enter your password"
                                                    required>
                                                <button class="btn btn-outline-secondary border-start-0" type="button"
                                                    id="toggle-password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <span class="invalid-feedback d-block">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Captcha -->
                                        <div class="form-group mt-3">
                                            <label class="mb-1 text-black"><strong>Captcha Verification</strong></label>

                                            <div class="captcha-side-container">
                                                <!-- Input Captcha (Kiri) -->
                                                <div class="captcha-input-side">
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white border-end-0">
                                                            <i class="fas fa-shield-alt text-muted"></i>
                                                        </span>
                                                        <input type="text"
                                                            class="form-control @error('captcha') is-invalid @enderror border-start-0"
                                                            name="captcha" id="captcha-input"
                                                            placeholder="Type the code" required autocomplete="off">
                                                    </div>
                                                    @error('captcha')
                                                        <span class="invalid-feedback d-block mt-1">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>

                                                <!-- Gambar Captcha (Kanan) -->
                                                <div class="captcha-image-side">
                                                    <div class="captcha-img d-inline-block" id="captcha-image">
                                                        {!! captcha_img() !!}
                                                    </div>
                                                    <button type="button" class="btn-reload" id="reload-captcha">
                                                        <i class="fas fa-redo-alt"></i> Reload
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Remember & Forgot -->
                                        <div class="row d-flex justify-content-between mt-4 mb-2">
                                            <div class="col-6">
                                                <div class="form-check custom-checkbox ms-1 text-black">
                                                    <input type="checkbox" class="form-check-input" id="remember"
                                                        name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    <label class="form-check-label text-primary"
                                                        for="remember">Remember me</label>
                                                </div>
                                            </div>
                                            <div class="col-6 text-end">
                                                <a class="text-primary" href="{{ route('password.request') }}">
                                                    <small>Forgot Password?</small>
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Submit -->
                                        <div class="text-center">
                                            <button type="submit"
                                                class="btn bg-primary text-white btn-block w-100">Sign In</button>
                                        </div>
                                    </form>

                                    <div class="new-account mt-3 text-center">
                                        <p class="text-black">Don't have an account?
                                            <a class="text-primary" href="{{ route('register') }}">Sign up</a>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts -->
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>

    <script>
        // === Reload Captcha with Animation ===
        const reloadBtn = document.getElementById('reload-captcha');
        const captchaImage = document.getElementById('captcha-image');
        const captchaInput = document.getElementById('captcha-input');
        const originalBtnText = reloadBtn.innerHTML;

        reloadBtn.addEventListener('click', function() {
            // Add loading animation to button
            reloadBtn.innerHTML = '<span class="loading-spinner"></span> Loading...';
            reloadBtn.disabled = true;

            fetch('{{ route('reload.captcha') }}')
                .then(response => response.json())
                .then(data => {
                    // Update captcha with fade effect
                    captchaImage.style.opacity = '0.5';
                    setTimeout(() => {
                        captchaImage.innerHTML = data.captcha;
                        captchaImage.style.opacity = '1';

                        // Reset button
                        reloadBtn.innerHTML = originalBtnText;
                        reloadBtn.disabled = false;

                        // Clear input
                        captchaInput.value = '';
                        captchaInput.focus();

                        // Add success animation
                        captchaImage.style.borderColor = '#28a745';
                        setTimeout(() => {
                            captchaImage.style.borderColor = '#e9ecef';
                        }, 1000);
                    }, 200);
                })
                .catch(err => {
                    console.error('Failed to reload captcha:', err);
                    reloadBtn.innerHTML = originalBtnText;
                    reloadBtn.disabled = false;

                    // Add error animation
                    captchaImage.style.borderColor = '#dc3545';
                    setTimeout(() => {
                        captchaImage.style.borderColor = '#e9ecef';
                    }, 1000);
                });
        });

        // === Toggle Password Visibility ===
        const toggleBtn = document.getElementById('toggle-password');
        const passwordField = document.getElementById('password');

        if (toggleBtn && passwordField) {
            toggleBtn.addEventListener('click', function() {
                const isPassword = passwordField.type === 'password';
                passwordField.type = isPassword ? 'text' : 'password';
                this.innerHTML = isPassword ?
                    '<i class="fas fa-eye-slash"></i>' :
                    '<i class="fas fa-eye"></i>';
            });
        }

        // === Auto focus captcha input when page loads if there's error ===
        @if ($errors->has('captcha'))
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    const captchaInput = document.getElementById('captcha-input');
                    if (captchaInput) {
                        captchaInput.focus();
                        captchaInput.classList.add('invalid-captcha');

                        // Remove animation class after animation completes
                        setTimeout(() => {
                            captchaInput.classList.remove('invalid-captcha');
                        }, 500);
                    }
                }, 300);
            });
        @endif
    </script>
</body>

</html>
