<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Print Report - {{ $ticket->ticket_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: rgba(0, 0, 0, 0.85);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
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
            position: relative;
        }

        .modal-icon {
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

        .print-btn {
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
            transition: background 0.2s;
            -webkit-tap-highlight-color: transparent;
        }

        .print-btn:active {
            background: #e55a2b;
            transform: scale(0.98);
        }

        .print-btn .btn-icon {
            font-size: 24px;
        }

        .close-link {
            display: block;
            margin-top: 20px;
            color: #999;
            text-decoration: none;
            font-size: 14px;
            padding: 10px;
        }

        .close-link:active {
            color: #666;
        }

        .report-frame {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            z-index: 9999;
            background: white;
        }

        .loading-text {
            display: none;
            color: #FF6B35;
            margin-top: 15px;
            font-size: 14px;
        }

        @media print {
            body {
                background: white;
            }

            .print-modal {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="print-modal" id="printModal">
        <span class="modal-icon">🖨️</span>
        <h2>Print Report</h2>
        <div class="ticket-number">#{{ $ticket->ticket_number }}</div>
        <p class="info-text">
            Tap the button below to start printing.<br>
            Make sure your printer is connected.
        </p>
        <button class="print-btn" onclick="startPrint()">
            <span class="btn-icon">📄</span> Print Now
        </button>
        <div class="loading-text" id="loadingText">
            Loading report...
        </div>
        <a href="javascript:history.back()" class="close-link">← Cancel & Go Back</a>
    </div>

    <iframe id="reportFrame" class="report-frame" src="{{ $reportUrl ?? '' }}"></iframe>

    <script>
        function startPrint() {
            const modal = document.getElementById('printModal');
            const loadingText = document.getElementById('loadingText');
            const iframe = document.getElementById('reportFrame');

            // Show loading
            loadingText.style.display = 'block';

            // Set iframe source if not set
            if (!iframe.src || iframe.src === 'about:blank') {
                iframe.src = '{{ $reportUrl ?? '' }}';
            }

            iframe.style.display = 'block';

            iframe.onload = function() {
                loadingText.style.display = 'none';
                setTimeout(function() {
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch (e) {
                        // Fallback
                        window.focus();
                        window.print();
                    }
                }, 800);
            };

            // If already loaded
            if (iframe.contentDocument && iframe.contentDocument.readyState === 'complete') {
                loadingText.style.display = 'none';
                setTimeout(function() {
                    iframe.contentWindow.print();
                }, 500);
            }
        }

        // Auto-detect if already loaded
        window.onload = function() {
            const iframe = document.getElementById('reportFrame');
            if (iframe.src && iframe.src !== 'about:blank') {
                // Already has source, might be ready
            }
        };
    </script>
</body>

</html>
