<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MAINTENANCE REQUEST - {{ $ticket->ticket_number }}</title>
    <style>
        /* RESET & BASE */
        @page {
            margin: 0;
            padding: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000000;
            background: #ffffff;
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 15mm;
        }

        /* HEADER SECTION */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #FF6B35;
        }

        .logo-section {
            width: 300px;
            height: 80px;
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
            font-size: 28px;
            font-weight: bold;
            line-height: 1.2;
        }

        /* FORM META INFORMATION */
        .form-meta {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            margin-bottom: 10px;
            align-items: baseline;
        }

        .form-field {
            display: flex;
            align-items: baseline;
            gap: 10px;
            flex: 1;
        }

        .form-field label {
            font-weight: bold;
            font-size: 11pt;
            white-space: nowrap;
            width: 80px;
        }

        /* STYLE UNTUK NO DENGAN WARNA ORANGE */
        .form-field label.orange-label {
            color: #FF6B35 !important;
        }

        .field-content {
            flex: 1;
            min-height: 22px;
            padding: 2px 8px;
            font-size: 11pt;
            font-weight: bold;
        }

        .field-with-line {
            flex: 1;
            border-bottom: 2px solid #000;
            min-height: 22px;
            padding: 2px 8px;
            font-size: 11pt;
            font-weight: bold;
        }

        /* MAIN FORM SECTIONS */
        .form-section {
            margin-bottom: 15px;
        }

        .full-width-field {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
            align-items: baseline;
        }

        .full-width-field label {
            font-weight: bold;
            font-size: 11pt;
            white-space: nowrap;
            width: 100px;
        }

        .multi-line-field {
            margin-bottom: 12px;
        }

        .multi-line-field label {
            font-weight: bold;
            font-size: 11pt;
            display: block;
            margin-bottom: 5px;
        }

        .lines {
            min-height: 22px;
            margin-bottom: 8px;
            padding: 2px 8px;
            font-size: 11pt;
            border-bottom: 1px solid #ddd;
            background-color: #f9f9f9;
            font-style: italic;
        }

        /* SIGNATURE SECTIONS - DENGAN GARIS */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            padding: 15px 0;
        }

        .signature-box {
            text-align: center;
            flex: 1;
            padding: 0 10px;
        }

        .signature-box .label {
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 15px;
        }

        .signature-box .name-line {
            width: 90%;
            margin: 0 auto;
            padding: 4px;
            min-height: 25px;
            border-bottom: 2px solid #000;
            font-weight: bold;
        }

        /* VR SECTION - MINIMALIST DENGAN " | " */
        .vr-section {
            margin: 20px 0 25px 0;
        }

        .vr-section label {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            display: block;
        }

        .vr-content {
            font-size: 10pt;
            text-align: right;
            padding-right: 10px;
        }

        .vr-line {
            margin-bottom: 5px;
            padding: 5px;
            background-color: #f5f5f5;
            border-radius: 4px;
        }

        .vr-total {
            font-weight: bold;
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px solid #000;
            text-align: right;
        }

        /* COMPLETION SECTION - DENGAN GARIS */
        .completion-section {
            margin-top: 20px;
        }

        .completion-field {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
            align-items: baseline;
        }

        .completion-field label {
            font-weight: bold;
            font-size: 11pt;
            white-space: nowrap;
            width: 200px;
        }

        .completion-field .field-with-line {
            flex: 1;
        }

        /* FOOTER */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .footer img {
            width: 100%;
            height: auto;
            max-height: 100px;
        }

        /* STATUS BADGE */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
            margin-left: 10px;
            text-transform: uppercase;
        }

        .status-open {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status-received {
            background: #e3f2fd;
            color: #1565c0;
        }

        .status-in_progress {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        .status-closed {
            background: #f8f9fa;
            color: #495057;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        /* PRINT STYLES */
        @media print {
            body {
                padding: 10mm;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="logo-section">
                @if ($logoPath && file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Hotel Logo">
                @else
                    <div
                        style="width: 300px; height: 80px; background-color: #f0f0f0; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; color: #666;">
                        HOTEL LOGO
                    </div>
                @endif
            </div>
            <div class="title-section">
                <h1>MAINTENANCE<br>REQUEST</h1>
            </div>
        </div>

        <!-- Form Meta Information -->
        <div class="form-meta">
            <div class="form-row">
                <div class="form-field">
                    <label>TO</label>
                    <span>:</span>
                    <div class="field-content">ENGINEERING DEPT.</div>
                </div>
                <div class="form-field">
                    <label class="orange-label">No</label>
                    <span>:</span>
                    <div class="field-with-line" style="color: #FF6B35; font-weight: bold;">
                        {{ $ticket->ticket_number }}
                        <span class="status-badge status-{{ $ticket->status }}">
                            {{ str_replace('_', ' ', $ticket->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label>FROM</label>
                    <span>:</span>
                    <div class="field-content">{{ $ticket->user->name ?? 'Unknown' }}</div>
                </div>
                <div class="form-field">
                    <label>Date</label>
                    <span>:</span>
                    <div class="field-content">{{ $ticket->created_at->format('d F Y') }}</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label>DEPT</label>
                    <span>:</span>
                    <div class="field-content">{{ $ticket->department->name ?? 'N/A' }}</div>
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
                            N/A
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label>CATEGORY</label>
                    <span>:</span>
                    <div class="field-content">{{ $ticket->category->name ?? 'N/A' }}</div>
                </div>
                <div class="form-field">
                    <label>PRIORITY</label>
                    <span>:</span>
                    <div class="field-content"
                        style="color: {{ $ticket->priority->color ?? '#000' }}; font-weight: bold;">
                        {{ $ticket->priority->name ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form Fields - DENGAN GARIS -->
        <div class="form-section">
            <div class="full-width-field">
                <label>TITLE :</label>
                <div class="field-with-line">{{ $ticket->title }}</div>
            </div>

            <div class="multi-line-field">
                <label>PLEASE REPAIR :</label>
                <div class="lines">{!! nl2br(e($ticket->description)) !!}</div>
            </div>

            @if (count($followUpNotes) > 0)
                <div class="multi-line-field">
                    <label>FOLLOW UP :</label>
                    @foreach ($followUpNotes as $note)
                        <div class="lines">
                            [{{ $note['time'] }}] {{ $note['user'] }} ({{ $note['role'] }}): {{ $note['note'] }}
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($ticket->due_date)
                <div class="multi-line-field">
                    <label>DUE DATE :</label>
                    <div class="lines" style="font-weight: bold; color: #dc3545;">
                        {{ \Carbon\Carbon::parse($ticket->due_date)->format('d F Y, H:i') }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Signature Section 1 - REQUESTED, RECEIVED, OM APPROVED -->
        <div class="signature-section">
            @php
                $stage1 = $signaturesByStage[1] ?? null;
                $stage2 = $signaturesByStage[2] ?? null;
                $stage3 = $signaturesByStage[3] ?? null;
            @endphp

            <!-- Requested By -->
            <div class="signature-box">
                <div class="label">REQUESTED BY :</div>
                <div class="name-line">
                    @if ($stage1 && $stage1['has_signature'])
                        ✓ {{ $stage1['signature']->user->name ?? $ticket->user->name }}
                    @else
                        {{ $ticket->user->name ?? 'Unknown' }}
                    @endif
                </div>
                <div style="margin-top: 5px; font-size: 9pt;">
                    {{ $ticket->user->role ?? 'User' }} • {{ $ticket->created_at->format('d/m/Y') }}
                </div>
            </div>

            <!-- Received By -->
            <div class="signature-box">
                <div class="label">RECEIVED BY :</div>
                <div class="name-line">
                    @if ($stage2 && $stage2['has_signature'])
                        ✓ {{ $stage2['signature']->user->name ?? 'N/A' }}
                    @else
                        {{ $ticket->approval->admin_eng_received_by ? User::find($ticket->approval->admin_eng_received_by)->name : 'N/A' }}
                    @endif
                </div>
                <div style="margin-top: 5px; font-size: 9pt;">
                    Admin Engineering •
                    {{ $ticket->approval->admin_eng_received_at ? \Carbon\Carbon::parse($ticket->approval->admin_eng_received_at)->format('d/m/Y') : 'N/A' }}
                </div>
            </div>

            <!-- Approved By OM -->
            <div class="signature-box">
                <div class="label">APPROVED BY OM :</div>
                <div class="name-line">
                    @if ($stage3 && $stage3['has_signature'])
                        ✓ {{ $stage3['signature']->user->name ?? 'N/A' }}
                    @else
                        {{ $ticket->approval->om_approved_by ? User::find($ticket->approval->om_approved_by)->name : 'N/A' }}
                    @endif
                </div>
                <div style="margin-top: 5px; font-size: 9pt;">
                    Operations Manager •
                    {{ $ticket->approval->om_approved_at ? \Carbon\Carbon::parse($ticket->approval->om_approved_at)->format('d/m/Y') : 'N/A' }}
                </div>
            </div>
        </div>

        <!-- VR Section (Voucher Request) - Hanya tampil jika ada VR -->
        @if ($vrItems['has_vr'])
            <div class="vr-section">
                <label>VR :</label>
                <div class="vr-content">
                    <div class="vr-line">
                        @foreach ($vrItems['items'] as $index => $item)
                            {{ $item['name'] }} , {{ $item['qty'] }} unit , Rp {{ $item['price'] }}
                            @if (!$loop->last)
                                |
                            @endif
                        @endforeach
                    </div>
                    <div class="vr-total">
                        || TOTAL = Rp {{ $vrItems['total'] }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Completion Section -->
        @if ($completionInfo['has_info'])
            <div class="completion-section">
                <div class="completion-field">
                    <label>THE WORK WAS DONE ON :</label>
                    <div class="field-with-line">{{ $completionInfo['date'] }}</div>
                </div>

                <div class="completion-field">
                    <label>TECHNICIAN :</label>
                    <div class="field-with-line">{{ $completionInfo['technician'] }}</div>
                </div>
            </div>

            <!-- Signature Section 2 - COMPLETED, CHECKED, GM APPROVED -->
            <div class="signature-section">
                @php
                    $stage6 = $signaturesByStage[6] ?? null;
                    $stage7 = $signaturesByStage[7] ?? null;
                    $stage8 = $signaturesByStage[8] ?? null;
                @endphp

                <!-- Completed By -->
                <div class="signature-box">
                    <div class="label">COMPLETED BY :</div>
                    <div class="name-line">
                        @if ($stage6 && $stage6['has_signature'])
                            ✓ {{ $stage6['signature']->user->name ?? $completionInfo['technician'] }}
                        @else
                            {{ $completionInfo['technician'] }}
                        @endif
                    </div>
                    <div style="margin-top: 5px; font-size: 9pt;">
                        Technician • {{ $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y') : 'N/A' }}
                    </div>
                </div>

                <!-- Checked By -->
                <div class="signature-box">
                    <div class="label">CHECKED BY :</div>
                    <div class="name-line">
                        @if ($stage7 && $stage7['has_signature'])
                            ✓ {{ $stage7['signature']->user->name ?? 'N/A' }}
                        @else
                            {{ $ticket->approval->user_checked_by ? User::find($ticket->approval->user_checked_by)->name : 'N/A' }}
                        @endif
                    </div>
                    <div style="margin-top: 5px; font-size: 9pt;">
                        User •
                        {{ $ticket->approval->user_checked_at ? \Carbon\Carbon::parse($ticket->approval->user_checked_at)->format('d/m/Y') : 'N/A' }}
                    </div>
                </div>

                <!-- Approved By GM -->
                <div class="signature-box">
                    <div class="label">APPROVED BY GM :</div>
                    <div class="name-line">
                        @if ($stage8 && $stage8['has_signature'])
                            ✓ {{ $stage8['signature']->user->name ?? 'N/A' }}
                        @else
                            {{ $ticket->approval->gm_approved_by ? User::find($ticket->approval->gm_approved_by)->name : 'N/A' }}
                        @endif
                    </div>
                    <div style="margin-top: 5px; font-size: 9pt;">
                        General Manager •
                        {{ $ticket->approval->gm_approved_at ? \Carbon\Carbon::parse($ticket->approval->gm_approved_at)->format('d/m/Y') : 'N/A' }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Footer Section -->
        <div class="footer">
            @if ($footerPath && file_exists($footerPath))
                <img src="{{ $footerPath }}" alt="Footer">
            @else
                <div
                    style="width: 100%; height: 80px; background-color: #f0f0f0; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; color: #666; font-size: 10pt;">
                    <div style="text-align: center;">
                        <div style="font-weight: bold; margin-bottom: 5px;">
                            {{ config('app.name', 'Hotel Maintenance System') }}</div>
                        <div>Generated on {{ now()->format('d F Y, H:i') }}</div>
                        <div>Page 1 of 1</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
