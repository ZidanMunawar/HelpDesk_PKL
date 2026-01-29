@extends('layouts.main')

@section('title', 'Ticket #' . $ticket->ticket_number . ' | ' . config('app.name'))

@section('page-title', 'Ticket Detail')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Tickets', 'url' => route('tickets.index')],
            ['title' => 'Ticket #' . $ticket->ticket_number, 'url' => 'javascript:void(0)'],
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
    {{-- <!--boostrap-->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}"> --}}
    <style>
        :root {
            --primary-color: #ff6200;
            --secondary-color: #ff7b00;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
        }

        /* ============================================
                                                                                       TICKET HEADER
                                                                                    ============================================ */
        .ticket-header-container {
            background: white;
            border: 2px solid var(--primary-color);
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .header-title {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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
            border-left: 4px solid var(--primary-color);
        }

        .info-item i {
            color: var(--primary-color);
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

        /* ============================================
                                                                                       STATUS & PRIORITY BADGES
                                                                                    ============================================ */
        .status-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-open {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }

        .status-received {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }

        .status-pending_om {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-in_progress {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .status-pending_vr {
            background: #fff8e1;
            color: #ff8f00;
            border: 1px solid #ffe082;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-pending_gm {
            background: #e8f4fd;
            color: #0d47a1;
            border: 1px solid #bbdefb;
        }

        .status-closed {
            background: #f8f9fa;
            color: #495057;
            border: 1px solid #e9ecef;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-ready_for_closure {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
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
                                                                                       CURRENT STAGE INFO
                                                                                    ============================================ */
        .stage-info {
            background: linear-gradient(135deg, #ff6200, #ff7b00);
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
                                                                                       ACTION BUTTONS
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
            transition: all 0.3s ease;
            border: none;
            color: white;
            min-width: 160px;
            cursor: pointer;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-action:active {
            transform: scale(0.95);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #e65500, #ff6b00);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #218838, #1ba87e);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: #212529 !important;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #e0a800, #e56b00);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #bd2130, #a71e2a);
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #138496, #117a8b);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268, #343a40);
        }

        .btn-dark {
            background: linear-gradient(135deg, #343a40, #212529);
        }

        .btn-dark:hover {
            background: linear-gradient(135deg, #23272b, #121416);
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
                                                                                       TICKET BODY - DENGAN JUDUL TICKET
                                                                                    ============================================ */
        .ticket-body {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Judul Ticket baru */
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
            color: var(--primary-color);
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
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .description-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border-left: 4px solid var(--primary-color);
            margin-bottom: 25px;
            line-height: 1.6;
            font-size: 14px;
            min-height: 100px;
        }

        /* ============================================
                                                                                       ATTACHMENTS
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
            border-color: var(--primary-color);
            color: var(--primary-color);
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            border-color: var(--primary-color);
        }

        /* ============================================
                                                                                       SIGNATURES SECTION
                                                                                    ============================================ */
        .signatures-section {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .signature-item {
            text-align: center;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #f9f9f9;
            transition: all 0.3s;
            flex-shrink: 0;
        }

        .signature-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            width: 100%;
            max-height: 100px;
            object-fit: contain;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: white;
            padding: 10px;
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

        /* ============================================
                                                                                       APPROVAL STATUS
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .approval-item {
            text-align: center;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            flex-shrink: 0;
        }

        .approval-status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        /* ============================================
                                                                                       DUE DATE WIDGET
                                                                                    ============================================ */
        .due-date-widget {
            background: linear-gradient(135deg, #ff6200, #ff7b00);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .due-date-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .due-date-icon {
            font-size: 24px;
        }

        .due-date-text h6 {
            margin: 0;
            font-weight: 600;
        }

        .due-date-text p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .due-date-value {
            font-size: 16px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
        }

        .due-date-overdue {
            background: linear-gradient(135deg, #dc3545, #c82333) !important;
        }

        /* ============================================
                                                                                       COMMENTS SECTION - DENGAN TOGGLE
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
            border-bottom: 2px solid var(--primary-color);
        }

        .comments-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 18px;
            color: var(--primary-color);
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
        }

        .comment-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
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
            background: var(--primary-color);
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

        /* ============================================
                                                                                       COMMENT FORM (akan disembunyikan jika status tertentu)
                                                                                    ============================================ */
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

        /* ============================================
                                                                                       ACTIVITY SECTION - WITH TOGGLE
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
            border-bottom: 2px solid var(--primary-color);
        }

        .activity-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 18px;
            color: var(--primary-color);
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
            background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
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
            background: var(--primary-color);
            border: 3px solid #f8f9fa;
            box-shadow: 0 0 0 2px var(--primary-color);
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
            border-bottom: 10px transparent;
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
                                                                                       VOUCHER INFO
                                                                                    ============================================ */
        .voucher-alert {
            background: #fff8e1;
            border: 1px solid #ffe082;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        /* ============================================
                                                                                       MODAL STYLES
                                                                                    ============================================ */
        .modal-signature-canvas {
            border: 2px dashed #ddd;
            border-radius: 6px;
            cursor: crosshair;
            background: white;
            width: 100%;
            height: 200px;
        }

        .signature-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        /* ============================================
                                                                                       MOBILE RESPONSIVE IMPROVEMENTS
                                                                                    ============================================ */

        /* DESKTOP: Grid normal */
        @media (min-width: 993px) {
            .info-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }

        /* TABLET: 2 kolom */
        @media (max-width: 992px) {

            /* Ticket Header - 2 kolom */
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

            /* Judul Ticket */
            .ticket-title {
                font-size: 20px;
            }

            .ticket-subtitle {
                font-size: 13px;
            }

            /* Stage Progress */
            .stage-info {
                padding: 12px;
            }

            .stage-name {
                font-size: 15px;
            }
        }

        /* MOBILE: Portrait & Landscape tetap 2 kolom */
        @media (max-width: 768px) {

            /* Sidebar - Quick Actions */
            .sidebar-quick-actions {
                display: none !important;
            }

            /* Action Buttons Bar */
            .action-buttons {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                padding: 12px;
                gap: 8px;
                margin-bottom: 15px;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                white-space: nowrap;
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

            /* Ticket Header - Tetap 2 kolom di mobile */
            .ticket-header-container {
                margin-bottom: 15px;
                border-radius: 6px;
            }

            .header-title {
                padding: 12px 15px;
                font-size: 16px;
            }

            .header-info {
                padding: 15px;
            }

            .info-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            /* Optimasi untuk mobile - lebih compact */
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
                color: #666;
                min-width: auto;
                width: 100%;
                font-weight: 600;
            }

            .info-value {
                font-size: 12px;
                width: 100%;
                line-height: 1.3;
                word-break: break-word;
            }

            /* Judul Ticket untuk mobile */
            .ticket-title-section {
                margin-bottom: 20px;
                padding-bottom: 12px;
            }

            .ticket-title {
                font-size: 18px;
            }

            .ticket-subtitle {
                font-size: 12px;
            }

            .ticket-title-label {
                font-size: 12px;
            }

            /* Khusus untuk teks panjang */
            .info-item:nth-child(1) .info-value,
            .info-item:nth-child(2) .info-value {
                font-size: 11px;
            }

            /* Untuk ticket number berikan highlight */
            .info-item:nth-child(3) .info-value {
                font-weight: 700;
                color: var(--primary-color);
                font-size: 13px;
            }

            /* Stage Progress - Mobile Friendly */
            .stage-info {
                padding: 12px;
            }

            .stage-name {
                font-size: 14px;
                line-height: 1.4;
            }

            .stage-progress {
                gap: 3px;
            }

            .stage-dot {
                width: 10px;
                height: 10px;
            }

            /* Signature Section - Horizontal Layout */
            .signature-grid {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                gap: 12px;
                padding-bottom: 10px;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                margin-top: 15px;
            }

            .signature-grid::-webkit-scrollbar {
                display: none;
            }

            .signature-item {
                min-width: 180px;
                flex-shrink: 0;
                padding: 15px;
            }

            .signature-label {
                font-size: 12px;
            }

            .signature-image {
                max-height: 70px;
            }

            .signature-info {
                font-size: 11px;
            }

            .signature-info strong {
                font-size: 12px;
            }

            /* Approval Status - Horizontal Layout */
            .approval-grid {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                gap: 10px;
                padding-bottom: 10px;
                -webkit-overflow-scrolling: touch;
                margin-top: 15px;
            }

            .approval-grid::-webkit-scrollbar {
                display: none;
            }

            .approval-item {
                min-width: 140px;
                flex-shrink: 0;
                padding: 12px;
            }

            /* Due Date Widget */
            .due-date-widget {
                flex-direction: column;
                text-align: center;
                padding: 15px;
                gap: 10px;
            }

            .due-date-content {
                flex-direction: column;
                gap: 10px;
                margin-bottom: 10px;
            }

            .due-date-value {
                width: 100%;
                text-align: center;
                padding: 8px 15px;
                font-size: 14px;
            }

            /* Ticket Body */
            .ticket-body {
                padding: 15px;
            }

            .section-title {
                font-size: 16px;
            }

            .description-box {
                padding: 15px;
                font-size: 14px;
            }

            /* Comments Section */
            .comments-section {
                padding: 15px;
            }

            .comments-header {
                padding: 0 0 10px 0;
            }

            .comments-title {
                font-size: 16px;
            }

            .comments-toggle-arrow {
                font-size: 14px;
            }

            .comment-item {
                padding: 15px 0;
            }

            .comment-header {
                flex-direction: row;
                align-items: center;
            }

            .comment-avatar {
                margin-bottom: 0;
                width: 35px;
                height: 35px;
                font-size: 14px;
                margin-right: 10px;
            }

            .comment-body {
                padding-left: 45px;
                font-size: 13px;
            }

            /* Comment Form */
            .comment-form-section {
                padding: 15px;
            }

            .comment-form-section .row {
                flex-direction: column;
            }

            .comment-form-section .col-md-6 {
                width: 100%;
                margin-bottom: 15px;
            }

            /* Activity Section */
            .activity-section {
                padding: 15px;
            }

            .activity-header {
                padding: 0 0 10px 0;
            }

            .activity-title {
                font-size: 16px;
            }

            .toggle-arrow {
                font-size: 14px;
            }

            .activity-timeline {
                padding-left: 20px;
            }

            .activity-item::before {
                left: -15px;
                width: 10px;
                height: 10px;
            }

            .activity-content {
                padding: 12px;
            }

            .activity-content::before,
            .activity-content::after {
                display: none;
            }

            /* Attachments */
            .attachment-list {
                gap: 10px;
                flex-wrap: wrap;
            }

            .image-attachment {
                max-width: 120px;
                max-height: 100px;
            }

            /* Modal Adjustments */
            .modal-dialog {
                margin: 10px;
            }

            .modal-header {
                padding: 12px 15px;
            }

            .modal-body {
                padding: 15px;
            }

            .modal-footer {
                padding: 12px 15px;
            }

            /* Voucher Alert */
            .voucher-alert {
                padding: 12px;
            }

            .voucher-alert i {
                font-size: 24px;
            }

            /* Quick Info in Sidebar */
            .card .small {
                font-size: 12px;
            }
        }

        /* Mobile kecil (≤ 576px): tetap 2 kolom tapi lebih compact */
        @media (max-width: 576px) {
            .header-title {
                font-size: 15px;
                padding: 10px 12px;
            }

            .header-info {
                padding: 12px;
            }

            .info-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .info-item {
                padding: 8px;
                min-height: 65px;
                gap: 5px;
            }

            .info-item i {
                font-size: 14px;
                width: 18px;
            }

            .info-label {
                font-size: 10px;
            }

            .info-value {
                font-size: 11px;
                line-height: 1.3;
            }

            /* Judul Ticket untuk mobile kecil */
            .ticket-title {
                font-size: 16px;
            }

            .ticket-subtitle {
                font-size: 11px;
            }

            .ticket-title-label {
                font-size: 11px;
            }

            .info-item:nth-child(1) .info-value,
            .info-item:nth-child(2) .info-value {
                font-size: 10px;
            }

            .info-item:nth-child(3) .info-value {
                font-size: 12px;
            }

            /* Action buttons lebih kecil */
            .action-buttons {
                padding: 10px;
                gap: 6px;
            }

            .btn-action {
                padding: 8px 12px;
                font-size: 12px;
                min-width: 65px;
            }

            .btn-action i {
                font-size: 13px;
                margin-right: 4px;
            }

            .btn-action span {
                font-size: 11px;
            }

            /* Status badges lebih kecil */
            .status-badge,
            .priority-badge {
                font-size: 9px;
                padding: 3px 6px;
            }
        }

        /* Mobile sangat kecil (≤ 400px) */
        @media (max-width: 400px) {
            .info-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 6px;
            }

            .info-item {
                padding: 6px;
                min-height: 60px;
            }

            .info-label {
                font-size: 9px;
            }

            .info-value {
                font-size: 10px;
            }

            .info-item:nth-child(3) .info-value {
                font-size: 11px;
            }

            .header-title {
                font-size: 14px;
                padding: 8px 10px;
            }

            /* Judul Ticket untuk mobile sangat kecil */
            .ticket-title {
                font-size: 15px;
            }

            .ticket-subtitle {
                font-size: 10px;
            }

            .btn-action {
                padding: 6px 10px;
                min-width: 60px;
                font-size: 11px;
            }

            .btn-action span {
                font-size: 10px;
            }

            .btn-action i {
                font-size: 12px;
                margin-right: 3px;
            }
        }

        /* Landscape Mode untuk Mobile */
        @media (max-width: 768px) and (orientation: landscape) {

            /* Di landscape, buat lebih rapat */
            .info-grid {
                gap: 8px;
            }

            .info-item {
                min-height: 60px;
                padding: 8px;
            }

            .action-buttons {
                flex-wrap: wrap;
                overflow-x: visible;
            }

            .btn-action {
                min-width: 90px;
            }

            /* Activity timeline lebih pendek di landscape */
            .activity-timeline {
                max-height: 150px;
                overflow-y: auto;
            }

            /* Comments container lebih pendek di landscape */
            .comments-container {
                max-height: 150px;
                overflow-y: auto;
            }
        }

        /* Fix untuk badge agar tidak kepotong */
        .status-badge,
        .priority-badge {
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: middle;
        }

        /* ============================================
                                                                                       PRINT STYLES
                                                                                    ============================================ */
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

            .btn-action {
                display: none !important;
            }

            /* Pastikan semua section terbuka saat print */
            .activity-section.collapsed .activity-timeline,
            .comments-section.collapsed .comments-container {
                max-height: none !important;
                display: block !important;
            }

            .toggle-arrow,
            .comments-toggle-arrow {
                display: none !important;
            }
        }

        /* ============================================
                                                                                       UTILITY CLASSES
                                                                                    ============================================ */
        .text-orange {
            color: var(--primary-color) !important;
        }

        .bg-orange {
            background-color: var(--primary-color) !important;
        }

        .border-orange {
            border-color: var(--primary-color) !important;
        }

        /* Image Modal Fix */
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

        /* Quick Action Button */
        .btn-quick-approve {
            background: linear-gradient(135deg, #20c997, #28a745);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-quick-approve:hover {
            background: linear-gradient(135deg, #1ba87e, #218838);
            transform: translateY(-2px);
        }

        /* Better scrolling for mobile */
        body {
            -webkit-overflow-scrolling: touch;
        }

        /* Fix untuk iOS Safari */
        @supports (-webkit-touch-callout) {

            .action-buttons,
            .signature-grid,
            .approval-grid {
                -webkit-overflow-scrolling: touch;
            }
        }

        /* Animation for mobile buttons */
        .btn-action.active {
            transform: scale(0.95);
            opacity: 0.8;
        }

        /* Responsive adjustments for Toggle buttons */
        .comments-collapse-btn,
        .activity-collapse-btn {
            background: none;
            border: none;
            padding: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .comments-collapse-btn:hover,
        .activity-collapse-btn:hover {
            opacity: 0.8;
        }

        .comments-collapse-btn:focus,
        .activity-collapse-btn:focus {
            outline: none;
        }
    </style>
@endpush

@section('content')
    @php
        // Helper function untuk warna avatar
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

        // Determine stage name
        function getStageName($stage)
        {
            $stages = [
                1 => 'Requested by User (Open)',
                2 => 'Received by Admin Engineering',
                3 => 'Waiting OM Approval',
                4 => 'In Progress / Technician Working',
                5 => 'Waiting VR Approval',
                6 => 'Completed by Technician',
                7 => 'User Check Done - Waiting GM', // Stage 7
                8 => 'GM Approved - Ready for Closure', // Stage 8
                9 => 'Closed by Admin', // Stage 9
            ];
            return $stages[$stage] ?? 'Unknown Stage';
        }

        // Get current user
        $user = auth()->user();
        $hasSignature = !empty($user->signature_path) && Storage::disk('public')->exists($user->signature_path);

        // Hanya AdminEng, OM, GM yang bisa save signature
        $canSaveSignature = in_array($user->role, ['admin_eng', 'om', 'gm']);

        // Determine available actions based on role and ticket status
        $availableActions = [];
        $ticketStatus = $ticket->status;
        $userRole = $user->role;

        // Admin Engineering Actions
        if ($userRole === 'admin_eng') {
            if ($ticketStatus === 'open') {
                $availableActions[] = 'receive';
                $availableActions[] = 'cancel';
            } elseif ($ticketStatus === 'pending_om') {
                // Waiting for OM
            } elseif (in_array($ticket->status, ['in_progress', 'pending_vr'])) {
                $availableActions[] = 'assign';
            } elseif ($ticketStatus === 'pending_vr') {
                $availableActions[] = 'create_vr';
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

        if ($userRole === 'technician' && $ticket->assigned_to == $user->id) {
            if ($ticket->status === 'in_progress') {
                $availableActions[] = 'complete';
                $availableActions[] = 'need_vr';
            } elseif ($ticket->status === 'pending_vr') {
                // Waiting for VR
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

        // GM Actions
        if ($userRole === 'gm' && $ticketStatus === 'pending_gm') {
            $availableActions[] = 'gm_approve';
            $availableActions[] = 'gm_reject';
        }

        // Always available
        $availableActions[] = 'print';
        $availableActions[] = 'back';

        // Check VR status
        $hasVR = $ticket->voucherRequests->count() > 0;
        $needsVR = $ticket->approval->needs_vr ?? false;

        // Check if due date is overdue
        $isOverdue = $ticket->due_date && $ticket->due_date < now();
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
                            <div class="stage-label">STEP {{ $ticket->current_stage }}</div>
                            <div class="stage-name">{{ getStageName($ticket->current_stage) }}</div>
                            <div class="stage-progress">
                                @for ($i = 1; $i <= 8; $i++)
                                    <div class="stage-dot {{ $i <= $ticket->current_stage ? 'active' : '' }}"></div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="sidebar-quick-actions">
                        <h6 class="mb-3"><i class="fas fa-bolt me-2"></i> Quick Actions</h6>
                        <div class="list-group list-group-flush mb-4">
                            {{-- Di bagian Quick Actions sidebar --}}
                            @if ($hasSignature && $canSaveSignature && in_array($user->role, ['admin_eng', 'om', 'gm']))
                                <!-- Quick Approve Button -->
                                @php
                                    $canQuickApprove = false;
                                    $quickApproveMessage = '';

                                    if ($user->role === 'admin_eng' && $ticket->status === 'open') {
                                        $canQuickApprove = true;
                                        $quickApproveMessage = 'Quick receive ticket using saved signature';
                                    } elseif ($user->role === 'om' && $ticket->status === 'pending_om') {
                                        $canQuickApprove = true;
                                        $quickApproveMessage = 'Quick approve ticket using saved signature';
                                    } elseif ($user->role === 'gm' && $ticket->status === 'pending_gm') {
                                        $canQuickApprove = true;
                                        $quickApproveMessage = 'Quick approve ticket using saved signature';
                                    }
                                @endphp

                                @if ($canQuickApprove)
                                    <a href="#" class="list-group-item list-group-item-action text-success"
                                        onclick="quickApprove()">
                                        <i class="fas fa-bolt me-2"></i> {{ $quickApproveMessage }}
                                    </a>
                                @endif
                            @endif
                            {{-- Di bagian Quick Actions sidebar --}}
                            @if ($ticket->status === 'received' && $user->role === 'admin_eng')
                                <a href="#" class="list-group-item list-group-item-action text-primary"
                                    onclick="continueToOM()">
                                    <i class="fas fa-forward me-2"></i> Continue to OM
                                </a>
                            @endif
                            <!-- HANYA ADMIN_ENG YANG BISA RECEIVE -->
                            @if (in_array('receive', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action text-orange"
                                    onclick="openReceiveModal()">
                                    <i class="fas fa-check-circle me-2"></i> Receive Ticket
                                    @if ($hasSignature && $canSaveSignature)
                                        <small class="d-block text-muted">Click to choose signature option</small>
                                    @endif
                                </a>
                            @endif

                            <!-- HANYA ADMIN_ENG YANG BISA ASSIGN -->

                            {{-- Di bagian Quick Actions sidebar --}}
                            @if (in_array('assign', $availableActions))
                                @if (in_array($ticket->status, ['in_progress', 'pending_vr']))
                                    <a href="#" class="list-group-item list-group-item-action"
                                        onclick="openAssignModal()">
                                        <i class="fas fa-user-plus me-2"></i> Assign Technician
                                    </a>
                                @endif
                            @endif
                            <!-- HANYA OM YANG BISA APPROVE -->
                            @if (in_array('om_approve', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openOmApproveModal()">
                                    <i class="fas fa-thumbs-up me-2"></i> OM Approve
                                    @if ($hasSignature && $canSaveSignature)
                                        <small class="d-block text-muted">Click to choose signature option</small>
                                    @endif
                                </a>
                            @endif

                            <!-- HANYA OM YANG BISA REJECT -->
                            @if (in_array('om_reject', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openOmRejectModal()">
                                    <i class="fas fa-thumbs-down me-2"></i> OM Reject
                                </a>
                            @endif

                            <!-- HANYA TECHNICIAN YANG BISA COMPLETE -->
                            @if (in_array('complete', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openCompleteModal()">
                                    <i class="fas fa-check-double me-2"></i> Mark Complete
                                </a>
                            @endif

                            <!-- HANYA TECHNICIAN YANG BISA REQUEST VR -->
                            @if (in_array('need_vr', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action" onclick="openVRModal()">
                                    <i class="fas fa-file-invoice-dollar me-2"></i> Need VR
                                </a>
                            @endif

                            <!-- HANYA ADMIN_ENG YANG BISA CREATE VR -->
                            @if (in_array('create_vr', $availableActions))
                                <a href="{{ route('tickets.vr.create', $ticket->id) }}"
                                    class="list-group-item list-group-item-action">
                                    <i class="fas fa-file-invoice me-2"></i> Create VR
                                </a>
                            @endif

                            <!-- HANYA USER ATAU ADMIN_ENG YANG CREATE TICKET YANG BISA CHECK -->
                            @if ($isReporter && $ticket->status === 'completed')
                                <!-- HANYA REPORTER YANG BISA CHECK ACCEPT -->
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openUserCheckAcceptModal()">
                                    <i class="fas fa-clipboard-check me-2"></i> Accept Completion
                                </a>

                                <!-- HANYA REPORTER YANG BISA CHECK REJECT -->
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openUserCheckRejectModal()">
                                    <i class="fas fa-times-circle me-2"></i> Reject Completion
                                </a>
                            @endif

                            <!-- HANYA GM YANG BISA APPROVE -->
                            @if (in_array('gm_approve', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openGmApproveModal()">
                                    <i class="fas fa-gavel me-2"></i> GM Approve
                                    @if ($hasSignature && $canSaveSignature)
                                        <small class="d-block text-muted">Click to choose signature option</small>
                                    @endif
                                </a>
                            @endif

                            <!-- HANYA GM YANG BISA REJECT -->
                            @if (in_array('gm_reject', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="openGmRejectModal()">
                                    <i class="fas fa-ban me-2"></i> GM Reject
                                </a>
                            @endif

                            <!-- HANYA ADMIN_ENG YANG BISA CLOSE -->
                            @if (in_array('close_admin', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action"
                                    onclick="closeTicketAdmin()">
                                    <i class="fas fa-lock me-2"></i> Close Ticket
                                </a>
                            @endif

                            <!-- DELETE TICKET (SUPERADMIN ONLY) -->
                            @if ($user->role === 'superadmin')
                                <a href="#" class="list-group-item list-group-item-action text-danger"
                                    onclick="deleteTicket()">
                                    <i class="fas fa-trash-alt me-2"></i> Delete Ticket
                                    <small class="d-block text-muted">Permanent deletion</small>
                                </a>
                            @endif

                            <!-- PRINT -->
                            <a href="#" class="list-group-item list-group-item-action" onclick="window.print()">
                                <i class="fas fa-print me-2"></i> Print Ticket
                            </a>

                            <!-- CANCEL -->
                            @if (in_array('cancel', $availableActions))
                                <a href="#" class="list-group-item list-group-item-action text-danger"
                                    onclick="openCancelModal()">
                                    <i class="fas fa-ban me-2"></i> Cancel Ticket
                                </a>
                            @endif

                            <!-- BACK -->
                            <a href="{{ route('tickets.index') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-arrow-left me-2"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <!-- Ticket Info -->
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
            <!-- VR Alert -->
            @if ($needsVR && !$hasVR && $ticket->status === 'pending_vr')
                <div class="voucher-alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                        <div>
                            <h6 class="mb-1"><strong>VR Required</strong></h6>
                            <p class="mb-0">This ticket requires a Voucher Request (VR) before work can continue.</p>
                            @if (in_array('create_vr', $availableActions))
                                <a href="{{ route('tickets.vr.create', $ticket->id) }}"
                                    class="btn btn-warning btn-sm mt-2">
                                    <i class="fas fa-file-invoice me-1"></i> Create VR
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Due Date Widget -->
            @if ($ticket->due_date)
                <div class="due-date-widget {{ $isOverdue ? 'due-date-overdue' : '' }}">
                    <div class="due-date-content">
                        <div class="due-date-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="due-date-text">
                            <h6>DUE DATE</h6>
                            <p>{{ $isOverdue ? 'OVERDUE!' : 'Deadline for completion' }}</p>
                        </div>
                    </div>
                    <div class="due-date-value">
                        {{ \Carbon\Carbon::parse($ticket->due_date)->format('d M Y, H:i') }}
                    </div>
                </div>
            @endif

            <!-- Action Buttons Bar -->
            <div class="action-buttons no-print">
                {{-- Di bagian Action Buttons Bar --}}
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
                {{-- Di bagian Action Buttons Bar --}}
                @if ($ticket->status === 'received' && $user->role === 'admin_eng')
                    <button class="btn-action btn-primary" onclick="continueToOM()">
                        <i class="fas fa-forward me-2"></i><span>Continue to OM</span>
                    </button>
                @endif
                <!-- HANYA ADMIN_ENG UNTUK RECEIVE TICKET -->
                @if (in_array('receive', $availableActions))
                    <button class="btn-action btn-primary" onclick="openReceiveModal()">
                        <i class="fas fa-check-circle"></i><span>Receive TIcket</span>
                    </button>
                @endif

                <!-- HANYA OM UNTUK APPROVE -->
                @if (in_array('om_approve', $availableActions))
                    <button class="btn-action btn-success" onclick="openOmApproveModal()">
                        <i class="fas fa-thumbs-up"></i> <span>OM Approve</span>
                    </button>
                @endif

                <!-- HANYA OM UNTUK REJECT -->
                @if (in_array('om_reject', $availableActions))
                    <button class="btn-action btn-danger" onclick="openOmRejectModal()">
                        <i class="fas fa-thumbs-down"></i> <span>OM Reject</span>
                    </button>
                @endif

                <!-- HANYA TECHNICIAN UNTUK MARK COMPLETE -->
                @if (in_array('complete', $availableActions))
                    <button class="btn-action btn-success" onclick="openCompleteModal()">
                        <i class="fas fa-check-double"></i> <span>Mark Complete</span>
                    </button>
                @endif

                <!-- HANYA TECHNICIAN UNTUK REQUEST VR -->
                @if (in_array('need_vr', $availableActions))
                    <button class="btn-action btn-warning" onclick="openVRModal()">
                        <i class="fas fa-file-invoice-dollar"></i><span> Need VR</span>
                    </button>
                @endif

                <!-- HANYA REPORTER UNTUK ACCEPT COMPLETION -->
                @if ($isReporter && $ticket->status === 'completed')
                    <button class="btn-action btn-success" onclick="openUserCheckAcceptModal()">
                        <i class="fas fa-clipboard-check"></i><span> Accept Completion</span>
                    </button>
                @endif

                <!-- HANYA REPORTER UNTUK REJECT COMPLETION -->
                @if ($isReporter && $ticket->status === 'completed')
                    <button class="btn-action btn-danger" onclick="openUserCheckRejectModal()">
                        <i class="fas fa-times-circle"></i> <span>Reject Completion</span>
                    </button>
                @endif

                <!-- HANYA GM UNTUK APPROVE -->
                @if (in_array('gm_approve', $availableActions))
                    <button class="btn-action btn-success" onclick="openGmApproveModal()">
                        <i class="fas fa-gavel"></i><span> GM Approve</span>
                    </button>
                @endif

                <!-- HANYA GM UNTUK REJECT -->
                @if (in_array('gm_reject', $availableActions))
                    <button class="btn-action btn-danger" onclick="openGmRejectModal()">
                        <i class="fas fa-ban"></i> <span>GM Reject</span>
                    </button>
                @endif

                <!-- HANYA ADMIN_ENG UNTUK CREATE VR -->
                @if (in_array('create_vr', $availableActions))
                    <a href="{{ route('tickets.vr.create', $ticket->id) }}" class="btn-action btn-warning">
                        <i class="fas fa-file-invoice"></i> <span> Create VR</span>
                    </a>
                @endif

                <!-- HANYA ADMIN_ENG UNTUK ASSIGN TECHNICIAN -->
                {{-- Di bagian Action Buttons --}}
                @if (in_array('assign', $availableActions))
                    {{-- Cek apakah status sudah in_progress atau pending_vr --}}
                    @if (in_array($ticket->status, ['in_progress', 'pending_vr']))
                        <button class="btn-action btn-primary" onclick="openAssignModal()">
                            <i class="fas fa-user-plus"></i><span> Assign Technician</span>
                        </button>
                    @endif
                @endif


                <!-- CANCEL TICKET -->
                @if (in_array('cancel', $availableActions))
                    <button class="btn-action btn-danger" onclick="openCancelModal()">
                        <i class="fas fa-ban"></i><span> Cancel Ticket</span>
                    </button>
                @endif

                <!-- CLOSE TICKET (ADMIN FINAL) -->
                @if (in_array('close_admin', $availableActions))
                    <button class="btn-action btn-dark" onclick="closeTicketAdmin()">
                        <i class="fas fa-lock"></i> <span>Close Ticket</span>
                    </button>
                @endif

                <!-- PRINT BUTTON -->
                <button class="btn-action btn-secondary" onclick="window.print()">
                    <i class="fas fa-print"></i><span> Print</span>
                </button>

                <!-- BACK TO LIST -->
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
                            <span class="info-label">TICKET NO:</span>
                            <span class="info-value" style="font-weight: 700; color: var(--primary-color);">
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
                                    {{ str_replace('_', ' ', $ticket->status) }}
                                </span>
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
                <div class="description-box">
                    {!! $ticket->description !!}
                </div>

                <!-- Attachments from TICKET -->
                @if ($ticket->attachments->count() > 0)
                    <div class="attachments-section">
                        <h6 class="section-title" style="font-size: 14px;">
                            <i class="fas fa-paperclip"></i> ATTACHED FILES ({{ $ticket->attachments->count() }})
                        </h6>
                        <div class="attachment-list">
                            @foreach ($ticket->attachments as $attachment)
                                @php
                                    $extension = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                                @endphp

                                @if ($isImage)
                                    <!-- Show image preview -->
                                    <div class="image-preview-container">
                                        <img src="{{ Storage::url($attachment->file_path) }}"
                                            alt="{{ $attachment->file_name }}" class="image-attachment me-2 mb-2"
                                            title="{{ $attachment->file_name }}"
                                            data-src="{{ Storage::url($attachment->file_path) }}">
                                    </div>
                                @else
                                    <!-- Show file link for non-images -->
                                    <a href="{{ Storage::url($attachment->file_path) }}" target="_blank"
                                        class="attachment-item" download>
                                        <i class="fas fa-file attachment-icon"></i>
                                        <span>{{ Str::limit($attachment->file_name, 30) }}</span>
                                        <small class="text-muted">({{ round($attachment->file_size / 1024) }}
                                            KB)</small>
                                    </a>
                                @endif
                            @endforeach
                        </div>
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

            <!-- SIGNATURES SECTION -->
            @if ($ticket->signatures->count() > 0)
                <div class="signatures-section">
                    <h5 class="section-title">
                        <i class="fas fa-signature"></i> SIGNATURES
                    </h5>
                    <!-- Di bagian Signatures Section -->
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

                                        {{-- @case(4)
                                            Completed by (Technician)
                                        @break

                                        @case(5)
                                            Checked by (User)
                                        @break --}}
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
                                        class="signature-image">
                                @else
                                    {{-- ... signature image ... --}}
                                    <div class="signature-info">
                                        <strong>{{ $signature->user->name ?? 'Unknown' }}</strong>
                                        <div>{{ ucfirst($signature->user->role ?? 'N/A') }}</div>
                                        <div>{{ $signature->signed_at->format('d M Y, H:i') }}</div>
                                        <small class="text-muted">Stage {{ $signature->stage }}</small> <!-- DEBUG -->
                                    </div>
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
            @endif

            <!-- VOUCHER REQUESTS -->
            @if ($ticket->voucherRequests->count() > 0)
                <div class="ticket-body">
                    <h5 class="section-title">
                        <i class="fas fa-file-invoice-dollar"></i> VOUCHER REQUESTS
                    </h5>
                    @foreach ($ticket->voucherRequests as $vr)
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>VR #{{ $vr->vr_number }}</strong>
                                    <span
                                        class="badge bg-{{ $vr->status === 'approved' ? 'success' : ($vr->status === 'rejected' ? 'danger' : 'warning') }} ms-2">
                                        {{ ucfirst($vr->status) }}
                                    </span>
                                </div>
                                <div>
                                    <strong>Total: Rp {{ number_format($vr->total_amount, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Qty</th>
                                                <th>Unit Price</th>
                                                <th>Total</th>
                                                <th>Vendor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($vr->items as $item)
                                                <tr>
                                                    <td>{{ $item->item_name }}</td>
                                                    <td>{{ $item->qty }}</td>
                                                    <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                                    <td>Rp
                                                        {{ number_format($item->qty * $item->unit_price, 0, ',', '.') }}
                                                    </td>
                                                    <td>{{ $item->vendor ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <!-- COMMENTS SECTION dengan Toggle -->
            <div class="comments-section">
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
                                {!! $comment->comment !!}

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
                                                    <br>
                                                    <a href="javascript:void(0)" class="small view-image-link"
                                                        data-src="{{ Storage::url($attachment->file_path) }}">
                                                        {{ $attachment->file_name }}
                                                    </a>
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

                    <!-- Add Comment Form - Hanya tampil jika ticket belum selesai -->
                    @php
                        // Status yang memungkinkan comment
                        $allowComments = in_array($ticket->status, [
                            'open',
                            'received',
                            'pending_om',
                            'in_progress',
                            'pending_vr',
                            'completed',
                        ]);

                        // Role yang boleh comment
                        $canComment =
                            in_array(auth()->user()->role, ['user', 'admin_eng', 'technician']) ||
                            $ticket->user_id == auth()->id() ||
                            $ticket->assigned_to == auth()->id();
                    @endphp

                    @if ($allowComments && $canComment)
                        <div class="comment-form-section" id="commentForm">
                            <h6 class="mb-3"><i class="fas fa-comment-medical me-2"></i> ADD COMMENT</h6>
                            <form id="commentFormSubmit" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <textarea name="comment" id="commentText" class="form-control" rows="4"
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
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-paper-plane me-2"></i> Post Comment
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @elseif(!$allowComments && $canComment)
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Commenting is disabled because this ticket is
                            {{ $ticket->status === 'closed' ? 'closed' : 'completed and checked' }}.
                        </div>
                    @endif
                </div>
            </div>
            <!-- ACTIVITY SECTION -->
            <div class="activity-section">
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
                                    <i class="fas fa-{{ getActivityIcon($activity->action) }} activity-icon"></i>
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

    <!-- New Signature Modal (Password Verification) -->
    <div class="modal fade" id="newSignatureModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2"></i> Create New Signature
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                            <button type="submit" class="btn btn-warning">
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="quickApproveForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You are about to approve this ticket using your saved signature.
                        </div>
                        <div class="text-center mb-3">
                            @if ($user->signature_path && Storage::disk('public')->exists($user->signature_path))
                                <img src="{{ Storage::url($user->signature_path) }}" alt="Your Signature"
                                    style="max-height: 80px; border: 1px solid #ddd; padding: 5px;">
                                <p class="small mt-2">Your saved signature will be used</p>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve with Saved Signature</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Ticket Modal (Superadmin Only) -->
    <div class="modal fade" id="deleteTicketModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-trash-alt me-2 text-danger"></i> Delete Ticket
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="deleteTicketForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>WARNING:</strong> This action will permanently delete the ticket and all related
                            data.
                            This cannot be undone!
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Enter your password to confirm *</label>
                            <input type="password" name="password" class="form-control"
                                placeholder="Your account password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Permanently</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Receive Modal - HANYA ADMIN_ENG -->
    <div class="modal fade" id="receiveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i> Receive Ticket
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="receiveForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You are about to receive this ticket. Please provide your signature.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Draw Your Signature *</label>
                            <div class="signature-canvas-container border rounded mb-3">
                                <canvas id="receiveSignatureCanvas" class="modal-signature-canvas"></canvas>
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
                        <button type="submit" class="btn btn-success">Receive Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- OM Approve Modal -->
    <div class="modal fade" id="omApproveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-thumbs-up me-2"></i> OM Approve Ticket
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="omApproveForm">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You are about to approve this ticket. Please provide your signature.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Draw Your Signature *</label>
                            <div class="signature-canvas-container border rounded mb-3">
                                <canvas id="omApproveSignatureCanvas" class="modal-signature-canvas"></canvas>
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
                        <button type="submit" class="btn btn-success">Approve Ticket</button>
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
                        <i class="fas fa-thumbs-down me-2"></i> OM Reject Ticket
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="omRejectForm">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Are you sure you want to reject this ticket?
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rejection Reason *</label>
                            <textarea name="rejection_reason" class="form-control" rows="3"
                                placeholder="Please provide reason for rejection..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Complete Modal - HANYA TECHNICIAN -->
    <div class="modal fade" id="completeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check-double me-2"></i> Mark as Complete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="completeForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Completion Notes (Optional)</label>
                            <textarea name="completion_notes" class="form-control" rows="3"
                                placeholder="Describe what was done, parts replaced, etc."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Your Signature *</label>
                            <div class="signature-canvas-container border rounded mb-3">
                                <canvas id="completeSignatureCanvas" class="modal-signature-canvas"></canvas>
                            </div>
                            <div class="signature-actions">
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="clearCompleteSignature()">
                                    <i class="fas fa-eraser me-1"></i> Clear
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm"
                                    onclick="undoCompleteSignature()">
                                    <i class="fas fa-undo me-1"></i> Undo
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            By signing, you confirm that the work has been completed according to specifications.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Mark Complete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- VR Modal - HANYA TECHNICIAN -->
    <div class="modal fade" id="vrModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-invoice-dollar me-2"></i> Request VR
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="vrForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Requesting a VR will pause this ticket until the VR is approved.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reason for VR *</label>
                            <textarea name="vr_reason" class="form-control" rows="3"
                                placeholder="Explain why you need a voucher request (parts needed, materials, etc.)" required></textarea>
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
                        <button type="submit" class="btn btn-warning">Request VR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Check Accept Modal -->
    <div class="modal fade" id="userCheckAcceptModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-clipboard-check me-2"></i> Accept Completion
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                <canvas id="userAcceptSignatureCanvas" class="modal-signature-canvas"></canvas>
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
                        <button type="submit" class="btn btn-success">Accept Completion</button>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                        <button type="submit" class="btn btn-danger">Reject Completion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- GM Approve Modal -->
    <div class="modal fade" id="gmApproveModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-gavel me-2"></i> GM Approve Ticket
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="gmApproveForm">
                    @csrf
                    <input type="hidden" name="action" value="approve">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            You are about to give final approval to this ticket. Please provide your signature.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Draw Your Signature *</label>
                            <div class="signature-canvas-container border rounded mb-3">
                                <canvas id="gmApproveSignatureCanvas" class="modal-signature-canvas"></canvas>
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
                        <button type="submit" class="btn btn-success">Approve Ticket</button>
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
                        <i class="fas fa-ban me-2"></i> GM Reject Ticket
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="gmRejectForm">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Are you sure you want to reject this ticket?
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rejection Reason *</label>
                            <textarea name="rejection_reason" class="form-control" rows="3"
                                placeholder="Please provide reason for rejection..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Modal - HANYA ADMIN_ENG -->
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i> Assign Technician
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                        <button type="submit" class="btn btn-primary">Assign</button>
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
                        <i class="fas fa-ban me-2"></i> Cancel Ticket
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="cancelForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Are you sure you want to cancel this ticket? This action cannot be undone.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cancellation Reason *</label>
                            <textarea name="cancellation_reason" class="form-control" rows="3"
                                placeholder="Please provide reason for cancellation..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Go Back</button>
                        <button type="submit" class="btn btn-danger">Cancel Ticket</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- <!-- jQuery -->
    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script> --}}

    <!-- Select2 -->
    <script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>

    <!-- Toastr -->
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    {{--
    <!-- Bootstrap Modal -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script> --}}

    <!-- Signature Pad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        // Global variables for signature pads
        let receiveSignaturePad = null;
        let omApproveSignaturePad = null;
        let completeSignaturePad = null;
        let userAcceptSignaturePad = null;
        let gmApproveSignaturePad = null;
        let pendingSignatureAction = null;

        // Toastr configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        $(document).ready(function() {
            // ============================================
            // MODAL INITIALIZATION
            // ============================================

            // Receive Modal
            $('#receiveModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('receiveSignatureCanvas');
                if (canvas) {
                    canvas.width = canvas.offsetWidth;
                    canvas.height = 200;

                    receiveSignaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5,
                        throttle: 16
                    });
                }
            });

            // OM Approve Modal
            $('#omApproveModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('omApproveSignatureCanvas');
                if (canvas) {
                    canvas.width = canvas.offsetWidth;
                    canvas.height = 200;

                    omApproveSignaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5,
                        throttle: 16
                    });
                }
            });

            // Complete Modal
            $('#completeModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('completeSignatureCanvas');
                if (canvas) {
                    canvas.width = canvas.offsetWidth;
                    canvas.height = 200;

                    completeSignaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5,
                        throttle: 16
                    });
                }
            });

            // User Accept Modal
            $('#userCheckAcceptModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('userAcceptSignatureCanvas');
                if (canvas) {
                    canvas.width = canvas.offsetWidth;
                    canvas.height = 200;

                    userAcceptSignaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5,
                        throttle: 16
                    });
                }
            });

            // GM Approve Modal
            $('#gmApproveModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('gmApproveSignatureCanvas');
                if (canvas) {
                    canvas.width = canvas.offsetWidth;
                    canvas.height = 200;

                    gmApproveSignaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5,
                        throttle: 16
                    });
                }
            });

            // ============================================
            // FORM HANDLERS
            // ============================================

            // Receive Ticket Form
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

            // OM Approve Form
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

            // OM Reject Form
            $('#omRejectForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.om-action', $ticket->id) }}', formData, '#omRejectModal');
            });

            // Complete Form
            $('#completeForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                if (!completeSignaturePad || completeSignaturePad.isEmpty()) {
                    toastr.error('Please draw your signature');
                    return;
                }
                formData.append('signature_data', completeSignaturePad.toDataURL());

                submitForm('{{ route('tickets.complete', $ticket->id) }}', formData, '#completeModal');
            });

            // VR Form
            $('#vrForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.request-vr', $ticket->id) }}', formData, '#vrModal');
            });

            // User Check Accept Form
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

            // User Check Reject Form
            $('#userCheckRejectForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.user-check', $ticket->id) }}', formData,
                    '#userCheckRejectModal');
            });

            // GM Approve Form
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

            // GM Reject Form
            $('#gmRejectForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.gm-action', $ticket->id) }}', formData, '#gmRejectModal');
            });

            // Assign Form
            $('#assignForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.assign', $ticket->id) }}', formData, '#assignModal');
            });

            // Cancel Form
            $('#cancelForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.cancel', $ticket->id) }}', formData, '#cancelModal');
            });

            // Comment Form
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

            // Quick Approve Form
            $('#quickApproveForm').on('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                submitForm('{{ route('tickets.quick-approve', $ticket->id) }}', formData,
                    '#quickApproveModal');
            });

            // Delete Ticket Form
            // Delete Ticket Form - GANTI DARI POST KE DELETE
            $('#deleteTicketForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const password = formData.get('password');

                Swal.fire({
                    title: 'Are you absolutely sure?',
                    text: "This will permanently delete the ticket and cannot be recovered!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete permanently!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Gunakan DELETE method dengan CSRF token
                        $.ajax({
                            url: '{{ route('tickets.destroy', $ticket->id) }}',
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            data: {
                                password: password
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message ||
                                        'Ticket deleted successfully');
                                    setTimeout(() => {
                                        if (response.redirect) {
                                            window.location.href = response
                                                .redirect;
                                        }
                                    }, 1500);
                                } else {
                                    toastr.error(response.message ||
                                        'Failed to delete ticket');
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Failed to delete ticket';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                toastr.error(errorMessage);
                            }
                        });
                    }
                });
            });

            function deleteTicket() {
                $('#deleteTicketModal').modal('show');
            }
            // New Signature Form (Password Verification)
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

                            // Buka modal signature yang sesuai
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
                        let errorMessage = 'Password verification failed';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                    }
                });
            });

            // ============================================
            // IMAGE VIEWER
            // ============================================

            // Handle image clicks
            $(document).on('click', '.image-attachment, .comment-image, .view-image-link', function(e) {
                e.preventDefault();
                e.stopPropagation();

                let imageSrc = $(this).data('src') || $(this).attr('src') || $(this).attr('href');

                if (!imageSrc) return;

                // Create image modal
                const modalHtml = `
                    <div class="image-modal-backdrop" id="imageModal">
                        <div class="image-modal-content">
                            <div class="image-modal-close" onclick="closeImageModal()">
                                <i class="fas fa-times"></i>
                            </div>
                            <img src="${imageSrc}" alt="Preview"
                                 onerror="this.onerror=null; this.src='{{ asset('assets/images/no-image.png') }}'">
                        </div>
                    </div>
                `;

                $('body').append(modalHtml);

                // Close on backdrop click
                $('#imageModal').on('click', function(e) {
                    if (e.target === this) {
                        closeImageModal();
                    }
                });

                // Close on escape key
                $(document).on('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeImageModal();
                    }
                });
            });
        });

        // ============================================
        // HELPER FUNCTIONS
        // ============================================

        function submitForm(url, formData, modalId = null, reload = true) {
            const form = modalId ? $(modalId + ' form') : $('form:last');
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin me-2"></i>Processing...');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);

                        if (modalId) {
                            $(modalId).modal('hide');
                        }

                        if (reload) {
                            setTimeout(() => {
                                if (response.redirect) {
                                    window.location.href = response.redirect;
                                } else {
                                    location.reload();
                                }
                            }, 1000);
                        }
                    } else {
                        toastr.error(response.message || 'Operation failed');
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).html(originalText);

                    let message = 'An error occurred. Please try again.';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.error) {
                            message = xhr.responseJSON.error;
                        }

                        // Show validation errors
                        if (xhr.responseJSON.errors) {
                            Object.values(xhr.responseJSON.errors).forEach(error => {
                                if (Array.isArray(error)) {
                                    error.forEach(err => toastr.error(err));
                                } else {
                                    toastr.error(error);
                                }
                            });
                        }
                    }

                    toastr.error(message);
                },
                complete: function() {
                    setTimeout(() => {
                        submitBtn.prop('disabled', false).html(originalText);
                    }, 1000);
                }
            });
        }

        // ============================================
        // SIGNATURE FUNCTIONS
        // ============================================

        // Receive signature functions
        window.clearReceiveSignature = function() {
            if (receiveSignaturePad) {
                receiveSignaturePad.clear();
            }
        };

        window.undoReceiveSignature = function() {
            if (receiveSignaturePad) {
                const data = receiveSignaturePad.toData();
                if (data.length > 0) {
                    data.pop();
                    receiveSignaturePad.fromData(data);
                }
            }
        };

        // OM Approve signature functions
        window.clearOmApproveSignature = function() {
            if (omApproveSignaturePad) {
                omApproveSignaturePad.clear();
            }
        };

        window.undoOmApproveSignature = function() {
            if (omApproveSignaturePad) {
                const data = omApproveSignaturePad.toData();
                if (data.length > 0) {
                    data.pop();
                    omApproveSignaturePad.fromData(data);
                }
            }
        };

        // Complete signature functions
        window.clearCompleteSignature = function() {
            if (completeSignaturePad) {
                completeSignaturePad.clear();
            }
        };

        window.undoCompleteSignature = function() {
            if (completeSignaturePad) {
                const data = completeSignaturePad.toData();
                if (data.length > 0) {
                    data.pop();
                    completeSignaturePad.fromData(data);
                }
            }
        };

        // User Accept signature functions
        window.clearUserAcceptSignature = function() {
            if (userAcceptSignaturePad) {
                userAcceptSignaturePad.clear();
            }
        };

        window.undoUserAcceptSignature = function() {
            if (userAcceptSignaturePad) {
                const data = userAcceptSignaturePad.toData();
                if (data.length > 0) {
                    data.pop();
                    userAcceptSignaturePad.fromData(data);
                }
            }
        };

        // GM Approve signature functions
        window.clearGmApproveSignature = function() {
            if (gmApproveSignaturePad) {
                gmApproveSignaturePad.clear();
            }
        };

        window.undoGmApproveSignature = function() {
            if (gmApproveSignaturePad) {
                const data = gmApproveSignaturePad.toData();
                if (data.length > 0) {
                    data.pop();
                    gmApproveSignaturePad.fromData(data);
                }
            }
        };

        // ============================================
        // MODAL OPEN FUNCTIONS
        // ============================================

        // Quick Approve Function
        function quickApprove() {
            Swal.fire({
                title: 'Quick Approve?',
                text: "Use your saved signature to approve this ticket",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, approve it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#quickApproveModal').modal('show');
                }
            });
        }

        // Delete Ticket Function
        function deleteTicket() {
            $('#deleteTicketModal').modal('show');
        }

        // Open signature modal with password verification
        function openSignatureModalWithPassword(actionType) {
            pendingSignatureAction = actionType;
            $('#newSignatureModal').modal('show');
        }

        // Modified modal open functions
        function openReceiveModal() {
            @if ($hasSignature && $canSaveSignature)
                // Jika sudah punya signature, tawarkan opsi
                Swal.fire({
                    title: 'How do you want to sign?',
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: 'Use Saved Signature',
                    denyButtonText: 'Create New Signature',
                    cancelButtonText: 'Cancel',
                    icon: 'question',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        quickApprove();
                    } else if (result.isDenied) {
                        openSignatureModalWithPassword('receive');
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
                    icon: 'question',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        quickApprove();
                    } else if (result.isDenied) {
                        openSignatureModalWithPassword('om_approve');
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
                    icon: 'question',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        quickApprove();
                    } else if (result.isDenied) {
                        openSignatureModalWithPassword('gm_approve');
                    }
                });
            @else
                $('#gmApproveModal').modal('show');
            @endif
        }

        // Other modal functions (unchanged)
        function openOmRejectModal() {
            $('#omRejectModal').modal('show');
        }

        function openCompleteModal() {
            $('#completeModal').modal('show');
        }

        function openVRModal() {
            $('#vrModal').modal('show');
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
                title: 'Close Ticket Administratively?',
                text: "This will mark the ticket as administratively closed after GM approval.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#343a40',
                confirmButtonText: 'Yes, close it',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('tickets.close-admin', $ticket->id) }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message || 'Ticket closed administratively');
                                setTimeout(() => {
                                    if (response.redirect) {
                                        window.location.href = response.redirect;
                                    } else {
                                        location.reload();
                                    }
                                }, 1500);
                            } else {
                                toastr.error(response.message || 'Failed to close ticket');
                            }
                        },
                        error: function(xhr) {
                            console.error('Close ticket error:', xhr);
                            let errorMessage = 'Failed to close ticket. Please try again.';

                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseJSON.error) {
                                    errorMessage = xhr.responseJSON.error;
                                }
                            }

                            toastr.error(errorMessage);
                        }
                    });
                }
            });
        }

        function scrollToCommentForm() {
            document.getElementById('commentForm').scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Tambahkan di bagian JavaScript functions
        function continueToOM() {
            Swal.fire({
                title: 'Continue to OM?',
                text: "Send this ticket to Operation Manager for approval",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, continue',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('tickets.continue-to-om', $ticket->id) }}',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Sending to OM',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Failed to continue to OM'
                            });
                        }
                    });
                }
            });
        }
        // Tambahkan di bagian script
        $(document).ready(function() {
            // Prevent double-tap zoom on buttons
            $('.btn-action').on('touchstart', function(e) {
                if ($(this).hasClass('disabled')) {
                    e.preventDefault();
                    return false;
                }
                $(this).addClass('active');
            }).on('touchend', function() {
                $(this).removeClass('active');
            });

            // Improve scrolling for signature sections
            $('.signature-grid, .approval-grid').on('touchmove', function(e) {
                e.stopPropagation();
            });

            // Close modals on backdrop tap for mobile
            $('.modal').on('show.bs.modal', function() {
                $('body').addClass('modal-open');
            });

            $('.modal').on('hidden.bs.modal', function() {
                $('body').removeClass('modal-open');
            });

            // Fix for iOS input focus in modal
            $(document).on('focus', 'input, textarea, select', function() {
                setTimeout(function() {
                    window.scrollTo(0, 0);
                }, 100);
            });
        });
        $(document).ready(function() {
            // Toggle Activity Timeline
            $('.activity-header').on('click', function() {
                const $activitySection = $(this).closest('.activity-section');
                $activitySection.toggleClass('collapsed');

                const $timeline = $activitySection.find('.activity-timeline');
                if ($activitySection.hasClass('collapsed')) {
                    $timeline.css('max-height', '0');
                } else {
                    $timeline.css('max-height', $timeline[0].scrollHeight + 'px');
                }
            });

            // Toggle Comments Section
            $('.comments-header').on('click', function() {
                const $commentsSection = $(this).closest('.comments-section');
                $commentsSection.toggleClass('collapsed');

                const $commentsContainer = $commentsSection.find('.comments-container');
                if ($commentsSection.hasClass('collapsed')) {
                    $commentsContainer.css('max-height', '0');
                } else {
                    $commentsContainer.css('max-height', $commentsContainer[0].scrollHeight + 'px');
                }
            });

            // Cek status ticket untuk comment form
            function checkCommentFormStatus() {
                const ticketStatus = '{{ $ticket->status }}';
                const $commentForm = $('#commentForm');

                // Status yang tidak boleh comment
                const disabledStatuses = ['closed', 'cancelled', 'ready_for_closure', 'pending_gm'];

                if (disabledStatuses.includes(ticketStatus)) {
                    $commentForm.addClass('disabled').prop('disabled', true);
                    $commentForm.find('textarea, input, button').prop('disabled', true);

                    // Jika sudah completed dan checked (user sudah check done)
                    if (ticketStatus === 'pending_gm' || '{{ $ticket->approval->user_checked ?? false }}') {
                        $commentForm.addClass('hidden');
                    }
                }
            }

            // Panggil saat halaman dimuat
            checkCommentFormStatus();

            // Cek ukuran layar untuk collapse/expand
            function checkScreenSize() {
                const $activitySection = $('.activity-section');
                const $activityTimeline = $activitySection.find('.activity-timeline');

                const $commentsSection = $('.comments-section');
                const $commentsContainer = $commentsSection.find('.comments-container');

                if ($(window).width() <= 768) {
                    // Di mobile, default collapsed
                    if (!$activitySection.hasClass('collapsed')) {
                        $activitySection.addClass('collapsed');
                        $activityTimeline.css('max-height', '0');
                    }
                    if (!$commentsSection.hasClass('collapsed')) {
                        $commentsSection.addClass('collapsed');
                        $commentsContainer.css('max-height', '0');
                    }
                } else {
                    // Di desktop, default expanded
                    if ($activitySection.hasClass('collapsed')) {
                        $activitySection.removeClass('collapsed');
                        $activityTimeline.css('max-height', 'none');
                    }
                    if ($commentsSection.hasClass('collapsed')) {
                        $commentsSection.removeClass('collapsed');
                        $commentsContainer.css('max-height', 'none');
                    }
                }
            }

            // Panggil saat halaman dimuat dan saat resize
            checkScreenSize();
            $(window).on('resize', checkScreenSize);
        });
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
            'login' => 'sign-in-alt',
            'logout' => 'sign-out-alt',
            'admin_eng_approved_quick' => 'bolt',
            'om_approved_quick' => 'bolt',
            'gm_approved_quick' => 'bolt',
        ];

        return $icons[$action] ?? 'info-circle';
    }
@endphp
