<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }

        .container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: #003366;
            color: white;
            padding: 25px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .header h2 {
            margin: 10px 0 0;
            font-size: 16px;
            font-weight: 500;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            background: #ff6600;
            color: white;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
        }

        .button:hover {
            background: #e55a00;
        }

        .alert-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px 16px;
            margin: 20px 0;
            border-radius: 6px;
            font-size: 13px;
        }

        .alert-warning ul {
            margin: 8px 0 0 20px;
            padding: 0;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }

        .footer p {
            margin: 5px 0;
            color: #666;
            font-size: 11px;
        }

        .footer a {
            color: #ff6600;
            text-decoration: none;
        }

        .url-box {
            word-break: break-all;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            font-size: 12px;
            color: #003366;
            margin-top: 15px;
        }

        @media only screen and (max-width: 480px) {
            body {
                padding: 10px;
            }

            .content {
                padding: 20px;
            }

            .button {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name', 'MAINTENANCE SYSTEM') }}</h1>
            <h2>Password Reset Request</h2>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Hello {{ $user->name }},</strong>
            </div>

            <p>You are receiving this email because we received a password reset request for your account.</p>

            <div style="text-align: center;">
                <a href="{{ $resetLink }}" class="button">Reset Password</a>
            </div>

            <div class="alert-warning">
                <strong>⚠️ Security Notice:</strong>
                <ul>
                    <li>This password reset link will expire in <strong>60 minutes</strong>.</li>
                    <li>If you did not request a password reset, no further action is required.</li>
                    <li>Never share this link with anyone.</li>
                </ul>
            </div>

            <p style="font-size: 12px; color: #666; margin-top: 20px;">
                If you're unable to click the button, copy and paste this URL into your browser:
            </p>
            <div class="url-box">
                {{ $resetLink }}
            </div>
        </div>

        <div class="footer">
            <p>This is an automated notification from <strong>{{ config('app.name') }}</strong>.</p>
            <p>If you did not request this, please ignore this email.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
