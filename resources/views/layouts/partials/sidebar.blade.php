@include('layouts.partials.part.sidebar-style')

<!--**********************************
    Desktop Sidebar
***********************************-->
<div class="deznav desktop-sidebar">
    <div class="deznav-scroll">
        <!-- Quick Create Ticket Button - FIXED -->
        @if (in_array(auth()->user()->role, ['user', 'manager', 'admin_eng']))
            <a href="{{ route('tickets.create') }}" class="add-menu-sidebar ai-icon">
                <i class="fas fa-plus-circle"></i>
                <span class="nav-text">New MR</span>
            </a>
        @endif

        <ul class="metismenu" id="menu">
            <!-- ========================= DASHBOARD ========================= -->
            <li>
                <a class="ai-icon" href="{{ route('dashboard') }}" aria-expanded="false">
                    <i class="fas fa-chart-pie"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!-- ========================= MAINTENANCE REQUESTS ========================= -->
            @php
                $userRole = auth()->user()->role;

                // Badge counts
                $pendingReceiveCount = \App\Models\Ticket::where('status', 'open')->where('current_stage', 1)->count();
                $unassignedCount = \App\Models\Ticket::where('status', 'in_progress')
                    ->whereNull('assigned_to')
                    ->count();
                $pendingPrCount = \App\Models\Ticket::where('status', 'pending_vr')->count();
                $readyCloseCount = \App\Models\Ticket::where('status', 'ready_for_closure')->count();
                $pendingOmCount = \App\Models\Ticket::where('status', 'pending_om')->count();
                $pendingGmCount = \App\Models\Ticket::where('status', 'pending_gm')->count();
                $assignedToMeCount = \App\Models\Ticket::where('assigned_to', auth()->id())
                    ->whereIn('status', ['in_progress', 'pending_vr'])
                    ->count();
                $myTicketsCount = \App\Models\Ticket::where('user_id', auth()->id())->count();
                $departmentTicketsCount = auth()->user()->department_id
                    ? \App\Models\Ticket::where('department_id', auth()->user()->department_id)
                        ->whereNotIn('status', ['closed', 'cancelled'])
                        ->count()
                    : 0;
            @endphp

            <li class="{{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-clipboard-list"></i>
                    <span class="nav-text">Maintenance Requests</span>
                </a>
                <ul aria-expanded="false" class="{{ request()->routeIs('tickets.*') ? 'show' : '' }}">
                    <!-- All MR -->
                    <li
                        class="{{ request()->routeIs('tickets.index') && !request()->anyFilled(['my_tickets', 'assigned', 'department_filter', 'status', 'stage', 'unassigned']) ? 'active' : '' }}">
                        <a href="{{ route('tickets.index') }}">
                            <i class="fas fa-list"></i> All MR
                        </a>
                    </li>

                    <!-- My MR -->
                    @if (in_array($userRole, ['user', 'manager', 'admin_eng']))
                        <li class="{{ request()->get('my_tickets') == '1' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?my_tickets=1">
                                <i class="fas fa-user"></i> My MR
                            </a>
                        </li>
                    @endif

                    <!-- Assigned to Me (Technician only) -->
                    @if ($userRole === 'technician')
                        <li class="{{ request()->get('assigned') == '1' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?assigned=1">
                                <i class="fas fa-user-check"></i> Assigned to Me
                                @if ($assignedToMeCount > 0)
                                    <span class="sidebar-badge"
                                        style="background: #ff6b35;">{{ $assignedToMeCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <!-- Department MR -->
                    @if (($userRole === 'user' && auth()->user()->department_id) || $userRole === 'manager')
                        <li class="{{ request()->get('department_filter') == '1' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?department_filter=1">
                                <i class="fas fa-users"></i> Department MR
                                @if ($departmentTicketsCount > 0)
                                    <span class="sidebar-badge"
                                        style="background: #ff6b35;">{{ $departmentTicketsCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <!-- Admin Engineering Menus -->
                    @if ($userRole === 'admin_eng')
                        <!-- Pending Receive -->
                        <li class="{{ request()->get('status') == 'open' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?status=open">
                                <i class="fas fa-inbox"></i> Pending Receive
                                @if ($pendingReceiveCount > 0)
                                    <span class="sidebar-badge"
                                        style="background: #dc3545;">{{ $pendingReceiveCount }}</span>
                                @endif
                            </a>
                        </li>

                        <!-- Unassigned (in_progress + assigned_to null) -->
                        <li class="{{ request()->get('unassigned') == '1' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?unassigned=1">
                                <i class="fas fa-user-plus"></i> Unassigned
                                @if ($unassignedCount > 0)
                                    <span class="sidebar-badge"
                                        style="background: #dc3545;">{{ $unassignedCount }}</span>
                                @endif
                            </a>
                        </li>

                        <!-- Pending PR -->
                        <li class="{{ request()->get('status') == 'pending_vr' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?status=pending_vr">
                                <i class="fas fa-file-invoice-dollar"></i> Pending PR
                                @if ($pendingPrCount > 0)
                                    <span class="sidebar-badge"
                                        style="background: #dc3545;">{{ $pendingPrCount }}</span>
                                @endif
                            </a>
                        </li>

                        <!-- Ready for Closure -->
                        <li class="{{ request()->get('status') == 'ready_for_closure' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?status=ready_for_closure">
                                <i class="fas fa-check-double"></i> Ready for Closure
                                @if ($readyCloseCount > 0)
                                    <span class="sidebar-badge"
                                        style="background: #28a745;">{{ $readyCloseCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <!-- OM Menu -->
                    @if ($userRole === 'om')
                        <li class="{{ request()->get('status') == 'pending_om' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?status=pending_om">
                                <i class="fas fa-clock"></i> Pending OM Approval
                                @if ($pendingOmCount > 0)
                                    <span class="sidebar-badge"
                                        style="background: #dc3545;">{{ $pendingOmCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <!-- GM Menu -->
                    @if ($userRole === 'gm')
                        <li class="{{ request()->get('status') == 'pending_gm' ? 'active' : '' }}">
                            <a href="{{ route('tickets.index') }}?status=pending_gm">
                                <i class="fas fa-clock"></i> Pending GM Approval
                                @if ($pendingGmCount > 0)
                                    <span class="sidebar-badge"
                                        style="background: #dc3545;">{{ $pendingGmCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

            <!-- ========================= PURCHASE REQUESTS ========================= -->
            @if (in_array($userRole, ['admin_eng', 'om', 'gm', 'superadmin']))
                @php
                    $pendingAdminPrCount = \App\Models\VoucherRequest::where('status', 'pending')->count();
                    $pendingOmPrCount = \App\Models\VoucherRequest::where('status', 'admin_approved')->count();
                    $pendingGmPrCount = \App\Models\VoucherRequest::where('status', 'om_approved')->count();
                @endphp

                <li class="{{ request()->routeIs('voucher-requests.*') ? 'active' : '' }}">
                    <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span class="nav-text">Purchase Requests</span>
                    </a>
                    <ul aria-expanded="false" class="{{ request()->routeIs('voucher-requests.*') ? 'show' : '' }}">
                        <li>
                            <a href="{{ route('voucher-requests.index') }}">
                                <i class="fas fa-list"></i> All PR
                            </a>
                        </li>
                        @if ($userRole === 'admin_eng')
                            <li class="{{ request()->get('status') == 'pending' ? 'active' : '' }}">
                                <a href="{{ route('voucher-requests.index') }}?status=pending">
                                    <i class="fas fa-hourglass-half"></i> Pending Admin
                                    @if ($pendingAdminPrCount > 0)
                                        <span class="sidebar-badge"
                                            style="background: #dc3545;">{{ $pendingAdminPrCount }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="{{ request()->get('status') == 'paid' ? 'active' : '' }}">
                                <a href="{{ route('voucher-requests.index') }}?status=paid">
                                    <i class="fas fa-check-circle"></i> Paid
                                </a>
                            </li>
                        @endif
                        @if ($userRole === 'om')
                            <li class="{{ request()->get('status') == 'admin_approved' ? 'active' : '' }}">
                                <a href="{{ route('voucher-requests.index') }}?status=admin_approved">
                                    <i class="fas fa-clock"></i> Pending OM Approval
                                    @if ($pendingOmPrCount > 0)
                                        <span class="sidebar-badge"
                                            style="background: #dc3545;">{{ $pendingOmPrCount }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        @if ($userRole === 'gm')
                            <li class="{{ request()->get('status') == 'om_approved' ? 'active' : '' }}">
                                <a href="{{ route('voucher-requests.index') }}?status=om_approved">
                                    <i class="fas fa-clock"></i> Pending GM Approval
                                    @if ($pendingGmPrCount > 0)
                                        <span class="sidebar-badge"
                                            style="background: #dc3545;">{{ $pendingGmPrCount }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            <!-- ========================= CALENDAR ========================= -->
            <li class="{{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <a class="ai-icon" href="{{ route('calendar.index') }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="nav-text">Calendar</span>
                </a>
            </li>

            <!-- ========================= MY DEPARTMENT ========================= -->
            @if ($userRole === 'manager')
                <li class="{{ request()->routeIs('my-department.*') ? 'active' : '' }}">
                    <a class="ai-icon" href="{{ route('my-department.index') }}">
                        <i class="fas fa-building"></i>
                        <span class="nav-text">My Department</span>
                    </a>
                </li>
            @endif

            <!-- ========================= MASTER DATA ========================= -->
            @if ($userRole === 'superadmin')
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

            <!-- ========================= TECHNICIAN PERFORMANCE ========================= -->
            @php
                $showTechPerformance = false;
                $techPerformanceRoute = route('technician-performance.index');

                if (in_array($userRole, ['superadmin', 'admin_eng', 'om', 'gm'])) {
                    $showTechPerformance = true;
                } elseif ($userRole === 'manager') {
                    $dept = \App\Models\Department::find(auth()->user()->department_id);
                    if ($dept && $dept->has_manager_access) {
                        $showTechPerformance = true;
                    }
                } elseif ($userRole === 'technician') {
                    $showTechPerformance = true;
                    $techPerformanceRoute = route('technician-performance.show', auth()->id());
                }
            @endphp

            @if ($showTechPerformance)
                <li class="{{ request()->routeIs('technician-performance.*') ? 'active' : '' }}">
                    <a class="ai-icon" href="{{ $techPerformanceRoute }}">
                        <i class="fas fa-chart-line"></i>
                        <span class="nav-text">Tech Performance</span>
                    </a>
                </li>
            @endif

            <!-- ========================= USER MANAGEMENT ========================= -->
            @if ($userRole === 'superadmin')
                @php
                    $pendingUsersCount = \App\Models\User::where('status', 'pending')->count();
                @endphp
                <li class="{{ request()->routeIs('admin.users.*') ? 'mm-active' : '' }}">
                    <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="fas fa-users-cog"></i>
                        <span class="nav-text">User Management</span>
                    </a>
                    <ul aria-expanded="false"
                        class="{{ request()->routeIs('admin.users.*') ? 'mm-collapse mm-show' : 'mm-collapse' }}">
                        <li
                            class="{{ request()->routeIs('admin.users.index') && !request()->hasAny(['status', 'verified']) ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.users.index') }}"><i class="fas fa-user"></i> All Users</a>
                        </li>
                        <li class="{{ request()->get('status') == 'pending' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.users.index', ['status' => 'pending']) }}">
                                <i class="fas fa-hourglass-half"></i> Pending
                                @if ($pendingUsersCount > 0)
                                    <span class="sidebar-badge"
                                        style="background: #dc3545;">{{ $pendingUsersCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="{{ request()->get('status') == 'inactive' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}">
                                <i class="fas fa-ban"></i> Inactive
                            </a>
                        </li>
                        <li class="{{ request()->get('verified') == 'unverified' ? 'mm-active' : '' }}">
                            <a href="{{ route('admin.users.index', ['verified' => 'unverified']) }}">
                                <i class="fas fa-envelope"></i> Unverified
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <!-- ========================= NOTIFICATIONS ========================= -->
            @php
                $unreadNotificationsCount = \App\Models\Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->count();
            @endphp
            <li class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <a class="ai-icon" href="{{ route('notifications.index') }}">
                    <i class="fas fa-bell"></i>
                    <span class="nav-text">Notifications</span>
                    @if ($unreadNotificationsCount > 0)
                        <span class="sidebar-badge"
                            style="background: #dc3545;">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a>
            </li>

            <!-- ========================= ACTIVITY LOGS ========================= -->
            @if (in_array($userRole, ['superadmin']))
                <li class="{{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                    <a class="ai-icon" href="{{ route('activity-logs.index') }}">
                        <i class="fas fa-history"></i>
                        <span class="nav-text">Activity Logs</span>
                    </a>
                </li>
            @endif

            <!-- ========================= SETTINGS ========================= -->
            <li class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <a class="ai-icon" href="{{ route('settings.index') }}">
                    <i class="fas fa-cog"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </li>

            <!-- ========================= ACCOUNT ========================= -->
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
        <a href="{{ route('dashboard') }}"
            class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('tickets.index') }}"
            class="mobile-nav-item {{ request()->routeIs('tickets.*') && !request()->routeIs('tickets.create') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span>MR</span>
        </a>

        @if (in_array($userRole, ['user', 'manager', 'admin_eng']))
            <a href="{{ route('tickets.create') }}" class="mobile-nav-item mobile-fab">
                <div class="fab-button" style="background: #1a2b4c;">
                    <i class="fas fa-plus"></i>
                </div>
            </a>
        @else
            <a href="{{ route('calendar.index') }}" class="mobile-nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendar</span>
            </a>
        @endif

        <a href="{{ route('notifications.index') }}"
            class="mobile-nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <i class="fas fa-bell"></i>
            <span>Notifikasi</span>
            @if ($unreadNotificationsCount > 0)
                <span
                    class="mobile-badge">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
            @endif
        </a>

        <a href="#" class="mobile-nav-item" id="mobileMenuTrigger">
            <i class="fas fa-bars"></i>
            <span>Menu</span>
        </a>
    </div>
</nav>

<!-- Mobile Menu Modal -->
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

            <!-- Quick Create MR -->
            @if (in_array($userRole, ['user', 'manager', 'admin_eng']))
                <a href="{{ route('tickets.create') }}" class="mobile-menu-link">
                    <i class="fas fa-plus-circle"></i>
                    <span>New MR</span>
                </a>
            @endif

            <!-- ========== MAINTENANCE REQUESTS ACCORDION ========== -->
            <div class="mobile-menu-accordion">
                <a href="javascript:void(0)" class="mobile-menu-accordion-header">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Maintenance Requests</span>
                    <i class="fas fa-chevron-right accordion-icon"></i>
                </a>
                <div class="mobile-menu-accordion-content">
                    <a href="{{ route('tickets.index') }}"
                        class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && !request()->anyFilled(['my_tickets', 'assigned', 'department_filter', 'status', 'stage', 'unassigned']) ? 'active' : '' }}">
                        <i class="fas fa-list"></i>
                        <span>All MR</span>
                    </a>

                    @if (in_array($userRole, ['user', 'manager', 'admin_eng']))
                        <a href="{{ route('tickets.index') }}?my_tickets=1"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('my_tickets') == '1' ? 'active' : '' }}">
                            <i class="fas fa-user"></i>
                            <span>My MR</span>
                        </a>
                    @endif

                    @if ($userRole === 'technician')
                        <a href="{{ route('tickets.index') }}?assigned=1"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('assigned') == '1' ? 'active' : '' }}">
                            <i class="fas fa-user-check"></i>
                            <span>Assigned to Me</span>
                            @if ($assignedToMeCount > 0)
                                <span
                                    class="mobile-badge">{{ $assignedToMeCount > 9 ? '9+' : $assignedToMeCount }}</span>
                            @endif
                        </a>
                    @endif

                    @if (($userRole === 'user' && auth()->user()->department_id) || $userRole === 'manager')
                        <a href="{{ route('tickets.index') }}?department_filter=1"
                            class="mobile-menu-sublink {{ request()->routeIs('tickets.index') && request()->get('department_filter') == '1' ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>Department MR</span>
                            @if ($departmentTicketsCount > 0)
                                <span
                                    class="mobile-badge">{{ $departmentTicketsCount > 9 ? '9+' : $departmentTicketsCount }}</span>
                            @endif
                        </a>
                    @endif

                    @if ($userRole === 'admin_eng')
                        <a href="{{ route('tickets.index') }}?status=open"
                            class="mobile-menu-sublink {{ request()->get('status') == 'open' ? 'active' : '' }}">
                            <i class="fas fa-inbox"></i>
                            <span>Pending Receive</span>
                            @if ($pendingReceiveCount > 0)
                                <span
                                    class="mobile-badge">{{ $pendingReceiveCount > 9 ? '9+' : $pendingReceiveCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('tickets.index') }}?unassigned=1"
                            class="mobile-menu-sublink {{ request()->get('unassigned') == '1' ? 'active' : '' }}">
                            <i class="fas fa-user-plus"></i>
                            <span>Unassigned</span>
                            @if ($unassignedCount > 0)
                                <span
                                    class="mobile-badge">{{ $unassignedCount > 9 ? '9+' : $unassignedCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('tickets.index') }}?status=pending_vr"
                            class="mobile-menu-sublink {{ request()->get('status') == 'pending_vr' ? 'active' : '' }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Pending PR</span>
                            @if ($pendingPrCount > 0)
                                <span class="mobile-badge">{{ $pendingPrCount > 9 ? '9+' : $pendingPrCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('tickets.index') }}?status=ready_for_closure"
                            class="mobile-menu-sublink {{ request()->get('status') == 'ready_for_closure' ? 'active' : '' }}">
                            <i class="fas fa-check-double"></i>
                            <span>Ready for Closure</span>
                            @if ($readyCloseCount > 0)
                                <span
                                    class="mobile-badge">{{ $readyCloseCount > 9 ? '9+' : $readyCloseCount }}</span>
                            @endif
                        </a>
                    @endif

                    @if ($userRole === 'om')
                        <a href="{{ route('tickets.index') }}?status=pending_om"
                            class="mobile-menu-sublink {{ request()->get('status') == 'pending_om' ? 'active' : '' }}">
                            <i class="fas fa-clock"></i>
                            <span>Pending OM Approval</span>
                            @if ($pendingOmCount > 0)
                                <span class="mobile-badge">{{ $pendingOmCount > 9 ? '9+' : $pendingOmCount }}</span>
                            @endif
                        </a>
                    @endif

                    @if ($userRole === 'gm')
                        <a href="{{ route('tickets.index') }}?status=pending_gm"
                            class="mobile-menu-sublink {{ request()->get('status') == 'pending_gm' ? 'active' : '' }}">
                            <i class="fas fa-clock"></i>
                            <span>Pending GM Approval</span>
                            @if ($pendingGmCount > 0)
                                <span class="mobile-badge">{{ $pendingGmCount > 9 ? '9+' : $pendingGmCount }}</span>
                            @endif
                        </a>
                    @endif
                </div>
            </div>

            <!-- ========== PURCHASE REQUESTS ACCORDION ========== -->
            @if (in_array($userRole, ['admin_eng', 'om', 'gm', 'superadmin']))
                <div class="mobile-menu-accordion">
                    <a href="javascript:void(0)" class="mobile-menu-accordion-header">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Purchase Requests</span>
                        <i class="fas fa-chevron-right accordion-icon"></i>
                    </a>
                    <div class="mobile-menu-accordion-content">
                        <a href="{{ route('voucher-requests.index') }}"
                            class="mobile-menu-sublink {{ request()->routeIs('voucher-requests.index') && !request()->has('status') ? 'active' : '' }}">
                            <i class="fas fa-list"></i>
                            <span>All PR</span>
                        </a>
                        @if ($userRole === 'admin_eng')
                            <a href="{{ route('voucher-requests.index') }}?status=pending"
                                class="mobile-menu-sublink {{ request()->get('status') == 'pending' ? 'active' : '' }}">
                                <i class="fas fa-hourglass-half"></i>
                                <span>Pending Admin</span>
                                @if ($pendingAdminPrCount > 0)
                                    <span
                                        class="mobile-badge">{{ $pendingAdminPrCount > 9 ? '9+' : $pendingAdminPrCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('voucher-requests.index') }}?status=paid"
                                class="mobile-menu-sublink {{ request()->get('status') == 'paid' ? 'active' : '' }}">
                                <i class="fas fa-check-circle"></i>
                                <span>Paid</span>
                            </a>
                        @endif
                        @if ($userRole === 'om')
                            <a href="{{ route('voucher-requests.index') }}?status=admin_approved"
                                class="mobile-menu-sublink {{ request()->get('status') == 'admin_approved' ? 'active' : '' }}">
                                <i class="fas fa-clock"></i>
                                <span>Pending OM Approval</span>
                                @if ($pendingOmPrCount > 0)
                                    <span
                                        class="mobile-badge">{{ $pendingOmPrCount > 9 ? '9+' : $pendingOmPrCount }}</span>
                                @endif
                            </a>
                        @endif
                        @if ($userRole === 'gm')
                            <a href="{{ route('voucher-requests.index') }}?status=om_approved"
                                class="mobile-menu-sublink {{ request()->get('status') == 'om_approved' ? 'active' : '' }}">
                                <i class="fas fa-clock"></i>
                                <span>Pending GM Approval</span>
                                @if ($pendingGmPrCount > 0)
                                    <span
                                        class="mobile-badge">{{ $pendingGmPrCount > 9 ? '9+' : $pendingGmPrCount }}</span>
                                @endif
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Calendar -->
            <a href="{{ route('calendar.index') }}"
                class="mobile-menu-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendar</span>
            </a>

            <!-- My Department -->
            @if ($userRole === 'manager')
                <a href="{{ route('my-department.index') }}"
                    class="mobile-menu-link {{ request()->routeIs('my-department.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    <span>My Department</span>
                </a>
            @endif

            <!-- Master Data -->
            @if ($userRole === 'superadmin')
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

            <!-- Tech Performance -->
            @if ($showTechPerformance)
                <a href="{{ $techPerformanceRoute }}"
                    class="mobile-menu-link {{ request()->routeIs('technician-performance.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Tech Performance</span>
                </a>
            @endif

            <!-- User Management -->
            @if ($userRole === 'superadmin')
                <div class="mobile-menu-accordion">
                    <a href="javascript:void(0)" class="mobile-menu-accordion-header">
                        <i class="fas fa-users-cog"></i>
                        <span>User Management</span>
                        <i class="fas fa-chevron-right accordion-icon"></i>
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
                            @if ($pendingUsersCount > 0)
                                <span
                                    class="mobile-badge">{{ $pendingUsersCount > 9 ? '9+' : $pendingUsersCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.users.index', ['status' => 'inactive']) }}"
                            class="mobile-menu-sublink {{ request()->get('status') == 'inactive' ? 'active' : '' }}">
                            <i class="fas fa-ban"></i>
                            <span>Inactive</span>
                        </a>
                        <a href="{{ route('admin.users.index', ['verified' => 'unverified']) }}"
                            class="mobile-menu-sublink {{ request()->get('verified') == 'unverified' ? 'active' : '' }}">
                            <i class="fas fa-envelope"></i>
                            <span>Unverified</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Notifications -->
            <a href="{{ route('notifications.index') }}"
                class="mobile-menu-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
                @if ($unreadNotificationsCount > 0)
                    <span
                        class="mobile-badge">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                @endif
            </a>

            <!-- Activity Logs -->
            @if (in_array($userRole, ['superadmin']))
                <a href="{{ route('activity-logs.index') }}"
                    class="mobile-menu-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i>
                    <span>Activity Logs</span>
                </a>
            @endif

            <!-- Settings -->
            <a href="{{ route('settings.index') }}"
                class="mobile-menu-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>

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

            <!-- Copyright -->
            <div class="mobile-menu-copyright">
                <p>Harris Hotel Ticketing © {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</div>

@include('layouts.partials.part.sidebar-script')
