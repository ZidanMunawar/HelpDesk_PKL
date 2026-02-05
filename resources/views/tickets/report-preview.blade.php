<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Report Preview - {{ $ticket->ticket_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
        }

        .report-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        /* Header */
        .report-header {
            background: linear-gradient(135deg, #ff6200, #ff7b00);
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
        }

        .report-header h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .report-header .ticket-number {
            font-size: 16px;
            opacity: 0.9;
            background: rgba(255, 255, 255, 0.2);
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            margin-top: 10px;
        }

        .report-status {
            position: absolute;
            top: 20px;
            right: 20px;
            transform: rotate(15deg);
            background: rgba(255, 255, 255, 0.9);
            color: #ff6200;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        /* Section */
        .report-section {
            padding: 25px;
            border-bottom: 1px solid #eee;
        }

        .report-section:last-child {
            border-bottom: none;
        }

        .section-title {
            color: #ff6200;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ff6200;
        }

        .section-title i {
            font-size: 20px;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-label {
            font-size: 12px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 15px;
            color: #333;
            font-weight: 500;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #ff6200;
        }

        /* Description Box */
        .description-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px solid #e9ecef;
            line-height: 1.8;
            font-size: 14px;
            color: #444;
        }

        /* Signature Grid */
        .signature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .signature-item {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .signature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .signature-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 15px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .signature-image {
            width: 100%;
            max-width: 120px;
            height: 60px;
            object-fit: contain;
            margin: 0 auto 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
            padding: 10px;
        }

        .signature-details {
            font-size: 12px;
            color: #666;
            line-height: 1.4;
        }

        .signature-details strong {
            display: block;
            color: #333;
            font-size: 14px;
            margin-bottom: 5px;
        }

        /* VR Section */
        .vr-section {
            background: #fff8e1;
            border: 1px solid #ffe082;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }

        .vr-title {
            color: #ff8f00;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .vr-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
        }

        .vr-table th {
            background: #ffecb3;
            color: #333;
            padding: 10px;
            text-align: left;
            font-weight: 600;
        }

        .vr-table td {
            padding: 10px;
            border-bottom: 1px solid #ffe082;
        }

        .vr-total {
            text-align: right;
            font-weight: 700;
            color: #333;
            margin-top: 15px;
            font-size: 14px;
        }

        /* Follow-up Section */
        .follow-up-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .follow-up-item {
            padding: 15px;
            background: #e8f4fd;
            border-radius: 10px;
            border-left: 4px solid #17a2b8;
        }

        .follow-up-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .follow-up-user {
            font-weight: 600;
            color: #333;
        }

        .follow-up-date {
            font-size: 12px;
            color: #666;
        }

        .follow-up-text {
            color: #444;
            line-height: 1.5;
            font-size: 13px;
        }

        /* Footer */
        .report-footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #e9ecef;
        }

        .report-footer i {
            color: #ff6200;
            margin: 0 5px;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .report-container {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
                font-size: 14px;
            }

            .report-header {
                padding: 20px 15px;
            }

            .report-header h1 {
                font-size: 20px;
            }

            .report-status {
                position: relative;
                top: 0;
                right: 0;
                transform: none;
                display: inline-block;
                margin-top: 10px;
            }

            .report-section {
                padding: 15px;
            }

            .section-title {
                font-size: 16px;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .signature-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .vr-table {
                font-size: 12px;
            }

            .vr-table th,
            .vr-table td {
                padding: 8px 5px;
            }

            .follow-up-item {
                padding: 12px;
            }
        }

        @media (max-width: 480px) {
            .report-header {
                padding: 15px 10px;
            }

            .report-header h1 {
                font-size: 18px;
            }

            .report-section {
                padding: 12px;
            }

            .section-title {
                font-size: 15px;
            }

            .info-value {
                font-size: 14px;
            }

            .vr-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            min-width: 150px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff6200, #ff7b00);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #e65500, #ff6b00);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 98, 0, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        /* Loading State */
        .loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #ff6200;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Back to App Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #f8f9fa;
            color: #333;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .back-button:hover {
            background: #e9ecef;
            transform: translateX(-5px);
        }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ccc;
        }

        .empty-state p {
            font-size: 14px;
        }

        /* Fix for iOS Safari */
        @supports (-webkit-touch-callout: none) {
            body {
                -webkit-overflow-scrolling: touch;
            }

            .btn {
                min-height: 44px;
                /* Minimum touch target size for iOS */
            }
        }

        /* Android Chrome Fix */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            .report-container {
                -webkit-backface-visibility: hidden;
            }
        }
    </style>
</head>

<body>
    <!-- Back Button (Hanya di preview standalone) -->
    @if (request()->has('standalone'))
        <a href="javascript:window.history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to App
        </a>
    @endif

    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <h1>MAINTENANCE REQUEST REPORT</h1>
            <div class="ticket-number">{{ $ticket->ticket_number }}</div>

            @php
                $statusDisplay = [
                    'open' => 'Open',
                    'received' => 'Received',
                    'pending_om' => 'OM Approval',
                    'in_progress' => 'In Progress',
                    'pending_vr' => 'VR Approval',
                    'completed' => 'Completed',
                    'pending_gm' => 'GM Approval',
                    'ready_for_closure' => 'Ready for Closure',
                    'closed' => 'Closed',
                    'cancelled' => 'Cancelled',
                ];
            @endphp
            <div class="report-status">
                {{ $statusDisplay[$ticket->status] ?? $ticket->status }}
            </div>
        </div>

        <!-- Ticket Information -->
        <div class="report-section">
            <h3 class="section-title">
                <i class="fas fa-info-circle"></i> TICKET INFORMATION
            </h3>

            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">CREATED BY</span>
                    <div class="info-value">{{ $ticket->user->name ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <span class="info-label">DEPARTMENT</span>
                    <div class="info-value">{{ $ticket->department->name ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <span class="info-label">LOCATION</span>
                    <div class="info-value">
                        @if ($ticket->location)
                            {{ $ticket->location->name }}
                            @if ($ticket->location->floor_number)
                                - Floor {{ $ticket->location->floor_number }}
                            @endif
                        @elseif($ticket->location_manual)
                            {{ $ticket->location_manual }}
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-label">CATEGORY</span>
                    <div class="info-value">{{ $ticket->category->name ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <span class="info-label">PRIORITY</span>
                    <div class="info-value">{{ $ticket->priority->name ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <span class="info-label">CREATED DATE</span>
                    <div class="info-value">{{ $ticket->created_at->format('d F Y, H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="report-section">
            <h3 class="section-title">
                <i class="fas fa-file-alt"></i> REQUEST DETAILS
            </h3>

            <div class="description-box">
                <strong>Title:</strong> {{ $ticket->title }}<br><br>
                <strong>Description:</strong><br>
                {!! nl2br(e($ticket->description)) !!}
            </div>
        </div>

        <!-- Assignment -->
        @if ($ticket->assigned_to)
            <div class="report-section">
                <h3 class="section-title">
                    <i class="fas fa-user-cog"></i> ASSIGNMENT
                </h3>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">TECHNICIAN</span>
                        <div class="info-value">{{ $ticket->assignedUser->name ?? '-' }}</div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">ASSIGNED DATE</span>
                        <div class="info-value">
                            @if ($ticket->approval && $ticket->approval->om_approved_at)
                                {{ $ticket->approval->om_approved_at->format('d F Y, H:i') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">DUE DATE</span>
                        <div class="info-value">
                            @if ($ticket->due_date)
                                {{ \Carbon\Carbon::parse($ticket->due_date)->format('d F Y, H:i') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">RESOLVED DATE</span>
                        <div class="info-value">
                            @if ($ticket->resolved_at)
                                {{ $ticket->resolved_at->format('d F Y, H:i') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Signatures -->
        @if (count($signatures) > 0)
            <div class="report-section">
                <h3 class="section-title">
                    <i class="fas fa-signature"></i> SIGNATURES
                </h3>

                <div class="signature-grid">
                    @for ($stage = 1; $stage <= 8; $stage++)
                        @php
                            $stageLabels = [
                                1 => 'Requested by',
                                2 => 'Received by',
                                3 => 'Approved by OM',
                                4 => 'Completed by',
                                5 => 'Checked by',
                                6 => 'Approved by GM',
                                7 => 'Closed by',
                            ];
                        @endphp

                        <div class="signature-item">
                            <div class="signature-label">{{ $stageLabels[$stage] ?? 'Signature' }}</div>

                            @if (isset($signatures[$stage]) && $signatures[$stage]->signature_path)
                                @if (Storage::disk('public')->exists($signatures[$stage]->signature_path))
                                    <img src="{{ Storage::url($signatures[$stage]->signature_path) }}" alt="Signature"
                                        class="signature-image">
                                @else
                                    <div class="empty-signature">-</div>
                                @endif
                                <div class="signature-details">
                                    <strong>{{ $signatures[$stage]->user->name ?? '-' }}</strong>
                                    <div>{{ $signatures[$stage]->user->role ?? '-' }}</div>
                                    <div>{{ $signatures[$stage]->signed_at->format('d M Y, H:i') }}</div>
                                </div>
                            @else
                                <div class="empty-signature" style="padding: 20px; color: #999; font-style: italic;">-
                                </div>
                                <div class="signature-details">
                                    <strong>-</strong>
                                    <div>-</div>
                                    <div>-</div>
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        @endif

        <!-- Voucher Request (PR) -->
        @if ($vrData)
            <div class="report-section">
                <h3 class="section-title">
                    <i class="fas fa-file-invoice-dollar"></i> PURCHASE REQUEST (PR)
                </h3>

                <div class="vr-section">
                    <div class="vr-title">PR #{{ $vrData->vr_number }}</div>

                    <table class="vr-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th>Vendor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vrItems as $item)
                                <tr>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->qty * $item->unit_price, 0, ',', '.') }}</td>
                                    <td>{{ $item->vendor ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="vr-total">
                        Total Amount: Rp {{ number_format($totalVRAmount, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Follow-up Comments -->
        @if ($followUpComments->count() > 0)
            <div class="report-section">
                <h3 class="section-title">
                    <i class="fas fa-comments"></i> FOLLOW-UP
                </h3>

                <div class="follow-up-list">
                    @foreach ($followUpComments as $comment)
                        <div class="follow-up-item">
                            <div class="follow-up-header">
                                <span class="follow-up-user">{{ $comment->user->name ?? 'System' }}</span>
                                <span class="follow-up-date">{{ $comment->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="follow-up-text">
                                {!! nl2br(e(Str::limit($comment->comment, 200))) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Footer -->
        <div class="report-footer">
            <div>
                <i class="fas fa-clock"></i> Report generated: {{ now()->format('d F Y, H:i') }}
            </div>
            <div style="margin-top: 5px;">
                <i class="fas fa-ticket-alt"></i> {{ $ticket->ticket_number }}
            </div>
        </div>
    </div>

    <!-- Action Buttons (Hanya di preview standalone) -->
    @if (request()->has('standalone'))
        <div class="action-buttons no-print">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
            <a href="{{ route('tickets.report.download', $ticket->id) }}" class="btn btn-success">
                <i class="fas fa-download"></i> Download PDF
            </a>
            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Ticket
            </a>
        </div>
    @endif

    <script>
        // Mobile detection and optimization
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            // Add mobile-specific optimizations
            document.body.style.padding = '5px';

            // Prevent zoom on double-tap
            document.addEventListener('touchstart', function(event) {
                if (event.touches.length > 1) {
                    event.preventDefault();
                }
            }, {
                passive: false
            });

            // Better touch handling for buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(btn => {
                btn.style.minHeight = '44px';
                btn.style.padding = '12px 20px';
            });
        }

        // Handle back button for PWA/standalone
        if (window.matchMedia('(display-mode: standalone)').matches) {
            // Running as PWA
            document.querySelector('.back-button').href = '/tickets';
        }

        // Print optimization
        document.addEventListener('DOMContentLoaded', function() {
            const printBtn = document.querySelector('button[onclick="window.print()"]');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    setTimeout(function() {
                        window.close(); // Close print dialog automatically (optional)
                    }, 1000);
                });
            }
        });
    </script>
</body>

</html>
