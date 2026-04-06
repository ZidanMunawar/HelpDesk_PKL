<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | {{ config('app.name') }}</title>
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
            letter-spacing: 1px;
        }

        .header h2 {
            margin: 10px 0 0;
            font-size: 16px;
            font-weight: 500;
            opacity: 0.9;
        }

        .content {
            background: white;
            padding: 30px;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .message-box {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #ff6600;
        }

        .message-box p {
            margin: 0;
            color: #333;
        }

        .ticket-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .ticket-info h3 {
            color: #003366;
            margin: 0 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #ff6600;
            font-size: 16px;
        }

        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            width: 120px;
            font-weight: 600;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-open {
            background: #1565c0;
            color: white;
        }

        .badge-received {
            background: #1565c0;
            color: white;
        }

        .badge-pending_om {
            background: #856404;
            color: white;
        }

        .badge-in_progress {
            background: #0c5460;
            color: white;
        }

        .badge-pending_vr {
            background: #ff8f00;
            color: white;
        }

        .badge-completed {
            background: #155724;
            color: white;
        }

        .badge-pending_gm {
            background: #0d47a1;
            color: white;
        }

        .badge-ready_for_closure {
            background: #0c5460;
            color: white;
        }

        .badge-closed {
            background: #495057;
            color: white;
        }

        .badge-cancelled {
            background: #721c24;
            color: white;
        }

        .priority-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: 600;
            color: white;
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
            transition: all 0.2s ease;
        }

        .button:hover {
            background: #e55a00;
            transform: translateY(-2px);
        }

        .button-secondary {
            background: #003366;
            margin-left: 10px;
        }

        .button-secondary:hover {
            background: #002244;
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

        .footer a:hover {
            text-decoration: underline;
        }

        .action-required {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 12px;
            border-radius: 6px;
            margin: 15px 0;
            text-align: center;
            font-size: 13px;
            font-weight: 600;
        }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, #ff6600, #003366);
            margin: 20px 0;
        }

        @media only screen and (max-width: 480px) {
            body {
                padding: 10px;
            }

            .content {
                padding: 20px;
            }

            .info-row {
                flex-direction: column;
                padding: 10px 0;
            }

            .info-label {
                width: 100%;
                margin-bottom: 4px;
            }

            .button {
                display: block;
                text-align: center;
                margin: 10px 0;
            }

            .button-secondary {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- HEADER WITH HYBRID LOGO -->
        <!-- HEADER DENGAN LOGO HOTEL (Hybrid) -->
        <div class="header">
            <!-- 1. GAMBAR LOGO (untuk email client yang mendukung gambar) -->
            <div style="margin-bottom: 20px;">
                <img src="https://i.ibb.co.com/G4rpCb1P/2-1.png" alt="Harris Festival Citylink Bandung"
                    style="display: block; margin: 0 auto; max-width: 180px; height: auto; border: none;" width="180">
            </div>

            <!-- 2. FALLBACK TEKS (PASTI MUNCUL jika gambar diblokir) -->
            <div style="margin-bottom: 10px; display: none; mso-hide: all;">
                <div
                    style="background: rgba(255, 255, 255, 0.15); padding: 6px 16px; border-radius: 50px; display: inline-block;">
                    <span style="color: #ff6600; font-size: 16px;">🏨</span>
                    <span style="color: white; font-size: 14px; font-weight: 600; letter-spacing: 1px;">
                        HARRIS FESTIVAL CITYLINK
                    </span>
                </div>
            </div>

            <!-- Judul Email -->
            <h1>MAINTENANCE SYSTEM</h1>
            <h2>{{ $title }}</h2>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Hello {{ $user->name }},</strong>
            </div>

            <!-- Message Box -->
            <div class="message-box">
                <p>{{ $messageText }}</p>
            </div>

            <!-- Action Required Notice based on type -->
            @if ($type === 'approval')
                <div class="action-required">
                    ⚠️ <strong>Action Required:</strong> Please review and take action on this request.
                </div>
            @elseif($type === 'assignment')
                <div class="action-required">
                    🔧 <strong>Action Required:</strong> This request has been assigned to you.
                </div>
            @elseif($type === 'check')
                <div class="action-required">
                    ✅ <strong>Action Required:</strong> Please check and confirm the completed work.
                </div>
            @elseif($type === 'rejection')
                <div class="action-required" style="background: #f8d7da; border-color: #f5c6cb; color: #721c24;">
                    ❌ <strong>Action Required:</strong> Your request has been rejected. Please check the reason.
                </div>
            @elseif($type === 'vr_request')
                <div class="action-required">
                    📄 <strong>Action Required:</strong> Purchase Request needs your approval.
                </div>
            @elseif($type === 'closure')
                <div class="action-required" style="background: #d4edda; border-color: #c3e6cb; color: #155724;">
                    ✅ <strong>Request Closed:</strong> This maintenance request has been completed and closed.
                </div>
            @elseif($type === 'cancellation')
                <div class="action-required" style="background: #f8d7da; border-color: #f5c6cb; color: #721c24;">
                    ❌ <strong>Request Cancelled:</strong> This maintenance request has been cancelled.
                </div>
            @endif

            <!-- Ticket Information -->
            <div class="ticket-info">
                <h3>📋 MAINTENANCE REQUEST DETAILS</h3>

                <div class="info-row">
                    <div class="info-label">Request Number:</div>
                    <div class="info-value">
                        <strong>#{{ $ticket->ticket_number }}</strong>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Title:</div>
                    <div class="info-value">{{ $ticket->title }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <span class="badge badge-{{ $ticket->status }}">
                            {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                        </span>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Priority:</div>
                    <div class="info-value">
                        @if ($ticket->priority)
                            <span class="priority-badge" style="background-color: {{ $ticket->priority->color }};">
                                {{ $ticket->priority->name }}
                            </span>
                        @else
                            N/A
                        @endif
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Category:</div>
                    <div class="info-value">{{ $ticket->category->name ?? 'N/A' }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Created By:</div>
                    <div class="info-value">{{ $ticket->user->name ?? 'N/A' }}</div>
                </div>

                <div class="info-row">
                    <div class="info-label">Department:</div>
                    <div class="info-value">{{ $ticket->department->name ?? 'N/A' }}</div>
                </div>

                @if ($ticket->location)
                    <div class="info-row">
                        <div class="info-label">Location:</div>
                        <div class="info-value">{{ $ticket->location->name }}</div>
                    </div>
                @elseif($ticket->location_manual)
                    <div class="info-row">
                        <div class="info-label">Location:</div>
                        <div class="info-value">{{ $ticket->location_manual }}</div>
                    </div>
                @endif

                <div class="info-row">
                    <div class="info-label">Created:</div>
                    <div class="info-value">{{ $ticket->created_at->format('d M Y, H:i') }}</div>
                </div>

                @if ($ticket->due_date)
                    <div class="info-row">
                        <div class="info-label">Due Date:</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($ticket->due_date)->format('d M Y, H:i') }}
                            @if ($ticket->due_date < now() && !in_array($ticket->status, ['completed', 'closed', 'cancelled']))
                                <span style="color: #dc3545; margin-left: 8px;">⚠️ OVERDUE</span>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($ticket->assigned_to && $ticket->assignedUser)
                    <div class="info-row">
                        <div class="info-label">Assigned To:</div>
                        <div class="info-value">{{ $ticket->assignedUser->name ?? 'N/A' }}</div>
                    </div>
                @endif

                @if ($ticket->resolved_at)
                    <div class="info-row">
                        <div class="info-label">Resolved:</div>
                        <div class="info-value">{{ $ticket->resolved_at->format('d M Y, H:i') }}</div>
                    </div>
                @endif

                @if ($ticket->closed_at)
                    <div class="info-row">
                        <div class="info-label">Closed:</div>
                        <div class="info-value">{{ $ticket->closed_at->format('d M Y, H:i') }}</div>
                    </div>
                @endif
            </div>

            <div class="divider"></div>

            <p>To view more details or take action, please click the button below:</p>

            <div style="text-align: center;">
                <a href="{{ route('tickets.show', $ticket->id) }}" class="button">
                    🔍 View Request Details
                </a>
                <a href="{{ route('dashboard') }}" class="button button-secondary">
                    📊 Go to Dashboard
                </a>
            </div>

            <p style="margin-top: 20px; font-size: 12px; color: #666;">
                If you're unable to click the button, copy and paste this URL into your browser:
            </p>
            <p
                style="word-break: break-all; background: #f8f9fa; padding: 10px; border-radius: 6px; font-size: 12px; color: #003366;">
                {{ route('tickets.show', $ticket->id) }}
            </p>
        </div>

        <div class="footer">
            <p>This is an automated notification from <strong>{{ config('app.name') }}</strong>.</p>
            <p>You are receiving this email because you are associated with this maintenance request.</p>
            <p>Please do not reply to this email. For assistance, contact support.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>
                <a href="{{ route('dashboard') }}">Dashboard</a> |
                <a href="{{ route('tickets.index') }}">My Requests</a>
            </p>
        </div>
    </div>
</body>

</html>
