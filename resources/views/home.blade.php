<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Harris Hotel Ticketing System</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Viga&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            background-color: #020e46;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Particle Canvas */
        #particle-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
            display: block;
        }

        /* Wave Atas */
        .wave-top {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 2;
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
            z-index: 2;
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

        /* Minimal Glow Overlay */
        .glow-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.02) 0%, transparent 70%);
            z-index: 1;
            pointer-events: none;
        }

        /* Main Container */
        .container {
            position: relative;
            z-index: 3;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        /* Logo Container */
        .logo-wrapper {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeInDown 0.8s ease-out;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        /* Minimal white glow for logo */
        .logo-wrapper::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 110%;
            height: 110%;
            /* background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%); */
            filter: blur(20px);
            z-index: -1;
        }

        .logo-wrapper img {
            width: min(700px, 85vw);
            height: auto;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.5));
            transition: transform 0.3s ease;
        }

        /* Loading Section - LEBIH KECIL */
        .loading-section {
            width: min(350px, 70vw);
            text-align: center;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        /* Loading Text */
        .loading-text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
            font-weight: 300;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 0.3rem;
        }

        .loading-text i {
            color: #ffffff;
            margin-right: 0.3rem;
            font-size: 0.7rem;
        }

        /* Percentage */
        .percentage {
            color: #ffffff;
            font-size: 1.8rem;
            font-weight: 700;
            font-family: "Viga", sans-serif;
            margin-bottom: 1.2rem;
        }

        /* Slider Container - LEBIH KECIL */
        .slider-container {
            width: 100%;
            height: 2px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.8rem;
            position: relative;
        }

        .slider {
            height: 100%;
            width: 30%;
            background: #ff6600;
            border-radius: 2px;
            position: relative;
            animation: slide 1.5s ease-in-out infinite;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.3);
        }

        .slider::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 1s linear infinite;
        }

        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        @keyframes slide {
            0% {
                transform: translateX(-100%);
            }

            50% {
                transform: translateX(200%);
            }

            100% {
                transform: translateX(400%);
            }
        }

        /* Status Message - LEBIH KECIL */
        .status-message {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.7rem;
            margin-top: 0.8rem;
            background: rgba(2, 14, 70, 0.5);
            backdrop-filter: blur(3px);
            padding: 0.3rem 1rem;
            border-radius: 20px;
            display: inline-block;
            border: 1px solid rgba(255, 102, 0, 0.1);
        }

        .status-message i {
            color: #fcfcfc;
            margin-right: 0.3rem;
            font-size: 0.6rem;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile Optimization */
        @media (max-width: 480px) {
            .logo-wrapper img {
                width: min(500px, 90vw);
            }

            .loading-section {
                width: min(300px, 80vw);
            }

            .percentage {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }

            .loading-text {
                font-size: 0.7rem;
            }

            .status-message {
                font-size: 0.65rem;
                padding: 0.25rem 0.8rem;
            }

            .slider-container {
                height: 1.5px;
            }
        }

        /* Extra small devices */
        @media (max-width: 360px) {
            .logo-wrapper img {
                width: min(400px, 95vw);
            }

            .loading-section {
                width: min(250px, 85vw);
            }

            .percentage {
                font-size: 1.3rem;
            }
        }

        /* Hide scrollbar */
        ::-webkit-scrollbar {
            display: none;
        }

        * {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body>
    <!-- Particle Canvas -->
    <canvas id="particle-canvas"></canvas>

    <!-- Minimal Glow Overlay -->
    <div class="glow-overlay"></div>

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

    <!-- Main Container -->
    <div class="container">
        <!-- Logo PNG BESAR CENTER -->
        <div class="logo-wrapper">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Harris Hotel Logo">
        </div>

        <!-- Loading Section - LEBIH KECIL -->
        <div class="loading-section">
            <div class="loading-text">
                <i class="fas fa-cog fa-spin"></i>
                <span id="loadingMessage">INITIALIZING SYSTEM</span>
            </div>

            <div class="percentage" id="percentage">0%</div>

            <div class="slider-container">
                <div class="slider" id="slider"></div>
            </div>

            <div class="status-message" id="statusMessage">
                <i class="fas fa-circle-notch fa-spin"></i>
                <span id="statusText">Checking authentication...</span>
            </div>
        </div>
    </div>

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
                    this.opacity = Math.random() * 0.15;
                    this.color = `rgba(255, 102, 0, ${this.opacity})`;
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
                const particleCount = 40;

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

                    // Draw connecting lines
                    for (let i = 0; i < particles.length; i++) {
                        for (let j = i + 1; j < particles.length; j++) {
                            const dx = particles[i].x - particles[j].x;
                            const dy = particles[i].y - particles[j].y;
                            const distance = Math.sqrt(dx * dx + dy * dy);

                            if (distance < 80) {
                                ctx.beginPath();
                                ctx.moveTo(particles[i].x, particles[i].y);
                                ctx.lineTo(particles[j].x, particles[j].y);
                                const opacity = 0.03 * (1 - distance / 80);
                                ctx.strokeStyle = `rgba(255, 102, 0, ${opacity})`;
                                ctx.lineWidth = 0.3;
                                ctx.stroke();
                            }
                        }
                    }

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

        // Authentication Check
        const loadingMessages = [
            'CONNECTING',
            'VERIFYING',
            'CHECKING',
            'LOADING',
            'READY'
        ];

        let messageIndex = 0;
        const loadingMessageEl = document.getElementById('loadingMessage');
        const percentageEl = document.getElementById('percentage');
        const statusTextEl = document.getElementById('statusText');
        let percentage = 0;

        const messageInterval = setInterval(() => {
            messageIndex = (messageIndex + 1) % loadingMessages.length;
            loadingMessageEl.textContent = loadingMessages[messageIndex];
        }, 600);

        const percentageInterval = setInterval(() => {
            if (percentage < 95) {
                percentage += Math.floor(Math.random() * 4) + 1;
                if (percentage > 95) percentage = 95;
                percentageEl.textContent = percentage + '%';
            }
        }, 80);

        function getCookie(name) {
            let cookieValue = null;
            if (document.cookie && document.cookie !== '') {
                const cookies = document.cookie.split(';');
                for (let i = 0; i < cookies.length; i++) {
                    const cookie = cookies[i].trim();
                    if (cookie.substring(0, name.length + 1) === (name + '=')) {
                        cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                        break;
                    }
                }
            }
            return cookieValue;
        }

        async function checkAuth() {
            try {
                statusTextEl.textContent = 'Checking...';

                const response = await fetch('/api/user', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCookie('XSRF-TOKEN')
                    },
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    const data = await response.json();

                    if (data.authenticated && data.user) {
                        statusTextEl.textContent = `Welcome, ${data.user.name}`;
                        loadingMessageEl.textContent = 'REDIRECTING';

                        clearInterval(percentageInterval);
                        percentage = 100;
                        percentageEl.textContent = '100%';

                        setTimeout(() => {
                            window.location.href = '/dashboard';
                        }, 1000);
                    } else {
                        statusTextEl.textContent = 'Please login';
                        loadingMessageEl.textContent = 'TO LOGIN';

                        clearInterval(percentageInterval);
                        percentage = 100;
                        percentageEl.textContent = '100%';

                        setTimeout(() => {
                            window.location.href = '/login';
                        }, 1000);
                    }
                } else {
                    throw new Error('Failed');
                }
            } catch (error) {
                console.error('Error:', error);
                statusTextEl.textContent = 'Redirecting...';
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1500);
            }
        }

        setTimeout(checkAuth, 2000);

        window.addEventListener('beforeunload', () => {
            clearInterval(messageInterval);
            clearInterval(percentageInterval);
        });
    </script>
</body>

</html>
