{{-- views/admin/users/index.blade.php --}}
@extends('layouts.main')

@section('title', 'User Management | ' . config('app.name'))

@section('page-title', 'User Management')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Admin', 'url' => 'javascript:void(0)'],
            ['title' => 'User Management', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* ============= THEME COLORS - PUTIH COK! ============= */
        :root {
            --primary-orange: #FF6B35;
            --primary-orange-dark: #E54B1F;
            --primary-orange-light: #FF8B5C;
            --primary-navy: #0A1929;
            --bg-white: #FFFFFF;
            --bg-light: #F8F9FA;
            --border-color: #E9ECEF;
            --text-dark: #212529;
            --text-muted: #6C757D;
            --text-light: #FFFFFF;
            --hover-light: #FFF3F0;
        }

        /* ============= STATISTICS CARDS - PUTIH ============= */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: var(--bg-white);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .stats-card:hover {
            border-color: var(--primary-orange);
            box-shadow: 0 8px 16px rgba(255, 107, 53, 0.1);
            transform: translateY(-2px);
        }

        .stats-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            background: rgba(255, 107, 53, 0.08);
        }

        .stats-icon i {
            font-size: 24px;
            color: var(--primary-orange);
        }

        .stats-content h6 {
            margin-bottom: 6px;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stats-content h3 {
            margin-bottom: 0;
            font-size: 26px;
            font-weight: 700;
            color: var(--text-dark);
        }

        /* ============= COMBOBOX FILTER ============= */
        .filter-section {
            background: var(--bg-white);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
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

        .filter-select {
            width: 100%;
            height: 40px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 13px;
            transition: all 0.2s ease;
            background-color: white;
        }

        .filter-select:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 2px rgba(255, 107, 53, 0.1);
            outline: none;
        }

        .filter-select2 {
            width: 100% !important;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-filter {
            background: var(--primary-orange);
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
        }

        .btn-filter:hover {
            background: var(--primary-orange-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(255, 107, 53, 0.3);
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
        }

        .btn-reset:hover {
            background: var(--primary-orange);
            border-color: var(--primary-orange);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(255, 107, 53, 0.3);
        }

        /* ============= QUICK FILTER BADGES (YANG NO RELOAD) ============= */
        .quick-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
            padding: 16px;
            background: var(--bg-light);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .quick-filter-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background: var(--bg-white);
            border-radius: 40px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid var(--border-color);
            cursor: pointer;
        }

        .quick-filter-badge i {
            margin-right: 8px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .quick-filter-badge:hover {
            background: var(--hover-light);
            border-color: var(--primary-orange);
            color: var(--primary-orange-dark);
        }

        .quick-filter-badge:hover i {
            color: var(--primary-orange);
        }

        .quick-filter-badge.active {
            background: var(--primary-orange);
            border-color: var(--primary-orange);
            color: white;
        }

        .quick-filter-badge.active i {
            color: white;
        }

        .quick-filter-badge .badge-count {
            margin-left: 8px;
            padding: 2px 8px;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .quick-filter-badge.active .badge-count {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* ============= LOADING OVERLAY ============= */
        .table-loading {
            position: relative;
            min-height: 200px;
        }

        .table-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        .table-loading::before {
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

        /* ============= DATATABLE STYLING - PUTIH ============= */
        #usersTable_wrapper {
            color: var(--text-dark);
        }

        #usersTable_wrapper .dataTables_length,
        #usersTable_wrapper .dataTables_filter,
        #usersTable_wrapper .dataTables_info,
        #usersTable_wrapper .dataTables_paginate {
            padding: 20px 0;
            font-size: 14px;
        }

        #usersTable_wrapper .dataTables_length label {
            color: var(--text-muted);
            font-weight: 500;
        }

        #usersTable_wrapper .dataTables_length select {
            padding: 8px 35px 8px 15px;
            font-size: 14px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            background-color: var(--bg-white);
            color: var(--text-dark);
            margin: 0 10px;
        }

        #usersTable_wrapper .dataTables_filter label {
            color: var(--text-muted);
            font-weight: 500;
        }

        #usersTable_wrapper .dataTables_filter input {
            padding: 10px 20px;
            font-size: 14px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            width: 280px;
            margin-left: 10px;
            background: var(--bg-white);
            color: var(--text-dark);
        }

        #usersTable_wrapper .dataTables_filter input:focus {
            border-color: var(--primary-orange);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        #usersTable_wrapper .dataTables_filter input::placeholder {
            color: #ADB5BD;
        }

        #usersTable_wrapper .dataTables_info {
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Pagination - PUTIH */
        #usersTable_wrapper .dataTables_paginate {
            float: right;
        }

        #usersTable_wrapper .dataTables_paginate .paginate_button {
            padding: 8px 16px;
            margin: 0 3px;
            border: 1px solid var(--border-color);
            background: var(--bg-white);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark) !important;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        #usersTable_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--hover-light) !important;
            border-color: var(--primary-orange);
            color: var(--primary-orange-dark) !important;
        }

        #usersTable_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-orange) !important;
            border-color: var(--primary-orange);
            color: white !important;
        }

        #usersTable_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: var(--bg-light);
            color: var(--text-muted) !important;
        }

        #usersTable_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: var(--bg-light);
            border-color: var(--border-color);
            color: var(--text-muted) !important;
        }

        /* Table - PUTIH BANGET */
        #usersTable {
            font-size: 14px;
            width: 100% !important;
            border-collapse: collapse;
        }

        #usersTable thead th {
            font-size: 14px;
            font-weight: 600;
            padding: 16px 12px;
            background-color: var(--bg-light) !important;
            border-bottom: 2px solid var(--primary-orange) !important;
            color: var(--text-dark);
            text-align: center;
            vertical-align: middle;
        }

        #usersTable tbody td {
            padding: 14px 12px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-white);
            color: var(--text-dark);
        }

        #usersTable tbody tr:hover td {
            background: var(--hover-light);
        }

        /* Profile Image */
        .profile-img {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border: 2px solid var(--bg-white);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Badge Styling */
        .badge {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 30px;
            letter-spacing: 0.3px;
        }

        .badge-superadmin {
            background: #212529;
            color: white;
        }

        .badge-admin_eng {
            background: #DC3545;
            color: white;
        }

        .badge-gm,
        .badge-om,
        .badge-manager {
            background: var(--primary-orange);
            color: white;
        }

        .badge-technician {
            background: #17A2B8;
            color: white;
        }

        .badge-user {
            background: #6C757D;
            color: white;
        }

        .badge-active {
            background: #28A745;
            color: white;
        }

        .badge-inactive {
            background: #DC3545;
            color: white;
        }

        .badge-pending {
            background: #FFC107;
            color: #212529;
        }

        .badge-info {
            background: #17A2B8;
            color: white;
        }

        /* Button Action */
        .btn-xs.sharp {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--bg-white);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            transition: all 0.2s ease;
        }

        .btn-xs.sharp:hover {
            background: var(--primary-orange);
            border-color: var(--primary-orange);
            color: white;
        }

        .btn-warning.btn-xs.sharp {
            background: #FFF3E0;
            border-color: #FFE0B2;
            color: #E65100;
        }

        .btn-warning.btn-xs.sharp:hover {
            background: #FFB74D;
            border-color: #FF9800;
            color: white;
        }

        .btn-success.btn-xs.sharp {
            background: #E8F5E9;
            border-color: #C8E6C9;
            color: #2E7D32;
        }

        .btn-success.btn-xs.sharp:hover {
            background: #66BB6A;
            border-color: #43A047;
            color: white;
        }

        .btn-danger.btn-xs.sharp {
            background: #FFEBEE;
            border-color: #FFCDD2;
            color: #C62828;
        }

        .btn-danger.btn-xs.sharp:hover {
            background: #EF5350;
            border-color: #E53935;
            color: white;
        }

        /* Verified Icons */
        .verified-icon {
            color: #28A745;
            margin-left: 5px;
        }

        .unverified-icon {
            color: #FFC107;
            margin-left: 5px;
        }

        /* Modal Styling - PUTIH */
        .modal-content {
            background: var(--bg-white);
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        .modal-header {
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-color);
            padding: 20px 24px;
        }

        .modal-header h5 {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 18px;
        }

        .modal-header .btn-close {
            filter: none;
            opacity: 0.5;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 20px 24px;
        }

        .form-label {
            color: var(--text-dark);
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-control {
            background: var(--bg-white);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-dark);
            padding: 10px 16px;
            font-size: 14px;
        }

        .form-control:focus {
            background: var(--bg-white);
            border-color: var(--primary-orange);
            color: var(--text-dark);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: #ADB5BD;
        }

        select.form-control {
            background: var(--bg-white);
        }

        /* Card Styling - PUTIH */
        .card {
            background: var(--bg-white);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .card-header {
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-color);
            padding: 20px 24px;
        }

        .card-header h4 {
            color: var(--text-dark);
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 24px;
        }

        .btn-primary {
            background: var(--primary-orange);
            border-color: var(--primary-orange);
            color: white;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 10px;
        }

        .btn-primary:hover {
            background: var(--primary-orange-dark);
            border-color: var(--primary-orange-dark);
        }

        .btn-secondary {
            background: #6C757D !important;
            border-color: #5A6268 !important;
            color: white !important;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 10px;
        }

        .btn-secondary:hover {
            background: #5A6268 !important;
            border-color: #545B62 !important;
            color: white !important;
        }

        .btn-info {
            background: #E3F2FD;
            border-color: #BBDEFB;
            color: #0277BD;
        }

        .btn-info:hover {
            background: #4FC3F7;
            border-color: #29B6F6;
            color: white;
        }

        /* ============= RESPONSIVE ============= */
        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
            }
        }

        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .filter-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
            }

            .stats-card {
                padding: 16px;
            }

            .stats-icon {
                width: 44px;
                height: 44px;
                margin-right: 12px;
            }

            .stats-icon i {
                font-size: 20px;
            }

            .stats-content h6 {
                font-size: 11px;
            }

            .stats-content h3 {
                font-size: 20px;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                flex-direction: column;
            }

            .quick-filters {
                flex-direction: column;
                gap: 8px;
            }

            .quick-filter-badge {
                width: 100%;
                justify-content: center;
            }

            #usersTable_wrapper .dataTables_filter input {
                width: 100%;
                margin-left: 0;
                margin-top: 10px;
            }

            #usersTable_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                margin-top: 20px;
            }

            .table-responsive {
                overflow-x: auto;
            }

            #usersTable thead th {
                font-size: 13px;
                padding: 12px 8px;
                white-space: nowrap;
            }

            #usersTable tbody td {
                padding: 12px 8px;
                white-space: nowrap;
            }

            .btn-xs.sharp {
                width: 32px;
                height: 32px;
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .stats-card {
                padding: 12px;
            }

            .stats-icon {
                width: 40px;
                height: 40px;
                margin-right: 10px;
            }

            .stats-icon i {
                font-size: 18px;
            }

            .stats-content h6 {
                font-size: 10px;
            }

            .stats-content h3 {
                font-size: 18px;
            }

            .filter-section {
                padding: 16px;
            }

            .card-header {
                padding: 16px;
                flex-direction: column;
                gap: 12px;
            }

            .card-header div {
                width: 100%;
                display: flex;
                gap: 8px;
            }

            .card-header .btn {
                flex: 1;
            }
        }

        /* Invalid feedback */
        .is-invalid {
            border-color: #DC3545 !important;
        }

        .invalid-feedback {
            display: block;
            color: #DC3545;
            font-size: 12px;
            margin-top: 4px;
        }

        /* Text colors */
        .text-muted {
            color: #6C757D !important;
        }

        .text-success {
            color: #28A745 !important;
        }

        .text-warning {
            color: #FFC107 !important;
        }

        .text-danger {
            color: #DC3545 !important;
        }

        /* Container */
        .container-fluid {
            padding: 0 30px;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding: 0 20px;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding: 0 16px;
            }
        }

        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            height: 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 12px;
            font-size: 13px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            right: 8px;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 2px rgba(255, 107, 53, 0.1);
        }
    </style>
