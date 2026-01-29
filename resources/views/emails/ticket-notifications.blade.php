<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #ff6200, #ff7b00);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }

        .ticket-info {
            background: white;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #ff6200;
        }

        .button {
            display: inline-block;
            background: #ff6200;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 12px;
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .status-open {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status-received {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status-pending_om {
            background: #fff3cd;
            color: #856404;
        }

        .status-in_progress {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-pending_vr {
            background: #fff8e1;
            color: #ff8f00;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending_gm {
            background: #e8f4fd;
            color: #0d47a1;
        }

        .status-closed {
            background: #f8f9fa;
            color: #495057;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <h2>{{ $title }}</h2>
    </div>

    <div class="content">
        <p>Hello {{ $user->name }},</p>

        <p>{{ $message }}</p>

        <div class="ticket-info">
            <h3>Ticket Details:</h3>
            <p><strong>Ticket Number:</strong> #{{ $ticket->ticket_number }}</p>
            <p><strong>Title:</strong> {{ $ticket->title }}</p>
            <p><strong>Status:</strong>
                <span class="badge status-{{ $ticket->status }}">
                    {{ str_replace('_', ' ', $ticket->status) }}
                </span>
            </p>
            <p><strong>Created:</strong> {{ $ticket->created_at->format('d M Y, H:i') }}</p>
            <p><strong>Category:</strong> {{ $ticket->category->name }}</p>
            @if ($ticket->location)
                <p><strong>Location:</strong> {{ $ticket->location->name }}</p>
            @endif
        </div>

        <p>To view more details or take action, please click the button below:</p>

        <a href="{{ route('tickets.show', $ticket->id) }}" class="button">
            View Ticket Details
        </a>

        <p>If you're unable to click the button, copy and paste this URL into your browser:</p>
        <p style="word-break: break-all; color: #666;">
            {{ route('tickets.show', $ticket->id) }}
        </p>
    </div>

    <div class="footer">
        <p>This is an automated notification from {{ config('app.name') }}.</p>
        <p>Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>

</html>
