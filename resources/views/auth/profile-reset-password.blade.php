<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | {{ config('app.name') }}</title>

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
        .reset-container {
            position: relative;
            z-index: 3;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* Card */
        .reset-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            width: min(500px, 100%);
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

        /* Form Area */
        .form-side {
            background: white;
            padding: 2.5rem;
        }

        /* Mobile Logo */
        .mobile-logo {
            text-align: center;
            margin-bottom: 1rem;
        }

        .mobile-logo .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .mobile-logo .logo-item img {
            width: 60px;
            height: auto;
        }

        .mobile-logo .logo-item:first-child img {
            width: 30px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .form-header h2 {
            color: #020e46;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #6c757d;
            font-size: 0.85rem;
        }

        /* Info Email Box */
        .info-email {
            background: #e6f0ff;
            border-left: 4px solid #ff6600;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }

        .info-email i {
            color: #ff6600;
            margin-right: 8px;
        }

        .info-email strong {
            color: #020e46;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: #020e46;
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .input-group {
            border-radius: 12px;
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
            padding: 0.75rem 1rem;
        }

        .form-control {
            border: none;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
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
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-toggle-password:hover {
            color: #ff6600;
        }

        /* Password Strength */
        .password-strength {
            height: 4px;
            margin-top: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
            transition: all 0.3s ease;
        }

        .password-strength.weak {
            background-color: #dc3545;
            width: 33%;
        }

        .password-strength.medium {
            background-color: #ffc107;
            width: 66%;
        }

        .password-strength.strong {
            background-color: #28a745;
            width: 100%;
        }

        /* Tooltip Info - Minimalis */
        .password-requirement-hint {
            font-size: 0.7rem;
            color: #6c757d;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .password-requirement-hint i {
            color: #ff6600;
            font-size: 0.7rem;
        }

        /* Submit Button */
        .btn-submit {
            background: #ff6600;
            border: none;
            border-radius: 12px;
            padding: 0.9rem;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-submit:hover {
            background: #e55a00;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 102, 0, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Back to Login */
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-link a {
            color: #ff6600;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            padding: 0.8rem 1rem;
            margin-bottom: 1.5rem;
            border: none;
            font-size: 0.85rem;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
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

        /* Responsive */
        @media (max-width: 576px) {
            .reset-container {
                padding: 1rem;
            }

            .form-side {
                padding: 1.5rem;
            }

            .form-header h2 {
                font-size: 1.4rem;
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
    <div class="reset-container">
        <div class="reset-card">
            <div class="form-side">
                <!-- Mobile Logo -->
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
                    <h2>Reset Password</h2>
                    <p>Create a new password for your account</p>
                </div>

                <!-- Info Email -->
                <div class="info-email">
                    <i class="fas fa-envelope"></i>
                    <strong>Email:</strong> {{ $email }}
                </div>

                <form id="resetPasswordForm">
                    @csrf
                    <input type="hidden" name="token" id="token" value="{{ $token }}">
                    <input type="hidden" name="email" id="email" value="{{ $email }}">

                    <!-- New Password -->
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" id="password" name="password" class="form-control"
                                placeholder="Enter new password" required>
                            <button class="btn-toggle-password" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                        <div class="password-requirement-hint">
                            <i class="fas fa-info-circle"></i>
                            <span>Password must be at least 8 characters</span>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group mt-3">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control" placeholder="Confirm new password" required>
                            <button class="btn-toggle-password" type="button" id="toggleConfirmPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center mt-4">
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="fas fa-key me-2"></i> Reset Password
                        </button>
                    </div>
                </form>

                <div class="back-link">
                    <a href="{{ route('login') }}">
                        <i class="fas fa-arrow-left me-1"></i> Back to Login
                    </a>
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

        // Toastr configuration
        toastr.options = {
            "positionClass": "toast-top-right",
            "timeOut": "5000",
            "closeButton": true,
            "progressBar": true
        };

        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const toggleConfirm = document.getElementById('toggleConfirmPassword');
        const passwordField = document.getElementById('password');
        const confirmField = document.getElementById('password_confirmation');

        if (togglePassword && passwordField) {
            togglePassword.addEventListener('click', function() {
                const isPassword = passwordField.type === 'password';
                passwordField.type = isPassword ? 'text' : 'password';
                this.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
            });
        }

        if (toggleConfirm && confirmField) {
            toggleConfirm.addEventListener('click', function() {
                const isPassword = confirmField.type === 'password';
                confirmField.type = isPassword ? 'text' : 'password';
                this.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
            });
        }

        // Password Strength Indicator (hanya untuk min 8 karakter)
        const strengthBar = document.getElementById('passwordStrength');

        if (passwordField && strengthBar) {
            passwordField.addEventListener('input', function() {
                const length = this.value.length;

                strengthBar.classList.remove('weak', 'medium', 'strong');

                if (length === 0) {
                    strengthBar.style.width = '0%';
                    strengthBar.style.backgroundColor = '#e9ecef';
                } else if (length < 4) {
                    strengthBar.classList.add('weak');
                } else if (length < 8) {
                    strengthBar.classList.add('medium');
                } else {
                    strengthBar.classList.add('strong');
                }
            });
        }

        // Form submit
        $('#resetPasswordForm').on('submit', function(e) {
            e.preventDefault();

            const password = $('#password').val();
            const confirm = $('#password_confirmation').val();

            if (password.length < 8) {
                toastr.error('Password must be at least 8 characters long!');
                return;
            }

            if (password !== confirm) {
                toastr.error('Passwords do not match!');
                return;
            }

            const submitBtn = $('#submitBtn');
            submitBtn.prop('disabled', true).html(
                '<span class="loading-spinner me-2"></span>Resetting...');

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
    </script>
</body>

</html>
