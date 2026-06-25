<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>{{ $title }} - Purchase Request</title>
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

        /* Action Required based on PR status */
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

        .action-approved {
            background: #e6f4ea;
            border-color: #b8e0c2;
            color: #166534;
        }

        .action-rejected {
            background: #fef2f0;
            border-color: #f5c2c7;
            color: #b91c1c;
        }

        /* Info Cards */
        .info-card {
            background: #ffffff;
            border: 1px solid #e9edf2;
            border-radius: 20px;
            margin: 28px 0;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .info-card h3 {
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

        /* Badge styles - PR Status */
        .badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .badge-pending {
            background: #ffedd5;
            color: #b45309;
        }

        .badge-admin_approved {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-om_approved {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-gm_approved {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-paid {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-rejected {
            background: #ffe4e2;
            color: #b91c1c;
        }

        /* Ticket badge */
        .badge-open,
        .badge-received {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-in_progress {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-completed {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-closed {
            background: #e2e8f0;
            color: #334155;
        }

        /* Photos info */
        .photos-info {
            background: #f8fafc;
            padding: 12px 20px;
            margin: 12px 24px 20px;
            border-radius: 12px;
            font-size: 13px;
            color: #4b5e77;
            text-align: center;
            border: 1px dashed #cbd5e1;
        }

        .photos-info i {
            font-style: normal;
            display: inline-block;
        }

        /* BUTTONS */
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
        }

        .button:hover {
            background-color: #0f3550;
            transform: translateY(-1px);
            text-decoration: none;
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

        /* Responsive */
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
            }

            .button {
                display: block;
                width: 100%;
                margin: 8px 0;
            }

            .header h1 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- HEADER -->
        <div class="header">
            <div class="logo-wrapper">
                <img src="https://i.ibb.co.com/G4rpCb1P/2-1.png" alt="Harris Festival Citylink Bandung" class="logo-img"
                    width="160" style="display: block; margin: 0 auto; max-width: 160px;">
                <div class="logo-fallback" style="display: none; mso-hide: all;">
                    <span>🏨 HARRIS FESTIVAL CITYLINK</span>
                </div>
            </div>
            <h1>MAINTENANCE SYSTEM</h1>
            <h2>{{ $title }} - Purchase Request</h2>
        </div>

        <div class="content">
            <div class="greeting">
                <strong>Hello {{ $user->name }},</strong>
            </div>

            <div class="message-box">
                <p>{{ $messageContent }}</p>
            </div>

            <!-- Action Required based on PR status -->
            @if ($vr && $vr->status == 'pending')
                <div class="action-required">
                    <span>⏳</span> <strong>Pending Approval:</strong> This purchase request requires your review and
                    action.
                </div>
            @elseif ($vr && $vr->status == 'admin_approved')
                <div class="action-required action-approved">
                    <span>✅</span> <strong>Admin Approved:</strong> This request has been approved by Admin and is
                    waiting for OM approval.
                </div>
            @elseif ($vr && $vr->status == 'om_approved')
                <div class="action-required action-approved">
                    <span>✅</span> <strong>OM Approved:</strong> This request has been approved by OM and is waiting for
                    GM approval.
                </div>
            @elseif ($vr && $vr->status == 'gm_approved')
                <div class="action-required action-approved">
                    <span>💰</span> <strong>GM Approved:</strong> Purchase request is fully approved and ready for
                    payment processing.
                </div>
            @elseif ($vr && $vr->status == 'paid')
                <div class="action-required action-approved">
                    <span>💵</span> <strong>Payment Completed:</strong> This purchase request has been marked as paid.
                </div>
            @elseif ($vr && $vr->status == 'rejected')
                <div class="action-required action-rejected">
                    <span>❌</span> <strong>Request Rejected:</strong> Your purchase request has been rejected. Please
                    contact support for more information.
                </div>
            @endif

            <!-- Purchase Request Details Card -->
            @if ($vr)
                <div class="info-card">
                    <h3>📄 PURCHASE REQUEST DETAILS</h3>

                    <div class="info-row">
                        <div class="info-label">PR Number:</div>
                        <div class="info-value"><strong>#{{ $vr->vr_number }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div class="info-value">
                            <span class="badge badge-{{ $vr->status }}">
                                {{ str_replace('_', ' ', ucfirst($vr->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Created:</div>
                        <div class="info-value">{{ $vr->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Created By:</div>
                        <div class="info-value">{{ $vr->creator->name ?? 'N/A' }}</div>
                    </div>

                    <!-- Notes jika ada -->
                    @if ($vr->notes)
                        <div class="info-row">
                            <div class="info-label">Notes:</div>
                            <div class="info-value">{{ Str::limit($vr->notes, 100) }}</div>
                        </div>
                    @endif

                    <!-- Photos info -->
                    <div class="photos-info">
                        <i class="fas fa-camera"></i> 📷 {{ $vr->attachments->count() }} photo(s) attached
                    </div>

                    @if ($vr->status == 'admin_approved' && $vr->adminApprover)
                        <div class="info-row">
                            <div class="info-label">Admin Approved:</div>
                            <div class="info-value">{{ $vr->adminApprover->name }}
                                ({{ $vr->admin_approved_at->format('d M Y, H:i') }})</div>
                        </div>
                    @endif
                    @if ($vr->status == 'om_approved' && $vr->omApprover)
                        <div class="info-row">
                            <div class="info-label">OM Approved:</div>
                            <div class="info-value">{{ $vr->omApprover->name }}
                                ({{ $vr->om_approved_at->format('d M Y, H:i') }})</div>
                        </div>
                    @endif
                    @if ($vr->status == 'gm_approved' && $vr->gmApprover)
                        <div class="info-row">
                            <div class="info-label">GM Approved:</div>
                            <div class="info-value">{{ $vr->gmApprover->name }}
                                ({{ $vr->gm_approved_at->format('d M Y, H:i') }})</div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Related Ticket Card -->
            <div class="info-card">
                <h3>🔧 RELATED MAINTENANCE TICKET</h3>

                <div class="info-row">
                    <div class="info-label">Ticket Number:</div>
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
                    <div class="info-label">Category:</div>
                    <div class="info-value">{{ $ticket->category->name ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Priority:</div>
                    <div class="info-value">
                        @if ($ticket->priority)
                            <span class="badge"
                                style="background-color: {{ $ticket->priority->color ?? '#f39c12' }}; color: white;">
                                {{ $ticket->priority->name }}
                            </span>
                        @else
                            <span class="badge" style="background-color: #95a5a6; color: white;">N/A</span>
                        @endif
                    </div>
                </div>
                @if ($ticket->assignedUser)
                    <div class="info-row">
                        <div class="info-label">Assigned To:</div>
                        <div class="info-value">{{ $ticket->assignedUser->name }}</div>
                    </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Created:</div>
                    <div class="info-value">{{ $ticket->created_at->format('d M Y, H:i') }}</div>
                </div>
            </div>

            <div class="divider"></div>

            <p style="font-size: 14px; margin: 8px 0 12px; color: #2c3e50;">To view more details or take action, please
                click the button below:</p>

            <div class="button-group">
                @if ($vr)
                    <a href="{{ route('voucher-requests.show', $vr->id) }}" class="button">
                        📄 View Purchase Request
                    </a>
                @endif
                <a href="{{ route('tickets.show', $ticket->id) }}" class="button button-secondary">
                    🔍 View Ticket Details
                </a>
                <a href="{{ route('dashboard') }}" class="button button-secondary">
                    📊 Go to Dashboard
                </a>
            </div>

            <!-- fallback URLs -->
            <div class="fallback-url">
                If the buttons don't work, copy and paste these links into your browser:<br>
                @if ($vr)
                    Purchase Request: <a href="{{ route('voucher-requests.show', $vr->id) }}"
                        style="color: #1e4663; text-decoration: underline;">
                        {{ route('voucher-requests.show', $vr->id) }}
                    </a><br>
                @endif
                Ticket: <a href="{{ route('tickets.show', $ticket->id) }}"
                    style="color: #1e4663; text-decoration: underline;">
                    {{ route('tickets.show', $ticket->id) }}
                </a>
            </div>
        </div>

        <div class="footer">
            <p>This is an automated notification from <strong>{{ config('app.name') }}</strong>.</p>
            <p>You are receiving this email because you are associated with this purchase request or maintenance ticket.
            </p>
            <p>Please do not reply to this email. For assistance, contact support.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>
                <a href="{{ route('dashboard') }}">Dashboard</a> |
                <a href="{{ route('tickets.index') }}">My Requests</a> |
                <a href="{{ route('voucher-requests.index') }}">Purchase Requests</a>
            </p>
            <p style="font-size: 11px; color: #999;">
                Purchase Request Module - Maintenance System
            </p>
        </div>
    </div>
</body>

</html>
