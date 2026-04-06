<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATTACHMENTS - {{ $ticket->ticket_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #ffffff;
            padding: 0;
            margin: 0;
        }

        .attachment-page {
            page-break-after: always;
            width: 210mm;
            height: 297mm;
            display: flex;
            flex-direction: column;
            padding: 5mm;
            position: relative;
            overflow: hidden;
        }

        /* Hapus page-break-after untuk halaman terakhir */
        .attachment-page:last-child {
            page-break-after: auto;
        }

        .attachment-header {
            width: 100%;
            text-align: center;
            margin-bottom: 5px;
            padding-bottom: 3px;
            border-bottom: 2px solid #FF6B35;
            flex-shrink: 0;
        }

        .attachment-title {
            font-size: 14pt;
            font-weight: bold;
            color: #FF6B35;
        }

        .attachment-subtitle {
            font-size: 9pt;
            color: #666;
            margin-top: 2px;
            word-break: break-all;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 10px;
        }

        /* MAIN CONTAINER FOTO - INI YANG BIKIN FOTO GEDE BANGET */
        .attachment-image-container {
            flex: 1;
            width: 100%;
            min-height: 0;
            /* PENTING! */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2px 0;
        }

        /* WRAPPER FOTO - BUAT NGONTROL UKURAN */
        .photo-wrapper {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f8f8f8;
            border: 1px solid #eee;
        }

        /* FOTO PORTRAIT - FULL SETINGGI MUNGKIN */
        .attachment-full-image {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* UNTUK FILE BUKAN FOTO (PDF, ZIP, DLL) */
        .file-info-box {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: #f0f0f0;
            border: 2px dashed #FF6B35;
            padding: 20px;
        }

        .file-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .file-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
            word-break: break-word;
            text-align: center;
        }

        .file-size {
            font-size: 11pt;
            color: #666;
        }

        /* FOOTER INFO - DIGABUNG DENGAN UPLOAD INFO */
        .attachment-footer {
            width: 100%;
            text-align: center;
            margin-top: 5px;
            padding-top: 3px;
            border-top: 1px solid #ddd;
            font-size: 9pt;
            color: #666;
            flex-shrink: 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .footer-item {
            display: inline-block;
        }

        .separator {
            color: #FF6B35;
            font-weight: bold;
        }

        .empty-data {
            color: #888;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }

        /* Pastikan tidak ada extra page */
        .no-extra-page {
            page-break-after: avoid;
        }
    </style>
</head>

<body>
    @if (isset($imageAttachments) && count($imageAttachments) > 0)
        @foreach ($imageAttachments as $index => $attachment)
            <div class="attachment-page {{ $loop->last ? 'last-page' : '' }}">
                <!-- HEADER - HANYA JUDUL -->
                <div class="attachment-header">
                    <div class="attachment-title">
                        ATTACHMENT {{ $index + 1 }} of {{ count($imageAttachments) }}
                    </div>
                </div>

                <!-- MAIN CONTENT - FOTO GEDE BANGET -->
                <div class="attachment-image-container">
                    @php
                        $filePath = storage_path('app/public/' . $attachment->file_path);
                        $fileExists = file_exists($filePath);
                        $fileExtension = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg']);

                        if ($fileExists && $isImage) {
                            $imageInfo = @getimagesize($filePath);
                            if ($imageInfo) {
                                $width = $imageInfo[0];
                                $height = $imageInfo[1];
                                $isLandscape = $width > $height;
                                $ratio = $width / $height;
                            }
                        }
                    @endphp

                    @if ($fileExists && $isImage && isset($imageInfo))
                        <!-- FOTO - TANPA INFO DIMENSI DI SINI, INFO DI FOOTER -->
                        <div class="photo-wrapper">
                            <img src="{{ $filePath }}" class="attachment-full-image"
                                alt="{{ $attachment->file_name }}">
                        </div>
                    @elseif($fileExists && !$isImage)
                        <!-- FILE BUKAN FOTO (PDF, ZIP, DLL) - TAMPILIN INFO GEDE -->
                        <div class="file-info-box">
                            <div class="file-icon">
                                @php
                                    $icon = match ($fileExtension) {
                                        'pdf' => '📄',
                                        'doc', 'docx' => '📝',
                                        'xls', 'xlsx' => '📊',
                                        'zip', 'rar', '7z' => '🗜️',
                                        'txt' => '📃',
                                        default => '📎',
                                    };
                                @endphp
                                {{ $icon }}
                            </div>
                            <div class="file-name">{{ $attachment->file_name }}</div>
                            <div class="file-size">
                                @php
                                    $size = $attachment->file_size ?? 0;
                                    if ($size > 1048576) {
                                        echo round($size / 1048576, 2) . ' MB';
                                    } elseif ($size > 1024) {
                                        echo round($size / 1024, 2) . ' KB';
                                    } else {
                                        echo $size . ' bytes';
                                    }
                                @endphp
                            </div>
                        </div>
                    @else
                        <!-- FILE TIDAK DITEMUKAN -->
                        <div class="file-info-box" style="background: #fee;">
                            <div class="file-icon">❌</div>
                            <div class="file-name">{{ $attachment->file_name }}</div>
                            <div class="file-size">File not found</div>
                        </div>
                    @endif
                </div>

                <!-- FOOTER - INFO DIGABUNG (UPLOAD + DIMENSI) -->
                <div class="attachment-footer">
                    <span class="footer-item">📅
                        {{ $attachment->created_at ? date('d F Y, H:i', strtotime($attachment->created_at)) : '-' }}</span>
                    <span class="separator">|</span>
                    @if (isset($imageInfo))
                        <span class="footer-item">📷 {{ $width }} x {{ $height }}
                            ({{ $isLandscape ? 'Landscape' : 'Portrait' }})</span>
                    @else
                        <span class="footer-item">📁 {{ strtoupper($fileExtension) }} file</span>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="attachment-page">
            <div class="attachment-header">
                <div class="attachment-title">ATTACHMENTS</div>
            </div>
            <div class="attachment-image-container">
                <div class="file-info-box">
                    <div class="file-icon">📭</div>
                    <div class="file-name">No Attachments</div>
                    <div class="file-size">This ticket has no attachments</div>
                </div>
            </div>
        </div>
    @endif
</body>

</html>
