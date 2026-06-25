<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Purchase Request - {{ $vr->vr_number }} | {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
            color: #333;
        }

        .print-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ff6600;
        }

        .header h1 {
            color: #003366;
            font-size: 22px;
        }

        .header p {
            color: #666;
            font-size: 11px;
        }

        .info-row {
            display: flex;
            padding: 6px 0;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            width: 130px;
            font-weight: 600;
            color: #555;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-admin_approved {
            background: #dbeafe;
            color: #2563eb;
        }

        .status-om_approved {
            background: #d1fae5;
            color: #059669;
        }

        .status-gm_approved {
            background: #a7f3d0;
            color: #047857;
        }

        .status-paid {
            background: #064e3b;
            color: white;
        }

        .status-rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .timeline-item {
            display: flex;
            padding: 8px 0;
            border-left: 2px solid #ddd;
            margin-left: 15px;
            padding-left: 20px;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 12px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ddd;
        }

        .timeline-item.approved::before {
            background: #10b981;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #888;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="print-container">
        <div class="header">
            <h1>{{ config('app.name', 'MAINTENANCE SYSTEM') }}</h1>
            <h2>PURCHASE REQUEST</h2>
            <p>Printed: {{ now()->format('d/m/Y H:i:s') }} | By: {{ Auth::user()->name }}</p>
        </div>

        <div class="info-row">
            <div class="info-label">PR Number:</div>
            <div class="info-value"><strong>{{ $vr->vr_number }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span
                    class="status-badge status-{{ $vr->status }}">{{ str_replace('_', ' ', ucfirst($vr->status)) }}</span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Ticket Number:</div>
            <div class="info-value">#{{ $vr->ticket->ticket_number ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Ticket Title:</div>
            <div class="info-value">{{ $vr->ticket->title ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Created By:</div>
            <div class="info-value">{{ $vr->creator->name ?? 'Unknown' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Created At:</div>
            <div class="info-value">{{ $vr->created_at->format('d M Y, H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Notes:</div>
            <div class="info-value">{{ $vr->notes ?: '-' }}</div>
        </div>

        <div style="margin: 20px 0;">
            <h4 style="color: #003366;">Approval Timeline</h4>
            <div class="timeline-item {{ $vr->admin_approved ? 'approved' : '' }}">
                <div><strong>Admin Engineering</strong> {{ $vr->admin_approved ? '✓ Approved' : 'Pending' }}</div>
            </div>
            <div class="timeline-item {{ $vr->om_approved ? 'approved' : '' }}">
                <div><strong>Operation Manager</strong> {{ $vr->om_approved ? '✓ Approved' : 'Pending' }}</div>
            </div>
            <div class="timeline-item {{ $vr->gm_approved ? 'approved' : '' }}">
                <div><strong>General Manager</strong> {{ $vr->gm_approved ? '✓ Approved' : 'Pending' }}</div>
            </div>
        </div>

        <div class="footer">
            <p>This is an official purchase request document from {{ config('app.name') }}.</p>
            <p>Please keep this for your records.</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 24px; background: #ff6600; color: white; border: none; border-radius: 6px; cursor: pointer;">
            <i class="fas fa-print"></i> Print
        </button>
        <button onclick="window.close()"
            style="padding: 10px 24px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>
</body>

</html>
