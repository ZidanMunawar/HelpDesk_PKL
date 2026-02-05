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
            line-height: 1.4;
            color: #000000;
            background: #ffffff;
            padding: 0;
            position: relative;
            min-height: 297mm;
            /* A4 height */
        }

        /* PERBAIKAN: Background status watermark - posisi lebih atas */
        .status-watermark {
            position: absolute;
            top: 40%;
            /* Naikkan lebih tinggi */
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72pt;
            /* Perbesar sedikit */
            font-weight: bold;
            color: rgba(255, 107, 53, 0.08);
            /* Opacity lebih rendah */
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #FF6B35;
        }

        .logo-section {
            width: 250px;
            height: 65px;
            background: transparent;
        }

        .logo-section img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .title-section h1 {
            color: #FF6B35;
            font-size: 22px;
            font-weight: bold;
            line-height: 1.1;
            text-align: right;
            margin: 0;
        }

        /* FORM META */
        .form-meta {
            margin-bottom: 12px;
        }

        .form-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            gap: 20px;
        }

        .form-field {
            display: flex;
            align-items: baseline;
            gap: 6px;
            flex: 1;
        }

        .form-field label {
            font-weight: bold;
            font-size: 10pt;
            white-space: nowrap;
            min-width: 75px;
        }

        .form-field label.orange-label {
            color: #FF6B35;
        }

        .field-content {
            flex: 1;
            min-height: 20px;
            padding: 2px 6px;
            font-size: 10pt;
        }

        /* GANTI: Field tanpa garis bawah untuk ticket number */
        .field-without-line {
            flex: 1;
            min-height: 22px;
            padding: 2px 6px;
            font-size: 10pt;
            color: #FF6B35;
            font-weight: bold;
        }

        .text-content {
            flex: 1;
            padding: 2px 6px;
            font-weight: bold;
            font-size: 10pt;
        }

        /* FORM SECTION */
        .form-section {
            margin-bottom: 12px;
        }

        .full-width-field {
            display: flex;
            gap: 6px;
            margin-bottom: 8px;
            align-items: baseline;
        }

        .full-width-field label {
            font-weight: bold;
            font-size: 10pt;
            white-space: nowrap;
            min-width: 100px;
        }

        .multi-line-field {
            margin-bottom: 10px;
        }

        .multi-line-field label {
            font-weight: bold;
            font-size: 10pt;
            display: block;
            margin-bottom: 4px;
        }

        /* GANTI: Box untuk please repair (tanpa garis) */
        .description-box {
            border: 1px solid #333;
            min-height: 60px;
            padding: 8px;
            font-size: 10pt;
            line-height: 1.3;
            margin-bottom: 5px;
            background: transparent;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lines {
            min-height: 22px;
            margin-bottom: 5px;
            padding: 2px 6px;
            font-size: 10pt;
            border-bottom: 1px solid #333;
        }

        /* PERBAIKAN: SIGNATURE SECTION - lebih rapi */
        .signature-section {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin: 15px 0;
            padding: 10px 0;
            border-top: 1px dashed #ddd;
            border-bottom: 1px dashed #ddd;
        }

        .signature-box {
            flex: 1;
            text-align: center;
            padding: 0 10px;
            min-height: 140px;
            /* Tinggi konsisten */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .signature-box .label {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 8px;
            min-height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1.2;
        }

        /* PERBAIKAN: Signature image lebih besar */
        .signature-img {
            width: 100%;
            max-width: 130px;
            /* Lebar lebih besar */
            max-height: 60px;
            /* Tinggi lebih besar */
            object-fit: contain;
            margin: 0 auto 8px;
            display: block;
            background: white;
            padding: 3px;
            border: 1px solid #ddd;
        }

        /* PERBAIKAN: Garis bawah nama lebih rapi */
        .signature-box .name-line {
            width: 100%;
            margin: 0 auto;
            padding: 4px 0;
            min-height: 24px;
            border-bottom: 1px solid #333;
            font-size: 9pt;
            word-wrap: break-word;
            text-align: center;
            position: relative;
        }

        /* PERBAIKAN: GM khusus - garis sama dengan lainnya */
        .signature-box:last-child .name-line {
            width: 100%;
            /* Pastikan sama lebar */
        }

        .signature-box .date-info {
            margin-top: 4px;
            font-size: 8pt;
            color: #555;
            line-height: 1.3;
            min-height: 20px;
        }

        /* PR SECTION */
        .pr-section {
            margin: 12px 0;
            padding: 8px 10px;
            background: transparent;
            border: 1px solid #ddd;
        }

        .pr-section label {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 5px;
            display: block;
            color: #FF6B35;
        }

        /* Table untuk PR items */
        .pr-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-top: 5px;
            background: transparent;
        }

        .pr-table th {
            background: #f5f5f5;
            border: 1px solid #ccc;
            padding: 4px 5px;
            text-align: left;
            font-weight: bold;
        }

        .pr-table td {
            border: 1px solid #ccc;
            padding: 3px 5px;
            vertical-align: top;
        }

        .pr-table .total-row td {
            font-weight: bold;
            background: #f9f9f9;
        }

        /* COMPLETION SECTION */
        .completion-section {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed #ddd;
        }

        .completion-field {
            display: flex;
            gap: 6px;
            margin-bottom: 8px;
            align-items: baseline;
        }

        .completion-field label {
            font-weight: bold;
            font-size: 10pt;
            white-space: nowrap;
            min-width: 170px;
        }

        /* FOOTER */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e0e0e0;
            position: relative;
            bottom: 0;
        }

        .footer img {
            width: 100%;
            height: auto;
            max-height: 80px;
            object-fit: cover;
            display: block;
        }

        .footer-info {
            text-align: center;
            font-size: 8pt;
            color: #666;
            margin-top: 6px;
        }

        .ticket-data {
            color: #222;
            font-weight: normal;
        }

        /* Fallback untuk data kosong */
        .empty-data {
            color: #888;
            font-style: italic;
        }

        /* Utility untuk align */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Watermark Status - DIATAS TTD -->
    <div class="status-watermark">
        {{ $statusDisplay }}
    </div>

    <div class="container">
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
                    {{-- Tanpa garis bawah --}}
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
                {{-- Gunakan box tanpa garis bawah --}}
                <div class="description-box">
                    {{ strip_tags($ticket->description) ?: '-' }}
                </div>
            </div>

            <div class="multi-line-field">
                <label>FOLLOW UP :</label>
                @foreach ($followUpComments as $comment)
                    <div class="lines" style="font-size: 9pt;">
                        @if (!empty($comment['date']))
                            [{{ $comment['date'] }}] {{ $comment['user'] }}:
                            {{ $comment['text'] }}
                        @else
                            &nbsp;
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Signature Section 1 -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="label">REQUESTED BY :</div>
                @if ($helper->hasSignature(1, $signatures))
                    <img src="{{ $helper->getSignaturePath(1, $signatures) }}" class="signature-img" alt="Signature">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div class="name-line">
                    {{ $helper->getSignatureData(1, $signatures, 'user', '-') }}
                </div>
                <div class="date-info">
                    {{ $helper->getSignatureData(1, $signatures, 'role', '-') }} •
                    {{ $helper->getSignatureData(1, $signatures, 'date', '-') }}
                </div>
            </div>
            <div class="signature-box">
                <div class="label">RECEIVED BY :</div>
                @if ($helper->hasSignature(2, $signatures))
                    <img src="{{ $helper->getSignaturePath(2, $signatures) }}" class="signature-img" alt="Signature">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div class="name-line">
                    {{ $helper->getSignatureData(2, $signatures, 'user', '-') }}
                </div>
                <div class="date-info">
                    {{ $helper->getSignatureData(2, $signatures, 'role', '-') }} •
                    {{ $helper->getSignatureData(2, $signatures, 'date', '-') }}
                </div>
            </div>
            <div class="signature-box">
                <div class="label">APPROVED BY OM :</div>
                @if ($helper->hasSignature(3, $signatures))
                    <img src="{{ $helper->getSignaturePath(3, $signatures) }}" class="signature-img" alt="Signature">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div class="name-line">
                    {{ $helper->getSignatureData(3, $signatures, 'user', '-') }}
                </div>
                <div class="date-info">
                    {{ $helper->getSignatureData(3, $signatures, 'role', '-') }} •
                    {{ $helper->getSignatureData(3, $signatures, 'date', '-') }}
                </div>
            </div>
        </div>

        <!-- PR Section -->
        @if ($prData)
            <div class="pr-section">
                <label>PR :</label>
                <table class="pr-table">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Item</th>
                            <th style="width: 15%; text-align: center;">Qty</th>
                            <th style="width: 20%; text-align: right;">Unit Price</th>
                            <th style="width: 15%; text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prItems as $item)
                            <tr>
                                <td>{{ $item->item_name ?: '-' }}</td>
                                <td style="text-align: center;">{{ $item->qty ?: '-' }}</td>
                                <td style="text-align: right;">Rp
                                    {{ $item->unit_price ? number_format($item->unit_price, 0, ',', '.') : '-' }}</td>
                                <td style="text-align: right;">Rp
                                    {{ $item->qty && $item->unit_price ? number_format($item->qty * $item->unit_price, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right; font-weight: bold;">Total:</td>
                            <td style="text-align: right; font-weight: bold;">
                                Rp {{ $totalPRAmount ? number_format($totalPRAmount, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif

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

        <!-- PERBAIKAN: Signature Section 2 - GM lebih rapi -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="label">COMPLETED BY :</div>
                @if ($helper->hasSignature(6, $signatures))
                    <img src="{{ $helper->getSignaturePath(6, $signatures) }}" class="signature-img" alt="Signature">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div class="name-line">
                    {{ $helper->getSignatureData(6, $signatures, 'user', '-') }}
                </div>
                <div class="date-info">
                    Technician • {{ $helper->getSignatureData(6, $signatures, 'date', '-') }}
                </div>
            </div>
            <div class="signature-box">
                <div class="label">CHECKED BY :</div>
                @if ($helper->hasSignature(7, $signatures))
                    <img src="{{ $helper->getSignaturePath(7, $signatures) }}" class="signature-img" alt="Signature">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div class="name-line">
                    {{ $helper->getSignatureData(7, $signatures, 'user', '-') }}
                </div>
                <div class="date-info">
                    {{ $helper->getSignatureData(7, $signatures, 'role', '-') }} •
                    {{ $helper->getSignatureData(7, $signatures, 'date', '-') }}
                </div>
            </div>
            <div class="signature-box">
                <div class="label">APPROVED BY GM :</div>
                @if ($helper->hasSignature(8, $signatures))
                    <img src="{{ $helper->getSignaturePath(8, $signatures) }}" class="signature-img"
                        alt="Signature">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div class="name-line">
                    {{ $helper->getSignatureData(8, $signatures, 'user', '-') }}
                </div>
                <div class="date-info">
                    General Manager • {{ $helper->getSignatureData(8, $signatures, 'date', '-') }}
                </div>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            @if (file_exists(public_path('assets/images/footer.png')))
                <img src="{{ public_path('assets/images/footer.png') }}" alt="Footer">
            @else
                <div
                    style="width: 100%; height: 60px; background: transparent; border: 1px solid #ddd;
                            display: flex; align-items: center; justify-content: center; color: #888; font-size: 8pt;">
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
