<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <link rel="icon" href="{{ asset('assets/images/logo-main.png') }}" type="image/x-icon">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/logo-main.png') }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        /* Main Container */
        .settings-wrapper {
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        /* Settings Container - Responsive */
        .settings-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 16px;
        }

        /* Header with Back Button */
        .settings-header {
            background: linear-gradient(135deg, #1a2b4c, #2a3b5c);
            color: white;
            padding: 20px 24px;
            margin-bottom: 24px;
            border-radius: 0 0 20px 20px;
        }

        .header-content {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .btn-back {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateX(-2px);
        }

        .header-text h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-text p {
            font-size: 13px;
            opacity: 0.8;
            margin: 6px 0 0 0;
        }

        /* Menu Group */
        .settings-group {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .group-title {
            padding: 14px 20px;
            background: #f8f9fa;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e9ecef;
        }

        /* Menu Item */
        .settings-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.2s ease;
            text-decoration: none;
            color: inherit;
        }

        .settings-item:last-child {
            border-bottom: none;
        }

        .settings-item:hover {
            background: #f8f9fa;
        }

        .item-left {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
        }

        .item-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .item-icon.faq {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .item-icon.info {
            background: #e3f2fd;
            color: #1976d2;
        }

        .item-icon.refresh {
            background: #fff3e0;
            color: #f57c00;
        }

        .item-icon.export {
            background: #e8f0fe;
            color: #1a2b4c;
        }

        .item-icon.activity {
            background: #ffebee;
            color: #c62828;
        }

        .item-icon.logout {
            background: #ffebee;
            color: #c62828;
        }

        .item-icon.health {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .item-icon.backup {
            background: #e3f2fd;
            color: #1976d2;
        }

        .item-icon.email {
            background: #fff3e0;
            color: #f57c00;
        }

        .item-text h4 {
            font-size: 15px;
            font-weight: 500;
            margin: 0 0 4px 0;
            color: #333;
        }

        .item-text p {
            font-size: 12px;
            color: #999;
            margin: 0;
        }

        .item-right {
            color: #ccc;
            font-size: 14px;
            flex-shrink: 0;
            margin-left: 12px;
        }

        /* Version Footer */
        .version-footer {
            text-align: center;
            padding: 24px 16px 32px;
            color: #999;
            font-size: 12px;
        }

        /* Modal Style */
        .modal-custom .modal-content {
            border-radius: 20px;
            border: none;
        }

        .modal-custom .modal-header {
            border-bottom: none;
            padding: 20px 20px 0 20px;
        }

        .modal-custom .modal-body {
            padding: 20px;
        }

        .modal-custom .modal-footer {
            border-top: none;
            padding: 0 20px 20px 20px;
        }

        /* FAQ Styles */
        .faq-lang-switch {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: flex-end;
        }

        .lang-btn {
            padding: 6px 16px;
            border-radius: 20px;
            border: 1px solid #ddd;
            background: white;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .lang-btn.active {
            background: #ff6b35;
            color: white;
            border-color: #ff6b35;
        }

        .faq-item {
            padding: 14px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .faq-item:last-child {
            border-bottom: none;
        }

        .faq-question {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .faq-question i {
            color: #ff6b35;
            font-size: 14px;
        }

        .faq-answer {
            font-size: 13px;
            color: #666;
            line-height: 1.5;
            padding-left: 26px;
        }

        .faq-contact {
            background: #e8f5e9;
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
            text-align: center;
        }

        .faq-contact a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: 600;
        }

        .faq-contact a:hover {
            text-decoration: underline;
        }

        /* Dark Mode */
        body.dark-mode {
            background-color: #121212;
        }

        body.dark-mode .settings-wrapper {
            background-color: #121212;
        }

        body.dark-mode .settings-group {
            background: #1e1e1e;
        }

        body.dark-mode .group-title {
            background: #2a2a2a;
            color: #aaa;
            border-bottom-color: #333;
        }

        body.dark-mode .settings-item {
            border-bottom-color: #2a2a2a;
        }

        body.dark-mode .settings-item:hover {
            background: #2a2a2a;
        }

        body.dark-mode .item-text h4 {
            color: #eee;
        }

        body.dark-mode .item-text p {
            color: #888;
        }

        body.dark-mode .version-footer {
            color: #666;
        }

        body.dark-mode .info-version {
            background: #2a2a2a;
        }

        body.dark-mode .info-label {
            color: #aaa;
        }

        body.dark-mode .info-value {
            color: #ddd;
        }

        body.dark-mode .faq-item {
            border-bottom-color: #2a2a2a;
        }

        body.dark-mode .faq-question {
            color: #eee;
        }

        body.dark-mode .faq-answer {
            color: #aaa;
        }

        body.dark-mode .faq-contact {
            background: #2a2a2a;
        }

        body.dark-mode .export-option {
            border-color: #333;
        }

        body.dark-mode .export-option:hover {
            background: #2a2a2a;
        }

        body.dark-mode .export-radio label {
            color: #eee;
        }

        body.dark-mode .custom-date-range {
            border-top-color: #333;
        }

        body.dark-mode .health-card {
            background: #2a2a2a;
        }

        body.dark-mode .health-card:hover {
            background: #333;
        }

        body.dark-mode .health-card .health-value {
            color: #eee;
        }

        body.dark-mode .backup-item {
            border-bottom-color: #2a2a2a;
        }

        body.dark-mode .backup-name {
            color: #eee;
        }

        /* Email Config Card */
        .email-config-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .email-config-card .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 5px;
        }

        .email-config-card .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            font-size: 13px;
        }

        .email-config-card .form-control:focus {
            border-color: #ff6b35;
            outline: none;
        }

        body.dark-mode .email-config-card {
            background: #2a2a2a;
        }

        body.dark-mode .email-config-card .form-label {
            color: #aaa;
        }

        body.dark-mode .email-config-card .form-control {
            background: #1e1e1e;
            border-color: #444;
            color: #eee;
        }

        /* Health Cards */
        .health-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .health-card {
            background: #f8f9fa;
            border-radius: 14px;
            padding: 14px;
            transition: all 0.2s ease;
        }

        .health-card:hover {
            background: #f0f0f0;
        }

        .health-card .health-title {
            font-size: 11px;
            color: #999;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .health-card .health-value {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
        }

        .health-card .health-sub {
            font-size: 11px;
            color: #666;
        }

        .health-card.success .health-value {
            color: #2e7d32;
        }

        .health-card.warning .health-value {
            color: #f57c00;
        }

        .health-card.danger .health-value {
            color: #c62828;
        }

        .health-card.info .health-value {
            color: #1976d2;
        }

        /* Export Options */
        .export-options {
            margin-bottom: 20px;
        }

        .export-option {
            padding: 12px 16px;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .export-option:hover {
            background: #f8f9fa;
            border-color: #ff6b35;
        }

        .export-radio {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .export-radio input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #ff6b35;
        }

        .export-radio label {
            font-weight: 600;
            color: #333;
            cursor: pointer;
            margin: 0;
        }

        .export-desc {
            font-size: 12px;
            color: #999;
            margin-left: 28px;
            margin-top: 4px;
        }

        .custom-date-range {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
        }

        .custom-date-range .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #666;
            margin-bottom: 6px;
        }

        .custom-date-range .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 13px;
        }

        .custom-date-range .form-control:focus {
            border-color: #ff6b35;
            outline: none;
        }

        /* Backup List */
        .backup-list {
            margin-top: 8px;
        }

        .backup-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .backup-item:last-child {
            border-bottom: none;
        }

        .backup-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .backup-name {
            font-size: 13px;
            font-weight: 500;
            color: #333;
        }

        .backup-meta {
            font-size: 11px;
            color: #999;
        }

        .backup-actions {
            display: flex;
            gap: 8px;
        }

        .btn-backup-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-backup-action.download {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .btn-backup-action.delete {
            background: #ffebee;
            color: #c62828;
        }

        .btn-backup-action:hover {
            transform: translateY(-1px);
        }

        .empty-backup {
            text-align: center;
            padding: 30px 20px;
            color: #999;
        }

        .empty-backup i {
            font-size: 40px;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        .btn-create-backup {
            background: #1a2b4c;
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-create-backup:hover {
            background: #2a3b5c;
            transform: translateY(-1px);
        }

        .btn-edit-email {
            background: #ff6b35;
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-edit-email:hover {
            background: #e55a2b;
            transform: translateY(-1px);
        }

        /* Responsive */
        @media (min-width: 768px) {
            .settings-container {
                padding: 0 24px;
            }

            .settings-header {
                padding: 24px 32px;
                border-radius: 0 0 24px 24px;
            }

            .btn-back {
                width: 44px;
                height: 44px;
                border-radius: 14px;
            }

            .header-text h1 {
                font-size: 28px;
            }

            .header-text p {
                font-size: 14px;
            }

            .settings-group {
                border-radius: 20px;
                margin-bottom: 24px;
            }

            .group-title {
                padding: 16px 24px;
                font-size: 14px;
            }

            .settings-item {
                padding: 18px 24px;
            }

            .item-left {
                gap: 18px;
            }

            .item-icon {
                width: 48px;
                height: 48px;
                border-radius: 16px;
                font-size: 22px;
            }

            .item-text h4 {
                font-size: 16px;
            }

            .item-text p {
                font-size: 13px;
            }

            .version-footer {
                padding: 32px 24px 40px;
                font-size: 13px;
            }

            .health-grid {
                gap: 16px;
            }

            .health-card .health-value {
                font-size: 22px;
            }
        }

        @media (max-width: 480px) {
            .settings-container {
                padding: 0 12px;
            }

            .settings-header {
                padding: 16px 20px;
            }

            .header-text h1 {
                font-size: 20px;
            }

            .btn-back {
                width: 36px;
                height: 36px;
            }

            .item-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .item-text h4 {
                font-size: 14px;
            }

            .item-text p {
                font-size: 11px;
            }

            .settings-item {
                padding: 14px 16px;
            }

            .health-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="settings-wrapper">
        <!-- Header with Back Button -->
        <div class="settings-header">
            <div class="header-content">
                <button class="btn-back" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div class="header-text">
                    <h1>
                        <i class="fas fa-cog"></i>
                        Settings
                    </h1>
                    <p>Customize your application preferences</p>
                </div>
            </div>
        </div>

        <div class="settings-container">
            <!-- General Settings Group -->
            <div class="settings-group">
                <div class="group-title">
                    <i class="fas fa-sliders-h me-2"></i> General
                </div>

                <div class="settings-item" onclick="openFAQ()">
                    <div class="item-left">
                        <div class="item-icon faq">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="item-text">
                            <h4>FAQ & Help</h4>
                            <p>Frequently asked questions and guides</p>
                        </div>
                    </div>
                    <div class="item-right">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>

                <div class="settings-item" onclick="openAppInfo()">
                    <div class="item-left">
                        <div class="item-icon info">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="item-text">
                            <h4>App Information</h4>
                            <p>Version, build, and license info</p>
                        </div>
                    </div>
                    <div class="item-right">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>

            <!-- Data & Privacy Settings Group (Semua Role) -->
            <div class="settings-group">
                <div class="group-title">
                    <i class="fas fa-database me-2"></i> Data & Privacy
                </div>

                <div class="settings-item" onclick="openExportModal()">
                    <div class="item-left">
                        <div class="item-icon export">
                            <i class="fas fa-file-export"></i>
                        </div>
                        <div class="item-text">
                            <h4>Export Activity Log</h4>
                            <p>Download your personal activity history</p>
                        </div>
                    </div>
                    <div class="item-right">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>

            <!-- Maintenance Settings Group -->
            <div class="settings-group">
                <div class="group-title">
                    <i class="fas fa-tools me-2"></i> Maintenance
                </div>

                <div class="settings-item" onclick="refreshCache()">
                    <div class="item-left">
                        <div class="item-icon refresh">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <div class="item-text">
                            <h4>Refresh Cache</h4>
                            <p>Clear application cache and refresh data</p>
                        </div>
                    </div>
                    <div class="item-right">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>

            <!-- System Health Group (SuperAdmin only) -->
            @if (auth()->user()->role === 'superadmin')
                <div class="settings-group">
                    <div class="group-title">
                        <i class="fas fa-heartbeat me-2"></i> System Health
                    </div>

                    <div style="padding: 16px 20px;">
                        <div class="health-grid">
                            <div class="health-card {{ $health['database']['size_mb'] > 100 ? 'warning' : 'success' }}">
                                <div class="health-title"><i class="fas fa-database"></i> Database</div>
                                <div class="health-value">{{ $health['database']['size_display'] }}</div>
                                <div class="health-sub">{{ number_format($health['database']['total_records']) }}
                                    records</div>
                            </div>
                            <div class="health-card info">
                                <div class="health-title"><i class="fas fa-hdd"></i> Storage</div>
                                <div class="health-value">{{ $health['storage']['total_size'] }}</div>
                                <div class="health-sub">Public: {{ $health['storage']['public_display'] }}</div>
                            </div>
                            <div class="health-card info">
                                <div class="health-title"><i class="fab fa-php"></i> PHP Version</div>
                                <div class="health-value">{{ $health['php']['version'] }}</div>
                                <div class="health-sub">Memory: {{ $health['php']['memory_limit'] }}</div>
                            </div>
                            <div
                                class="health-card {{ $health['laravel']['debug_mode'] === 'On' ? 'warning' : 'success' }}">
                                <div class="health-title"><i class="fab fa-laravel"></i> Laravel</div>
                                <div class="health-value">{{ $health['laravel']['version'] }}</div>
                                <div class="health-sub">Env: {{ $health['laravel']['environment'] }}</div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="health-card">
                                    <div class="health-title"><i class="fas fa-server"></i> Server</div>
                                    <div class="health-value" style="font-size: 14px;">
                                        {{ $health['server']['software'] }}</div>
                                    <div class="health-sub">{{ $health['server']['name'] }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div
                                    class="health-card {{ $health['server']['last_backup']['exists'] ? 'success' : 'warning' }}">
                                    <div class="health-title"><i class="fas fa-archive"></i> Last Backup</div>
                                    @if ($health['server']['last_backup']['exists'])
                                        <div class="health-value" style="font-size: 13px;">
                                            {{ $health['server']['last_backup']['date'] }}</div>
                                        <div class="health-sub">Size: {{ $health['server']['last_backup']['size'] }}
                                        </div>
                                    @else
                                        <div class="health-value" style="font-size: 14px;">No Backup Yet</div>
                                        <div class="health-sub">Click "Create Backup" below</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Notification Config (SuperAdmin only) -->
                <div class="settings-group">
                    <div class="group-title">
                        <i class="fas fa-envelope me-2"></i> Email Notification
                    </div>

                    <div style="padding: 16px 20px;">
                        <div class="email-config-card">
                            <div class="mb-3">
                                <label class="form-label">Engineering Department Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" id="notificationEmail" class="form-control" readonly
                                        value="{{ $emailConfig['email'] }}">
                                </div>
                                <small class="text-muted">Email used to send engineering department
                                    notifications</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Password / App Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" id="notificationPassword" class="form-control" readonly
                                        value="{{ $emailConfig['password'] }}">
                                </div>
                                <small class="text-muted">App password for the email account</small>
                            </div>
                            <button class="btn-edit-email" id="openEmailConfigModalBtn">
                                <i class="fas fa-edit me-2"></i> Edit Configuration
                            </button>
                        </div>
                        <div class="alert alert-info mt-3" style="font-size: 12px;">
                            <i class="fas fa-info-circle me-2"></i>
                            This configuration is used for sending automated email notifications from the Engineering
                            Department.
                            Changes require your admin password confirmation.
                        </div>
                    </div>
                </div>

                <!-- Backup Database Group (SuperAdmin only) -->
                <div class="settings-group">
                    <div class="group-title">
                        <i class="fas fa-database me-2"></i> Backup Database
                    </div>

                    <div style="padding: 16px 20px;">
                        <button class="btn-create-backup" id="createBackupBtn">
                            <i class="fas fa-download me-2"></i> Create New Backup
                        </button>

                        <div id="backupListContainer" style="margin-top: 16px;">
                            <div class="backup-list">
                                @if (count($backups) > 0)
                                    @foreach ($backups as $backup)
                                        <div class="backup-item" data-filename="{{ $backup['name'] }}">
                                            <div class="backup-info">
                                                <span class="backup-name">{{ $backup['name'] }}</span>
                                                <span class="backup-meta">{{ $backup['created_at'] }} •
                                                    {{ $backup['size_display'] }}</span>
                                            </div>
                                            <div class="backup-actions">
                                                <button class="btn-backup-action download"
                                                    onclick="downloadBackup('{{ $backup['name'] }}')"
                                                    title="Download">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <button class="btn-backup-action delete"
                                                    onclick="deleteBackup('{{ $backup['name'] }}')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="empty-backup">
                                        <i class="fas fa-database"></i>
                                        <p>No backups available</p>
                                        <small>Click "Create New Backup" to get started</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Account Settings Group (SuperAdmin only) -->
            @if (auth()->user()->role === 'superadmin')
                <div class="settings-group">
                    <div class="group-title">
                        <i class="fas fa-shield-alt me-2"></i> Admin
                    </div>

                    <a href="{{ route('activity-logs.index') }}" class="settings-item"
                        style="text-decoration: none;">
                        <div class="item-left">
                            <div class="item-icon activity">
                                <i class="fas fa-history"></i>
                            </div>
                            <div class="item-text">
                                <h4>Activity Logs</h4>
                                <p>View system audit trail</p>
                            </div>
                        </div>
                        <div class="item-right">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>
                </div>
            @endif

            <!-- Danger Zone -->
            <div class="settings-group">
                <div class="group-title">
                    <i class="fas fa-exclamation-triangle me-2"></i> Account
                </div>

                <div class="settings-item" onclick="confirmLogout()">
                    <div class="item-left">
                        <div class="item-icon logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <div class="item-text">
                            <h4>Logout</h4>
                            <p>Sign out from your account</p>
                        </div>
                    </div>
                    <div class="item-right">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>

            <!-- Version Footer -->
            <div class="version-footer">
                <i class="fas fa-code-branch me-1"></i> Version 2.0.0<br>
                &copy; {{ date('Y') }} {{ config('app.name') }}
            </div>
        </div>
    </div>

    <!-- FAQ Modal -->
    <div class="modal fade modal-custom" id="faqModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-question-circle me-2"
                            style="color: #ff6b35;"></i>Frequently Asked Questions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="faq-lang-switch">
                        <button class="lang-btn active" data-lang="id">🇮🇩 Indonesia</button>
                        <button class="lang-btn" data-lang="en">🇬🇧 English</button>
                    </div>

                    <!-- Indonesian FAQ -->
                    <div id="faq-id" class="faq-content">
                        @php
                            $userRole = auth()->user()->role;
                        @endphp

                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-question"></i> Bagaimana cara membuat tiket baru?
                            </div>
                            <div class="faq-answer">
                                Klik tombol "+" di header atau buka menu Tickets dan klik "Create New Ticket". Isi
                                informasi yang diperlukan dan submit.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-question"></i> Bagaimana cara melacak status tiket saya?
                            </div>
                            <div class="faq-answer">
                                Buka menu "My Tickets". Anda dapat melihat status dan progress setiap tiket di sana.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-question"></i> Apa itu Purchase Request (PR)?
                            </div>
                            <div class="faq-answer">
                                PR adalah permintaan pembelian untuk material yang dibutuhkan untuk menyelesaikan tugas
                                maintenance. Teknisi dapat meminta PR ketika membutuhkan part/suku cadang.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-question"></i> Berapa lama waktu approval?
                            </div>
                            <div class="faq-answer">
                                Waktu approval tergantung pada tingkat prioritas. Tiket dengan prioritas URGENT akan
                                diprioritaskan. Umumnya 1-24 jam.
                            </div>
                        </div>

                        @if ($userRole === 'user')
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> Bisakah saya membatalkan tiket yang sudah saya
                                    buat?
                                </div>
                                <div class="faq-answer">
                                    Ya, jika tiket masih dalam status OPEN. Hubungi manager departemen Anda atau admin
                                    untuk membatalkan.
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> Apa yang harus saya lakukan setelah teknisi selesai
                                    bekerja?
                                </div>
                                <div class="faq-answer">
                                    Setelah teknisi menandai pekerjaan selesai, Anda akan menerima notifikasi. Silakan
                                    cek hasil pekerjaan dan konfirmasi (accept) jika sudah sesuai.
                                </div>
                            </div>
                        @endif

                        @if ($userRole === 'technician')
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> Bagaimana cara menandai pekerjaan selesai?
                                </div>
                                <div class="faq-answer">
                                    Setelah menyelesaikan pekerjaan, buka tiket dan klik tombol "Mark Complete". Isi
                                    completion notes dan berikan tanda tangan Anda.
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> Kapan saya harus mengajukan Purchase Request?
                                </div>
                                <div class="faq-answer">
                                    Ajukan PR ketika Anda membutuhkan material/part yang tidak tersedia. PR harus
                                    diajukan sebelum pekerjaan dapat dilanjutkan.
                                </div>
                            </div>
                        @endif

                        @if ($userRole === 'manager')
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> Bagaimana cara memantau tiket departemen saya?
                                </div>
                                <div class="faq-answer">
                                    Buka menu "My Department" untuk melihat semua tiket dari departemen Anda. Anda juga
                                    bisa melihat statistik dan performa teknisi.
                                </div>
                            </div>
                        @endif

                        @if (in_array($userRole, ['admin_eng', 'superadmin']))
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> Bagaimana cara menugaskan teknisi?
                                </div>
                                <div class="faq-answer">
                                    Buka tiket yang sudah di-approve OM, lalu klik tombol "Assign Technician". Pilih
                                    teknisi yang tersedia dan tentukan due date.
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> Bagaimana cara menutup tiket?
                                </div>
                                <div class="faq-answer">
                                    Setelah GM approve dan tiket status "Ready for Closure", admin dapat menutup tiket
                                    secara administratif.
                                </div>
                            </div>
                        @endif

                        @if (in_array($userRole, ['om', 'gm']))
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> Bagaimana cara memberikan approval?
                                </div>
                                <div class="faq-answer">
                                    Buka tiket yang membutuhkan approval Anda, lalu klik tombol "Approve" dan berikan
                                    tanda tangan Anda.
                                </div>
                            </div>
                        @endif

                        <div class="faq-contact">
                            <i class="fab fa-whatsapp fa-2x mb-2" style="color: #25D366;"></i>
                            <p class="mb-1"><strong>Butuh bantuan lebih lanjut?</strong></p>
                            <p class="mb-2">Hubungi tim support kami via WhatsApp:</p>
                            <a href="https://wa.me/6281234567890" target="_blank">
                                <i class="fab fa-whatsapp me-1"></i> +62 812-3456-7890
                            </a>
                        </div>
                    </div>

                    <!-- English FAQ -->
                    <div id="faq-en" class="faq-content" style="display: none;">
                        @php $userRole = auth()->user()->role; @endphp

                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-question"></i> How to create a new ticket?
                            </div>
                            <div class="faq-answer">
                                Click the "+" button in the header or go to Tickets menu and click "Create New Ticket".
                                Fill in the required information and submit.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-question"></i> How to track my ticket status?
                            </div>
                            <div class="faq-answer">
                                Go to "My Tickets" menu. You can see the current status and progress of each ticket
                                there.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-question"></i> What is Purchase Request (PR)?
                            </div>
                            <div class="faq-answer">
                                PR is a purchase request for materials needed to complete a maintenance task.
                                Technicians can request PR when parts are needed.
                            </div>
                        </div>

                        <div class="faq-item">
                            <div class="faq-question">
                                <i class="fas fa-question"></i> How long does approval take?
                            </div>
                            <div class="faq-answer">
                                Approval time depends on priority level. URGENT priority tickets are prioritized.
                                Generally 1-24 hours.
                            </div>
                        </div>

                        @if ($userRole === 'user')
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> Can I cancel my ticket?
                                </div>
                                <div class="faq-answer">
                                    Yes, if the ticket is still in OPEN status. Contact your department manager or admin
                                    to cancel.
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> What should I do after the technician completes the
                                    work?
                                </div>
                                <div class="faq-answer">
                                    After the technician marks the work as complete, you will receive a notification.
                                    Please check the result and confirm (accept) if satisfied.
                                </div>
                            </div>
                        @endif

                        @if ($userRole === 'technician')
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> How to mark work as complete?
                                </div>
                                <div class="faq-answer">
                                    After completing the work, open the ticket and click "Mark Complete". Fill in
                                    completion notes and provide your signature.
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> When should I submit a Purchase Request?
                                </div>
                                <div class="faq-answer">
                                    Submit a PR when you need materials/parts that are not available. PR must be
                                    submitted before work can continue.
                                </div>
                            </div>
                        @endif

                        @if ($userRole === 'manager')
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> How to monitor my department's tickets?
                                </div>
                                <div class="faq-answer">
                                    Go to "My Department" menu to view all tickets from your department. You can also
                                    view statistics and technician performance.
                                </div>
                            </div>
                        @endif

                        @if (in_array($userRole, ['admin_eng', 'superadmin']))
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> How to assign a technician?
                                </div>
                                <div class="faq-answer">
                                    Open the ticket that has been approved by OM, then click "Assign Technician". Select
                                    an available technician and set due date.
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> How to close a ticket?
                                </div>
                                <div class="faq-answer">
                                    After GM approves and the ticket status is "Ready for Closure", admin can close the
                                    ticket administratively.
                                </div>
                            </div>
                        @endif

                        @if (in_array($userRole, ['om', 'gm']))
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-question"></i> How to give approval?
                                </div>
                                <div class="faq-answer">
                                    Open the ticket that needs your approval, then click "Approve" and provide your
                                    signature.
                                </div>
                            </div>
                        @endif

                        <div class="faq-contact">
                            <i class="fab fa-whatsapp fa-2x mb-2" style="color: #25D366;"></i>
                            <p class="mb-1"><strong>Need further assistance?</strong></p>
                            <p class="mb-2">Contact our support team via WhatsApp:</p>
                            <a href="https://wa.me/6281234567890" target="_blank">
                                <i class="fab fa-whatsapp me-1"></i> +62 812-3456-7890
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- App Info Modal -->
    <div class="modal fade modal-custom" id="appInfoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"
                            style="color: #ff6b35;"></i>Application Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="info-version">
                        <div class="info-version-item">
                            <span class="info-label">Application Name</span>
                            <span class="info-value">{{ config('app.name') }}</span>
                        </div>
                        <div class="info-version-item">
                            <span class="info-label">Version</span>
                            <span class="info-value">2.0.0</span>
                        </div>
                        <div class="info-version-item">
                            <span class="info-label">Build Number</span>
                            <span class="info-value">2025.12.16</span>
                        </div>
                        <div class="info-version-item">
                            <span class="info-label">Release Date</span>
                            <span class="info-value">April 19, 2026</span>
                        </div>
                        <div class="info-version-item">
                            <span class="info-label">Framework</span>
                            <span class="info-value">Laravel 10.x</span>
                        </div>
                        <div class="info-version-item">
                            <span class="info-label">PHP Version</span>
                            <span class="info-value">{{ phpversion() }}</span>
                        </div>
                    </div>
                    <div class="text-center">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo"
                            style="height: 50px; margin-bottom: 12px;" onerror="this.style.display='none'">
                        <p class="text-muted small">
                            <i class="fas fa-copyright"></i> {{ date('Y') }} {{ config('app.name') }}. All rights
                            reserved.<br>
                            Hotel Maintenance Request System
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade modal-custom" id="exportModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-file-export me-2" style="color: #ff6b35;"></i>Export
                        Activity Log</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Select date range to export your activity log.</p>

                    <div class="export-options">
                        <div class="export-option" data-range="today">
                            <div class="export-radio">
                                <input type="radio" name="export_range" id="export_today" value="today" checked>
                                <label for="export_today">Today</label>
                            </div>
                            <div class="export-desc">Activities from today</div>
                        </div>

                        <div class="export-option" data-range="last7">
                            <div class="export-radio">
                                <input type="radio" name="export_range" id="export_last7" value="last7">
                                <label for="export_last7">Last 7 Days</label>
                            </div>
                            <div class="export-desc">Activities from the past week</div>
                        </div>

                        <div class="export-option" data-range="last15">
                            <div class="export-radio">
                                <input type="radio" name="export_range" id="export_last15" value="last15">
                                <label for="export_last15">Last 15 Days</label>
                            </div>
                            <div class="export-desc">Activities from the past 15 days</div>
                        </div>

                        <div class="export-option" data-range="last30">
                            <div class="export-radio">
                                <input type="radio" name="export_range" id="export_last30" value="last30">
                                <label for="export_last30">Last 30 Days</label>
                            </div>
                            <div class="export-desc">Activities from the past month</div>
                        </div>

                        <div class="export-option" data-range="all">
                            <div class="export-radio">
                                <input type="radio" name="export_range" id="export_all" value="all">
                                <label for="export_all">All Time</label>
                            </div>
                            <div class="export-desc">Complete activity history</div>
                        </div>

                        <div class="export-option" data-range="custom">
                            <div class="export-radio">
                                <input type="radio" name="export_range" id="export_custom" value="custom">
                                <label for="export_custom">Custom Range</label>
                            </div>
                            <div class="export-desc">Select your own date range</div>
                        </div>
                    </div>

                    <div id="customDateRange" class="custom-date-range" style="display: none;">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" id="exportStartDate" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label">End Date</label>
                                <input type="date" id="exportEndDate" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn" style="background: #ff6b35; color: white;"
                        id="doExportBtn">
                        <i class="fas fa-download me-2"></i>Export CSV
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Email Configuration Modal -->
    <!-- Edit Email Configuration Modal -->
    <div class="modal fade modal-custom" id="editEmailConfigModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"
                    style="background: linear-gradient(135deg, #1a2b4c, #2a3b5c); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-envelope me-2"></i> Edit Email Notification Configuration
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <!-- INFO: Email bisa login ke Webmail -->
                    <div class="alert alert-primary mb-3"
                        style="font-size: 13px; background: #e8f0fe; border-color: #bbdefb;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Email Account Info:</strong> This email can be accessed via:
                        <div class="mt-2">
                            <a href="https://webmail.maintenancerequest.cfcb.my.id" target="_blank" class="me-3"
                                style="color: #1a2b4c;">
                                <i class="fas fa-envelope-open-text me-1"></i> Webmail Roundcube
                            </a>
                            <a href="https://mail.google.com" target="_blank" style="color: #1a2b4c;">
                                <i class="fab fa-google me-1"></i> Google (Gmail)
                            </a>
                        </div>
                        <small class="d-block mt-1">Use the email and password above to login</small>
                    </div>

                    <div class="alert alert-info"
                        style="font-size: 13px; background: #e3f2fd; border-color: #bbdefb;">
                        <i class="fas fa-info-circle me-2"></i>
                        Update the email account used to send notifications from the Engineering Department.
                    </div>

                    <!-- form fields -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="modalNotificationEmail" class="form-control"
                                placeholder="engreq@maintenancerequest.cfcb.my.id"
                                value="{{ $emailConfig['email'] }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Password / App Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="modalNotificationPassword" class="form-control"
                                value="{{ $emailConfig['password'] }}">
                            <button class="btn btn-outline-secondary toggle-password-modal" type="button"
                                data-target="modalNotificationPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Use app password if 2FA is enabled on this email account</small>
                    </div>

                    <hr class="my-3">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Confirm with Your Password <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" id="adminConfirmPassword" class="form-control"
                                placeholder="Enter your admin password">
                            <button class="btn btn-outline-secondary toggle-password-modal" type="button"
                                data-target="adminConfirmPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted">Your password is required to save changes</small>
                        <div id="adminPasswordError" class="text-danger small mt-1" style="display: none;">Incorrect
                            password</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button" class="btn" id="saveEmailConfigConfirmBtn"
                        style="background: #ff6b35; color: white;">
                        <i class="fas fa-save me-1"></i> Save Configuration
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

        // Go Back function
        function goBack() {
            if (document.referrer && document.referrer !== window.location.href) {
                window.history.back();
            } else {
                window.location.href = "{{ url('/dashboard') }}";
            }
        }

        // FAQ Language Switch
        let currentLang = 'id';

        function switchFaqLang(lang) {
            currentLang = lang;
            if (lang === 'id') {
                $('#faq-id').show();
                $('#faq-en').hide();
            } else {
                $('#faq-id').hide();
                $('#faq-en').show();
            }
            $('.lang-btn').removeClass('active');
            $(`.lang-btn[data-lang="${lang}"]`).addClass('active');
        }

        $('.lang-btn').on('click', function() {
            const lang = $(this).data('lang');
            switchFaqLang(lang);
        });

        // Open FAQ Modal
        function openFAQ() {
            switchFaqLang(currentLang);
            const modal = new bootstrap.Modal(document.getElementById('faqModal'));
            modal.show();
        }

        // Open App Info Modal
        function openAppInfo() {
            const modal = new bootstrap.Modal(document.getElementById('appInfoModal'));
            modal.show();
        }

        // Open Export Modal
        function openExportModal() {
            $('#export_today').prop('checked', true);
            $('#customDateRange').hide();
            $('#exportStartDate').val('');
            $('#exportEndDate').val('');

            const modal = new bootstrap.Modal(document.getElementById('exportModal'));
            modal.show();
        }

        // Handle export option click
        $('.export-option').on('click', function() {
            const range = $(this).data('range');
            $(`input[name="export_range"][value="${range}"]`).prop('checked', true);

            if (range === 'custom') {
                $('#customDateRange').show();
            } else {
                $('#customDateRange').hide();
            }
        });

        // Handle radio button change
        $('input[name="export_range"]').on('change', function() {
            const range = $(this).val();
            if (range === 'custom') {
                $('#customDateRange').show();
            } else {
                $('#customDateRange').hide();
            }
        });

        // Do Export
        $('#doExportBtn').on('click', function() {
            const range = $('input[name="export_range"]:checked').val();
            let url = "{{ route('settings.export-activity-log') }}?";

            if (range === 'today') {
                url += "range=today";
            } else if (range === 'last7') {
                url += "range=last7";
            } else if (range === 'last15') {
                url += "range=last15";
            } else if (range === 'last30') {
                url += "range=last30";
            } else if (range === 'all') {
                url += "range=all";
            } else if (range === 'custom') {
                const startDate = $('#exportStartDate').val();
                const endDate = $('#exportEndDate').val();

                if (!startDate || !endDate) {
                    toastr.warning('Please select both start and end date');
                    return;
                }

                if (startDate > endDate) {
                    toastr.warning('Start date cannot be after end date');
                    return;
                }

                url += `range=custom&start_date=${startDate}&end_date=${endDate}`;
            }

            bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
            window.location.href = url;
        });

        // Refresh Cache
        function refreshCache() {
            Swal.fire({
                title: 'Refresh Cache?',
                text: 'This will clear application cache and refresh all data.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ff6b35',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-sync-alt me-2"></i>Yes, Refresh',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('settings.refresh-cache') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cache Refreshed!',
                                text: response.message ||
                                    'Application cache has been cleared successfully.',
                                confirmButtonColor: '#ff6b35'
                            });
                            toastr.success('Cache refreshed successfully');
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cache Refreshed!',
                                text: 'Application cache has been cleared successfully. (Demo)',
                                confirmButtonColor: '#ff6b35'
                            });
                            toastr.success('Cache refreshed successfully (Demo)');
                        }
                    });
                }
            });
        }

        // Confirm Logout
        function confirmLogout() {
            Swal.fire({
                title: 'Logout?',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ff6b35',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-sign-out-alt me-2"></i>Yes, Logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form')?.submit();
                }
            });
        }

        @if (auth()->user()->role === 'superadmin')
            // Toggle Password visibility untuk modal
            $('.toggle-password-modal').on('click', function() {
                const target = $(this).data('target');
                const input = $('#' + target);
                const icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Open Email Config Modal
            $('#openEmailConfigModalBtn').on('click', function() {
                // Load current values ke modal
                $('#modalNotificationEmail').val($('#notificationEmail').val());
                $('#modalNotificationPassword').val($('#notificationPassword').val());
                $('#adminConfirmPassword').val('');
                $('#adminPasswordError').hide();

                $('#editEmailConfigModal').modal('show');
            });

            // Save Email Configuration
            $('#saveEmailConfigConfirmBtn').on('click', function() {
                const email = $('#modalNotificationEmail').val().trim();
                const password = $('#modalNotificationPassword').val();
                const adminPassword = $('#adminConfirmPassword').val();

                if (!email) {
                    toastr.warning('Please enter email address');
                    return;
                }
                if (!password) {
                    toastr.warning('Please enter email password');
                    return;
                }
                if (!adminPassword) {
                    $('#adminPasswordError').text('Please enter your admin password').show();
                    return;
                }

                const $btn = $(this);
                const originalText = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...').prop('disabled', true);

                $.ajax({
                    url: "{{ route('settings.email-config') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        email: email,
                        password: password,
                        admin_password: adminPassword
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update tampilan form utama
                            $('#notificationEmail').val(email);
                            $('#notificationPassword').val(password);

                            // Simpan ke localStorage juga
                            localStorage.setItem('email_config', JSON.stringify({
                                email: email,
                                password: password
                            }));

                            toastr.success('Email configuration saved successfully!');
                            $('#editEmailConfigModal').modal('hide');
                        } else {
                            $('#adminPasswordError').text(response.message ||
                                'Failed to save configuration').show();
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to save configuration';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                            if (errorMsg.includes('password')) {
                                $('#adminPasswordError').text(errorMsg).show();
                            } else {
                                toastr.error(errorMsg);
                            }
                        } else {
                            toastr.error(errorMsg);
                        }
                    },
                    complete: function() {
                        $btn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Create Backup
            $('#createBackupBtn').on('click', function() {
                const $btn = $(this);
                const originalText = $btn.html();
                $btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Creating...').prop('disabled', true);

                $.ajax({
                    url: "{{ route('settings.backup') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        type: 'full'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.message);
                            $btn.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Failed to create backup');
                        $btn.html(originalText).prop('disabled', false);
                    }
                });
            });

            // Download Backup
            window.downloadBackup = function(filename) {
                window.location.href = "{{ route('settings.backup.download', '') }}/" + filename;
            };

            // Delete Backup
            window.deleteBackup = function(filename) {
                Swal.fire({
                    title: 'Delete Backup?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('settings.backup.delete', '') }}/" + filename,
                            method: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    $(`.backup-item[data-filename="${filename}"]`).fadeOut(300,
                                        function() {
                                            $(this).remove();
                                            if ($('.backup-item').length === 0) {
                                                location.reload();
                                            }
                                        });
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function() {
                                toastr.error('Failed to delete backup');
                            }
                        });
                    }
                });
            };
        @endif

        // Load saved email config from localStorage (for demo)
        @if (auth()->user()->role === 'superadmin')
            const savedEmailConfig = localStorage.getItem('email_config');
            if (savedEmailConfig) {
                try {
                    const config = JSON.parse(savedEmailConfig);
                    $('#notificationEmail').val(config.email);
                    $('#notificationPassword').val(config.password);
                } catch (e) {}
            }
        @endif
    </script>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</body>

</html>
