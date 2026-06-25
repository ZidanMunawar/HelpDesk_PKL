@extends('layouts.main')

@section('title', 'Maintenance Calendar | ' . config('app.name'))

@section('page-title', 'Maintenance Calendar')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Maintenance Calendar', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fullcalendar/css/main.min.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css">

    <style>
        :root {
            --navy: #003366;
            --orange: #ff6600;
            --navy-light: #1e4a7a;
            --orange-light: #ff8533;
        }

        /* Dynamic priority styles from database */
        @foreach ($priorities as $priority)
            .priority-badge-{{ strtolower(str_replace(' ', '-', $priority->name)) }} {
                background-color: {{ $priority->color }};
            }

            .priority-flag-color-{{ strtolower(str_replace(' ', '-', $priority->name)) }} {
                color: {{ $priority->color }} !important;
            }

            .fc-event[data-priority-name="{{ $priority->name }}"] {
                border-left-color: {{ $priority->color }} !important;
            }
        @endforeach

        /* Tippy Tooltip */
        .tippy-box {
            background: #1e293b;
            color: #f1f5f9;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        }

        .tippy-content {
            padding: 10px 14px;
        }

        .calendar-tooltip {
            font-size: 12px;
            line-height: 1.4;
        }

        .calendar-tooltip .tooltip-title {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 6px;
            padding-bottom: 5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .calendar-tooltip .tooltip-row {
            display: flex;
            margin-bottom: 4px;
            gap: 8px;
        }

        .calendar-tooltip .tooltip-label {
            width: 65px;
            font-size: 10px;
            color: #94a3b8;
            font-weight: 500;
        }

        .calendar-tooltip .tooltip-value {
            flex: 1;
            font-weight: 500;
            color: #f1f5f9;
            font-size: 11px;
        }

        .calendar-tooltip .priority-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 600;
            color: white;
        }

        .calendar-tooltip .access-warning {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 9px;
        }

        .calendar-tooltip .access-allowed {
            color: #22c55e;
        }

        .calendar-tooltip .access-denied {
            color: #fbbf24;
        }

        /* Filter Card */
        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0, 51, 102, 0.08);
            border: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .filter-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--navy);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: 1.5px solid transparent;
        }

        .filter-btn.created {
            background: rgba(0, 51, 102, 0.08);
            color: var(--navy);
            border-color: var(--navy);
        }

        .filter-btn.created.active {
            background: var(--navy);
            color: white;
        }

        .filter-btn.closed {
            background: rgba(255, 102, 0, 0.08);
            color: var(--orange);
            border-color: var(--orange);
        }

        .filter-btn.closed.active {
            background: var(--orange);
            color: white;
        }

        .filter-btn i {
            font-size: 12px;
        }

        .filter-btn .count {
            background: rgba(0, 0, 0, 0.1);
            padding: 1px 6px;
            border-radius: 20px;
            font-size: 10px;
            margin-left: 4px;
        }

        .filter-btn.active .count {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Info Card */
        .info-card {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 16px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);
        }

        .info-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .info-text h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .info-text p {
            font-size: 11px;
            opacity: 0.9;
            margin: 0;
        }

        .btn-print {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Calendar Container */
        .calendar-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Calendar Customization */
        .fc {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 51, 102, 0.08);
            border: 1px solid #e5e7eb;
            width: 100%;
        }

        .fc .fc-view-harness {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }

        .fc .fc-scroller {
            overflow: auto !important;
        }

        .fc .fc-toolbar {
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
            padding: 0 8px;
        }

        .fc .fc-toolbar-title {
            color: var(--navy);
            font-weight: 700;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
            padding: 4px 12px;
            border-radius: 30px;
            background: rgba(0, 51, 102, 0.05);
            display: inline-block;
        }

        .fc .fc-toolbar-title:hover {
            background: rgba(0, 51, 102, 0.12);
            color: var(--orange);
        }

        .fc .fc-button-primary {
            background-color: var(--navy);
            border-color: var(--navy);
            font-weight: 500;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 12px;
        }

        .fc .fc-button-primary:hover {
            background-color: var(--navy-light);
            border-color: var(--navy-light);
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: var(--orange);
            border-color: var(--orange);
        }

        .fc .fc-day-today {
            background-color: rgba(255, 102, 0, 0.05) !important;
        }

        .fc .fc-daygrid-day-number {
            font-weight: 600;
            color: #333;
            font-size: 13px;
            padding: 4px;
        }

        .fc .fc-col-header-cell-cushion {
            font-weight: 600;
            color: var(--navy);
            padding: 8px 4px;
            text-transform: uppercase;
            font-size: 11px;
        }

        .fc .fc-event {
            cursor: pointer;
            border-radius: 5px;
            padding: 2px 5px;
            font-size: 10px;
            font-weight: 500;
            border: none;
            margin: 1px 3px;
            transition: transform 0.2s;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border-left: 3px solid var(--navy);
        }

        .fc .fc-event:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .fc .fc-daygrid-more-link {
            background: #eef2ff;
            border-radius: 5px;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: 600;
            color: var(--navy);
            cursor: pointer;
            margin: 1px 3px;
            display: inline-block;
            transition: all 0.2s;
            text-decoration: none;
        }

        .fc .fc-daygrid-more-link:hover {
            background: var(--navy);
            color: white;
        }

        /* Sembunyikan tombol more di week & day view */
        .fc-timegrid .fc-daygrid-more-link,
        .fc-timegrid-event-harness .fc-daygrid-more-link {
            display: none !important;
        }

        /* Modal Bootstrap untuk pilih bulan */
        .month-year-modal .modal-content {
            border-radius: 16px;
            overflow: hidden;
        }

        .month-year-modal .modal-header {
            background: var(--navy);
            color: white;
            padding: 12px 20px;
            border: none;
        }

        .month-year-modal .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .month-year-modal .modal-body {
            padding: 20px;
        }

        .month-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .month-card {
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            background: #f8f9fa;
            font-weight: 500;
            font-size: 14px;
        }

        .month-card:hover {
            background: var(--navy);
            color: white;
            transform: translateY(-2px);
        }

        .month-card.active {
            background: var(--navy);
            color: white;
        }

        .year-selector {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
        }

        .year-nav {
            background: var(--navy);
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .year-nav:hover {
            background: var(--orange);
        }

        .current-year {
            font-size: 18px;
            font-weight: 700;
            color: var(--navy);
            min-width: 80px;
            text-align: center;
        }

        .modal-footer {
            padding: 12px 20px;
            border-top: 1px solid #e5e7eb;
        }

        /* Modal info ticket */
        .ticket-info-item {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .ticket-info-item:last-child {
            border-bottom: none;
        }

        .ticket-info-label {
            font-size: 10px;
            color: #999;
            margin-bottom: 3px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .ticket-info-value {
            font-size: 13px;
            color: #333;
            font-weight: 500;
        }

        .modal-warning {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 10px;
            border-radius: 8px;
            margin-top: 12px;
            font-size: 11px;
        }

        /* Status Badge Styles - Manual Colors */
        .status-badge-manual {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }

        /* ========== RESPONSIVE STYLES ========== */
        @media (max-width: 1024px) {
            .fc .fc-toolbar {
                flex-wrap: wrap;
            }

            .fc .fc-daygrid-day-frame {
                min-height: 85px;
            }

            .fc .fc-event {
                font-size: 9px;
                padding: 1px 4px;
            }
        }

        @media (max-width: 768px) {
            .fc {
                min-height: auto;
                max-height: none;
            }

            .fc .fc-view-harness {
                height: auto !important;
                min-height: auto !important;
                max-height: none !important;
                overflow-y: visible !important;
            }

            .fc .fc-scroller {
                max-height: none !important;
                overflow-y: visible !important;
            }

            .fc .fc-toolbar {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
            }

            .fc .fc-toolbar-title {
                font-size: 14px;
                order: 0;
                width: 100%;
                text-align: center;
                margin-bottom: 8px;
            }

            .fc .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
            }

            .fc .fc-button {
                padding: 4px 8px;
                font-size: 11px;
            }

            .fc .fc-daygrid-day-frame {
                min-height: 70px;
            }

            .fc .fc-daygrid-day-number {
                font-size: 11px;
                padding: 3px;
            }

            .fc .fc-col-header-cell-cushion {
                font-size: 9px;
                padding: 6px 2px;
            }

            .fc .fc-event {
                font-size: 8px;
                padding: 1px 3px;
                margin: 1px 2px;
                white-space: normal;
                word-break: break-word;
                line-height: 1.2;
            }

            .fc .fc-daygrid-more-link {
                font-size: 8px;
                padding: 1px 4px;
            }

            .filter-card {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .filter-title {
                font-size: 12px;
            }

            .filter-buttons {
                flex: 1;
                justify-content: flex-end;
            }

            .filter-btn {
                padding: 5px 10px;
                font-size: 11px;
            }

            .info-left {
                flex-direction: row;
                align-items: center;
            }

            .info-icon {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }

            .info-text h4 {
                font-size: 13px;
            }

            .info-text p {
                font-size: 10px;
            }

            .btn-print span {
                display: none;
            }

            .btn-print {
                padding: 8px 12px;
            }
        }

        @media (max-width: 576px) {
            .fc {
                min-height: auto;
                max-height: none;
            }

            .fc .fc-view-harness {
                height: auto !important;
                min-height: auto !important;
                max-height: none !important;
            }

            .fc .fc-scroller {
                max-height: none !important;
            }

            .fc .fc-toolbar-title {
                font-size: 13px;
            }

            .fc .fc-button {
                padding: 3px 6px;
                font-size: 10px;
            }

            .fc .fc-daygrid-day-frame {
                min-height: 60px;
            }

            .fc .fc-daygrid-day-number {
                font-size: 10px;
                padding: 2px;
            }

            .fc .fc-col-header-cell-cushion {
                font-size: 8px;
                padding: 5px 1px;
            }

            .fc .fc-event {
                font-size: 7px;
                padding: 2px 3px;
                margin: 1px 2px;
                white-space: normal;
                word-break: break-word;
                line-height: 1.3;
            }

            .fc .fc-daygrid-more-link {
                font-size: 7px;
                padding: 1px 3px;
            }

            .filter-btn span:not(.count) {
                font-size: 10px;
            }

            .filter-btn .count {
                font-size: 9px;
                padding: 1px 4px;
            }

            .filter-btn {
                padding: 4px 8px;
            }

            .info-left {
                gap: 8px;
            }

            .info-text p {
                display: none;
            }

            .month-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .month-card {
                padding: 8px;
                font-size: 13px;
            }
        }

        @media (max-width: 400px) {
            .fc .fc-daygrid-day-frame {
                min-height: 55px;
            }

            .fc .fc-daygrid-day-number {
                font-size: 9px;
                padding: 2px;
            }

            .fc .fc-event {
                font-size: 6px;
                padding: 1px 2px;
            }

            .filter-btn {
                padding: 4px 6px;
                gap: 3px;
            }

            .filter-btn i {
                font-size: 10px;
            }

            .filter-btn span:not(.count) {
                font-size: 9px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-0">
        <!-- Info Card -->
        <div class="info-card">
            <div class="info-left">
                <div class="info-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="info-text">
                    <h4 style="color: #eef2ff">Maintenance Request Calendar</h4>
                    <p>View all maintenance requests from all users. Click on any event to see details (access restrictions
                        apply).</p>
                </div>
            </div>
            <button class="btn-print" onclick="printCalendar()">
                <i class="fas fa-print me-1"></i>
                <span>Print</span>
            </button>
        </div>

        <!-- Filter Buttons -->
        <div class="filter-card">
            <div class="filter-title">
                <i class="fas fa-filter"></i>
                <span>Filter Events</span>
            </div>
            <div class="filter-buttons">
                <div class="filter-btn created active" id="filterCreated" onclick="toggleFilter('created')">
                    <i class="fas fa-plus-circle"></i>
                    <span>Created</span>
                    <span class="count" id="createdCount">0</span>
                </div>
                <div class="filter-btn closed active" id="filterClosed" onclick="toggleFilter('closed')">
                    <i class="fas fa-check-circle"></i>
                    <span>Closed</span>
                    <span class="count" id="closedCount">0</span>
                </div>
            </div>
        </div>

        <!-- Calendar Card -->
        <div class="card">
            <div class="card-body p-2 p-md-3">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Month/Year Picker Modal (Bootstrap) -->
    <div class="modal fade" id="monthYearModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-alt me-2"></i>Select Month & Year
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="month-grid" id="monthGrid">
                        <!-- Months akan diisi JS -->
                    </div>
                    <div class="year-selector">
                        <button type="button" class="year-nav" id="prevYear">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span class="current-year" id="currentYearDisplay">2026</span>
                        <button type="button" class="year-nav" id="nextYear">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" id="confirmGoToDate">Go to Date</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Ticket Info Modal -->
    <div class="modal fade" id="ticketInfoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-tools me-2"></i>Maintenance Request Information
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="ticket-info-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/fullcalendar/js/main.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>

    <script>
        let showCreated = true;
        let showClosed = true;
        let calendar = null;
        let currentUserRole = '{{ Auth::user()->role }}';
        let currentUserId = {{ Auth::id() }};
        let currentUserDeptId = {{ Auth::user()->department_id ?? 'null' }};
        let tippyInstances = new Map();
        let selectedYear = new Date().getFullYear();
        let selectedMonth = new Date().getMonth() + 1;
        let monthYearModal = null;
        let modalListenersAttached = false;
        let calendarObserver = null;

        // Priority data from database
        const priorities = @json($priorities);

        // Create priority color map
        const priorityColorMap = {};
        priorities.forEach(priority => {
            priorityColorMap[priority.name.toUpperCase()] = priority.color;
            priorityColorMap[priority.name] = priority.color;
        });

        function getPriorityColor(priorityName) {
            if (!priorityName) return '#003366';
            const upperName = priorityName.toUpperCase();
            return priorityColorMap[upperName] || priorityColorMap[priorityName] || '#003366';
        }

        const months = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        // ========== STATUS MAPPING ==========
        // Mapping dari display name ke status code asli (untuk tooltip)
        const statusCodeMap = {
            'Open': 'open',
            'Received': 'received',
            'OM Approval': 'pending_om',
            'In Progress': 'in_progress',
            'PR Approval': 'pending_vr',
            'Completed': 'completed',
            'GM Approval': 'pending_gm',
            'Ready for Closure': 'ready_for_closure',
            'Closed': 'closed',
            'Cancelled': 'cancelled'
        };

        // Mapping dari status code ke display name
        const statusDisplayMap = {
            'open': 'Open',
            'received': 'Received',
            'pending_om': 'OM Approval',
            'in_progress': 'In Progress',
            'pending_vr': 'PR Approval',
            'completed': 'Completed',
            'pending_gm': 'GM Approval',
            'ready_for_closure': 'Ready for Closure',
            'closed': 'Closed',
            'cancelled': 'Cancelled'
        };

        // Status color mapping berdasarkan status code (HEX)
        function getStatusColorByCode(statusCode) {
            const colors = {
                'open': '#003366',
                'received': '#17a2b8',
                'pending_om': '#ffc107',
                'in_progress': '#17a2b8',
                'pending_vr': '#ffc107',
                'completed': '#28a745',
                'pending_gm': '#ffc107',
                'ready_for_closure': '#17a2b8',
                'closed': '#343a40',
                'cancelled': '#dc3545'
            };
            return colors[statusCode] || '#6c757d';
        }

        // Status text color (warning status pakai teks gelap)
        function getStatusTextColorByCode(statusCode) {
            const warningStatuses = ['pending_om', 'pending_vr', 'pending_gm'];
            return warningStatuses.includes(statusCode) ? '#212529' : 'white';
        }

        // Get display name dari status code
        function getStatusDisplayName(statusCode) {
            return statusDisplayMap[statusCode] || statusCode;
        }

        // Get status code dari display name
        function getStatusCodeFromDisplay(displayName) {
            return statusCodeMap[displayName] || displayName?.toLowerCase() || 'open';
        }

        function destroyAllTippyInstances() {
            tippyInstances.forEach((instance, el) => {
                if (instance && instance.destroy) {
                    instance.destroy();
                }
            });
            tippyInstances.clear();
        }

        function applyTooltipsToAllEvents() {
            destroyAllTippyInstances();

            let events = document.querySelectorAll(
                '.fc-event, .fc-timegrid-event, .fc-daygrid-event, .fc-daygrid-more-link'
            );

            events.forEach(event => {
                if (event.classList.contains('fc-daygrid-more-link')) {
                    tippy(event, {
                        content: 'Click to see all maintenance requests on this day',
                        placement: 'auto',
                        duration: [200, 150]
                    });
                    return;
                }

                let ticketId = event.getAttribute('data-ticket-id');
                let ticketNumber = event.getAttribute('data-ticket-number');
                let title = event.getAttribute('data-title');
                let priority = event.getAttribute('data-priority');
                let priorityColor = event.getAttribute('data-priority-color') || getPriorityColor(priority);
                let statusDisplay = event.getAttribute('data-status');
                let createdBy = event.getAttribute('data-created-by');
                let department = event.getAttribute('data-department');
                let type = event.getAttribute('data-type');
                let eventDate = event.getAttribute('data-event-date');

                if (!ticketNumber) return;

                // 🔥 KONVERSI display name ke status code
                let statusCode = getStatusCodeFromDisplay(statusDisplay);
                let statusColor = getStatusColorByCode(statusCode);
                let statusTextColor = getStatusTextColorByCode(statusCode);
                let statusDisplayName = statusDisplay || getStatusDisplayName(statusCode);

                let accessMessage = '';
                let accessClass = '';

                if (currentUserRole === 'user') {
                    accessMessage = '⚠️ You can only view requests you created';
                    accessClass = 'access-denied';
                } else if (currentUserRole === 'technician') {
                    accessMessage = '⚠️ You can only view requests assigned to you';
                    accessClass = 'access-denied';
                } else if (currentUserRole === 'manager') {
                    accessMessage = 'ℹ️ Click to check if request is from your department';
                    accessClass = 'access-allowed';
                } else {
                    accessMessage = '✅ Click to view full details';
                    accessClass = 'access-allowed';
                }

                let tooltipContent = `
                    <div class="calendar-tooltip">
                        <div class="tooltip-title">
                            <span>🔧 ${ticketNumber}</span>
                            ${priority ? `<span class="priority-badge" style="background-color: ${priorityColor}">${priority}</span>` : ''}
                        </div>
                        <div class="tooltip-row">
                            <div class="tooltip-label">Title:</div>
                            <div class="tooltip-value">${title ? (title.substring(0, 45) + (title.length > 45 ? '...' : '')) : 'N/A'}</div>
                        </div>
                        <div class="tooltip-row">
                            <div class="tooltip-label">Status:</div>
                            <div class="tooltip-value">
                                <span class="status-badge-manual" style="background-color: ${statusColor}; color: ${statusTextColor};">${statusDisplayName}</span>
                            </div>
                        </div>
                        <div class="tooltip-row">
                            <div class="tooltip-label">${type === 'created' ? '📅 Created:' : '🔒 Closed:'}</div>
                            <div class="tooltip-value">${eventDate || 'N/A'}</div>
                        </div>
                        <div class="tooltip-row">
                            <div class="tooltip-label">👤 By:</div>
                            <div class="tooltip-value">${createdBy || 'Unknown'}</div>
                        </div>
                        <div class="access-warning ${accessClass}">
                            ${accessMessage}
                        </div>
                    </div>
                `;

                try {
                    let instance = tippy(event, {
                        content: tooltipContent,
                        allowHTML: true,
                        placement: 'auto',
                        theme: 'light-border',
                        maxWidth: 320,
                        delay: [400, 0],
                        animation: 'shift-away',
                        duration: [200, 150]
                    });
                    tippyInstances.set(event, instance);
                } catch (e) {}
            });
        }

        function storeEventDataToElement(el, eventProps) {
            if (!el) return;
            const priorityColor = eventProps.priority_color || getPriorityColor(eventProps.priority);

            el.setAttribute('data-ticket-id', eventProps.ticket_id || '');
            el.setAttribute('data-ticket-number', eventProps.ticket_number || '');
            el.setAttribute('data-title', eventProps.title_full || '');
            el.setAttribute('data-priority', eventProps.priority || '');
            el.setAttribute('data-priority-color', priorityColor);
            el.setAttribute('data-priority-level', eventProps.priority_level || '3');
            el.setAttribute('data-status', eventProps.status_display || '');
            el.setAttribute('data-created-by', eventProps.created_by || '');
            el.setAttribute('data-department', eventProps.department || '');
            el.setAttribute('data-type', eventProps.type || 'created');
            el.setAttribute('data-event-date', eventProps.event_date || '');
            el.setAttribute('data-priority-name', eventProps.priority || '');

            if (priorityColor) {
                el.style.borderLeftColor = priorityColor;
            }
        }

        function attachModalListeners() {
            if (modalListenersAttached) return;

            const prevYearBtn = document.getElementById('prevYear');
            const nextYearBtn = document.getElementById('nextYear');
            const confirmBtn = document.getElementById('confirmGoToDate');

            if (prevYearBtn) {
                prevYearBtn.addEventListener('click', () => {
                    selectedYear--;
                    updateYearDisplay();
                    updateMonthGrid();
                });
            }

            if (nextYearBtn) {
                nextYearBtn.addEventListener('click', () => {
                    selectedYear++;
                    updateYearDisplay();
                    updateMonthGrid();
                });
            }

            if (confirmBtn) {
                confirmBtn.addEventListener('click', goToSelectedDate);
            }

            modalListenersAttached = true;
        }

        function showMonthYearPicker() {
            if (!calendar) return;

            let currentDate = calendar.getDate();
            selectedYear = currentDate.getFullYear();
            selectedMonth = currentDate.getMonth() + 1;

            updateMonthGrid();
            updateYearDisplay();
            attachModalListeners();

            if (!monthYearModal) {
                monthYearModal = new bootstrap.Modal(document.getElementById('monthYearModal'));
            }
            monthYearModal.show();
        }

        function updateMonthGrid() {
            let grid = document.getElementById('monthGrid');
            if (!grid) return;

            grid.innerHTML = '';

            months.forEach((month, index) => {
                let monthNum = index + 1;
                let isActive = (monthNum === selectedMonth);
                let monthDiv = document.createElement('div');
                monthDiv.className = 'month-card' + (isActive ? ' active' : '');
                monthDiv.textContent = month.substring(0, 3);
                monthDiv.onclick = () => {
                    document.querySelectorAll('.month-card').forEach(c => c.classList.remove('active'));
                    monthDiv.classList.add('active');
                    selectedMonth = monthNum;
                };
                grid.appendChild(monthDiv);
            });
        }

        function updateYearDisplay() {
            const yearDisplay = document.getElementById('currentYearDisplay');
            if (yearDisplay) {
                yearDisplay.textContent = selectedYear;
            }
        }

        function goToSelectedDate() {
            if (calendar) {
                calendar.gotoDate(`${selectedYear}-${String(selectedMonth).padStart(2, '0')}-01`);
                if (monthYearModal) monthYearModal.hide();
            }
        }

        function loadFilterState() {
            let savedCreated = localStorage.getItem('calendar_filter_created');
            let savedClosed = localStorage.getItem('calendar_filter_closed');

            showCreated = savedCreated !== null ? savedCreated === 'true' : true;
            showClosed = savedClosed !== null ? savedClosed === 'true' : true;

            const filterCreated = document.getElementById('filterCreated');
            const filterClosed = document.getElementById('filterClosed');

            if (filterCreated) filterCreated.classList.toggle('active', showCreated);
            if (filterClosed) filterClosed.classList.toggle('active', showClosed);
        }

        function initCalendar() {
            var calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: new Date().toISOString().slice(0, 10),
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,dayGridDay'
                },
                height: 'auto',
                contentHeight: 'auto',
                aspectRatio: 1.35,
                firstDay: 1,
                navLinks: true,
                editable: false,
                selectable: true,
                weekends: true,
                handleWindowResize: true,
                windowResizeDelay: 100,

                views: {
                    dayGridMonth: {
                        dayMaxEvents: 2,
                        dayMaxEventRows: 2
                    },
                    timeGridWeek: {
                        dayMaxEvents: false,
                        eventDisplay: 'block'
                    },
                    timeGridDay: {
                        dayMaxEvents: false,
                        eventDisplay: 'block'
                    }
                },

                events: function(fetchInfo, successCallback, failureCallback) {
                    $.ajax({
                        url: '{{ route('calendar.events') }}',
                        method: 'GET',
                        data: {
                            show_created: showCreated,
                            show_closed: showClosed
                        },
                        success: function(data) {
                            successCallback(data);
                            updateEventCounts(data);
                            setTimeout(() => applyTooltipsToAllEvents(), 150);
                        },
                        error: function() {
                            toastr.error('Failed to load calendar events');
                            failureCallback();
                        }
                    });
                },

                eventDidMount: function(info) {
                    storeEventDataToElement(info.el, info.event.extendedProps);
                    let innerEvent = info.el.querySelector('.fc-event-main');
                    if (innerEvent) storeEventDataToElement(innerEvent, info.event.extendedProps);
                },

                eventClick: function(info) {
                    let ticketId = info.event.extendedProps.ticket_id;
                    if (ticketId) checkTicketAccess(ticketId);
                    return false;
                },

                datesSet: function() {
                    setTimeout(() => applyTooltipsToAllEvents(), 150);
                },

                loading: function(isLoading) {
                    if (!isLoading) setTimeout(() => applyTooltipsToAllEvents(), 200);
                }
            });

            calendar.render();

            setTimeout(() => {
                let titleEl = document.querySelector('.fc-toolbar-title');
                if (titleEl && !titleEl.hasClickListener) {
                    titleEl.hasClickListener = true;
                    titleEl.addEventListener('click', function(e) {
                        e.stopPropagation();
                        showMonthYearPicker();
                    });
                }
            }, 500);

            if (calendarObserver) {
                calendarObserver.disconnect();
            }

            calendarObserver = new MutationObserver(function() {
                setTimeout(() => applyTooltipsToAllEvents(), 100);
            });
            calendarObserver.observe(calendarEl, {
                childList: true,
                subtree: true
            });
        }

        function toggleFilter(type) {
            if (type === 'created') {
                showCreated = !showCreated;
                localStorage.setItem('calendar_filter_created', showCreated);
                const filterCreated = document.getElementById('filterCreated');
                if (filterCreated) filterCreated.classList.toggle('active', showCreated);
            } else if (type === 'closed') {
                showClosed = !showClosed;
                localStorage.setItem('calendar_filter_closed', showClosed);
                const filterClosed = document.getElementById('filterClosed');
                if (filterClosed) filterClosed.classList.toggle('active', showClosed);
            }

            if (calendar) {
                destroyAllTippyInstances();
                calendar.refetchEvents();
            }
        }

        function updateEventCounts(events) {
            let createdCount = events.filter(e => e.type === 'created').length;
            let closedCount = events.filter(e => e.type === 'closed').length;

            const createdCountEl = document.getElementById('createdCount');
            const closedCountEl = document.getElementById('closedCount');

            if (createdCountEl) createdCountEl.textContent = createdCount;
            if (closedCountEl) closedCountEl.textContent = closedCount;
        }

        function checkTicketAccess(ticketId) {
            let url = '{{ route('calendar.check-access', ':id') }}'.replace(':id', ticketId);

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.type === 'modal_info') showTicketInfoModal(data.ticket_info);
                    else if (data.type === 'redirect') window.location.href = data.url;
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.location.href = '{{ route('tickets.show', ':id') }}'.replace(':id', ticketId);
                });
        }

        function showTicketInfoModal(ticketInfo) {
            let statusCode = getStatusCodeFromDisplay(ticketInfo.status_display);
            let statusColor = getStatusColorByCode(statusCode);
            let statusTextColor = getStatusTextColorByCode(statusCode);
            let statusDisplayName = ticketInfo.status_display || getStatusDisplayName(statusCode);
            let priorityColor = ticketInfo.priority_color || getPriorityColor(ticketInfo.priority);

            let modalContent = `
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Request Number</div>
                    <div class="ticket-info-value">
                        <strong>${escapeHtml(ticketInfo.number)}</strong>
                        <span class="status-badge-manual" style="background-color: ${statusColor}; color: ${statusTextColor}; margin-left: 8px;">${escapeHtml(statusDisplayName)}</span>
                    </div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Title</div>
                    <div class="ticket-info-value">${escapeHtml(ticketInfo.title)}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Priority</div>
                    <div class="ticket-info-value">
                        <span class="status-badge-manual" style="background-color: ${priorityColor}; color: white;">${escapeHtml(ticketInfo.priority || 'N/A')}</span>
                    </div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Created By</div>
                    <div class="ticket-info-value">${escapeHtml(ticketInfo.created_by)}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Department</div>
                    <div class="ticket-info-value">${escapeHtml(ticketInfo.department)}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Category</div>
                    <div class="ticket-info-value">${escapeHtml(ticketInfo.category)}</div>
                </div>
                <div class="ticket-info-item">
                    <div class="ticket-info-label">Created At</div>
                    <div class="ticket-info-value">${escapeHtml(ticketInfo.created_at)}</div>
                </div>
                <div class="modal-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Access Restricted:</strong> ${escapeHtml(ticketInfo.reason)}
                </div>
            `;

            const ticketInfoContent = document.querySelector('.ticket-info-content');
            if (ticketInfoContent) ticketInfoContent.innerHTML = modalContent;

            const ticketInfoModal = new bootstrap.Modal(document.getElementById('ticketInfoModal'));
            ticketInfoModal.show();
        }

        function printCalendar() {
            if (!calendar) return;
            let currentDate = calendar.getDate();
            let year = currentDate.getFullYear();
            let month = currentDate.getMonth() + 1;
            window.open('{{ route('calendar.print') }}?year=' + year + '&month=' + month, '_blank');
        }

        function escapeHtml(text) {
            if (!text) return '';
            let div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        $(document).ready(function() {
            loadFilterState();
            initCalendar();

            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
        });

        window.addEventListener('beforeunload', function() {
            if (calendarObserver) {
                calendarObserver.disconnect();
            }
            destroyAllTippyInstances();
        });
    </script>
@endpush
