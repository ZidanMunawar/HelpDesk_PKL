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
            box-sizing: border-box;
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
        }

        .card-body {
            padding: 20px;
        }

        /* ============================================
                           FORM ELEMENTS
                        ============================================ */
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
            display: block;
        }

        .form-control,
        .form-select {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 14px;
            min-height: 42px;
            background: white;
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #ff6200;
            box-shadow: 0 0 0 2px rgba(255, 98, 0, 0.1);
            outline: none;
        }

        /* ============================================
                           SELECT2 CUSTOMIZATION - FIXED
                        ============================================ */
        .select2-container {
            width: 100% !important;
            display: block;
        }

        .select2-container .select2-selection--single {
            height: 42px !important;
            border: 1px solid #ddd !important;
            border-radius: 6px !important;
            background: white !important;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 40px !important;
            padding-left: 12px !important;
            padding-right: 30px !important;
            font-size: 14px !important;
            color: #333 !important;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
            width: 20px !important;
        }

        .select2-container.select2-container--focus .select2-selection--single,
        .select2-container.select2-container--open .select2-selection--single {
            border-color: #ff6200 !important;
            box-shadow: 0 0 0 2px rgba(255, 98, 0, 0.1) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #666 transparent transparent transparent !important;
            border-width: 6px 6px 0 6px !important;
            margin-top: -3px !important;
        }

        .select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #666 transparent !important;
            border-width: 0 6px 6px 6px !important;
            margin-top: -3px !important;
        }

        /* ============================================
                           SELECT2 DROPDOWN STYLING
                        ============================================ */
        .select2-dropdown {
            border: 1px solid #ddd !important;
            border-radius: 6px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
            margin-top: 4px !important;
            overflow: hidden !important;
        }

        .select2-search--dropdown {
            padding: 8px !important;
            background: #f8f9fa !important;
            border-bottom: 1px solid #eee !important;
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid #ddd !important;
            border-radius: 4px !important;
            padding: 6px 10px !important;
            font-size: 13px !important;
            width: 100% !important;
            height: 36px !important;
        }

        .select2-results {
            padding: 0 !important;
            max-height: 300px !important;
        }

        .select2-results__option {
            padding: 8px 12px !important;
            font-size: 14px !important;
            color: #333 !important;
            border-bottom: 1px solid #f5f5f5 !important;
        }

        .select2-results__option:last-child {
            border-bottom: none !important;
        }

        .select2-results__option--highlighted[aria-selected] {
            background-color: #ff6200 !important;
            color: white !important;
        }

        .select2-results__option[aria-selected=true] {
            background-color: #f0f0f0 !important;
            color: #333 !important;
        }

        /* ============================================
                           SELECT2 CLEAR BUTTON FIX
                        ============================================ */
        .select2-container--default .select2-selection--single .select2-selection__clear {
            height: 40px !important;
            width: 20px !important;
            margin-right: 25px !important;
            font-size: 18px !important;
            color: #999 !important;
            line-height: 40px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear:hover {
            color: #dc3545 !important;
        }

        /* ============================================
                           COMBOBOX STYLING (BERBEDA DARI INPUT BIASA)
                        ============================================ */
        .form-combobox {
            background: #f8f9fa !important;
            border: 2px solid #e0e0e0 !important;
            border-radius: 8px !important;
            padding: 2px !important;
            overflow: hidden !important;
        }

        .form-combobox .select2-container .select2-selection--single {
            background: transparent !important;
            border: none !important;
            height: 38px !important;
        }

        .form-combobox .select2-container .select2-selection--single .select2-selection__rendered {
            font-weight: 500 !important;
            color: #444 !important;
        }

        .form-combobox .select2-container.select2-container--focus .select2-selection--single,
        .form-combobox .select2-container.select2-container--open .select2-selection--single {
            border: none !important;
            box-shadow: none !important;
        }

        .form-combobox.select2-container--open {
            border-color: #ff6200 !important;
            background: white !important;
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
                           FILE UPLOAD DENGAN PREVIEW
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

        /* ============================================
                           SIGNATURE BUTTON - WARNA ORANGE
                        ============================================ */
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

        .signature-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* ============================================
                           CKEDITOR STYLING
                        ============================================ */
        .ck-editor__editable {
            min-height: 200px;
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd !important;
            border-radius: 6px !important;
            font-size: 14px;
            line-height: 1.6;
        }

        .ck-toolbar {
            background: #f8f9fa !important;
            border: 1px solid #ddd !important;
            border-bottom: none !important;
            border-radius: 6px 6px 0 0 !important;
        }

        .ck-editor__main {
            border-radius: 0 0 6px 6px !important;
            overflow: hidden;
        }

        /* ============================================
                           SIGNATURE CANVAS
                        ============================================ */
        .signature-canvas-container {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            margin-bottom: 15px;
        }

        #signatureCanvas {
            width: 100%;
            height: 200px;
            background: white;
            cursor: crosshair;
            display: block;
        }

        .signature-preview {
            max-width: 100%;
            max-height: 80px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-top: 10px;
            display: none;
        }

        .signature-preview.show {
            display: block;
            animation: fadeIn 0.3s ease;
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
                           REQUIRED FIELD & FORM TEXT
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

        .is-invalid:focus {
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.1) !important;
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

        /* ============================================
                           GRID SPACING
                        ============================================ */
        .row {
            margin-bottom: 10px;
        }

        .mb-3 {
            margin-bottom: 20px !important;
        }

        /* ============================================
                           ANIMATIONS
                        ============================================ */
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
                           MODAL SIGNATURE STYLING
                        ============================================ */
        .signature-modal .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .signature-modal .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
            padding: 15px 20px;
        }

        .signature-modal .modal-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .signature-modal .modal-body {
            padding: 20px;
        }

        .signature-modal .modal-footer {
            border-top: 1px solid #eaeaea;
            padding: 15px 20px;
            background: #f8f9fa;
        }

        .signature-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .signature-actions .btn {
            flex: 1;
            padding: 10px;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .signature-instructions {
            background: #f8f9fa;
            border: 1px solid #eaeaea;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #666;
        }

        .signature-instructions i {
            color: #ff6200;
            margin-right: 6px;
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
            color: #333;
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

        /* ============================================
                           MANUAL LOCATION INPUT
                        ============================================ */
        .manual-location-input {
            border: 2px solid #e0e0e0 !important;
            background: #f8f9fa !important;
            font-weight: 500 !important;
            color: #444 !important;
        }

        .manual-location-input:focus {
            border-color: #ff6200 !important;
            background: white !important;
        }

        /* ============================================
                           MOBILE RESPONSIVE
                        ============================================ */
        @media (max-width: 768px) {
            .card-body {
                padding: 15px;
            }

            .section-header {
                margin: 15px -15px 12px -15px;
                padding: 10px 12px;
                font-size: 13px;
            }

            .form-label {
                font-size: 13px;
                margin-bottom: 6px;
            }

            .form-control,
            .form-select {
                padding: 9px 11px;
                font-size: 13px;
                min-height: 40px;
            }

            .select2-container .select2-selection--single {
                height: 40px !important;
            }

            .select2-container .select2-selection--single .select2-selection__rendered {
                line-height: 38px !important;
                font-size: 13px !important;
            }

            .location-type-group {
                flex-direction: column;
                gap: 8px;
            }

            .location-type-option {
                min-width: 100%;
            }

            .location-type-label {
                padding: 10px 12px;
            }

            .file-upload-area {
                padding: 20px;
            }

            .file-upload-area i {
                font-size: 32px;
            }

            .file-upload-area h5 {
                font-size: 14px;
            }

            .file-upload-area p {
                font-size: 11px;
            }

            .file-list {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .file-preview {
                height: 100px;
            }

            .signature-btn,
            .btn-submit-ticket,
            .btn-cancel {
                padding: 10px 20px;
                font-size: 14px;
                width: 100%;
                justify-content: center;
                margin-bottom: 8px;
            }

            .btn-cancel {
                margin-bottom: 0;
            }

            .ck-editor__editable {
                min-height: 150px;
            }

            .signature-modal .modal-dialog {
                margin: 10px;
            }

            #signatureCanvas {
                height: 180px;
            }

            .signature-actions {
                flex-direction: column;
            }

            .signature-actions .btn {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 12px;
            }

            .section-header {
                margin: 12px -12px 10px -12px;
                padding: 8px 10px;
                font-size: 12px;
            }

            .form-text {
                font-size: 11px;
            }

            #signatureCanvas {
                height: 150px;
            }

            .file-list {
                grid-template-columns: 1fr;
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
                                <input type="text" class="form-control" name="title"
                                    placeholder="e.g., AC Room 502 tidak dingin" required>
                                <div class="invalid-feedback"></div>
                                <small class="form-text">Brief description of the issue</small>
                            </div>

                            <!-- Category - Combobox dengan styling berbeda -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Category <span class="required-mark">*</span>
                                </label>
                                <div class="form-combobox">
                                    <select class="form-select select2-single" name="category_id" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Priority - Combobox dengan styling berbeda -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Priority Level <span class="required-mark">*</span>
                                </label>
                                <div class="form-combobox">
                                    <select class="form-select select2-single" name="priority_id" required>
                                        <option value="">Select Priority</option>
                                        @foreach ($priorities as $priority)
                                            <option value="{{ $priority->id }}" data-color="{{ $priority->color }}">
                                                {{ $priority->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Department -->
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

                                <!-- Predefined Location Select - Combobox -->
                                <div class="location-selection-area active" id="predefinedLocationSection">
                                    <div class="form-combobox">
                                        <select class="form-select select2-single" id="location_id" name="location_id">
                                            <option value="">Select Location</option>
                                            <!-- Rooms -->
                                            @foreach ($locations->where('location_type', 'room')->sortBy('name') as $location)
                                                <option value="{{ $location->id }}" data-type="room"
                                                    data-floor="{{ $location->floor_number }}">
                                                    {{ $location->name }}
                                                    @if ($location->floor_number)
                                                        (Floor {{ $location->floor_number }})
                                                    @endif
                                                    - {{ ucfirst($location->hotel) }}
                                                </option>
                                            @endforeach
                                            <!-- Floors -->
                                            @foreach ($locations->where('location_type', 'floor')->sortBy('floor_number') as $location)
                                                <option value="{{ $location->id }}" data-type="floor">
                                                    {{ $location->name }}
                                                    @if ($location->floor_number)
                                                        (Floor {{ $location->floor_number }})
                                                    @endif
                                                    - {{ ucfirst($location->hotel) }}
                                                </option>
                                            @endforeach
                                            <!-- Areas -->
                                            @foreach ($locations->where('location_type', 'area')->sortBy('name') as $location)
                                                <option value="{{ $location->id }}" data-type="area">
                                                    {{ $location->name }} - {{ ucfirst($location->hotel) }}
                                                </option>
                                            @endforeach
                                            <!-- Facilities -->
                                            @foreach ($locations->where('location_type', 'facility')->sortBy('name') as $location)
                                                <option value="{{ $location->id }}" data-type="facility">
                                                    {{ $location->name }} - {{ ucfirst($location->hotel) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Manual Location Input -->
                                <div class="location-selection-area" id="manualLocationSection">
                                    <input type="text" class="form-control manual-location-input"
                                        id="location_manual_input" name="location_manual"
                                        placeholder="Enter location manually (e.g., Room 305, Lobby Area, Warehouse, etc.)">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Due Date (Optional)
                                </label>
                                <input type="datetime-local" class="form-control" name="due_date">
                                <div class="invalid-feedback"></div>
                                <small class="form-text">Expected completion date</small>
                            </div>
                        </div>

                        <!-- Description dengan CKEditor - HANYA SATU TEXTAREA -->
                        <div class="section-header">
                            <i class="fas fa-align-left"></i>
                            Detailed Description
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    Description <span class="required-mark">*</span>
                                </label>
                                <!-- HANYA SATU TEXTAREA untuk CKEditor -->
                                <textarea id="description" name="description" class="form-control" style="display:none;"></textarea>
                                <div class="invalid-feedback"></div>
                                <small class="form-text">Describe the issue in detail including when it started, symptoms,
                                    and any actions taken</small>
                            </div>
                        </div>

                        <!-- Attachments with Preview -->
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
                                ticket.
                                This signature will be recorded in the system audit trail.
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">
                                        Your Signature (As Reporter) <span class="required-mark">*</span>
                                    </label>

                                    <button type="button" class="signature-btn" id="openSignatureModal">
                                        <i class="fas fa-signature"></i>
                                        Click to Sign
                                    </button>

                                    <input type="hidden" name="signature_data" id="signatureData">

                                    <div class="mt-3">
                                        <img id="signaturePreview" class="signature-preview">
                                        <small id="signatureStatus" class="form-text">
                                            <i class="fas fa-times-circle text-danger"></i> No signature provided yet
                                        </small>
                                        <small class="form-text text-muted d-block mt-1">
                                            <i class="fas fa-shield-alt"></i> This signature will be stored securely in the
                                            signatures table
                                        </small>
                                    </div>
                                </div>
                            </div>
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
    <div class="modal fade signature-modal" id="signatureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-signature"></i>
                        Your Signature (Reporter)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="signature-instructions">
                        <p><i class="fas fa-info-circle"></i> Use your mouse or finger to sign in the box below.
                            Your signature confirms that you are reporting this ticket.</p>
                    </div>

                    <div class="signature-canvas-container">
                        <canvas id="signatureCanvas"></canvas>
                    </div>

                    <div class="signature-actions">
                        <button type="button" class="btn btn-light" id="clearSignature">
                            <i class="fas fa-eraser me-1"></i> Clear
                        </button>
                        <button type="button" class="btn btn-light" id="undoSignature">
                            <i class="fas fa-undo me-1"></i> Undo
                        </button>
                        <button type="button" class="btn btn-primary" id="saveSignature">
                            <i class="fas fa-check me-1"></i> Save Signature
                        </button>
                    </div>

                    <div class="signature-preview-container mt-3">
                        <small class="form-text">Preview:</small>
                        <img id="modalSignaturePreview" class="signature-preview">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <!-- Bootstrap Modal -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- CKEditor -->
    <script src="{{ asset('assets/vendor/ckeditor/ckeditor.js') }}"></script>
    <!-- Signature Pad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 dengan SEARCH enabled
            $('.select2-single').select2({
                theme: 'bootstrap',
                width: '100%',
                placeholder: 'Select an option',
                allowClear: false,
                dropdownParent: $('#createTicketForm'),
                minimumResultsForSearch: 0, // Selalu tampilkan search
                searchInputPlaceholder: 'Type to search...',
                language: {
                    searching: function() {
                        return "Searching...";
                    },
                    noResults: function() {
                        return "No results found";
                    }
                }
            });

            // Toastr configuration
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "4000",
                "extendedTimeOut": "1000"
            };

            // Initialize CKEditor - LANGSUNG ke textarea #description
            let editor;
            ClassicEditor
                .create(document.querySelector('#description'), {
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', 'underline', 'strikethrough', '|',
                            'bulletedList', 'numberedList', '|',
                            'link', 'blockQuote', 'insertTable', '|',
                            'undo', 'redo', '|',
                            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor'
                        ]
                    },
                    language: 'en',
                    licenseKey: '',
                })
                .then(newEditor => {
                    editor = newEditor;

                    // Set minimum height
                    editor.editing.view.change(writer => {
                        writer.setStyle('min-height', '200px', editor.editing.view.document.getRoot());
                    });
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                    // Fallback ke textarea biasa
                    $('#description').show().attr('rows', 5);
                });

            // Location Type Toggle
            $('.location-type-label').on('click', function() {
                const radioId = $(this).attr('for');
                const radio = $('#' + radioId);

                // Update radio button state
                radio.prop('checked', true);

                // Update label styles
                $('.location-type-label').removeClass('active');
                $(this).addClass('active');

                // Show/hide appropriate sections
                const type = radio.val();
                if (type === 'predefined') {
                    $('#predefinedLocationSection').addClass('active');
                    $('#manualLocationSection').removeClass('active');
                    $('#location_id').prop('required', true);
                    $('#location_manual_input').prop('required', false).val('');
                } else {
                    $('#predefinedLocationSection').removeClass('active');
                    $('#manualLocationSection').addClass('active');
                    $('#location_manual_input').prop('required', true);
                    $('#location_id').prop('required', false).val(null).trigger('change');
                }

                // Clear validation
                $('#location_id, #location_manual_input').removeClass('is-invalid');
            });

            // Initialize Signature Pad
            let signaturePad = null;
            const canvas = document.getElementById('signatureCanvas');

            // Initialize signature pad when modal is shown
            $('#signatureModal').on('shown.bs.modal', function() {
                if (!signaturePad) {
                    signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(0, 0, 0)',
                        minWidth: 0.5,
                        maxWidth: 2.5,
                        throttle: 16,
                        velocityFilterWeight: 0.7
                    });

                    // Clear signature preview
                    $('#modalSignaturePreview').attr('src', '').removeClass('show');
                }

                // Resize canvas
                resizeCanvas();
            });

            // Resize canvas function
            function resizeCanvas() {
                if (!canvas || !signaturePad) return;

                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);

                // Clear and redraw if signature exists
                if (signaturePad && !signaturePad.isEmpty()) {
                    const data = signaturePad.toData();
                    signaturePad.clear();
                    setTimeout(() => {
                        signaturePad.fromData(data);
                    }, 100);
                }
            }

            // Window resize handler
            let resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(resizeCanvas, 250);
            });

            // Signature modal actions
            $('#openSignatureModal').on('click', function() {
                $('#signatureModal').modal('show');
            });

            $('#clearSignature').on('click', function() {
                if (signaturePad) {
                    signaturePad.clear();
                    $('#modalSignaturePreview').attr('src', '').removeClass('show');
                }
            });

            $('#undoSignature').on('click', function() {
                if (signaturePad) {
                    const data = signaturePad.toData();
                    if (data.length > 0) {
                        data.pop(); // Remove the last dot or line
                        signaturePad.fromData(data);
                        updateSignaturePreview();
                    }
                }
            });

            $('#saveSignature').on('click', function() {
                if (!signaturePad || signaturePad.isEmpty()) {
                    toastr.error('Please provide a signature');
                    return;
                }

                const signatureData = signaturePad.toDataURL('image/png');

                // Save to hidden input
                $('#signatureData').val(signatureData);

                // Update preview
                $('#signaturePreview').attr('src', signatureData).addClass('show');
                $('#signatureStatus').html(
                    '<i class="fas fa-check-circle text-success"></i> Signature saved (Reporter)'
                );

                // Close modal
                $('#signatureModal').modal('hide');

                toastr.success('Signature saved successfully');
            });

            // Update signature preview on draw
            canvas.addEventListener('endStroke', updateSignaturePreview);

            function updateSignaturePreview() {
                if (signaturePad && !signaturePad.isEmpty()) {
                    const previewData = signaturePad.toDataURL('image/png');
                    $('#modalSignaturePreview').attr('src', previewData).addClass('show');
                } else {
                    $('#modalSignaturePreview').attr('src', '').removeClass('show');
                }
            }

            // File upload handling dengan preview
            let selectedFiles = [];

            $('#fileInput').on('change', function(e) {
                const files = Array.from(e.target.files);

                files.forEach(file => {
                    if (file.size > 5 * 1024 * 1024) {
                        toastr.error(`File ${file.name} exceeds 5MB limit`);
                        return;
                    }

                    if (!file.type.match(
                            /^image\/(jpeg|png|jpg)|application\/(pdf|msword|vnd\.openxmlformats-officedocument\.wordprocessingml\.document)$/
                        )) {
                        toastr.error(`File type not supported: ${file.name}`);
                        return;
                    }

                    selectedFiles.push(file);
                    addFileToList(file);
                });

                this.value = '';
            });

            function addFileToList(file) {
                const fileId = 'file_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                const fileExtension = file.name.split('.').pop().toLowerCase();
                const fileType = getFileType(fileExtension);

                // Create preview based on file type
                let previewContent = '';
                if (fileType === 'image') {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $(`#${fileId} .file-preview-content`).html(
                            `<img src="${e.target.result}" alt="${file.name}">`);
                    };
                    reader.readAsDataURL(file);
                    previewContent = '<div class="file-preview-content"></div>';
                } else {
                    const icon = getFileIcon(fileExtension);
                    previewContent =
                        `<div class="file-preview-content"><i class="fas ${icon} file-icon"></i></div>`;
                }

                const fileItem = $(`
                    <div class="file-item" id="${fileId}" data-filename="${file.name}">
                        <div class="file-preview">
                            ${previewContent}
                        </div>
                        <div class="file-info">
                            <div class="file-name" title="${file.name}">${file.name}</div>
                            <div class="file-size">${formatFileSize(file.size)}</div>
                            <div class="file-actions">
                                <span class="file-type-badge">${fileExtension.toUpperCase()}</span>
                                <i class="fas fa-times remove-file" title="Remove file"></i>
                            </div>
                        </div>
                    </div>
                `);

                fileItem.find('.remove-file').on('click', function() {
                    const fileName = $(this).closest('.file-item').data('filename');
                    selectedFiles = selectedFiles.filter(f => f.name !== fileName);
                    $(this).closest('.file-item').remove();
                    toastr.info(`Removed: ${fileName}`);
                });

                $('#fileList').append(fileItem);
            }

            function getFileType(extension) {
                const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                return imageExtensions.includes(extension) ? 'image' : 'document';
            }

            function getFileIcon(extension) {
                const iconMap = {
                    'pdf': 'fa-file-pdf',
                    'doc': 'fa-file-word',
                    'docx': 'fa-file-word',
                    'xls': 'fa-file-excel',
                    'xlsx': 'fa-file-excel',
                    'ppt': 'fa-file-powerpoint',
                    'pptx': 'fa-file-powerpoint',
                    'txt': 'fa-file-alt',
                    'zip': 'fa-file-archive',
                    'rar': 'fa-file-archive'
                };
                return iconMap[extension] || 'fa-file';
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }

            // Form submission
            $('#createTicketForm').on('submit', function(e) {
                e.preventDefault();

                // Basic validation
                let isValid = true;

                // Clear previous errors
                $('.form-control, .form-select, .select2-selection').removeClass('is-invalid');
                $('.invalid-feedback').hide();

                // Validate title
                if (!$('input[name="title"]').val().trim()) {
                    $('input[name="title"]').addClass('is-invalid').next('.invalid-feedback').text(
                        'Title is required').show();
                    isValid = false;
                }

                // Validate category
                if (!$('select[name="category_id"]').val()) {
                    $('select[name="category_id"]').addClass('is-invalid').next('.invalid-feedback').text(
                        'Category is required').show();
                    isValid = false;
                }

                // Validate priority
                if (!$('select[name="priority_id"]').val()) {
                    $('select[name="priority_id"]').addClass('is-invalid').next('.invalid-feedback').text(
                        'Priority is required').show();
                    isValid = false;
                }

                // Validate location
                const locationType = $('input[name="location_type"]:checked').val();
                if (locationType === 'predefined' && !$('#location_id').val()) {
                    $('#location_id').addClass('is-invalid').next('.invalid-feedback').text(
                        'Please select a location').show();
                    isValid = false;
                } else if (locationType === 'manual' && !$('#location_manual_input').val().trim()) {
                    $('#location_manual_input').addClass('is-invalid').next('.invalid-feedback').text(
                        'Please enter a location').show();
                    isValid = false;
                }

                // Validate description (CKEditor)
                let description = '';
                if (editor) {
                    description = editor.getData().trim();
                } else {
                    description = $('#description').val().trim();
                }

                if (!description) {
                    toastr.error('Description is required');
                    isValid = false;
                }

                // Validate signature
                if (!$('#signatureData').val()) {
                    toastr.error('Please provide your signature');
                    $('#openSignatureModal').focus();
                    isValid = false;
                }

                if (!isValid) {
                    // Scroll to first error
                    const firstError = $('.is-invalid').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 100
                        }, 500);
                    }
                    return;
                }

                const formData = new FormData(this);

                // Append selected files
                selectedFiles.forEach((file, index) => {
                    formData.append(`attachments[${index}]`, file);
                });

                // Ensure description is included
                formData.set('description', description);

                // Handle location based on type
                if (locationType === 'predefined') {
                    formData.set('location_id', $('#location_id').val());
                    formData.set('location_manual', '');
                } else {
                    formData.set('location_id', '');
                    formData.set('location_manual', $('#location_manual_input').val());
                }

                const $submitBtn = $('#submitBtn');
                const originalText = $submitBtn.html();
                $submitBtn.prop('disabled', true).addClass('btn-loading').html('');

                // Show loading
                toastr.info('Creating ticket... Please wait');

                $.ajax({
                    url: "{{ route('tickets.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Ticket created successfully!');

                            Swal.fire({
                                icon: 'success',
                                title: 'Ticket Created!',
                                html: `Ticket <strong>#${response.ticket_number}</strong> has been created successfully.<br><br>
                                      <small class="text-muted">Your signature has been recorded as reporter</small>`,
                                showConfirmButton: true,
                                confirmButtonText: 'View Ticket',
                                showCancelButton: true,
                                cancelButtonText: 'Create Another',
                                focusConfirm: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Redirect ke My Tickets (sesuai sidebar)
                                    window.location.href =
                                        "{{ route('tickets.index', ['my_tickets' => '1']) }}";
                                } else {
                                    // Reset form for new ticket
                                    resetForm();
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        $submitBtn.prop('disabled', false).removeClass('btn-loading').html(
                            originalText);

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            // Clear previous errors
                            $('.form-control, .form-select').removeClass('is-invalid');
                            $('.invalid-feedback').hide();

                            // Display new errors
                            $.each(errors, function(key, value) {
                                const element = $(`[name="${key}"]`);
                                element.addClass('is-invalid');
                                element.next('.invalid-feedback').text(value[0]).show();
                            });

                            // Scroll to first error
                            const firstError = $('.is-invalid').first();
                            if (firstError.length) {
                                $('html, body').animate({
                                    scrollTop: firstError.offset().top - 100
                                }, 500);
                            }

                            toastr.error('Please check the form for errors');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message ||
                                    'An error occurred while creating the ticket. Please try again.',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).removeClass('btn-loading').html(
                            originalText);
                    }
                });
            });

            // Form reset function
            function resetForm() {
                $('#createTicketForm')[0].reset();
                $('.select2-single').val('').trigger('change');

                // Reset CKEditor
                if (editor) {
                    editor.setData('');
                } else {
                    $('#description').val('');
                }

                // Reset location type
                $('.location-type-label').removeClass('active');
                $('.location-type-label[for="location_predefined"]').addClass('active');
                $('input[name="location_type"][value="predefined"]').prop('checked', true);
                $('#predefinedLocationSection').addClass('active');
                $('#manualLocationSection').removeClass('active');

                // Reset signature
                $('#signatureData').val('');
                $('#signaturePreview').attr('src', '').removeClass('show');
                $('#signatureStatus').html(
                    '<i class="fas fa-times-circle text-danger"></i> No signature provided yet');

                // Reset files
                selectedFiles = [];
                $('#fileList').empty();

                // Clear validation
                $('.form-control, .form-select').removeClass('is-invalid');
                $('.invalid-feedback').hide();

                toastr.info('Form cleared. Ready to create new ticket.');
                $('input[name="title"]').focus();
            }

            // Remove invalid class on input
            $('.form-control, .form-select').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').hide();
            });

            // Initialize
            $('input[name="title"]').focus();
        });
    </script>
@endpush
