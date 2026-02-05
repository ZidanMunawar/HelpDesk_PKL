<!--**********************************
    Sidebar dengan Mobile Bottom Navigation
***********************************-->

<!-- CSS Styles -->
<style>
    /* ========================================
   MOBILE BOTTOM NAVIGATION BAR STYLES
======================================== */

    /* Hide bottom nav on desktop, show sidebar */
    .mobile-bottom-nav {
        display: none;
    }

    .desktop-sidebar {
        display: block;
    }

    /* Mobile Menu Modal - Hidden by default */
    .mobile-menu-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        z-index: 9999;
        overflow-y: auto;
    }

    .mobile-menu-modal.active {
        display: block;
        animation: slideInFromBottom 0.3s ease-out;
    }

    @keyframes slideInFromBottom {
        from {
            transform: translateY(100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* ========================================
   MOBILE RESPONSIVE (MAX-WIDTH: 768px)
======================================== */
    @media screen and (max-width: 768px) {

        /* Hide desktop sidebar on mobile */
        .desktop-sidebar {
            display: none !important;
        }

        /* Show mobile bottom nav */
        .mobile-bottom-nav {
            display: block;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            height: 65px;
            padding-bottom: env(safe-area-inset-bottom);
            border-top: 1px solid #e9ecef;
        }

        .mobile-nav-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 100%;
            padding: 0 10px;
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #6c757d;
            font-size: 11px;
            transition: all 0.3s ease;
            position: relative;
            flex: 1;
            padding: 5px;
            min-width: 50px;
        }

        .mobile-nav-item i {
            font-size: 22px;
            margin-bottom: 3px;
            transition: all 0.3s ease;
        }

        .mobile-nav-item span {
            font-size: 10px;
            font-weight: 500;
            text-align: center;
            display: block;
        }

        .mobile-nav-item:hover,
        .mobile-nav-item.active {
            color: #7e74f1;
        }

        .mobile-nav-item.active i {
            transform: scale(1.1);
        }

        /* Floating Action Button (FAB) */
        .mobile-nav-item.mobile-fab {
            position: relative;
            top: -20px;
        }

        .fab-button {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7e74f1 0%, #5e54e3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(126, 116, 241, 0.4);
            transition: all 0.3s ease;
        }

        .fab-button i {
            color: #fff;
            font-size: 24px;
            margin-bottom: 0;
        }

        .fab-button:active {
            transform: scale(0.95);
            box-shadow: 0 2px 8px rgba(126, 116, 241, 0.4);
        }

        /* Mobile Badge */
        .mobile-badge {
            position: absolute;
            top: 2px;
            right: 15%;
            background: #dc3545;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 5px;
            border-radius: 10px;
            min-width: 16px;
            text-align: center;
            line-height: 1.2;
        }

        /* Add padding to content to prevent overlap with bottom nav */
        .content-body {
            padding-bottom: 80px !important;
        }

        /* ========================================
       MOBILE MENU MODAL STYLES
    ======================================== */
        .mobile-menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: linear-gradient(135deg, #7e74f1 0%, #5e54e3 100%);
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .mobile-menu-header h4 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .close-mobile-menu {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            padding: 5px;
            transition: transform 0.3s ease;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close-mobile-menu:active {
            transform: scale(0.9);
            background: rgba(255, 255, 255, 0.1);
        }

        .mobile-menu-content {
            padding: 20px;
            padding-bottom: 100px;
        }

        .mobile-menu-user-info {
            display: flex;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7e74f1 0%, #5e54e3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .user-avatar i {
            font-size: 28px;
            color: #fff;
        }

        .user-details h5 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .user-details p {
            margin: 0;
            font-size: 13px;
            color: #6c757d;
        }

        .mobile-menu-list {
            margin-top: 10px;
        }

        .mobile-menu-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            margin-bottom: 8px;
            background: #fff;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            position: relative;
        }

        .mobile-menu-link i {
            font-size: 20px;
            margin-right: 15px;
            color: #7e74f1;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .mobile-menu-link span {
            flex: 1;
        }

        .mobile-menu-link:active {
            background: #f8f9fa;
            border-color: #7e74f1;
            transform: scale(0.98);
        }

        .mobile-menu-link .mobile-badge {
            position: relative;
            right: auto;
            top: auto;
            transform: none;
            margin-left: 10px;
        }

        .mobile-menu-link.logout-link {
            margin-top: 20px;
            border-color: #dc3545;
            background: #fff5f5;
        }

        .mobile-menu-link.logout-link i {
            color: #dc3545;
        }

        .mobile-menu-link.logout-link:active {
            background: #ffe5e5;
            border-color: #dc3545;
        }

        /* Menu Section Divider */
        .mobile-menu-divider {
            height: 1px;
            background: #e9ecef;
            margin: 20px 0;
        }

        .mobile-menu-section-title {
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 15px 0 10px 0;
            padding: 0 5px;
        }
    }

    /* ========================================
   EXTRA SMALL DEVICES (MAX-WIDTH: 375px)
======================================== */
    @media screen and (max-width: 375px) {
        .mobile-nav-item {
            font-size: 10px;
            padding: 3px;
        }

        .mobile-nav-item i {
            font-size: 20px;
        }

        .mobile-nav-item span {
            font-size: 9px;
        }

        .fab-button {
            width: 50px;
            height: 50px;
        }

        .fab-button i {
            font-size: 22px;
        }

        .mobile-menu-link {
            padding: 12px 15px;
            font-size: 14px;
        }

        .mobile-menu-user-info {
            padding: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
        }

        .user-avatar i {
            font-size: 24px;
        }
    }

    /* ========================================
   LANDSCAPE MODE (Mobile)
======================================== */
    @media screen and (max-width: 768px) and (orientation: landscape) {
        .mobile-bottom-nav {
            height: 55px;
        }

        .mobile-nav-item i {
            font-size: 20px;
        }

        .mobile-nav-item span {
            font-size: 9px;
        }

        .fab-button {
            width: 48px;
            height: 48px;
        }

        .mobile-nav-item.mobile-fab {
            top: -15px;
        }
    }
</style>

<!--**********************************
    Desktop Sidebar (Hidden on Mobile)
***********************************-->
<div class="deznav desktop-sidebar">
    <div class="deznav-scroll">
        <!-- Quick Create Ticket Button - User, Technician & Admin Eng ONLY -->
        @if (in_array(auth()->user()->role, ['user', 'technician', 'admin_eng']))
            <a href="{{ route('tickets.create') }}" class="add-menu-sidebar">
                <i class="flaticon-381-add"></i> New Ticket
            </a>
        @endif

        <ul class="metismenu" id="menu">
            <!-- ========================= DASHBOARD (ALL ROLES) ========================= -->
            <li>
                <a class="ai-icon" href="{{ route('dashboard') }}" aria-expanded="false">
                    <i class="flaticon-381-networking"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!-- ========================= TICKETS ========================= -->
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-notepad"></i>
                    <span class="nav-text">Tickets</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('tickets.index') }}">
                            <i class="flaticon-381-list"></i> All Tickets
                        </a>
                    </li>

                    @if (auth()->user()->role === 'user')
                        <li>
                            <a href="{{ route('tickets.index', ['my_tickets' => '1']) }}">
                                <i class="flaticon-381-file"></i> My Tickets
                                @php
                                    $myTicketsCount = App\Models\Ticket::where('user_id', auth()->id())->count();
                                @endphp
                                @if ($myTicketsCount > 0)
                                    <span class="badge badge-primary badge-sm">{{ $myTicketsCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->role === 'user' && auth()->user()->department_id)
                        <li>
                            <a href="#">
                                <i class="flaticon-381-folder-11"></i> Department Tickets
                                @php
                                    $deptCount = App\Models\Ticket::where(
                                        'department_id',
                                        auth()->user()->department_id,
                                    )
                                        ->whereNotIn('status', ['closed', 'cancelled'])
                                        ->count();
                                @endphp
                                @if ($deptCount > 0)
                                    <span class="badge badge-primary badge-sm">{{ $deptCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->role === 'technician')
                        <li>
                            <a href="#">
                                <i class="flaticon-381-user-7"></i> Assigned to Me
                                @php
                                    $assignedCount = App\Models\Ticket::where('assigned_to', auth()->id())
                                        ->whereIn('status', ['in_progress', 'pending_vr'])
                                        ->count();
                                @endphp
                                @if ($assignedCount > 0)
                                    <span class="badge badge-info badge-sm">{{ $assignedCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    @if (auth()->user()->role === 'admin_eng')
                        <li>
                            <a href="#">
                                <i class="flaticon-381-inbox"></i> Pending Receive
                                @php
                                    $pendingReceiveCount = App\Models\Ticket::where('status', 'open')
                                        ->where('current_stage', 1)
                                        ->count();
                                @endphp
                                @if ($pendingReceiveCount > 0)
                                    <span class="badge badge-warning badge-sm">{{ $pendingReceiveCount }}</span>
                                @endif
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <i class="flaticon-381-folder-1"></i> Unassigned
                                @php
                                    $unassignedCount = App\Models\Ticket::where('status', 'pending_om')
                                        ->where('current_stage', 3)
                                        ->whereNull('assigned_to')
                                        ->count();
                                @endphp
                                @if ($unassignedCount > 0)
                                    <span class="badge badge-danger badge-sm">{{ $unassignedCount }}</span>
                                @endif
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <i class="flaticon-381-price-tag"></i> Pending VR
                                @php
                                    $pendingVRCount = App\Models\Ticket::where('status', 'pending_vr')
                                        ->where('current_stage', 5)
                                        ->count();
                                @endphp
                                @if ($pendingVRCount > 0)
                                    <span class="badge badge-warning badge-sm">{{ $pendingVRCount }}</span>
                                @endif
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <i class="flaticon-381-stopwatch"></i> Waiting User Check
                                @php
                                    $waitingCheckCount = App\Models\Ticket::where('status', 'completed')
                                        ->where('current_stage', 6)
                                        ->count();
                                @endphp
                                @if ($waitingCheckCount > 0)
                                    <span class="badge badge-info badge-sm">{{ $waitingCheckCount }}</span>
                                @endif
                            </a>
                        </li>

                        <li>
                            <a href="#">
                                <i class="flaticon-381-success"></i> Ready to Close
                                @php
                                    $readyCloseCount = App\Models\Ticket::where('status', 'pending_gm')
                                        ->where('current_stage', 8)
                                        ->count();
                                @endphp
                                @if ($readyCloseCount > 0)
                                    <span class="badge badge-success badge-sm">{{ $readyCloseCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

            <!-- ========================= APPROVAL CENTERS ========================= -->
            @if (auth()->user()->role === 'om')
                <li>
                    <a class="ai-icon" href="#" aria-expanded="false">
                        <i class="flaticon-381-box"></i>
                        <span class="nav-text">OM Approval</span>
                        @php
                            $pendingOMCount = App\Models\Ticket::where('status', 'received')
                                ->where('current_stage', 2)
                                ->count();
                        @endphp
                        @if ($pendingOMCount > 0)
                            <span class="badge badge-danger badge-sm">{{ $pendingOMCount }}</span>
                        @endif
                    </a>
                </li>
            @endif

            @if (auth()->user()->role === 'gm')
                <li>
                    <a class="ai-icon" href="#" aria-expanded="false">
                        <i class="flaticon-381-box"></i>
                        <span class="nav-text">GM Approval</span>
                        @php
                            $pendingGMCount = App\Models\Ticket::where('current_stage', 7)->count();
                        @endphp
                        @if ($pendingGMCount > 0)
                            <span class="badge badge-danger badge-sm">{{ $pendingGMCount }}</span>
                        @endif
                    </a>
                </li>
            @endif

            @if (auth()->user()->role === 'user')
                <li>
                    <a class="ai-icon" href="#" aria-expanded="false">
                        <i class="flaticon-381-check"></i>
                        <span class="nav-text">Pending Verification</span>
                        @php
                            $pendingVerificationCount = App\Models\Ticket::where(
                                'department_id',
                                auth()->user()->department_id,
                            )
                                ->where('status', 'completed')
                                ->where('current_stage', 6)
                                ->count();
                        @endphp
                        @if ($pendingVerificationCount > 0)
                            <span class="badge badge-info badge-sm">{{ $pendingVerificationCount }}</span>
                        @endif
                    </a>
                </li>
            @endif

            <!-- ========================= VOUCHER REQUESTS (VR) ========================= -->
            @if (in_array(auth()->user()->role, ['admin_eng', 'om', 'gm']))
                <li>
                    <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-price-tag"></i>
                        <span class="nav-text">Voucher Requests</span>
                        @php
                            $myVRCount = 0;
                            if (auth()->user()->role === 'admin_eng') {
                                $myVRCount = App\Models\VoucherRequest::where('status', 'pending')->count();
                            } elseif (auth()->user()->role === 'om') {
                                $myVRCount = App\Models\VoucherRequest::where('status', 'admin_approved')->count();
                            } elseif (auth()->user()->role === 'gm') {
                                $myVRCount = App\Models\VoucherRequest::where('status', 'om_approved')->count();
                            }
                        @endphp
                        @if ($myVRCount > 0)
                            <span class="badge badge-warning badge-sm">{{ $myVRCount }}</span>
                        @endif
                    </a>
                    <ul aria-expanded="false">
                        <li>
                            <a href="{{ route('vouchers.index') }}">
                                <i class="flaticon-381-list"></i> All VR
                            </a>
                        </li>

                        @if (auth()->user()->role === 'admin_eng')
                            <li>
                                <a href="#" onclick="openCreateVRModal()">
                                    <i class="flaticon-381-add"></i> Create New VR
                                </a>
                            </li>
                        @endif

                        <li>
                            <a href="{{ route('vouchers.index') }}?filter=pending_my_approval">
                                <i class="flaticon-381-clock"></i> Pending My Approval
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('vouchers.index') }}?filter=approved">
                                <i class="flaticon-381-check"></i> Approved
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('vouchers.index') }}?filter=rejected">
                                <i class="flaticon-381-close"></i> Rejected
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('vouchers.index') }}?filter=paid">
                                <i class="flaticon-381-check-double"></i> Paid
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <!-- ========================= CALENDAR (ALL) ========================= -->
            <li>
                <a class="ai-icon" href="#" aria-expanded="false">
                    <i class="flaticon-381-calendar-1"></i>
                    <span class="nav-text">Calendar</span>
                </a>
            </li>

            <!-- ========================= MASTER DATA (SuperAdmin ONLY!) ========================= -->
            @if (auth()->user()->role === 'superadmin')
                <li>
                    <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-database-2"></i>
                        <span class="nav-text">Master Data</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('admin.departments.index') }}"><i class="flaticon-381-layer-1"></i>
                                Departments</a></li>
                        <li><a href="{{ route('admin.locations.index') }}"><i class="flaticon-381-location-1"></i>
                                Locations</a></li>
                        <li><a href="{{ route('admin.categories.index') }}"><i class="flaticon-381-folder"></i>
                                Categories</a></li>
                        <li><a href="{{ route('admin.priorities.index') }}"><i class="flaticon-381-flag"></i>
                                Priorities</a></li>
                    </ul>
                </li>
            @endif

            <!-- ========================= USER MANAGEMENT ========================= -->
            @if (auth()->user()->role === 'superadmin')
                <li>
                    <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-user-9"></i>
                        <span class="nav-text">User Management</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('admin.users.index') }}"><i class="flaticon-381-user"></i> All
                                Users</a></li>
                        <li><a href="#"><i class="flaticon-381-clock"></i> Pending Approval</a></li>
                        <li><a href="#"><i class="flaticon-381-settings"></i> Role Management</a></li>
                        <li><a href="#"><i class="flaticon-381-trash-1"></i> Inactive Users</a></li>
                    </ul>
                </li>
            @endif

            @if (auth()->user()->role === 'admin_eng')
                <li>
                    <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-user-9"></i>
                        <span class="nav-text">Users</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('admin.users.index') }}"><i class="flaticon-381-user"></i> All
                                Users</a></li>
                        <li><a href="#"><i class="flaticon-381-settings"></i> Technicians</a></li>
                        <li><a href="#"><i class="flaticon-381-layer-1"></i> Department Users</a></li>
                    </ul>
                </li>
            @endif

            @if (auth()->user()->role === 'manager')
                <li>
                    <a class="ai-icon" href="#" aria-expanded="false">
                        <i class="flaticon-381-layer-1"></i>
                        <span class="nav-text">My Department</span>
                    </a>
                </li>
            @endif

            <!-- ========================= REPORTS ========================= -->
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-diploma"></i>
                    <span class="nav-text">Reports</span>
                </a>
                <ul aria-expanded="false">
                    @if (in_array(auth()->user()->role, ['superadmin', 'admin_eng']))
                        <li><a href="#"><i class="flaticon-381-controls-3"></i> Ticket Reports</a></li>
                        <li><a href="#"><i class="flaticon-381-television"></i> User Activity</a></li>
                        <li><a href="#"><i class="flaticon-381-line-chart"></i> Performance Report</a></li>
                        <li><a href="#"><i class="flaticon-381-controls-1"></i> Department Performance</a></li>
                        <li><a href="#"><i class="flaticon-381-price-tag"></i> VR Summary</a></li>
                    @endif

                    @if (in_array(auth()->user()->role, ['gm', 'om']))
                        <li><a href="#"><i class="flaticon-381-controls-1"></i> Department Report</a></li>
                        <li><a href="#"><i class="flaticon-381-line-chart"></i> Approval Summary</a></li>
                        <li><a href="#"><i class="flaticon-381-diploma"></i> Monthly Report</a></li>
                    @endif

                    @if (auth()->user()->role === 'manager')
                        <li><a href="#"><i class="flaticon-381-controls-1"></i> Department Report</a></li>
                        <li><a href="#"><i class="flaticon-381-layer-1"></i> Department Analytics</a></li>
                    @endif

                    @if (auth()->user()->role === 'technician')
                        <li><a href="#"><i class="flaticon-381-controls"></i> My Performance</a></li>
                        <li><a href="#"><i class="flaticon-381-file-1"></i> Completed Tickets</a></li>
                    @endif

                    @if (auth()->user()->role === 'user')
                        <li><a href="#"><i class="flaticon-381-file-1"></i> My Ticket History</a></li>
                        <li><a href="#"><i class="flaticon-381-diploma"></i> Department Summary</a></li>
                    @endif
                </ul>
            </li>

            <!-- ========================= NOTIFICATIONS (ALL) ========================= -->
            <li>
                <a class="ai-icon" href="{{ route('notifications.index') }}">
                    <i class="flaticon-381-ring"></i>
                    <span class="nav-text">Notifications</span>
                    @php
                        $unreadCount = App\Models\Notification::where('user_id', auth()->id())
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="badge badge-danger badge-sm" id="notificationBadge">{{ $unreadCount }}</span>
                    @endif
                </a>
            </li>

            <!-- ========================= ACTIVITY LOGS (SuperAdmin & Admin Eng) ========================= -->
            @if (in_array(auth()->user()->role, ['superadmin', 'admin_eng']))
                <li>
                    <a class="ai-icon" href="{{ route('activity-logs.index') }}">
                        <i class="flaticon-381-notebook"></i>
                        <span class="nav-text">Activity Logs</span>
                    </a>
                </li>
            @endif

            <!-- ========================= SYSTEM SETTINGS (SuperAdmin ONLY!) ========================= -->
            @if (auth()->user()->role === 'superadmin')
                <li>
                    <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-settings-2"></i>
                        <span class="nav-text">System Settings</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="#"><i class="flaticon-381-settings-6"></i> General Settings</a></li>
                        <li><a href="#"><i class="flaticon-381-network"></i> Email Configuration</a></li>
                        <li><a href="#"><i class="flaticon-381-home"></i> Hotel Settings</a></li>
                        <li><a href="#"><i class="flaticon-381-database"></i> Database Backup</a></li>
                        <li><a href="#"><i class="flaticon-381-edit"></i> System Logs</a></li>
                    </ul>
                </li>
            @endif

            <!-- ========================= ACCOUNT (ALL) ========================= -->
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-user-7"></i>
                    <span class="nav-text">Account</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('profile.index') }}"><i class="flaticon-381-user-3"></i> My Profile</a>
                    </li>

                    @if (in_array(auth()->user()->role, ['om', 'gm', 'admin_eng']))
                        <li><a href="#"><i class="flaticon-381-edit"></i> Digital Signature</a></li>
                    @endif

                    @if (auth()->user()->role === 'manager')
                        <li><a href="#"><i class="flaticon-381-layer-1"></i> Manage Department</a></li>
                    @endif

                    <li><a href="#"><i class="flaticon-381-settings-2"></i> Settings</a></li>
                    <li>
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="flaticon-381-exit"></i> Logout
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- Copyright -->
        <div class="copyright">
            <p><strong>Harris Hotel Ticketing</strong> © {{ date('Y') }} All Rights Reserved</p>
            <p class="fs-12">Made with <span class="heart"></span> by VHP Team</p>
        </div>
    </div>
