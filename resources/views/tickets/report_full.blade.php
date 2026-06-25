<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Report - {{ $ticket->ticket_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            background: #f0f2f5;
            padding: 20px;
        }

        @media print {

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                overflow: hidden !important;
                background: white !important;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .page-break {
                page-break-before: always;
            }

            *:last-child {
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }

            .action-buttons {
                display: none !important;
            }

            /* Reset container styles bawaan sub-blade saat print */
            .main-section .print-container,
            .pr-photos-section .print-container,
            .attachments-section .print-container {
                max-width: 100%;
                padding: 0;
                margin: 0;
                box-shadow: none;
                border-radius: 0;
                background: white;
            }
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
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .action-buttons .btn {
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-print {
            background: #FF6B35;
            color: white;
        }

        .btn-print:hover {
            background: #e55a2b;
            transform: translateY(-1px);
        }

        .btn-close {
            background: #6c757d;
            color: white;
        }

        .btn-close:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            body {
                padding: 0;
            }

            .action-buttons {
                border-radius: 0;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                margin: 0;
            }

            .action-buttons .btn {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <!-- Main Report -->
    <div class="main-section">
        {!! view('tickets.report_main', $mainData)->render() !!}
    </div>

    <!-- PR Photos -->
    @if ($hasPrPhotos)
        <div class="page-break"></div>
        <div class="pr-photos-section">
            {!! view('tickets.report_pr_photos', $prPhotosData)->render() !!}
        </div>
    @endif

    <!-- Attachments -->
    @if ($hasAttachments)
        <div class="page-break"></div>
        <div class="attachments-section">
            {!! view('tickets.report_attachments', $attachmentData)->render() !!}
        </div>
    @endif

    <!-- ACTION BUTTONS (hanya 1 set di paling bawah) -->
    <div class="action-buttons">
        <button onclick="window.print()" class="btn btn-print">
            <i class="fas fa-print"></i> Print Full Report
        </button>
        <button onclick="window.close()" class="btn btn-close">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    <script>
        @if (request()->has('print'))
            if (window.innerWidth > 768) {
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                    }, 1000);
                };
            }
        @endif
    </script>
</body>

</html>
