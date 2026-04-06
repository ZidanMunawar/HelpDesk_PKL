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
            position: relative;
            min-height: 297mm;
        }

        /* Watermark Status - Tengah */
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
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            min-height: 277mm;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #FF6B35;
        }

        .logo-section {
            width: 200px;
            height: 50px;
        }

        .logo-section img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .title-section h1 {
            color: #FF6B35;
            font-size: 20px;
            font-weight: bold;
            line-height: 1;
            text-align: right;
            margin: 0;
        }

        /* FORM META - Lebih rapat */
        .form-meta {
            margin-bottom: 8px;
        }

        .form-row {
            display: flex;
            margin-bottom: 3px;
            gap: 15px;
        }

        .form-field {
            display: flex;
            align-items: baseline;
            gap: 4px;
            flex: 1;
        }

        .form-field label {
            font-weight: bold;
            font-size: 9pt;
            white-space: nowrap;
            min-width: 60px;
        }

        .form-field label.orange-label {
            color: #FF6B35;
        }

        .field-content {
            flex: 1;
            padding: 2px 4px;
            font-size: 9pt;
            border-bottom: 1px solid #333;
            min-height: 18px;
        }

        .field-without-line {
            flex: 1;
            padding: 2px 4px;
            font-size: 10pt;
            color: #FF6B35;
            font-weight: bold;
            min-height: 18px;
        }

        .text-content {
            flex: 1;
            padding: 2px 4px;
            font-weight: bold;
            font-size: 9pt;
        }

        /* FORM SECTION - Lebih rapat */
        .form-section {
            margin-bottom: 8px;
        }

        .full-width-field {
            display: flex;
            gap: 4px;
            margin-bottom: 5px;
            align-items: baseline;
        }

        .full-width-field label {
            font-weight: bold;
            font-size: 9pt;
            white-space: nowrap;
            min-width: 70px;
        }

        .multi-line-field {
            margin-bottom: 6px;
        }

        .multi-line-field label {
            font-weight: bold;
            font-size: 9pt;
            display: block;
            margin-bottom: 2px;
        }

        /* Description box dengan pre-wrap */
        .description-box {
            border: 1px solid #333;
            min-height: 50px;
            padding: 6px;
            font-size: 9pt;
            line-height: 1.3;
            margin-bottom: 3px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Follow-up box - hanya untuk follow-up saja */
        .followup-box {
            border: 1px solid #333;
            min-height: 40px;
            padding: 6px;
            font-size: 9pt;
            line-height: 1.3;
            margin-bottom: 3px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .lines {
            padding: 2px 4px;
            font-size: 9pt;
            min-height: 18px;
        }

        /* SIGNATURE SECTION - Tanpa border */
        .signature-section {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 8px 0;
            padding: 5px 0;
        }

        .signature-box {
            flex: 1;
            text-align: center;
            padding: 0 5px;
            min-height: 110px;
            display: flex;
            flex-direction: column;
        }

        .signature-box .label {
            font-weight: bold;
            font-size: 8pt;
            margin-bottom: 5px;
            min-height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1.1;
        }

        /* Signature image tanpa border */
        .signature-img {
            width: 100%;
            max-width: 130px;
            max-height: 50px;
            object-fit: contain;
            margin: 0 auto 5px;
            display: block;
        }

        /* Nama dengan garis bawah - tanpa role */
        .name-line {
            width: 100%;
            margin: 0 auto;
            padding: 3px 0;
            min-height: 20px;
            border-bottom: 1px solid #333;
            font-size: 9pt;
            text-align: center;
        }

        .name-line.empty {
            color: #888;
            font-style: italic;
        }

        .signature-box .date-info {
            margin-top: 2px;
            font-size: 7pt;
            color: #555;
            min-height: 15px;
        }

        /* PR SECTION - Simple format */
        .pr-section {
            margin: 8px 0;
            padding: 5px 8px;
            border: 1px solid #ddd;
            background: #fafafa;
        }

        .pr-section label {
            font-weight: bold;
            font-size: 9pt;
            display: block;
            margin-bottom: 3px;
            color: #FF6B35;
        }

        .pr-items {
            font-size: 8pt;
            line-height: 1.3;
            margin-bottom: 3px;
        }

        .pr-item-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            border-bottom: 1px dotted #eee;
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
            padding: 3px 0;
            font-weight: bold;
            border-top: 1px solid #333;
            margin-top: 3px;
        }

        /* COMPLETION SECTION - Lebih rapat */
        .completion-section {
            margin-top: 5px;
            padding-top: 5px;
        }

        .completion-field {
            display: flex;
            gap: 4px;
            margin-bottom: 3px;
            align-items: baseline;
        }

        .completion-field label {
            font-weight: bold;
            font-size: 9pt;
            white-space: nowrap;
            min-width: 140px;
        }

        /* FOOTER - Paling bawah */
        .footer {
            margin-top: auto;
            padding-top: 8px;
            width: 100%;
        }

        .footer img {
            width: 100%;
            height: auto;
            max-height: 70px;
            object-fit: cover;
            display: block;
        }

        .footer-info {
            text-align: center;
            font-size: 7pt;
            color: #666;
            margin-top: 3px;
        }

        /* ATTACHMENTS SECTION - Halaman terpisah */
        .attachments-section {
            page-break-before: always;
            margin-top: 20px;
        }

        .attachment-page {
            page-break-after: always;
            min-height: 277mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 10px 0;
        }

        .attachment-page:last-child {
            page-break-after: auto;
        }

        .attachment-image-container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .attachment-full-image {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* Untuk landscape, rotate */
        .attachment-landscape {
            transform: rotate(90deg);
            transform-origin: center;
        }

        .attachment-title {
            text-align: center;
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 5px;
            color: #FF6B35;
        }

        .attachment-caption {
            text-align: center;
            font-size: 7pt;
            color: #666;
            margin-top: 5px;
        }

        .empty-data {
            color: #888;
            font-style: italic;
        }
    </style>
</head>

<body>
    <!-- Watermark Status - Tengah -->
    <div class="status-watermark">
        {{ $statusDisplay }}
    </div>

    <div class="container">
        <div class="content">
            <!-- Header Section -->
            <div class="header">
                <div class="logo-section">
                    @if (file_exists(public_path('assets/images/logo.png')))
                        <img src="{{ public_path('assets/images/logo.png') }}" alt="Company Logo">
                    @else
                        <div style="color: #888; font-size: 9pt; display: flex; align-items: center;">
                            [LOGO]
                        </div>
                    @endif
                </div>
                <div class="title-section">
                    <h1>MAINTENANCE<br>REQUEST</h1>
                </div>
            </div>

            <!-- Form Meta -->
            <div class="form-meta">
                <div class="form-row">
                    <div class="form-field">
                        <label>TO</label>
                        <span>:</span>
                        <div class="text-content">ENGINEERING DEPT.</div>
                    </div>
                    <div class="form-field">
                        <label class="orange-label">No</label>
                        <span>:</span>
                        <div class="field-without-line">
                            {{ $ticket->ticket_number }}
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label>FROM</label>
                        <span>:</span>
                        <div class="field-content">
                            {{ $helper->getUserName($ticket->user, '-') }}
                        </div>
                    </div>
                    <div class="form-field">
                        <label>Date</label>
                        <span>:</span>
                        <div class="field-content">
                            {{ $ticket->created_at->format('d F Y') }}
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-field">
                        <label>DEPT</label>
                        <span>:</span>
                        <div class="field-content">
                            {{ $ticket->department->name ?? '-' }}
                        </div>
                    </div>
                    <div class="form-field">
                        <label>LOCATION</label>
                        <span>:</span>
                        <div class="field-content">
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
                </div>
            </div>

            <!-- Main Form -->
            <div class="form-section">
                <div class="full-width-field">
                    <label>TITLE :</label>
                    <div class="field-content" style="font-weight: bold;">
                        {{ $ticket->title ?? '-' }}
                    </div>
                </div>

                <div class="multi-line-field">
                    <label>PLEASE REPAIR :</label>
                    {{-- Box dengan pre-wrap --}}
                    <div class="description-box">
                        {{ strip_tags($ticket->description) ?: '-' }}
                    </div>
                </div>

                <div class="multi-line-field">
                    <label>FOLLOW UP :</label>
                    {{-- Hanya follow-up yang benar-benar follow-up --}}
                    @php
                        $realFollowUps = array_filter($followUpComments, function ($comment) {
                            return !empty($comment['text']) && $comment['text'] !== '-';
                        });
                    @endphp

                    @if (count($realFollowUps) > 0)
                        @foreach ($realFollowUps as $comment)
                            <div class="followup-box">
                                @if (!empty($comment['date']))
                                    [{{ $comment['date'] }}] {{ $comment['user'] }}:
                                    {{ $comment['text'] }}
                                @else
                                    &nbsp;
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="followup-box empty-data">
                            -
                        </div>
                    @endif
                </div>
            </div>

            <!-- Signature Section 1 -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="label">REQUESTED BY :</div>
                    @if ($helper->hasSignature(1, $signatures))
                        <img src="{{ $helper->getSignaturePath(1, $signatures) }}" class="signature-img"
                            alt="Signature">
                    @endif
                    {{-- Nama dengan garis bawah, jika kosong pakai - --}}
                    <div
                        class="name-line {{ !$helper->getSignatureData(1, $signatures, 'user', '-') ? 'empty' : '' }}">
                        {{ $helper->getSignatureData(1, $signatures, 'user', '-') }}
                    </div>
                    {{-- Hanya tanggal di bawah, tanpa role --}}
                    <div class="date-info">
                        {{ $helper->getSignatureData(1, $signatures, 'date', '-') }}
                    </div>
                </div>
                <div class="signature-box">
                    <div class="label">RECEIVED BY :</div>
                    @if ($helper->hasSignature(2, $signatures))
                        <img src="{{ $helper->getSignaturePath(2, $signatures) }}" class="signature-img"
                            alt="Signature">
                    @endif
                    <div
                        class="name-line {{ !$helper->getSignatureData(2, $signatures, 'user', '-') ? 'empty' : '' }}">
                        {{ $helper->getSignatureData(2, $signatures, 'user', '-') }}
                    </div>
                    <div class="date-info">
                        {{ $helper->getSignatureData(2, $signatures, 'date', '-') }}
                    </div>
                </div>
                <div class="signature-box">
                    <div class="label">APPROVED BY OM :</div>
                    @if ($helper->hasSignature(3, $signatures))
                        <img src="{{ $helper->getSignaturePath(3, $signatures) }}" class="signature-img"
                            alt="Signature">
                    @endif
                    <div
                        class="name-line {{ !$helper->getSignatureData(3, $signatures, 'user', '-') ? 'empty' : '' }}">
                        {{ $helper->getSignatureData(3, $signatures, 'user', '-') }}
                    </div>
                    <div class="date-info">
                        {{ $helper->getSignatureData(3, $signatures, 'date', '-') }}
                    </div>
                </div>
            </div>

            <!-- PR Section - Simple format, tetap tampil meskipun kosong -->
            <div class="pr-section">
                <label>PR / VOUCHER REQUEST :</label>
                @if ($prData && $prItems->count() > 0)
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
                    <div class="empty-data" style="padding: 3px 0;">-</div>
                @endif
            </div>

            <!-- Completion -->
            <div class="completion-section">
                <div class="completion-field">
                    <label>THE WORK WAS DONE ON :</label>
                    <div class="field-content">
                        {{ $ticket->resolved_at ? $ticket->resolved_at->format('d F Y, H:i') : '-' }}
                    </div>
                </div>

                <div class="completion-field">
                    <label>TECHNICIAN :</label>
                    <div class="field-content">
                        {{ $helper->getUserName($ticket->assignedUser, '-') }}
                    </div>
                </div>
            </div>

            <!-- Signature Section 2 -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="label">COMPLETED BY :</div>
                    @if ($helper->hasSignature(6, $signatures))
                        <img src="{{ $helper->getSignaturePath(6, $signatures) }}" class="signature-img"
                            alt="Signature">
                    @endif
                    <div
                        class="name-line {{ !$helper->getSignatureData(6, $signatures, 'user', '-') ? 'empty' : '' }}">
                        {{ $helper->getSignatureData(6, $signatures, 'user', '-') }}
                    </div>
                    <div class="date-info">
                        {{ $helper->getSignatureData(6, $signatures, 'date', '-') }}
                    </div>
                </div>
                <div class="signature-box">
                    <div class="label">CHECKED BY :</div>
                    @if ($helper->hasSignature(7, $signatures))
                        <img src="{{ $helper->getSignaturePath(7, $signatures) }}" class="signature-img"
                            alt="Signature">
                    @endif
                    <div
                        class="name-line {{ !$helper->getSignatureData(7, $signatures, 'user', '-') ? 'empty' : '' }}">
                        {{ $helper->getSignatureData(7, $signatures, 'user', '-') }}
                    </div>
                    <div class="date-info">
                        {{ $helper->getSignatureData(7, $signatures, 'date', '-') }}
                    </div>
                </div>
                <div class="signature-box">
                    <div class="label">APPROVED BY GM :</div>
                    @if ($helper->hasSignature(8, $signatures))
                        <img src="{{ $helper->getSignaturePath(8, $signatures) }}" class="signature-img"
                            alt="Signature">
                    @endif
                    <div
                        class="name-line {{ !$helper->getSignatureData(8, $signatures, 'user', '-') ? 'empty' : '' }}">
                        {{ $helper->getSignatureData(8, $signatures, 'user', '-') }}
                    </div>
                    <div class="date-info">
                        {{ $helper->getSignatureData(8, $signatures, 'date', '-') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Section - Paling bawah -->
        <div class="footer">
            @if (file_exists(public_path('assets/images/footer.png')))
                <img src="{{ public_path('assets/images/footer.png') }}" alt="Footer"
                    style="width: 100%; height: auto; object-fit: cover;">
            @else
                <div
                    style="width: 100%; height: 50px; background: #f0f0f0; border: 1px solid #ddd;
                            display: flex; align-items: center; justify-content: center; color: #888; font-size: 8pt;">
                    [FOOTER]
                </div>
            @endif
            <div class="footer-info">
                Generated on {{ now()->format('d F Y, H:i') }} | Ticket #{{ $ticket->ticket_number }}
            </div>
        </div>
    </div>

    <!-- ATTACHMENTS SECTION - Halaman terpisah untuk setiap foto -->
    @if (isset($imageAttachments) && count($imageAttachments) > 0)
        <div class="attachments-section">
            @foreach ($imageAttachments as $index => $attachment)
                <div class="attachment-page">
                    {{-- <div class="attachment-title">
                        ATTACHMENT {{ $index + 1 }} - {{ $attachment->file_name }}
                    </div> --}}
                    <div class="attachment-image-container">
                        @php
                            $imagePath = storage_path('app/public/' . $attachment->file_path);
                            $imageInfo = @getimagesize($imagePath);
                            $isLandscape = $imageInfo && $imageInfo[0] > $imageInfo[1];
                        @endphp

                        @if (file_exists($imagePath))
                            <img src="{{ $imagePath }}"
                                class="attachment-full-image {{ $isLandscape ? 'attachment-landscape' : '' }}"
                                alt="{{ $attachment->file_name }}"
                                style="max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain;">
                        @else
                            <div style="text-align: center; color: #999;">
                                Image file not found: {{ $attachment->file_name }}
                            </div>
                        @endif
                    </div>
                    <div class="attachment-caption">
                        Uploaded:
                        {{ $attachment->created_at ? date('d F Y', strtotime($attachment->created_at)) : '-' }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</body>

</html>