</div>

<!--**********************************
    Mobile Bottom Navigation Bar (Shown on Mobile Only)
***********************************-->
<nav class="mobile-bottom-nav">
    <div class="mobile-nav-container">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="flaticon-381-networking"></i>
            <span>Dashboard</span>
        </a>

        <!-- Tickets -->
        <a href="{{ route('tickets.index') }}"
            class="mobile-nav-item {{ request()->routeIs('tickets.*') && !request()->routeIs('tickets.create') ? 'active' : '' }}">
            <i class="flaticon-381-notepad"></i>
            <span>Tickets</span>
            @php
                $totalTicketsCount = 0;
                if (auth()->user()->role === 'user') {
                    $totalTicketsCount = App\Models\Ticket::where('user_id', auth()->id())->count();
                } elseif (auth()->user()->role === 'technician') {
                    $totalTicketsCount = App\Models\Ticket::where('assigned_to', auth()->id())
                        ->whereIn('status', ['in_progress', 'pending_vr'])
                        ->count();
                } elseif (auth()->user()->role === 'admin_eng') {
                    $totalTicketsCount = App\Models\Ticket::whereIn('status', [
                        'open',
                        'received',
                        'pending_vr',
                    ])->count();
                }
            @endphp
            @if ($totalTicketsCount > 0)
                <span class="mobile-badge">{{ $totalTicketsCount > 99 ? '99+' : $totalTicketsCount }}</span>
            @endif
        </a>

        <!-- Create Ticket (Floating Action Button) -->
        @if (in_array(auth()->user()->role, ['user', 'technician', 'admin_eng']))
            <a href="{{ route('tickets.create') }}" class="mobile-nav-item mobile-fab">
                <div class="fab-button">
                    <i class="flaticon-381-add"></i>
                </div>
            </a>
        @else
            <!-- Calendar for other roles -->
            <a href="#" class="mobile-nav-item {{ request()->is('calendar') ? 'active' : '' }}">
                <i class="flaticon-381-calendar-1"></i>
                <span>Calendar</span>
            </a>
        @endif

        <!-- Notifications -->
        <a href="{{ route('notifications.index') }}"
            class="mobile-nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="flaticon-381-ring"></i>
            <span>Notifikasi</span>
            @php
                $mobileUnreadCount = App\Models\Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
            @endphp
            @if ($mobileUnreadCount > 0)
                <span class="mobile-badge">{{ $mobileUnreadCount > 9 ? '9+' : $mobileUnreadCount }}</span>
            @endif
        </a>

        <!-- Menu (More Options) -->
        <a href="#" class="mobile-nav-item" id="mobileMenuTrigger">
            <i class="flaticon-381-menu-1"></i>
            <span>Menu</span>
        </a>
    </div>
