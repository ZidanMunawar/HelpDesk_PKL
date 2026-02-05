<!--**********************************
    Sidebar start
***********************************-->
<div class="deznav">
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
                    <!-- All Tickets - ALL roles can view -->
                    <li>
                        <a href="{{ route('tickets.index') }}">
                            <i class="flaticon-381-list"></i> All Tickets
                        </a>
                    </li>

                    <!-- My Tickets - User ONLY -->
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

                    <!-- My Department Tickets - User ONLY -->
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

                    <!-- Assigned to Me - Technician ONLY -->
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

                    <!-- Pending Receive - Admin Eng ONLY (Stage 1 → 2) -->
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
                    @endif

                    <!-- Unassigned - Admin Eng ONLY (Stage 3 sudah OM approved, belum assign) -->
                    @if (auth()->user()->role === 'admin_eng')
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
                    @endif

                    <!-- Pending VR - Admin Eng ONLY -->
                    @if (auth()->user()->role === 'admin_eng')
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
                    @endif

                    <!-- Waiting User Check - Admin Eng (Stage 6 → 7) -->
                    @if (auth()->user()->role === 'admin_eng')
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
                    @endif

                    <!-- Ready to Close - Admin Eng (Stage 8, GM approved, belum close) -->
                    @if (auth()->user()->role === 'admin_eng')
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

            <!-- OM Approval - OM ONLY (Stage 2 → 3) -->
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

            <!-- GM Approval - GM ONLY (Stage 7 → 8) -->
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

            <!-- User Verification - User ONLY (Stage 6 → 7) -->
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
                    <!-- Main VR List -->
                    <li>
                        <a href="{{ route('vouchers.index') }}">
                            <i class="flaticon-381-list"></i> All VR
                            @php
                            $allVRCount = App\Models\VoucherRequest::count();
                            @endphp
                            @if ($allVRCount > 0)
                            <span class="badge badge-primary badge-sm">{{ $allVRCount }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- Create VR Link (Admin Eng Only) -->
                    @if (auth()->user()->role === 'admin_eng')
                    <li>
                        <a href="#" onclick="openCreateVRModal()">
                            <i class="flaticon-381-add"></i> Create New VR
                        </a>
                    </li>
                    @endif

                    <!-- My Approval Queue -->
                    <li>
                        <a href="{{ route('vouchers.index') }}?filter=pending_my_approval">
                            <i class="flaticon-381-clock"></i> Pending My Approval
                            @if ($myVRCount > 0)
                            <span class="badge badge-danger badge-sm">{{ $myVRCount }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- Approved VRs -->
                    <li>
                        <a href="{{ route('vouchers.index') }}?filter=approved">
                            <i class="flaticon-381-check"></i> Approved
                            @php
                            $approvedCount = App\Models\VoucherRequest::whereIn('status', [
                            'admin_approved',
                            'om_approved',
                            'gm_approved',
                            ])->count();
                            @endphp
                            @if ($approvedCount > 0)
                            <span class="badge badge-success badge-sm">{{ $approvedCount }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- Rejected VRs -->
                    <li>
                        <a href="{{ route('vouchers.index') }}?filter=rejected">
                            <i class="flaticon-381-close"></i> Rejected
                            @php
                            $rejectedCount = App\Models\VoucherRequest::where('status', 'rejected')->count();
                            @endphp
                            @if ($rejectedCount > 0)
                            <span class="badge badge-danger badge-sm">{{ $rejectedCount }}</span>
                            @endif
                        </a>
                    </li>

                    <!-- Paid VRs -->
                    <li>
                        <a href="{{ route('vouchers.index') }}?filter=paid">
                            <i class="flaticon-381-check-double"></i> Paid
                            @php
                            $paidCount = App\Models\VoucherRequest::where('status', 'paid')->count();
                            @endphp
                            @if ($paidCount > 0)
                            <span class="badge badge-info badge-sm">{{ $paidCount }}</span>
                            @endif
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
                    <li>
                        <a href="{{ route('admin.departments.index') }}">
                            <i class="flaticon-381-layer-1"></i> Departments
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.locations.index') }}">
                            <i class="flaticon-381-location-1"></i> Locations
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}">
                            <i class="flaticon-381-folder"></i> Categories
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.priorities.index') }}">
                            <i class="flaticon-381-flag"></i> Priorities
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- ========================= USER MANAGEMENT ========================= -->

            <!-- SuperAdmin - Full Access -->
            @if (auth()->user()->role === 'superadmin')
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-user-9"></i>
                    <span class="nav-text">User Management</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('admin.users.index') }}">
                            <i class="flaticon-381-user"></i> All Users
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="flaticon-381-clock"></i> Pending Approval
                            @php
                            $pendingUsersCount = App\Models\User::where('status', 'pending')->count();
                            @endphp
                            @if ($pendingUsersCount > 0)
                            <span class="badge badge-warning badge-sm">{{ $pendingUsersCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="flaticon-381-settings"></i> Role Management
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="flaticon-381-trash-1"></i> Inactive Users
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Admin Eng - Limited (NO CREATE/DELETE!) -->
            @if (auth()->user()->role === 'admin_eng')
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-user-9"></i>
                    <span class="nav-text">Users</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('admin.users.index') }}">
                            <i class="flaticon-381-user"></i> All Users
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="flaticon-381-settings"></i> Technicians
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="flaticon-381-layer-1"></i> Department Users
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Manager - View Department Only -->
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
                    <!-- SuperAdmin & Admin Eng Reports -->
                    @if (in_array(auth()->user()->role, ['superadmin', 'admin_eng']))
                    <li><a href="#"><i class="flaticon-381-controls-3"></i> Ticket Reports</a></li>
                    <li><a href="#"><i class="flaticon-381-television"></i> User Activity</a></li>
                    <li><a href="#"><i class="flaticon-381-line-chart"></i> Performance Report</a></li>
                    <li><a href="#"><i class="flaticon-381-controls-1"></i> Department Performance</a></li>
                    <li><a href="#"><i class="flaticon-381-price-tag"></i> VR Summary</a></li>
                    @endif

                    <!-- GM/OM Reports -->
                    @if (in_array(auth()->user()->role, ['gm', 'om']))
                    <li><a href="#"><i class="flaticon-381-controls-1"></i> Department Report</a></li>
                    <li><a href="#"><i class="flaticon-381-line-chart"></i> Approval Summary</a></li>
                    <li><a href="#"><i class="flaticon-381-diploma"></i> Monthly Report</a></li>
                    @endif

                    <!-- Manager Reports -->
                    @if (auth()->user()->role === 'manager')
                    <li><a href="#"><i class="flaticon-381-controls-1"></i> Department Report</a></li>
                    <li><a href="#"><i class="flaticon-381-layer-1"></i> Department Analytics</a></li>
                    @endif

                    <!-- Technician Reports -->
                    @if (auth()->user()->role === 'technician')
                    <li><a href="#"><i class="flaticon-381-controls"></i> My Performance</a></li>
                    <li><a href="#"><i class="flaticon-381-file-1"></i> Completed Tickets</a></li>
                    @endif

                    <!-- User Reports -->
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
                    @php
                    $recentActivityCount = App\Models\ActivityLog::whereDate('created_at', today())->count();
                    @endphp
                    @if ($recentActivityCount > 0)
                    <span class="badge badge-info badge-sm" title="{{ $recentActivityCount }} activities today">
                        {{ $recentActivityCount > 99 ? '99+' : $recentActivityCount }}
                    </span>
                    @endif
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
                    <li>
                        <a href="{{ route('profile.index') }}">
                            <i class="flaticon-381-user-3"></i> My Profile
                        </a>
                    </li>

                    <!-- Permanent Signature - OM, GM, Admin Eng ONLY -->
                    @if (in_array(auth()->user()->role, ['om', 'gm', 'admin_eng']))
                    <li>
                        <a href="#">
                            <i class="flaticon-381-edit"></i> Digital Signature
                        </a>
                    </li>
                    @endif

                    <!-- Manager - Edit Department Name -->
                    @if (auth()->user()->role === 'manager')
                    <li>
                        <a href="#">
                            <i class="flaticon-381-layer-1"></i> Manage Department
                        </a>
                    </li>
                    @endif

                    <li>
                        <a href="#">
                            <i class="flaticon-381-settings-2"></i> Settings
                        </a>
                    </li>
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

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
<!--**********************************
    Sidebar end
***********************************-->