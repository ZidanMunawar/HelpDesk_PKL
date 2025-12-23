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

        /* Input dengan ikon — pastikan border tetap kelihatan */
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

        .input-group> :not(:first-child):not(.dropdown-menu):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback) {
            margin-left: -1px;
        }

        .captcha-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .captcha-img {
            border: 1px solid #000000;
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
                                            {{ session('success') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    @if (session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show">
                                            {{ session('error') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif
                                    <form method="POST" action="{{ route('login') }}">
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
                                                    placeholder="Masukkan email" required autofocus>
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
                                                    name="password" id="password" placeholder="Masukkan password"
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
                                            <label class="mb-1 text-black"><strong>Captcha</strong></label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control @error('captcha') is-invalid @enderror"
                                                    name="captcha" placeholder="Ketik kode captcha" required
                                                    autocomplete="off">
                                            </div>
                                            <div class="captcha-container mt-2">
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

                                        <!-- Remember & Forgot -->
                                        <div class="row d-flex justify-content-between mt-4 mb-2">
                                            <div class="form-group">
                                                <div class="form-check custom-checkbox ms-1 text-black">
                                                    <input type="checkbox" class="form-check-input" id="remember"
                                                        name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    <label class="form-check-label text-primary" for="remember">Remember
                                                        me</label>
                                                </div>
                                            </div>
                                            {{-- <div class="form-group">
            <a class="text-black" href="{{ route('password.request') }}">Forgot Password?</a>
        </div> --}}
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

    <!-- Scripts -->
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>

    <script>
        // === Reload Captcha ===
        document.getElementById('reload-captcha').addEventListener('click', function() {
            fetch('{{ route('reload.captcha') }}')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('.captcha-img').innerHTML = data.captcha;
                })
                .catch(err => console.error('Gagal reload captcha:', err));
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
    </script>
</body>

</html>
