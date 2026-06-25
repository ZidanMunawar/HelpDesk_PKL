{{-- resources/views/activity-logs/index.blade.php --}}
@extends('layouts.main')

@section('title', 'Activity Logs | ' . config('app.name'))
@section('page-title', 'Activity Logs')

@section('breadcrumb')
    @php
        $breadcrumb = [['title' => 'Activity Logs', 'url' => 'javascript:void(0)']];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        :root {
            --primary-navy: #1a2b4c;
            --primary-orange: #ff6b35;
        }

        body {
            background-color: #f4f7fc;
        }

        .activity-log-page {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Filter Accordion */
        .filter-accordion {
            background: white;
            border-radius: 16px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .filter-accordion-header {
            padding: 16px 20px;
            background: white;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .filter-accordion-header:hover {
            background: #f8f9fa;
        }

        .filter-accordion-header h5 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: var(--primary-navy);
        }

        .filter-accordion-header h5 i {
            color: var(--primary-orange);
            margin-right: 8px;
        }

        /* HANYA icon chevron yang berputar */
        .filter-accordion-header .fa-chevron-down {
            transition: transform 0.3s ease;
            color: #999;
        }

        .filter-accordion-header.active .fa-chevron-down {
            transform: rotate(180deg);
        }

        /* Atau alternatif dengan class spesifik */
        .filter-accordion-header .toggle-icon {
            transition: transform 0.3s ease;
            color: #999;
        }

        .filter-accordion-header.active .toggle-icon {
            transform: rotate(180deg);
        }

        .filter-accordion-content {
            display: none;
            padding: 20px;
            border-top: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .filter-accordion-content.show {
            display: block;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-item {
            flex: 1;
            min-width: 160px;
        }

        .filter-item label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 6px;
            display: block;
        }

        .filter-item select,
        .filter-item input {
            width: 100%;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 13px;
            height: 42px;
            background: white;
        }

        .filter-item select:focus,
        .filter-item input:focus {
            border-color: var(--primary-orange);
            outline: none;
        }

        /* Select2 styling agar seragam dengan input lainnya */
        .filter-item .select2-container--default .select2-selection--single {
            height: 42px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
        }

        .filter-item .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 12px;
            padding-right: 30px;
            font-size: 13px;
            color: #333;
        }

        .filter-item .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            right: 8px;
        }

        .filter-item .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #999;
        }

        .filter-item .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--primary-orange);
            outline: none;
        }

        .filter-item .select2-dropdown {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 4px;
        }

        .filter-item .select2-results__option {
            padding: 8px 12px;
            font-size: 13px;
        }

        .filter-item .select2-results__option--highlighted {
            background: var(--primary-orange);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
        }

        .btn-filter {
            background: var(--primary-navy);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
            font-size: 13px;
            font-weight: 500;
            height: 42px;
            transition: all 0.3s ease;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-filter:hover {
            background: #2a3b5c;
            transform: translateY(-1px);
        }

        .btn-filter.reset {
            background: #6c757d;
        }

        .btn-filter.reset:hover {
            background: #5a6268;
        }

        /* Toolbar */
        .toolbar {
            background: white;
            border-radius: 16px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .total-info {
            font-size: 14px;
            color: #666;
        }

        .total-info strong {
            color: var(--primary-orange);
            font-size: 18px;
        }

        .btn-icon {
            border: none;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-icon i {
            margin-right: 6px;
        }

        .btn-icon:hover {
            background: #e9ecef;
            transform: translateY(-1px);
        }

        /* Logs Container */
        .logs-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .logs-list {
            padding: 0;
        }

        .log-card {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            background: white;
        }

        .log-card:hover {
            background: #fafbfc;
        }

        .log-card-content {
            padding: 20px;
        }

        .log-header {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 12px;
        }

        .log-icon-wrapper {
            flex-shrink: 0;
        }

        .log-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .log-icon-login {
            background: #e3f2fd;
            color: #1976d2;
        }

        .log-icon-logout {
            background: #f5f5f5;
            color: #616161;
        }

        .log-icon-created {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .log-icon-updated {
            background: #e3f2fd;
            color: #1976d2;
        }

        .log-icon-deleted {
            background: #ffebee;
            color: #c62828;
        }

        .log-icon-approved,
        .log-icon-completed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .log-icon-rejected {
            background: #ffebee;
            color: #c62828;
        }

        .log-icon-commented {
            background: #fff3e0;
            color: #f57c00;
        }

        .log-icon-assigned {
            background: #e8f0fe;
            color: var(--primary-navy);
        }

        .log-info {
            flex: 1;
        }

        .log-title {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 5px;
        }

        .log-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .log-badge-login {
            background: #e3f2fd;
            color: #1976d2;
        }

        .log-badge-logout {
            background: #f5f5f5;
            color: #616161;
        }

        .log-badge-created {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .log-badge-updated {
            background: #e3f2fd;
            color: #1976d2;
        }

        .log-badge-deleted {
            background: #ffebee;
            color: #c62828;
        }

        .log-badge-approved,
        .log-badge-completed {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .log-badge-rejected {
            background: #ffebee;
            color: #c62828;
        }

        .log-badge-commented {
            background: #fff3e0;
            color: #f57c00;
        }

        .log-badge-assigned {
            background: #e8f0fe;
            color: var(--primary-navy);
        }

        .log-user {
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }

        .log-role {
            font-size: 11px;
            color: #999;
            font-weight: normal;
        }

        .log-time {
            font-size: 11px;
            color: #999;
        }

        .log-relative {
            color: #bbb;
        }

        .log-description {
            font-size: 13px;
            color: #555;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .log-ticket {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 12px;
            margin-bottom: 10px;
            border-left: 3px solid var(--primary-orange);
        }

        .log-badge-danger {
            background: #ffebee;
            color: #c62828;
        }

        .log-badge-success {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .log-badge-warning {
            background: #fff3e0;
            color: #f57c00;
        }

        .log-badge-secondary {
            background: #f5f5f5;
            color: #616161;
        }

        /* Ikon untuk login_failed dan password_reset_failed */
        .log-icon-login_failed,
        .log-icon-password_reset_failed {
            background: #ffebee;
            color: #c62828;
        }

        .log-icon-password_reset_requested {
            background: #fff3e0;
            color: #f57c00;
        }

        .log-icon-password_reset {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .log-icon-user_registered {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .log-icon-user_deleted {
            background: #ffebee;
            color: #c62828;
        }

        .log-icon-user_reset {
            background: #fff3e0;
            color: #f57c00;
        }

        .log-icon-status_changed {
            background: #fff3e0;
            color: #f57c00;
        }

        .log-icon-broadcast {
            background: #e8f0fe;
            color: var(--primary-navy);
        }

        .btn-ticket-link {
            color: var(--primary-orange);
            margin-left: 8px;
        }

        .log-meta {
            font-size: 11px;
            color: #aaa;
            margin-bottom: 10px;
        }

        .log-actions {
            flex-shrink: 0;
        }

        .btn-view-log {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: none;
            background: #f0f0f0;
            color: #666;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-view-log:hover {
            background: var(--primary-orange);
            color: white;
            transform: translateY(-2px);
        }

        .btn-toggle-changes {
            background: none;
            border: none;
            color: var(--primary-orange);
            font-size: 12px;
            font-weight: 500;
            padding: 0;
            cursor: pointer;
        }

        .btn-toggle-changes:hover {
            text-decoration: underline;
        }

        .changes-content {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
        }

        .changes-content pre {
            font-size: 11px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        /* Pagination */
        .pagination-wrapper {
            padding: 20px;
            border-top: 1px solid #f0f0f0;
            display: flex;
            justify-content: center;
        }

        .pagination {
            display: flex;
            gap: 5px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .pagination .page-item {
            margin: 0;
        }

        .pagination .page-link {
            padding: 8px 14px;
            border: 1px solid #e9ecef;
            background: white;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            color: #666;
            transition: all 0.3s ease;
            text-decoration: none;
            cursor: pointer;
        }

        .pagination .page-link:hover {
            background: var(--primary-navy);
            color: white;
            border-color: var(--primary-navy);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-orange);
            color: white;
            border-color: var(--primary-orange);
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 70px;
            color: #ddd;
            margin-bottom: 15px;
        }

        .empty-state h4 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }

        .empty-state p {
            color: #666;
        }

        /* Modal Styles */
        .log-details-container {
            padding: 5px;
        }

        .detail-section {
            margin-bottom: 25px;
        }

        .detail-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--primary-navy);
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f0f0f0;
        }

        .detail-title i {
            color: var(--primary-orange);
            margin-right: 8px;
        }

        .detail-table {
            width: 100%;
            font-size: 13px;
        }

        .detail-table td {
            padding: 6px 0;
        }

        .detail-description {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            font-size: 13px;
            line-height: 1.5;
        }

        .old-values,
        .new-values {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 12px;
        }

        .old-values pre,
        .new-values pre {
            font-size: 11px;
            white-space: pre-wrap;
            word-wrap: break-word;
            margin: 0;
        }

        .ticket-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }

        .btn-view-ticket {
            background: var(--primary-navy);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-view-ticket:hover {
            background: #2a3b5c;
            color: white;
        }

        /* Loading */
        .ajax-loading {
            position: relative;
            min-height: 200px;
        }

        .ajax-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px;
            z-index: 10;
        }

        .ajax-loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-orange);
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

        /* Responsive */
        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
            }

            .filter-item {
                width: 100%;
            }

            .filter-actions {
                justify-content: flex-end;
            }

            .toolbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .log-header {
                flex-wrap: wrap;
            }

            .log-actions {
                margin-left: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="activity-log-page">
        <!-- Filter Accordion -->
        <div class="filter-accordion">
            <div class="filter-accordion-header" id="filterAccordionHeader">
                <h5><i class="fas fa-filter me-2"></i> Filter Activity Logs</h5>
                <i class="fas fa-chevron-down toggle-icon"></i> {{-- Tambahkan class toggle-icon --}}
            </div>
            <div class="filter-accordion-content" id="filterAccordionContent">
                <div class="filter-row">
                    <div class="filter-item">
                        <label><i class="fas fa-user"></i> User</label>
                        <select id="filterUser" class="select2-filter" style="width: 100%;">
                            <option value="all">All Users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ ($currentFilters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item">
                        <label><i class="fas fa-tag"></i> Action</label>
                        <select id="filterAction" class="select2-filter" style="width: 100%;">
                            <option value="all">All Actions</option>
                            @foreach ($actions as $action)
                                <option value="{{ $action }}"
                                    {{ ($currentFilters['action'] ?? '') == $action ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $action)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-item">
                        <label><i class="fas fa-calendar-alt"></i> From Date</label>
                        <input type="date" id="filterDateFrom" value="{{ $currentFilters['date_from'] ?? '' }}">
                    </div>

                    <div class="filter-item">
                        <label><i class="fas fa-calendar-alt"></i> To Date</label>
                        <input type="date" id="filterDateTo" value="{{ $currentFilters['date_to'] ?? '' }}">
                    </div>

                    <div class="filter-item">
                        <label><i class="fas fa-search"></i> Search</label>
                        <input type="text" id="filterSearch" placeholder="Search description, user, ticket..."
                            value="{{ $currentFilters['search'] ?? '' }}">
                    </div>

                    <div class="filter-actions">
                        <button type="button" class="btn-filter" id="applyFiltersBtn">
                            <i class="fas fa-check me-1"></i> Apply
                        </button>
                        <button type="button" class="btn-filter reset" id="resetFiltersBtn">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="toolbar">
            <div class="total-info">
                <i class="fas fa-database me-1"></i> Total: <strong id="totalCount">{{ $logs->total() }}</strong> logs
            </div>
            <div>
                <button type="button" class="btn-icon" id="exportBtn">
                    <i class="fas fa-file-csv text-success"></i> Export CSV
                </button>
                <button type="button" class="btn-icon" id="refreshBtn">
                    <i class="fas fa-sync-alt text-primary"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Logs Container -->
        <div class="logs-container">
            <div id="logsList" class="logs-list">
                @include('activity-logs.partials.logs-list')
            </div>
            <div id="paginationContainer" class="pagination-wrapper">
                @include('activity-logs.partials.pagination')
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="logDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--primary-navy); color: white;">
                    <h5 class="modal-title" style="color: white"><i class="fas fa-info-circle me-2"
                            style="color: white"></i> Log Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="logDetailContent">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="mt-2">Loading...</p>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        // Toastr config
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        // Initialize Select2 dengan styling seragam
        $('.select2-filter').select2({
            width: '100%',
            placeholder: 'Select...',
            allowClear: true,
            minimumResultsForSearch: 5
        });

        // Filter Accordion
        $('#filterAccordionHeader').on('click', function() {
            $(this).toggleClass('active');
            $('#filterAccordionContent').toggleClass('show');
        });

        // =============================================
        // LOAD LOGS VIA AJAX (NO RELOAD!)
        // =============================================
        function loadLogs(page = 1) {
            const container = $('#logsList');
            const paginationContainer = $('#paginationContainer');

            container.addClass('ajax-loading');

            let url = "{{ route('activity-logs.index') }}?ajax=1&page=" + page;
            const userId = $('#filterUser').val();
            const action = $('#filterAction').val();
            const dateFrom = $('#filterDateFrom').val();
            const dateTo = $('#filterDateTo').val();
            const search = $('#filterSearch').val();

            if (userId && userId !== 'all') url += "&user_id=" + userId;
            if (action && action !== 'all') url += "&action=" + action;
            if (dateFrom) url += "&date_from=" + dateFrom;
            if (dateTo) url += "&date_to=" + dateTo;
            if (search) url += "&search=" + encodeURIComponent(search);

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    container.removeClass('ajax-loading');
                    if (response.html) {
                        container.html(response.html);
                    }
                    if (response.pagination) {
                        paginationContainer.html(response.pagination);
                    }
                    if (response.total !== undefined) {
                        $('#totalCount').text(response.total);
                    }
                    // Update URL tanpa reload
                    updateUrlParams(page);
                },
                error: function(xhr) {
                    container.removeClass('ajax-loading');
                    console.error(xhr);
                    toastr.error('Failed to load logs');
                }
            });
        }

        // Update URL params without reload
        function updateUrlParams(page) {
            let params = new URLSearchParams();
            const userId = $('#filterUser').val();
            const action = $('#filterAction').val();
            const dateFrom = $('#filterDateFrom').val();
            const dateTo = $('#filterDateTo').val();
            const search = $('#filterSearch').val();

            if (userId && userId !== 'all') params.set('user_id', userId);
            if (action && action !== 'all') params.set('action', action);
            if (dateFrom) params.set('date_from', dateFrom);
            if (dateTo) params.set('date_to', dateTo);
            if (search) params.set('search', search);
            if (page > 1) params.set('page', page);

            let newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.pushState({}, '', newUrl);
        }

        // =============================================
        // PAGINATION HANDLER
        // =============================================
        $(document).on('click', '.ajax-page', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            loadLogs(page);
        });

        // =============================================
        // APPLY FILTERS
        // =============================================
        $('#applyFiltersBtn').on('click', function() {
            loadLogs(1);
        });

        // =============================================
        // RESET FILTERS
        // =============================================
        $('#resetFiltersBtn').on('click', function() {
            $('#filterUser').val('all').trigger('change');
            $('#filterAction').val('all').trigger('change');
            $('#filterDateFrom').val('');
            $('#filterDateTo').val('');
            $('#filterSearch').val('');
            loadLogs(1);
        });

        // =============================================
        // EXPORT CSV
        // =============================================
        $('#exportBtn').on('click', function() {
            let url = "{{ route('activity-logs.export') }}?";
            const userId = $('#filterUser').val();
            const action = $('#filterAction').val();
            const dateFrom = $('#filterDateFrom').val();
            const dateTo = $('#filterDateTo').val();
            const search = $('#filterSearch').val();

            if (userId && userId !== 'all') url += "&user_id=" + userId;
            if (action && action !== 'all') url += "&action=" + action;
            if (dateFrom) url += "&date_from=" + dateFrom;
            if (dateTo) url += "&date_to=" + dateTo;
            if (search) url += "&search=" + encodeURIComponent(search);

            window.location.href = url;
        });

        // =============================================
        // REFRESH
        // =============================================
        $('#refreshBtn').on('click', function() {
            loadLogs(1);
            toastr.info('Refreshed');
        });

        // =============================================
        // VIEW LOG DETAILS
        // =============================================
        window.viewLogDetails = function(id) {
            const modal = $('#logDetailModal');
            const content = $('#logDetailContent');

            content.html(
                '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i><p class="mt-2">Loading...</p></div>'
            );
            modal.modal('show');

            $.ajax({
                url: "{{ url('activity-logs') }}/" + id,
                method: 'GET',
                data: {
                    ajax: 1
                },
                success: function(response) {
                    content.html(response);
                },
                error: function(xhr) {
                    content.html(
                        '<div class="text-center py-4"><i class="fas fa-exclamation-triangle fa-3x text-warning"></i><p class="mt-2">Failed to load details</p></div>'
                    );
                    toastr.error('Failed to load log details');
                }
            });
        };

        // =============================================
        // TOGGLE CHANGES
        // =============================================
        window.toggleChanges = function(id) {
            $('#changes-' + id).toggle('fast');
        };
    </script>
@endpush
