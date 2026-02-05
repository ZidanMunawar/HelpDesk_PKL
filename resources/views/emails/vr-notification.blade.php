<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }} - Voucher Request</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 25px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header h2 {
            margin: 10px 0 0 0;
            font-size: 18px;
            font-weight: normal;
        }

        .content {
            background: white;
            padding: 30px;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .message-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }

        .details-box {
            background: #e8f4fd;
            padding: 20px;
            border-radius: 6px;
            margin: 25px 0;
            border: 1px solid #b3e0ff;
        }

        .ticket-info,
        .vr-info {
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .ticket-info {
            border-left: 4px solid #007bff;
        }

        .vr-info {
            border-left: 4px solid #28a745;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-admin_approved {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-om_approved {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-gm_approved {
            background: #d4edda;
            color: #155724;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px;
            font-weight: bold;
            text-align: center;
            min-width: 200px;
        }

        .button:hover {
            background: #218838;
        }

        .button-ticket {
            background: #007bff;
        }

        .button-ticket:hover {
            background: #0056b3;
        }

        .button-vr {
            background: #28a745;
        }

        .button-vr:hover {
            background: #218838;
        }

        .url-link {
            word-break: break-all;
            color: #666;
            font-size: 12px;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 12px;
            text-align: center;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .info-table td:first-child {
            font-weight: bold;
            width: 40%;
            color: #555;
        }

        .amount-box {
            background: #f8fff9;
            border: 2px solid #28a745;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }

        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <h2>Voucher Request Notification</h2>
    </div>

    <div class="content">
        <div class="greeting">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
        </div>

        <div class="message-box">
            <p>{{ $messageContent }}</p>
        </div>

        @if ($vr)
            <div class="vr-info">
                <h3 style="margin-top: 0; color: #28a745;">Voucher Request Details:</h3>
                <table class="info-table">
                    <tr>
                        <td>VR Number:</td>
                        <td><strong>#{{ $vr->vr_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td>
                            <span class="badge status-{{ $vr->status }}">
                                {{ str_replace('_', ' ', $vr->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Total Amount:</td>
                        <td><strong>Rp {{ number_format($vr->total_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Created:</td>
                        <td>{{ $vr->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Created By:</td>
                        <td>{{ $vr->creator->name }}</td>
                    </tr>
                    @if ($vr->status == 'admin_approved' && $vr->adminApprover)
                        <tr>
                            <td>Admin Approved:</td>
                            <td>{{ $vr->adminApprover->name }} ({{ $vr->admin_approved_at->format('d M Y, H:i') }})
                            </td>
                        </tr>
                    @endif
                    @if ($vr->status == 'om_approved' && $vr->omApprover)
                        <tr>
                            <td>OM Approved:</td>
                            <td>{{ $vr->omApprover->name }} ({{ $vr->om_approved_at->format('d M Y, H:i') }})</td>
                        </tr>
                    @endif
                    @if ($vr->status == 'gm_approved' && $vr->gmApprover)
                        <tr>
                            <td>GM Approved:</td>
                            <td>{{ $vr->gmApprover->name }} ({{ $vr->gm_approved_at->format('d M Y, H:i') }})</td>
                        </tr>
                    @endif
                </table>

                @if ($vr->items && count($vr->items) > 0)
                    <div style="margin-top: 15px;">
                        <h4 style="color: #555; margin-bottom: 10px;">Items:</h4>
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 8px; text-align: left; border-bottom: 2px solid #ddd;">Item</th>
                                    <th style="padding: 8px; text-align: center; border-bottom: 2px solid #ddd;">Qty
                                    </th>
                                    <th style="padding: 8px; text-align: right; border-bottom: 2px solid #ddd;">Unit
                                        Price</th>
                                    <th style="padding: 8px; text-align: right; border-bottom: 2px solid #ddd;">Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vr->items as $item)
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 8px;">{{ $item->item_name }}</td>
                                        <td style="padding: 8px; text-align: center;">{{ $item->qty }}</td>
                                        <td style="padding: 8px; text-align: right;">Rp
                                            {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                        <td style="padding: 8px; text-align: right;">Rp
                                            {{ number_format($item->qty * $item->unit_price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        <div class="ticket-info">
            <h3 style="margin-top: 0; color: #007bff;">Related Ticket:</h3>
            <table class="info-table">
                <tr>
                    <td>Ticket Number:</td>
                    <td><strong>#{{ $ticket->ticket_number }}</strong></td>
                </tr>
                <tr>
                    <td>Title:</td>
                    <td>{{ $ticket->title }}</td>
                </tr>
                <tr>
                    <td>Status:</td>
                    <td>
                        <span class="badge status-{{ $ticket->status }}">
                            {{ str_replace('_', ' ', $ticket->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>Category:</td>
                    <td>{{ $ticket->category->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Priority:</td>
                    <td>{{ $ticket->priority->name ?? 'N/A' }}</td>
                </tr>
                @if ($ticket->assignedUser)
                    <tr>
                        <td>Technician:</td>
                        <td>{{ $ticket->assignedUser->name }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="button-container">
            @if ($vr)
                <a href="{{ route('vouchers.show', $vr->id) }}" class="button button-vr">
                    View Voucher Details
                </a>
            @endif

            <a href="{{ route('tickets.show', $ticket->id) }}" class="button button-ticket">
                View Ticket Details
            </a>
        </div>

        <div style="margin-top: 20px; font-size: 13px; color: #666;">
            <p><strong>Direct Links:</strong></p>
            @if ($vr)
                <p class="url-link">Voucher Request: {{ route('vouchers.show', $vr->id) }}</p>
            @endif
            <p class="url-link">Ticket: {{ route('tickets.show', $ticket->id) }}</p>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated notification from <strong>{{ config('app.name') }}</strong>.</p>
        <p>Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p style="font-size: 11px; color: #999;">
            Ticket System - Voucher Request Module
        </p>
    </div>
</body>

</html>