@endpush

@section('content')
    <!-- Statistics Cards - TETAP KESAMPING DI MOBILE -->
    <div class="stats-grid">
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-content">
                <h6>Total Users</h6>
                <h3>{{ $statistics['total_users'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-content">
                <h6>Active</h6>
                <h3>{{ $statistics['active_users'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-content">
                <h6>Pending</h6>
                <h3>{{ $statistics['pending_users'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-ban"></i>
            </div>
            <div class="stats-content">
                <h6>Inactive</h6>
                <h3>{{ $statistics['inactive_users'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-tools"></i>
            </div>
            <div class="stats-content">
                <h6>Technicians</h6>
                <h3>{{ $statistics['technician_count'] ?? 0 }}</h3>
            </div>
        </div>
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stats-content">
                <h6>Managers</h6>
                <h3>{{ $statistics['manager_count'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <!-- QUICK FILTERS - YANG NO RELOAD (All Users, Pending, Inactive, Unverified) -->
    <div class="quick-filters">
        <!-- Hapus ?quick_filter=all dari href -->
        <a href="{{ route('admin.users.index') }}"
            class="quick-filter-badge {{ !request()->has('status') && !request()->has('verified') ? 'active' : '' }}"
            data-filter="all">
            <i class="fas fa-users"></i> All Users
            <span class="badge-count">{{ $statistics['total_users'] ?? 0 }}</span>
        </a>
        <a href="{{ route('admin.users.index') }}?status=pending"
            class="quick-filter-badge {{ request()->get('status') == 'pending' ? 'active' : '' }}" data-filter="pending">
            <i class="fas fa-clock"></i> Pending
            @if ($filterCounts['pending_approval'] > 0)
                <span class="badge-count">{{ $filterCounts['pending_approval'] }}</span>
            @endif
        </a>
        <a href="{{ route('admin.users.index') }}?status=inactive"
            class="quick-filter-badge {{ request()->get('status') == 'inactive' ? 'active' : '' }}" data-filter="inactive">
            <i class="fas fa-ban"></i> Inactive
            @if ($filterCounts['inactive_users'] > 0)
                <span class="badge-count">{{ $filterCounts['inactive_users'] }}</span>
            @endif
        </a>
        <a href="{{ route('admin.users.index') }}?verified=unverified"
            class="quick-filter-badge {{ request()->get('verified') == 'unverified' ? 'active' : '' }}"
            data-filter="unverified">
            <i class="fas fa-envelope"></i> Unverified
            @if ($filterCounts['unverified_users'] > 0)
                <span class="badge-count">{{ $filterCounts['unverified_users'] }}</span>
            @endif
        </a>
    </div>
    <!-- COMBOBOX FILTER SECTION -->
    <div class="filter-section">
        <div class="filter-grid">
            <!-- Role Filter -->
            <div class="filter-item">
                <label class="filter-label">Role</label>
                <select name="role" id="roleFilter" class="filter-select filter-select2" multiple="multiple">
                    <option value="superadmin">Super Admin</option>
                    <option value="admin_eng">Admin Engineering</option>
                    <option value="gm">General Manager</option>
                    <option value="om">Operational Manager</option>
                    <option value="manager">Department Manager</option>
                    <option value="technician">Technician</option>
                    <option value="user">User</option>
                </select>
            </div>

            <!-- Department Filter -->
            <div class="filter-item">
                <label class="filter-label">Department</label>
                <select name="department_id" id="departmentFilter" class="filter-select filter-select2" multiple="multiple">
                    <option value="">All Departments</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Signature Filter -->
            <div class="filter-item">
                <label class="filter-label">Signature</label>
                <select name="signature" id="signatureFilter" class="filter-select">
                    <option value="">All</option>
                    <option value="has">Has Signature</option>
                    <option value="none">No Signature</option>
                </select>
            </div>

            <!-- Verified Filter -->
            <div class="filter-item">
                <label class="filter-label">Email Verification</label>
                <select name="verified" id="verifiedFilter" class="filter-select">
                    <option value="">All</option>
                    <option value="verified">Verified</option>
                    <option value="unverified">Unverified</option>
                </select>
            </div>
        </div>

        <div class="filter-actions">
            <button type="button" class="btn-filter" id="applyFiltersBtn">
                <i class="fas fa-filter"></i> Apply Filters
            </button>
            <button type="button" class="btn-reset" id="resetFiltersBtn">
                <i class="fas fa-redo-alt"></i> Reset Filters
            </button>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addUserForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="Enter full name"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" placeholder="Enter email"
                                    required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone"
                                    placeholder="Enter phone number">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" name="profile_picture" accept="image/*">
                                <small class="text-muted">Max: 2MB (jpg, png, jpeg, gif)</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password" placeholder="Enter password"
                                    required>
                                <small class="text-muted">Min 8 characters</small>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" name="password_confirmation"
                                    placeholder="Confirm password" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-control" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="superadmin">Super Administrator</option>
                                    <option value="admin_eng">Admin Engineering</option>
                                    <option value="gm">General Manager</option>
                                    <option value="om">Operational Manager</option>
                                    <option value="technician">Technician</option>
                                    <option value="manager">Department Manager</option>
                                    <option value="user">User</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-control" name="department_id">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editUserForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_user_id" name="user_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" name="profile_picture" accept="image/*">
                                <small class="text-muted">Leave empty to keep current</small>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" id="edit_password" name="password"
                                    placeholder="Leave blank to keep current">
                                <small class="text-muted">Min 8 characters if changing</small>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="edit_password_confirmation"
                                    name="password_confirmation" placeholder="Confirm new password">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="superadmin">Super Administrator</option>
                                    <option value="admin_eng">Admin Engineering</option>
                                    <option value="gm">General Manager</option>
                                    <option value="om">Operational Manager</option>
                                    <option value="technician">Technician</option>
                                    <option value="manager">Department Manager</option>
                                    <option value="user">User</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-control" id="edit_department_id" name="department_id">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Activate Pending User Modal -->
    <div class="modal fade" id="activatePendingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Activate Pending User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Activate user: <strong id="pendingUserName"></strong></p>
                    <p class="text-muted mb-3">You can assign a department now or skip to assign later.</p>

                    <form id="activatePendingForm">
                        @csrf
                        <input type="hidden" id="pending_user_id" name="user_id">

                        <div class="mb-3">
                            <label class="form-label">Department <span class="text-muted">(Optional)</span></label>
                            <select class="form-control" id="pending_department_id" name="department_id">
                                <option value="">-- Skip for now (no department) --</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">You can assign department now or later via edit</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmActivatePending">
                        <i class="fas fa-check-circle me-1"></i> Activate User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">User List</h4>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm me-2" data-bs-toggle="modal"
                            data-bs-target="#addUserModal">
                            <i class="fas fa-plus me-1"></i> Add User
                        </button>
                        <button type="button" class="btn btn-info btn-sm" id="exportUsersBtn" style="color: navy">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="usersTableContainer">
                        @include('admin.users.partials.user-rows', ['users' => $users])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Toastr Configuration
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "progressBarColor": "#FF6B35"
            };

            // Initialize Select2 for multiple selects
            $('.filter-select2').select2({
                width: '100%',
                placeholder: 'Select options',
                allowClear: true,
                closeOnSelect: false
            });

            // Set initial values from URL
            setInitialFilterValues();

            // ============ DATATABLE ============
            var table = $('#usersTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search users...",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "zeroRecords": "No matching records found",
                    "emptyTable": "No data available in table",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "<i class='fa fa-angle-right'></i>",
                        "previous": "<i class='fa fa-angle-left'></i>"
                    }
                },
                "columnDefs": [{
                        "orderable": false,
                        "targets": [1, 10]
                    },
                    {
                        "className": "text-center",
                        "targets": [0, 1, 5, 6, 7, 8, 9, 10]
                    }
                ],
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                "drawCallback": function(settings) {
                    var api = this.api();
                    if (api.rows().count() === 0) {
                        $(this).find('tbody').html(
                            '<tr><td colspan="11" class="text-center" style="color: #6C757D; padding: 40px;">No users found</td></tr>'
                        );
                    }
                }
            });

            // ============ FILTER FUNCTIONS ============
            function setInitialFilterValues() {
                // Get URL parameters
                const urlParams = new URLSearchParams(window.location.search);

                // Set role filter
                const roles = urlParams.getAll('role[]');
                if (roles.length > 0) {
                    $('#roleFilter').val(roles).trigger('change');
                }

                // Set department filter
                const depts = urlParams.getAll('department_id[]');
                if (depts.length > 0) {
                    $('#departmentFilter').val(depts).trigger('change');
                }

                // Set signature filter
                const signature = urlParams.get('signature');
                if (signature) {
                    $('#signatureFilter').val(signature).trigger('change');
                }

                // Set verified filter
                const verified = urlParams.get('verified');
                if (verified) {
                    $('#verifiedFilter').val(verified).trigger('change');
                }
            }

            function getFilterParams() {
                let params = {};

                // Role filter (multiple)
                let roles = $('#roleFilter').val();
                if (roles && roles.length > 0) {
                    params['role[]'] = roles;
                }

                // Department filter (multiple)
                let depts = $('#departmentFilter').val();
                if (depts && depts.length > 0) {
                    params['department_id[]'] = depts;
                }

                // Signature filter
                let signature = $('#signatureFilter').val();
                if (signature) {
                    params['signature'] = signature;
                }

                // Verified filter
                let verified = $('#verifiedFilter').val();
                if (verified) {
                    params['verified'] = verified;
                }

                // Add quick filter params from URL
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('status')) {
                    params['status'] = urlParams.get('status');
                }

                return params;
            }

            function applyFilters() {
                let params = getFilterParams();

                // Add loading state
                $('#usersTableContainer').addClass('table-loading');

                // Build query string
                let queryString = $.param(params);
                let url = '{{ route('admin.users.index') }}?' + queryString + '&filter=1';

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update table content
                            $('#usersTableContainer').html(response.html);

                            // Update URL without refresh
                            window.history.pushState({}, '', '{{ route('admin.users.index') }}?' +
                                queryString);

                            // Reinitialize DataTable
                            $('#usersTable').DataTable().destroy();
                            $('#usersTable').DataTable({
                                "pageLength": 10,
                                "ordering": true,
                                "searching": true,
                                "lengthMenu": [
                                    [10, 25, 50, -1],
                                    [10, 25, 50, "All"]
                                ],
                                "language": {
                                    "search": "_INPUT_",
                                    "searchPlaceholder": "Search users...",
                                    "lengthMenu": "Show _MENU_ entries",
                                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                                    "infoFiltered": "(filtered from _MAX_ total entries)",
                                    "zeroRecords": "No matching records found",
                                    "emptyTable": "No data available in table",
                                    "paginate": {
                                        "first": "First",
                                        "last": "Last",
                                        "next": "<i class='fa fa-angle-right'></i>",
                                        "previous": "<i class='fa fa-angle-left'></i>"
                                    }
                                },
                                "columnDefs": [{
                                        "orderable": false,
                                        "targets": [1, 10]
                                    },
                                    {
                                        "className": "text-center",
                                        "targets": [0, 1, 5, 6, 7, 8, 9, 10]
                                    }
                                ]
                            });

                            // Update active states in quick filters
                            updateQuickFilterActiveState();
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to apply filters');
                        console.error(xhr);
                    },
                    complete: function() {
                        $('#usersTableContainer').removeClass('table-loading');
                    }
                });
            }

            function resetFilters() {
                // Reset all select inputs
                $('#roleFilter').val(null).trigger('change');
                $('#departmentFilter').val(null).trigger('change');
                $('#signatureFilter').val('').trigger('change');
                $('#verifiedFilter').val('').trigger('change');

                // Remove status from URL if exists
                let url = '{{ route('admin.users.index') }}';
                window.location.href = url;
            }

            function updateQuickFilterActiveState() {
                const urlParams = new URLSearchParams(window.location.search);

                $('.quick-filter-badge').removeClass('active');

                if (!urlParams.has('status') && !urlParams.has('verified')) {
                    $('[data-filter="all"]').addClass('active');
                }
                if (urlParams.get('status') === 'pending') {
                    $('[data-filter="pending"]').addClass('active');
                }
                if (urlParams.get('status') === 'inactive') {
                    $('[data-filter="inactive"]').addClass('active');
                }
                if (urlParams.get('verified') === 'unverified') {
                    $('[data-filter="unverified"]').addClass('active');
                }
            }

            // ============ EVENT HANDLERS ============
            $('#applyFiltersBtn').on('click', function() {
                applyFilters();
            });

            $('#resetFiltersBtn').on('click', function() {
                resetFilters();
            });

            // Handle quick filter clicks (no reload)
            $('.quick-filter-badge').on('click', function(e) {
                e.preventDefault();
                let href = $(this).attr('href');
                window.location.href = href; // These will reload because they're the 4 specified filters
            });

            // Handle browser back/forward buttons
            window.onpopstate = function() {
                location.reload();
            };

            // ============ ADD USER ============
            $('#addUserForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.users.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#addUserModal').modal('hide');
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1500);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $('.is-invalid').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            $.each(errors, function(field, messages) {
                                var input = $('[name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(messages[0]);
                            });

                            toastr.error('Please check the form for errors');
                        } else {
                            toastr.error('Failed to create user');
                        }
                    }
                });
            });

            // ============ EDIT USER ============
            $(document).on('click', '.edit-user', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var email = $(this).data('email');
                var phone = $(this).data('phone');
                var role = $(this).data('role');
                var status = $(this).data('status');
                var departmentId = $(this).data('department-id');

                $('#edit_user_id').val(id);
                $('#edit_name').val(name);
                $('#edit_email').val(email);
                $('#edit_phone').val(phone);
                $('#edit_role').val(role);
                $('#edit_status').val(status);
                $('#edit_department_id').val(departmentId || '');
                $('#edit_password, #edit_password_confirmation').val('');

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#editUserModal').modal('show');
            });

            // ============ EDIT USER SUBMIT ============
            $('#editUserForm').on('submit', function(e) {
                e.preventDefault();

                var userId = $('#edit_user_id').val();
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('admin.users.update', ':id') }}".replace(':id', userId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#editUserModal').modal('hide');
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1500);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $('.is-invalid').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            $.each(errors, function(field, messages) {
                                var input = $('#editUserForm [name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(messages[0]);
                            });

                            toastr.error('Please check the form for errors');
                        } else {
                            toastr.error('Failed to update user');
                        }
                    }
                });
            });

            // ============ TOGGLE STATUS ============
            $(document).on('click', '.toggle-status', function() {
                var userId = $(this).data('id');
                var userName = $(this).data('name');
                var currentStatus = $(this).data('status');
                var actionText = currentStatus === 'active' ? 'deactivate' : 'activate';

                Swal.fire({
                    title: `${actionText.charAt(0).toUpperCase() + actionText.slice(1)} User?`,
                    html: `Are you sure you want to <strong>${actionText}</strong> <strong>${userName}</strong>'s account?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: currentStatus === 'active' ? '#DC3545' : '#28A745',
                    cancelButtonColor: '#6C757D',
                    confirmButtonText: `Yes, ${actionText}!`,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.users.toggle-status', ':id') }}".replace(
                                ':id', userId),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => location.reload());
                                }
                            },
                            error: function() {
                                toastr.error('Failed to update status');
                            }
                        });
                    }
                });
            });

            // ============ HANDLE PENDING USER ACTIVATION ============
            $(document).on('click', '.activate-pending', function() {
                var userId = $(this).data('id');
                var userName = $(this).data('name');

                $('#pending_user_id').val(userId);
                $('#pendingUserName').text(userName);
                $('#pending_department_id').val('');
                $('#activatePendingModal').modal('show');
            });

            // Confirm activation with optional department
            $('#confirmActivatePending').on('click', function() {
                var userId = $('#pending_user_id').val();
                var departmentId = $('#pending_department_id').val();

                Swal.fire({
                    title: 'Activate User?',
                    html: `Activate <strong>${$('#pendingUserName').text()}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28A745',
                    cancelButtonColor: '#6C757D',
                    confirmButtonText: 'Yes, activate!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.users.activate-with-department', ':id') }}"
                                .replace(':id', userId),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                department_id: departmentId
                            },
                            success: function(response) {
                                if (response.success) {
                                    $('#activatePendingModal').modal('hide');
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Activated!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => location.reload());
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    var errors = xhr.responseJSON.errors;
                                    var errorMsg = 'Validation error: ' + Object.values(
                                        errors).join(', ');
                                    toastr.error(errorMsg);
                                } else {
                                    toastr.error(xhr.responseJSON?.message ||
                                        'Failed to activate user');
                                }
                            }
                        });
                    }
                });
            });

            // Quick activate without modal (skip department)
            $(document).on('click', '.quick-activate-pending', function() {
                var userId = $(this).data('id');
                var userName = $(this).data('name');

                Swal.fire({
                    title: 'Quick Activate?',
                    html: `Activate <strong>${userName}</strong> without department?<br><small class="text-muted">You can assign department later via edit</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28A745',
                    cancelButtonColor: '#6C757D',
                    confirmButtonText: 'Yes, activate',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.users.activate-with-department', ':id') }}"
                                .replace(':id', userId),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                department_id: ''
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Activated!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => location.reload());
                                }
                            },
                            error: function() {
                                toastr.error('Failed to activate user');
                            }
                        });
                    }
                });
            });

            // ============ DELETE USER ============
            $(document).on('click', '.delete-user', function() {
                var userId = $(this).data('id');
                var userName = $(this).data('name');

                Swal.fire({
                    title: 'Delete User?',
                    html: `You are about to delete <strong>${userName}</strong>.<br>This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DC3545',
                    cancelButtonColor: '#6C757D',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Enter Password',
                            html: `<input type="password" id="admin_password" class="swal2-input" placeholder="Your password">`,
                            showCancelButton: true,
                            confirmButtonText: 'Confirm Delete',
                            cancelButtonText: 'Cancel',
                            preConfirm: () => {
                                const password = Swal.getPopup().querySelector(
                                    '#admin_password').value;
                                if (!password) {
                                    Swal.showValidationMessage('Password is required');
                                    return false;
                                }
                                return $.ajax({
                                    url: "{{ route('admin.users.destroy', ':id') }}"
                                        .replace(':id', userId),
                                    type: 'DELETE',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        admin_password: password
                                    }
                                }).then(response => response).catch(error => {
                                    Swal.showValidationMessage(error
                                        .responseJSON?.message ||
                                        'Invalid password');
                                });
                            }
                        }).then((result) => {
                            if (result.isConfirmed && result.value.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: result.value.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => location.reload());
                            }
                        });
                    }
                });
            });

            // ============ EXPORT ============
            $('#exportUsersBtn').on('click', function() {
                var params = new URLSearchParams(window.location.search).toString();
                window.location.href = "{{ route('admin.users.export') }}" + (params ? '?' + params : '');
            });

            // ============ MODAL RESET ============
            $('#addUserModal, #editUserModal, #activatePendingModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            });
        });
    </script>
@endpush
