<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | {{ config('app.name') }}</title>

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
        .input-group .input-group-text {
            border: 1px solid #ced4da !important;
        }

        .input-group .input-group-text {
            background-color: #f8f9fa;
        }

        .captcha-container {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
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

                                    <h4 class="text-center mb-2 text-black">Forgot Password?</h4>
                                    <p class="text-center text-muted mb-4">
                                        Enter your email address and we'll send you a link to reset your password.
                                    </p>

                                    @if (session('status'))
                                        <div class="alert alert-success alert-dismissible fade show">
                                            <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

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

                                    <form method="POST" action="{{ route('password.email') }}">
                                        @csrf

                                        <!-- Email -->
                                        <div class="form-group">
                                            <label class="mb-1 text-black"><strong>Email Address</strong></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="fas fa-envelope text-muted"></i>
                                                </span>
                                                <input type="email" name="email"
                                                    class="form-control @error('email') is-invalid @enderror border-start-0"
                                                    placeholder="Enter your email" value="{{ old('email') }}" required
                                                    autofocus>
                                            </div>
                                            @error('email')
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
                                                    name="captcha" placeholder="Enter captcha code" required
                                                    autocomplete="off">
                                            </div>
                                            <div class="captcha-container">
                                                <div class="captcha-img d-inline-block">
                                                    {!! captcha_img() !!}
                                                </div>
                                                <button type="button" class="btn-reload" id="reload-captcha">↻</button>
                                            </div>
                                            @error('captcha')
                                                <span class="invalid-feedback d-block mt-1">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <!-- Submit -->
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn bg-primary text-white btn-block w-100">
                                                <i class="fas fa-paper-plane me-2"></i> Send Reset Link
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
        // Reload Captcha
        document.getElementById('reload-captcha').addEventListener('click', function() {
            fetch('{{ route('reload.captcha') }}')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('.captcha-img').innerHTML = data.captcha;
                })
                .catch(err => console.error('Failed to reload captcha:', err));
        });
    </script>
</body>

</html>
