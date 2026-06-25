@extends('layouts.main')

@section('title', 'Maintenance Request #' . $ticket->ticket_number . ' | ' . config('app.name'))

@section('page-title', 'Maintenance Request Detail')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Maintenance Requests', 'url' => route('tickets.index')],
            ['title' => $ticket->ticket_number, 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2-bootstrap-5-theme.min.css') }}">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --navy: #003366;
            --orange: #ff6600;
            --navy-light: #1e4a7a;
            --orange-light: #ff8533;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --dark: #343a40;
            --light: #f8f9fa;
        }

        /* ============================================
                                                                                                                MAINTENANCE REQUEST HEADER - Navy & Orange Theme
                                                                                                            ============================================ */
        .ticket-header-container {
            background: white;
            border: 2px solid var(--navy);
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.08);
        }

        .header-title {
            background: var(--navy);
            color: white;
            padding: 15px 20px;
            text-align: center;
            font-weight: 700;
            font-size: 18px;
            letter-spacing: 1px;
        }

        .header-info {
            padding: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid var(--orange);
        }

        .info-item i {
            color: var(--orange);
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 100px;
            font-size: 13px;
        }

        .info-value {
            flex: 1;
            color: #333;
            font-size: 14px;
            font-weight: 500;
        }

        .required::after {
            content: " *";
            color: var(--danger);
        }

        .form-text {
            font-size: 12px;
            color: #6c757d;
        }

        .signature-container {
            min-height: 160px;
            position: relative;
        }

        .signature-container canvas {
            cursor: crosshair;
            touch-action: none;
        }

        /* ============================================
                                                                                                                STATUS & PRIORITY BADGES - Solid Colors
                                                                                                            ============================================ */
        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: white !important;
        }

        .status-open {
            background: #1565c0;
        }

        .status-received {
            background: #1565c0;
        }

        .status-pending_om {
            background: #856404;
        }

        .status-in_progress {
            background: #0c5460;
        }

        .status-pending_vr {
            background: #ff8f00;
        }

        .status-completed {
            background: #155724;
        }

        .status-pending_gm {
            background: #0d47a1;
        }

        .status-closed {
            background: #495057;
        }

        .status-cancelled {
            background: #721c24;
        }

        .status-ready_for_closure {
            background: #0c5460;
        }

        .priority-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 11px;
            color: white !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ============================================
                                                                                                                CURRENT STAGE INFO - Orange Solid
                                                                                                            ============================================ */
        .stage-info {
            background: var(--orange);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .stage-label {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .stage-name {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stage-progress {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }

        .stage-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transition: all 0.3s;
        }

        .stage-dot.active {
            background: white;
            transform: scale(1.3);
        }

        /* ============================================
                                                                                                                ACTION BUTTONS - Solid Colors
                                                                                                            ============================================ */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
            color: white;
            min-width: 160px;
            cursor: pointer;
            background: var(--navy);
        }

        .btn-action:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 51, 102, 0.2);
        }

        .btn-action:active {
            transform: scale(0.98);
        }

        .btn-primary {
            background: var(--navy);
        }

        .btn-success {
            background: var(--success);
        }

        .btn-warning {
            background: var(--orange);
        }

        .btn-danger {
            background: var(--danger);
        }

        .btn-info {
            background: var(--info);
        }

        .btn-secondary {
            background: #6c757d;
        }

        .btn-dark {
            background: var(--dark);
        }

        .btn-outline-secondary {
            background: transparent;
            border: 2px solid #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }

        /* ============================================
                                                                                                                TICKET BODY
                                                                                                            ============================================ */
        .ticket-body {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 51, 102, 0.05);
        }

        .ticket-title-section {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .ticket-title-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ticket-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.3;
            margin: 0;
        }

        .ticket-subtitle {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }

        .section-title {
            color: var(--navy);
            border-bottom: 2px solid var(--orange);
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .description-wrapper {
            position: relative;
            width: 100%;
            max-width: 793px;
            height: 210px;
            border: 1px solid #ff6a2a;
            border-radius: 6px;
            background: #f8f9fa;
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

        /* ============================================
                                                                                                                ATTACHMENTS - More organized
                                                                                                            ============================================ */
        .attachments-section {
            margin-top: 25px;
        }

        .attachment-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s ease;
            text-decoration: none;
            color: #555;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }

        .attachment-item:hover {
            background: #f8f9fa;
            border-color: var(--orange);
            color: var(--orange);
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 51, 102, 0.1);
        }

        .attachment-icon {
            font-size: 18px;
            color: #666;
        }

        .image-attachment {
            max-width: 200px;
            max-height: 150px;
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        .image-attachment:hover {
            transform: scale(1.05);
            border-color: var(--orange);
        }

        /* Image Gallery Grid */
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            transition: all 0.2s ease;
        }

        .gallery-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--orange);
        }

        .gallery-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            cursor: pointer;
        }

        .gallery-item .gallery-filename {
            padding: 8px;
            font-size: 11px;
            background: #f8f9fa;
            text-align: center;
            word-break: break-all;
            color: #666;
        }

        /* File List */
        .file-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 15px;
        }

        .file-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            transition: all 0.2s ease;
        }

        .file-item:hover {
            background: #fff;
            border-color: var(--orange);
        }

        .file-item i {
            font-size: 24px;
            color: var(--orange);
        }

        .file-item .file-info {
            flex: 1;
        }

        .file-item .file-name {
            font-weight: 500;
            color: #333;
        }

        .file-item .file-size {
            font-size: 11px;
            color: #999;
        }

        .file-item .file-download {
            color: var(--navy);
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .file-item .file-download:hover {
            background: var(--navy);
            color: white;
        }

        /* ============================================
                                                                            SIGNATURES SECTION - With Toggle & Responsive
                                                                        ============================================ */
        .signatures-section {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .signatures-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            margin-bottom: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--orange);
        }

        .signatures-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 18px;
            color: var(--navy);
            margin: 0;
        }

        .signatures-toggle-arrow {
            transition: transform 0.3s ease;
            font-size: 16px;
            color: #666;
        }

        .signatures-section.collapsed .signatures-toggle-arrow {
            transform: rotate(-90deg);
        }

        .signatures-container {
            margin-top: 20px;
            overflow: hidden;
            transition: max-height 0.3s ease;
            max-height: 2000px;
        }

        .signatures-section.collapsed .signatures-container {
            max-height: 0;
            margin-top: 0;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 0;
        }

        .signature-item {
            text-align: center;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #f9f9f9;
            transition: all 0.2s;
        }

        .signature-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 51, 102, 0.1);
        }

        .signature-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 15px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .signature-image {
            width: 300px;
            height: 200px;
            object-fit: contain;
            margin: 0 auto 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: transparent;
            padding: 5px;
            display: block;
        }

        .signature-info {
            font-size: 12px;
            color: #666;
            line-height: 1.4;
        }

        .signature-info strong {
            display: block;
            color: #333;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .signature-missing {
            color: #ff6b6b;
            font-size: 12px;
            margin-top: 5px;
        }

        /* Mobile Signature - Ukuran 162x108 */
        @media (max-width: 768px) {
            .signature-grid {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                gap: 12px;
                padding-bottom: 10px;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
            }

            .signature-grid::-webkit-scrollbar {
                height: 4px;
            }

            .signature-grid::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }

            .signature-grid::-webkit-scrollbar-thumb {
                background: var(--orange);
                border-radius: 4px;
            }

            .signature-item {
                min-width: 200px;
                flex-shrink: 0;
                padding: 12px;
            }

            .signature-label {
                font-size: 11px;
                margin-bottom: 10px;
            }

            .signature-image {
                width: 162px !important;
                height: 108px !important;
                margin-bottom: 10px;
            }

            .signature-info strong {
                font-size: 12px;
            }

            .signature-info {
                font-size: 10px;
            }
        }

        @media (max-width: 576px) {
            .signature-item {
                min-width: 180px;
                padding: 10px;
            }

            .signature-image {
                width: 140px !important;
                height: 93px !important;
            }

            .signature-label {
                font-size: 10px;
            }

            .signature-info strong {
                font-size: 11px;
            }
        }

        /* Print styles tetap sama */
        @media print {
            .signatures-section.collapsed .signatures-container {
                max-height: none !important;
                display: block !important;
            }

            .signatures-toggle-arrow {
                display: none !important;
            }

            .signature-image {
                width: 300px;
                height: 200px;
            }
        }

        /* ============================================
                                                                                                                PURCHASE REQUEST (PR) SECTION
                                                                                                            ============================================ */
        .pr-alert {
            background: #fff8e1;
            border: 1px solid #ffe082;
            border-left: 4px solid var(--orange);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .pr-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
        }

        .pr-card-header {
            background: var(--navy);
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .pr-card-body {
            padding: 20px;
        }

        .pr-photos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .pr-photo-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }

        .pr-photo-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            cursor: pointer;
        }

        .pr-photo-item .photo-description {
            padding: 8px;
            font-size: 11px;
            background: #f8f9fa;
            text-align: center;
            word-break: break-all;
        }

        .pr-notes {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
            font-size: 13px;
            border-left: 3px solid var(--orange);
        }

        /* ============================================
                                                                                            APPROVAL STATUS - Horizontal di semua device
                                                                                        ============================================ */
        .approval-status {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .approval-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .approval-item {
            text-align: center;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
        }

        .approval-status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: white;
        }

        .status-pending {
            background: var(--warning);
            color: #212529;
        }

        .status-approved {
            background: var(--success);
        }

        .status-rejected {
            background: var(--danger);
        }

        /* Mobile: tetap horizontal dengan scroll jika perlu */
        @media (max-width: 768px) {
            .approval-grid {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                gap: 12px;
                padding-bottom: 10px;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
            }

            .approval-grid::-webkit-scrollbar {
                height: 4px;
            }

            .approval-grid::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }

            .approval-grid::-webkit-scrollbar-thumb {
                background: var(--orange);
                border-radius: 4px;
            }

            .approval-item {
                min-width: 140px;
                flex-shrink: 0;
                padding: 12px;
            }
        }

        @media (max-width: 576px) {
            .approval-item {
                min-width: 130px;
                padding: 10px;
            }

            .approval-item .mb-2 {
                font-size: 11px;
            }

            .approval-status-badge {
                font-size: 10px;
                padding: 3px 8px;
            }
        }

        /* ============================================
                                                                                                                DUE DATE WIDGET
                                                                                                            ============================================ */
        .due-date-compact {
            background: var(--navy);
            color: white;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            box-shadow: 0 3px 6px rgba(0, 51, 102, 0.1);
        }

        .due-date-compact.overdue {
            background: var(--danger) !important;
        }

        .due-date-compact.completed {
            background: var(--success) !important;
        }

        .due-date-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .due-date-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .due-date-label i {
            font-size: 16px;
        }

        .due-date-status {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .due-date-value {
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            padding: 8px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 6px;
            letter-spacing: 0.5px;
        }

        /* ============================================
                                                                                                                COMMENTS SECTION
                                                                                                            ============================================ */
        .comments-section {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .comments-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            margin-bottom: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--orange);
        }

        .comments-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 18px;
            color: var(--navy);
            margin: 0;
        }

        .comments-toggle-arrow {
            transition: transform 0.3s ease;
            font-size: 16px;
            color: #666;
        }

        .comments-section.collapsed .comments-toggle-arrow {
            transform: rotate(-90deg);
        }

        .comments-container {
            margin-top: 20px;
            overflow: hidden;
            transition: max-height 0.3s ease;
            max-height: 2000px;
        }

        .comments-section.collapsed .comments-container {
            max-height: 0;
            margin-top: 0;
        }

        .comment-item {
            padding: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
            position: relative;
        }

        .comment-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .follow-up-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--orange);
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 12px;
            background: var(--navy);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }

        .comment-meta {
            flex: 1;
        }

        .comment-author {
            font-weight: 600;
            color: #333;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .comment-role {
            font-size: 12px;
            color: #666;
            background: #f0f0f0;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 5px;
        }

        .comment-time {
            font-size: 12px;
            color: #888;
        }

        .comment-body {
            color: #444;
            line-height: 1.6;
            font-size: 14px;
            padding-left: 52px;
        }

        .comment-body img {
            max-width: 100%;
            height: auto;
            border-radius: 6px;
            margin: 10px 0;
        }

        .comment-attachments {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #eee;
        }

        /* Comment Form */
        .comment-form-section {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            margin-top: 25px;
            transition: opacity 0.3s ease;
        }

        .comment-form-section.hidden {
            display: none;
        }

        .comment-form-section.disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        .comment-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            line-height: 1.6;
            resize: vertical;
            min-height: 120px;
        }

        .comment-textarea:focus {
            border-color: var(--orange);
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 102, 0, 0.1);
        }

        /* ============================================
                                                                                                                ACTIVITY SECTION
                                                                                                            ============================================ */
        .activity-section {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .activity-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            margin-bottom: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--orange);
        }

        .activity-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 18px;
            color: var(--navy);
            margin: 0;
        }

        .toggle-arrow {
            transition: transform 0.3s ease;
            font-size: 16px;
            color: #666;
        }

        .activity-section.collapsed .toggle-arrow {
            transform: rotate(-90deg);
        }

        .activity-timeline {
            position: relative;
            padding-left: 30px;
            margin-top: 20px;
            overflow: hidden;
            transition: max-height 0.3s ease;
            max-height: 2000px;
        }

        .activity-section.collapsed .activity-timeline {
            max-height: 0;
            margin-top: 0;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--orange);
        }

        .activity-item {
            position: relative;
            margin-bottom: 20px;
        }

        .activity-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--navy);
            border: 3px solid #f8f9fa;
            box-shadow: 0 0 0 2px var(--navy);
        }

        .activity-content {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .activity-content::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 15px;
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-right: 10px solid #e0e0e0;
        }

        .activity-content::after {
            content: '';
            position: absolute;
            left: -9px;
            top: 15px;
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-right: 10px solid white;
        }

        .activity-item-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .activity-icon {
            font-size: 16px;
            margin-right: 8px;
        }

        .activity-user {
            font-weight: 600;
            color: #333;
        }

        .activity-time {
            font-size: 12px;
            color: #888;
            margin-left: auto;
        }

        .activity-description {
            color: #444;
            line-height: 1.5;
            font-size: 13px;
        }

        /* ============================================
                                                                                                                MODAL STYLES
                                                                                                            ============================================ */
        .modal-dialog {
            margin: 0.5rem;
            max-width: 500px;
        }

        @media (min-width: 576px) {
            .modal-dialog {
                margin: 1.75rem auto;
                max-width: 500px;
            }
        }

        .modal-content {
            border-radius: 8px;
            overflow: hidden;
        }

        .modal-header {
            background: var(--navy) !important;
            color: white !important;
            padding: 1rem 1.5rem;
            border-bottom: none;
        }

        .modal-header .modal-title {
            color: white !important;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 1;
        }

        .modal-body {
            padding: 1.5rem;
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #dee2e6;
        }

        .modal-signature-canvas {
            border: 2px dashed #ddd;
            border-radius: 6px;
            cursor: crosshair;
            background: transparent;
            width: 100%;
            height: 200px;
            max-width: 300px;
            margin: 0 auto;
            display: block;
        }

        .signature-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            justify-content: center;
        }

        .signature-canvas-container {
            display: flex;
            justify-content: center;
            background: transparent;
            padding: 10px;
        }

        /* ============================================
                                                                                                                MOBILE RESPONSIVE
                                                                                                            ============================================ */
        @media (min-width: 993px) {
            .info-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }

        @media (max-width: 992px) {
            .info-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .info-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
                padding: 12px;
                min-height: 80px;
            }

            .info-label {
                min-width: auto;
                font-size: 12px;
                width: 100%;
            }

            .info-value {
                font-size: 13px;
                width: 100%;
                line-height: 1.4;
            }

            .ticket-title {
                font-size: 20px;
            }
        }

        @media (max-width: 768px) {
            .sidebar-quick-actions {
                display: none !important;
            }

            .action-buttons {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                padding: 12px;
                gap: 8px;
                margin-bottom: 15px;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .action-buttons::-webkit-scrollbar {
                display: none;
            }

            .btn-action {
                min-width: auto;
                padding: 10px 15px;
                font-size: 13px;
                white-space: nowrap;
                flex-shrink: 0;
                min-width: 70px;
                border-radius: 6px;
            }

            .btn-action i {
                font-size: 15px;
                margin-right: 6px;
            }

            .btn-action span {
                display: inline;
                font-size: 12px;
            }

            .info-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .info-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
                padding: 10px;
                min-height: 70px;
            }

            .info-item i {
                font-size: 16px;
                margin-bottom: 4px;
            }

            .info-label {
                font-size: 11px;
                min-width: auto;
                width: 100%;
            }

            .info-value {
                font-size: 12px;
                width: 100%;
                line-height: 1.3;
                word-break: break-word;
            }

            .ticket-title {
                font-size: 18px;
            }

            /* Signature Grid - Horizontal Scroll */
            .signature-grid {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                gap: 12px;
                padding-bottom: 10px;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }

            .signature-grid::-webkit-scrollbar {
                display: none;
            }

            .signature-item {
                min-width: 320px;
                flex-shrink: 0;
                padding: 15px;
            }

            /* Image Gallery */
            .image-gallery {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 10px;
            }

            .gallery-item img {
                height: 120px;
            }

            .pr-photos {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 10px;
            }

            .pr-photo-item img {
                height: 120px;
            }
        }

        @media (max-width: 576px) {
            .image-gallery {
                grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            }

            .gallery-item img {
                height: 100px;
            }

            .pr-photos {
                grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            }

            .pr-photo-item img {
                height: 100px;
            }

            .pr-card-header {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }

            .ticket-header-container {
                border: 2px solid #000;
                box-shadow: none;
            }

            .header-title {
                background: #000 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .action-buttons,
            .comment-form-section,
            .sidebar-filters,
            .sidebar-quick-actions {
                display: none !important;
            }

            .ticket-body,
            .signatures-section,
            .comments-section,
            .activity-section {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .activity-section.collapsed .activity-timeline,
            .comments-section.collapsed .comments-container {
                max-height: none !important;
                display: block !important;
            }

            .toggle-arrow,
            .comments-toggle-arrow {
                display: none !important;
            }

            .status-badge,
            .priority-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .text-navy {
            color: var(--navy) !important;
        }

        .bg-navy {
            background-color: var(--navy) !important;
        }

        .border-navy {
            border-color: var(--navy) !important;
        }

        .text-orange {
            color: var(--orange) !important;
        }

        .bg-orange {
            background-color: var(--orange) !important;
        }

        .border-orange {
            border-color: var(--orange) !important;
        }

        /* Image Modal */
        .image-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            cursor: pointer;
        }

        .image-modal-content {
            max-width: 90%;
            max-height: 90%;
            position: relative;
            cursor: default;
        }

        .image-modal-content img {
            max-width: 100%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .image-modal-close {
            position: absolute;
            top: -40px;
            right: 0;
            background: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            color: #333;
        }

        .followup-alert {
            background: #fff8e1;
            border: 1px solid var(--orange);
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .followup-alert .btn {
            background: var(--orange);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
        }

        .followup-alert .btn:hover {
            opacity: 0.9;
        }
    </style>
@endpush

@section('content')
    @php
        function getDisplayStage($actualStage)
        {
            $stageMapping = [
                1 => 1, // Requested
                2 => 2, // Received
                3 => 3, // OM Approval
                4 => 4, // In Progress
                5 => 4, // PR (ditampilkan sebagai stage 4 juga)
                6 => 5, // Completed
                7 => 6, // User Check Done
                8 => 7, // GM Approval
                9 => 8, // Closed
            ];
            return $stageMapping[$actualStage] ?? $actualStage;
        }

        function stringToColorPHP($str)
        {
            if (!$str) {
                return '#6c757d';
            }
            $hash = 0;
            for ($i = 0; $i < strlen($str); $i++) {
                $hash = ord($str[$i]) + (($hash << 5) - $hash);
            }
            $color = '#';
            for ($i = 0; $i < 3; $i++) {
                $value = ($hash >> $i * 8) & 0xff;
                $color .= str_pad(dechex($value), 2, '0', STR_PAD_LEFT);
            }
            return $color;
        }

        function getStageName($stage)
        {
            $stages = [
                1 => 'Requested by User (Open)',
                2 => 'Received by Admin Engineering',
                3 => 'OM Approval',
                4 => 'In Progress / Technician Working',
                5 => 'Waiting PR Approval',
                6 => 'Completed by Technician',
                7 => 'User Check Done - Waiting GM',
                8 => 'GM Approved - Ready for Closure',
                9 => 'Closed by Admin',
            ];
            return $stages[$stage] ?? 'Unknown Stage';
        }

        $displayStage = getDisplayStage($ticket->current_stage);
        $user = auth()->user();
        $hasSignature = !empty($user->signature_path) && Storage::disk('public')->exists($user->signature_path);
        $canSaveSignature = in_array($user->role, ['admin_eng', 'om', 'gm']);

        // Determine available actions
        $availableActions = [];
        $ticketStatus = $ticket->status;
        $userRole = $user->role;

        // Admin Engineering Actions
        if ($userRole === 'admin_eng') {
            if ($ticketStatus === 'open') {
                $availableActions[] = 'receive';
                $availableActions[] = 'cancel';
            } elseif (in_array($ticket->status, ['in_progress', 'pending_vr'])) {
                $availableActions[] = 'assign';
            } elseif ($ticketStatus === 'pending_vr') {
                $availableActions[] = 'create_pr';
                $availableActions[] = 'assign';
                $availableActions[] = 'cancel';
            } elseif ($ticketStatus === 'ready_for_closure') {
                $availableActions[] = 'close_admin';
            }
        }

        // OM Actions
        if ($userRole === 'om' && $ticketStatus === 'pending_om') {
            $availableActions[] = 'om_approve';
            $availableActions[] = 'om_reject';
        }

        // Technician Actions
        if ($userRole === 'technician' && $ticket->assigned_to == $user->id) {
            if ($ticket->status === 'in_progress') {
                $availableActions[] = 'complete';
                $availableActions[] = 'need_pr';
            }
        }

        // User/Reporter Actions
        $isReporter = $ticket->user_id == $user->id && in_array($user->role, ['user', 'admin_eng']);
        if ($isReporter) {
            if ($ticketStatus === 'completed') {
                $availableActions[] = 'user_check_accept';
                $availableActions[] = 'user_check_reject';
            }
        }

        // Manager Actions (for department tickets)
        $isManagerForDepartment = $user->role === 'manager' && $ticket->department_id === $user->department_id;
        if ($isManagerForDepartment && $ticketStatus === 'completed') {
            $availableActions[] = 'user_check_accept';
            $availableActions[] = 'user_check_reject';
        }

        // GM Actions
        if ($userRole === 'gm' && $ticketStatus === 'pending_gm') {
            $availableActions[] = 'gm_approve';
            $availableActions[] = 'gm_reject';
        }

        $availableActions[] = 'print';
        $availableActions[] = 'back';

        // Check PR status
        $hasPR = $ticket->voucherRequests->count() > 0;
        $needsPR = $ticket->approval->needs_vr ?? false;
        $hasPaidPR = false;
        $hasPendingOrApprovedPR = false;

        foreach ($ticket->voucherRequests as $pr) {
            if ($pr->status === 'paid') {
                $hasPaidPR = true;
            }
            if (in_array($pr->status, ['pending', 'admin_approved', 'om_approved', 'gm_approved', 'paid'])) {
                $hasPendingOrApprovedPR = true;
            }
        }

        $canShowNeedPRButton = false;
        if ($userRole === 'technician' && $ticket->assigned_to == $user->id) {
            if ($ticket->status === 'in_progress') {
                if (!$hasPR) {
                    $canShowNeedPRButton = true;
                } else {
                    $allPRRejected = true;
                    foreach ($ticket->voucherRequests as $pr) {
                        if ($pr->status !== 'rejected') {
                            $allPRRejected = false;
                            break;
                        }
                    }
                    $canShowNeedPRButton = $allPRRejected;
                }
            }
        }

        // Separate attachments by type
        $imageAttachments = $ticket->attachments
            ->filter(function ($attachment) {
                $extension = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg']);
            })
            ->values();

        $fileAttachments = $ticket->attachments
            ->filter(function ($attachment) {
                $extension = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                return !in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg']);
            })
            ->values();

        $hasImageAttachments = $imageAttachments->count() > 0;
        $hasFileAttachments = $fileAttachments->count() > 0;

        $isOverdue = $ticket->due_date && $ticket->due_date < now();

        function getStatusDisplay($status)
        {
            $statusMap = [
                'open' => 'Open',
                'received' => 'Received',
                'pending_om' => 'OM Approval',
                'in_progress' => 'In Progress',
                'pending_vr' => 'PR Approval',
                'completed' => 'Completed',
                'pending_gm' => 'GM Approval',
                'ready_for_closure' => 'Ready for Closure',
                'closed' => 'Closed',
                'cancelled' => 'Cancelled',
            ];
            return $statusMap[$status] ?? str_replace('_', ' ', $status);
        }

        // Check if user can comment
        $canComment = false;

        if ($user->role === 'admin_eng') {
            $canComment = true;
        } elseif ($user->role === 'manager' && $ticket->department_id === $user->department_id) {
            $canComment = true;
        } elseif ($ticket->user_id === $user->id) {
            $canComment = true;
        } elseif ($user->role === 'technician' && $ticket->assigned_to === $user->id) {
            $canComment = true;
        }

        $showComments = in_array($ticket->status, [
            'received',
            'pending_om',
            'in_progress',
            'pending_vr',
            'completed',
            'pending_gm',
            'ready_for_closure',
        ]);

        $needsAdminFollowup = $ticket->approval->needs_admin_followup ?? false;

        $hasPRPhotos = false;
        foreach ($ticket->voucherRequests as $pr) {
            if ($pr->attachments && $pr->attachments->count() > 0) {
                $hasPRPhotos = true;
                break;
            }
        }
    @endphp

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 mb-4 no-print">
            <div class="card">
                <div class="card-body">
                    <!-- Stage Progress -->
                    <div class="mb-4">
                        <h6 class="mb-3"><i class="fas fa-project-diagram me-2"></i> Current Step</h6>
                        <div class="stage-info">
                            <div class="stage-label">STEP {{ $displayStage }}</div>
                            <div class="stage-name">{{ getStageName($ticket->current_stage) }}</div>
                            <div class="stage-progress">
                                @for ($i = 1; $i <= 8; $i++)
                                    <div class="stage-dot {{ $i <= $displayStage ? 'active' : '' }}"></div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="sidebar-quick-actions">
                        <h6 class="mb-3"><i class="fas fa-bolt me-2"></i> Quick Actions</h6>
                        <div class="list-group list-group-flush mb-4">
                            @if ($hasSignature && $canSaveSignature && in_array($user->role, ['admin_eng', 'om', 'gm']))
                                @php
                                    $canQuickApprove = false;
                                    $quickApproveMessage = '';

                                    if ($user->role === 'admin_eng' && $ticket->status === 'open') {
                                        $canQuickApprove = true;
                                        $quickApproveMessage = 'Quick receive using saved signature';
                                    } elseif ($user->role === 'om' && $ticket->status === 'pending_om') {
                                        $canQuickApprove = true;
                                        $quickApproveMessage = 'Quick approve using saved signature';
                                    } elseif ($user->role === 'gm' && $ticket->status === 'pending_gm') {
                                        $canQuickApprove = true;
                                        $quickApproveMessage = 'Quick approve using saved signature';
                                    }
                                @endphp

                                @if ($canQuickApprove)
                                    <a href="#" class="list-group-item list-group-item-action text-success"
                                        onclick="quickApprove()">
                                        <i class="fas fa-bolt me-2"></i> {{ $quickApproveMessage }}
                                    </a>
                                @endif
                            @endif

                            @if ($ticket->status === 'received' && $user->role === 'admin_eng')
                                <a href="#" class="list-group-item list-group-item-action" onclick="continueToOM()">
                                    <i class="fas fa-forward me-2"></i> Continue to OM
                                </a>
                            @endif

                            @if (in_array('receive', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action text-navy"
                                    onclick="openReceiveModal()">
                                    <i class="fas fa-check-circle me-2"></i> Receive Request
                                    @if ($hasSignature && $canSaveSignature)
                                        <small class="d-block text-muted">Click to choose signature option</small>
                                    @endif
                                </a>
                            @endif

                            @if (in_array('assign', $availableActions) && in_array($ticket->status, ['in_progress', 'pending_vr']))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openAssignModal()">
                                    <i class="fas fa-user-plus me-2"></i> Assign Technician
                                </a>
                            @endif

                            @if (in_array('om_approve', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openOmApproveModal()">
                                    <i class="fas fa-thumbs-up me-2"></i> OM Approve
                                    @if ($hasSignature && $canSaveSignature)
                                        <small class="d-block text-muted">Click to choose signature option</small>
                                    @endif
                                </a>
                            @endif

                            @if (in_array('om_reject', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openOmRejectModal()">
                                    <i class="fas fa-thumbs-down me-2"></i> OM Reject
                                </a>
                            @endif

                            @if (in_array('complete', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openCompleteModal()">
                                    <i class="fas fa-check-double me-2"></i> Mark Complete
                                </a>
                            @endif

                            @if ($canShowNeedPRButton)
                                <a href="#" class="list-group-item list-group-item-action" onclick="openPRModal()">
                                    <i class="fas fa-file-invoice-dollar me-2"></i> Need PR
                                </a>
                            @endif

                            @if (in_array('create_pr', $availableActions))
                                <a href="{{ route('tickets.vr.create', $ticket->id) }}"
                                    class="list-group-item list-group-item-action">
                                    <i class="fas fa-file-invoice me-2"></i> Create PR
                                </a>
                            @endif

                            @if (($isReporter || $isManagerForDepartment) && $ticket->status === 'completed')
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openUserCheckAcceptModal()">
                                    <i class="fas fa-clipboard-check me-2"></i> Accept Completion
                                </a>
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openUserCheckRejectModal()">
                                    <i class="fas fa-times-circle me-2"></i> Reject Completion
                                </a>
                            @endif

                            @if (in_array('gm_approve', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openGmApproveModal()">
                                    <i class="fas fa-gavel me-2"></i> GM Approve
                                    @if ($hasSignature && $canSaveSignature)
                                        <small class="d-block text-muted">Click to choose signature option</small>
                                    @endif
                                </a>
                            @endif

                            @if (in_array('gm_reject', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openGmRejectModal()">
                                    <i class="fas fa-ban me-2"></i> GM Reject
                                </a>
                            @endif

                            @if (in_array('close_admin', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="closeTicketAdmin()">
                                    <i class="fas fa-lock me-2"></i> Close Request
                                </a>
                            @endif

                            @if ($user->role === 'superadmin')
                                <a href="#" class="list-group-item list-group-item-action text-danger"
                                    onclick="deleteTicket()">
                                    <i class="fas fa-trash-alt me-2"></i> Delete Request
                                    <small class="d-block text-muted">Permanent deletion</small>
                                </a>
                            @endif

                            @if (in_array('cancel', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action text-danger"
                                    onclick="openCancelModal()">
                                    <i class="fas fa-ban me-2"></i> Cancel Request
                                </a>
                            @endif

                            <a href="{{ route('tickets.index') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-arrow-left me-2"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <!-- Quick Info -->
                    <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i> Quick Info</h6>
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-muted">Created:</span>
                            <span>{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-muted">Last Update:</span>
                            <span>{{ $ticket->updated_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if ($ticket->assigned_to)
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="text-muted">Technician:</span>
                                <span>{{ $ticket->assignedUser->name ?? 'N/A' }}</span>
                            </div>
                        @endif
                        @if ($ticket->resolved_at)
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                <span class="text-muted">Resolved:</span>
                                <span>{{ $ticket->resolved_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                        @if ($ticket->closed_at)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Closed:</span>
                                <span>{{ $ticket->closed_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9 col-md-8">
            <!-- PR Alert -->
            @if ($needsPR && !$hasPR && $ticket->status === 'pending_vr')
                <div class="pr-alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                        <div>
                            <h6 class="mb-1"><strong>Purchase Request Required</strong></h6>
                            <p class="mb-0">This maintenance request requires a Purchase Request (PR) before work can
                                continue.</p>
                            @if (in_array('create_pr', $availableActions))
                                <a href="{{ route('tickets.vr.create', $ticket->id) }}"
                                    class="btn btn-warning btn-sm mt-2" style="background: var(--orange); border: none;">
                                    <i class="fas fa-file-invoice me-1"></i> Create PR
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Admin Follow-up Alert -->
            @if ($needsAdminFollowup && $user->role === 'admin_eng')
                <div class="followup-alert">
                    <div>
                        <i class="fas fa-exclamation-triangle" style="color: var(--orange);"></i>
                        <strong>Follow-up Notes Required</strong>
                        <p class="mb-0 small">Technician completed work without follow-up notes. Please add them.</p>
                    </div>
                    <button class="btn" onclick="openAddFollowupModal()">
                        <i class="fas fa-plus me-1"></i> Add Follow-up Notes
                    </button>
                </div>
            @endif

            <!-- Due Date Widget -->
            @if ($ticket->due_date)
                @php
                    $isOverdue =
                        $ticket->due_date < now() && !in_array($ticket->status, ['completed', 'closed', 'cancelled']);
                    $isCompletedOrClosed = in_array($ticket->status, ['completed', 'closed']);
                @endphp
                <div class="due-date-compact {{ $isOverdue ? 'overdue' : ($isCompletedOrClosed ? 'completed' : '') }}">
                    <div class="due-date-header">
                        <div class="due-date-label">
                            <i class="fas fa-calendar-check"></i>
                            <span>DUE DATE</span>
                        </div>
                        @if ($isOverdue)
                            <span class="due-date-status">OVERDUE</span>
                        @elseif($isCompletedOrClosed)
                            <span class="due-date-status">COMPLETED</span>
                        @else
                            <span class="due-date-status">ACTIVE</span>
                        @endif
                    </div>
                    <div class="due-date-value">
                        {{ \Carbon\Carbon::parse($ticket->due_date)->format('d M Y, H:i') }}
                    </div>
                </div>
            @endif

            <!-- Action Buttons Bar -->
            <div class="action-buttons no-print">
                @if ($hasSignature && $canSaveSignature && in_array($user->role, ['admin_eng', 'om', 'gm']))
                    @php
                        $showQuickApprove = false;
                        $quickApproveText = '';

                        if ($user->role === 'admin_eng' && $ticket->status === 'open') {
                            $showQuickApprove = true;
                            $quickApproveText = 'Quick Receive';
                        } elseif ($user->role === 'om' && $ticket->status === 'pending_om') {
                            $showQuickApprove = true;
                            $quickApproveText = 'Quick Approve';
                        } elseif ($user->role === 'gm' && $ticket->status === 'pending_gm') {
                            $showQuickApprove = true;
                            $quickApproveText = 'Quick Approve';
                        }
                    @endphp

                    @if ($showQuickApprove)
                        <button class="btn-action btn-success" onclick="quickApprove()">
                            <i class="fas fa-bolt"></i><span> {{ $quickApproveText }}</span>
                        </button>
                    @endif
                @endif

                @if ($ticket->status === 'received' && $user->role === 'admin_eng')
                    <button class="btn-action btn-primary" onclick="continueToOM()">
                        <i class="fas fa-forward me-2"></i><span>Continue to OM</span>
                    </button>
                @endif

                @if (in_array('receive', $availableActions))
                    <button class="btn-action btn-primary" onclick="openReceiveModal()">
                        <i class="fas fa-check-circle"></i><span>Receive Request</span>
                    </button>
                @endif
                @if (in_array($user->role, ['admin_eng', 'superadmin']) && $ticket->status === 'open')
                    <button class="btn-action btn-primary" onclick="openEditTicketModal()" style="background: #17a2b8;">
                        <i class="fas fa-edit"></i><span> Edit Ticket</span>
                    </button>
                @endif
                @if (in_array('om_approve', $availableActions))
                    <button class="btn-action btn-success" onclick="openOmApproveModal()">
                        <i class="fas fa-thumbs-up"></i> <span>OM Approve</span>
                    </button>
                @endif

                @if (in_array('om_reject', $availableActions))
                    <button class="btn-action btn-danger" onclick="openOmRejectModal()">
                        <i class="fas fa-thumbs-down"></i> <span>OM Reject</span>
                    </button>
                @endif

                @if (in_array('complete', $availableActions))
                    <button class="btn-action btn-success" onclick="openCompleteModal()">
                        <i class="fas fa-check-double"></i> <span>Mark Complete</span>
                    </button>
                @endif

                @if ($canShowNeedPRButton)
                    <button class="btn-action btn-warning" onclick="openPRModal()">
                        <i class="fas fa-file-invoice-dollar"></i><span> Need PR</span>
                    </button>
                @endif

                @if (($isReporter || $isManagerForDepartment) && $ticket->status === 'completed')
                    <button class="btn-action btn-success" onclick="openUserCheckAcceptModal()">
                        <i class="fas fa-clipboard-check"></i><span> Accept Completion</span>
                    </button>
                    <button class="btn-action btn-danger" onclick="openUserCheckRejectModal()">
                        <i class="fas fa-times-circle"></i> <span>Reject Completion</span>
                    </button>
                @endif

                @if (in_array('gm_approve', $availableActions))
                    <button class="btn-action btn-success" onclick="openGmApproveModal()">
                        <i class="fas fa-gavel"></i><span> GM Approve</span>
                    </button>
                @endif

                @if (in_array('gm_reject', $availableActions))
                    <button class="btn-action btn-danger" onclick="openGmRejectModal()">
                        <i class="fas fa-ban"></i> <span>GM Reject</span>
                    </button>
                @endif

                @if (in_array('create_pr', $availableActions))
                    <a href="{{ route('tickets.vr.create', $ticket->id) }}" class="btn-action btn-warning">
                        <i class="fas fa-file-invoice"></i> <span> Create PR</span>
                    </a>
                @endif

                @if (in_array('assign', $availableActions) && in_array($ticket->status, ['in_progress', 'pending_vr']))
                    <button class="btn-action btn-primary" onclick="openAssignModal()">
                        <i class="fas fa-user-plus"></i><span> Assign Technician</span>
                    </button>
                @endif

                @if (in_array('cancel', $availableActions))
                    <button class="btn-action btn-danger" onclick="openCancelModal()">
                        <i class="fas fa-ban"></i><span> Cancel Request</span>
                    </button>
                @endif

                @if (in_array('close_admin', $availableActions))
                    <button class="btn-action btn-dark" onclick="closeTicketAdmin()">
                        <i class="fas fa-lock"></i> <span>Close Request</span>
                    </button>
                @endif

                @if (in_array($user->role, ['superadmin', 'admin_eng', 'om', 'gm']) ||
                        $ticket->user_id == $user->id ||
                        $ticket->assigned_to == $user->id)
                    <button class="btn-action btn-info" type="button" onclick="openReportModal()"
                        style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-file-pdf"></i>
                        <span>Report</span>
                    </button>
                @endif
                <a href="{{ route('tickets.index') }}" class="btn-action btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i><span> Back to List</span>
                </a>
            </div>

            <!-- TICKET HEADER -->
            <div class="ticket-header-container">
                <div class="header-title">
                    MAINTENANCE REQUEST - {{ strtoupper($ticket->location->hotel ?? 'HARRIS') }} HOTEL
                </div>
                <div class="header-info">
                    <div class="info-grid">
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <span class="info-label">FROM:</span>
                            <span class="info-value">{{ $ticket->user->name }} -
                                {{ $ticket->department->name ?? 'No Department' }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-building"></i>
                            <span class="info-label">TO:</span>
                            <span class="info-value">Engineering Department</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-ticket-alt"></i>
                            <span class="info-label">REQUEST NO:</span>
                            <span class="info-value" style="font-weight: 700; color: var(--navy);">
                                #{{ $ticket->ticket_number }}
                            </span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar"></i>
                            <span class="info-label">DATE:</span>
                            <span class="info-value">{{ $ticket->created_at->format('d F Y, H:i A') }}</span>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span class="info-label">LOCATION:</span>
                            <span class="info-value">
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
                            </span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-folder"></i>
                            <span class="info-label">CATEGORY:</span>
                            <span class="info-value">{{ $ticket->category->name }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-flag"></i>
                            <span class="info-label">PRIORITY:</span>
                            <span class="info-value">
                                <span class="priority-badge" style="background-color: {{ $ticket->priority->color }};">
                                    {{ $ticket->priority->name }}
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-info-circle"></i>
                            <span class="info-label">STATUS:</span>
                            <span class="info-value">
                                <span class="status-badge status-{{ $ticket->status }}">
                                    {{ getStatusDisplay($ticket->status) }}
                                </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TICKET BODY -->
            <div class="ticket-body">
                <div class="ticket-title-section">
                    <div class="ticket-title-label">MAINTENANCE REQUEST TITLE</div>
                    <h2 class="ticket-title">{{ $ticket->title ?? 'Maintenance Request' }}</h2>
                    @if ($ticket->subtitle)
                        <div class="ticket-subtitle">{{ $ticket->subtitle }}</div>
                    @endif
                </div>

                <h5 class="section-title">
                    <i class="fas fa-tools"></i> PLEASE REPAIR:
                </h5>

                <div class="scroll-hint">(scroll horizontally)
                </div>

                <div class="description-container">
                    <div class="description-wrapper">
                        <div class="description-content">{{ $ticket->description }}</div>
                    </div>
                </div>
            </div>

            <!-- ATTACHMENTS SECTION - More organized -->
            @if ($hasImageAttachments || $hasFileAttachments)
                <div class="attachments-section">
                    <h5 class="section-title">
                        <i class="fas fa-paperclip"></i> ATTACHED FILES
                    </h5>

                    @if ($hasImageAttachments)
                        <h6 class="mb-2"><i class="fas fa-image me-2"></i>Images ({{ $imageAttachments->count() }})
                        </h6>
                        <div class="image-gallery">
                            @foreach ($imageAttachments as $attachment)
                                <div class="gallery-item">
                                    <img src="{{ Storage::url($attachment->file_path) }}"
                                        alt="{{ $attachment->file_name }}"
                                        data-src="{{ Storage::url($attachment->file_path) }}">
                                    <div class="gallery-filename">
                                        {{ Str::limit($attachment->file_name, 20) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($hasFileAttachments)
                        <h6 class="mb-2 mt-3"><i class="fas fa-file me-2"></i>Documents ({{ $fileAttachments->count() }})
                        </h6>
                        <div class="file-list">
                            @foreach ($fileAttachments as $attachment)
                                <div class="file-item">
                                    <i
                                        class="fas fa-file-{{ pathinfo($attachment->file_name, PATHINFO_EXTENSION) === 'pdf' ? 'pdf' : 'alt' }}"></i>
                                    <div class="file-info">
                                        <div class="file-name">{{ $attachment->file_name }}</div>
                                        <div class="file-size">{{ round($attachment->file_size / 1024) }} KB</div>
                                    </div>
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                        class="file-download" download>
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- APPROVAL STATUS -->
        @if ($ticket->approval)
            <div class="approval-status">
                <h5 class="section-title">
                    <i class="fas fa-clipboard-check"></i> APPROVAL STATUS
                </h5>
                <div class="approval-grid">
                    <div class="approval-item">
                        <div class="mb-2">Admin Received</div>
                        @if ($ticket->approval->admin_eng_received)
                            <span class="approval-status-badge status-approved">Approved</span>
                            <div class="small mt-1">
                                {{ $ticket->approval->admin_eng_received_at ? $ticket->approval->admin_eng_received_at->format('d M Y, H:i') : '' }}
                            </div>
                        @else
                            <span class="approval-status-badge status-pending">Pending</span>
                        @endif
                    </div>
                    <div class="approval-item">
                        <div class="mb-2">OM Approval</div>
                        @if ($ticket->approval->om_approved)
                            <span class="approval-status-badge status-approved">Approved</span>
                            <div class="small mt-1">
                                {{ $ticket->approval->om_approved_at ? $ticket->approval->om_approved_at->format('d M Y, H:i') : '' }}
                            </div>
                        @else
                            <span class="approval-status-badge status-pending">Pending</span>
                        @endif
                    </div>
                    <div class="approval-item">
                        <div class="mb-2">User Check</div>
                        @if ($ticket->approval->user_checked)
                            <span class="approval-status-badge status-approved">Checked</span>
                            <div class="small mt-1">
                                {{ $ticket->approval->user_checked_at ? $ticket->approval->user_checked_at->format('d M Y, H:i') : '' }}
                            </div>
                        @else
                            <span class="approval-status-badge status-pending">Pending</span>
                        @endif
                    </div>
                    <div class="approval-item">
                        <div class="mb-2">GM Approval</div>
                        @if ($ticket->approval->gm_approved)
                            <span class="approval-status-badge status-approved">Approved</span>
                            <div class="small mt-1">
                                {{ $ticket->approval->gm_approved_at ? $ticket->approval->gm_approved_at->format('d M Y, H:i') : '' }}
                            </div>
                        @else
                            <span class="approval-status-badge status-pending">Pending</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- SIGNATURES SECTION - With Toggle -->
        @if ($ticket->signatures->count() > 0)
            <div class="signatures-section collapsed">
                <div class="signatures-header">
                    <h5 class="signatures-title">
                        <i class="fas fa-signature"></i> SIGNATURES ({{ $ticket->signatures->count() }})
                    </h5>
                    <span class="signatures-toggle-arrow">
                        <i class="fas fa-chevron-down"></i>
                    </span>
                </div>

                <div class="signatures-container">
                    <div class="signature-grid">
                        @foreach ($ticket->signatures->sortBy('stage') as $signature)
                            @if ($user->role === 'user' && $signature->user_id != $user->id)
                                @continue
                            @endif
                            <div class="signature-item">
                                <div class="signature-label">
                                    @switch($signature->stage)
                                        @case(1)
                                            Requested by
                                        @break

                                        @case(2)
                                            Received by
                                        @break

                                        @case(3)
                                            OM Approved by
                                        @break

                                        @case(6)
                                            Completed by
                                        @break

                                        @case(7)
                                            User Check Done
                                        @break

                                        @case(8)
                                            GM Approved by
                                        @break

                                        @case(9)
                                            Closed by (Admin)
                                        @break

                                        @default
                                            Signed by
                                    @endswitch
                                </div>

                                @if ($signature->signature_path && Storage::exists('public/' . $signature->signature_path))
                                    <img src="{{ Storage::url($signature->signature_path) }}" alt="Signature"
                                        class="signature-image" data-src="{{ Storage::url($signature->signature_path) }}"
                                        style="cursor: pointer;">
                                @endif
                                <div class="signature-info">
                                    <strong>{{ $signature->user->name ?? 'Unknown' }}</strong>
                                    <div>{{ ucfirst($signature->user->role ?? 'N/A') }}</div>
                                    <div>{{ $signature->signed_at->format('d M Y, H:i') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- PURCHASE REQUESTS (PR) SECTION - Updated for photos only -->
        @if ($ticket->voucherRequests->count() > 0)
            <div class="ticket-body">
                <h5 class="section-title">
                    <i class="fas fa-file-invoice-dollar"></i> PURCHASE REQUESTS (PR)
                </h5>
                @foreach ($ticket->voucherRequests as $pr)
                    <div class="pr-card">
                        <div class="pr-card-header">
                            <div>
                                <strong>PR #{{ $pr->vr_number }}</strong>
                                <span
                                    class="badge bg-{{ $pr->status === 'paid' ? 'success' : ($pr->status === 'rejected' ? 'danger' : 'warning') }} ms-2">
                                    {{ ucfirst($pr->status) }}
                                </span>
                            </div>
                            <div>
                                <small>Created: {{ $pr->created_at->format('d M Y, H:i') }}</small>
                            </div>
                        </div>
                        <div class="pr-card-body">
                            <!-- Notes -->
                            @if ($pr->notes)
                                <div class="pr-notes">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    <strong>Notes:</strong> {{ $pr->notes }}
                                </div>
                            @endif

                            <!-- Photos (Voucher Attachments) -->
                            @if ($pr->attachments && $pr->attachments->count() > 0)
                                <h6 class="mt-3 mb-2"><i class="fas fa-camera me-2"></i>Photos
                                    ({{ $pr->attachments->count() }})
                                </h6>
                                <div class="pr-photos">
                                    @foreach ($pr->attachments as $photo)
                                        <div class="pr-photo-item">
                                            <img src="{{ Storage::url($photo->file_path) }}"
                                                alt="{{ $photo->file_name }}"
                                                data-src="{{ Storage::url($photo->file_path) }}">
                                            @if ($photo->description)
                                                <div class="photo-description">
                                                    {{ Str::limit($photo->description, 30) }}
                                                </div>
                                            @else
                                                <div class="photo-description">
                                                    {{ Str::limit($photo->file_name, 25) }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Approval Status Badges -->
                            <div class="row mt-3">
                                <div class="col-md-4 col-6 text-center mb-2">
                                    <small class="text-muted">Admin Engineering</small><br>
                                    @if ($pr->admin_approved)
                                        <span class="badge bg-success">Approved</span>
                                        <small
                                            class="d-block">{{ $pr->admin_approved_at ? $pr->admin_approved_at->format('d M Y') : '' }}</small>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </div>
                                <div class="col-md-4 col-6 text-center mb-2">
                                    <small class="text-muted">OM Approval</small><br>
                                    @if ($pr->om_approved)
                                        <span class="badge bg-success">Approved</span>
                                        <small
                                            class="d-block">{{ $pr->om_approved_at ? $pr->om_approved_at->format('d M Y') : '' }}</small>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </div>
                                <div class="col-md-4 col-6 text-center mb-2">
                                    <small class="text-muted">GM Approval</small><br>
                                    @if ($pr->gm_approved)
                                        <span class="badge bg-success">Approved</span>
                                        <small
                                            class="d-block">{{ $pr->gm_approved_at ? $pr->gm_approved_at->format('d M Y') : '' }}</small>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- COMMENTS SECTION -->
        <div class="comments-section {{ $showComments ? '' : 'collapsed' }}">
            <div class="comments-header">
                <h5 class="comments-title">
                    <i class="fas fa-comments"></i> COMMENTS ({{ $ticket->comments->count() }})
                </h5>
                <span class="comments-toggle-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span>
            </div>

            <div class="comments-container">
                @forelse($ticket->comments->sortByDesc('created_at') as $comment)
                    <div class="comment-item">
                        @if ($comment->is_followup)
                            <span class="follow-up-badge">FOLLOW-UP</span>
                        @endif
                        <div class="comment-header">
                            <div class="comment-avatar"
                                style="background-color: {{ $comment->user ? stringToColorPHP($comment->user->name) : '#6c757d' }}">
                                {{ $comment->user ? substr($comment->user->name, 0, 1) : '?' }}
                            </div>
                            <div class="comment-meta">
                                <div class="d-flex align-items-center">
                                    <span class="comment-author">
                                        {{ $comment->user->name ?? 'System' }}
                                    </span>
                                    @if ($comment->user)
                                        <span class="comment-role">{{ ucfirst($comment->user->role) }}</span>
                                    @endif
                                </div>
                                <div class="comment-time">
                                    {{ $comment->created_at->format('d M Y, H:i') }}
                                    ({{ $comment->created_at->diffForHumans() }})
                                </div>
                            </div>
                        </div>
                        <div class="comment-body">
                            {!! nl2br(e($comment->comment)) !!}

                            @if ($comment->attachments->count() > 0)
                                <div class="comment-attachments">
                                    <small class="text-muted d-block mb-1">Attachments:</small>
                                    @foreach ($comment->attachments as $attachment)
                                        @php
                                            $extension = strtolower(
                                                pathinfo($attachment->file_name, PATHINFO_EXTENSION),
                                            );
                                            $isImage = in_array($extension, [
                                                'jpg',
                                                'jpeg',
                                                'png',
                                                'gif',
                                                'bmp',
                                                'webp',
                                            ]);
                                        @endphp

                                        @if ($isImage)
                                            <div class="mb-2">
                                                <img src="{{ Storage::url($attachment->file_path) }}"
                                                    alt="{{ $attachment->file_name }}"
                                                    class="image-attachment comment-image"
                                                    style="max-width: 150px; max-height: 120px; cursor: pointer;"
                                                    data-src="{{ Storage::url($attachment->file_path) }}">
                                            </div>
                                        @else
                                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                                class="attachment-item small d-inline-block mb-1">
                                                <i class="fas fa-paperclip"></i> {{ $attachment->file_name }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-comment-slash fa-2x mb-3"></i>
                        <p>No comments yet</p>
                    </div>
                @endforelse

                <!-- Add Comment Form -->
                @if ($canComment && $showComments)
                    <div class="comment-form-section" id="commentForm">
                        <h6 class="mb-3"><i class="fas fa-comment-medical me-2"></i> ADD COMMENT</h6>
                        <form id="commentFormSubmit" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <textarea name="comment" id="commentText" class="comment-textarea" rows="4"
                                    placeholder="Write your comment here..." required></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Attach Files (Optional)</label>
                                    <input type="file" name="attachments[]" class="form-control" multiple
                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.webp">
                                    <small class="text-muted">Max 5MB per file. Max 5 files.</small>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100"
                                        style="background: var(--navy); border: none;">
                                        <i class="fas fa-paper-plane me-2"></i> Post Comment
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- ACTIVITY SECTION -->
        <div class="activity-section collapsed">
            <div class="activity-header">
                <h5 class="activity-title">
                    <i class="fas fa-history"></i> ACTIVITY TIMELINE
                </h5>
                <span class="toggle-arrow">
                    <i class="fas fa-chevron-down"></i>
                </span>
            </div>

            <div class="activity-timeline">
                @forelse($ticket->activities->sortByDesc('created_at') as $activity)
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-item-header">
                                <i class="fas fa-{{ getActivityIcon($activity->action) }} activity-icon"
                                    style="color: var(--navy);"></i>
                                <span class="activity-user">{{ $activity->user->name ?? 'System' }}</span>
                                <span class="activity-time">
                                    {{ $activity->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>
                            <div class="activity-description">
                                {{ $activity->description }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-3"></i>
                        <p>No activity yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    </div>

    <!-- ============================================
                                                                                                            MODALS
                                                                                                        ============================================ -->

    <!-- Add Follow-up Modal -->
    <div class="modal fade" id="addFollowupModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-pen me-2"></i> Add Follow-up Notes
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addFollowupForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Technician completed work without follow-up notes. Please add them here.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Follow-up Notes *</label>
                            <textarea name="follow_up_notes" class="form-control" rows="5"
                                placeholder="Describe what work was done, parts replaced, etc." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background: var(--navy); border: none;">
                            <i class="fas fa-save me-1"></i> Save Notes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- New Signature Modal -->
    <div class="modal fade" id="newSignatureModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2"></i> Create New Signature
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="newSignatureForm">
                    @csrf
                    <input type="hidden" name="action_type" id="actionType">
                    <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            You already have a saved signature. Creating a new one will replace the old signature.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Enter your password to confirm *</label>
                            <input type="password" name="current_password" class="form-control"
                                placeholder="Your account password" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-warning"
                                style="background: var(--orange); border: none;">
                                <i class="fas fa-pen me-1"></i> Proceed to Create New Signature
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Approve Modal -->
    <div class="modal fade" id="quickApproveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-bolt me-2"></i> Quick Approve
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="quickApproveForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You are about to approve this request using your saved signature.
                        </div>
                        <div class="text-center mb-3">
                            @if ($user->signature_path && Storage::disk('public')->exists($user->signature_path))
                                <img src="{{ Storage::url($user->signature_path) }}" alt="Your Signature"
                                    style="max-height: 80px; border: 1px solid #ddd; padding: 5px; background: transparent;">
                                <p class="small mt-2">Your saved signature will be used</p>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success"
                            style="background: var(--success); border: none;">Approve with Saved Signature</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Ticket Modal -->
    <div class="modal fade" id="deleteTicketModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-trash-alt me-2"></i> Delete Maintenance Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteTicketForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>WARNING:</strong> This action will permanently delete the request and all related
                            data. This cannot be undone!
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Enter your password to confirm *</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Your account password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"
                            style="background: var(--danger); border: none;">Delete Permanently</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Receive Modal -->
    <div class="modal fade" id="receiveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i> Receive Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="receiveForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You are about to receive this request. Please provide your signature.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Draw Your Signature *</label>
                            <div class="signature-canvas-container border rounded mb-3">
                                <canvas id="receiveSignatureCanvas" class="modal-signature-canvas" width="300"
                                    height="200" style="background: transparent; width: 100%; height: auto;"></canvas>
                            </div>
                            <div class="signature-actions">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="clearReceiveSignature()">
                                    <i class="fas fa-eraser me-1"></i> Clear
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="undoReceiveSignature()">
                                    <i class="fas fa-undo me-1"></i> Undo
                                </button>
                            </div>
                        </div>
                        @if ($canSaveSignature)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="save_signature"
                                    id="saveReceiveSignature" value="1" {{ !$hasSignature ? 'checked' : '' }}>
                                <label class="form-check-label" for="saveReceiveSignature">
                                    <i class="fas fa-save me-1"></i> Save this signature to my profile
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success"
                            style="background: var(--navy); border: none;">Receive Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- OM Approve Modal -->
    <div class="modal fade" id="omApproveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-thumbs-up me-2"></i> OM Approve Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="omApproveForm">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You are about to approve this request. Please provide your signature.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Draw Your Signature *</label>
                            <div class="signature-canvas-container border rounded mb-3">
                                <canvas id="omApproveSignatureCanvas" class="modal-signature-canvas" width="300"
                                    height="200" style="background: transparent; width: 100%; height: auto;"></canvas>
                            </div>
                            <div class="signature-actions">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="clearOmApproveSignature()">
                                    <i class="fas fa-eraser me-1"></i> Clear
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="undoOmApproveSignature()">
                                    <i class="fas fa-undo me-1"></i> Undo
                                </button>
                            </div>
                        </div>
                        @if ($canSaveSignature)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="save_signature"
                                    id="saveOmApproveSignature" value="1" {{ !$hasSignature ? 'checked' : '' }}>
                                <label class="form-check-label" for="saveOmApproveSignature">
                                    <i class="fas fa-save me-1"></i> Save this signature to my profile
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success"
                            style="background: var(--navy); border: none;">Approve Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- OM Reject Modal -->
    <div class="modal fade" id="omRejectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-thumbs-down me-2"></i> OM Reject Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="omRejectForm">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Are you sure you want to reject this request?
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason *</label>
                            <textarea name="rejection_reason" class="form-control" rows="3"
                                placeholder="Please provide reason for rejection..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"
                            style="background: var(--danger); border: none;">Reject Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Complete Modal -->
    <div class="modal fade" id="completeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-check-double me-2"></i>Mark Work as Complete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="completeForm" method="POST" action="{{ route('tickets.complete', $ticket->id) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="fas fa-info-circle me-2"></i>
                            Complete the work and optionally mark as follow-up.
                        </div>
                        <div class="mb-3">
                            <label for="completion_notes" class="form-label required">
                                <i class="fas fa-sticky-note me-1"></i>Completion Notes
                            </label>
                            <textarea name="completion_notes" id="completion_notes" class="comment-textarea" rows="4"
                                placeholder="Describe what work was done, parts replaced, etc." required></textarea>
                            <div class="form-text">
                                These notes will be visible in the report.
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_followup" id="isFollowup"
                                    value="1" checked>
                                <label class="form-check-label" for="isFollowup">
                                    <i class="fas fa-check-circle me-1" style="color: var(--orange);"></i>
                                    Mark as <strong>FOLLOW-UP</strong> (recommended)
                                </label>
                                <div class="form-text">
                                    If unchecked, Admin will need to add follow-up notes later.
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">
                                <i class="fas fa-signature me-1"></i>Signature
                            </label>
                            <div class="signature-canvas-container border rounded p-2 bg-light">
                                <canvas id="completeSignatureCanvas" width="300" height="200" class="w-100 border"
                                    style="background: transparent; width: 100%; height: auto;"></canvas>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="clearCompleteSignature()">
                                    <i class="fas fa-eraser me-1"></i>Clear
                                </button>
                            </div>
                            <input type="hidden" name="signature_data" id="completeSignatureData">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" style="background: var(--navy); border: none;">
                            <i class="fas fa-check-double me-1"></i>Submit Completion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PR Modal (Purchase Request) -->
    <div class="modal fade" id="prModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Request PR
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="prForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning" style="border-left-color: var(--orange);">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Requesting a PR will pause this request until the PR is approved.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason for PR *</label>
                            <textarea name="vr_reason" class="form-control" rows="3"
                                placeholder="Explain why you need a purchase request (parts needed, materials, etc.)" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estimated Cost (Optional)</label>
                            <input type="number" name="estimated_cost" class="form-control" placeholder="Rp"
                                min="0" step="1000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Required Items (Optional)</label>
                            <textarea name="required_items" class="form-control" rows="2"
                                placeholder="List items needed (e.g., 2x Light bulbs, 1x Switch)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning"
                            style="background: var(--orange); border: none;">Request PR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Check Accept Modal -->
    <div class="modal fade" id="userCheckAcceptModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-clipboard-check me-2"></i> Accept Completion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="userCheckAcceptForm">
                    @csrf
                    <input type="hidden" name="action" value="accept">
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Are you satisfied with the completed work?
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Your Signature *</label>
                            <div class="signature-canvas-container border rounded mb-3">
                                <canvas id="userAcceptSignatureCanvas" class="modal-signature-canvas" width="300"
                                    height="200" style="background: transparent; width: 100%; height: auto;"></canvas>
                            </div>
                            <div class="signature-actions">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="clearUserAcceptSignature()">
                                    <i class="fas fa-eraser me-1"></i> Clear
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="undoUserAcceptSignature()">
                                    <i class="fas fa-undo me-1"></i> Undo
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success"
                            style="background: var(--navy); border: none;">Accept Completion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Check Reject Modal -->
    <div class="modal fade" id="userCheckRejectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle me-2"></i> Reject Completion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="userCheckRejectForm">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Please explain why you are rejecting the completion.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason *</label>
                            <textarea name="rejection_reason" class="form-control" rows="3"
                                placeholder="Please explain what needs to be improved..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"
                            style="background: var(--danger); border: none;">Reject Completion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- GM Approve Modal -->
    <div class="modal fade" id="gmApproveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-gavel me-2"></i> GM Approve Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="gmApproveForm">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You are about to give final approval to this request. Please provide your signature.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Draw Your Signature *</label>
                            <div class="signature-canvas-container border rounded mb-3">
                                <canvas id="gmApproveSignatureCanvas" class="modal-signature-canvas" width="300"
                                    height="200" style="background: transparent; width: 100%; height: auto;"></canvas>
                            </div>
                            <div class="signature-actions">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="clearGmApproveSignature()">
                                    <i class="fas fa-eraser me-1"></i> Clear
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="undoGmApproveSignature()">
                                    <i class="fas fa-undo me-1"></i> Undo
                                </button>
                            </div>
                        </div>
                        @if ($canSaveSignature)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="save_signature"
                                    id="saveGmApproveSignature" value="1" {{ !$hasSignature ? 'checked' : '' }}>
                                <label class="form-check-label" for="saveGmApproveSignature">
                                    <i class="fas fa-save me-1"></i> Save this signature to my profile
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success"
                            style="background: var(--navy); border: none;">Approve Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- GM Reject Modal -->
    <div class="modal fade" id="gmRejectModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-ban me-2"></i> GM Reject Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="gmRejectForm">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Are you sure you want to reject this request?
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rejection Reason *</label>
                            <textarea name="rejection_reason" class="form-control" rows="3"
                                placeholder="Please provide reason for rejection..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"
                            style="background: var(--danger); border: none;">Reject Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i> Assign Technician
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="assignForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Technician *</label>
                            <select name="assigned_to" class="form-select" required>
                                <option value="">-- Select Technician --</option>
                                @foreach ($technicians as $technician)
                                    <option value="{{ $technician->id }}"
                                        {{ $ticket->assigned_to == $technician->id ? 'selected' : '' }}>
                                        {{ $technician->name }}
                                        @if ($technician->department)
                                            ({{ $technician->department->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Due Date (Optional)</label>
                            <input type="datetime-local" name="due_date" class="form-control"
                                value="{{ $ticket->due_date ? $ticket->due_date->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notify_user" id="notifyUser"
                                value="1" checked>
                            <label class="form-check-label" for="notifyUser">
                                <i class="fas fa-bell me-1"></i> Notify technician
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"
                            style="background: var(--navy); border: none;">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-ban me-2"></i> Cancel Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="cancelForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Are you sure you want to cancel this request? This action cannot be undone.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cancellation Reason *</label>
                            <textarea name="cancellation_reason" class="form-control" rows="3"
                                placeholder="Please provide reason for cancellation..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Go Back</button>
                        <button type="submit" class="btn btn-danger"
                            style="background: var(--danger); border: none;">Cancel Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Report Preview Modal -->
    <div class="modal fade" id="reportPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-pdf me-2"></i>Report Preview - {{ $ticket->ticket_number }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="report-preview-container">
                        <iframe id="reportPreviewFrame" src="{{ route('tickets.report.view', $ticket->id) }}"
                            style="width: 100%; height: 70vh; border: none;"></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                    <button type="button" class="btn btn-success" onclick="printReport()"
                        style="background: var(--navy); border: none;">
                        <i class="fas fa-print me-1"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Options Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--navy); color: white; border-bottom: none;">
                    <h5 class="modal-title">
                        <i class="fas fa-file-pdf me-2"></i> Pilih Jenis Report
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="report-option" onclick="generateReport('full')" data-bs-dismiss="modal"
                        style="display: flex; align-items: center; gap: 15px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 10px; cursor: pointer;">
                        <div
                            style="width: 40px; height: 40px; background: #ffebee; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-file-pdf" style="color: #dc3545; font-size: 20px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #333;">Full Report</div>
                            <div style="font-size: 12px; color: #666;">Report + Attachments + PR Photos</div>
                        </div>
                        <i class="fas fa-print" style="color: #999;"></i>
                    </div>

                    <div class="report-option" onclick="generateReport('main')" data-bs-dismiss="modal"
                        style="display: flex; align-items: center; gap: 15px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 10px; cursor: pointer;">
                        <div
                            style="width: 40px; height: 40px; background: #e6f2ff; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-file-alt" style="color: #0d6efd; font-size: 20px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #333;">Report Only</div>
                            <div style="font-size: 12px; color: #666;">Tanpa attachments & PR photos</div>
                        </div>
                        <i class="fas fa-print" style="color: #999;"></i>
                    </div>

                    @if ($imageAttachments && $imageAttachments->count() > 0)
                        <div class="report-option" onclick="generateReport('attachments')" data-bs-dismiss="modal"
                            style="display: flex; align-items: center; gap: 15px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 10px; cursor: pointer;">
                            <div
                                style="width: 40px; height: 40px; background: #e8f5e9; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-images" style="color: #198754; font-size: 20px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #333;">Attachments Only</div>
                                <div style="font-size: 12px; color: #666;">{{ $imageAttachments->count() }} foto</div>
                            </div>
                            <i class="fas fa-print" style="color: #999;"></i>
                        </div>
                    @endif

                    @if (isset($hasPRPhotos) && $hasPRPhotos)
                        <div class="report-option" onclick="generateReport('pr_photos')" data-bs-dismiss="modal"
                            style="display: flex; align-items: center; gap: 15px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 10px; cursor: pointer;">
                            <div
                                style="width: 40px; height: 40px; background: #fff3e0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-receipt" style="color: #ff9800; font-size: 20px;"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #333;">PR Photos Only</div>
                                <div style="font-size: 12px; color: #666;">Purchase Request photos</div>
                            </div>
                            <i class="fas fa-print" style="color: #999;"></i>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Ticket Modal (Admin only - saat status OPEN) -->
    @if (in_array($user->role, ['admin_eng', 'superadmin']) && $ticket->status === 'open')
        <div class="modal fade" id="editTicketModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header"
                        style="background: linear-gradient(135deg, #003366, #1e4a7a); color: white;">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2"></i> Edit Maintenance Request
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editTicketForm">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="alert alert-info mb-3" style="font-size: 13px;">
                                <i class="fas fa-info-circle me-2"></i>
                                You can only edit <strong>Priority</strong> and <strong>Category</strong> while the ticket
                                is still <strong>OPEN</strong>.
                            </div>

                            <!-- Category -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                                <select name="category_id" id="editCategoryId" class="form-select select2-edit"
                                    style="width: 100%;" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $ticket->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Priority -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Priority <span class="text-danger">*</span></label>
                                <select name="priority_id" id="editPriorityId" class="form-select select2-edit"
                                    style="width: 100%;" required>
                                    <option value="">-- Select Priority --</option>
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}"
                                            {{ $ticket->priority_id == $priority->id ? 'selected' : '' }}>
                                            {{ $priority->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="alert alert-warning mt-3" style="font-size: 12px;">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Changing priority may affect the ticket's processing order.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn" style="background: #ff6600; color: white;">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        // Global variables
        let receiveSignaturePad = null;
        let omApproveSignaturePad = null;
        let completeSignaturePad = null;
        let userAcceptSignaturePad = null;
        let gmApproveSignaturePad = null;
        let pendingSignatureAction = null;

        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        $(document).ready(function() {
            // Signature Pad Initializations
            $('#receiveModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('receiveSignatureCanvas');
                if (canvas) {
                    canvas.width = 300;
                    canvas.height = 200;
                    receiveSignaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(0,0,0,0)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5
                    });
                }
            });

            $('#omApproveModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('omApproveSignatureCanvas');
                if (canvas) {
                    canvas.width = 300;
                    canvas.height = 200;
                    omApproveSignaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(0,0,0,0)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5
                    });
                }
            });

            $('#completeModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('completeSignatureCanvas');
                if (canvas) {
                    canvas.width = 300;
                    canvas.height = 200;
                    completeSignaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(0,0,0,0)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5
                    });
                }
            });

            $('#userCheckAcceptModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('userAcceptSignatureCanvas');
                if (canvas) {
                    canvas.width = 300;
                    canvas.height = 200;
                    userAcceptSignaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(0,0,0,0)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5
                    });
                }
            });

            $('#gmApproveModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('gmApproveSignatureCanvas');
                if (canvas) {
                    canvas.width = 300;
                    canvas.height = 200;
                    gmApproveSignaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgba(0,0,0,0)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5
                    });
                }
            });

            // Form Submissions
            $('#receiveForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                if (!receiveSignaturePad || receiveSignaturePad.isEmpty()) {
                    toastr.error('Please draw your signature');
                    return;
                }
                formData.append('signature_data', receiveSignaturePad.toDataURL());
                submitForm('{{ route('tickets.receive', $ticket->id) }}', formData, '#receiveModal');
            });

            $('#omApproveForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                if (!omApproveSignaturePad || omApproveSignaturePad.isEmpty()) {
                    toastr.error('Please draw your signature');
                    return;
                }
                formData.append('signature_data', omApproveSignaturePad.toDataURL());
                submitForm('{{ route('tickets.om-action', $ticket->id) }}', formData, '#omApproveModal');
            });

            $('#omRejectForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.om-action', $ticket->id) }}', formData, '#omRejectModal');
            });

            $('#completeForm').submit(function(e) {
                e.preventDefault();
                const notes = $('#completion_notes').val().trim();
                if (!notes) {
                    toastr.error('Please fill in completion notes');
                    $('#completion_notes').focus();
                    return;
                }
                if (!completeSignaturePad || completeSignaturePad.isEmpty()) {
                    toastr.error('Please provide your signature');
                    return;
                }
                const formData = new FormData(this);
                formData.append('signature_data', completeSignaturePad.toDataURL());
                submitForm('{{ route('tickets.complete', $ticket->id) }}', formData, '#completeModal');
            });

            $('#prForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.request-vr', $ticket->id) }}', formData, '#prModal');
            });

            $('#userCheckAcceptForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                if (!userAcceptSignaturePad || userAcceptSignaturePad.isEmpty()) {
                    toastr.error('Please draw your signature');
                    return;
                }
                formData.append('signature_data', userAcceptSignaturePad.toDataURL());
                submitForm('{{ route('tickets.user-check', $ticket->id) }}', formData,
                    '#userCheckAcceptModal');
            });

            $('#userCheckRejectForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.user-check', $ticket->id) }}', formData,
                    '#userCheckRejectModal');
            });

            $('#gmApproveForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                if (!gmApproveSignaturePad || gmApproveSignaturePad.isEmpty()) {
                    toastr.error('Please draw your signature');
                    return;
                }
                formData.append('signature_data', gmApproveSignaturePad.toDataURL());
                submitForm('{{ route('tickets.gm-action', $ticket->id) }}', formData, '#gmApproveModal');
            });

            $('#gmRejectForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.gm-action', $ticket->id) }}', formData, '#gmRejectModal');
            });

            $('#assignForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.assign', $ticket->id) }}', formData, '#assignModal');
            });

            $('#cancelForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.cancel', $ticket->id) }}', formData, '#cancelModal');
            });

            $('#commentFormSubmit').on('submit', function(e) {
                e.preventDefault();
                const commentContent = $('#commentText').val().trim();
                if (!commentContent) {
                    toastr.error('Please enter a comment');
                    return;
                }
                const formData = new FormData(this);
                formData.set('comment', commentContent);
                submitForm('{{ route('tickets.add-comment', $ticket->id) }}', formData, null, true);
            });

            $('#quickApproveForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.quick-approve', $ticket->id) }}', formData,
                    '#quickApproveModal');
            });

            $('#addFollowupForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.add-followup', $ticket->id) }}', formData,
                    '#addFollowupModal');
            });

            $('#newSignatureForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action_type', pendingSignatureAction);
                $.ajax({
                    url: '{{ route('tickets.verify-password') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#newSignatureModal').modal('hide');
                            switch (pendingSignatureAction) {
                                case 'receive':
                                    $('#receiveModal').modal('show');
                                    break;
                                case 'om_approve':
                                    $('#omApproveModal').modal('show');
                                    break;
                                case 'gm_approve':
                                    $('#gmApproveModal').modal('show');
                                    break;
                            }
                        } else {
                            toastr.error(response.message || 'Password verification failed');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message ||
                            'Password verification failed');
                    }
                });
            });

            // Image viewer
            $(document).on('click', '.gallery-item img, .pr-photo-item img, .comment-image', function(e) {
                e.preventDefault();
                e.stopPropagation();
                let imageSrc = $(this).data('src') || $(this).attr('src');
                if (!imageSrc) return;
                const modalHtml = `
                    <div class="image-modal-backdrop" id="imageModal">
                        <div class="image-modal-content">
                            <div class="image-modal-close" onclick="closeImageModal()">
                                <i class="fas fa-times"></i>
                            </div>
                            <img src="${imageSrc}" alt="Preview">
                        </div>
                    </div>
                `;
                $('body').append(modalHtml);
                $('#imageModal').on('click', function(e) {
                    if (e.target === this) closeImageModal();
                });
                $(document).on('keydown', function(e) {
                    if (e.key === 'Escape') closeImageModal();
                });
            });

            // Toggle sections
            $('.activity-header').on('click', function() {
                const $section = $(this).closest('.activity-section');
                $section.toggleClass('collapsed');
            });
            $('.comments-header').on('click', function() {
                const $section = $(this).closest('.comments-section');
                $section.toggleClass('collapsed');
            });
        });

        function submitForm(url, formData, modalId = null, reload = true) {
            const submitBtn = modalId ? $(modalId + ' form button[type="submit"]') : $(
                '#commentFormSubmit button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        if (modalId) $(modalId).modal('hide');
                        if (reload) setTimeout(() => location.reload(), 1000);
                    } else {
                        toastr.error(response.message || 'Operation failed');
                    }
                },
                error: function(xhr) {
                    let message = 'An error occurred';
                    if (xhr.responseJSON?.message) message = xhr.responseJSON.message;
                    toastr.error(message);
                },
                complete: function() {
                    setTimeout(() => submitBtn.prop('disabled', false).html(originalText), 1000);
                }
            });
        }
        // Toggle Signatures Section
        $(document).ready(function() {
            // Signature toggle
            $('.signatures-header').on('click', function() {
                const $section = $(this).closest('.signatures-section');
                $section.toggleClass('collapsed');

                const $container = $section.find('.signatures-container');
                if ($section.hasClass('collapsed')) {
                    $container.css('max-height', '0');
                } else {
                    $container.css('max-height', $container[0].scrollHeight + 'px');
                }
            });

            // Initialize collapsed state for signatures on mobile
            function checkSignatureToggle() {
                const $signaturesSection = $('.signatures-section');
                const $signaturesContainer = $signaturesSection.find('.signatures-container');

                if ($(window).width() <= 768) {
                    if (!$signaturesSection.hasClass('collapsed')) {
                        $signaturesSection.addClass('collapsed');
                        $signaturesContainer.css('max-height', '0');
                    }
                } else {
                    // Desktop bisa tetap collapsed atau expanded, terserah user
                    // Tidak otomatis berubah
                }
            }

            checkSignatureToggle();
            $(window).on('resize', checkSignatureToggle);

            // Click signature image to enlarge
            $(document).on('click', '.signature-image', function(e) {
                e.preventDefault();
                e.stopPropagation();
                let imageSrc = $(this).data('src') || $(this).attr('src');
                if (!imageSrc) return;

                const modalHtml = `
            <div class="image-modal-backdrop" id="signatureImageModal">
                <div class="image-modal-content">
                    <div class="image-modal-close" onclick="closeSignatureImageModal()">
                        <i class="fas fa-times"></i>
                    </div>
                    <img src="${imageSrc}" alt="Signature Preview">
                </div>
            </div>
        `;
                $('body').append(modalHtml);

                $('#signatureImageModal').on('click', function(e) {
                    if (e.target === this) closeSignatureImageModal();
                });
                $(document).on('keydown', function(e) {
                    if (e.key === 'Escape') closeSignatureImageModal();
                });
            });
        });

        function closeSignatureImageModal() {
            $('#signatureImageModal').remove();
            $(document).off('keydown');
        }
        // Signature functions
        window.clearReceiveSignature = () => receiveSignaturePad?.clear();
        window.undoReceiveSignature = () => {
            if (receiveSignaturePad) {
                const data = receiveSignaturePad.toData();
                if (data.length) data.pop();
                receiveSignaturePad.fromData(data);
            }
        };
        window.clearOmApproveSignature = () => omApproveSignaturePad?.clear();
        window.undoOmApproveSignature = () => {
            if (omApproveSignaturePad) {
                const data = omApproveSignaturePad.toData();
                if (data.length) data.pop();
                omApproveSignaturePad.fromData(data);
            }
        };
        window.clearCompleteSignature = () => completeSignaturePad?.clear();
        window.clearUserAcceptSignature = () => userAcceptSignaturePad?.clear();
        window.undoUserAcceptSignature = () => {
            if (userAcceptSignaturePad) {
                const data = userAcceptSignaturePad.toData();
                if (data.length) data.pop();
                userAcceptSignaturePad.fromData(data);
            }
        };
        window.clearGmApproveSignature = () => gmApproveSignaturePad?.clear();
        window.undoGmApproveSignature = () => {
            if (gmApproveSignaturePad) {
                const data = gmApproveSignaturePad.toData();
                if (data.length) data.pop();
                gmApproveSignaturePad.fromData(data);
            }
        };

        // Modal open functions
        function quickApprove() {
            Swal.fire({
                title: 'Quick Approve?',
                text: "Use your saved signature to approve this request",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) $('#quickApproveModal').modal('show');
            });
        }

        function deleteTicket() {
            $('#deleteTicketModal').modal('show');
        }

        function openAddFollowupModal() {
            $('#addFollowupModal').modal('show');
        }

        function openReceiveModal() {
            @if ($hasSignature && $canSaveSignature)
                Swal.fire({
                    title: 'How do you want to sign?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Use Saved Signature',
                    denyButtonText: 'Create New Signature',
                    cancelButtonText: 'Cancel',
                    icon: 'question'
                }).then((result) => {
                    if (result.isConfirmed) quickApprove();
                    else if (result.isDenied) {
                        pendingSignatureAction = 'receive';
                        $('#newSignatureModal').modal('show');
                    }
                });
            @else
                $('#receiveModal').modal('show');
            @endif
        }

        function openOmApproveModal() {
            @if ($hasSignature && $canSaveSignature)
                Swal.fire({
                    title: 'How do you want to sign?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Use Saved Signature',
                    denyButtonText: 'Create New Signature',
                    cancelButtonText: 'Cancel',
                    icon: 'question'
                }).then((result) => {
                    if (result.isConfirmed) quickApprove();
                    else if (result.isDenied) {
                        pendingSignatureAction = 'om_approve';
                        $('#newSignatureModal').modal('show');
                    }
                });
            @else
                $('#omApproveModal').modal('show');
            @endif
        }

        function openGmApproveModal() {
            @if ($hasSignature && $canSaveSignature)
                Swal.fire({
                    title: 'How do you want to sign?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Use Saved Signature',
                    denyButtonText: 'Create New Signature',
                    cancelButtonText: 'Cancel',
                    icon: 'question'
                }).then((result) => {
                    if (result.isConfirmed) quickApprove();
                    else if (result.isDenied) {
                        pendingSignatureAction = 'gm_approve';
                        $('#newSignatureModal').modal('show');
                    }
                });
            @else
                $('#gmApproveModal').modal('show');
            @endif
        }

        function openOmRejectModal() {
            $('#omRejectModal').modal('show');
        }

        function openCompleteModal() {
            $('#completeModal').modal('show');
        }

        function openPRModal() {
            $('#prModal').modal('show');
        }

        function openUserCheckAcceptModal() {
            $('#userCheckAcceptModal').modal('show');
        }

        function openUserCheckRejectModal() {
            $('#userCheckRejectModal').modal('show');
        }

        function openGmRejectModal() {
            $('#gmRejectModal').modal('show');
        }

        function openAssignModal() {
            $('#assignModal').modal('show');
        }

        function openCancelModal() {
            $('#cancelModal').modal('show');
        }

        function closeImageModal() {
            $('#imageModal').remove();
            $(document).off('keydown');
        }

        function closeTicketAdmin() {
            Swal.fire({
                title: 'Close Request Administratively?',
                text: "This will mark the request as administratively closed after GM approval.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#343a40',
                confirmButtonText: 'Yes, close it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('tickets.close-admin', $ticket->id) }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1500);
                            } else toastr.error(response.message);
                        },
                        error: function(xhr) {
                            toastr.error('Failed to close request');
                        }
                    });
                }
            });
        }

        function continueToOM() {
            Swal.fire({
                title: 'Continue to OM?',
                text: "Send this request to Operation Manager for approval",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Yes, continue'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('tickets.continue-to-om', $ticket->id) }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1000);
                            } else toastr.error(response.message);
                        },
                        error: function(xhr) {
                            toastr.error('Failed to continue to OM');
                        }
                    });
                }
            });
        }

        function openReportModal() {
            $('#reportModal').modal('show');
        }

        function generateReport(type) {
            $('#reportModal').modal('hide');

            // Check ukuran layar
            const isSmallScreen = window.innerWidth <= 768;

            if (isSmallScreen) {
                // Layar kecil: buka di tab/window baru (modal muncul via CSS)
                const url = '{{ route('tickets.report.view', $ticket->id) }}?type=' + type;
                window.open(url, '_blank');
            } else {
                // Desktop: buka tab baru dengan auto-print
                const url = '{{ route('tickets.report.view', $ticket->id) }}?type=' + type + '&print=1';
                window.open(url, '_blank');
            }
        }

        function viewReportModal(type) {
            $('#reportModal').modal('hide');

            let url = '';
            switch (type) {
                case 'full':
                    url = '{{ route('tickets.report.view', $ticket->id) }}?type=full';
                    break;
                case 'main':
                    url = '{{ route('tickets.report.view', $ticket->id) }}?type=main';
                    break;
                case 'attachments':
                    url = '{{ route('tickets.report.view', $ticket->id) }}?type=attachments';
                    break;
                default:
                    url = '{{ route('tickets.report.view', $ticket->id) }}?type=full';
                    break;
            }

            window.open(url, '_blank');
        }

        function printReport() {
            window.print();
        } // ==================== EDIT TICKET MODAL ====================
        @if (in_array($user->role, ['admin_eng', 'superadmin']) && $ticket->status === 'open')
            // Initialize Select2 for modal
            function initEditSelect2() {
                $('#editCategoryId, #editPriorityId').select2({
                    width: '100%',
                    placeholder: 'Select an option',
                    allowClear: true,
                    dropdownParent: $('#editTicketModal')
                });
            }

            function openEditTicketModal() {
                // Refresh Select2 options (in case categories/priorities changed)
                initEditSelect2();
                $('#editTicketModal').modal('show');
            }

            // Submit Edit Form
            $('#editTicketForm').on('submit', function(e) {
                e.preventDefault();

                const categoryId = $('#editCategoryId').val();
                const priorityId = $('#editPriorityId').val();

                if (!categoryId) {
                    toastr.error('Please select a category');
                    return;
                }
                if (!priorityId) {
                    toastr.error('Please select a priority');
                    return;
                }

                const $btn = $(this).find('button[type="submit"]');
                const originalText = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...').prop('disabled', true);

                $.ajax({
                    url: '{{ route('tickets.update-detail', $ticket->id) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        category_id: categoryId,
                        priority_id: priorityId
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Ticket updated successfully');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            toastr.error(response.message || 'Failed to update ticket');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Failed to update ticket';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        toastr.error(message);
                    },
                    complete: function() {
                        $btn.html(originalText).prop('disabled', false);
                    }
                });
            });
        @endif
    </script>
@endpush

@php
    function getActivityIcon($action)
    {
        $icons = [
            'created' => 'plus-circle',
            'received' => 'check-circle',
            'assigned' => 'user-plus',
            'om_approved' => 'thumbs-up',
            'om_rejected' => 'thumbs-down',
            'completed' => 'check-double',
            'vr_requested' => 'file-invoice-dollar',
            'accepted' => 'clipboard-check',
            'rejected' => 'times-circle',
            'gm_approved' => 'gavel',
            'gm_rejected' => 'ban',
            'commented' => 'comment',
            'cancelled' => 'ban',
            'admin_closed' => 'lock',
        ];
        return $icons[$action] ?? 'info-circle';
    }
@endphp
