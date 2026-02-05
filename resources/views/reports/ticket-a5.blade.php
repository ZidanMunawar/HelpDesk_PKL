<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>LAPORAN TIKET - {{ $ticket->ticket_number }}</title>
    <style>
        @page {
            margin: 8mm;
            size: A5 portrait;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            color: #000;
            background: #fff;
        }

        /* HEADER - SIMPLE & COMPACT */
        .header {
            text-align: center;
            margin-bottom: 6px;
            padding-bottom: 5px;
            border-bottom: 2px solid #ff6200;
        }

        .hotel-title {
            font-size: 14px;
            font-weight: bold;
            color: #ff6200;
            margin: 0;
        }

        .ticket-number {
            font-size: 12px;
            font-weight: bold;
            margin: 2px 0;
        }

        .ticket-title {
            font-size: 10px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

        /* COMPACT INFO GRID - 2 COLUMNS */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 4px;
            margin-bottom: 6px;
        }

        .info-card {
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 4px;
            font-size: 7pt;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 1px;
        }

        .info-value {
            color: #000;
            margin-bottom: 2px;
        }

        /* STATUS BADGES - SMALL */
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 8px;
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* SIGNATURE GRID - COMPACT */
        .signature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            margin-top: 6px;
        }

        .signature-item {
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 4px;
            font-size: 7pt;
            min-height: 50px;
        }

        .signature-label {
            font-weight: bold;
            font-size: 6pt;
            color: #666;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .signature-image {
            max-height: 30px;
            max-width: 100%;
            object-fit: contain;
            margin: 2px 0;
            border: 1px solid #eee;
        }

        /* SECTION TITLES */
        .section-title {
            font-size: 9pt;
            font-weight: bold;
            color: #ff6200;
            margin: 6px 0 3px 0;
            padding-bottom: 2px;
            border-bottom: 1px solid #ff6200;
        }

        /* DIVIDER */
        .divider {
            height: 1px;
            background: #ddd;
            margin: 4px 0;
        }

        /* VR TABLE - COMPACT */
        .vr-table {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0;
            font-size: 6pt;
        }

        .vr-table th {
            background: #ff6200;
            color: white;
            font-weight: bold;
            padding: 3px;
            text-align: left;
            border: 1px solid #ff6200;
        }

        .vr-table td {
            padding: 3px;
            border: 1px solid #ddd;
        }

        /* UTILITY CLASSES */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold;
        }

        .mb-2 {
            margin-bottom: 2px;
        }

        .mb-3 {
            margin-bottom: 3px;
        }

        .mt-3 {
            margin-top: 3px;
        }

        /* FOOTER */
        .footer {
            margin-top: 8px;
            padding-top: 4px;
            border-top: 1px solid #ddd;
            font-size: 6pt;
            color: #666;
            text-align: center;
        }

        /* COMMENTS - COMPACT */
        .comment-item {
            margin-bottom: 4px;
            padding-bottom: 3px;
            border-bottom: 1px dashed #eee;
            font-size: 7pt;
        }

        .comment-header {
            font-size: 6pt;
            color: #666;
            margin-bottom: 1px;
        }

        /* TIMELINE */
        .timeline {
            font-size: 6pt;
        }

        .timeline-item {
            margin-bottom: 2px;
        }

        /* PRIORITY INDICATOR */
        .priority-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 3px;
            vertical-align: middle;
        }

        /* PRINT OPTIMIZATION */
        @media print {
            body {
                font-size: 7pt;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="hotel-title">MAINTENANCE REQUEST - {{ $hotelName }} HOTEL</div>
        <div class="ticket-number">#{{ $ticket->ticket_number }}</div>
        <div class="ticket-title">{{ Str::limit($ticket->title, 50) }}</div>
    </div>

    <!-- BASIC INFO GRID -->
    <div class="info-grid">
        <div class="info-card">
            <div class="info-label">Dibuat:</div>
            <div class="info-value">{{ $createdAt }}</div>

            <div class="info-label">Oleh:</div>
            <div class="info-value">{{ $ticket->user->name }}</div>

            <div class="info-label">Departemen:</div>
            <div class="info-value">{{ $ticket->department->name ?? '-' }}</div>
        </div>

        <div class="info-card">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="badge"
                    style="background:
                    @if ($ticket->status === 'open') #e3f2fd
                    @elseif($ticket->status === 'received') #e3f2fd
                    @elseif($ticket->status === 'pending_om') #fff3cd
                    @elseif($ticket->status === 'in_progress') #d1ecf1
                    @elseif($ticket->status === 'pending_vr') #fff8e1
                    @elseif($ticket->status === 'completed') #d4edda
                    @elseif($ticket->status === 'pending_gm') #e8f4fd
                    @elseif($ticket->status === 'closed') #f8f9fa
                    @elseif($ticket->status === 'cancelled') #f8d7da
                    @else #f8f9fa @endif;
                    color:
                    @if ($ticket->status === 'open') #1565c0
                    @elseif($ticket->status === 'received') #1565c0
                    @elseif($ticket->status === 'pending_om') #856404
                    @elseif($ticket->status === 'in_progress') #0c5460
                    @elseif($ticket->status === 'pending_vr') #ff8f00
                    @elseif($ticket->status === 'completed') #155724
                    @elseif($ticket->status === 'pending_gm') #0d47a1
                    @elseif($ticket->status === 'closed') #495057
                    @elseif($ticket->status === 'cancelled') #721c24
                    @else #495057 @endif;
                    border: 1px solid
                    @if ($ticket->status === 'open') #bbdefb
                    @elseif($ticket->status === 'received') #bbdefb
                    @elseif($ticket->status === 'pending_om') #ffeaa7
                    @elseif($ticket->status === 'in_progress') #bee5eb
                    @elseif($ticket->status === 'pending_vr') #ffe082
                    @elseif($ticket->status === 'completed') #c3e6cb
                    @elseif($ticket->status === 'pending_gm') #bbdefb
                    @elseif($ticket->status === 'closed') #e9ecef
                    @elseif($ticket->status === 'cancelled') #f5c6cb
                    @else #e9ecef @endif;">
                    {{ str_replace('_', ' ', $ticket->status) }}
                </span>
            </div>

            <div class="info-label">Prioritas:</div>
            <div class="info-value">
                <span class="priority-dot" style="background: {{ $priorityColor }};"></span>
                {{ $ticket->priority->name }}
            </div>

            <div class="info-label">Tahap:</div>
            <div class="info-value">{{ $stageName }}</div>
        </div>
    </div>

    <div class="divider"></div>

    <!-- LOCATION & ASSIGNMENT -->
    <div class="info-grid">
        <div class="info-card">
            <div class="info-label">Lokasi:</div>
            <div class="info-value">{{ $locationText }}</div>

            <div class="info-label">Kategori:</div>
            <div class="info-value">{{ $ticket->category->name }}</div>
        </div>

        <div class="info-card">
            <div class="info-label">Teknisi:</div>
            <div class="info-value">{{ $ticket->assignedUser->name ?? '-' }}</div>

            <div class="info-label">Deadline:</div>
            <div class="info-value">{{ $dueDate }}</div>
        </div>
    </div>

    <!-- DESCRIPTION -->
    <div class="section-title">DESKRIPSI PERBAIKAN</div>
    <div style="font-size: 7pt; padding: 0 2px;">
        {!! nl2br(e(Str::limit($ticket->description, 300))) !!}
    </div>

    @if ($hasVR)
        <div class="section-title mt-3">VOUCHER REQUEST</div>
        <table class="vr-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vrItems as $item)
                    <tr>
                        <td>{{ Str::limit($item->item_name, 20) }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->qty * $item->unit_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                @if ($vrTotal > 0)
                    <tr>
                        <td colspan="3" class="text-right text-bold">TOTAL:</td>
                        <td class="text-right text-bold">
                            Rp {{ number_format($vrTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    <!-- SIGNATURES -->
    @if ($ticket->signatures->count() > 0)
        <div class="section-title">TANDA TANGAN</div>
        <div class="signature-grid">
            @foreach ($ticket->signatures->sortBy('stage') as $signature)
                @if ($loop->index < 4)
                    <!-- Limit 4 signatures in A5 -->
                    <div class="signature-item">
                        <div class="signature-label">
                            @switch($signature->stage)
                                @case(1)
                                    Diminta oleh
                                @break

                                @case(2)
                                    Diterima oleh
                                @break

                                @case(3)
                                    Disetujui OM
                                @break

                                @case(6)
                                    Diselesaikan oleh
                                @break

                                @case(7)
                                    Diperiksa oleh
                                @break

                                @case(8)
                                    Disetujui GM
                                @break

                                @default
                                    Ditandatangani
                            @endswitch
                        </div>

                        <div style="font-size: 7pt; font-weight: bold; margin: 2px 0;">
                            {{ $signature->user->name ?? 'Unknown' }}
                        </div>

                        <div style="font-size: 6pt; color: #666;">
                            {{ ucfirst($signature->user->role ?? 'N/A') }}<br>
                            {{ $signature->signed_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <!-- TIMELINE -->
    <div class="section-title">TIMELINE</div>
    <div class="timeline">
        <div class="timeline-item">
            <strong>Dibuat:</strong> {{ $createdAt }}
        </div>
        @if ($resolvedAt !== '-')
            <div class="timeline-item">
                <strong>Diselesaikan:</strong> {{ $resolvedAt }}
            </div>
        @endif
        @if ($closedAt !== '-')
            <div class="timeline-item">
                <strong>Ditutup:</strong> {{ $closedAt }}
            </div>
        @endif
    </div>

    <!-- COMMENTS SUMMARY -->
    @if ($ticket->comments->count() > 0)
        <div class="section-title">UPDATE TERAKHIR</div>
        <div style="font-size: 7pt;">
            @php
                $lastComment = $ticket->comments->sortByDesc('created_at')->first();
            @endphp
            @if ($lastComment)
                <div><strong>{{ $lastComment->user->name ?? 'System' }}:</strong></div>
                <div>{{ Str::limit(strip_tags($lastComment->comment), 100) }}</div>
                <div style="font-size: 6pt; color: #666;">
                    {{ $lastComment->created_at->format('d/m/Y H:i') }}
                </div>
            @endif
        </div>
    @endif

    <!-- FOOTER -->
    <div class="footer">
        <div>Dokumen ini dibuat pada: {{ $now }}</div>
        <div>ID Dokumen: {{ $ticket->ticket_number }} | Format: A5</div>
    </div>
</body>

</html>