</nav>

<!-- Mobile Menu Modal (Full Screen) -->
<div class="mobile-menu-modal" id="mobileMenuModal">
    <div class="mobile-menu-header">
        <h4>Menu</h4>
        <button class="close-mobile-menu" id="closeMobileMenu" type="button">
            <i class="flaticon-381-close"></i>
        </button>
    </div>

    <div class="mobile-menu-content">
        <div class="mobile-menu-user-info">
            <div class="user-avatar">
                <i class="flaticon-381-user-7"></i>
            </div>
            <div class="user-details">
                <h5>{{ auth()->user()->name }}</h5>
                <p>{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
            </div>
        </div>

        <div class="mobile-menu-list">
            <!-- Approval Centers -->
            @if (auth()->user()->role === 'om')
                <div class="mobile-menu-section-title">Approval</div>
                <a href="#" class="mobile-menu-link">
                    <i class="flaticon-381-box"></i>
                    <span>OM Approval</span>
                    @php
                        $mobilePendingOMCount = App\Models\Ticket::where('status', 'received')
                            ->where('current_stage', 2)
                            ->count();
                    @endphp
                    @if ($mobilePendingOMCount > 0)
                        <span class="mobile-badge">{{ $mobilePendingOMCount }}</span>
                    @endif
                </a>
            @endif

            @if (auth()->user()->role === 'gm')
                <div class="mobile-menu-section-title">Approval</div>
                <a href="#" class="mobile-menu-link">
                    <i class="flaticon-381-box"></i>
                    <span>GM Approval</span>
                    @php
                        $mobilePendingGMCount = App\Models\Ticket::where('current_stage', 7)->count();
                    @endphp
                    @if ($mobilePendingGMCount > 0)
                        <span class="mobile-badge">{{ $mobilePendingGMCount }}</span>
                    @endif
                </a>
            @endif

            @if (auth()->user()->role === 'user')
                <div class="mobile-menu-section-title">Verifikasi</div>
                <a href="#" class="mobile-menu-link">
                    <i class="flaticon-381-check"></i>
                    <span>Pending Verification</span>
                    @php
                        $mobilePendingVerificationCount = App\Models\Ticket::where(
                            'department_id',
                            auth()->user()->department_id,
                        )
                            ->where('status', 'completed')
                            ->where('current_stage', 6)
                            ->count();
                    @endphp
                    @if ($mobilePendingVerificationCount > 0)
                        <span class="mobile-badge">{{ $mobilePendingVerificationCount }}</span>
                    @endif
                </a>
            @endif

            <!-- Voucher Requests -->
            @if (in_array(auth()->user()->role, ['admin_eng', 'om', 'gm']))
                <div class="mobile-menu-section-title">Voucher</div>
                <a href="{{ route('vouchers.index') }}" class="mobile-menu-link">
                    <i class="flaticon-381-price-tag"></i>
                    <span>Voucher Requests</span>
                    @php
                        $mobileMyVRCount = 0;
                        if (auth()->user()->role === 'admin_eng') {
                            $mobileMyVRCount = App\Models\VoucherRequest::where('status', 'pending')->count();
                        } elseif (auth()->user()->role === 'om') {
                            $mobileMyVRCount = App\Models\VoucherRequest::where('status', 'admin_approved')->count();
                        } elseif (auth()->user()->role === 'gm') {
                            $mobileMyVRCount = App\Models\VoucherRequest::where('status', 'om_approved')->count();
                        }
                    @endphp
                    @if ($mobileMyVRCount > 0)
                        <span class="mobile-badge">{{ $mobileMyVRCount }}</span>
                    @endif
                </a>
            @endif

            <!-- Main Menu -->
            <div class="mobile-menu-section-title">Main Menu</div>

            <!-- Calendar -->
            <a href="#" class="mobile-menu-link">
                <i class="flaticon-381-calendar-1"></i>
                <span>Calendar</span>
            </a>

            <!-- Reports -->
            <a href="#" class="mobile-menu-link">
                <i class="flaticon-381-diploma"></i>
                <span>Reports</span>
            </a>

            <!-- Master Data (SuperAdmin) -->
            @if (auth()->user()->role === 'superadmin')
                <div class="mobile-menu-divider"></div>
                <div class="mobile-menu-section-title">Administration</div>
                <a href="#" class="mobile-menu-link">
                    <i class="flaticon-381-database-2"></i>
                    <span>Master Data</span>
                </a>
            @endif

            <!-- User Management -->
            @if (in_array(auth()->user()->role, ['superadmin', 'admin_eng']))
                <a href="{{ route('admin.users.index') }}" class="mobile-menu-link">
                    <i class="flaticon-381-user-9"></i>
                    <span>User Management</span>
                </a>
            @endif

            <!-- Activity Logs -->
            @if (in_array(auth()->user()->role, ['superadmin', 'admin_eng']))
                <a href="{{ route('activity-logs.index') }}" class="mobile-menu-link">
                    <i class="flaticon-381-notebook"></i>
                    <span>Activity Logs</span>
                </a>
            @endif

            <!-- System Settings (SuperAdmin) -->
            @if (auth()->user()->role === 'superadmin')
                <a href="#" class="mobile-menu-link">
                    <i class="flaticon-381-settings-2"></i>
                    <span>System Settings</span>
                </a>
            @endif

            <!-- Account Section -->
            <div class="mobile-menu-divider"></div>
            <div class="mobile-menu-section-title">Account</div>

            <!-- Profile -->
            <a href="{{ route('profile.index') }}" class="mobile-menu-link">
                <i class="flaticon-381-user-3"></i>
                <span>My Profile</span>
            </a>

            <!-- Digital Signature -->
            @if (in_array(auth()->user()->role, ['om', 'gm', 'admin_eng']))
                <a href="#" class="mobile-menu-link">
                    <i class="flaticon-381-edit"></i>
                    <span>Digital Signature</span>
                </a>
            @endif

            <!-- Settings -->
            <a href="#" class="mobile-menu-link">
                <i class="flaticon-381-settings-2"></i>
                <span>Settings</span>
            </a>

            <!-- Logout -->
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();"
                class="mobile-menu-link logout-link">
                <i class="flaticon-381-exit"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>

<!-- Logout Forms -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile Menu Elements
        const mobileMenuTrigger = document.getElementById('mobileMenuTrigger');
        const mobileMenuModal = document.getElementById('mobileMenuModal');
        const closeMobileMenu = document.getElementById('closeMobileMenu');

        // Open mobile menu
        if (mobileMenuTrigger) {
            mobileMenuTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                mobileMenuModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }

        // Close mobile menu via close button
        if (closeMobileMenu) {
            closeMobileMenu.addEventListener('click', function() {
                mobileMenuModal.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        // Close on outside click (background)
        if (mobileMenuModal) {
            mobileMenuModal.addEventListener('click', function(e) {
                if (e.target === mobileMenuModal) {
                    mobileMenuModal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        }

        // Prevent scroll on body when modal is open
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.target.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
        });

        if (mobileMenuModal) {
            observer.observe(mobileMenuModal, {
                attributes: true,
                attributeFilter: ['class']
            });
        }

        // Add ripple effect on mobile nav items (optional)
        const mobileNavItems = document.querySelectorAll('.mobile-nav-item, .mobile-menu-link');

        mobileNavItems.forEach(item => {
            item.addEventListener('touchstart', function(e) {
                this.style.transform = 'scale(0.95)';
            });

            item.addEventListener('touchend', function(e) {
                this.style.transform = '';
            });
        });

        // Handle back button on Android
        window.addEventListener('popstate', function(e) {
            if (mobileMenuModal && mobileMenuModal.classList.contains('active')) {
                e.preventDefault();
                mobileMenuModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Add haptic feedback for FAB button (if supported)
        const fabButton = document.querySelector('.fab-button');
        if (fabButton && 'vibrate' in navigator) {
            fabButton.addEventListener('click', function() {
                navigator.vibrate(10); // 10ms vibration
            });
        }
    });
</script>

<!--**********************************
    Sidebar end
***********************************-->
