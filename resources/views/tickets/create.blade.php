@extends('layouts.main')

@section('title', 'Create New Ticket | ' . config('app.name'))

@section('page-title', 'Create New Ticket')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Tickets', 'url' => route('tickets.index')],
            ['title' => 'Create New Ticket', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        /* ============================================
                                                                                                            RESET & BASE STYLES
                                                                                                        ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: #f5f5f5;
        }

        /* ============================================
                                                                                                            MAIN CARD STYLING
                                                                                                        ============================================ */
        .form-card {
            background: white;
            border-radius: 10px;
            border: 1px solid #eaeaea;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            max-width: 250mm;
            /* Lebar A4 */
            margin-left: auto;
            margin-right: auto;
        }

        .card-body {
            padding: 20px;
        }

        /* ============================================
                                                                                                            FORM ELEMENTS - WARNA TEKS HITAM!
                                                                                                        ============================================ */
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
            display: block;
        }

        /* Semua input, select, textarea wajib hitam */
        .form-control,
        .form-select,
        textarea.form-control,
        input.form-control,
        select.form-control {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 14px;
            min-height: 42px;
            background: white;
            width: 100%;
            transition: all 0.3s ease;
            color: #000000 !important;
        }

        /* Saat focus tetap hitam */
        .form-control:focus,
        .form-select:focus,
        textarea.form-control:focus,
        input.form-control:focus,
        select.form-control:focus {
            border-color: #ff6200;
            box-shadow: 0 0 0 2px rgba(255, 98, 0, 0.1);
            outline: none;
            color: #000000 !important;
        }

        /* Placeholder tetap abu-abu */
        .form-control::placeholder,
        .form-select::placeholder,
        textarea.form-control::placeholder,
        input.form-control::placeholder {
            color: #999 !important;
            opacity: 1;
        }

        /* ============================================
                                                                                                            DESCRIPTION BOX - FIX UKURAN SEPERTI REPORT!
                                                                                                            MONOSPACE FONT - 200px HEIGHT - FIX WIDTH
                                                                                                        ============================================ */
        .description-wrapper {
            position: relative;
            width: 100%;
            max-width: 793px;
            height: 200px;
            /* FIX: Lebar box di PDF A4 (210mm = 793px - padding) */
            border: 1px solid #ff6a2a;
            border-radius: 6px;
            background: white;
            overflow: hidden;
            margin: 0 auto;
            /* Center jika lebih kecil dari container */
        }

        .description-wrapper textarea {
            width: 793px !important;
            /* FIX: Lebar persis seperti di report */
            min-width: 793px !important;
            max-width: 793px !important;
            min-height: 200px !important;
            height: 200px !important;
            max-height: 200px !important;
            padding: 8px 10px;
            /* Padding persis seperti di report */
            font-family: 'Lucida Console', Monaco, monospace !important;
            font-size: 10pt !important;
            line-height: 1.2 !important;
            resize: none !important;
            overflow: hidden !important;
            border: none;
            background: transparent;
            color: #000000 !important;
            display: block;
            margin: 0 auto;
        }

        .description-wrapper textarea:focus {
            outline: none;
            box-shadow: inset 0 0 0 2px rgba(255, 98, 0, 0.1);
        }

        /* Character counter styling */
        .char-counter {
            font-size: 11px;
            color: #999;
            margin-top: 4px;
            text-align: right;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .char-counter.warning {
            color: #ff6200;
            font-weight: 600;
        }

        .char-counter.danger {
            color: #dc3545;
            font-weight: bold;
        }

        /* Live preview box (untuk visualisasi batas) */
        .preview-box {
            background: #f8f9fa;
            border: 1px dashed #ff6200;
            border-radius: 4px;
            padding: 8px 12px;
            margin-top: 10px;
            font-size: 11px;
            max-width: 773px;
            margin-left: auto;
            margin-right: auto;
        }

        .preview-box .label {
            font-weight: 600;
            color: #ff6200;
            display: block;
            margin-bottom: 5px;
        }

        .preview-box .stats {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .preview-box .stat-item {
            flex: 1;
            min-width: 120px;
        }

        .preview-box .stat-value {
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .preview-box .stat-label {
            font-size: 10px;
            color: #666;
        }

        .overflow-indicator {
            background: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            margin-top: 5px;
        }

        /* ============================================
                                                                                                            SELECT2 CUSTOMIZATION
                                                                                                        ============================================ */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            padding-left: 12px;
            font-size: 14px;
            color: #000000 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #999 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #ff6200;
            box-shadow: 0 0 0 2px rgba(255, 98, 0, 0.1);
        }

        .select2-dropdown {
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .select2-search--dropdown {
            padding: 8px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 13px;
            color: #000000 !important;
        }

        .select2-results__option {
            padding: 8px 12px;
            font-size: 14px;
            color: #000000 !important;
        }

        .select2-results__option--highlighted[aria-selected] {
            background-color: #ff6200;
            color: white !important;
        }

        /* ============================================
                                                                                                            SECTION HEADERS
                                                                                                        ============================================ */
        .section-header {
            padding: 12px 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
            font-weight: 600;
            margin: 20px -20px 15px -20px;
            font-size: 14px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-header i {
            color: #ff6200;
            font-size: 14px;
        }

        /* ============================================
                                                                                                            LOCATION SELECTION
                                                                                                        ============================================ */
        .location-type-group {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .location-type-option {
            flex: 1;
            min-width: 200px;
        }

        .location-type-option input[type="radio"] {
            display: none;
        }

        .location-type-label {
            display: block;
            padding: 12px 15px;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            color: #000000 !important;
        }

        .location-type-label:hover {
            border-color: #ff6200;
            background: #fff3e0;
        }

        .location-type-label.active {
            border-color: #ff6200;
            background: #fff3e0;
            box-shadow: 0 0 0 3px rgba(255, 98, 0, 0.1);
        }

        .location-type-label i {
            font-size: 20px;
            color: #ff6200;
            margin-bottom: 8px;
            display: block;
        }

        .location-type-label strong {
            display: block;
            font-size: 14px;
            margin-bottom: 4px;
            color: #000000 !important;
        }

        .location-type-label small {
            font-size: 12px;
            color: #666;
            line-height: 1.4;
        }

        .location-selection-area {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .location-selection-area.active {
            display: block;
        }

        .manual-location-input {
            border: 2px solid #e0e0e0 !important;
            background: #f8f9fa !important;
            font-weight: 500 !important;
            color: #000000 !important;
        }

        .manual-location-input:focus {
            border-color: #ff6200 !important;
            background: white !important;
            color: #000000 !important;
        }

        /* ============================================
                                                                                                            SIGNATURE SECTION
                                                                                                        ============================================ */
        .signature-section {
            background: #f9f9f9;
            border: 1px solid #eaeaea;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .signature-info {
            background: #e8f4fd;
            border: 1px solid #b6d7f2;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #2c5282;
        }

        .signature-info i {
            color: #3182ce;
            margin-right: 8px;
        }

        .signature-btn {
            background: linear-gradient(135deg, #ff7b00, #ff6200);
            border: none;
            border-radius: 6px;
            padding: 12px 24px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .signature-btn:hover {
            background: linear-gradient(135deg, #ff6200, #ff7b00);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 98, 0, 0.2);
        }

        .signature-preview {
            max-width: 300px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-top: 10px;
            display: none;
            background: transparent;
        }

        .signature-preview.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        /* Signature Canvas - 300x200 */
        .signature-canvas-container {
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
            background: transparent;
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 10px;
            position: relative;
        }

        .signature-canvas-wrapper {
            position: relative;
            width: 100%;
            background: transparent;
            display: flex;
            justify-content: center;
        }

        .signature-canvas-wrapper canvas {
            max-width: 300px;
            width: 100%;
            height: auto;
            background: transparent !important;
            cursor: crosshair;
            touch-action: none;
            border: 1px solid #eee;
        }

        .signature-dimension-info {
            text-align: center;
            font-size: 11px;
            color: #999;
            margin-top: 8px;
        }

        /* Signature instructions */
        .signature-instructions {
            background: #f8f9fa;
            border-left: 4px solid #ff6200;
            padding: 12px 15px;
            border-radius: 6px;
            font-size: 13px;
        }

        /* Manager Signature Card */
        .manager-signature-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .manager-signature-card.has-signature {
            border-color: #28a745;
            background: #f0fff0;
        }

        .manager-signature-preview {
            max-width: 200px;
            max-height: 60px;
            object-fit: contain;
            background: transparent;
        }

        .manager-signature-info {
            flex: 1;
        }

        .manager-signature-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .manager-signature-date {
            font-size: 11px;
            color: #666;
        }

        .manager-signature-badge {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .manager-signature-note {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 6px;
            padding: 10px 15px;
            margin-top: 10px;
            font-size: 13px;
            color: #856404;
        }

        /* ============================================
                                                                                                            BUTTONS
                                                                                                        ============================================ */
        .btn-submit-ticket {
            background: linear-gradient(135deg, #ff7b00, #ff6200);
            border: none;
            padding: 12px 28px;
            font-size: 15px;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            cursor: pointer;
        }

        .btn-submit-ticket:hover:not(:disabled) {
            background: linear-gradient(135deg, #ff6200, #ff7b00);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 98, 0, 0.2);
        }

        .btn-submit-ticket:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-cancel {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 11px 26px;
            font-size: 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #666;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
            border-color: #ccc;
            color: #333;
            text-decoration: none;
        }

        /* ============================================
                                                                                                            FILE UPLOAD
                                                                                                        ============================================ */
        .file-upload-area {
            border: 2px dashed #ddd;
            border-radius: 6px;
            padding: 25px;
            text-align: center;
            background: #f9f9f9;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #ff6200;
            background: #fff3e0;
        }

        .file-upload-area i {
            font-size: 36px;
            color: #ccc;
            margin-bottom: 12px;
            display: block;
        }

        .file-upload-area h5 {
            font-size: 15px;
            color: #333;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .file-upload-area p {
            font-size: 12px;
            color: #999;
            margin: 0;
        }

        .file-list {
            margin-top: 15px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .file-item {
            background: white;
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .file-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .file-preview {
            height: 120px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }

        .file-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .file-preview .file-icon {
            font-size: 48px;
            color: #666;
        }

        .file-info {
            padding: 12px;
        }

        .file-name {
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #000000 !important;
        }

        .file-size {
            font-size: 11px;
            color: #999;
            margin-bottom: 8px;
        }

        .file-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .file-type-badge {
            background: #f0f0f0;
            color: #666;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 500;
        }

        .remove-file {
            color: #dc3545;
            cursor: pointer;
            font-size: 14px;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .remove-file:hover {
            background: #f8d7da;
        }

        /* ============================================
                                                                                                            REQUIRED FIELD
                                                                                                        ============================================ */
        .required-mark {
            color: #dc3545;
            margin-left: 2px;
        }

        .form-text {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
            display: block;
        }

        /* ============================================
                                                                                                            VALIDATION
                                                                                                        ============================================ */
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            font-size: 12px;
            color: #dc3545;
            margin-top: 4px;
            display: none;
        }

        .is-invalid~.invalid-feedback {
            display: block;
        }

        /* ============================================
                                                                                                            LOADING STATE
                                                                                                        ============================================ */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading:after {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            width: 18px;
            height: 18px;
            margin: -9px 0 0 -9px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============================================
                                                                                                            MOBILE RESPONSIVE (HANYA UNTUK LAYAR KECIL)
                                                                                                        ============================================ */
        @media (max-width: 820px) {
            .form-card {
                max-width: 100%;
                margin: 0 10px;
            }

            .description-wrapper {
                overflow-x: auto;
                /* Scroll horizontal jika layar terlalu kecil */
            }

            .description-wrapper textarea {
                width: 773px !important;
                min-width: 773px !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="form-card">
                <div class="card-body">
                    <form id="createTicketForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Ticket Information -->
                        <div class="section-header">
                            <i class="fas fa-clipboard-list"></i>
                            Ticket Information
                        </div>

                        <div class="row">
                            <!-- Title -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    Ticket Title / Subject <span class="required-mark">*</span>
                                </label>
                                <input type="text" class="form-control" name="title" id="title"
                                    placeholder="e.g., AC Room 502 tidak dingin" required maxlength="100">
                                <div class="char-counter" id="titleCounter">0/100</div>
                                <div class="invalid-feedback"></div>
                                <small class="form-text">Brief description of the issue</small>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Category <span class="required-mark">*</span>
                                </label>
                                <select class="form-control select2" name="category_id" id="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                                @if ($categories->isEmpty())
                                    <small class="text-danger">No active categories available</small>
                                @endif
                            </div>

                            <!-- Priority -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Priority Level <span class="required-mark">*</span>
                                </label>
                                <select class="form-control select2" name="priority_id" id="priority_id" required>
                                    <option value="">Select Priority</option>
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                                @if ($priorities->isEmpty())
                                    <small class="text-danger">No active priorities available</small>
                                @endif
                            </div>

                            <!-- Department - readonly -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    Department
                                </label>
                                <div class="d-flex align-items-center">
                                    <input type="text" class="form-control bg-light"
                                        value="{{ auth()->user()->department->name ?? 'N/A' }}" readonly>
                                    <span class="ms-3" style="color: #666; font-size: 13px;">
                                        <i class="fas fa-info-circle"></i> Your department (cannot be changed)
                                    </span>
                                </div>
                                <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
                            </div>

                            <!-- Location Selection -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    Location <span class="required-mark">*</span>
                                </label>

                                <!-- Location Type Selection -->
                                <div class="location-type-group">
                                    <div class="location-type-option">
                                        <input type="radio" name="location_type" value="predefined"
                                            id="location_predefined" class="location-type-radio" checked>
                                        <label for="location_predefined" class="location-type-label active">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <strong>Select from List</strong>
                                            <small>Choose from registered rooms or floors</small>
                                        </label>
                                    </div>

                                    <div class="location-type-option">
                                        <input type="radio" name="location_type" value="manual" id="location_manual"
                                            class="location-type-radio">
                                        <label for="location_manual" class="location-type-label">
                                            <i class="fas fa-keyboard"></i>
                                            <strong>Enter Manual</strong>
                                            <small>If location is not in the list</small>
                                        </label>
                                    </div>
                                </div>

                                <!-- Predefined Location Select -->
                                <div class="location-selection-area active" id="predefinedLocationSection">
                                    <select class="form-control select2" id="location_id" name="location_id">
                                        <option value="">Select Location</option>
                                        @foreach ($locations->sortBy('name') as $location)
                                            <option value="{{ $location->id }}">
                                                {{ $location->name }}
                                                @if ($location->floor_number)
                                                    (Floor {{ $location->floor_number }})
                                                @endif
                                                - {{ ucfirst($location->hotel) }}
                                                ({{ ucfirst($location->location_type) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                    @if ($locations->isEmpty())
                                        <small class="text-danger">No active locations available</small>
                                    @endif
                                </div>

                                <!-- Manual Location Input -->
                                <div class="location-selection-area" id="manualLocationSection">
                                    <input type="text" class="form-control manual-location-input"
                                        id="location_manual_input" name="location_manual"
                                        placeholder="Enter location manually (e.g., Room 305, Lobby Area, etc.)"
                                        maxlength="255">
                                    <div class="char-counter" id="locationCounter">0/255</div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Due Date (Optional)
                                </label>
                                <input type="datetime-local" class="form-control" name="due_date" id="due_date">
                                <div class="invalid-feedback"></div>
                                <small class="form-text">Expected completion date</small>
                            </div>
                        </div>

                        <!-- Description dengan TEXTAREA - FIX 773px WIDTH, 200px HEIGHT, MONOSPACE FONT -->
                        <div class="section-header">
                            <i class="fas fa-align-left"></i>
                            Detailed Description
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    Description <span class="required-mark">*</span>
                                </label>

                                <div class="description-wrapper">
                                    <textarea class="form-control" name="description" id="description" rows="8"
                                        placeholder="Describe the issue in detail... (Monospace font - exactly like PDF report - 773px width × 200px height)"
                                        required></textarea>
                                </div>

                                <!-- Live Preview Box - Menampilkan statistik realtime -->
                                <div class="preview-box" id="previewBox">
                                    <span class="label"><i class="fas fa-ruler"></i> LIVE PREVIEW (EXACT PDF
                                        DIMENSIONS)</span>
                                    <div class="stats">
                                        <div class="stat-item">
                                            <span class="stat-value" id="charCount">0</span>
                                            <span class="stat-label">Characters</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-value" id="lineCount">0</span>
                                            <span class="stat-label">Lines</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-value">200px</span>
                                            <span class="stat-label">Box Height</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-value">773px</span>
                                            <span class="stat-label">Box Width</span>
                                        </div>
                                    </div>
                                    <div id="overflowWarning" style="display: none;" class="overflow-indicator">
                                        <i class="fas fa-exclamation-triangle"></i> TEXT OVERFLOW! Melebihi tinggi box
                                        200px
                                    </div>
                                </div>

                                <div class="invalid-feedback"></div>
                                <small class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Dimensions: 773px width × 200px height | Font: Courier New 10pt (monospace) |
                                        Line-height: 1.2</strong> - EXACTLY like PDF report.
                                    All characters have same width for accurate calculation. Text auto-truncated if exceeds
                                    200px.
                                </small>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="section-header">
                            <i class="fas fa-paperclip"></i>
                            Attachments (Optional)
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="file-upload-area" onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h5>Click to upload files</h5>
                                    <p>Supported: JPG, PNG, PDF, DOC, DOCX (Max 5MB per file)</p>
                                </div>
                                <input type="file" id="fileInput" name="attachments[]" multiple
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" style="display: none;">
                                <div class="file-list" id="fileList"></div>
                            </div>
                        </div>

                        <!-- Signature Section -->
                        <div class="signature-section">
                            <div class="signature-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Signature Required:</strong> Your signature is required as the reporter of this
                                ticket. Canvas size: 300 x 200 pixels.
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">
                                        Your Signature (As Reporter) <span class="required-mark">*</span>
                                    </label>

                                    <button type="button" class="signature-btn" id="openSignatureModal">
                                        <i class="fas fa-signature"></i>
                                        Click to Sign (300x200)
                                    </button>

                                    <input type="hidden" name="signature_data" id="signatureData">

                                    <div class="mt-3">
                                        <img id="signaturePreview" class="signature-preview"
                                            style="background: transparent;">
                                        <small id="signatureStatus" class="form-text">
                                            <i class="fas fa-times-circle text-danger"></i> No signature provided yet
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Manager Signature Section -->
                            @if (auth()->user()->role === 'manager')
                                <div class="manager-signature-section mt-4">
                                    <div class="signature-info" style="background: #fff3e0; border-color: #ffb366;">
                                        <i class="fas fa-shield-alt"></i>
                                        <strong>Manager Signature:</strong> You can use your saved signature.
                                    </div>

                                    <div id="managerSignatureContainer">
                                        <div class="text-center p-3">
                                            <i class="fas fa-spinner fa-spin"></i> Loading signature data...
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex flex-wrap gap-3">
                                    <button type="submit" class="btn-submit-ticket" id="submitBtn">
                                        <i class="fas fa-paper-plane me-2"></i> Submit Ticket
                                    </button>
                                    <a href="{{ route('tickets.index') }}" class="btn-cancel">
                                        <i class="fas fa-times me-2"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Signature Modal -->
    <div class="modal fade" id="signatureModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-signature text-primary"></i>
                        Your Signature (Reporter) - 300 x 200
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="signature-instructions mb-3">
                        <p class="mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Sign in the box below. Canvas size: 300 x 200 pixels. Background will be transparent.
                        </p>
                    </div>

                    <div class="signature-canvas-container">
                        <div class="signature-canvas-wrapper">
                            <canvas id="signatureCanvas"></canvas>
                        </div>
                        <div class="signature-dimension-info">
                            <i class="fas fa-expand-alt"></i> Canvas size: 300 x 200 pixels
                        </div>
                    </div>

                    <div class="signature-actions d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-light flex-fill" id="clearSignature">
                            <i class="fas fa-eraser me-1"></i> Clear
                        </button>
                        <button type="button" class="btn btn-light flex-fill" id="undoSignature">
                            <i class="fas fa-undo me-1"></i> Undo
                        </button>
                        <button type="button" class="btn btn-primary flex-fill" id="saveSignature">
                            <i class="fas fa-check me-1"></i> Save
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        $(document).ready(function() {
            // ==================== SELECT2 ====================
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select an option',
                allowClear: true
            });

            // ==================== TOASTR ====================
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "4000",
                "extendedTimeOut": "1000",
                "preventDuplicates": true, // Mencegah toastr duplikat
                "newestOnTop": true,
                "progressBar": true,
                "showDuration": "300",
                "hideDuration": "1000"
            };

            // ==================== CHARACTER COUNTERS ====================
            const MAX_TITLE = 100;
            const MAX_LOCATION = 255;

            // Title counter
            $('#title').on('input', function() {
                const len = $(this).val().length;
                $('#titleCounter').text(len + '/' + MAX_TITLE);

                if (len >= MAX_TITLE) {
                    $('#titleCounter').addClass('danger');
                } else if (len >= MAX_TITLE * 0.8) {
                    $('#titleCounter').addClass('warning').removeClass('danger');
                } else {
                    $('#titleCounter').removeClass('warning danger');
                }
            });

            // Location manual counter
            $('#location_manual_input').on('input', function() {
                const len = $(this).val().length;
                $('#locationCounter').text(len + '/' + MAX_LOCATION);

                if (len >= MAX_LOCATION) {
                    $('#locationCounter').addClass('danger');
                } else if (len >= MAX_LOCATION * 0.8) {
                    $('#locationCounter').addClass('warning').removeClass('danger');
                } else {
                    $('#locationCounter').removeClass('warning danger');
                }
            });

            // ==================== DESCRIPTION - FIX 200px HEIGHT, FIX 773px WIDTH (MONOSPACE) ====================
            const description = document.getElementById('description');
            const charCountEl = document.getElementById('charCount');
            const lineCountEl = document.getElementById('lineCount');
            const overflowWarning = document.getElementById('overflowWarning');

            // Constants untuk pengukuran - MONOSPACE - FIX
            const BOX_HEIGHT = 200; // px
            const BOX_WIDTH = 773; // px - FIX! Lebar box di PDF
            const LINE_HEIGHT_PX = 16; // 13.33px * 1.2 ≈ 16px
            const MAX_LINES = Math.floor(BOX_HEIGHT / LINE_HEIGHT_PX); // 12 baris (200/16 = 12.5, floor = 12)

            // Dengan monospace, semua karakter lebarnya SAMA PERSIS
            const CHAR_WIDTH_MONOSPACE = 8; // pixels per character di font monospace 10pt

            // Hitung karakter per baris dengan lebar FIX
            const CHARS_PER_LINE = Math.floor(BOX_WIDTH / CHAR_WIDTH_MONOSPACE); // 773 / 8 = 96 karakter per baris

            // Variable untuk debounce
            let previewTimeout;
            let lastTruncateToastTime = 0;
            const TOAST_THROTTLE_TIME = 3000; // 3 detik jeda antar toast truncate

            // Function untuk menghitung jumlah baris dengan AKURAT (monospace)
            function countLines(text) {
                if (!text) return 0;

                // Split berdasarkan newline
                const lines = text.split('\n');
                let totalLines = 0;

                for (let i = 0; i < lines.length; i++) {
                    const line = lines[i];

                    // Hitung berapa baris yang diperlukan untuk line ini (wrap)
                    if (line.length > 0) {
                        // Math.ceil untuk pembulatan ke atas karena jika sisa karakter < CHARS_PER_LINE, tetap butuh 1 baris
                        const linesNeeded = Math.ceil(line.length / CHARS_PER_LINE);
                        totalLines += linesNeeded;
                    } else {
                        // Baris kosong (hasil dari enter) tetap dihitung 1 baris
                        totalLines += 1;
                    }
                }

                return totalLines;
            }

            // Function untuk memotong teks agar muat (monospace)
            function enforceHeightLimit(text) {
                if (!text) return '';

                // Split lines
                const lines = text.split('\n');
                let resultLines = [];
                let totalLinesUsed = 0;

                for (let i = 0; i < lines.length; i++) {
                    const line = lines[i];

                    // Hitung baris yang diperlukan untuk line ini
                    let linesNeeded = 0;
                    if (line.length > 0) {
                        linesNeeded = Math.ceil(line.length / CHARS_PER_LINE);
                    } else {
                        // Baris kosong tetap dihitung 1 baris
                        linesNeeded = 1;
                    }

                    // Cek apakah masih muat
                    if (totalLinesUsed + linesNeeded <= MAX_LINES) {
                        // Masih muat, ambil semua
                        resultLines.push(line);
                        totalLinesUsed += linesNeeded;
                    } else {
                        // Tidak muat, potong
                        const remainingLines = MAX_LINES - totalLinesUsed;
                        if (remainingLines > 0) {
                            // Untuk baris terakhir yang dipotong
                            const maxChars = remainingLines * CHARS_PER_LINE;
                            resultLines.push(line.substring(0, maxChars));
                        }
                        break;
                    }
                }

                return resultLines.join('\n');
            }

            // Update preview dengan debounce
            function updatePreview() {
                // Clear timeout sebelumnya
                if (previewTimeout) {
                    clearTimeout(previewTimeout);
                }

                // Set timeout baru untuk debounce
                previewTimeout = setTimeout(function() {
                    const text = description.value;
                    const lines = countLines(text);
                    const chars = text.length;

                    // Update display
                    charCountEl.textContent = chars;
                    lineCountEl.textContent = lines;

                    // Cek overflow
                    if (lines > MAX_LINES) {
                        overflowWarning.style.display = 'block';

                        // Potong teks otomatis
                        const limitedText = enforceHeightLimit(text);
                        if (limitedText !== text) {
                            description.value = limitedText;

                            // Update setelah dipotong
                            const newLines = countLines(limitedText);
                            const newChars = limitedText.length;
                            charCountEl.textContent = newChars;
                            lineCountEl.textContent = newLines;

                            // Tampilkan warning dengan throttle (jeda)
                            const now = Date.now();
                            if (now - lastTruncateToastTime > TOAST_THROTTLE_TIME) {
                                toastr.warning(
                                    `Text truncated to fit 200px height (max ${MAX_LINES} lines, ${CHARS_PER_LINE} chars per line)`
                                );
                                lastTruncateToastTime = now;
                            }

                            // Sembunyikan warning karena sudah dipotong
                            overflowWarning.style.display = 'none';
                        }
                    } else {
                        overflowWarning.style.display = 'none';
                    }
                }, 300); // Debounce 300ms
            }

            // Event listeners dengan passive flag untuk performance
            description.addEventListener('input', updatePreview, {
                passive: true
            });
            description.addEventListener('paste', function(e) {
                // Biarkan paste dulu, lalu proses dengan debounce
                setTimeout(updatePreview, 50);
            }, {
                passive: true
            });
            description.addEventListener('cut', function() {
                setTimeout(updatePreview, 50);
            }, {
                passive: true
            });

            // Initial update
            updatePreview();

            // ==================== DUE DATE ====================
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById(
                'due_date').min = now.toISOString().slice(0, 16);

            // ==================== LOCATION TYPE TOGGLE ====================
            $('.location-type-label').on('click', function() {
                const radioId = $(this).attr('for');
                $('#' + radioId).prop('checked', true);

                $('.location-type-label').removeClass('active');
                $(this).addClass('active');

                if (radioId === 'location_predefined') {
                    $('#predefinedLocationSection').addClass('active');
                    $('#manualLocationSection').removeClass('active');
                    $('#location_id').prop('required', true);
                    $('#location_manual_input').prop('required', false).val('');
                    $('#locationCounter').text('0/255');
                } else {
                    $('#predefinedLocationSection').removeClass('active');
                    $('#manualLocationSection').addClass('active');
                    $('#location_manual_input').prop('required', true);
                    $('#location_id').prop('required', false).val(null).trigger('change');
                }
            });

            // ==================== SIGNATURE PAD ====================
            let signaturePad = null;
            let undoStack = [];
            const canvas = document.getElementById('signatureCanvas');

            function initSignaturePad() {
                canvas.width = 300;
                canvas.height = 200;
                canvas.style.width = '100%';
                canvas.style.height = 'auto';
                canvas.style.maxWidth = '300px';
                canvas.style.backgroundColor = 'transparent';

                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // Draw border
                ctx.strokeStyle = '#ccc';
                ctx.lineWidth = 1;
                ctx.setLineDash([5, 5]);
                ctx.strokeRect(2, 2, canvas.width - 4, canvas.height - 4);
                ctx.setLineDash([]);

                // Add signature line
                ctx.beginPath();
                ctx.strokeStyle = '#999';
                ctx.lineWidth = 1;
                ctx.moveTo(20, canvas.height - 40);
                ctx.lineTo(canvas.width - 20, canvas.height - 40);
                ctx.stroke();

                if (signaturePad) {
                    signaturePad.clear();
                    undoStack = [];
                }
            }

            $('#signatureModal').on('shown.bs.modal', function() {
                if (!signaturePad) {
                    initSignaturePad();

                    signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'transparent',
                        penColor: '#000000',
                        minWidth: 0.5,
                        maxWidth: 2.5,
                        throttle: 16
                    });

                    signaturePad.addEventListener('beginStroke', () => {
                        undoStack.push(signaturePad.toData());
                    });
                } else {
                    initSignaturePad();
                    undoStack = [];
                }
            });

            $('#openSignatureModal').on('click', function() {
                $('#signatureModal').modal('show');
            });

            $('#clearSignature').on('click', function() {
                if (signaturePad) {
                    signaturePad.clear();
                    undoStack = [];

                    const ctx = canvas.getContext('2d');
                    ctx.strokeStyle = '#ccc';
                    ctx.lineWidth = 1;
                    ctx.setLineDash([5, 5]);
                    ctx.strokeRect(2, 2, canvas.width - 4, canvas.height - 4);
                    ctx.setLineDash([]);

                    ctx.beginPath();
                    ctx.strokeStyle = '#999';
                    ctx.lineWidth = 1;
                    ctx.moveTo(20, canvas.height - 40);
                    ctx.lineTo(canvas.width - 20, canvas.height - 40);
                    ctx.stroke();
                }
            });

            $('#undoSignature').on('click', function() {
                if (signaturePad && undoStack.length > 0) {
                    const previousState = undoStack.pop();
                    signaturePad.fromData(previousState);
                } else if (signaturePad) {
                    signaturePad.clear();
                    const ctx = canvas.getContext('2d');
                    ctx.strokeStyle = '#ccc';
                    ctx.lineWidth = 1;
                    ctx.setLineDash([5, 5]);
                    ctx.strokeRect(2, 2, canvas.width - 4, canvas.height - 4);
                    ctx.setLineDash([]);

                    ctx.beginPath();
                    ctx.strokeStyle = '#999';
                    ctx.lineWidth = 1;
                    ctx.moveTo(20, canvas.height - 40);
                    ctx.lineTo(canvas.width - 20, canvas.height - 40);
                    ctx.stroke();
                }
            });

            $('#saveSignature').on('click', function() {
                if (!signaturePad || signaturePad.isEmpty()) {
                    toastr.error('Please provide a signature');
                    return;
                }

                const signatureData = signaturePad.toDataURL('image/png');
                $('#signatureData').val(signatureData);

                $('#signaturePreview').attr('src', signatureData).addClass('show');
                $('#signatureStatus').html(
                    '<i class="fas fa-check-circle text-success"></i> Signature saved (300x200)'
                );

                $('#signatureModal').modal('hide');
                toastr.success('Signature saved successfully');
            });

            // ==================== MANAGER SIGNATURE ====================
            @if (auth()->user()->role === 'manager')
                $.ajax({
                    url: "{{ route('tickets.manager-signature') }}",
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.has_signature) {
                            const html = `
                            <div class="manager-signature-card has-signature">
                                <img src="${response.signature_url}" class="manager-signature-preview" style="background: transparent;">
                                <div class="manager-signature-info">
                                    <div class="manager-signature-name">${response.signature_name}</div>
                                    <div class="manager-signature-date">Updated: ${response.signature_date || 'N/A'}</div>
                                    <span class="manager-signature-badge mt-2">
                                        <i class="fas fa-check-circle"></i> Available
                                    </span>
                                </div>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="useManagerSignature">
                                <label class="form-check-label" for="useManagerSignature">
                                    Use my saved signature for this ticket
                                </label>
                            </div>
                            <input type="hidden" name="manager_signature_used" id="managerSignatureUsed" value="0">
                        `;
                            $('#managerSignatureContainer').html(html);

                            $('#useManagerSignature').on('change', function() {
                                $('#managerSignatureUsed').val($(this).is(':checked') ? '1' :
                                    '0');
                                if ($(this).is(':checked')) {
                                    if (signaturePad) signaturePad.clear();
                                    $('#signatureData').val('');
                                    $('#signaturePreview').removeClass('show');
                                    $('#signatureStatus').html(
                                        '<i class="fas fa-info-circle text-info"></i> Using manager signature'
                                    );
                                } else {
                                    $('#signatureStatus').html(
                                        '<i class="fas fa-times-circle text-danger"></i> No signature provided yet'
                                    );
                                }
                            });
                        } else {
                            $('#managerSignatureContainer').html(`
                            <div class="manager-signature-note">
                                <i class="fas fa-exclamation-triangle"></i>
                                No signature uploaded.
                                <a href="{{ route('profile.index') }}" class="btn btn-sm btn-warning ms-2">
                                    <i class="fas fa-upload"></i> Upload
                                </a>
                            </div>
                        `);
                        }
                    }
                });
            @endif

            // ==================== FILE UPLOAD ====================
            let selectedFiles = [];

            $('#fileInput').on('change', function(e) {
                Array.from(e.target.files).forEach(file => {
                    if (file.size > 5 * 1024 * 1024) {
                        toastr.error(`File ${file.name} exceeds 5MB`);
                        return;
                    }
                    selectedFiles.push(file);
                    addFileToList(file);
                });
                this.value = '';
            });

            function addFileToList(file) {
                const fileId = 'file_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                const ext = file.name.split('.').pop().toLowerCase();
                const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(ext);

                const item = $(`
                    <div class="file-item" id="${fileId}" data-filename="${file.name}">
                        <div class="file-preview">
                            ${isImage ? '<div class="file-preview-content"></div>' : '<i class="fas fa-file file-icon"></i>'}
                        </div>
                        <div class="file-info">
                            <div class="file-name">${file.name}</div>
                            <div class="file-size">${formatBytes(file.size)}</div>
                            <div class="file-actions">
                                <span class="file-type-badge">${ext.toUpperCase()}</span>
                                <i class="fas fa-times remove-file"></i>
                            </div>
                        </div>
                    </div>
                `);

                if (isImage) {
                    const reader = new FileReader();
                    reader.onload = e => item.find('.file-preview-content').html(
                        `<img src="${e.target.result}" style="max-width:100%; max-height:100%; object-fit:contain;">`
                    );
                    reader.readAsDataURL(file);
                }

                item.find('.remove-file').on('click', function() {
                    selectedFiles = selectedFiles.filter(f => f.name !== file.name);
                    item.remove();
                });

                $('#fileList').append(item);
            }

            function formatBytes(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }

            // ==================== FORM SUBMIT ====================
            // Variable untuk mencegah double submit
            let isSubmitting = false;

            $('#createTicketForm').on('submit', function(e) {
                e.preventDefault();

                // Cegah double submit
                if (isSubmitting) {
                    return;
                }

                let isValid = true;
                $('.is-invalid').removeClass('is-invalid');

                // Title validation
                if (!$('#title').val().trim()) {
                    $('#title').addClass('is-invalid').next('.invalid-feedback').text('Title is required');
                    isValid = false;
                }

                // Category validation
                if (!$('select[name="category_id"]').val()) {
                    $('select[name="category_id"]').addClass('is-invalid').next('.invalid-feedback').text(
                        'Category is required');
                    isValid = false;
                }

                // Priority validation
                if (!$('select[name="priority_id"]').val()) {
                    $('select[name="priority_id"]').addClass('is-invalid').next('.invalid-feedback').text(
                        'Priority is required');
                    isValid = false;
                }

                // Location validation
                const locType = $('input[name="location_type"]:checked').val();
                if (locType === 'predefined' && !$('#location_id').val()) {
                    $('#location_id').addClass('is-invalid').next('.invalid-feedback').text(
                        'Please select a location');
                    isValid = false;
                } else if (locType === 'manual' && !$('#location_manual_input').val().trim()) {
                    $('#location_manual_input').addClass('is-invalid').next('.invalid-feedback').text(
                        'Please enter a location');
                    isValid = false;
                }

                // Description validation
                const descText = $('#description').val();

                // Cek apakah description kosong atau hanya whitespace
                if (!descText || descText.trim() === '') {
                    toastr.error('Description is required');
                    $('#description').addClass('is-invalid');
                    $('#description').nextAll('.invalid-feedback').text('Description cannot be empty');
                    isValid = false;
                    $('html, body').animate({
                        scrollTop: $('#description').offset().top - 100
                    }, 500);
                } else {
                    $('#description').removeClass('is-invalid');
                }

                // Signature validation
                const useManager = $('#managerSignatureUsed')?.val() === '1';
                if (!useManager && !$('#signatureData').val()) {
                    toastr.error('Please provide your signature');
                    isValid = false;
                }

                // Minimum length check (10 karakter) - hanya sekali, pakai toast yang sama
                if (descText && descText.trim().length < 10) {
                    // Gunakan toastr dengan ID unik untuk mencegah duplikasi
                    toastr.options = {
                        ...toastr.options,
                        "preventDuplicates": true
                    };
                    toastr.warning('Description should be at least 10 characters');
                }

                if (!isValid) return;

                // Set flag submitting
                isSubmitting = true;

                const formData = new FormData(this);

                // Add attachments
                selectedFiles.forEach((f, i) => {
                    formData.append(`attachments[${i}]`, f);
                });

                // Handle manager signature
                if (useManager) {
                    formData.set('signature_data', '');
                    formData.set('use_manager_signature', '1');
                }

                const $btn = $('#submitBtn');
                $btn.prop('disabled', true).addClass('btn-loading').html('');

                $.ajax({
                    url: "{{ route('tickets.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                html: `Ticket <strong>#${res.ticket_number}</strong> created successfully`,
                                confirmButtonText: 'View Ticket'
                            }).then(() => {
                                window.location.href =
                                    "{{ route('tickets.index') }}?my_tickets=1";
                            });
                        }
                    },
                    error: function(xhr) {
                        // Reset flag submitting
                        isSubmitting = false;

                        $btn.prop('disabled', false).removeClass('btn-loading').html(
                            '<i class="fas fa-paper-plane me-2"></i> Submit Ticket');

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            if (errors) {
                                Object.keys(errors).forEach(key => {
                                    const el = $(`[name="${key}"]`);
                                    if (el.length) {
                                        el.addClass('is-invalid');
                                        el.next('.invalid-feedback').text(errors[key][
                                            0
                                        ]);
                                    }
                                });
                            }
                            toastr.error(xhr.responseJSON.message || 'Please check the form');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message ||
                                    'Something went wrong',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });

            // ==================== PREVENT ACCIDENTAL NAVIGATION ====================
            let formChanged = false;
            let changeTimeout;

            $('#createTicketForm input, #createTicketForm select, #createTicketForm textarea')
                .on('change input', function() {
                    // Debounce untuk mencegah terlalu sering update
                    if (changeTimeout) {
                        clearTimeout(changeTimeout);
                    }
                    changeTimeout = setTimeout(function() {
                        formChanged = true;
                    }, 1000); // Setelah 1 detik tidak ada perubahan baru dianggap form berubah
                });

            $(window).on('beforeunload', function() {
                if (formChanged) {
                    return 'You have unsaved changes. Are you sure you want to leave?';
                }
            });

            $('#createTicketForm').on('submit', function() {
                $(window).off('beforeunload');
                formChanged = false;
            });
        });
    </script>
@endpush
