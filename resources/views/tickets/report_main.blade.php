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
            padding: 15px 15px 0 15px;
            margin: 0;
            position: relative;
            width: 210mm;
            height: 297mm;
            overflow: hidden;
        }

        /* Watermark Status */
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

        .container {
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 2px solid #FF6B35;
            flex-shrink: 0;
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

        .title-section h1 {
            color: #FF6B35;
            font-size: 18px;
            font-weight: bold;
            line-height: 1;
            text-align: right;
            margin: 0;
        }

        /* FORM META */
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

        /* TITLE FIELD */
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

        /* DESCRIPTION */
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

        .description-box {
            border: 1px solid #333;
            padding: 8px 10px;
            font-size: 10pt;
            line-height: 1;
            /* white-space: pre-wrap; */
            word-wrap: break-word;
            height: 200px;
            overflow-y: hidden;
            width: 796px;
            font-family: 'Lucida Console', Monaco, monospace !important;
            font-size: 10pt !important;
            line-height: 1 !important;
        }

        /* FOLLOW UP */
        .followup-section {
            flex-shrink: 0;
            margin-bottom: 6px;
            display: flex;
            flex-direction: column;
            height: auto;
            width: 796px;
        }

        .followup-section label {
            font-weight: bold;
            font-size: 9pt;
            display: block;
            margin-bottom: 3px;
        }

        .followup-container {
            border: 1px solid #333;
            overflow-y: auto;
            height: calc(100% - 18px);
            /* Full height after label */
            background: #ffffff47;
        }

        .followup-box {
            border-bottom: 1px dotted #ccc;
            padding: 6px 8px;
            font-size: 9pt;
            line-height: 1.3;
            white-space: pre-wrap;
            word-wrap: break-word;

        }

        .followup-box:last-child {
            border-bottom: none;
        }

        .followup-box.empty-data {
            color: #888;
            font-style: italic;
            /* text-align: center;
            padding: 10px; */
        }

        /* PR SECTION - WAJIB TAMPIL! */
        .pr-section {
            margin: 4px 0 8px 0;
            padding: 8px 10px;
            border: 1px solid #333;
            background: #fafafa49;
            flex-shrink: 0;
            width: 796px;
        }

        .pr-section label {
            font-weight: bold;
            font-size: 9pt;
            display: block;
            margin-bottom: 5px;
            color: #FF6B35;
        }

        .pr-items {
            font-size: 9pt;
            line-height: 1.3;
        }

        .pr-item-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            border-bottom: 1px dotted #ccc;
        }

        .pr-item-name {
            flex: 2;
        }

        .pr-item-qty {
            flex: 0.5;
            text-align: center;
        }

        .pr-item-price {
            flex: 1;
            text-align: right;
        }

        .pr-total {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-weight: bold;
            border-top: 2px solid #333;
            margin-top: 4px;
        }

        .pr-empty {
            /* padding: 10px; */
            /* text-align: center; */
            color: #888;
            font-style: italic;
        }

        /* DIVIDER ORANGE */
        .pr-divider {
            width: 100%;
            height: 2px;
            background-color: #FF6B35;
            margin: 4px 0 8px 0;
            flex-shrink: 0;
        }

        /* SIGNATURE SECTION - DIGEDEIN! */
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
            padding: 0 5px;
            display: flex;
            flex-direction: column;
            min-height: 160px;
            /* DARI 140 JADI 160 */
        }

        .signature-box .label {
            font-weight: bold;
            font-size: 10pt;
            /* DARI 9PT JADI 10PT */
            margin-bottom: 10px;
            text-transform: uppercase;
            color: #333;
        }

        .signature-img {
            max-width: 130px;
            /* DARI 110 JADI 130 */
            max-height: 60px;
            /* DARI 45 JADI 60 */
            object-fit: contain;
            margin: 0 auto 8px;
            display: block;
        }

        .name-line {
            width: 100%;
            margin: 0 auto;
            padding: 5px 0;
            border-bottom: 1px solid #333;
            font-size: 10pt;
            /* DARI 9PT JADI 10PT */
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
            margin-top: 5px;
            font-size: 9pt;
            /* DARI 7.5PT JADI 9PT */
            color: #555;
        }

        /* COMPLETION SECTION */
        .completion-section {
            margin: 6px 0;
            padding: 3px 0;
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
            /* DIGEDEIN */
            white-space: nowrap;
            width: 140px;
        }

        .completion-field .field-content {
            flex: 1;
            font-size: 10pt;
        }

        /* FLEX SPACER */
        .flex-spacer {
            flex: 1 1 auto;
            min-height: 5px;
            max-height: 30px;
            width: 100%;
        }

        /* FOOTER */
        /* FOOTER - STRETCH FULL */
        .footer {
            margin-top: 2px;
            padding-top: 3px;
            width: 100%;
            flex-shrink: 0;
            border-top: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }

        .footer img {
            width: 100%;
            height: 100%;
            /* Berubah dari 45px jadi 100% */
            min-height: 50px;
            /* Minimal tinggi */
            max-height: 70px;
            /* Maksimal tinggi */
            object-fit: cover;
            /* Cover biar stretch penuh */
            display: block;
        }

        .footer-info {
            text-align: center;
            font-size: 8pt;
            color: #666;
            padding: 3px 0;
        }

        .no-break {
            page-break-inside: avoid;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <!-- Watermark Status -->
    <div class="status-watermark">
        {{ $statusDisplay }}
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header no-break">
            <div class="logo-section">
                @if (file_exists(public_path('assets/images/logo.png')))
                    <img src="{{ public_path('assets/images/logo.png') }}" alt="Company Logo">
                @else
                    <div style="color: #888; display: flex; align-items: center; height: 40px;">[LOGO]</div>
                @endif
            </div>
            <div class="title-section">
                <h1>MAINTENANCE<br>REQUEST</h1>
            </div>
        </div>

        <!-- Form Meta -->
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

        <!-- Title -->
        <div class="full-width-field no-break">
            <label>TITLE</label>
            <span class="colon">:</span>
            <div class="field-content">{{ $ticket->title ?? '-' }}</div>
        </div>

        <!-- Description -->
        <div class="description-section no-break">
            <label>PLEASE REPAIR :</label>
            <div class="description-box">{!! nl2br(e(strip_tags($ticket->description))) ?: '-' !!}</div>
        </div>

        <!-- FOLLOW UP-->
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
                            @if (!empty($comment['date']))
                                <strong>[{{ $comment['date'] }}] {{ $comment['user'] }}:</strong><br>
                                {{ $comment['text'] }}
                            @else
                                &nbsp;
                            @endif
                        </div>
                    @endforeach
                @else<div class="followup-box empty-data">-</div>
                @endif
            </div>
        </div>

        <!-- PR SECTION - WAJIB TAMPIL! -->
        <div class="pr-section no-break">
            <label>PR / VOUCHER REQUEST :</label>
            @if (isset($prData) && isset($prItems) && $prItems->count() > 0)
                <div class="pr-items">
                    @foreach ($prItems as $item)
                        <div class="pr-item-row">
                            <span class="pr-item-name">{{ $item->item_name ?: '-' }}</span>
                            <span class="pr-item-qty">{{ $item->qty ?: '-' }}</span>
                            <span class="pr-item-price">
                                Rp {{ $item->unit_price ? number_format($item->unit_price, 0, ',', '.') : '-' }}
                            </span>
                        </div>
                    @endforeach
                    <div class="pr-total">
                        <span>TOTAL</span>
                        <span>Rp {{ $totalPRAmount ? number_format($totalPRAmount, 0, ',', '.') : '-' }}</span>
                    </div>
                </div>
            @else
                <div class="pr-empty">-</div>
            @endif
        </div>

        <!-- DIVIDER ORANGE -->
        <div class="pr-divider"></div>

        <!-- SIGNATURE SECTION 1 - DIGEDEIN -->
        <div class="signature-section no-break">
            <div class="signature-box">
                <div class="label">REQUESTED BY</div>
                @if ($helper->hasSignature(1, $signatures ?? []))
                    <img src="{{ $helper->getSignaturePath(1, $signatures) }}" class="signature-img" alt="Signature">
                @endif
                <div
                    class="name-line {{ !$helper->getSignatureData(1, $signatures ?? [], 'user', '-') ? 'empty' : '' }}">
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
                @endif
                <div
                    class="name-line {{ !$helper->getSignatureData(2, $signatures ?? [], 'user', '-') ? 'empty' : '' }}">
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
                @endif
                <div
                    class="name-line {{ !$helper->getSignatureData(3, $signatures ?? [], 'user', '-') ? 'empty' : '' }}">
                    {{ $helper->getSignatureData(3, $signatures ?? [], 'user', '-') }}
                </div>
                <div class="date-info">
                    {{ $helper->getSignatureData(3, $signatures ?? [], 'date', '-') }}
                </div>
            </div>
        </div>

        <!-- COMPLETION -->
        <div class="completion-section no-break">
            <div class="completion-field">
                <label>WORK WAS DONE ON</label>
                <span class="colon">:</span>
                <div class="field-content">
                    {{ $ticket->resolved_at ? $ticket->resolved_at->format('d F Y, H:i') : '-' }}</div>
            </div>
            <div class="completion-field">
                <label>TECHNICIAN</label>
                <span class="colon">:</span>
                <div class="field-content">{{ $helper->getUserName($ticket->assignedUser ?? null, '-') }}</div>
            </div>
        </div>

        <!-- FLEX SPACER -->
        <div class="flex-spacer"></div>

        <!-- SIGNATURE SECTION 2 - DIGEDEIN -->
        <div class="signature-section no-break">
            <div class="signature-box">
                <div class="label">COMPLETED BY</div>
                @if ($helper->hasSignature(6, $signatures ?? []))
                    <img src="{{ $helper->getSignaturePath(6, $signatures) }}" class="signature-img" alt="Signature">
                @endif
                <div
                    class="name-line {{ !$helper->getSignatureData(6, $signatures ?? [], 'user', '-') ? 'empty' : '' }}">
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
                @endif
                <div
                    class="name-line {{ !$helper->getSignatureData(7, $signatures ?? [], 'user', '-') ? 'empty' : '' }}">
                    {{ $helper->getSignatureData(7, $signatures ?? [], 'user', '-') }}
                </div>
                <div class="date-info">
                    {{ $helper->getSignatureData(7, $signatures ?? [], 'date', '-') }}
                </div>
            </div>
            <div class="signature-box">
                <div class="label">APPROVED BY GM</div>
                @if ($helper->hasSignature(8, $signatures ?? []))
                    <img src="{{ $helper->getSignaturePath(8, $signatures) }}" class="signature-img"
                        alt="Signature">
                @endif
                <div
                    class="name-line {{ !$helper->getSignatureData(8, $signatures ?? [], 'user', '-') ? 'empty' : '' }}">
                    {{ $helper->getSignatureData(8, $signatures ?? [], 'user', '-') }}
                </div>
                <div class="date-info">
                    {{ $helper->getSignatureData(8, $signatures ?? [], 'date', '-') }}
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer no-break">
            @if (file_exists(public_path('assets/images/footer.png')))
                <img src="{{ public_path('assets/images/footer.png') }}" alt="Footer">
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
    </div>
</body>

</html>
