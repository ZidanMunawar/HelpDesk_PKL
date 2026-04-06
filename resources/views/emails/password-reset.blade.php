{{-- resources/views/emails/password-reset.blade.php --}}
{{-- Template tunggal untuk semua reset password --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #003366 0%, #002244 100%);
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            color: white;
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #ff6600;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }

        .button:hover {
            background: #cc5200;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }

        .warning {
            color: #dc3545;
            font-size: 13px;
            margin-top: 20px;
            padding: 10px;
            background: #f8d7da;
            border-radius: 5px;
        }

        .info-box {
            background: #e6f0ff;
            border-left: 4px solid #003366;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            background: #ff6600;
            color: white;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>

        <div class="content">
            <h2>Hello, {{ $user->name }}!</h2>

            @if (isset($source) && $source === 'profile')
                <div class="info-box">
                    <strong>🔐 Password Reset Request from Profile</strong><br>
                    You requested to reset your password from your profile page.
                </div>
            @else
                <div class="info-box">
                    <strong>🔐 Password Reset Request</strong><br>
                    You requested to reset your password from the login page.
                </div>
            @endif

            <p>Click the button below to reset your password:</p>

            <p style="text-align: center;">
                <a href="{{ $resetLink }}" class="button">Reset Your Password</a>
            </p>

            <p><strong>⏰ This password reset link will expire in 60 minutes.</strong></p>

            <p>If you did not request a password reset, please ignore this email or contact support if you have
                concerns.</p>

            <div class="warning">
                <strong>⚠️ Security Notice:</strong> Never share this link with anyone. Our staff will never ask for
                your password.
            </div>

            <p style="margin-top: 30px;">
                Best regards,<br>
                {{ config('app.name') }} Team
            </p>

            <p style="font-size: 12px; color: #6c757d; margin-top: 30px; word-break: break-all;">
                If you're having trouble clicking the button, copy and paste the URL below:<br>
                <span style="color: #003366;">{{ $resetLink }}</span>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>
