<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>LAPORAN - {{ $ticket->ticket_number }}</title>
    <style>
        @page {
            margin: 5mm;
            size: A5 landscape;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        /* HEADER - COMPACT */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ff6200;
            padding-bottom: 3px;
            margin-bottom: 5px;
        }

        .hotel-info {
            flex: 1;
        }

        .hotel-name {
            font-size: 12px;
            font-weight: bold;
            color: #ff6200;
            margin: 0;
        }

        .ticket-info {
            text-align: right;
        }

        .ticket-number {
            font-size: 11px;
            font-weight: bold;
            margin: 0;
        }

        .ticket-date {
            font-size: 8px;
            color: #666;
            margin: 0;
        }

        /* TWO COLUMN LAYOUT */
        .two-columns {
            display: grid;
            grid-template-columns: 60% 40%;
            gap: 5px;
            flex: 1;
        }

        .left-column,
        .right-column {
            display: flex;
            flex-direction: column;
        }

        /* INFO CARDS */
        .info-card {
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 4px;
            margin-bottom: 4px;
        }

        .info-label {
            font-weight: bold;
            font-size: 7pt;
            color: #666;
        }

        .info-value {
            font-size: 8pt;
        }

        /* SIGNATURE GRID - 2x2 */
        .signature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 4px;
            margin-top: 4px;
        }

        .signature-box {
            border: 1px solid #ddd;
            border-radius: 2px;
            padding: 3px;
            text-align: center;
            font-size: 7pt;
            min-height: 35px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 7pt;
        }

        .signature-role {
            font-size: 6pt;
            color: #666;
        }

        .signature-date {
            font-size: 6pt;
            color: #999;
        }

        /* SECTION TITLES */
        .section-title {
            font-size: 9pt;
            font-weight: bold;
            color: #ff6200;
            margin: 4px 0 2px 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 1px;
        }

        /* COMPACT TABLE */
        .compact-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
        }

        .compact-table th {
            background: #ff6200;
            color: white;
            font-weight: bold;
            padding: 2px 3px;
            text-align: left;
        }

        .compact-table td {
            padding: 2px 3px;
            border: 1px solid #ddd;
        }

        /* STATUS INDICATOR */
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 7pt;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .status-open .status-dot {
            background: #1565c0;
        }

        .status-received .status-dot {
            background: #1565c0;
        }

        .status-pending_om .status-dot {
            background: #856404;
        }

        .status-in_progress .status-dot {
            background: #0c5460;
        }

        .status-pending_vr .status-dot {
            background: #ff8f00;
        }

        .status-completed .status-dot {
            background: #155724;
        }

        .status-pending_gm .status-dot {
            background: #0d47a1;
        }

        .status-closed .status-dot {
            background: #495057;
        }

        .status-cancelled .status-dot {
            background: #721c24;
        }

        /* FOOTER */
        .footer {
            margin-top: 5px;
            padding-top: 3px;
            border-top: 1px solid #ddd;
            font-size: 6pt;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- HEADER -->
        <div class="header">
            <div class="hotel-info">
                <div class="hotel-name">MAINTENANCE REQUEST</div>
                <div style="font-size: 8px;">{{ $hotelName }} HOTEL</div>
            </div>
            <div class="ticket-info">
                <div class="ticket-number">#{{ $ticket->ticket_number }}</div>
                <div class="ticket-date">{{ $createdAt }}</div>
            </div>
        </div>

        <!-- TWO COLUMN CONTENT -->
        <div class="two-columns">
            <!-- LEFT COLUMN -->
            <div class="left-column">
                <!-- BASIC INFO -->
                <div class="info-card">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px;">
                        <div>
                            <div class="info-label">Dari:</div>
                            <div class="info-value">{{ $ticket->user->name }}</div>
                        </div>
                        <div>
                            <div class="info-label">Dept:</div>
                            <div class="info-value">{{ $ticket->department->name ?? '-' }}</div>
                        </div>
                    </div>

                    <div style="margin-top: 3px;">
                        <div class="info-label">Judul:</div>
                        <div class="info-value">{{ Str::limit($ticket->title, 40) }}</div>
                    </div>
                </div>

                <!-- DESCRIPTION -->
                <div class="info-card" style="flex: 1;">
                    <div class="section-title">DESKRIPSI</div>
                    <div style="font-size: 8pt; max-height: 60px; overflow: hidden;">
                        {!! nl2br(e(Str::limit($ticket->description, 200))) !!}
                    </div>
                </div>

                <!-- LOCATION & CATEGORY -->
                <div class="info-card">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px;">
                        <div>
                            <div class="info-label">Lokasi:</div>
                            <div class="info-value">{{ Str::limit($locationText, 25) }}</div>
                        </div>
                        <div>
                            <div class="info-label">Kategori:</div>
                            <div class="info-value">{{ $ticket->category->name }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="right-column">
                <!-- STATUS & PRIORITY -->
                <div class="info-card">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4px;">
                        <div>
                            <div class="info-label">Status:</div>
                            <div class="info-value">
                                <div class="status-indicator status-{{ $ticket->status }}">
                                    <span class="status-dot"></span>
                                    {{ str_replace('_', ' ', $ticket->status) }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="info-label">Prioritas:</div>
                            <div class="info-value" style="color: {{ $priorityColor }}; font-weight: bold;">
                                {{ $ticket->priority->name }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ASSIGNMENT -->
                <div class="info-card">
                    <div class="info-label">Ditugaskan ke:</div>
                    <div class="info-value">{{ $ticket->assignedUser->name ?? 'Belum ditugaskan' }}</div>

                    <div style="margin-top: 3px;">
                        <div class="info-label">Deadline:</div>
                        <div class="info-value">{{ $dueDate }}</div>
                    </div>
                </div>

                <!-- SIGNATURES -->
                <div class="info-card" style="flex: 1;">
                    <div class="section-title">TANDA TANGAN</div>
                    <div class="signature-grid">
                        @foreach ($ticket->signatures->sortBy('stage') as $signature)
                            @if ($loop->index < 4)
                                <div class="signature-box">
                                    <div class="signature-name">
                                        {{ Str::limit($signature->user->name ?? 'Unknown', 15) }}</div>
                                    <div class="signature-role">{{ ucfirst($signature->user->role ?? 'N/A') }}</div>
                                    <div class="signature-date">{{ $signature->signed_at->format('d/m H:i') }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- VR SUMMARY -->
        @if ($hasVR)
            <div style="margin-top: 4px;">
                <div class="section-title">VOUCHER REQUEST</div>
                <div style="font-size: 7pt;">
                    Total: Rp {{ number_format($vrTotal, 0, ',', '.') }}
                    ({{ count($vrItems) }} item)
                </div>
            </div>
        @endif

        <!-- FOOTER -->
        <div class="footer">
            <div>Dokumen A5 Landscape | {{ $now }} | {{ $ticket->ticket_number }}</div>
        </div>
    </div>
</body>

</html>
