<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PR Photos - {{ $ticket->ticket_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            background: white;
            padding: 20px;
        }

        @media print {

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .photo-item {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            *:last-child {
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }

            body {
                overflow: hidden !important;
            }
        }

        .report-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #FF6B35;
        }

        .report-header h1 {
            color: #FF6B35;
            font-size: 22px;
            margin: 0;
        }

        .report-header p {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
            width: 100%;
            justify-content: center;
        }

        .photo-grid.single-photo {
            grid-template-columns: 1fr;
            max-width: 100%;
        }

        .photo-item {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            text-align: center;
        }

        .photo-item img {
            width: 100%;
            height: auto;
            max-height: 70vh;
            object-fit: contain;
            display: block;
        }

        .photo-caption {
            padding: 10px;
            background: #f9f9f9;
            font-size: 11px;
            color: #666;
            text-align: center;
            margin-top: auto;
        }

        @media print {
            .photo-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .photo-grid.single-photo {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .photo-grid {
                grid-template-columns: 1fr;
            }
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
    <div class="report-header">
        <h1>PURCHASE REQUEST PHOTOS</h1>
        <p>Ticket: {{ $ticket->ticket_number }} | Generated: {{ now()->format('d F Y, H:i') }}</p>
    </div>

    <div class="photo-grid {{ count($prPhotos) == 1 ? 'single-photo' : '' }}">
        @foreach ($prPhotos as $photo)
            <div class="photo-item">
                <img src="{{ $photo->url }}" alt="{{ $photo->file_name }}" loading="lazy">
                <div class="photo-caption">
                    <strong>{{ $photo->file_name }}</strong><br>
                    PR Number: {{ $photo->vr_number }}<br>
                    {{ $helper->formatDate($photo->created_at, 'd/m/Y H:i') }}
                    ({{ number_format($photo->file_size / 1024, 2) }} KB)
                </div>
            </div>
        @endforeach
    </div>

    @if (count($prPhotos) == 0)
        <div style="text-align: center; padding: 50px; color: #999;">
            <p>No PR photos found</p>
        </div>
    @endif

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
