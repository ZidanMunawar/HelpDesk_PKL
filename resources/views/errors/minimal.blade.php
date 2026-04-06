{{-- resources/views/errors/minimal.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Harris & Pop Hotel Citylink">

    <title>@yield('title')</title>

    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%
        }

        body {
            margin: 0
        }

        a {
            background-color: transparent
        }

        code {
            font-family: monospace, monospace;
            font-size: 1em
        }

        [hidden] {
            display: none
        }

        html {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
            line-height: 1.5
        }

        *,
        :after,
        :before {
            box-sizing: border-box;
            border: 0 solid #e2e8f0
        }

        a {
            color: inherit;
            text-decoration: inherit
        }

        code {
            font-family: Menlo, Monaco, Consolas, Liberation Mono, Courier New, monospace
        }

        svg,
        video {
            display: block;
            vertical-align: middle
        }

        video {
            max-width: 100%;
            height: auto
        }

        /* Custom Harris Pop Styles */
        body {
            font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            background: linear-gradient(135deg, #1a2b4c 0%, #0f1a2f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .error-container {
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
        }

        .error-card {
            background: white;
            border-radius: 20px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border-bottom: 5px solid #f39c12;
            position: relative;
            overflow: hidden;
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #f39c12, #f1c40f);
        }

        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .logo-item img {
            max-height: 50px;
            width: auto;
        }

        .error-code {
            font-size: 120px;
            font-weight: 800;
            line-height: 1;
            color: #1a2b4c;
            margin: 20px 0;
            text-shadow: 4px 4px 0 rgba(243, 156, 18, 0.2);
            letter-spacing: 5px;
        }

        .error-message {
            font-size: 24px;
            font-weight: 600;
            color: #1a2b4c;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .error-message i {
            color: #f39c12;
            margin-right: 10px;
        }

        .error-description {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .btn-home {
            display: inline-block;
            background: #1a2b4c;
            color: white;
            padding: 12px 35px;
            border-radius: 50px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            font-size: 14px;
        }

        .btn-home:hover {
            background: #f39c12;
            color: #1a2b4c;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.4);
        }

        .btn-home i {
            margin-right: 8px;
        }

        @media (max-width: 640px) {
            .error-card {
                padding: 30px 20px;
            }

            .error-code {
                font-size: 80px;
            }

            .error-message {
                font-size: 18px;
            }

            .logo-item img {
                max-height: 40px;
            }
        }
    </style>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="error-container">
        <div class="error-card">
            <!-- Logo Harris & Pop -->
            <div class="logo-container">
                <div class="logo-item">
                    <img src="{{ asset('assets/images/logo-main.png') }}" alt="Harris Ticketing System"
                        onerror="this.style.display='none'">
                </div>
                <div class="logo-item">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Harris Hotel & Pop Hotel Citylink"
                        onerror="this.style.display='none'">
                </div>
            </div>

            <!-- Error Code -->
            <div class="error-code">
                @yield('code')
            </div>

            <!-- Error Message with Icon -->
            <div class="error-message">
                @php
                    $code = trim(str_replace('@yield', '', View::getSection('code') ?? ''));
                    $icon = match ((string) View::getSection('code')) {
                        '401' => '<i class="fas fa-lock"></i>',
                        '402' => '<i class="fas fa-credit-card"></i>',
                        '403' => '<i class="fas fa-ban"></i>',
                        '404' => '<i class="fas fa-compass"></i>',
                        '419' => '<i class="fas fa-hourglass-end"></i>',
                        '429' => '<i class="fas fa-tachometer-alt"></i>',
                        '500' => '<i class="fas fa-exclamation-triangle"></i>',
                        '503' => '<i class="fas fa-tools"></i>',
                        default => '<i class="fas fa-exclamation-circle"></i>',
                    };
                @endphp
                {!! $icon !!} @yield('message')
            </div>

            <!-- Description based on error code -->
            <div class="error-description">
                @switch(View::getSection('code'))
                    @case('401')
                        {{ __('You are not authorized to access this page. Please log in first.') }}
                    @break

                    @case('402')
                        {{ __('Payment is required to access this resource.') }}
                    @break

                    @case('403')
                        {{ __('You do not have permission to view this resource.') }}
                    @break

                    @case('404')
                        {{ __('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.') }}
                    @break

                    @case('419')
                        {{ __('Your session has expired. Please refresh the page and try again.') }}
                    @break

                    @case('429')
                        {{ __('You have made too many requests. Please wait a moment before trying again.') }}
                    @break

                    @case('500')
                        {{ __('Something went wrong on our servers. We\'re working to fix it.') }}
                    @break

                    @case('503')
                        {{ __('Sorry, we are under maintenance. We\'ll be back shortly!') }}
                    @break

                    @default
                        {{ __('An error occurred. Please try again later.') }}
                @endswitch
            </div>

            <!-- Back to Home Button -->
            <a href="{{ url('/') }}" class="btn-home">
                <i class="fas fa-home"></i> {{ __('Back to Home') }}
            </a>
        </div>
    </div>
</body>

</html>
