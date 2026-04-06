{{-- resources/views/profile/index.blade.php --}}
@extends('layouts.main')

@section('title', 'My Profile | ' . config('app.name'))
@section('page-title', 'My Profile')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Profile', 'url' => 'javascript:void(0)'],
            ['title' => 'My Profile', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

    <style>
        :root {
            --navy: #003366;
            --navy-dark: #002244;
            --navy-light: #678abe;
            --orange: #ff6600;
            --orange-dark: #cc5200;
            --orange-light: #ffebe6;
            --gray-light: #f8f9fa;
            --gray-border: #e9ecef;
            --gray-dark: #6c757d;
        }

        /* Main Profile Container */
        .profile-container {
            padding: 20px 0;
            margin-top: 0;
            position: relative;
            z-index: 1;
        }

        /* Layout Utama - Responsive Grid */
        .profile-layout {
            display: grid;
            grid-template-columns: 360px 1fr;
            gap: 24px;
        }

        /* Left Sidebar */
        .profile-sidebar {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 51, 102, 0.06);
            overflow: hidden;
            height: fit-content;
            position: relative;
        }

        /* Cover Photo */
        .cover-photo {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
            height: 140px;
            position: relative;
            overflow: hidden;
        }

        .cover-photo::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");
        }

        /* Cover Action Buttons */
        .cover-actions {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 5;
            display: flex;
            gap: 8px;
        }

        .cover-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--orange);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .cover-btn:hover {
            background: var(--orange-dark);
            transform: scale(1.05);
        }

        .cover-btn i {
            font-size: 14px;
        }

        /* Profile Info */
        .profile-info-wrap {
            padding: 0 24px 20px;
            margin-top: -60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .profile-photo {
            position: relative;
            margin-bottom: 16px;
            cursor: pointer;
        }

        .profile-photo img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 8px 25px rgba(0, 51, 102, 0.15);
            border-radius: 50%;
            background: white;
            transition: transform 0.3s;
        }

        .profile-photo:hover img {
            transform: scale(1.02);
        }

        .profile-name h4 {
            font-size: 20px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 8px;
        }

        /* Badge Roles */
        .badge-role {
            font-size: 11px;
            padding: 5px 14px;
            font-weight: 600;
            border-radius: 50px;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .badge-superadmin {
            background: linear-gradient(135deg, var(--navy), var(--navy-dark));
            color: white;
        }

        .badge-admin_eng {
            background: linear-gradient(135deg, #dc3545, #bb2d3b);
            color: white;
        }

        .badge-gm {
            background: linear-gradient(135deg, var(--navy), #002856);
            color: white;
        }

        .badge-om {
            background: linear-gradient(135deg, #20c997, #198754);
            color: white;
        }

        .badge-technician {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }

        .badge-manager {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #212529;
        }

        .badge-user {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }

        /* Stats Row - Responsive Grid */
        .stats-row {
            padding: 20px 24px;
            border-top: 1px solid var(--gray-border);
            border-bottom: 1px solid var(--gray-border);
            margin: 0;
            width: 100%;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
            flex: 1;
            padding: 0 8px;
            min-width: 0;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .stat-label {
            font-size: 11px;
            color: var(--gray-dark);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Info List */
        .info-list {
            padding: 20px 24px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed var(--gray-border);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 13px;
            color: var(--gray-dark);
        }

        .info-value {
            font-weight: 600;
            color: var(--navy);
            text-align: right;
        }

        /* Right Content Card */
        .profile-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 51, 102, 0.06);
            overflow: hidden;
        }

        /* Tabs - Horizontal Scrollable */
        .content-tabs {
            border-bottom: 1px solid var(--gray-border);
            padding: 0 24px;
            background: white;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        .content-tabs::-webkit-scrollbar {
            height: 3px;
        }

        .content-tabs::-webkit-scrollbar-track {
            background: var(--gray-border);
            border-radius: 3px;
        }

        .content-tabs::-webkit-scrollbar-thumb {
            background: var(--orange);
            border-radius: 3px;
        }

        .content-tabs .nav-tabs {
            border-bottom: none;
            margin-bottom: -1px;
            flex-wrap: nowrap;
            display: flex;
            width: max-content;
            min-width: 100%;
        }

        .content-tabs .nav-link {
            color: var(--gray-dark);
            font-weight: 600;
            border: none;
            padding: 16px 20px;
            margin-right: 4px;
            border-radius: 0.75rem 0.75rem 0 0;
            transition: all 0.3s;
            font-size: 14px;
            white-space: nowrap;
            flex-shrink: 0;
            background: transparent;
        }

        .content-tabs .nav-link i {
            margin-right: 8px;
            color: var(--navy);
        }

        .content-tabs .nav-link:hover {
            background: var(--orange-light);
            color: var(--navy);
        }

        .content-tabs .nav-link.active {
            color: var(--orange);
            border-bottom: 3px solid var(--orange);
            background: transparent;
        }

        .content-tabs .nav-link.active i {
            color: var(--orange);
        }

        /* Tab Content */
        .tab-content {
            background: white;
        }

        .tab-pane {
            padding: 24px;
        }

        /* Signature Section */
        .signature-section {
            border-top: 8px solid #f1f5f9;
            padding: 20px 24px;
            background: white;
        }

        .signature-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .signature-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .signature-title i {
            color: var(--orange);
            font-size: 18px;
        }

        .signature-title h6 {
            font-weight: 700;
            color: var(--navy);
            margin: 0;
            font-size: 15px;
        }

        .signature-preview-small {
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--gray-border);
        }

        .signature-preview-small img {
            max-width: 100%;
            max-height: 80px;
            background: transparent;
        }

        .signature-actions-row {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }

        .signature-actions-row .btn {
            flex: 1;
        }

        /* Signature Pad Container */
        .signature-pad-container {
            background: white;
            border-radius: 1rem;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid var(--gray-border);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .signature-pad-wrapper {
            background: white;
            border-radius: 0.75rem;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 2px solid var(--gray-border);
            padding: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .signature-pad {
            width: 100%;
            height: auto;
            aspect-ratio: 300/200;
            background: white;
            cursor: crosshair;
            display: block;
            max-width: 300px;
            margin: 0 auto;
            touch-action: none;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            border: 1px solid #ddd;
        }

        .signature-pad-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            justify-content: center;
        }

        .signature-pad-actions .btn {
            flex: 1;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 6px;
            display: block;
            font-size: 13px;
        }

        .form-control {
            border: 1.5px solid var(--gray-border);
            border-radius: 0.75rem;
            padding: 10px 16px;
            width: 100%;
            transition: all 0.3s;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: var(--orange);
            box-shadow: 0 0 0 0.2rem rgba(255, 102, 0, 0.1);
            outline: none;
        }

        .form-control:read-only,
        .form-control:disabled {
            background: var(--gray-light);
            cursor: not-allowed;
        }

        /* Buttons */
        .btn {
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            min-height: 44px;
        }

        .btn-navy {
            background: var(--navy);
            color: white;
        }

        .btn-navy:hover {
            background: var(--navy-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);
            color: white;
        }

        .btn-orange {
            background: var(--orange);
            color: white;
        }

        .btn-orange:hover {
            background: var(--orange-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 102, 0, 0.3);
            color: white;
        }

        .btn-outline-navy {
            background: transparent;
            color: var(--navy);
            border: 2px solid var(--navy);
        }

        .btn-outline-navy:hover {
            background: var(--navy);
            color: white;
            border-color: var(--navy);
        }

        .btn-outline-secondary {
            background: transparent;
            color: var(--gray-dark);
            border: 2px solid var(--gray-border);
        }

        .btn-outline-secondary:hover {
            background: var(--gray-light);
            color: var(--gray-dark);
            border-color: var(--gray-dark);
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .form-actions .btn {
            flex: 0 0 auto;
        }

        /* Alert Styles */
        .alert-navy {
            background: var(--navy-light);
            color: var(--navy);
            border-left: 4px solid var(--navy);
            padding: 14px 16px;
            border-radius: 0.75rem;
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .alert-navy i {
            color: var(--orange);
            margin-right: 12px;
            font-size: 18px;
        }

        .alert-warning-custom {
            background: #fff3cd;
            border-left: 4px solid #856404;
            padding: 14px 16px;
            border-radius: 0.75rem;
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .alert-warning-custom i {
            color: #856404;
            margin-right: 12px;
            font-size: 18px;
        }

        .alert-info-custom {
            background: #d1ecf1;
            border-left: 4px solid #0c5460;
            padding: 14px 16px;
            border-radius: 0.75rem;
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .alert-info-custom i {
            color: #0c5460;
            margin-right: 12px;
            font-size: 18px;
        }

        /* Captcha Styles */
        .captcha-container {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .captcha-image {
            border: 2px solid var(--gray-border);
            border-radius: 0.75rem;
            background-color: var(--gray-light);
            padding: 5px;
            transition: all 0.3s;
        }

        .captcha-image:hover {
            border-color: var(--orange);
        }

        .btn-reload-captcha {
            background: var(--orange);
            border: none;
            border-radius: 0.75rem;
            padding: 8px 12px;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            min-height: 44px;
        }

        .btn-reload-captcha:hover {
            background: var(--orange-dark);
            transform: translateY(-2px);
        }

        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Crop Modal */
        .img-container {
            max-height: 400px;
            overflow: hidden;
        }

        #cropImage {
            max-width: 100%;
        }

        /* Modal Styles - Fixed for iPhone SE */
        .modal-content {
            border-radius: 1rem;
            border: none;
            overflow: hidden;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
            color: white;
            border-bottom: none;
            padding: 16px 20px;
            flex-shrink: 0;
        }

        .modal-header .modal-title {
            color: white;
            font-weight: 700;
            font-size: 16px;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
            margin: 0;
            padding: 8px;
        }

        .modal-header .btn-close:hover {
            opacity: 1;
        }

        .modal-body {
            padding: 20px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-footer {
            border-top: 1px solid var(--gray-border);
            padding: 16px 20px;
            gap: 12px;
            flex-shrink: 0;
            flex-wrap: wrap;
        }

        .modal-footer .btn {
            min-width: 100px;
        }

        /* Responsive Modal for Small Devices */
        @media (max-width: 576px) {
            .modal-dialog {
                margin: 0;
                width: 100%;
                max-width: none;
            }

            .modal-content {
                border-radius: 20px 20px 0 0;
                max-height: 85vh;
            }

            .modal-body {
                padding: 16px;
            }

            .modal-footer {
                padding: 12px 16px;
                flex-direction: row;
                flex-wrap: wrap;
            }

            .modal-footer .btn {
                flex: 1;
                min-width: 0;
                margin: 0;
            }

            /* Crop Modal specific */
            .img-container {
                max-height: 400px;
            }

            .signature-pad-container {
                padding: 12px;
            }

            .signature-pad-wrapper {
                padding: 8px;
            }

            .signature-pad {
                max-width: 100%;
            }

            .signature-pad-actions {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .signature-pad-actions .btn {
                flex: 1;
                min-width: 0;
                padding: 8px 12px;
            }
        }

        /* iPhone SE Specific (375px) */
        @media (max-width: 375px) {
            .modal-body {
                padding: 12px;
            }

            .modal-footer .btn {
                font-size: 12px;
                padding: 8px 12px;
            }

            .img-container {
                max-height: 250px;
            }

            .signature-pad-actions .btn {
                font-size: 11px;
                padding: 8px 8px;
            }

            .form-group label {
                font-size: 12px;
            }

            .form-control {
                padding: 8px 12px;
                font-size: 13px;
            }
        }

        /* SweetAlert2 Custom Styles - Only button spacing */
        .swal2-actions {
            gap: 12px !important;
            margin-top: 1rem !important;
        }

        .swal2-actions .btn {
            margin: 0 !important;
            min-width: 100px;
        }

        /* Untuk mobile, gap tetap 12px, button tidak full width */
        @media (max-width: 576px) {
            .swal2-actions {
                gap: 12px !important;
                flex-direction: row !important;
                flex-wrap: wrap !important;
            }

            .swal2-actions .btn {
                min-width: 120px !important;
                flex: 0 0 auto !important;
            }
        }

        /* Responsive Layout */
        @media (max-width: 992px) {
            .profile-layout {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .profile-sidebar,
            .profile-card {
                margin: 0 16px;
            }

            .profile-container {
                padding: 16px 0;
            }
        }

        /* Tablet Stats */
        @media (min-width: 768px) and (max-width: 992px) {
            .stats-row {
                display: flex;
                justify-content: space-around;
                padding: 20px;
            }

            .stat-item {
                flex: 1;
                text-align: center;
            }
        }

        /* Mobile Stats */
        @media (max-width: 768px) {
            .stats-row {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                padding: 16px;
            }

            .stat-item {
                flex: 1 1 calc(33.33% - 12px);
                min-width: 100px;
                padding: 12px 8px;
                background: var(--gray-light);
                border-radius: 12px;
            }

            .stat-value {
                font-size: 18px;
            }

            .stat-label {
                font-size: 10px;
            }
        }

        /* Small Mobile */
        @media (max-width: 576px) {
            .profile-photo img {
                width: 90px;
                height: 90px;
            }

            .profile-info-wrap {
                padding: 0 16px 16px;
            }

            .info-list {
                padding: 16px;
            }

            .signature-section {
                padding: 16px;
            }

            .tab-pane {
                padding: 16px;
            }

            .content-tabs {
                padding: 0 16px;
            }

            .signature-actions-row {
                flex-direction: row;
                gap: 12px;
            }

            .signature-actions-row .btn {
                flex: 1;
            }

            .captcha-container {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-reload-captcha {
                width: 100%;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
            }

            .stat-item {
                flex: 1 1 calc(50% - 12px);
            }
        }

        /* Desktop Stats */
        @media (min-width: 992px) {
            .stats-row {
                display: flex;
                padding: 20px 24px;
            }

            .stat-item {
                flex: 1;
                border-right: 1px solid var(--gray-border);
            }

            .stat-item:last-child {
                border-right: none;
            }
        }

        /* Bottom Sheet Modal for Mobile */
        @media (max-width: 768px) {
            .modal.fade .modal-dialog {
                transform: translateY(100%);
                transition: transform 0.3s ease-out;
                margin: 0;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
            }

            .modal.fade.show .modal-dialog {
                transform: translateY(0);
            }

            .modal-dialog {
                margin: 0;
            }

            .modal-dialog-centered {
                align-items: flex-end;
                min-height: 100%;
                display: flex;
            }

            .modal-dialog-centered::before {
                display: none;
            }
        }

        /* Preview Modal - Square image */
        #previewPhotoModal .modal-body img {
            border-radius: 0 !important;
            max-width: 100%;
            max-height: 60vh;
            object-fit: contain;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-border);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--orange);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--orange-dark);
        }
    </style>
@endpush

@section('content')
    <div class="profile-container">
        <div class="profile-layout">
            <!-- LEFT SIDEBAR -->
            <div class="profile-sidebar">
                <div class="cover-photo">
                    <div class="cover-actions">
                        {{-- <button class="cover-btn" id="previewPhotoBtn" title="Preview Photo">
                            <i class="fas fa-eye"></i>
                        </button> --}}
                        <label for="profilePictureInput" class="cover-btn" title="Change Photo" style="cursor: pointer;">
                            <i class="fas fa-camera"></i>
                        </label>
                        @if ($user->profile_picture)
                            <button class="cover-btn" id="removePhotoBtn" title="Remove Photo">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="profile-info-wrap">
                    <div class="profile-photo" id="profilePhotoClick">
                        <img src="{{ $user->profile_picture_url ?? asset('assets/images/default-avatar.png') }}"
                            alt="{{ $user->name }}" id="profileImage">
                    </div>
                    <input type="file" id="profilePictureInput" accept="image/*" style="display: none;">

                    <div class="profile-name">
                        <h4>{{ $user->name }}</h4>
                        <span class="badge-role badge-{{ $user->role }}">
                            {{ $user->role_name }}
                        </span>
                        @if (!$user->email_verified_at)
                            <span class="badge bg-warning mt-2 d-block" style="font-size: 11px;">Email Unverified</span>
                        @endif
                    </div>
                </div>

                <!-- Statistics -->
                <div class="stats-row">
                    <div class="stat-item">
                        <div class="stat-value">{{ $stats['days_active'] ?? 0 }}</div>
                        <div class="stat-label">Days Active</div>
                    </div>

                    @if ($user->role === 'technician')
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['resolved_tickets'] ?? 0 }}</div>
                            <div class="stat-label">Resolved</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['in_progress'] ?? 0 }}</div>
                            <div class="stat-label">In Progress</div>
                        </div>
                    @elseif(in_array($user->role, ['user', 'manager']))
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['total_tickets'] ?? 0 }}</div>
                            <div class="stat-label">Tickets</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['resolved_tickets'] ?? 0 }}</div>
                            <div class="stat-label">Resolved</div>
                        </div>
                    @elseif($user->role === 'admin_eng')
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['pending_receive'] ?? 0 }}</div>
                            <div class="stat-label">Pending Receive</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['ready_close'] ?? 0 }}</div>
                            <div class="stat-label">Ready Close</div>
                        </div>
                    @elseif($user->role === 'om')
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['pending_approvals'] ?? 0 }}</div>
                            <div class="stat-label">Pending Approvals</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['pending_vr'] ?? 0 }}</div>
                            <div class="stat-label">Pending VR</div>
                        </div>
                    @elseif($user->role === 'gm')
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['pending_approvals'] ?? 0 }}</div>
                            <div class="stat-label">Pending Approvals</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['pending_vr'] ?? 0 }}</div>
                            <div class="stat-label">Pending VR</div>
                        </div>
                    @elseif($user->role === 'superadmin')
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['total_users'] ?? 0 }}</div>
                            <div class="stat-label">Users</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $stats['total_tickets'] ?? 0 }}</div>
                            <div class="stat-label">Tickets</div>
                        </div>
                    @endif
                </div>

                <div class="info-list">
                    <h6 class="fw-bold mb-3" style="color: var(--navy); font-size: 14px;">
                        <i class="fas fa-user-circle me-2" style="color: var(--orange);"></i>
                        Personal Information
                    </h6>

                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value">
                            {{ $user->email }}
                            @if ($user->email_verified_at)
                                <i class="fas fa-check-circle text-success ms-1" title="Verified"></i>
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone</span>
                        <span class="info-value">{{ $user->phone ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Department</span>
                        <span class="info-value">
                            @if ($user->department)
                                {{ $user->department->name }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <span
                                class="badge bg-{{ $user->status === 'active' ? 'success' : ($user->status === 'inactive' ? 'danger' : 'warning') }}"
                                style="font-size: 11px;">
                                {{ ucfirst($user->status) }}
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Joined</span>
                        <span class="info-value">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT CONTENT CARD -->
            <div class="profile-card">
                <div class="content-tabs">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a href="#edit-profile" data-bs-toggle="tab" class="nav-link active show">
                                <i class="fas fa-user-edit"></i>
                                <span>Edit Profile</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#change-password" data-bs-toggle="tab" class="nav-link">
                                <i class="fas fa-lock"></i>
                                <span>Change Password</span>
                            </a>
                        </li>
                        @if (in_array($user->role, ['user', 'technician', 'manager']))
                            <li class="nav-item">
                                <a href="#reset-password" data-bs-toggle="tab" class="nav-link">
                                    <i class="fas fa-envelope"></i>
                                    <span>Reset via Email</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="tab-content">
                    <!-- EDIT PROFILE TAB -->
                    <div id="edit-profile" class="tab-pane fade active show">
                        <h6 class="fw-bold mb-4" style="color: var(--navy); font-size: 15px;">
                            <i class="fas fa-address-card me-2" style="color: var(--orange);"></i>
                            Profile Information
                        </h6>

                        <form id="updateProfileForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ $user->name }}" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ $user->email }}" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ $user->phone }}" placeholder="e.g., +62812345678">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Role</label>
                                        <input type="text" class="form-control" value="{{ $user->role_name }}"
                                            readonly disabled>
                                        <small class="text-muted">Role cannot be changed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button class="btn btn-navy" type="submit">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- CHANGE PASSWORD TAB -->
                    <div id="change-password" class="tab-pane fade">
                        <h6 class="fw-bold mb-4" style="color: var(--navy); font-size: 15px;">
                            <i class="fas fa-key me-2" style="color: var(--orange);"></i>
                            Change Password
                        </h6>

                        <form id="changePasswordForm">
                            @csrf
                            <div class="form-group">
                                <label>Current Password <span class="text-danger">*</span></label>
                                <input type="password" name="current_password" class="form-control"
                                    placeholder="Enter current password" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group">
                                <label>New Password <span class="text-danger">*</span></label>
                                <input type="password" name="new_password" class="form-control"
                                    placeholder="Enter new password (min. 8 characters)" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-group">
                                <label>Confirm New Password <span class="text-danger">*</span></label>
                                <input type="password" name="new_password_confirmation" class="form-control"
                                    placeholder="Confirm new password" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="form-actions">
                                <button class="btn btn-navy" type="submit">
                                    <i class="fas fa-lock me-2"></i>Change Password
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- RESET PASSWORD VIA EMAIL TAB -->
                    @if (in_array($user->role, ['user', 'technician', 'manager']))
                        <div id="reset-password" class="tab-pane fade">
                            <h6 class="fw-bold mb-4" style="color: var(--navy); font-size: 15px;">
                                <i class="fas fa-paper-plane me-2" style="color: var(--orange);"></i>
                                Reset Password via Email
                            </h6>

                            <div class="alert-navy">
                                <i class="fas fa-info-circle"></i>
                                <div>
                                    <strong>Forgot your password?</strong>
                                    <p class="mb-0 mt-1 small">We'll send a password reset link to your email address. The
                                        link will expire in 60 minutes.</p>
                                </div>
                            </div>

                            <form id="profileResetPasswordForm">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ $user->email }}" readonly>
                                    <small class="text-muted">Reset link will be sent to this email</small>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="fw-semibold">Captcha Verification <span
                                            class="text-danger">*</span></label>
                                    <div class="captcha-container">
                                        <div style="flex: 1;">
                                            <input type="text" class="form-control" name="captcha"
                                                id="profile-captcha-input" placeholder="Type the code shown" required
                                                autocomplete="off">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <div id="profile-captcha-image" class="captcha-image">
                                                {!! captcha_img() !!}
                                            </div>
                                            <button type="button" class="btn-reload-captcha" id="profile-reload-captcha"
                                                title="Reload Captcha">
                                                <i class="fas fa-redo-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert-warning-custom d-flex align-items-center mb-3">
                                    <i class="fas fa-shield-alt"></i>
                                    <div class="small">
                                        <strong>Note:</strong> This will send a password reset link directly to your email.
                                        You can use this if you've forgotten your current password.
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-orange" id="sendProfileResetLinkBtn">
                                        <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- INFO UNTUK ROLE TINGGI -->
                    @if (in_array($user->role, ['superadmin', 'admin_eng', 'om', 'gm']))
                        <div id="reset-password" class="tab-pane fade">
                            <h6 class="fw-bold mb-4" style="color: var(--navy); font-size: 15px;">
                                <i class="fas fa-shield-alt me-2" style="color: var(--orange);"></i>
                                Password Reset
                            </h6>

                            <div class="alert-info-custom">
                                <i class="fas fa-info-circle"></i>
                                <div>
                                    <strong>High-Privilege Account</strong>
                                    <p class="mb-0 mt-1 small">For security reasons, password reset via email is not
                                        available for your role. Please contact the administrator for password reset
                                        assistance.</p>
                                </div>
                            </div>

                            <div class="text-center mt-4 p-4 bg-light rounded">
                                <i class="fas fa-lock fa-3x mb-3" style="color: var(--navy);"></i>
                                <h5>Manual Reset Required</h5>
                                <p class="text-muted small">Please contact the system administrator to reset your password.
                                </p>
                                <a href="mailto:admin@example.com" class="btn btn-outline-navy">
                                    <i class="fas fa-envelope me-2"></i>Contact Admin
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- SIGNATURE SECTION -->
                @if ($user->canManageSignature())
                    <div class="signature-section">
                        <div class="signature-header">
                            <div class="signature-title">
                                <i class="fas fa-signature"></i>
                                <h6>Digital Signature</h6>
                            </div>
                            @if ($user->has_signature)
                                <span class="badge bg-success">Uploaded</span>
                            @else
                                <span class="badge bg-warning">Not Uploaded</span>
                            @endif
                        </div>

                        @if ($user->has_signature)
                            <div class="signature-preview-small">
                                <img src="{{ $user->signature_url }}" alt="Digital Signature" id="signatureImage">
                            </div>
                            <p class="small text-muted mb-3">
                                <i class="fas fa-clock me-1"></i>
                                Last updated:
                                {{ $user->signature_updated_at ? $user->signature_updated_at->format('d M Y H:i') : '-' }}
                            </p>
                        @endif

                        <div class="signature-actions-row">
                            <button type="button" class="btn btn-orange" id="createSignatureBtn">
                                <i class="fas fa-pen me-2"></i>
                                {{ $user->has_signature ? 'Update' : 'Create' }}
                            </button>

                            @if ($user->has_signature)
                                <button type="button" class="btn btn-outline-navy" id="removeSignatureBtn">
                                    <i class="fas fa-trash me-2"></i>
                                    Remove
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- PREVIEW PHOTO MODAL -->
    <div class="modal fade" id="previewPhotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-image me-2"></i>
                        Profile Picture
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img src="{{ $user->profile_picture_url ?? asset('assets/images/default-avatar.png') }}"
                        alt="Profile Picture"
                        style="max-width: 100%; border-radius: 0; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                </div>
            </div>
        </div>
    </div>

    <!-- CROP MODAL - Fixed for iPhone SE -->
    <div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-crop me-2"></i>
                        Crop Profile Picture
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <img id="cropImage" src="" alt="Crop Image">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-orange" id="cropAndUploadBtn">
                        <i class="fas fa-check me-2"></i>Crop & Upload
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SIGNATURE PAD MODAL - Fixed for iPhone SE -->
    <div class="modal fade" id="signaturePadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-pen me-2"></i>
                        {{ $user->has_signature ? 'Update Digital Signature' : 'Create Digital Signature' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="signature-pad-container">
                        <div class="signature-pad-wrapper">
                            <canvas id="signaturePad" class="signature-pad" width="300" height="200"></canvas>
                        </div>

                        <div class="signature-pad-actions">
                            <button type="button" class="btn btn-outline-secondary" id="clearSignatureBtn">
                                <i class="fas fa-eraser me-2"></i>Clear
                            </button>
                            <button type="button" class="btn btn-outline-navy" id="undoSignatureBtn" disabled>
                                <i class="fas fa-undo me-2"></i>Undo
                            </button>
                            <button type="button" class="btn btn-outline-navy" id="redoSignatureBtn" disabled>
                                <i class="fas fa-redo me-2"></i>Redo
                            </button>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label>Your Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="signaturePassword"
                            placeholder="Enter your password to confirm">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-orange" id="saveSignatureBtn">
                        <i class="fas fa-save me-2"></i>Save Signature
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "5000",
                "closeButton": true,
                "progressBar": true,
                "preventDuplicates": true
            };

            // SweetAlert2 Custom with button spacing
            const SwalCustom = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-orange',
                    cancelButton: 'btn btn-outline-secondary',
                    denyButton: 'btn btn-outline-navy',
                    actions: 'swal2-actions',
                    popup: 'swal2-popup'
                },
                buttonsStyling: false,
                focusConfirm: false
            });

            let cropper;

            // Preview Photo
            $('#previewPhotoBtn, #profilePhotoClick').on('click', function() {
                const imgSrc = $('#profileImage').attr('src');
                if (imgSrc && !imgSrc.includes('default-avatar')) {
                    $('#previewPhotoModal .modal-body img').attr('src', imgSrc);
                    $('#previewPhotoModal').modal('show');
                }
            });

            // Profile Picture Upload with Crop
            $('#profilePictureInput').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        toastr.error('Image size must be less than 5MB');
                        $(this).val('');
                        return;
                    }

                    if (!file.type.match('image.*')) {
                        toastr.error('Please select an image file');
                        $(this).val('');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#cropImage').attr('src', event.target.result);
                        $('#cropModal').modal('show');

                        $('#cropModal').on('shown.bs.modal', function() {
                            if (cropper) cropper.destroy();
                            cropper = new Cropper(document.getElementById('cropImage'), {
                                aspectRatio: 1,
                                viewMode: 1,
                                dragMode: 'move',
                                cropBoxMovable: true,
                                cropBoxResizable: true,
                                minCropBoxWidth: 50,
                                minCropBoxHeight: 50,
                                autoCropArea: 1,
                                responsive: true
                            });
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Crop and Upload
            $('#cropAndUploadBtn').on('click', function() {
                if (cropper) {
                    const canvas = cropper.getCroppedCanvas();
                    if (!canvas) {
                        toastr.error('Please select a crop area');
                        return;
                    }

                    canvas.toBlob(function(blob) {
                        const formData = new FormData();
                        formData.append('profile_picture', blob, 'profile.jpg');
                        formData.append('_token', '{{ csrf_token() }}');

                        const btn = $('#cropAndUploadBtn');
                        const originalText = btn.html();
                        btn.prop('disabled', true).html(
                            '<span class="loading-spinner me-2"></span>Uploading...');

                        $.ajax({
                            url: "{{ route('profile.upload-picture') }}",
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    $('#cropModal').modal('hide');
                                    toastr.success(response.message);
                                    setTimeout(() => location.reload(), 1500);
                                }
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to upload photo');
                            },
                            complete: function() {
                                btn.prop('disabled', false).html(originalText);
                                $('#profilePictureInput').val('');
                            }
                        });
                    }, 'image/jpeg', 0.9);
                }
            });

            $('#cropModal').on('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                $('#cropImage').attr('src', '');
                $('#profilePictureInput').val('');
            });

            // Remove Profile Picture
            $('#removePhotoBtn').on('click', function(e) {
                e.stopPropagation();
                SwalCustom.fire({
                    title: 'Remove Photo?',
                    text: 'Are you sure you want to remove your profile picture?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-orange',
                        cancelButton: 'btn btn-outline-secondary',
                        actions: 'swal2-actions'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('profile.remove-picture') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    SwalCustom.fire({
                                        icon: 'success',
                                        title: 'Removed!',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => location.reload());
                                }
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to remove photo');
                            }
                        });
                    }
                });
            });

            // Update Profile
            $('#updateProfileForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Updating...');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('profile.update') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalText);
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1500);
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('[name="' + key + '"]').addClass('is-invalid')
                                    .siblings('.invalid-feedback').text(value[0]);
                            });
                            toastr.error('Please check the form for errors');
                        } else {
                            toastr.error(xhr.responseJSON?.message ||
                                'Failed to update profile');
                        }
                    }
                });
            });

            // Change Password
            $('#changePasswordForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Changing...');
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: "{{ route('profile.update-password') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#changePasswordForm')[0].reset();
                        submitBtn.prop('disabled', false).html(originalText);
                        if (response.success) {
                            SwalCustom.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);
                        if (xhr.status === 422) {
                            if (xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    $('[name="' + key + '"]').addClass('is-invalid')
                                        .siblings('.invalid-feedback').text(value[0]);
                                });
                            } else {
                                toastr.error(xhr.responseJSON.message);
                            }
                        } else {
                            toastr.error('Failed to change password');
                        }
                    }
                });
            });

            // Profile Reset Password
            @if (in_array($user->role, ['user', 'technician', 'manager']))
                $('#profile-reload-captcha').on('click', function() {
                    var reloadBtn = $(this);
                    var originalHtml = reloadBtn.html();
                    reloadBtn.html('<span class="loading-spinner"></span>').prop('disabled', true);

                    $.get('{{ route('reload.captcha') }}', function(data) {
                        $('#profile-captcha-image').html(data.captcha);
                        $('#profile-captcha-input').val('').removeClass('is-invalid');
                        reloadBtn.html(originalHtml).prop('disabled', false);
                    }).fail(function() {
                        reloadBtn.html(originalHtml).prop('disabled', false);
                        toastr.error('Failed to reload captcha');
                    });
                });

                $('#profileResetPasswordForm').on('submit', function(e) {
                    e.preventDefault();
                    var formData = {
                        email: $('input[name="email"]', this).val(),
                        captcha: $('input[name="captcha"]', this).val(),
                        _token: '{{ csrf_token() }}'
                    };
                    var submitBtn = $(this).find('button[type="submit"]');
                    var originalText = submitBtn.html();

                    submitBtn.prop('disabled', true).html(
                        '<span class="loading-spinner me-2"></span>Sending...');

                    $.ajax({
                        url: "{{ route('profile.password.email') }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            submitBtn.prop('disabled', false).html(originalText);
                            if (response.success) {
                                SwalCustom.fire({
                                    icon: 'success',
                                    title: 'Reset Link Sent!',
                                    text: response.message,
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                                $('.is-invalid').removeClass('is-invalid');
                                $('#profile-captcha-input').val('');
                                $('#profile-reload-captcha').trigger('click');
                            }
                        },
                        error: function(xhr) {
                            submitBtn.prop('disabled', false).html(originalText);
                            if (xhr.status === 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    var input = $('[name="' + key + '"]');
                                    if (input.length) {
                                        input.addClass('is-invalid').siblings(
                                            '.invalid-feedback').text(value[0]);
                                    } else {
                                        toastr.error(value[0]);
                                    }
                                });
                                toastr.error('Please check the form for errors');
                                if (errors.captcha) {
                                    $('#profile-reload-captcha').trigger('click');
                                }
                            } else {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to send reset link.');
                            }
                        }
                    });
                });
            @endif

            // Signature Pad
            @if ($user->canManageSignature())
                let signaturePad;
                let undoStack = [];
                let redoStack = [];

                $('#createSignatureBtn').on('click', function() {
                    $('#signaturePadModal').modal('show');
                });

                $('#signaturePadModal').on('shown.bs.modal', function() {
                    const canvas = document.getElementById('signaturePad');
                    canvas.width = 300;
                    canvas.height = 200;

                    const ctx = canvas.getContext('2d');
                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    undoStack = [];
                    redoStack = [];

                    signaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(0, 0, 0)',
                        velocityFilterWeight: 0.7,
                        minWidth: 1,
                        maxWidth: 2.5,
                        throttle: 16,
                        dotSize: 1
                    });

                    signaturePad.addEventListener('endStroke', () => {
                        const data = signaturePad.toData();
                        if (data && data.length > 0) {
                            undoStack.push(JSON.parse(JSON.stringify(data)));
                            redoStack = [];
                        }
                        updateButtons();
                    });

                    $('#clearSignatureBtn').off('click').on('click', function() {
                        signaturePad.clear();
                        undoStack = [];
                        redoStack = [];
                        updateButtons();
                    });

                    $('#undoSignatureBtn').off('click').on('click', function() {
                        if (undoStack.length > 0) {
                            const lastState = undoStack.pop();
                            redoStack.push(lastState);
                            if (undoStack.length > 0) {
                                signaturePad.fromData(undoStack[undoStack.length - 1]);
                            } else {
                                signaturePad.clear();
                            }
                            updateButtons();
                        }
                    });

                    $('#redoSignatureBtn').off('click').on('click', function() {
                        if (redoStack.length > 0) {
                            const redoState = redoStack.pop();
                            undoStack.push(redoState);
                            signaturePad.fromData(redoState);
                            updateButtons();
                        }
                    });

                    function updateButtons() {
                        $('#undoSignatureBtn').prop('disabled', undoStack.length === 0);
                        $('#redoSignatureBtn').prop('disabled', redoStack.length === 0);
                    }
                    updateButtons();
                });

                $('#signaturePadModal').on('hidden.bs.modal', function() {
                    if (signaturePad) signaturePad.clear();
                    $('#signaturePassword').val('').removeClass('is-invalid');
                    undoStack = [];
                    redoStack = [];
                });

                $('#saveSignatureBtn').on('click', function() {
                    if (!signaturePad || signaturePad.isEmpty()) {
                        toastr.error('Please draw your signature first');
                        return;
                    }

                    const password = $('#signaturePassword').val();
                    if (!password) {
                        $('#signaturePassword').addClass('is-invalid').siblings('.invalid-feedback').text(
                            'Password is required');
                        return;
                    }

                    const canvas = document.getElementById('signaturePad');
                    const outputCanvas = document.createElement('canvas');
                    outputCanvas.width = 300;
                    outputCanvas.height = 200;
                    const outputCtx = outputCanvas.getContext('2d');
                    outputCtx.fillStyle = '#FFFFFF';
                    outputCtx.fillRect(0, 0, outputCanvas.width, outputCanvas.height);
                    outputCtx.drawImage(canvas, 0, 0, 300, 200);

                    const signatureData = outputCanvas.toDataURL('image/png');
                    const blob = dataURItoBlob(signatureData);
                    const formData = new FormData();
                    formData.append('signature', blob, 'signature.png');
                    formData.append('password', password);
                    formData.append('_token', '{{ csrf_token() }}');

                    var submitBtn = $(this);
                    var originalText = submitBtn.html();
                    submitBtn.prop('disabled', true).html(
                        '<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

                    $.ajax({
                        url: "{{ route('profile.upload-signature') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            submitBtn.prop('disabled', false).html(originalText);
                            if (response.success) {
                                $('#signaturePadModal').modal('hide');
                                toastr.success(response.message);
                                setTimeout(() => location.reload(), 1500);
                            }
                        },
                        error: function(xhr) {
                            submitBtn.prop('disabled', false).html(originalText);
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                if (xhr.responseJSON.errors.password) {
                                    $('#signaturePassword').addClass('is-invalid').siblings(
                                        '.invalid-feedback').text(xhr.responseJSON.errors
                                        .password[0]);
                                } else {
                                    toastr.error('Please check the form for errors');
                                }
                            } else {
                                toastr.error(xhr.responseJSON?.message ||
                                    'Failed to save signature');
                            }
                        }
                    });
                });

                function dataURItoBlob(dataURI) {
                    const byteString = atob(dataURI.split(',')[1]);
                    const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
                    const ab = new ArrayBuffer(byteString.length);
                    const ia = new Uint8Array(ab);
                    for (let i = 0; i < byteString.length; i++) ia[i] = byteString.charCodeAt(i);
                    return new Blob([ab], {
                        type: mimeString
                    });
                }

                $('#removeSignatureBtn').on('click', function() {
                    SwalCustom.fire({
                        title: 'Remove Signature?',
                        text: 'Are you sure you want to remove your digital signature?',
                        icon: 'warning',
                        input: 'password',
                        inputLabel: 'Enter your password to confirm',
                        inputPlaceholder: 'Your password',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, remove it!',
                        cancelButtonText: 'Cancel',
                        preConfirm: (password) => {
                            if (!password) {
                                Swal.showValidationMessage('Password is required');
                                return false;
                            }
                            return $.ajax({
                                url: "{{ route('profile.remove-signature') }}",
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    password: password
                                }
                            }).then(response => response).catch(error => {
                                Swal.showValidationMessage(error.responseJSON
                                    ?.message || 'Invalid password');
                            });
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value.success) {
                            SwalCustom.fire({
                                icon: 'success',
                                title: 'Removed!',
                                text: result.value.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        }
                    });
                });
            @endif
        });
    </script>
@endpush
