<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Calendar - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #1a1a2e;
            background: #f0f2f5;
            padding: 20px;
        }

        /* Container untuk screen */
        .print-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 25px;
        }

        /* ========== HEADER ========== */
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

        .logo-fallback i {
            font-size: 40px;
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

        /* ========== INFO ROW ========== */
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .info-item {
            display: flex;
            align-items: baseline;
            gap: 8px;
            font-size: 12px;
        }

        .info-item .label {
            font-weight: 600;
            color: #FF6B35;
            min-width: 55px;
        }

        .info-item .value {
            color: #333;
            font-weight: 500;
        }

        /* ========== CALENDAR GRID ========== */
        .calendar-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            table-layout: fixed;
        }

        .calendar-grid th {
            background: #FF6B35;
            color: white;
            padding: 12px 5px;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .calendar-grid td {
            border: 1px solid #ddd;
            vertical-align: top;
            height: 100px;
            padding: 8px;
            background: white;
        }

        /* Background untuk bulan lain */
        .calendar-grid td.other-month {
            background: #f9f9f9 !important;
        }

        .date-number {
            font-weight: bold;
            font-size: 14px;
            color: #FF6B35;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #f0f0f0;
        }

        .other-month .date-number {
            color: #bbb;
        }

        /* Event items */
        .event-item {
            font-size: 10px;
            padding: 4px 6px;
            margin-bottom: 4px;
            border-radius: 4px;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: transform 0.2s;
        }

        .event-item:hover {
            transform: translateX(3px);
        }

        .event-created {
            background: #003366;
        }

        .event-closed {
            background: #2d2d2d;
        }

        .event-item i.flag-icon {
            font-size: 9px;
            width: 12px;
            text-align: center;
            flex-shrink: 0;
        }

        /* Dynamic priority styles from database - all priorities including inactive */
        @foreach ($priorities as $priority)
            .priority-{{ strtolower(str_replace(' ', '-', $priority->name)) }} i.flag-icon {
                color: {{ $priority->color }} !important;
            }

            .priority-{{ strtolower(str_replace(' ', '-', $priority->name)) }} .ticket-number {
                color: white;
            }

            /* Optional: add opacity for inactive priorities */
            @if ($priority->status != 'active')
                .priority-{{ strtolower(str_replace(' ', '-', $priority->name)) }} {
                    opacity: 0.7;
                }
            @endif
        @endforeach

        .event-item .ticket-number {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 500;
        }

        /* ========== LEGEND ========== */
        .legend {
            margin: 25px 0;
            padding: 12px 20px;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            align-items: center;
            border: 1px solid #e0e0e0;
        }

        .legend-group {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .legend-divider {
            width: 1px;
            height: 30px;
            background: #ddd;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
        }

        /* Legend item inactive style */
        .legend-item.inactive-priority {
            opacity: 0.6;
            filter: grayscale(0.2);
        }

        .legend-color {
            width: 14px;
            height: 14px;
            border-radius: 3px;
        }

        .legend-color.navy {
            background: #003366;
        }

        .legend-color.gray {
            background: #2d2d2d;
        }

        .legend-priority i {
            font-size: 11px;
        }

        /* ========== FOOTER ========== */
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 2px solid #FF6B35;
        }

        .footer-info {
            text-align: center;
            font-size: 10px;
            color: #888;
            padding: 5px 0;
        }

        /* ========== ACTION BUTTONS ========== */
        .action-buttons {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            bottom: 20px;
            z-index: 1000;
        }

        .action-buttons .btn {
            padding: 10px 24px;
            margin: 0 8px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            border: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #FF6B35;
            color: white;
        }

        .btn-primary:hover {
            background: #e55a2b;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        /* ========== PRINT STYLES ========== */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .print-container {
                max-width: 100%;
                padding: 0;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .action-buttons {
                display: none;
            }

            .event-item {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .calendar-grid td.other-month {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .legend {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            html,
            body {
                height: auto;
                overflow: visible;
            }

            .print-header,
            .info-row,
            .calendar-grid,
            .legend,
            .footer {
                page-break-inside: avoid;
                page-break-after: avoid;
                page-break-before: avoid;
            }

            .calendar-grid {
                margin-bottom: 15px;
            }

            .legend {
                margin: 15px 0;
            }

            .footer {
                margin-top: 15px;
                position: relative;
                bottom: 0;
            }
        }

        /* A4 Portrait Print */
        @media print and (orientation: portrait) {
            body {
                width: 210mm;
            }

            .calendar-grid th {
                padding: 6px 3px;
                font-size: 10px;
            }

            .calendar-grid td {
                height: 80px;
                padding: 5px;
            }

            .event-item {
                font-size: 8px;
                padding: 2px 4px;
            }

            .date-number {
                font-size: 12px;
                margin-bottom: 4px;
            }

            .legend {
                padding: 8px 15px;
            }

            .footer {
                margin-top: 10px;
                padding-top: 10px;
            }
        }

        /* A4 Landscape Print */
        @media print and (orientation: landscape) {
            body {
                width: 297mm;
            }

            .calendar-grid th {
                padding: 8px 5px;
                font-size: 11px;
            }

            .calendar-grid td {
                height: 95px;
                padding: 6px;
            }

            .event-item {
                font-size: 9px;
                padding: 3px 5px;
                margin-bottom: 3px;
            }

            .date-number {
                font-size: 13px;
                margin-bottom: 5px;
            }

            .print-header {
                margin-bottom: 15px;
                padding-bottom: 10px;
            }

            .info-row {
                margin-bottom: 15px;
                padding: 5px 0;
            }

            .calendar-grid {
                margin-bottom: 15px;
            }

            .legend {
                margin: 15px 0;
                padding: 8px 15px;
            }

            .footer {
                margin-top: 10px;
                padding-top: 8px;
            }

            .footer {
                page-break-before: avoid;
                page-break-after: avoid;
                position: relative;
                bottom: 0;
            }
        }

        /* ========== RESPONSIVE SCREEN ========== */
        @media (max-width: 992px) {
            .print-container {
                padding: 20px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .print-container {
                padding: 15px;
            }

            .info-row {
                flex-direction: column;
                gap: 8px;
            }

            .calendar-grid th {
                font-size: 10px;
                padding: 8px 3px;
            }

            .calendar-grid td {
                height: 80px;
                padding: 5px;
            }

            .date-number {
                font-size: 11px;
                margin-bottom: 5px;
            }

            .event-item {
                font-size: 8px;
                padding: 2px 4px;
            }

            .legend {
                padding: 10px 15px;
                gap: 15px;
            }

            .legend-item {
                font-size: 9px;
            }

            .legend-group {
                gap: 10px;
            }
        }

        @media (max-width: 576px) {
            .print-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .title-section {
                text-align: center;
            }

            .calendar-grid th {
                font-size: 8px;
                padding: 5px 2px;
            }

            .calendar-grid td {
                height: 70px;
                padding: 4px;
            }

            .event-item {
                font-size: 7px;
                padding: 2px 3px;
            }

            .event-item i.flag-icon {
                font-size: 6px;
            }

            .legend-divider {
                display: none;
            }

            .legend {
                flex-direction: column;
                align-items: flex-start;
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
                <h1>MAINTENANCE<br>CALENDAR</h1>
                <p>{{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</p>
            </div>
        </div>

        <!-- INFO ROW -->
        <div class="info-row">
            <div class="info-item">
                <span class="label">Period:</span>
                <span class="value">{{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</span>
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

        <!-- CALENDAR GRID -->
        @php
            // Create priority color map
            $priorityColorMap = [];
            foreach ($priorities as $priority) {
                $priorityColorMap[strtoupper($priority->name)] = $priority->color;
            }

            function getPriorityColor($priorityName, $priorityColorMap)
            {
                $upperName = strtoupper($priorityName);
                return $priorityColorMap[$upperName] ?? '#f97316';
            }

            function getPriorityClass($priorityName)
            {
                return 'priority-' . strtolower(str_replace(' ', '-', $priorityName));
            }

            // Group events by date
            $eventsByDate = [];
            foreach ($events['created'] ?? [] as $event) {
                $eventsByDate[$event['date']]['created'][] = $event['ticket'];
            }
            foreach ($events['closed'] ?? [] as $event) {
                $eventsByDate[$event['date']]['closed'][] = $event['ticket'];
            }

            $date = \Carbon\Carbon::create($year, $month, 1);
            $startOfWeek = $date->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
            $endOfWeek = $date->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SUNDAY);
            $currentWeekStart = $startOfWeek->copy();
        @endphp

        <table class="calendar-grid">
            <thead>
                <tr>
                    <th>Mon</th>
                    <th>Tue</th>
                    <th>Wed</th>
                    <th>Thu</th>
                    <th>Fri</th>
                    <th>Sat</th>
                    <th>Sun</th>
                </tr>
            </thead>
            <tbody>
                @while ($currentWeekStart <= $endOfWeek)
                    <tr>
                        @for ($i = 0; $i < 7; $i++)
                            @php
                                $currentDate = $currentWeekStart->copy()->addDays($i);
                                $isCurrentMonth = $currentDate->month == $month;
                                $dateKey = $currentDate->format('Y-m-d');
                                $dayEvents = $eventsByDate[$dateKey] ?? [];
                                $hasEvents = !empty($dayEvents['created']) || !empty($dayEvents['closed']);
                            @endphp
                            <td class="{{ !$isCurrentMonth ? 'other-month' : '' }}">
                                <div class="date-number">
                                    {{ $currentDate->format('d') }}
                                </div>
                                @if ($isCurrentMonth && $hasEvents)
                                    @foreach ($dayEvents['created'] ?? [] as $ticket)
                                        @php
                                            $priorityName = $ticket->priority->name ?? 'MEDIUM';
                                            $priorityClass = getPriorityClass($priorityName);
                                            $priorityColor = getPriorityColor($priorityName, $priorityColorMap);
                                        @endphp
                                        <div class="event-item event-created {{ $priorityClass }}"
                                            title="{{ $ticket->ticket_number }} - {{ $ticket->title }}"
                                            style="border-left: 3px solid {{ $priorityColor }};">
                                            <i class="fas fa-flag flag-icon" style="color: {{ $priorityColor }};"></i>
                                            <span class="ticket-number">#{{ $ticket->ticket_number }}</span>
                                        </div>
                                    @endforeach
                                    @foreach ($dayEvents['closed'] ?? [] as $ticket)
                                        @php
                                            $priorityName = $ticket->priority->name ?? 'MEDIUM';
                                            $priorityClass = getPriorityClass($priorityName);
                                            $priorityColor = getPriorityColor($priorityName, $priorityColorMap);
                                        @endphp
                                        <div class="event-item event-closed {{ $priorityClass }}"
                                            title="{{ $ticket->ticket_number }} - {{ $ticket->title }}"
                                            style="border-left: 3px solid {{ $priorityColor }};">
                                            <i class="fas fa-flag flag-icon" style="color: {{ $priorityColor }};"></i>
                                            <span class="ticket-number">#{{ $ticket->ticket_number }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </td>
                        @endfor
                    </tr>
                    @php $currentWeekStart->addWeek(); @endphp
                @endwhile
            </tbody>
        </table>

        <!-- LEGEND -->
        <div class="legend">
            <div class="legend-group">
                <div class="legend-item">
                    <div class="legend-color navy"></div>
                    <span><i class="fas fa-plus-circle"></i> Created</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color gray"></div>
                    <span><i class="fas fa-check-circle"></i> Closed</span>
                </div>
            </div>
            <div class="legend-divider"></div>
            <div class="legend-group">
                @foreach ($priorities as $priority)
                    <div class="legend-item {{ $priority->status != 'active' ? 'inactive-priority' : '' }}"
                        @if ($priority->status != 'active') title="Priority is inactive" @endif>
                        <i class="fas fa-flag" style="color: {{ $priority->color }};"></i>
                        <span>
                            {{ ucfirst(strtolower($priority->name)) }}
                            @if ($priority->status != 'active')
                                <small style="color: #999; font-size: 9px;">(inactive)</small>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <div class="footer-info">
                <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }} |
                <i class="fas fa-print"></i> Printed: {{ now()->format('d/m/Y H:i:s') }} |
                <i class="fas fa-building"></i> {{ config('app.name') }}
            </div>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="action-buttons">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Calendar
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    <script>
        // Tooltip untuk event di screen
        document.querySelectorAll('.event-item').forEach(el => {
            el.addEventListener('mouseenter', function() {
                let title = this.getAttribute('title');
                if (title) {
                    // Optional: custom tooltip
                }
            });
        });
    </script>
</body>

</html>
