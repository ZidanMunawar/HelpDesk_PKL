<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Requests Report - {{ config('app.name') }}</title>
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
        .ticket-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8px;
        }

        .ticket-table th {
            background: #FF6B35;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-size: 8px;
            font-weight: 600;
        }

        .ticket-table td {
            border: 1px solid #ddd;
            padding: 5px 4px;
            vertical-align: top;
            font-size: 8px;
        }

        .ticket-table tr:nth-child(even) {
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

        .status-ready_for_closure {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-closed {
            background: #e9ecef;
            color: #495057;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .priority-badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 6px;
            font-size: 7px;
            font-weight: 600;
            color: white;
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

        .legend-color.open {
            background: #e3f2fd;
            border: 1px solid #1565c0;
        }

        .legend-color.in_progress {
            background: #d1ecf1;
            border: 1px solid #0c5460;
        }

        .legend-color.pending_vr {
            background: #fff8e1;
            border: 1px solid #ff8f00;
        }

        .legend-color.pending_gm {
            background: #e8f4fd;
            border: 1px solid #0d47a1;
        }

        .legend-color.completed {
            background: #d4edda;
            border: 1px solid #155724;
        }

        .legend-color.closed {
            background: #e9ecef;
            border: 1px solid #495057;
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

        /* ========== ACTION BUTTONS (SCREEN ONLY) ========== */
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

            /* Fix: tidak ada page break di awal */
            .ticket-table {
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

            /* Header tidak diulang di setiap halaman */
            thead {
                display: table-header-group;
            }

            /* Status badge warna tetap */
            .status-badge,
            .priority-badge {
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
                <h1>MAINTENANCE REQUESTS</h1>
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
                    class="value">{{ request('status') == 'pending_vr' ? 'PR Approval' : (request('status') ? ucfirst(str_replace('_', ' ', request('status'))) : 'All') }}</span>
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
        @if ($tickets->count() > 0)
            <table class="ticket-table">
                <thead>
                    <tr>
                        <th width="11%">MR Number</th>
                        <th width="22%">Title</th>
                        <th width="10%">Status</th>
                        <th width="8%">Priority</th>
                        <th width="12%">Category</th>
                        <th width="12%">Department</th>
                        <th width="10%">Location</th>
                        <th width="10%">Created By</th>
                        <th width="5%">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $ticket)
                        @php
                            $statusDisplay =
                                $ticket->status == 'pending_vr'
                                    ? 'PR Approval'
                                    : ucfirst(str_replace('_', ' ', $ticket->status));
                            $priorityColor = $ticket->priority->color ?? '#6c757d';
                        @endphp
                        <tr>
                            <td><strong>{{ $ticket->ticket_number }}</strong></td>
                            <td>{{ Str::limit($ticket->title, 45) }}</td>
                            <td><span class="status-badge status-{{ $ticket->status }}">{{ $statusDisplay }}</span>
                            </td>
                            <td><span class="priority-badge"
                                    style="background-color: {{ $priorityColor }}">{{ $ticket->priority->name ?? 'N/A' }}</span>
                            </td>
                            <td>{{ Str::limit($ticket->category->name ?? 'N/A', 15) }}</td>
                            <td>{{ Str::limit($ticket->department->name ?? 'N/A', 15) }}</td>
                            <td>{{ Str::limit($ticket->location->name ?? ($ticket->location_manual ?? 'N/A'), 12) }}
                            </td>
                            <td>{{ Str::limit($ticket->user->name ?? 'N/A', 12) }}</td>
                            <td>{{ $ticket->created_at->format('d/m/y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- <!-- LEGEND & SUMMARY -->
            <div class="legend-summary-wrapper">
                <div class="legend">
                    <div class="legend-group">
                        <div class="legend-item">
                            <div class="legend-color open"></div><span>Open</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color in_progress"></div><span>In Progress</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color pending_vr"></div><span>PR Approval</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color pending_gm"></div><span>GM Approval</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color completed"></div><span>Completed</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color closed"></div><span>Closed</span>
                        </div>
                    </div>
                    <div class="legend-divider"></div>
                    <div class="legend-group">
                        @php
                            $priorities = \App\Models\Priority::orderBy('level')->get();
                        @endphp
                        @foreach ($priorities as $priority)
                            <div class="legend-item">
                                <i class="fas fa-flag" style="color: {{ $priority->color }}; font-size: 8px;"></i>
                                <span>{{ $priority->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="summary">
                    <div class="summary-item"><span class="label">Total:</span><span
                            class="value">{{ $tickets->count() }}</span></div>
                    <div class="summary-item"><span class="label">Open:</span><span
                            class="value">{{ $tickets->where('status', 'open')->count() }}</span></div>
                    <div class="summary-item"><span class="label">In Prog:</span><span
                            class="value">{{ $tickets->where('status', 'in_progress')->count() }}</span></div>
                    <div class="summary-item"><span class="label">PR App:</span><span
                            class="value">{{ $tickets->where('status', 'pending_vr')->count() }}</span></div>
                    <div class="summary-item"><span class="label">GM App:</span><span
                            class="value">{{ $tickets->where('status', 'pending_gm')->count() }}</span></div>
                    <div class="summary-item"><span class="label">Done:</span><span
                            class="value">{{ $tickets->where('status', 'completed')->count() + $tickets->where('status', 'closed')->count() }}</span>
                    </div>
                </div>
            </div> --}}
        @else
            <div style="text-align: center; padding: 40px; color: #999;">
                <p>No maintenance requests found.</p>
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
