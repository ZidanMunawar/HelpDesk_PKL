<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email | {{ config('app.name') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/logo-main.png') }}" type="image/x-icon">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/logo-main.png') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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

        .email-icon-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
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
                                <div class="auth-form text-center p-4">
                                    <div class="mb-3">
                                        <img src="{{ asset('assets/images/logo.jpeg') }}" alt="Logo"
                                            style="width: 150px; height: auto;">
                                    </div>

                                    <div class="mb-4">
                                        <i class="fas fa-envelope-open-text email-icon-pulse"
                                            style="font-size: 64px; color: #ff8000;"></i>
                                    </div>

                                    <h4 class="text-black mb-3">Verify Your Email Address</h4>

                                    <div class="alert alert-info text-start">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Your account:</strong> {{ auth()->user()->email }}
                                        <br>
                                        <strong>Status:</strong>
                                        @if (auth()->user()->role === 'technician')
                                            <span class="badge badge-warning">Technician (Pending Admin Approval)</span>
                                        @else
                                            <span class="badge badge-info">User Account</span>
                                        @endif
                                    </div>

                                    <p class="text-muted mb-4">
                                        We have sent a verification link to your email address.
                                        <br><br>
                                        <strong>Please check your inbox (and spam folder)</strong> and click the
                                        verification link.
                                        <br><br>
                                        If you did not receive the email, click the button below to resend.
                                    </p>

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

                                    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-100 mb-2">
                                            <i class="fas fa-paper-plane me-2"></i> Resend Verification Email
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary w-100">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>

                                    <div class="mt-4">
                                        <small class="text-muted">
                                            <i class="fas fa-question-circle me-1"></i>
                                            Having trouble? Contact support at support@harrishotel.com
                                        </small>
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
</body>

</html>
