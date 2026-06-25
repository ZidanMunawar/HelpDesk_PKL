<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Requests Report - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
            background: white;
            padding: 10px;
        }

        .print-container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            padding: 10px;
        }

        /* ========== HEADER ========== */
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #FF6B35;
        }

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo-section img {
            max-height: 45px;
            width: auto;
        }

        .logo-fallback {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #FF6B35;
        }

        .logo-fallback i {
            font-size: 30px;
        }

        .logo-fallback span {
            font-size: 14px;
            font-weight: bold;
        }

        .title-section {
            text-align: right;
        }

        .title-section h1 {
            color: #FF6B35;
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 1px;
        }

        .title-section p {
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }

        /* ========== INFO ROW ========== */
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 6px 0;
            border-bottom: 1px solid #e0e0e0;
            flex-wrap: wrap;
            gap: 10px;
        }

        .info-item {
            display: flex;
            align-items: baseline;
            gap: 6px;
            font-size: 8px;
        }

        .info-item .label {
            font-weight: 600;
            color: #FF6B35;
            min-width: 45px;
        }

        /* ========== TABLE ========== */
        .pr-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8px;
        }

        .pr-table th {
            background: #FF6B35;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
            font-weight: 600;
        }

        .pr-table td {
            border: 1px solid #ddd;
            padding: 5px 4px;
            vertical-align: top;
            font-size: 8px;
        }

        .pr-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 8px;
            font-size: 7px;
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

        .approval-icons {
            display: flex;
            gap: 5px;
            align-items: center;
            flex-wrap: wrap;
        }

        .approval-icon {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            font-size: 7px;
        }

        .approval-icon.approved {
            color: #10b981;
        }

        .approval-icon.pending {
            color: #9ca3af;
        }

        /* ========== LEGEND & SUMMARY ========== */
        .legend-summary-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .legend {
            flex: 1.5;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            border: 1px solid #e0e0e0;
        }

        .legend-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .legend-divider {
            width: 1px;
            height: 20px;
            background: #ddd;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 8px;
        }

        .legend-color {
            width: 10px;
            height: 10px;
            border-radius: 2px;
        }

        .legend-color.pending {
            background: #fef3c7;
            border: 1px solid #d97706;
        }

        .legend-color.admin_approved {
            background: #dbeafe;
            border: 1px solid #2563eb;
        }

        .legend-color.om_approved {
            background: #d1fae5;
            border: 1px solid #059669;
        }

        .legend-color.gm_approved {
            background: #a7f3d0;
            border: 1px solid #047857;
        }

        .legend-color.paid {
            background: #064e3b;
        }

        .legend-color.rejected {
            background: #fee2e2;
            border: 1px solid #dc2626;
        }

        .summary {
            flex: 1;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            border: 1px solid #e0e0e0;
        }

        .summary-item {
            display: flex;
            align-items: baseline;
            gap: 5px;
            font-size: 8px;
        }

        .summary-item .label {
            font-weight: 600;
            color: #666;
        }

        .summary-item .value {
            font-weight: 700;
            font-size: 11px;
            color: #FF6B35;
        }

        /* ========== FOOTER ========== */
        .footer {
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #e0e0e0;
        }

        .footer-info {
            text-align: center;
            font-size: 7px;
            color: #888;
        }

        /* ========== ACTION BUTTONS ========== */
        .action-buttons {
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            bottom: 10px;
            z-index: 1000;
        }

        .action-buttons .btn {
            padding: 6px 16px;
            margin: 0 5px;
            border-radius: 6px;
            font-size: 11px;
            cursor: pointer;
            border: none;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #FF6B35;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        /* ========== PRINT STYLES ========== */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }

            .print-container {
                padding: 8px;
            }

            .action-buttons {
                display: none;
            }

            .pr-table {
                page-break-before: avoid;
                page-break-after: avoid;
            }

            .legend-summary-wrapper {
                page-break-before: avoid;
                page-break-inside: avoid;
            }

            .footer {
                page-break-before: avoid;
            }

            thead {
                display: table-header-group;
            }

            .status-badge {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="print-container">
        <!-- HEADER -->
        <div class="print-header">
            <div class="logo-section">
                @php
                    $logoPath = public_path('assets/images/logo.png');
                    $logoUrl = asset('assets/images/logo.png');
                    $hasLogo = file_exists($logoPath);
                @endphp

                @if ($hasLogo)
                    <img src="{{ $logoUrl }}" alt="Company Logo">
                @else
                    <div class="logo-fallback">
                        <i class="fas fa-building"></i>
                        <span>{{ config('app.name', 'HOTEL') }}</span>
                    </div>
                @endif
            </div>
            <div class="title-section">
                <h1>PURCHASE REQUESTS</h1>
                <p>Report Date: {{ now()->format('d M Y') }}</p>
            </div>
        </div>

        <!-- INFO ROW -->
        <div class="info-row">
            <div class="info-item">
                <span class="label">Period:</span>
                <span class="value">
                    @if (request('date_from') || request('date_to'))
                        {{ request('date_from') ?? 'All' }} - {{ request('date_to') ?? 'Now' }}
                    @else
                        All Time
                    @endif
                </span>
            </div>
            <div class="info-item">
                <span class="label">Status:</span>
                <span
                    class="value">{{ request('status') ? ucfirst(str_replace('_', ' ', request('status'))) : 'All' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Printed:</span>
                <span class="value">{{ now()->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-item">
                <span class="label">By:</span>
                <span class="value">{{ Auth::user()->name }}</span>
            </div>
        </div>

        <!-- TABLE -->
        @if ($prs->count() > 0)
            <table class="pr-table">
                <thead>
                    <tr>
                        <th width="10%">PR Number</th>
                        <th width="8%">Ticket</th>
                        <th width="20%">Ticket Title</th>
                        <th width="9%">Status</th>
                        <th width="15%">Notes</th>
                        <th width="9%">Created By</th>
                        <th width="8%">Date</th>
                        <th width="6%">Photos</th>
                        <th width="15%">Approvals</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($prs as $pr)
                        @php
                            $statusDisplay = match ($pr->status) {
                                'pending' => 'Pending',
                                'admin_approved' => 'Admin OK',
                                'om_approved' => 'OM OK',
                                'gm_approved' => 'GM OK',
                                'paid' => 'Paid',
                                'rejected' => 'Rejected',
                                default => ucfirst($pr->status),
                            };
                        @endphp
                        <tr>
                            <td><strong>{{ $pr->vr_number }}</strong></td>
                            <td>#{{ $pr->ticket->ticket_number ?? 'N/A' }}</td>
                            <td>{{ Str::limit($pr->ticket->title ?? 'N/A', 35) }}</td>
                            <td><span class="status-badge status-{{ $pr->status }}">{{ $statusDisplay }}</span></td>
                            <td>{{ Str::limit($pr->notes ?? '-', 40) }}</td>
                            <td>{{ Str::limit($pr->creator->name ?? 'Unknown', 12) }}</td>
                            <td>{{ $pr->created_at->format('d/m/y') }}</td>
                            <td>{{ $pr->attachments->count() }}</td>
                            <td>
                                <div class="approval-icons">
                                    @if ($pr->admin_approved)
                                        <span class="approval-icon approved"><i class="fas fa-check-circle"></i>
                                            A</span>
                                    @else
                                        <span class="approval-icon pending"><i class="fas fa-clock"></i> A</span>
                                    @endif
                                    @if ($pr->om_approved)
                                        <span class="approval-icon approved"><i class="fas fa-check-circle"></i>
                                            OM</span>
                                    @elseif($pr->admin_approved)
                                        <span class="approval-icon pending"><i class="fas fa-clock"></i> OM</span>
                                    @endif
                                    @if ($pr->gm_approved)
                                        <span class="approval-icon approved"><i class="fas fa-check-circle"></i>
                                            GM</span>
                                    @elseif($pr->om_approved)
                                        <span class="approval-icon pending"><i class="fas fa-clock"></i> GM</span>
                                    @endif
                                    @if ($pr->status == 'paid')
                                        <span class="approval-icon approved"><i class="fas fa-money-bill-wave"></i>
                                            $</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- <!-- LEGEND & SUMMARY -->
            <div class="legend-summary-wrapper">
                <div class="legend">
                    <div class="legend-group">
                        <div class="legend-item">
                            <div class="legend-color pending"></div><span>Pending</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color admin_approved"></div><span>Admin OK</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color om_approved"></div><span>OM OK</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color gm_approved"></div><span>GM OK</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color paid"></div><span>Paid</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color rejected"></div><span>Rejected</span>
                        </div>
                    </div>
                    <div class="legend-divider"></div>
                    <div class="legend-group">
                        <div class="legend-item"><i class="fas fa-check-circle"
                                style="color: #10b981; font-size: 8px;"></i><span>Approved</span></div>
                        <div class="legend-item"><i class="fas fa-clock"
                                style="color: #9ca3af; font-size: 8px;"></i><span>Pending</span></div>
                        <div class="legend-item"><i class="fas fa-money-bill-wave"
                                style="color: #064e3b; font-size: 8px;"></i><span>Paid</span></div>
                    </div>
                </div>

                <div class="summary">
                    <div class="summary-item"><span class="label">Total:</span><span
                            class="value">{{ $prs->count() }}</span></div>
                    <div class="summary-item"><span class="label">Pending:</span><span
                            class="value">{{ $prs->where('status', 'pending')->count() }}</span></div>
                    <div class="summary-item"><span class="label">Admin:</span><span
                            class="value">{{ $prs->where('status', 'admin_approved')->count() }}</span></div>
                    <div class="summary-item"><span class="label">OM:</span><span
                            class="value">{{ $prs->where('status', 'om_approved')->count() }}</span></div>
                    <div class="summary-item"><span class="label">GM:</span><span
                            class="value">{{ $prs->where('status', 'gm_approved')->count() }}</span></div>
                    <div class="summary-item"><span class="label">Paid:</span><span
                            class="value">{{ $prs->where('status', 'paid')->count() }}</span></div>
                    <div class="summary-item"><span class="label">Reject:</span><span
                            class="value">{{ $prs->where('status', 'rejected')->count() }}</span></div>
                </div>
            </div> --}}
        @else
            <div style="text-align: center; padding: 40px; color: #999;">
                <p>No purchase requests found.</p>
            </div>
        @endif

        <!-- FOOTER -->
        <div class="footer">
            <div class="footer-info">
                {{ config('app.name') }} - Generated: {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="action-buttons">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print
        </button>
        <button onclick="goBack()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>

</html>
