<!-- sidebar.blade.php -->
@include('layouts.partials.part.sidebar-style')

<!--**********************************
    Desktop Sidebar
***********************************-->
<div class="deznav desktop-sidebar">
    <div class="deznav-scroll">
        <!-- Quick Create Ticket Button sesuai role -->
        @if (in_array(auth()->user()->role, ['user', 'manager', 'admin_eng']))
            <a href="{{ route('tickets.create') }}" class="add-menu-sidebar">
                <i class="fas fa-plus-circle"></i> New Ticket
            </a>
        @endif

        <ul class="metismenu" id="menu">
            <!-- ========================= DASHBOARD (ALL ROLES) ========================= -->
            <li>
                <a class="ai-icon" href="{{ route('dashboard') }}" aria-expanded="false">
                    <i class="fas fa-chart-pie"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!-- ========================= APPROVAL CENTERS (OM & GM) ========================= -->
            @if (auth()->user()->role === 'om')
                <li>
                    <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-check-double"></i>
                        <span class="nav-text">OM Approval</span>
                        @php
                            $omApprovalCount = App\Models\Ticket::where('status', 'pending_om')
                                ->where('current_stage', 3)
                                ->count();
                            $omVRCount = App\Models\VoucherRequest::where('status', 'admin_approved')->count();
                            $totalOmCount = $omApprovalCount + $omVRCount;
                        @endphp
                        @if ($totalOmCount > 0)
                            <span class="badge badge-danger badge-sm">{{ $totalOmCount }}</span>
                        @endif
                    </a>
                    <ul aria-expanded="false">
                        <li>
                            <a href="{{ route('tickets.index') }}?status=pending_om">
                                <i class="fas fa-clipboard-list"></i> MR Approval
                                @if ($omApprovalCount > 0)
                                    <span class="badge badge-danger badge-sm">{{ $omApprovalCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vouchers.index') }}?status=admin_approved">
                                <i class="fas fa-ticket-alt"></i> VR Approval
                                @if ($omVRCount > 0)
                                    <span class="badge badge-danger badge-sm">{{ $omVRCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            @if (auth()->user()->role === 'gm')
                <li>
                    <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-check-circle"></i>
                        <span class="nav-text">GM Approval</span>
                        @php
                            $gmApprovalCount = App\Models\Ticket::where('status', 'pending_gm')
                                ->where('current_stage', 8)
                                ->count();
                            $gmVRCount = App\Models\VoucherRequest::where('status', 'om_approved')->count();
                            $totalGmCount = $gmApprovalCount + $gmVRCount;
                        @endphp
                        @if ($totalGmCount > 0)
                            <span class="badge badge-danger badge-sm">{{ $totalGmCount }}</span>
                        @endif
                    </a>
                    <ul aria-expanded="false">
                        <li>
                            <a href="{{ route('tickets.index') }}?status=pending_gm">
                                <i class="fas fa-clipboard-list"></i> MR Approval
                                @if ($gmApprovalCount > 0)
                                    <span class="badge badge-danger badge-sm">{{ $gmApprovalCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('vouchers.index') }}?status=om_approved">
                                <i class="fas fa-ticket-alt"></i> VR Approval
                                @if ($gmVRCount > 0)
                                    <span class="badge badge-danger badge-sm">{{ $gmVRCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <!-- ========================= TICKETS ========================= -->
            <li class="{{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-ticket-alt"></i>
                    <span class="nav-text">Tickets</span>
                </a>
                <ul aria-expanded="false" class="{{ request()->routeIs('tickets.*') ? 'show' : '' }}">
                    <!-- All Tickets - Tetap tanpa badge -->
                    <li
                        class="{{ request()->routeIs('tickets.index') && !request()->anyFilled(['my_tickets', 'assigned', 'department_filter', 'status', 'stage', 'unassigned']) ? 'active' : '' }}">
                        <a href="{{ route('tickets.index') }}">
                            <i class="fas fa-list"></i> All Tickets
                        </a>
                    </li>

                    <!-- My Tickets untuk User, Manager, Admin Eng -->
                    @if (in_array(auth()->user()->role, ['user', 'manager', 'admin_eng']))
                        <li class="{{ request()->get('my_tickets') == '1' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?my_tickets=1">
                                <i class="fas fa-user"></i> My Tickets
                                @php
                                    $myTicketsCount = App\Models\Ticket::where('user_id', auth()->id())->count();
                                @endphp
                                @if ($myTicketsCount > 0)
                                    <span class="badge badge-primary badge-sm">{{ $myTicketsCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <!-- Assigned to Me untuk Technician -->
                    @if (auth()->user()->role === 'technician')
                        <li class="{{ request()->get('assigned') == '1' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?assigned=1">
                                <i class="fas fa-user-check"></i> Assigned to Me
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

                    <!-- Department Tickets untuk User dengan department atau Manager -->
                    @if ((auth()->user()->role === 'user' && auth()->user()->department_id) || auth()->user()->role === 'manager')
                        <li class="{{ request()->get('department_filter') == '1' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?department_filter=1">
                                <i class="fas fa-users"></i> Department Tickets
                                @php
                                    $deptCount = auth()->user()->department_id
                                        ? App\Models\Ticket::where('department_id', auth()->user()->department_id)
                                            ->whereNotIn('status', ['closed', 'cancelled'])
                                            ->count()
                                        : 0;
                                @endphp
                                @if ($deptCount > 0)
                                    <span class="badge badge-primary badge-sm">{{ $deptCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <!-- Pending Check untuk User -->
                    @if (auth()->user()->role === 'user' && auth()->user()->department_id)
                        <li
                            class="{{ request()->get('status') == 'completed' && request()->get('stage') == '6' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?status=completed&stage=6">
                                <i class="fas fa-check-square"></i> Pending Check
                                @php
                                    $pendingCheckCount = App\Models\Ticket::where(
                                        'department_id',
                                        auth()->user()->department_id,
                                    )
                                        ->where('status', 'completed')
                                        ->where('current_stage', 6)
                                        ->count();
                                @endphp
                                @if ($pendingCheckCount > 0)
                                    <span class="badge badge-info badge-sm">{{ $pendingCheckCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <!-- Menu khusus Admin Engineering -->
                    @if (auth()->user()->role === 'admin_eng')
                        <li
                            class="{{ request()->get('status') == 'open' && !request()->has('unassigned') ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?status=open">
                                <i class="fas fa-inbox"></i> Pending Receive
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

                        <li
                            class="{{ request()->get('status') == 'pending_om' && request()->get('unassigned') == '1' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?status=pending_om&unassigned=1">
                                <i class="fas fa-user-slash"></i> Unassigned
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

                        <li class="{{ request()->get('status') == 'pending_vr' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?status=pending_vr">
                                <i class="fas fa-tag"></i> Pending VR
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

                        <li class="{{ request()->get('status') == 'ready_for_closure' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?status=ready_for_closure">
                                <i class="fas fa-check-circle"></i> Ready to Close
                                @php
                                    $readyCloseCount = App\Models\Ticket::where('status', 'ready_for_closure')
                                        ->where('current_stage', 8)
                                        ->count();
                                @endphp
                                @if ($readyCloseCount > 0)
                                    <span class="badge badge-success badge-sm">{{ $readyCloseCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <!-- Menu khusus OM (Operation Manager) -->
                    @if (auth()->user()->role === 'om')
                        <li class="{{ request()->get('status') == 'pending_om' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?status=pending_om">
                                <i class="fas fa-hourglass-half"></i> Pending My Approval
                                @php
                                    $pendingOMCount = App\Models\Ticket::where('status', 'pending_om')
                                        ->where('current_stage', 3)
                                        ->count();
                                @endphp
                                @if ($pendingOMCount > 0)
                                    <span class="badge badge-danger badge-sm">{{ $pendingOMCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <!-- Menu khusus GM (General Manager) -->
                    @if (auth()->user()->role === 'gm')
                        <li class="{{ request()->get('status') == 'pending_gm' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?status=pending_gm">
                                <i class="fas fa-hourglass-half"></i> Pending My Approval
                                @php
                                    $pendingGMCount = App\Models\Ticket::where('status', 'pending_gm')
                                        ->where('current_stage', 8)
                                        ->count();
                                @endphp
                                @if ($pendingGMCount > 0)
                                    <span class="badge badge-danger badge-sm">{{ $pendingGMCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <!-- Menu untuk Manager -->
                    @if (auth()->user()->role === 'manager')
                        <li
                            class="{{ request()->get('status') == 'in_progress' && request()->get('department') == auth()->user()->department_id ? 'active' : '' }}">
                            <a
                                href="{{ route('tickets.index') }}?status=in_progress&department={{ auth()->user()->department_id }}">
                                <i class="fas fa-spinner"></i> Dept In Progress
                                @php
                                    $deptInProgress = auth()->user()->department_id
                                        ? App\Models\Ticket::where('department_id', auth()->user()->department_id)
                                            ->where('status', 'in_progress')
                                            ->count()
                                        : 0;
                                @endphp
                                @if ($deptInProgress > 0)
                                    <span class="badge badge-primary badge-sm">{{ $deptInProgress }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

            <!-- ========================= VOUCHER REQUESTS (VR) ========================= -->
            @php
                $user = auth()->user();
                $role = $user->role;

                // Hitung badge sekali aja
                $badgeCount = 0;
                if ($role === 'admin_eng') {
                    $badgeCount = App\Models\VoucherRequest::where('status', 'pending')->count();
                } elseif ($role === 'om') {
                    $badgeCount = App\Models\VoucherRequest::where('status', 'admin_approved')->count();
                } elseif ($role === 'gm') {
                    $badgeCount = App\Models\VoucherRequest::where('status', 'om_approved')->count();
                }
            @endphp

            @if (in_array($role, ['admin_eng', 'om', 'gm', 'superadmin']))
                <li class="{{ request()->routeIs('vouchers.*') ? 'active' : '' }}">
                    <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span class="nav-text">Voucher Requests</span>
                        @if ($badgeCount > 0)
                            <span class="badge badge-warning badge-sm">{{ $badgeCount }}</span>
                        @endif
                    </a>
                    <ul aria-expanded="false">
                        <li
                            class="{{ request()->routeIs('vouchers.index') && !request()->get('status') ? 'active' : '' }}">
                            <a href="{{ route('vouchers.index') }}">
                                <i class="fas fa-list"></i> All VR
                            </a>
                        </li>

                        @if (in_array($role, ['admin_eng', 'om', 'gm']))
                            @php
                                $pendingStatus =
                                    $role === 'admin_eng'
                                        ? 'pending'
                                        : ($role === 'om'
                                            ? 'admin_approved'
                                            : 'om_approved');
                            @endphp
                            <li
                                class="{{ request()->routeIs('vouchers.index') && request()->get('status') === $pendingStatus ? 'active' : '' }}">
                                <a href="{{ route('vouchers.index') }}?status={{ $pendingStatus }}">
                                    <i class="fas fa-hourglass-half"></i> Pending My Approval
                                    @if ($badgeCount > 0)
                                        <span class="badge badge-warning badge-sm">{{ $badgeCount }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif

                        <li
                            class="{{ request()->routeIs('vouchers.index') && request()->get('status') === 'gm_approved' ? 'active' : '' }}">
                            <a href="{{ route('vouchers.index') }}?status=gm_approved">
                                <i class="fas fa-check-circle"></i> Approved
                            </a>
                        </li>

                        <li
                            class="{{ request()->routeIs('vouchers.index') && request()->get('status') === 'paid' ? 'active' : '' }}">
                            <a href="{{ route('vouchers.index') }}?status=paid">
                                <i class="fas fa-money-bill-wave"></i> Paid
                            </a>
                        </li>

                        <li
                            class="{{ request()->routeIs('vouchers.index') && request()->get('status') === 'rejected' ? 'active' : '' }}">
                            <a href="{{ route('vouchers.index') }}?status=rejected">
                                <i class="fas fa-times-circle"></i> Rejected
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
            <!-- ========================= CALENDAR (ALL) ========================= -->
            <li class="{{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <a class="ai-icon" href="{{ route('calendar.index') }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="nav-text">Calendar</span>
                </a>
            </li>

            <!-- ========================= MY DEPARTMENT (Manager) ========================= -->
            @if (auth()->user()->role === 'manager')
                <li class="{{ request()->routeIs('my-department.*') ? 'active' : '' }}">
                    <a class="ai-icon" href="{{ route('my-department.index') }}">
                        <i class="fas fa-building"></i>
                        <span class="nav-text">My Department</span>
                    </a>
                </li>
            @endif

            <!-- ========================= MASTER DATA (SuperAdmin ONLY) ========================= -->
            @if (auth()->user()->role === 'superadmin')
                <li>
                    <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-database"></i>
                        <span class="nav-text">Master Data</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('admin.departments.index') }}"><i class="fas fa-building"></i>
                                Departments</a></li>
                        <li><a href="{{ route('admin.locations.index') }}"><i class="fas fa-map-marker-alt"></i>
                                Locations</a></li>
                        <li><a href="{{ route('admin.categories.index') }}"><i class="fas fa-folder"></i>
                                Categories</a></li>
                        <li><a href="{{ route('admin.priorities.index') }}"><i class="fas fa-flag"></i>
                                Priorities</a></li>
                    </ul>
                </li>
            @endif
            <!-- ========================= USER MANAGEMENT (SUPERADMIN ONLY) ========================= -->
            @if (auth()->user()->role === 'superadmin')
                <li
                    class="{{ request()->routeIs('admin.users.*') ||
                    request()->get('status') == 'pending' ||
                    request()->get('status') == 'inactive' ||
                    request()->get('verified') == 'unverified'
                        ? 'mm-active'
                        : '' }}">
                    <a class="has-arrow ai-icon" href="javascript:void()"
                        aria-expanded="{{ request()->routeIs('admin.users.*') ||
                        request()->get('status') == 'pending' ||
                        request()->get('status') == 'inactive' ||
                        request()->get('verified') == 'unverified'
                            ? 'true'
                            : 'false' }}">
                        <i class="fas fa-users-cog"></i>
                        <span class="nav-text">User Management</span>
                        @php
                            $pendingCount = App\Models\User::where('status', 'pending')->count();
                        @endphp
                        @if ($pendingCount > 0)
                            <span class="badge badge-danger badge-sm">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <ul aria-expanded="{{ request()->routeIs('admin.users.*') ||
                    request()->get('status') == 'pending' ||
                    request()->get('status') == 'inactive' ||
                    request()->get('verified') == 'unverified'
                        ? 'true'
                        : 'false' }}"
                        class="mm-collapse">
                        <!-- All Users -->
                        <li
                            class="{{ request()->routeIs('admin.users.index') && !request()->hasAny(['status', 'verified']) ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.users.index') }}">
                                <i class="fas fa-user"></i> All Users
                            </a>
                        </li>
                        <!-- Pending Approval -->
                        <li class="{{ request()->get('status') == 'pending' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.users.index', ['status' => 'pending']) }}">
                                <i class="fas fa-hourglass-half"></i> Pending
                                @php
                                    $pendingCount = App\Models\User::where('status', 'pending')->count();
                                @endphp
                                @if ($pendingCount > 0)
                                    <span class="badge badge-danger badge-sm ms-auto">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                        <!-- Inactive Users -->
                        <li class="{{ request()->get('status') == 'inactive' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}">
                                <i class="fas fa-ban"></i> Inactive
                                @php
                                    $inactiveCount = App\Models\User::where('status', 'inactive')->count();
                                @endphp
                                @if ($inactiveCount > 0)
                                    <span class="badge badge-danger badge-sm ms-auto">{{ $inactiveCount }}</span>
                                @endif
                            </a>
                        </li>
                        <!-- Unverified Email -->
                        <li class="{{ request()->get('verified') == 'unverified' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.users.index', ['verified' => 'unverified']) }}">
                                <i class="fas fa-envelope"></i> Unverified
                                @php
                                    $unverifiedCount = App\Models\User::whereNull('email_verified_at')->count();
                                @endphp
                                @if ($unverifiedCount > 0)
                                    <span class="badge badge-warning badge-sm ms-auto">{{ $unverifiedCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <!-- ========================= NOTIFICATIONS (ALL) ========================= -->
            <li>
                <a class="ai-icon" href="{{ route('notifications.index') }}">
                    <i class="fas fa-bell"></i>
                    <span class="nav-text">Notifications</span>
                    @php
                        $unreadCount = App\Models\Notification::where('user_id', auth()->id())
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="badge badge-danger badge-sm">{{ $unreadCount }}</span>
                    @endif
                </a>
            </li>

            <!-- ========================= ACTIVITY LOGS (SuperAdmin & Admin Eng) ========================= -->
            @if (in_array(auth()->user()->role, ['superadmin', 'admin_eng']))
                <li>
                    <a class="ai-icon" href="{{ route('activity-logs.index') }}">
                        <i class="fas fa-history"></i>
                        <span class="nav-text">Activity Logs</span>
                    </a>
                </li>
            @endif

            {{-- <!-- ========================= SYSTEM SETTINGS (SuperAdmin ONLY) ========================= -->
            @if (auth()->user()->role === 'superadmin')
                <li>
                    <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-cog"></i>
                        <span class="nav-text">System Settings</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="#"><i class="fas fa-sliders-h"></i> General Settings</a></li>
                        <li><a href="#"><i class="fas fa-envelope"></i> Email Configuration</a></li>
                        <li><a href="#"><i class="fas fa-hotel"></i> Hotel Settings</a></li>
                        <li><a href="#"><i class="fas fa-database"></i> Database Backup</a></li>
                        <li><a href="#"><i class="fas fa-edit"></i> System Logs</a></li>
                    </ul>
                </li>
            @endif --}}
            <!-- ========================= ACCOUNT (ALL) ========================= -->
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                    <span class="nav-text">Account</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('profile.index') }}">
                            <i class="fas fa-id-card"></i> My Profile
                        </a>
                    </li>
                    {{--
                    @if (auth()->user()->role === 'manager')
                        <li>
                            <a href="#">
                                <i class="fas fa-building"></i> Manage Department
                            </a>
                        </li>
                    @endif --}}

                    <li>
                        <a href="javascript:void(0)" onclick="confirmLogout()">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- Copyright -->
        <div class="copyright">
            <p><strong>Harris Hotel Ticketing</strong> © {{ date('Y') }} All Rights Reserved</p>
            <p class="fs-12">Made with <span class="heart"></span> by IT Harris</p>
        </div>
    </div>
</div>

<!--**********************************
    Mobile Bottom Navigation Bar
***********************************-->
<nav class="mobile-bottom-nav">
    <div class="mobile-nav-container">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>

        <!-- Tickets -->
        <a href="{{ route('tickets.index') }}"
            class="mobile-nav-item {{ request()->routeIs('tickets.*') && !request()->routeIs('tickets.create') ? 'active' : '' }}">
            <i class="fas fa-ticket-alt"></i>
            <span>Tickets</span>
            @php
                $mobileTicketCount = 0;
                if (auth()->user()->role === 'user') {
                    $mobileTicketCount = App\Models\Ticket::where('user_id', auth()->id())->count();
                } elseif (auth()->user()->role === 'technician') {
                    $mobileTicketCount = App\Models\Ticket::where('assigned_to', auth()->id())
                        ->whereIn('status', ['in_progress', 'pending_vr'])
                        ->count();
                } elseif (auth()->user()->role === 'admin_eng') {
                    $mobileTicketCount = App\Models\Ticket::whereIn('status', [
                        'open',
                        'pending_vr',
                        'ready_for_closure',
                    ])
                        ->whereIn('current_stage', [1, 5, 8])
                        ->count();
                } elseif (auth()->user()->role === 'om') {
                    $mobileTicketCount = App\Models\Ticket::where('status', 'pending_om')
                        ->where('current_stage', 3)
                        ->count();
                } elseif (auth()->user()->role === 'gm') {
                    $mobileTicketCount = App\Models\Ticket::where('status', 'pending_gm')
                        ->where('current_stage', 8)
                        ->count();
                }
            @endphp
            @if ($mobileTicketCount > 0)
                <span class="mobile-badge">{{ $mobileTicketCount > 99 ? '99+' : $mobileTicketCount }}</span>
            @endif
        </a>

        <!-- Create Ticket FAB / Calendar -->
        @if (in_array(auth()->user()->role, ['user', 'manager', 'admin_eng']))
            <a href="{{ route('tickets.create') }}" class="mobile-nav-item mobile-fab">
                <div class="fab-button">
                    <i class="fas fa-plus"></i>
                </div>
            </a>
        @else
            <a href="{{ route('calendar.index') }}" class="mobile-nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendar</span>
            </a>
        @endif

        <!-- Notifications -->
        <a href="{{ route('notifications.index') }}"
            class="mobile-nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i>
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
            <i class="fas fa-bars"></i>
            <span>Menu</span>
        </a>
    </div>
</nav>

<!-- Mobile Menu Modal dengan Accordion -->
<div class="mobile-menu-modal" id="mobileMenuModal">
    <div class="mobile-menu-header">
        <h4>Menu</h4>
        <button class="close-mobile-menu" id="closeMobileMenu" type="button">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="mobile-menu-content">
        <div class="mobile-menu-user-info">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-details">
                <h5>{{ auth()->user()->name }}</h5>
                <p>{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
            </div>
        </div>

        <div class="mobile-menu-list">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="mobile-menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>

            <!-- Quick Create Ticket (Mobile version) -->
            @if (in_array(auth()->user()->role, ['user', 'manager', 'admin_eng']))
                <a href="{{ route('tickets.create') }}" class="mobile-menu-link">
                    <i class="fas fa-plus-circle"></i>
                    <span>New Ticket</span>
                </a>
            @endif

            <!-- ========== APPROVAL CENTERS (OM & GM) ========== -->
            @if (auth()->user()->role === 'om')
                <div class="mobile-menu-accordion">
                    <a href="javascript:void(0)" class="mobile-menu-accordion-header">
                        <i class="fas fa-check-double"></i>
                        <span>OM Approval</span>
                        <i class="fas fa-chevron-right accordion-icon"></i>
                        @php
                            $omApprovalCount = App\Models\Ticket::where('status', 'pending_om')
                                ->where('current_stage', 3)
                                ->count();
                            $omVRCount = App\Models\VoucherRequest::where('status', 'admin_approved')->count();
                            $totalOmCount = $omApprovalCount + $omVRCount;
                        @endphp
                        @if ($totalOmCount > 0)
                            <span class="mobile-badge">{{ $totalOmCount }}</span>
                        @endif
                    </a>
                    <div class="mobile-menu-accordion-content">
                        <a href="{{ route('tickets.index') }}?status=pending_om"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('status') == 'pending_om' ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>MR Approval</span>
                            @if ($omApprovalCount > 0)
                                <span class="mobile-badge">{{ $omApprovalCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('vouchers.index') }}?status=admin_approved"
                            class="mobile-menu-sublink {{ request()->routeIs('vouchers.index') && request()->get('status') == 'admin_approved' ? 'active' : '' }}">
                            <i class="fas fa-ticket-alt"></i>
                            <span>VR Approval</span>
                            @if ($omVRCount > 0)
                                <span class="mobile-badge">{{ $omVRCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            @endif

            @if (auth()->user()->role === 'gm')
                <div class="mobile-menu-accordion">
                    <a href="javascript:void(0)" class="mobile-menu-accordion-header">
                        <i class="fas fa-check-circle"></i>
                        <span>GM Approval</span>
                        <i class="fas fa-chevron-right accordion-icon"></i>
                        @php
                            $gmApprovalCount = App\Models\Ticket::where('status', 'pending_gm')
                                ->where('current_stage', 8)
                                ->count();
                            $gmVRCount = App\Models\VoucherRequest::where('status', 'om_approved')->count();
                            $totalGmCount = $gmApprovalCount + $gmVRCount;
                        @endphp
                        @if ($totalGmCount > 0)
                            <span class="mobile-badge">{{ $totalGmCount }}</span>
                        @endif
                    </a>
                    <div class="mobile-menu-accordion-content">
                        <a href="{{ route('tickets.index') }}?status=pending_gm"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('status') == 'pending_gm' ? 'active' : '' }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>MR Approval</span>
                            @if ($gmApprovalCount > 0)
                                <span class="mobile-badge">{{ $gmApprovalCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('vouchers.index') }}?status=om_approved"
                            class="mobile-menu-sublink {{ request()->routeIs('vouchers.index') && request()->get('status') == 'om_approved' ? 'active' : '' }}">
                            <i class="fas fa-ticket-alt"></i>
                            <span>VR Approval</span>
                            @if ($gmVRCount > 0)
                                <span class="mobile-badge">{{ $gmVRCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            @endif

            <!-- ========== TICKETS ACCORDION ========== -->
            <div class="mobile-menu-accordion">
                <a href="javascript:void(0)" class="mobile-menu-accordion-header">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Tickets</span>
                    <i class="fas fa-chevron-right accordion-icon"></i>
                </a>
                <div class="mobile-menu-accordion-content">
                    <!-- All Tickets -->
                    <a href="{{ route('tickets.index') }}"
                        class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && !request()->anyFilled(['my_tickets', 'assigned', 'department_filter', 'status', 'stage', 'unassigned']) ? 'active' : '' }}">
                        <i class="fas fa-list"></i>
                        <span>All Tickets</span>
                    </a>

                    <!-- My Tickets -->
                    @if (in_array(auth()->user()->role, ['user', 'manager', 'admin_eng']))
                        @php
                            $myTicketsCount = App\Models\Ticket::where('user_id', auth()->id())->count();
                        @endphp
                        <a href="{{ route('tickets.index') }}?my_tickets=1"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('my_tickets') == '1' ? 'active' : '' }}">
                            <i class="fas fa-user"></i>
                            <span>My Tickets</span>
                            @if ($myTicketsCount > 0)
                                <span class="mobile-badge">{{ $myTicketsCount }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- Assigned to Me -->
                    @if (auth()->user()->role === 'technician')
                        @php
                            $assignedCount = App\Models\Ticket::where('assigned_to', auth()->id())
                                ->whereIn('status', ['in_progress', 'pending_vr'])
                                ->count();
                        @endphp
                        <a href="{{ route('tickets.index') }}?assigned=1"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('assigned') == '1' ? 'active' : '' }}">
                            <i class="fas fa-user-check"></i>
                            <span>Assigned to Me</span>
                            @if ($assignedCount > 0)
                                <span class="mobile-badge">{{ $assignedCount }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- Department Tickets -->
                    @if ((auth()->user()->role === 'user' && auth()->user()->department_id) || auth()->user()->role === 'manager')
                        @php
                            $deptCount = auth()->user()->department_id
                                ? App\Models\Ticket::where('department_id', auth()->user()->department_id)
                                    ->whereNotIn('status', ['closed', 'cancelled'])
                                    ->count()
                                : 0;
                        @endphp
                        <a href="{{ route('tickets.index') }}?department_filter=1"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('department_filter') == '1' ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Department Tickets</span>
                            @if ($deptCount > 0)
                                <span class="mobile-badge">{{ $deptCount }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- Pending Check untuk User -->
                    @if (auth()->user()->role === 'user' && auth()->user()->department_id)
                        @php
                            $pendingCheckCount = App\Models\Ticket::where(
                                'department_id',
                                auth()->user()->department_id,
                            )
                                ->where('status', 'completed')
                                ->where('current_stage', 6)
                                ->count();
                        @endphp
                        <a href="{{ route('tickets.index') }}?status=completed&stage=6"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('status') == 'completed' && request()->get('stage') == '6' ? 'active' : '' }}">
                            <i class="fas fa-check-square"></i>
                            <span>Pending Check</span>
                            @if ($pendingCheckCount > 0)
                                <span class="mobile-badge">{{ $pendingCheckCount }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- Menu Admin Engineering -->
                    @if (auth()->user()->role === 'admin_eng')
                        @php
                            $pendingReceiveCount = App\Models\Ticket::where('status', 'open')
                                ->where('current_stage', 1)
                                ->count();
                            $unassignedCount = App\Models\Ticket::where('status', 'pending_om')
                                ->where('current_stage', 3)
                                ->whereNull('assigned_to')
                                ->count();
                            $pendingVRCount = App\Models\Ticket::where('status', 'pending_vr')
                                ->where('current_stage', 5)
                                ->count();
                            $readyCloseCount = App\Models\Ticket::where('status', 'ready_for_closure')
                                ->where('current_stage', 8)
                                ->count();
                        @endphp
                        <a href="{{ route('tickets.index') }}?status=open"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('status') == 'open' && !request()->has('unassigned') ? 'active' : '' }}">
                            <i class="fas fa-inbox"></i>
                            <span>Pending Receive</span>
                            @if ($pendingReceiveCount > 0)
                                <span class="mobile-badge">{{ $pendingReceiveCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('tickets.index') }}?status=pending_om&unassigned=1"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('status') == 'pending_om' && request()->get('unassigned') == '1' ? 'active' : '' }}">
                            <i class="fas fa-user-slash"></i>
                            <span>Unassigned</span>
                            @if ($unassignedCount > 0)
                                <span class="mobile-badge">{{ $unassignedCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('tickets.index') }}?status=pending_vr"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('status') == 'pending_vr' ? 'active' : '' }}">
                            <i class="fas fa-tag"></i>
                            <span>Pending VR</span>
                            @if ($pendingVRCount > 0)
                                <span class="mobile-badge">{{ $pendingVRCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('tickets.index') }}?status=ready_for_closure"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('status') == 'ready_for_closure' ? 'active' : '' }}">
                            <i class="fas fa-check-circle"></i>
                            <span>Ready to Close</span>
                            @if ($readyCloseCount > 0)
                                <span class="mobile-badge">{{ $readyCloseCount }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- Pending My Approval OM -->
                    @if (auth()->user()->role === 'om')
                        @php
                            $pendingOMCount = App\Models\Ticket::where('status', 'pending_om')
                                ->where('current_stage', 3)
                                ->count();
                        @endphp
                        <a href="{{ route('tickets.index') }}?status=pending_om"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('status') == 'pending_om' ? 'active' : '' }}">
                            <i class="fas fa-hourglass-half"></i>
                            <span>Pending My Approval</span>
                            @if ($pendingOMCount > 0)
                                <span class="mobile-badge">{{ $pendingOMCount }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- Pending My Approval GM -->
                    @if (auth()->user()->role === 'gm')
                        @php
                            $pendingGMCount = App\Models\Ticket::where('status', 'pending_gm')
                                ->where('current_stage', 8)
                                ->count();
                        @endphp
                        <a href="{{ route('tickets.index') }}?status=pending_gm"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('status') == 'pending_gm' ? 'active' : '' }}">
                            <i class="fas fa-hourglass-half"></i>
                            <span>Pending My Approval</span>
                            @if ($pendingGMCount > 0)
                                <span class="mobile-badge">{{ $pendingGMCount }}</span>
                            @endif
                        </a>
                    @endif

                    <!-- Dept In Progress untuk Manager -->
                    @if (auth()->user()->role === 'manager')
                        @php
                            $deptInProgress = auth()->user()->department_id
                                ? App\Models\Ticket::where('department_id', auth()->user()->department_id)
                                    ->where('status', 'in_progress')
                                    ->count()
                                : 0;
                        @endphp
                        <a href="{{ route('tickets.index') }}?status=in_progress&department={{ auth()->user()->department_id }}"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('status') == 'in_progress' && request()->get('department') == auth()->user()->department_id ? 'active' : '' }}">
                            <i class="fas fa-spinner"></i>
                            <span>Dept In Progress</span>
                            @if ($deptInProgress > 0)
                                <span class="mobile-badge">{{ $deptInProgress }}</span>
                            @endif
                        </a>
                    @endif
                </div>
            </div>

            <!-- ========== VOUCHER REQUESTS ========== -->
            @php
                $user = auth()->user();
                $role = $user->role;
                $badgeCount = 0;
                if ($role === 'admin_eng') {
                    $badgeCount = App\Models\VoucherRequest::where('status', 'pending')->count();
                } elseif ($role === 'om') {
                    $badgeCount = App\Models\VoucherRequest::where('status', 'admin_approved')->count();
                } elseif ($role === 'gm') {
                    $badgeCount = App\Models\VoucherRequest::where('status', 'om_approved')->count();
                }
            @endphp

            @if (in_array($role, ['admin_eng', 'om', 'gm', 'superadmin']))
                <div class="mobile-menu-accordion">
                    <a href="javascript:void(0)" class="mobile-menu-accordion-header">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Voucher Requests</span>
                        <i class="fas fa-chevron-right accordion-icon"></i>
                        @if ($badgeCount > 0)
                            <span class="mobile-badge">{{ $badgeCount }}</span>
                        @endif
                    </a>
                    <div class="mobile-menu-accordion-content">
                        <a href="{{ route('vouchers.index') }}"
                            class="mobile-menu-sublink {{ request()->routeIs('vouchers.index') && !request()->get('status') ? 'active' : '' }}">
                            <i class="fas fa-list"></i>
                            <span>All VR</span>
                        </a>

                        @if (in_array($role, ['admin_eng', 'om', 'gm']))
                            @php
                                $pendingStatus =
                                    $role === 'admin_eng'
                                        ? 'pending'
                                        : ($role === 'om'
                                            ? 'admin_approved'
                                            : 'om_approved');
                            @endphp
                            <a href="{{ route('vouchers.index') }}?status={{ $pendingStatus }}"
                                class="mobile-menu-sublink {{ request()->routeIs('vouchers.index') && request()->get('status') === $pendingStatus ? 'active' : '' }}">
                                <i class="fas fa-hourglass-half"></i>
                                <span>Pending My Approval</span>
                                @if ($badgeCount > 0)
                                    <span class="mobile-badge">{{ $badgeCount }}</span>
                                @endif
                            </a>
                        @endif

                        <a href="{{ route('vouchers.index') }}?status=gm_approved"
                            class="mobile-menu-sublink {{ request()->routeIs('vouchers.index') && request()->get('status') === 'gm_approved' ? 'active' : '' }}">
                            <i class="fas fa-check-circle"></i>
                            <span>Approved</span>
                        </a>

                        <a href="{{ route('vouchers.index') }}?status=paid"
                            class="mobile-menu-sublink {{ request()->routeIs('vouchers.index') && request()->get('status') === 'paid' ? 'active' : '' }}">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Paid</span>
                        </a>

                        <a href="{{ route('vouchers.index') }}?status=rejected"
                            class="mobile-menu-sublink {{ request()->routeIs('vouchers.index') && request()->get('status') === 'rejected' ? 'active' : '' }}">
                            <i class="fas fa-times-circle"></i>
                            <span>Rejected</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Calendar -->
            <a href="{{ route('calendar.index') }}"
                class="mobile-menu-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendar</span>
            </a>

            <!-- My Department untuk Manager -->
            @if (auth()->user()->role === 'manager')
                <a href="{{ route('my-department.index') }}"
                    class="mobile-menu-link {{ request()->routeIs('my-department.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span>My Department</span>
                </a>
            @endif

            <!-- Master Data untuk SuperAdmin -->
            @if (auth()->user()->role === 'superadmin')
                <div class="mobile-menu-accordion">
                    <a href="javascript:void(0)" class="mobile-menu-accordion-header">
                        <i class="fas fa-database"></i>
                        <span>Master Data</span>
                        <i class="fas fa-chevron-right accordion-icon"></i>
                    </a>
                    <div class="mobile-menu-accordion-content">
                        <a href="{{ route('admin.departments.index') }}"
                            class="mobile-menu-sublink {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                            <i class="fas fa-building"></i>
                            <span>Departments</span>
                        </a>
                        <a href="{{ route('admin.locations.index') }}"
                            class="mobile-menu-sublink {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Locations</span>
                        </a>
                        <a href="{{ route('admin.categories.index') }}"
                            class="mobile-menu-sublink {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="fas fa-folder"></i>
                            <span>Categories</span>
                        </a>
                        <a href="{{ route('admin.priorities.index') }}"
                            class="mobile-menu-sublink {{ request()->routeIs('admin.priorities.*') ? 'active' : '' }}">
                            <i class="fas fa-flag"></i>
                            <span>Priorities</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- User Management untuk SuperAdmin -->
            @if (auth()->user()->role === 'superadmin')
                <div class="mobile-menu-accordion">
                    <a href="javascript:void(0)" class="mobile-menu-accordion-header">
                        <i class="fas fa-users-cog"></i>
                        <span>User Management</span>
                        <i class="fas fa-chevron-right accordion-icon"></i>
                        @php
                            $pendingCount = App\Models\User::where('status', 'pending')->count();
                        @endphp
                        @if ($pendingCount > 0)
                            <span class="mobile-badge">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <div class="mobile-menu-accordion-content">
                        <a href="{{ route('admin.users.index') }}"
                            class="mobile-menu-sublink {{ request()->routeIs('admin.users.index') && !request()->hasAny(['status', 'verified']) ? 'active' : '' }}">
                            <i class="fas fa-user"></i>
                            <span>All Users</span>
                        </a>
                        <a href="{{ route('admin.users.index', ['status' => 'pending']) }}"
                            class="mobile-menu-sublink {{ request()->get('status') == 'pending' ? 'active' : '' }}">
                            <i class="fas fa-hourglass-half"></i>
                            <span>Pending</span>
                            @php
                                $pendingCount = App\Models\User::where('status', 'pending')->count();
                            @endphp
                            @if ($pendingCount > 0)
                                <span class="mobile-badge">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}"
                            class="mobile-menu-sublink {{ request()->get('status') == 'inactive' ? 'active' : '' }}">
                            <i class="fas fa-ban"></i>
                            <span>Inactive</span>
                            @php
                                $inactiveCount = App\Models\User::where('status', 'inactive')->count();
                            @endphp
                            @if ($inactiveCount > 0)
                                <span class="mobile-badge">{{ $inactiveCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.users.index', ['verified' => 'unverified']) }}"
                            class="mobile-menu-sublink {{ request()->get('verified') == 'unverified' ? 'active' : '' }}">
                            <i class="fas fa-envelope"></i>
                            <span>Unverified</span>
                            @php
                                $unverifiedCount = App\Models\User::whereNull('email_verified_at')->count();
                            @endphp
                            @if ($unverifiedCount > 0)
                                <span class="mobile-badge">{{ $unverifiedCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            @endif

            <!-- Notifications -->
            <a href="{{ route('notifications.index') }}"
                class="mobile-menu-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
                @php
                    $unreadCount = App\Models\Notification::where('user_id', auth()->id())
                        ->where('is_read', false)
                        ->count();
                @endphp
                @if ($unreadCount > 0)
                    <span class="mobile-badge">{{ $unreadCount }}</span>
                @endif
            </a>

            <!-- Activity Logs -->
            @if (in_array(auth()->user()->role, ['superadmin', 'admin_eng']))
                <a href="{{ route('activity-logs.index') }}"
                    class="mobile-menu-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>Activity Logs</span>
                </a>
            @endif

            <!-- ========== ACCOUNT ========== -->
            <div class="mobile-menu-accordion">
                <a href="javascript:void(0)" class="mobile-menu-accordion-header">
                    <i class="fas fa-user-circle"></i>
                    <span>Account</span>
                    <i class="fas fa-chevron-right accordion-icon"></i>
                </a>
                <div class="mobile-menu-accordion-content">
                    <a href="{{ route('profile.index') }}"
                        class="mobile-menu-sublink {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="fas fa-id-card"></i>
                        <span>My Profile</span>
                    </a>



                    <a href="javascript:void(0)" onclick="confirmLogout()" class="mobile-menu-sublink">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>

            <!-- Copyright (simplified for mobile) -->
            <div class="mobile-menu-copyright">
                <p>Harris Hotel Ticketing © {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</div>

@include('layouts.partials.part.sidebar-script')
