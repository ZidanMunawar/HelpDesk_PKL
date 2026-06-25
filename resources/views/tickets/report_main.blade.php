<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAINTENANCE REQUEST - {{ $ticket->ticket_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000000;
            background: #ffffff;
            padding: 0;
            margin: 0;
            position: relative;
            min-height: 297mm;
        }

        @media print {

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                overflow: hidden !important;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .no-break {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .page-break {
                page-break-before: always;
            }

            .signature-section,
            .footer {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .description-wrapper {
                overflow: hidden !important;
                height: auto !important;
                min-height: 210px;
            }

            .description-content {
                overflow: hidden !important;
                height: auto !important;
                min-height: 200px;
            }

            *:last-child {
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }
        }

        .status-watermark {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 80pt;
            font-weight: bold;
            color: rgba(255, 107, 53, 0.12);
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
            text-transform: uppercase;
            letter-spacing: 10px;
            width: 100%;
            text-align: center;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 2px solid #FF6B35;
            flex-shrink: 0;
            position: relative;
        }

        .logo-section {
            width: 180px;
            height: 60px;
        }

        .logo-section img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .title-section {
            text-align: right;
        }

        .title-section h1 {
            color: #FF6B35;
            font-size: 18px;
            font-weight: bold;
            line-height: 1;
            text-align: right;
            margin: 0;
        }

        .header-status-text {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12pt;
            font-weight: bold;
            color: #FF6B35;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .form-meta {
            margin-bottom: 6px;
            flex-shrink: 0;
        }

        .form-row {
            display: flex;
            margin-bottom: 3px;
            gap: 15px;
        }

        .form-field {
            display: flex;
            align-items: baseline;
            flex: 1;
        }

        .form-field label {
            font-weight: bold;
            font-size: 9pt;
            white-space: nowrap;
            width: 55px;
        }

        .form-field label.orange-label {
            color: #FF6B35;
        }

        .colon {
            width: 8px;
            text-align: center;
            margin: 0 2px;
        }

        .field-content {
            flex: 1;
            padding: 1px 3px;
            font-size: 9pt;
            border-bottom: 1px solid #333;
            min-height: 18px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .field-without-line {
            flex: 1;
            padding: 1px 3px;
            font-size: 10pt;
            color: #FF6B35;
            font-weight: bold;
        }

        .text-content {
            flex: 1;
            padding: 1px 3px;
            font-weight: bold;
            font-size: 9pt;
        }

        .full-width-field {
            display: flex;
            align-items: baseline;
            margin-bottom: 6px;
            flex-shrink: 0;
        }

        .full-width-field label {
            font-weight: bold;
            font-size: 9pt;
            white-space: nowrap;
            width: 70px;
        }

        .full-width-field .field-content {
            flex: 1;
            font-weight: bold;
            border-bottom: 1px solid #333;
            padding: 1px 3px;
        }

        .description-section {
            margin-bottom: 6px;
            flex-shrink: 0;
        }

        .description-section label {
            font-weight: bold;
            font-size: 9pt;
            display: block;
            margin-bottom: 3px;
        }

        .description-wrapper {
            position: relative;
            width: 100%;
            max-width: 793px;
            height: 210px;
            border: 1px solid #ff6a2a;
            border-radius: 6px;
            overflow-x: auto;
            overflow-y: hidden;
            margin: 0 auto;
        }

        .description-content {
            width: 793px !important;
            min-width: 793px !important;
            max-width: 793px !important;
            min-height: 200px !important;
            height: 200px !important;
            max-height: 200px !important;
            padding: 8px 10px;
            font-family: 'Lucida Console', Monaco, monospace !important;
            font-size: 10pt !important;
            line-height: 1.2 !important;
            overflow: hidden !important;
            border: none;
            background: transparent;
            color: #000000 !important;
            display: block;
            margin: 0 auto;
            word-wrap: break-word;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .description-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 15px;
        }

        .scroll-hint {
            font-size: 11px;
            color: #999;
            margin-bottom: 5px;
            text-align: right;
        }

        .scroll-hint i {
            color: #ff6a2a;
            margin-right: 4px;
        }

        @media (max-width: 820px) {
            .description-container {
                overflow-x: auto;
                border: 1px solid #eee;
                border-radius: 4px;
            }

            .description-content {
                width: 793px !important;
                min-width: 793px !important;
                margin: 0;
            }
        }

        .followup-section {
            margin: 6px 0;
            flex-shrink: 0;
        }

        .followup-section label {
            font-weight: bold;
            font-size: 10pt;
            display: block;
            margin-bottom: 3px;
        }

        .followup-container {
            border: 1px solid #333;
            min-height: 20px;
            background: transparent !important;
        }

        .followup-box {
            border-bottom: 1px dotted #ccc;
            padding: 8px 10px;
            font-size: 9pt;
            line-height: 1.4;
            background: transparent !important;
        }

        .followup-box:last-child {
            border-bottom: none;
        }

        .followup-header {
            font-weight: 600;
            margin-bottom: 4px;
            color: #333;
        }

        .followup-text {
            color: #555;
            line-height: 1.4;
            word-wrap: break-word;
            white-space: normal;
            padding-left: 10px;
            border-left: 2px solid #FF6B35;
        }

        .followup-box.empty-data {
            color: #999;
            font-style: italic;
            padding: 8px 10px;
            background: #fafafa !important;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin: 8px 0;
            padding: 5px 0;
            flex-shrink: 0;
        }

        .signature-box {
            flex: 1;
            text-align: center;
            padding: 0 3px;
            display: flex;
            flex-direction: column;
            min-height: 130px;
        }

        .signature-box .label {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 6px;
            text-transform: uppercase;
            color: #333;
        }

        .signature-img {
            max-width: 110px;
            max-height: 50px;
            object-fit: contain;
            margin: 0 auto 5px;
            display: block;
        }

        .signature-placeholder {
            width: 100%;
            max-width: 110px;
            max-height: 50px;
            aspect-ratio: 3 / 2;
            margin: 0 auto 5px;
            background: transparent;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .name-line {
            width: 100%;
            margin: 0 auto;
            padding: 4px 0;
            border-bottom: 1px solid #333;
            font-size: 9pt;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .name-line.empty {
            color: #888;
            font-style: italic;
        }

        .signature-box .date-info {
            margin-top: 3px;
            font-size: 8pt;
            color: #555;
        }

        .completion-section {
            margin: 4px 0;
            padding: 2px 0;
            flex-shrink: 0;
        }

        .completion-field {
            display: flex;
            align-items: baseline;
            margin-bottom: 4px;
        }

        .completion-field label {
            font-weight: bold;
            font-size: 10pt;
            white-space: nowrap;
            width: 140px;
        }

        .completion-field .field-content {
            flex: 1;
            font-size: 10pt;
        }

        .flex-spacer {
            flex: 1 1 auto;
            min-height: 5px;
            max-height: 20px;
            width: 100%;
        }

        .footer {
            margin-top: 2px;
            padding-top: 2px;
            padding-bottom: 0;
            width: 100%;
            flex-shrink: 0;
            border-top: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }

        .footer img {
            width: 100%;
            height: auto;
            max-height: 60px;
            object-fit: contain;
            display: block;
        }

        .footer-info {
            text-align: center;
            font-size: 8pt;
            color: #666;
            padding: 3px 0 0 0;
        }

        .no-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .hidden {
            display: none;
        }

        /* TAMBAHKAN INI */
        .print-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.85);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .print-modal {
            background: white;
            border-radius: 16px;
            padding: 40px 30px;
            text-align: center;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .print-modal .modal-icon {
            font-size: 64px;
            margin-bottom: 20px;
            display: block;
        }

        .print-modal h2 {
            color: #FF6B35;
            margin-bottom: 10px;
            font-size: 22px;
            font-weight: bold;
        }

        .print-modal .ticket-number {
            color: #666;
            margin-bottom: 10px;
            font-size: 13px;
            background: #f5f5f5;
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
        }

        .print-modal .info-text {
            color: #888;
            margin-bottom: 30px;
            font-size: 14px;
            line-height: 1.5;
        }

        .print-modal .print-btn {
            background: #FF6B35;
            color: white;
            border: none;
            padding: 16px 40px;
            border-radius: 12px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            justify-content: center;
        }

        .print-modal .close-link {
            display: block;
            margin-top: 20px;
            color: #999;
            text-decoration: none;
            font-size: 14px;
            padding: 10px;
            cursor: pointer;
        }

        @media screen and (max-width: 768px) {
            .print-modal-overlay {
                display: flex;
            }

            .print-content {
                display: none;
            }
        }

        @media screen and (min-width: 769px) {
            .print-modal-overlay {
                display: none !important;
            }

            .print-content {
                display: block;
            }
        }

        @media print {
            .print-modal-overlay {
                display: none !important;
            }

            .print-content {
                display: block !important;
            }
        }
    </style>
</head>

<body>
    <!-- MODAL PRINT -->
    <div class="print-modal-overlay" id="printModalOverlay">
        <div class="print-modal">
            <span class="modal-icon">🖨️</span>
            <h2>Print Report</h2>
            <div class="ticket-number">#{{ $ticket->ticket_number }}</div>
            <p class="info-text">Tap button below to print</p>
            <button class="print-btn" onclick="triggerPrint()">
                <span class="btn-icon">📄</span> Print Now
            </button>
            <div class="close-link" onclick="window.close()">← Cancel</div>
        </div>
    </div>
    @php
        $showWatermark = !empty($statusDisplay) && !in_array($statusDisplay, ['COMPLETED', 'CLOSED']);
    @endphp

    @if ($showWatermark)
        <div class="status-watermark">
            {{ $statusDisplay }}
        </div>
    @endif

    <div class="header no-break">
        <div class="logo-section">
            @php
                $logoPath = public_path('assets/images/logo.png');
                $logoUrl = asset('assets/images/logo.png');
                $hasLogo = file_exists($logoPath);
            @endphp
            @if ($hasLogo)
                <img src="{{ $logoUrl }}" alt="Company Logo">
            @else
                <div style="color: #888; display: flex; align-items: center; height: 40px;">[LOGO]</div>
            @endif
        </div>

        <div class="header-status-text">
            {{ $statusDisplay }}
        </div>

        <div class="title-section">
            <h1>MAINTENANCE<br>REQUEST</h1>
        </div>
    </div>

    <div class="form-meta no-break">
        <div class="form-row">
            <div class="form-field">
                <label>TO</label>
                <span class="colon">:</span>
                <div class="text-content">ENGINEERING DEPT.</div>
            </div>
            <div class="form-field">
                <label class="orange-label">No</label>
                <span class="colon">:</span>
                <div class="field-without-line">{{ $ticket->ticket_number }}</div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label>FROM</label>
                <span class="colon">:</span>
                <div class="field-content">{{ $helper->getUserName($ticket->user, '-') }}</div>
            </div>
            <div class="form-field">
                <label>Date</label>
                <span class="colon">:</span>
                <div class="field-content">{{ $ticket->created_at->format('d F Y') }}</div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-field">
                <label>DEPT</label>
                <span class="colon">:</span>
                <div class="field-content">{{ $ticket->department->name ?? '-' }}</div>
            </div>
            <div class="form-field">
                <label>AREA</label>
                <span class="colon">:</span>
                <div class="field-content">
                    @if ($ticket->location)
                        {{ $ticket->location->name }}
                        @if ($ticket->location->floor_number)
                            (Fl {{ $ticket->location->floor_number }})
                        @endif
                    @elseif($ticket->location_manual)
                        {{ $ticket->location_manual }}
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="full-width-field no-break">
        <label>TITLE</label>
        <span class="colon">:</span>
        <div class="field-content">{{ $ticket->title ?? '-' }}</div>
    </div>

    <div class="description-section no-break">
        <label>PLEASE REPAIR :</label>
        <div class="description-container">
            <div class="description-wrapper">
                <div class="description-content">{{ $ticket->description }}</div>
            </div>
        </div>
    </div>

    <div class="followup-section no-break">
        <label>FOLLOW UP :</label>
        <div class="followup-container">
            @php
                $realFollowUps = array_filter($followUpComments ?? [], function ($comment) {
                    return !empty($comment['text']) && $comment['text'] !== '-';
                });
            @endphp

            @if (count($realFollowUps) > 0)
                @foreach ($realFollowUps as $comment)
                    <div class="followup-box">
                        <div class="followup-header">
                            <strong>[{{ $comment['date'] }}] {{ $comment['user'] }}:</strong>
                        </div>
                        <div class="followup-text">{{ $comment['text'] }}</div>
                    </div>
                @endforeach
            @else
                <div class="followup-box empty-data">
                    No follow-up notes available
                </div>
            @endif
        </div>
    </div>

    <div class="signature-section no-break">
        <div class="signature-box">
            <div class="label">REQUESTED BY</div>
            @if ($helper->hasSignature(1, $signatures ?? []))
                <img src="{{ $helper->getSignaturePath(1, $signatures) }}" class="signature-img" alt="Signature">
            @else
                <div class="signature-placeholder"></div>
            @endif
            <div class="name-line {{ !$helper->getSignatureData(1, $signatures ?? [], 'user', '-') ? 'empty' : '' }}">
                {{ $helper->getSignatureData(1, $signatures ?? [], 'user', '-') }}
            </div>
            <div class="date-info">
                {{ $helper->getSignatureData(1, $signatures ?? [], 'date', '-') }}
            </div>
        </div>

        <div class="signature-box">
            <div class="label">RECEIVED BY</div>
            @if ($helper->hasSignature(2, $signatures ?? []))
                <img src="{{ $helper->getSignaturePath(2, $signatures) }}" class="signature-img" alt="Signature">
            @else
                <div class="signature-placeholder"></div>
            @endif
            <div class="name-line {{ !$helper->getSignatureData(2, $signatures ?? [], 'user', '-') ? 'empty' : '' }}">
                {{ $helper->getSignatureData(2, $signatures ?? [], 'user', '-') }}
            </div>
            <div class="date-info">
                {{ $helper->getSignatureData(2, $signatures ?? [], 'date', '-') }}
            </div>
        </div>

        <div class="signature-box">
            <div class="label">APPROVED BY OM</div>
            @if ($helper->hasSignature(3, $signatures ?? []))
                <img src="{{ $helper->getSignaturePath(3, $signatures) }}" class="signature-img" alt="Signature">
            @else
                <div class="signature-placeholder"></div>
            @endif
            <div class="name-line {{ !$helper->getSignatureData(3, $signatures ?? [], 'user', '-') ? 'empty' : '' }}">
                {{ $helper->getSignatureData(3, $signatures ?? [], 'user', '-') }}
            </div>
            <div class="date-info">
                {{ $helper->getSignatureData(3, $signatures ?? [], 'date', '-') }}
            </div>
        </div>
    </div>

    <div class="completion-section no-break">
        <div class="completion-field">
            <label>WORK WAS DONE ON</label>
            <span class="colon">:</span>
            <div class="field-content">
                {{ $ticket->resolved_at ? $ticket->resolved_at->format('d F Y, H:i') : '-' }}
            </div>
        </div>
        <div class="completion-field">
            <label>TECHNICIAN</label>
            <span class="colon">:</span>
            <div class="field-content">{{ $helper->getUserName($ticket->assignedUser ?? null, '-') }}</div>
        </div>
    </div>

    <div class="flex-spacer"></div>

    <div class="signature-section no-break">
        <div class="signature-box">
            <div class="label">COMPLETED BY</div>
            @if ($helper->hasSignature(6, $signatures ?? []))
                <img src="{{ $helper->getSignaturePath(6, $signatures) }}" class="signature-img" alt="Signature">
            @else
                <div class="signature-placeholder"></div>
            @endif
            <div class="name-line {{ !$helper->getSignatureData(6, $signatures ?? [], 'user', '-') ? 'empty' : '' }}">
                {{ $helper->getSignatureData(6, $signatures ?? [], 'user', '-') }}
            </div>
            <div class="date-info">
                {{ $helper->getSignatureData(6, $signatures ?? [], 'date', '-') }}
            </div>
        </div>

        <div class="signature-box">
            <div class="label">CHECKED BY</div>
            @if ($helper->hasSignature(7, $signatures ?? []))
                <img src="{{ $helper->getSignaturePath(7, $signatures) }}" class="signature-img" alt="Signature">
            @else
                <div class="signature-placeholder"></div>
            @endif
            <div class="name-line {{ !$helper->getSignatureData(7, $signatures ?? [], 'user', '-') ? 'empty' : '' }}">
                {{ $helper->getSignatureData(7, $signatures ?? [], 'user', '-') }}
            </div>
            <div class="date-info">
                {{ $helper->getSignatureData(7, $signatures ?? [], 'date', '-') }}
            </div>
        </div>

        <div class="signature-box">
            <div class="label">APPROVED BY GM</div>
            @if ($helper->hasSignature(8, $signatures ?? []))
                <img src="{{ $helper->getSignaturePath(8, $signatures) }}" class="signature-img" alt="Signature">
            @else
                <div class="signature-placeholder"></div>
            @endif
            <div class="name-line {{ !$helper->getSignatureData(8, $signatures ?? [], 'user', '-') ? 'empty' : '' }}">
                {{ $helper->getSignatureData(8, $signatures ?? [], 'user', '-') }}
            </div>
            <div class="date-info">
                {{ $helper->getSignatureData(8, $signatures ?? [], 'date', '-') }}
            </div>
        </div>
    </div>

    <div class="footer no-break">
        @php
            $footerPath = public_path('assets/images/footer.png');
            $footerUrl = asset('assets/images/footer.png');
            $hasFooter = file_exists($footerPath);
        @endphp
        @if ($hasFooter)
            <img src="{{ $footerUrl }}" alt="Footer">
        @else
            <div
                style="width: 100%; height: 45px; background: #f0f0f0; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; color: #888; font-size: 8pt;">
                [FOOTER]
            </div>
        @endif
        <div class="footer-info">
            Generated on {{ now()->format('d F Y, H:i') }} | Ticket #{{ $ticket->ticket_number }}
        </div>
    </div>

    <script>
        function triggerPrint() {
            document.getElementById('printModalOverlay').style.display = 'none';
            document.querySelector('.print-content').style.display = 'block';
            setTimeout(function() {
                window.print();
            }, 500);
        }

        @if (request()->has('print'))
            if (window.innerWidth > 768) {
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 800);
                };
            }
        @endif
    </script>
</body>

</html>
