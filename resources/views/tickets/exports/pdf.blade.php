<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Maintenance Requests Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        /* ========== HEADER (Sesuai Template) ========== */
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #FF6B35;
        }

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo-section img {
            max-height: 60px;
            width: auto;
            object-fit: contain;
        }

        .logo-fallback {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #FF6B35;
        }

        .logo-fallback span {
            font-size: 18px;
            font-weight: bold;
        }

        .title-section {
            text-align: right;
        }

        .title-section h1 {
            color: #FF6B35;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            letter-spacing: 1px;
        }

        .title-section p {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        /* ========== REPORT INFO ========== */
        .report-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 10px;
        }

        .report-info table {
            width: 100%;
        }

        .report-info td {
            padding: 3px 5px;
        }

        .report-info td:first-child {
            width: 120px;
            font-weight: 600;
            color: #555;
        }

        /* Table */
        .ticket-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .ticket-table th {
            background: #1a2b4c;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
            font-weight: 600;
        }

        .ticket-table td {
            border: 1px solid #e0e0e0;
            padding: 6px;
            vertical-align: top;
            font-size: 9px;
        }

        .ticket-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
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

        /* Priority Badge */
        .priority-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 8px;
            font-weight: 600;
            color: white;
        }

        /* Footer */
        .footer {
            margin-top: 25px;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #e0e0e0;
        }

        /* Page break */
        .page-break {
            page-break-before: always;
        }

        /* Summary */
        .summary {
            margin-top: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            display: inline-block;
            width: 100%;
        }

        .summary-item {
            display: inline-block;
            margin-right: 20px;
            font-size: 10px;
        }

        .summary-item strong {
            color: #FF6B35;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <!-- HEADER (Sesuai Template) -->
    <div class="print-header">
        <div class="logo-section">
            @php
                $logoPath = public_path('assets/images/logo.png');
                $logoUrl = 'file://' . $logoPath;
                $hasLogo = file_exists($logoPath);
            @endphp

            @if ($hasLogo)
                <img src="{{ $logoUrl }}" alt="Company Logo">
            @else
                <div class="logo-fallback">
                    <span>{{ config('app.name', 'HOTEL') }}</span>
                </div>
            @endif
        </div>
        <div class="title-section">
            <h1>MAINTENANCE<br>REQUESTS</h1>
            <p>Maintenance Request Report</p>
        </div>
    </div>

    <div class="report-info">
        <table>
            <tr>
                <td>Generated Date:</td>
                <td>{{ now()->format('d F Y, H:i:s') }}</td>
            </tr>
            <tr>
                <td>Total Records:</td>
                <td><strong>{{ $tickets->count() }}</strong> maintenance requests</td>
            </tr>
            @if (request('date_from'))
                <tr>
                    <td>Date Range:</td>
                    <td>{{ request('date_from') }} to {{ request('date_to') ?? 'now' }}</td>
                </tr>
            @endif
            @if (request('status'))
                <tr>
                    <td>Status Filter:</td>
                    <td>{{ request('status') == 'pending_vr' ? 'PR Approval' : ucfirst(str_replace('_', ' ', request('status'))) }}
                    </td>
                </tr>
            @endif
        </table>
    </div>

    @if ($tickets->count() > 0)
        <table class="ticket-table">
            <thead>
                <tr>
                    <th width="12%">MR Number</th>
                    <th width="20%">Title</th>
                    <th width="10%">Status</th>
                    <th width="8%">Priority</th>
                    <th width="12%">Category</th>
                    <th width="12%">Department</th>
                    <th width="10%">Location</th>
                    <th width="10%">Created By</th>
                    <th width="6%">Date</th>
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
                        <td>{{ Str::limit($ticket->title, 50) }}</td>
                        <td><span class="status-badge status-{{ $ticket->status }}">{{ $statusDisplay }}</span></td>
                        <td><span class="priority-badge"
                                style="background-color: {{ $priorityColor }}">{{ $ticket->priority->name ?? 'N/A' }}</span>
                        </td>
                        <td>{{ $ticket->category->name ?? 'N/A' }}</td>
                        <td>{{ $ticket->department->name ?? 'N/A' }}</td>
                        <td>{{ $ticket->location->name ?? ($ticket->location_manual ?? 'N/A') }}</td>
                        <td>{{ Str::limit($ticket->user->name ?? 'N/A', 15) }}</td>
                        <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary">
            <div class="summary-item">
                <strong>{{ $tickets->where('status', 'open')->count() }}</strong> Open
            </div>
            <div class="summary-item">
                <strong>{{ $tickets->where('status', 'in_progress')->count() }}</strong> In Progress
            </div>
            <div class="summary-item">
                <strong>{{ $tickets->where('status', 'pending_vr')->count() }}</strong> PR Approval
            </div>
            <div class="summary-item">
                <strong>{{ $tickets->where('status', 'pending_gm')->count() }}</strong> GM Approval
            </div>
            <div class="summary-item">
                <strong>{{ $tickets->where('status', 'completed')->count() }}</strong> Completed
            </div>
            <div class="summary-item">
                <strong>{{ $tickets->where('status', 'closed')->count() }}</strong> Closed
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 50px; color: #999;">
            <p>No maintenance requests found matching the criteria.</p>
        </div>
    @endif

    <div class="footer">
        Generated by {{ Auth::user()->name }} ({{ Auth::user()->role }})<br>
        {{ config('app.name') }} &copy; {{ date('Y') }} - All Rights Reserved
    </div>
</body>

</html>
