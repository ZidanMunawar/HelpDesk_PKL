@extends('layouts.main')

@section('title', 'Purchase Requests | ' . config('app.name'))

@section('page-title', 'Purchase Requests')

@section('breadcrumb')
    @php
        $breadcrumb = [['title' => 'Purchase Requests', 'url' => 'javascript:void(0)']];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css">

    <style>
        :root {
            --navy: #003366;
            --orange: #ff6600;
            --orange-light: #ff8533;
        }

        .modal-title {
            color: whitesmoke;
        }

        /* ========== HEADER ACTIONS ========== */
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-title-section h4 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }

        .page-title-section h4 i {
            color: #ff6200;
            margin-right: 8px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* ========== MODERN BUTTON STYLES ========== */
        .btn-modern {
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            outline: none;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            height: 44px;
        }

        .btn-modern::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-modern:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-modern:active {
            transform: scale(0.95);
        }

        .btn-create-pr {
            background: linear-gradient(135deg, var(--orange), var(--orange-light));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 102, 0, 0.3);
            font-size: 14px;
            height: 44px;
            cursor: pointer;
        }

        .btn-create-pr:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 102, 0, 0.4);
            color: white;
        }

        .btn-create-pr:active {
            transform: scale(0.95);
        }

        /* ========== FLOATING ACTION BUTTON (FAB) ========== */
        .fab-export {
            position: fixed;
            bottom: 80px;
            right: 30px;
            z-index: 1000;
        }

        .fab-button {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff7b00, #ff6200);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(255, 98, 0, 0.4);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .fab-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(255, 98, 0, 0.5);
        }

        .fab-button i {
            font-size: 24px;
        }

        .fab-menu {
            position: absolute;
            bottom: 70px;
            right: 0;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            overflow: hidden;
            z-index: 1001;
        }

        .fab-export:hover .fab-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .fab-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
            transition: all 0.2s ease;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
        }

        .fab-menu a:last-child {
            border-bottom: none;
        }

        .fab-menu a:hover {
            background: #fff3e0;
            padding-left: 25px;
        }

        .fab-menu a i {
            width: 20px;
            font-size: 16px;
        }

        .fab-menu a:first-child i {
            color: #27ae60;
        }

        .fab-menu a:last-child i {
            color: #e74c3c;
        }

        /* ========== SEARCH & FILTER SECTION ========== */
        .search-filter-wrapper {
            background: white;
            border-radius: 12px;
            border: 1px solid #eaeaea;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Search Bar */
        .search-bar {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
        }

        .search-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-input-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 0 15px;
            transition: all 0.3s ease;
        }

        .search-input-wrapper:focus-within {
            border-color: #ff6200;
            box-shadow: 0 0 0 3px rgba(255, 98, 0, 0.1);
        }

        .search-input-wrapper i {
            color: #999;
            font-size: 14px;
        }

        .search-input {
            flex: 1;
            border: none;
            padding: 12px 10px;
            font-size: 14px;
            background: transparent;
        }

        .search-input:focus {
            outline: none;
        }

        .search-btn {
            background: #ff6200;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-btn:hover {
            background: #ff7b00;
            transform: translateY(-1px);
        }

        /* Filter Header */
        .filter-header {
            padding: 12px 16px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s ease;
        }

        .filter-header:hover {
            background: #f0f0f0;
        }

        .filter-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #555;
        }

        .filter-header-left i {
            color: #ff6200;
        }

        .filter-header-arrow {
            transition: transform 0.3s ease;
            color: #999;
        }

        .filter-header.collapsed .filter-header-arrow {
            transform: rotate(0deg);
        }

        .filter-header:not(.collapsed) .filter-header-arrow {
            transform: rotate(180deg);
        }

        .filter-body {
            padding: 16px;
            border-top: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .filter-body.collapsed {
            display: none;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-item {
            width: 100%;
        }

        .filter-label {
            font-size: 12px;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }

        .filter-select,
        .filter-input {
            width: 100%;
            height: 40px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 13px;
            transition: all 0.2s ease;
            background-color: white;
        }

        .filter-select:focus,
        .filter-input:focus {
            border-color: #ff6200;
            box-shadow: 0 0 0 2px rgba(255, 98, 0, 0.1);
            outline: none;
        }

        /* Filter Actions - Horizontal scroll di mobile */
        .filter-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 5px;
            overflow-x: auto;
            flex-wrap: nowrap;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
        }

        .filter-actions::-webkit-scrollbar {
            display: none;
        }

        .btn-filter,
        .btn-reset {
            flex-shrink: 0;
            white-space: nowrap;
        }

        .btn-filter {
            background: #ff6200;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 40px;
        }

        .btn-filter:hover {
            background: #ff7b00;
            transform: translateY(-1px);
        }

        .btn-reset {
            background: #f5f5f5;
            color: #666;
            border: 1px solid #ddd;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            height: 40px;
        }

        .btn-reset:hover {
            background: #ff6200;
            border-color: #ff6200;
            color: white;
        }

        /* Loading Overlay */
        .pr-loading {
            position: relative;
            min-height: 200px;
        }

        .pr-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 10;
            border-radius: 12px;
        }

        .pr-loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #ff6200;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 11;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        /* ========== PR CARDS ========== */
        .pr-card {
            background: white;
            border-radius: 12px;
            margin-bottom: 16px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .pr-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .pr-card-body {
            display: flex;
            flex-wrap: wrap;
            padding: 16px;
            gap: 16px;
        }

        .pr-thumbnail {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .pr-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .pr-thumbnail .no-image {
            font-size: 32px;
            color: #9ca3af;
        }

        .pr-info {
            flex: 1;
            min-width: 200px;
        }

        .pr-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .pr-number {
            font-size: 14px;
            font-weight: 700;
            color: var(--orange);
            background: #fff3e0;
            padding: 4px 12px;
            border-radius: 20px;
        }

        .pr-status {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .pr-status.pending {
            background: #fef3c7;
            color: #d97706;
        }

        .pr-status.admin_approved {
            background: #dbeafe;
            color: #2563eb;
        }

        .pr-status.om_approved {
            background: #d1fae5;
            color: #059669;
        }

        .pr-status.gm_approved {
            background: #a7f3d0;
            color: #047857;
        }

        .pr-status.paid {
            background: #064e3b;
            color: white;
        }

        .pr-status.rejected {
            background: #fee2e2;
            color: #dc2626;
        }

        .pr-ticket {
            font-size: 12px;
            color: #666;
            margin-bottom: 8px;
        }

        .pr-ticket a {
            color: var(--navy);
            text-decoration: none;
            font-weight: 500;
        }

        .pr-ticket a:hover {
            color: var(--orange);
        }

        .pr-title {
            font-size: 13px;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
        }

        .pr-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            font-size: 11px;
            color: #888;
            margin-top: 8px;
        }

        .pr-meta i {
            width: 14px;
            margin-right: 4px;
        }

        .pr-notes {
            font-size: 12px;
            color: #888;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #eee;
        }

        .pr-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: flex-start;
            flex-shrink: 0;
        }

        .pr-action-btn {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .pr-action-btn.view {
            background: #f3f4f6;
            color: #374151;
        }

        .pr-action-btn.view:hover {
            background: #e5e7eb;
        }

        .pr-action-btn.approve {
            background: #10b981;
            color: white;
        }

        .pr-action-btn.approve:hover {
            background: #059669;
        }

        .pr-action-btn.reject {
            background: #ef4444;
            color: white;
        }

        .pr-action-btn.reject:hover {
            background: #dc2626;
        }

        .pr-action-btn.paid {
            background: #047857;
            color: white;
        }

        .pr-action-btn.paid:hover {
            background: #065f46;
        }

        .pr-action-btn.delete {
            background: #fee2e2;
            color: #dc2626;
        }

        .pr-action-btn.delete:hover {
            background: #fecaca;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .empty-state-icon {
            font-size: 60px;
            color: #e0e0e0;
            margin-bottom: 20px;
        }

        .empty-state-title {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
            font-weight: 500;
        }

        .empty-state-text {
            font-size: 14px;
            color: #999;
            margin-bottom: 25px;
        }

        .empty-state-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ========== PAGINATION STYLES ========== */
        .pagination-wrapper {
            margin-top: 25px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 12px;
            display: flex;
            justify-content: center;
        }

        .pagination {
            margin: 0;
            flex-wrap: wrap;
            gap: 5px;
        }

        .pagination .page-link {
            font-size: 13px;
            padding: 8px 14px;
            border-color: #ddd;
            color: #666;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .pagination .page-item.active .page-link {
            background: #ff6200;
            border-color: #ff6200;
            color: white;
            box-shadow: 0 4px 10px rgba(255, 98, 0, 0.3);
        }

        .pagination .page-link:hover {
            background: #ff7b00;
            border-color: #ff7b00;
            color: white;
            transform: translateY(-1px);
        }

        /* ========== CANVAS SIGNATURE - TRANSPARENT! ========== */
        .modal-signature-canvas {
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: crosshair;
            background: transparent !important;
            width: 100%;
            max-width: 300px;
            height: auto;
            aspect-ratio: 3 / 2;
            display: block;
            margin: 0 auto;
        }

        .signature-canvas-container {
            display: flex;
            justify-content: center;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
        }

        /* Radio button styles - FIX! */
        .form-check-input {
            cursor: pointer !important;
        }

        .form-check-label {
            cursor: pointer !important;
        }

        .form-check-input:disabled {
            cursor: not-allowed !important;
            opacity: 0.6;
        }

        .form-check-input:checked {
            background-color: #ff6600 !important;
            border-color: #ff6600 !important;
        }

        /* ========== MODAL STEP INDICATOR ========== */
        .modal-step {
            display: none;
        }

        .modal-step.active {
            display: block;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #9ca3af;
            font-size: 13px;
        }

        .step.active {
            color: var(--orange);
        }

        .step.completed {
            color: #10b981;
        }

        .step-number {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }

        .step.active .step-number {
            background: var(--orange);
            color: white;
        }

        .step.completed .step-number {
            background: #10b981;
            color: white;
        }

        /* Image Preview */
        #photoPreview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        #photoPreview img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .alert-info {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 13px;
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
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .action-buttons {
                width: 100%;
            }

            .btn-create-pr {
                width: 100%;
                justify-content: center;
            }

            .search-form {
                flex-direction: row;
                gap: 8px;
            }

            .search-input-wrapper {
                flex: 1;
                padding: 0 12px;
            }

            .search-btn {
                padding: 10px 16px;
                white-space: nowrap;
            }

            .search-btn span {
                display: none;
            }

            .search-btn i {
                margin: 0;
            }

            .filter-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .pr-card-body {
                flex-direction: column;
            }

            .pr-thumbnail {
                width: 100%;
                height: 150px;
            }

            .pr-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .fab-export {
                bottom: 70px;
                right: 16px;
            }

            .fab-button {
                width: 48px;
                height: 48px;
            }

            .fab-button i {
                font-size: 20px;
            }

            .pagination-wrapper {
                padding: 10px;
            }

            .pagination .page-link {
                padding: 6px 10px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .fab-export {
                bottom: 60px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-0">
        <!-- Header dengan Tombol Create -->
        <div class="header-actions">
            <div class="page-title-section">
                <h4><i class="fas fa-receipt me-2" style="color: var(--orange);"></i>Purchase Requests</h4>
            </div>
            <div class="action-buttons">
                @if (in_array(Auth::user()->role, ['admin_eng']))
                    <button type="button" class="btn-create-pr" id="createPrBtn">
                        <i class="fas fa-plus-circle"></i> New PR
                    </button>
                @endif
            </div>
        </div>

        <!-- Search & Filter Section -->
        <div class="search-filter-wrapper">
            <!-- Search Bar -->
            <div class="search-bar">
                <form action="{{ route('voucher-requests.index') }}" method="GET" id="searchForm"
                    class="ajax-filter-form search-form">
                    <div class="search-input-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" class="search-input"
                            placeholder="Search by PR Number, Ticket Number, or Notes..." value="{{ request('search') }}"
                            autocomplete="off">
                    </div>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                        <span>Search</span>
                    </button>
                </form>
            </div>

            <!-- Filter Header -->
            <div class="filter-header {{ request()->anyFilled(['status', 'date_from', 'date_to']) ? '' : 'collapsed' }}"
                id="filterToggle">
                <div class="filter-header-left">
                    <i class="fas fa-filter"></i>
                    <span>Advanced Filters</span>
                    @if (request()->anyFilled(['status', 'date_from', 'date_to']))
                        <span class="badge bg-warning text-dark ms-2" style="font-size: 10px;">Active</span>
                    @endif
                </div>
                <i class="fas fa-chevron-down filter-header-arrow"></i>
            </div>

            <!-- Filter Body -->
            <div class="filter-body {{ request()->anyFilled(['status', 'date_from', 'date_to']) ? '' : 'collapsed' }}"
                id="filterBody">
                <form action="{{ route('voucher-requests.index') }}" method="GET" id="filterForm"
                    class="ajax-filter-form">
                    <input type="hidden" name="search" value="{{ request('search') }}">

                    <div class="filter-grid">
                        <div class="filter-item">
                            <label class="filter-label">Status</label>
                            <select name="status" class="filter-select">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Admin
                                </option>
                                <option value="admin_approved"
                                    {{ request('status') == 'admin_approved' ? 'selected' : '' }}>Admin Approved</option>
                                <option value="om_approved" {{ request('status') == 'om_approved' ? 'selected' : '' }}>OM
                                    Approved</option>
                                <option value="gm_approved" {{ request('status') == 'gm_approved' ? 'selected' : '' }}>GM
                                    Approved</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected
                                </option>
                            </select>
                        </div>

                        <div class="filter-item">
                            <label class="filter-label">Date From</label>
                            <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                        </div>

                        <div class="filter-item">
                            <label class="filter-label">Date To</label>
                            <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <button type="button" class="btn-reset" id="resetFiltersBtn">
                            <i class="fas fa-redo-alt"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- PR LIST CONTAINER -->
        <div id="pr-list-container">
            @include('voucher-requests.partials.pr-cards', ['voucherRequests' => $voucherRequests])
        </div>
    </div>

    <!-- FLOATING EXPORT BUTTON (FAB) -->
    <div class="fab-export">
        <button class="fab-button" id="fabExportBtn">
            <i class="fas fa-download"></i>
        </button>
        <div class="fab-menu">
            <a href="#" onclick="exportPR('csv'); return false;">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
            <a href="#" onclick="exportPR('pdf'); return false;">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- ==================== MODAL CREATE PR ==================== -->
    <div class="modal fade" id="createPrModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--navy); color: white;">
                    <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Create Purchase Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="max-height: 65vh; overflow-y: auto; padding: 20px;">
                    <!-- Step Indicator -->
                    <div class="step-indicator"
                        style="position: sticky; top: 0; background: white; z-index: 10; padding-bottom: 10px; margin-bottom: 15px;">
                        <div class="step active" id="step1Indicator">
                            <div class="step-number">1</div>
                            <span>Select Ticket</span>
                        </div>
                        <div class="step" id="step2Indicator">
                            <div class="step-number">2</div>
                            <span>PR Details</span>
                        </div>
                    </div>

                    <div id="step1" class="modal-step active">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Ticket <span class="text-danger">*</span></label>
                            <select class="form-select select2-ticket" id="ticketSelect" style="width: 100%;">
                                <option value="">-- Search and select ticket --</option>
                                @foreach ($pendingVrTickets as $ticket)
                                    <option value="{{ $ticket->id }}" data-number="{{ $ticket->ticket_number }}"
                                        data-title="{{ $ticket->title }}">
                                        #{{ $ticket->ticket_number }} - {{ Str::limit($ticket->title, 50) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Only tickets with "PR Approval" status are shown</small>
                        </div>
                        <div class="alert alert-info" id="selectedTicketInfo" style="display: none;">
                            <i class="fas fa-info-circle me-2"></i>
                            <span id="selectedTicketText"></span>
                        </div>
                    </div>

                    <div id="step2" class="modal-step">
                        <form id="createPrForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="ticket_id" id="prTicketId">

                            <div class="mb-3">
                                <label class="form-label">PR Number</label>
                                <input type="text" class="form-control" id="prNumber" readonly
                                    style="background: #f3f4f6;">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Describe what needs to be purchased..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Photos <span class="text-danger">*</span></label>
                                <input type="file" name="photos[]" class="form-control" accept="image/*" multiple
                                    id="prPhotos">
                                <small class="text-muted">You can upload up to 5 photos (JPG, PNG). Max 5MB each.</small>
                                <div class="mt-2" id="photoPreview" style="display: flex; flex-wrap: wrap; gap: 10px;">
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" id="backToStep1">Back</button>
                                <button type="submit" class="btn"
                                    style="background: var(--orange); color: white;">Submit PR</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer" style="position: sticky; bottom: 0; background: white; z-index: 10;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn" id="continueToStep2"
                        style="background: var(--orange); color: white;">Continue</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL VIEW PR DETAILS ==================== -->
    <div class="modal fade" id="viewPrModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--navy); color: white;">
                    <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Purchase Request Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewPrContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL APPROVE ==================== -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--navy); color: white;">
                    <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Approve Purchase Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="approveVrId">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Signature Option</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="approveSignatureOption"
                                id="useSavedSignature" value="saved" checked>
                            <label class="form-check-label" for="useSavedSignature">
                                <i class="fas fa-save me-1"></i> Use my saved signature
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="approveSignatureOption"
                                id="drawNewSignature" value="new">
                            <label class="form-check-label" for="drawNewSignature">
                                <i class="fas fa-pen me-1"></i> Draw new signature
                            </label>
                        </div>
                    </div>

                    <div id="savedSignatureInfo" class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="savedSignatureText"></span>
                    </div>

                    <div id="newSignaturePad" style="display: none;">
                        <label class="form-label">Signature Pad</label>
                        <div class="signature-canvas-container">
                            <canvas id="signatureCanvas" class="modal-signature-canvas" width="300"
                                height="200"></canvas>
                        </div>
                        <div class="mt-2 text-center">
                            <button type="button" class="btn btn-sm btn-secondary" id="clearSignature">Clear</button>
                        </div>
                    </div>

                    <div class="mb-3 mt-3" id="passwordField" style="display: none;">
                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="approvePassword"
                            placeholder="Enter your password to confirm">
                        <small class="text-muted">Required to create a new signature</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn" id="confirmApprove"
                        style="background: #10b981; color: white;">Confirm Approve</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL REJECT ==================== -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--navy); color: white;">
                    <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Reject Purchase Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="rejectVrId">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectionReason" rows="3"
                            placeholder="Explain why this PR is being rejected..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmReject">Confirm Reject</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL MARK PAID ==================== -->
    <div class="modal fade" id="markPaidModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--navy); color: white;">
                    <h5 class="modal-title"><i class="fas fa-money-bill-wave me-2"></i>Mark as Paid</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="paidVrId">
                    <div class="mb-3">
                        <label class="form-label">Payment Notes (Optional)</label>
                        <textarea class="form-control" id="paymentNotes" rows="3" placeholder="Add any payment reference or notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn" id="confirmMarkPaid"
                        style="background: #047857; color: white;">Confirm Paid</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        let signaturePad = null;
        let currentUserRole = '{{ Auth::user()->role }}';

        // Initialize Select2
        $('.select2-ticket').select2({
            dropdownParent: $('#createPrModal'),
            placeholder: 'Search by ticket number or title',
            allowClear: true
        });

        // Toastr config
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 3000
        };

        // ==================== FILTER ACCORDION TOGGLE ====================
        const filterBody = $('#filterBody');
        const filterToggle = $('#filterToggle');

        filterToggle.on('click', function() {
            filterBody.toggleClass('collapsed');
            $(this).toggleClass('collapsed');
        });

        // ==================== AJAX FILTER ====================
        $('.ajax-filter-form').on('submit', function(e) {
            e.preventDefault();
            applyFilters();
        });

        $('#resetFiltersBtn').on('click', function() {
            $('#filterForm')[0].reset();
            $('#filterForm input[name="search"]').val('');
            applyFilters();
        });

        function applyFilters() {
            let formData = $('#filterForm').serialize();
            let searchData = $('#searchForm').serialize();

            let params = new URLSearchParams(formData + '&' + searchData);
            let url = '{{ route('voucher-requests.index') }}?' + params.toString();

            fetchPRs(url);
        }

        function fetchPRs(url) {
            $('#pr-list-container').addClass('pr-loading');

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'html',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    let tempDiv = $('<div>').html(response);
                    let newContent = tempDiv.find('#pr-list-container').html();

                    if (newContent) {
                        $('#pr-list-container').html(newContent);
                    } else {
                        $('#pr-list-container').html(response);
                    }

                    window.history.pushState({}, '', url);
                },
                error: function(xhr) {
                    toastr.error('Failed to load purchase requests');
                },
                complete: function() {
                    $('#pr-list-container').removeClass('pr-loading');
                }
            });
        }

        window.onpopstate = function() {
            location.reload();
        };

        // ==================== EXPORT FUNCTION ====================
        function exportPR(format) {
            let url = '{{ route('voucher-requests.export') }}?export=' + format;
            let params = new URLSearchParams(window.location.search);
            params.delete('page');
            url += '&' + params.toString();
            window.location.href = url;
        }

        // ==================== LOAD PR LIST ====================
        function loadPrList() {
            applyFilters();
        }

        // ==================== CREATE PR MODAL LOGIC ====================
        let selectedTicket = null;

        $('#createPrBtn').on('click', function() {
            selectedTicket = null;
            $('#step1').addClass('active');
            $('#step2').removeClass('active');
            $('#step1Indicator').addClass('active').removeClass('completed');
            $('#step2Indicator').removeClass('active completed');
            $('#ticketSelect').val('').trigger('change');
            $('#selectedTicketInfo').hide();
            $('#prTicketId').val('');
            $('#prNumber').val('');
            $('#prPhotos').val('');
            $('#photoPreview').empty();
            $('#createPrForm')[0].reset();
            $('#createPrModal').modal('show');

            $.ajax({
                url: '{{ route('voucher-requests.generate-number') }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#prNumber').val(response.vr_number);
                    }
                }
            });
        });

        $('#ticketSelect').on('change', function() {
            const option = $(this).find(':selected');
            if (option.val()) {
                selectedTicket = {
                    id: option.val(),
                    number: option.data('number'),
                    title: option.data('title')
                };
                $('#selectedTicketText').html(
                    `<strong>#${selectedTicket.number}</strong> - ${selectedTicket.title}`);
                $('#selectedTicketInfo').show();
            } else {
                selectedTicket = null;
                $('#selectedTicketInfo').hide();
            }
        });

        $('#continueToStep2').on('click', function() {
            if (!selectedTicket) {
                toastr.warning('Please select a ticket first');
                return;
            }

            $('#prTicketId').val(selectedTicket.id);
            $('#step1').removeClass('active');
            $('#step2').addClass('active');
            $('#step1Indicator').removeClass('active').addClass('completed');
            $('#step2Indicator').addClass('active');
        });

        $('#backToStep1').on('click', function() {
            $('#step2').removeClass('active');
            $('#step1').addClass('active');
            $('#step2Indicator').removeClass('active');
            $('#step1Indicator').removeClass('completed').addClass('active');
        });

        $('#prPhotos').on('change', function(e) {
            const files = e.target.files;
            const preview = $('#photoPreview');
            preview.empty();

            if (files.length > 5) {
                toastr.warning('Maximum 5 photos allowed');
                $(this).val('');
                return;
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (!file.type.startsWith('image/')) {
                    toastr.warning('Only image files are allowed');
                    $(this).val('');
                    preview.empty();
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    toastr.warning('Each photo must be less than 5MB');
                    $(this).val('');
                    preview.empty();
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.append(
                        `<img src="${e.target.result}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin: 5px;">`
                    );
                };
                reader.readAsDataURL(file);
            }
        });

        $('#createPrForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            $.ajax({
                url: '{{ route('voucher-requests.store') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success('Purchase request created successfully');
                        $('#createPrModal').modal('hide');
                        loadPrList();
                    } else {
                        toastr.error(response.message || 'Failed to create purchase request');
                    }
                },
                error: function(xhr) {
                    let message = 'Failed to create purchase request';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    toastr.error(message);
                }
            });
        });

        // ==================== VIEW PR DETAILS ====================
        function viewPr(vrId) {
            $.ajax({
                url: '{{ route('voucher-requests.show', ':id') }}'.replace(':id', vrId),
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#viewPrContent').html(response.html);
                        $('#viewPrModal').modal('show');
                    }
                },
                error: function() {
                    toastr.error('Failed to load purchase request details');
                }
            });
        }

        // ==================== APPROVE PR (FIXED RADIO BUTTON!) ====================
        let approveVrId = null;

        function approvePr(vrId) {
            approveVrId = vrId;

            // Reset form
            $('#approvePassword').val('');
            if (signaturePad) signaturePad.clear();

            // Reset radio button state - ENABLE DULU!
            $('#useSavedSignature').prop('disabled', false);
            $('#drawNewSignature').prop('disabled', false);

            // Cek apakah user punya saved signature
            $.ajax({
                url: '{{ route('profile.signature-info-pr') }}',
                method: 'GET',
                success: function(response) {
                    if (response.has_signature) {
                        $('#savedSignatureText').text(
                            `Using signature from ${response.signature_date || 'profile'}`
                        );
                        $('#useSavedSignature').prop('checked', true).prop('disabled', false);
                        $('#drawNewSignature').prop('disabled', false);
                        $('#newSignaturePad').hide();
                        $('#passwordField').hide();
                        $('#savedSignatureInfo').show();
                    } else {
                        // User tidak punya saved signature
                        $('#useSavedSignature').prop('checked', false).prop('disabled', true);
                        $('#drawNewSignature').prop('checked', true).prop('disabled', false);
                        $('#savedSignatureInfo').hide();
                        $('#newSignaturePad').show();
                        $('#passwordField').show();
                    }

                    // Initialize signature pad jika belum
                    if (!signaturePad) {
                        const canvas = document.getElementById('signatureCanvas');
                        canvas.width = 300;
                        canvas.height = 200;
                        signaturePad = new SignaturePad(canvas, {
                            backgroundColor: 'rgba(0,0,0,0)',
                            penColor: 'rgb(0,0,0)',
                            minWidth: 0.5,
                            maxWidth: 2.5
                        });
                    } else {
                        signaturePad.clear();
                    }

                    $('#approveModal').modal('show');
                },
                error: function() {
                    // Fallback: anggap user tidak punya signature
                    $('#useSavedSignature').prop('checked', false).prop('disabled', true);
                    $('#drawNewSignature').prop('checked', true).prop('disabled', false);
                    $('#savedSignatureInfo').hide();
                    $('#newSignaturePad').show();
                    $('#passwordField').show();

                    if (!signaturePad) {
                        const canvas = document.getElementById('signatureCanvas');
                        canvas.width = 300;
                        canvas.height = 200;
                        signaturePad = new SignaturePad(canvas, {
                            backgroundColor: 'rgba(0,0,0,0)',
                            penColor: 'rgb(0,0,0)',
                            minWidth: 0.5,
                            maxWidth: 2.5
                        });
                    }

                    $('#approveModal').modal('show');
                }
            });
        }

        // RADIO BUTTON CHANGE HANDLER - PAKAI .on('change') yang benar
        $(document).on('change', 'input[name="approveSignatureOption"]', function() {
            const isSavedSelected = $('#useSavedSignature').is(':checked') && !$('#useSavedSignature').prop(
                'disabled');

            if (isSavedSelected) {
                $('#newSignaturePad').hide();
                $('#passwordField').hide();
                $('#savedSignatureInfo').show();
            } else {
                $('#newSignaturePad').show();
                $('#passwordField').show();
                $('#savedSignatureInfo').hide();
            }
        });

        // Clear signature button
        $('#clearSignature').on('click', function() {
            if (signaturePad) signaturePad.clear();
        });

        // Confirm Approve
        $('#confirmApprove').on('click', function() {
            const useSaved = $('#useSavedSignature').is(':checked') && !$('#useSavedSignature').prop('disabled');
            const password = $('#approvePassword').val();

            if (!useSaved) {
                if (!signaturePad || signaturePad.isEmpty()) {
                    toastr.warning('Please provide a signature');
                    return;
                }
                if (!password) {
                    toastr.warning('Please enter your password to create a new signature');
                    return;
                }
            }

            let signatureData = null;
            if (!useSaved) {
                signatureData = signaturePad.toDataURL();
            }

            // Disable button
            const $btn = $('#confirmApprove');
            const originalText = $btn.html();
            $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...').prop('disabled', true);

            $.ajax({
                url: '{{ route('voucher-requests.approve', ':id') }}'.replace(':id', approveVrId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    use_saved_signature: useSaved,
                    signature_data: signatureData,
                    current_password: password
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Purchase request approved successfully');
                        $('#approveModal').modal('hide');
                        $('#approvePassword').val('');
                        if (signaturePad) signaturePad.clear();
                        loadPrList();
                    } else {
                        toastr.error(response.message || 'Failed to approve');
                    }
                },
                error: function(xhr) {
                    let message = 'Failed to approve purchase request';
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

        // ==================== REJECT PR ====================
        let rejectVrId = null;

        function rejectPr(vrId) {
            rejectVrId = vrId;
            $('#rejectionReason').val('');
            $('#rejectModal').modal('show');
        }

        $('#confirmReject').on('click', function() {
            const reason = $('#rejectionReason').val();

            if (!reason.trim()) {
                toastr.warning('Please provide a rejection reason');
                return;
            }

            // Disable button
            const $btn = $('#confirmReject');
            const originalText = $btn.html();
            $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...').prop('disabled', true);

            $.ajax({
                url: '{{ route('voucher-requests.reject', ':id') }}'.replace(':id', rejectVrId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    rejection_reason: reason
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Purchase request rejected');
                        $('#rejectModal').modal('hide');
                        loadPrList();
                    } else {
                        toastr.error(response.message || 'Failed to reject');
                    }
                },
                error: function(xhr) {
                    let message = 'Failed to reject purchase request';
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

        // ==================== MARK PAID ====================
        let paidVrId = null;

        function markAsPaid(vrId) {
            paidVrId = vrId;
            $('#paymentNotes').val('');
            $('#markPaidModal').modal('show');
        }

        $('#confirmMarkPaid').on('click', function() {
            const notes = $('#paymentNotes').val();

            $.ajax({
                url: '{{ route('voucher-requests.mark-paid', ':id') }}'.replace(':id', paidVrId),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    notes: notes
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Purchase request marked as paid');
                        $('#markPaidModal').modal('hide');
                        loadPrList();
                    } else {
                        toastr.error(response.message || 'Failed to mark as paid');
                    }
                },
                error: function() {
                    toastr.error('Failed to mark as paid');
                }
            });
        });

        // ==================== DELETE PR ====================
        function deletePr(vrId) {
            Swal.fire({
                title: 'Delete Purchase Request?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('voucher-requests.destroy', ':id') }}'.replace(':id', vrId),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Purchase request deleted');
                                loadPrList();
                            } else {
                                toastr.error(response.message || 'Failed to delete');
                            }
                        },
                        error: function() {
                            toastr.error('Failed to delete purchase request');
                        }
                    });
                }
            });
        }

        // ==================== PHOTO PREVIEW ====================
        function previewPhoto(imageSrc) {
            if ($('#imageModal').length) {
                $('#imageModal').remove();
            }

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
                if (e.target === this) {
                    closeImageModal();
                }
            });

            $(document).on('keydown.imageModal', function(e) {
                if (e.key === 'Escape') {
                    closeImageModal();
                }
            });
        }

        function closeImageModal() {
            $('#imageModal').remove();
            $(document).off('keydown.imageModal');
        }
    </script>
@endpush
