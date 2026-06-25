<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | {{ config('app.name') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/logo-main.png') }}" type="image/x-icon">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/logo-main.png') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #020e46;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Wave Atas */
        .wave-top {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1;
            line-height: 0;
            opacity: 0.15;
            transform: rotate(180deg);
        }

        .wave-top svg {
            display: block;
            width: 100%;
            height: auto;
        }

        .wave-top svg path {
            fill: #ffffff;
        }

        /* Wave Bawah */
        .wave-bottom {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 1;
            line-height: 0;
            opacity: 0.15;
        }

        .wave-bottom svg {
            display: block;
            width: 100%;
            height: auto;
        }

        .wave-bottom svg path {
            fill: #ffffff;
        }

        /* Particle Canvas */
        #particle-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            pointer-events: none;
            opacity: 0.3;
        }

        /* Main Container */
        .register-container {
            position: relative;
            z-index: 3;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* Card Register */
        .register-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: min(1200px, 100%);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left Side - Logo Area */
        .logo-side {
            background: linear-gradient(135deg, #020e46 0%, #041a6b 100%);
            padding: 1rem 0.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            min-height: 830px;
            position: relative;
            overflow: hidden;
        }

        .logo-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Logo Container - Side by Side */
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 3rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .logo-item {
            text-align: center;
            animation: float 4s ease-in-out infinite;
        }

        .logo-item:first-child {
            animation-delay: 0s;
        }

        .logo-item:last-child {
            animation-delay: 0.5s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .logo-item img {
            width: 260px;
            height: auto;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.3)) drop-shadow(0 0 20px rgba(255, 255, 255, 0.2));
            transition: transform 0.3s ease;
        }

        .logo-item:first-child img {
            width: 160px;
        }

        .logo-item img:hover {
            transform: scale(1.05);
        }

        .logo-side h3 {
            color: white;
            margin-top: 2rem;
            font-weight: 600;
            font-size: 1.5rem;
            text-align: center;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .logo-side p {
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            font-size: 0.9rem;
            margin-top: 1rem;
            max-width: 300px;
        }

        /* Right Side - Form Area */
        .form-side {
            background: white;
            padding: 2.5rem 2.5rem;
            height: 100%;
            min-height: 800px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Mobile Logo - Hidden on Desktop */
        .mobile-logo {
            display: none;
            text-align: center;
            margin-bottom: 0;
            /* Dulu 1rem, sekarang 0 */
        }

        .mobile-logo .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 0;
            /* Dulu 0.5rem, sekarang 0 */
        }

        .mobile-logo .logo-item img {
            width: 60px;
            height: auto;
        }

        .mobile-logo .logo-item:first-child img {
            width: 50px;
        }

        .form-header {
            margin-bottom: 1rem;
        }

        .form-header h2 {
            color: #020e46;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            color: #020e46;
            font-weight: 500;
            font-size: 0.85rem;
            margin-bottom: 0.3rem;
            display: block;
        }

        .input-group {
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: #ff6600;
            box-shadow: 0 0 0 3px rgba(255, 102, 0, 0.1);
        }

        .input-group-text {
            background: #f8f9fa;
            border: none;
            color: #6c757d;
            padding: 0.6rem 1rem;
        }

        .form-control {
            border: none;
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            box-shadow: none;
            background: #f8f9fa;
        }

        .btn-toggle-password {
            background: #f8f9fa;
            border: none;
            color: #6c757d;
            padding: 0.6rem 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-toggle-password:hover {
            color: #ff6600;
        }

        /* Password Strength Indicator */
        .password-strength {
            height: 4px;
            background-color: #e0e0e0;
            border-radius: 2px;
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
            font-size: 11px;
            margin-top: 3px;
        }

        /* Captcha Container */
        .captcha-container {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .captcha-input {
            flex: 1;
            min-width: 200px;
        }

        .captcha-image {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 6px;
            border: 1px solid #e0e0e0;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .captcha-image img {
            display: block;
            border-radius: 6px;
            max-width: 130px;
            height: auto;
        }

        /* Reload Button - Icon Only */
        .btn-reload-icon {
            background: #ff6600;
            border: none;
            border-radius: 8px;
            width: 38px;
            height: 38px;
            color: white;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .btn-reload-icon:hover {
            background: #e55a00;

        }

        .btn-reload-icon:active {
            transform: scale(0.95);
        }

        .btn-reload-icon i {
            transition: transform 0.3s ease;
        }

        .btn-reload-icon:hover i {
            transform: rotate(180deg);
        }

        /* Submit Button */
        .btn-register {
            background: #ff6600;
            border: none;
            border-radius: 10px;
            padding: 0.8rem;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 1rem 0 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-register:hover {
            background: #e55a00;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 102, 0, 0.4);
        }

        .btn-register:active {
            transform: translateY(0);
        }

        /* Login Link */
        .login-link {
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .login-link a {
            color: #ff6600;
            text-decoration: none;
            font-weight: 600;
            margin-left: 5px;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Alerts */
        .alert {
            border-radius: 10px;
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
            border: none;
            font-size: 0.85rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .alert ul {
            padding-left: 1.2rem;
            margin-bottom: 0;
        }

        .alert li {
            font-size: 0.85rem;
        }

        /* Loading Spinner */
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

        /* Shake Animation */
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

        /* Responsive */
        @media (max-width: 992px) {
            .logo-side {
                min-height: 750px;
                padding: 2rem;
            }

            .logo-item img {
                width: 160px;
            }

            .logo-item:first-child img {
                width: 180px;
            }

            .form-side {
                padding: 2rem;
                min-height: 750px;
            }
        }

        @media (max-width: 768px) {

            /* Hide desktop logo side on mobile */
            .logo-side {
                display: none;
            }

            /* Show mobile logo */
            .mobile-logo {
                display: block;
            }

            .form-side {
                min-height: auto;
                padding: 2rem 1.5rem;
            }

            .register-card {
                border-radius: 20px;
            }

            .form-header {
                margin-bottom: 0.5rem;
            }

            .form-header h2 {
                font-size: 1.5rem;
                text-align: center;
                margin-bottom: 0.2rem;
            }

            .form-header p {
                font-size: 0.8rem;
                text-align: center;
                margin-bottom: 0.5rem;
            }

            .captcha-container {
                flex-wrap: nowrap;
            }

            .captcha-image {
                flex-shrink: 0;
            }
        }

        @media (max-width: 576px) {
            .register-container {
                padding: 1rem;
            }

            .form-side {
                padding: 1.5rem;
            }

            .mobile-logo .logo-item img {
                width: 50px;
            }

            .mobile-logo .logo-item:first-child img {
                width: 45px;
            }

            .form-header {
                margin-bottom: 0.3rem;
            }

            .form-header h2 {
                font-size: 1.3rem;
            }

            .captcha-container {
                flex-wrap: wrap;
            }

            .captcha-input {
                min-width: 100%;
            }

            .captcha-image {
                width: 100%;
                justify-content: space-between;
            }

            .btn-reload-icon {
                width: 38px;
                height: 38px;
            }
        }

        @media (max-width: 380px) {
            .mobile-logo .logo-container {
                gap: 0.5rem;
            }

            .mobile-logo .logo-item img {
                width: 45px;
            }

            .mobile-logo .logo-item:first-child img {
                width: 40px;
            }
        }
    </style>
</head>

<body>
    <!-- Wave Atas -->
    <div class="wave-top">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#ffffff" fill-opacity="1"
                d="M0,96L40,128C80,160,160,224,240,218.7C320,213,400,139,480,112C560,85,640,107,720,144C800,181,880,235,960,256C1040,277,1120,267,1200,218.7C1280,171,1360,85,1400,42.7L1440,0L1440,320L1400,320C1360,320,1280,320,1200,320C1120,320,1040,320,960,320C880,320,800,320,720,320C640,320,560,320,480,320C400,320,320,320,240,320C160,320,80,320,40,320L0,320Z">
            </path>
        </svg>
    </div>

    <!-- Wave Bawah -->
    <div class="wave-bottom">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#ffffff" fill-opacity="1"
                d="M0,160L34.3,154.7C68.6,149,137,139,206,144C274.3,149,343,171,411,197.3C480,224,549,256,617,261.3C685.7,267,754,245,823,229.3C891.4,213,960,203,1029,197.3C1097.1,192,1166,192,1234,186.7C1302.9,181,1371,171,1406,165.3L1440,160L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z">
            </path>
        </svg>
    </div>

    <!-- Particle Canvas -->
    <canvas id="particle-canvas"></canvas>

    <!-- Main Container -->
    <div class="register-container">
        <div class="register-card row g-0">
            <!-- Left Side - Logo Area (Desktop Only) -->
            <div class="col-lg-6 logo-side">
                <div class="logo-container">
                    <div class="logo-item">
                        <img src="{{ asset('assets/images/logo-main.png') }}" alt="Harris Ticketing System">
                    </div>
                    <div class="logo-item">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Harris Hotel">
                    </div>
                </div>
                <h3>Engineering Maintenance System</h3>
                <p>Integrated ticketing system for Harris Hotel & Pop Hotel with multi-level approval workflow</p>
            </div>

            <!-- Right Side - Form Area -->
            <div class="col-lg-6 form-side">
                <!-- Mobile Logo (Visible only on mobile) -->
                <div class="mobile-logo">
                    <div class="logo-container">
                        <div class="logo-item">
                            <img src="{{ asset('assets/images/logo-main.png') }}" alt="Harris Ticketing System">
                        </div>
                        <div class="logo-item">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="Harris Hotel">
                        </div>
                    </div>
                </div>

                <div class="form-header">
                    <h2>Create Account</h2>
                    <p>Sign up to get started</p>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

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
                                    $query->whereNull('email_verified_at')->orWhereNotNull('deleted_at');
                                })
                                ->first();
                        @endphp

                        @if ($problematicUser)
                            <div class="alert alert-warning alert-dismissible fade show mt-2">
                                <h6 class="mb-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Account Issue Detected
                                </h6>
                                <p class="mb-3 small">
                                    @if ($problematicUser->trashed())
                                        This email was previously associated with a
                                        <strong>deactivated account</strong>.
                                    @elseif (is_null($problematicUser->email_verified_at))
                                        This email has an <strong>unverified account</strong>
                                        created on
                                        <strong>{{ $problematicUser->created_at->format('d M Y H:i') }}</strong>.
                                    @endif
                                    <br>
                                    You can reset this account to register again with the same email.
                                </p>

                                <form method="POST" action="{{ route('auth.reset-unverified') }}" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to permanently delete the previous account?');">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $problematicUser->email }}">
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-sync-alt me-1"></i> Reset & Register Again
                                    </button>
                                </form>

                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                    @endif
                @endif

                <form method="POST" action="{{ route('register') }}" id="register-form">
                    @csrf

                    <!-- Full Name -->
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}" placeholder="Enter your full name" required>
                        </div>
                        @error('name')
                            <span class="text-danger small mt-1 d-block">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                        </div>
                        @error('email')
                            <span class="text-danger small mt-1 d-block">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                name="phone" value="{{ old('phone') }}" placeholder="Optional" maxlength="15">
                        </div>
                        @error('phone')
                            <span class="text-danger small mt-1 d-block">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" id="password" placeholder="min 8 characters" required>
                            <button class="btn-toggle-password" type="button" id="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <!-- Password Strength Indicator -->
                        <div class="password-strength">
                            <div class="password-strength-bar" id="strength-bar"></div>
                        </div>
                        <small class="password-strength-text" id="strength-text"></small>
                        @error('password')
                            <span class="text-danger small mt-1 d-block">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" name="password_confirmation"
                                id="password-confirm" placeholder="Repeat password" required>
                            <button class="btn-toggle-password" type="button" id="toggle-password-confirm">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Captcha -->
                    <div class="form-group">
                        <label class="form-label">Captcha Verification</label>

                        <div class="captcha-container">
                            <div class="captcha-input">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-shield-alt"></i>
                                    </span>
                                    <input type="text" class="form-control @error('captcha') is-invalid @enderror"
                                        name="captcha" id="captcha-input" placeholder="Type the code" required
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="captcha-image">
                                <div id="captcha-img">
                                    {!! captcha_img() !!}
                                </div>
                                <button type="button" class="btn-reload-icon" id="reload-captcha">
                                    <i class="fas fa-redo-alt"></i>
                                </button>
                            </div>
                        </div>

                        @error('captcha')
                            <span class="text-danger small mt-1 d-block">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-register">
                        Sign Up
                    </button>

                    <!-- Login Link -->
                    <div class="login-link">
                        Already have an account?
                        <a href="{{ route('login') }}">Sign in</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Particle Effect
        (function() {
            class Particle {
                constructor(canvas, ctx) {
                    this.canvas = canvas;
                    this.ctx = ctx;
                    this.reset();
                }

                reset() {
                    this.x = Math.random() * this.canvas.width;
                    this.y = Math.random() * this.canvas.height;
                    this.vx = (Math.random() - 0.5) * 0.15;
                    this.vy = (Math.random() - 0.5) * 0.15;
                    this.size = Math.random() * 1.5 + 0.3;
                    this.opacity = Math.random() * 0.1;
                    this.color = `rgba(255, 255, 255, ${this.opacity})`;
                }

                update() {
                    this.x += this.vx;
                    this.y += this.vy;

                    if (this.x < 0) this.x = this.canvas.width;
                    if (this.x > this.canvas.width) this.x = 0;
                    if (this.y < 0) this.y = this.canvas.height;
                    if (this.y > this.canvas.height) this.y = 0;
                }

                draw() {
                    this.ctx.beginPath();
                    this.ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    this.ctx.fillStyle = this.color;
                    this.ctx.fill();
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const canvas = document.getElementById('particle-canvas');
                if (!canvas) return;

                const ctx = canvas.getContext('2d');
                let particles = [];
                let animationFrame;
                const particleCount = 30;

                function initParticles() {
                    canvas.width = window.innerWidth;
                    canvas.height = window.innerHeight;

                    particles = [];
                    for (let i = 0; i < particleCount; i++) {
                        particles.push(new Particle(canvas, ctx));
                    }
                }

                function animateParticles() {
                    if (!ctx || !canvas) return;

                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    particles.forEach(particle => {
                        particle.update();
                        particle.draw();
                    });

                    animationFrame = requestAnimationFrame(animateParticles);
                }

                initParticles();
                animateParticles();

                window.addEventListener('resize', function() {
                    cancelAnimationFrame(animationFrame);
                    initParticles();
                    animateParticles();
                });
            });
        })();

        // Toggle Password Visibility
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

        // Password Strength Indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);

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

            if (password.length >= 8) score++;
            if (password.length >= 12) score++;

            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^a-zA-Z0-9]/.test(password)) score++;

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

        // Reload Captcha - Icon Only
        const reloadBtn = document.getElementById('reload-captcha');
        const captchaImg = document.getElementById('captcha-img');
        const captchaInput = document.getElementById('captcha-input');

        reloadBtn.addEventListener('click', function() {
            const icon = this.querySelector('i');
            icon.className = 'fas fa-spinner fa-spin';

            fetch('{{ route('reload.captcha') }}')
                .then(response => response.json())
                .then(data => {
                    captchaImg.innerHTML = data.captcha;
                    icon.className = 'fas fa-redo-alt';
                    captchaInput.value = '';
                    captchaInput.focus();
                })
                .catch(err => {
                    console.error('Failed to reload captcha:', err);
                    icon.className = 'fas fa-redo-alt';
                });
        });

        // Auto focus captcha if error
        @if ($errors->has('captcha'))
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => {
                    const captchaInput = document.getElementById('captcha-input');
                    if (captchaInput) {
                        captchaInput.focus();
                        captchaInput.classList.add('invalid-captcha');
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
