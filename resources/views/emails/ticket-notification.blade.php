<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>{{ $title }} | {{ config('app.name') }}</title>
    <style>
        /* Reset & Base Styles - Responsive First */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.5;
            color: #1e2a3e;
            background-color: #eef2f5;
            margin: 0;
            padding: 16px;
            -webkit-font-smoothing: antialiased;
        }

        /* Main Container */
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        /* HEADER */
        .header {
            background: linear-gradient(135deg, #1a3c5e 0%, #0f2b44 100%);
            padding: 28px 24px 24px;
            text-align: center;
            border-bottom: 3px solid #f39c12;
        }

        .logo-wrapper {
            margin-bottom: 16px;
        }

        .logo-img {
            display: block;
            margin: 0 auto;
            max-width: 160px;
            height: auto;
            border: none;
        }

        /* Fallback teks jika gambar tidak tampil */
        .logo-fallback {
            display: none;
            background: rgba(255, 255, 255, 0.12);
            padding: 6px 18px;
            border-radius: 60px;
            width: fit-content;
            margin: 0 auto;
        }

        .logo-fallback span {
            color: #f39c12;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 14px;
        }

        .header h1 {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin: 8px 0 4px;
            letter-spacing: -0.2px;
        }

        .header h2 {
            color: rgba(255, 255, 255, 0.85);
            font-size: 16px;
            font-weight: 500;
            margin: 6px 0 0;
        }

        /* Content */
        .content {
            padding: 32px 28px;
            background: #ffffff;
        }

        .greeting {
            font-size: 16px;
            color: #1e2a3e;
            margin-bottom: 20px;
            font-weight: 500;
        }

        /* Message Box */
        .message-box {
            background-color: #f8fafc;
            border-left: 4px solid #f39c12;
            padding: 16px 20px;
            border-radius: 12px;
            margin: 20px 0;
            font-size: 15px;
            color: #1e2a3e;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .message-box p {
            margin: 0;
            line-height: 1.5;
        }

        /* Action Required Cards */
        .action-required {
            background: #fff8e7;
            border: 1px solid #ffe0a3;
            color: #a86800;
            padding: 14px 18px;
            border-radius: 14px;
            margin: 20px 0;
            text-align: left;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-required strong {
            font-weight: 700;
        }

        .action-rejection {
            background: #fef2f0;
            border-color: #f5c2c7;
            color: #b91c1c;
        }

        .action-closure {
            background: #e6f4ea;
            border-color: #b8e0c2;
            color: #166534;
        }

        .ticket-info {
            background: #ffffff;
            border: 1px solid #e9edf2;
            border-radius: 20px;
            padding: 6px 0;
            margin: 28px 0;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .ticket-info h3 {
            background: #f9fbfd;
            color: #1a3c5e;
            margin: 0;
            padding: 18px 24px 12px;
            border-bottom: 1px solid #eef2f8;
            font-size: 17px;
            font-weight: 600;
            letter-spacing: -0.2px;
        }

        /* Info rows */
        .info-row {
            display: flex;
            padding: 12px 24px;
            border-bottom: 1px solid #f0f2f5;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            width: 130px;
            font-weight: 600;
            color: #4b5e77;
            font-size: 13px;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
            color: #1e2a3e;
            font-size: 14px;
            font-weight: 500;
            word-break: break-word;
        }

        /* Badge styles - soft & accessible */
        .badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
            background-color: #eef2ff;
            color: #1e3a8a;
        }

        .badge-open,
        .badge-received {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-pending_om {
            background: #ffedd5;
            color: #b45309;
        }

        .badge-in_progress {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-pending_vr {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge-completed {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-pending_gm {
            background: #fce7f3;
            color: #9d174d;
        }

        .badge-ready_for_closure {
            background: #d1fae5;
            color: #0b5e42;
        }

        .badge-closed {
            background: #e2e8f0;
            color: #334155;
        }

        .badge-cancelled {
            background: #ffe4e2;
            color: #b91c1c;
        }

        /* priority badge clean */
        .priority-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            color: white;
            background-color: #6c757d;
        }

        /* BUTTONS - NO COLOR CLASH (Dark blue + white text) */
        .button-group {
            text-align: center;
            margin: 28px 0 16px;
        }

        .button {
            display: inline-block;
            background-color: #1e4663;
            color: white !important;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s ease;
            text-align: center;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin: 4px 6px;
        }

        .button-secondary {
            background-color: #2c5a7a;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .button:hover {
            background-color: #0f3550;
            transform: translateY(-1px);
            text-decoration: none;
        }

        .button-secondary:hover {
            background-color: #1e4a6a;
        }

        /* Fallback link */
        .fallback-url {
            margin-top: 20px;
            background: #f8fafc;
            padding: 12px 14px;
            border-radius: 14px;
            font-size: 12px;
            color: #2c5f8a;
            word-break: break-all;
            font-family: monospace;
            border: 1px solid #e2e8f0;
        }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, #e2e8f0, #cbd5e1, #e2e8f0);
            margin: 16px 0 20px;
        }

        /* Footer */
        .footer {
            background: #fafcff;
            padding: 24px 20px 20px;
            text-align: center;
            border-top: 1px solid #eef2f8;
        }

        .footer p {
            margin: 6px 0;
            color: #5b6e8c;
            font-size: 12px;
        }

        .footer a {
            color: #1e4663;
            text-decoration: none;
            font-weight: 500;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        /* Responsive tweaks */
        @media only screen and (max-width: 540px) {
            body {
                padding: 10px;
            }

            .content {
                padding: 22px 18px;
            }

            .info-row {
                flex-direction: column;
                padding: 12px 18px;
            }

            .info-label {
                width: 100%;
                margin-bottom: 6px;
                font-size: 12px;
            }

            .info-value {
                width: 100%;
                font-size: 14px;
            }

            .button {
                display: block;
                width: 100%;
                margin: 8px 0;
                box-sizing: border-box;
            }

            .button-group {
                padding: 0;
            }

            .action-required {
                font-size: 13px;
                padding: 12px 14px;
            }

            .header h1 {
                font-size: 20px;
            }

            .ticket-info h3 {
                padding: 14px 18px;
                font-size: 16px;
            }
        }

        @media only screen and (max-width: 380px) {
            .logo-img {
                max-width: 130px;
            }

            .badge,
            .priority-badge {
                font-size: 10px;
                padding: 3px 10px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- HEADER with hybrid logo (improved fallback) -->
        <div class="header">
            <div class="logo-wrapper">
                <!-- Primary logo image -->
                <img src="https://i.ibb.co.com/G4rpCb1P/2-1.png" alt="Harris Festival Citylink Bandung" class="logo-img"
                    width="160" style="display: block; margin: 0 auto; max-width: 160px;">
                <!-- Fallback text (only shown when image fails, but hidden in normal clients) -->
                <div class="logo-fallback" style="display: none; mso-hide: all;">
                    <span>🏨 HARRIS FESTIVAL CITYLINK</span>
                </div>
            </div>
            <h1>MAINTENANCE SYSTEM</h1>
            <h2>{{ $title }}</h2>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Hello {{ $user->name }},</strong>
            </div>

            <!-- Dynamic message box -->
            <div class="message-box">
                <p>{{ $messageText }}</p>
            </div>

            <!-- Action Required based on type (Warna tidak nabrak, soft & jelas) -->
            @if ($type === 'approval')
                <div class="action-required">
                    <span>⚠️</span> <strong>Action Required:</strong> Please review and take action on this request.
                </div>
            @elseif($type === 'assignment')
                <div class="action-required">
                    <span>🔧</span> <strong>Action Required:</strong> This request has been assigned to you.
                </div>
            @elseif($type === 'check')
                <div class="action-required">
                    <span>✅</span> <strong>Action Required:</strong> Please check and confirm the completed work.
                </div>
            @elseif($type === 'rejection')
                <div class="action-required action-rejection">
                    <span>❌</span> <strong>Action Required:</strong> Your request has been rejected. Please check the
                    reason.
                </div>
            @elseif($type === 'vr_request')
                <div class="action-required">
                    <span>📄</span> <strong>Action Required:</strong> Purchase Request needs your approval.
                </div>
            @elseif($type === 'closure')
                <div class="action-required action-closure">
                    <span>✅</span> <strong>Request Closed:</strong> This maintenance request has been completed and
                    closed.
                </div>
            @elseif($type === 'cancellation')
                <div class="action-required action-rejection">
                    <span>🚫</span> <strong>Request Cancelled:</strong> This maintenance request has been cancelled.
                </div>
            @endif

            <!-- Ticket Information Card -->
            <div class="ticket-info">
                <h3>📋 MAINTENANCE REQUEST DETAILS</h3>

                <div class="info-row">
                    <div class="info-label">Request Number:</div>
                    <div class="info-value"><strong>#{{ $ticket->ticket_number }}</strong></div>
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
                            <span class="priority-badge"
                                style="background-color: {{ $ticket->priority->color ?? '#f39c12' }};">
                                {{ $ticket->priority->name }}
                            </span>
                        @else
                            <span class="priority-badge" style="background-color: #95a5a6;">N/A</span>
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
                                <span style="color: #c2410c; font-weight: 600; margin-left: 8px;">⚠️ OVERDUE</span>
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

            <p style="font-size: 14px; margin: 8px 0 12px; color: #2c3e50;">To view more details or take action, please
                click the button below:</p>

            <div class="button-group">
                <a href="{{ route('tickets.show', $ticket->id) }}" class="button">
                    🔍 View Request Details
                </a>
                <a href="{{ route('dashboard') }}" class="button button-secondary">
                    📊 Go to Dashboard
                </a>
            </div>

            <!-- fallback URL -->
            <div class="fallback-url">
                If the button doesn’t work, copy and paste this link into your browser:<br>
                <a href="{{ route('tickets.show', $ticket->id) }}" style="color: #1e4663; text-decoration: underline;">
                    {{ route('tickets.show', $ticket->id) }}
                </a>
            </div>
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
