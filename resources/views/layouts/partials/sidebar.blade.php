<!--**********************************
    Sidebar start
***********************************-->
<div class="deznav">
    <div class="deznav-scroll">
        <!-- Quick Create Ticket Button - Hanya User & Technician & Admin -->
        @if (in_array(auth()->user()->role, ['user', 'technician', 'admin']))
            <a href="{{ route('tickets.create') }}" class="add-menu-sidebar">
                <i class="flaticon-381-add"></i> New Ticket
            </a>
        @endif

        <ul class="metismenu" id="menu">
            <!-- ========================= DASHBOARD ========================= -->
            <li>
                <a class="ai-icon" href="{{ route('dashboard') }}" aria-expanded="false">
                    <i class="flaticon-381-networking"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!-- ========================= TICKETS (ALL ROLES) ========================= -->
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-notepad"></i>
                    <span class="nav-text">Tickets</span>
                </a>
                <ul aria-expanded="false">
                    <!-- All Tickets - SEMUA role bisa lihat -->
                    <li>
                        <a href="{{ route('tickets.index') }}">
                            <i class="flaticon-381-list"></i> All Tickets
                        </a>
                    </li>

                    <!-- My Tickets - Kecuali Manager/GM/OM (mereka gak bikin ticket) -->
                    @if (!in_array(auth()->user()->role, ['manager', 'gm', 'om']))
                        <li>
                            <a href="{{ route('tickets.my-tickets') }}">
                                <i class="flaticon-381-file"></i> My Tickets
                            </a>
                        </li>
                    @endif

                    <!-- Assigned to Me - Teknisi & Admin only -->
                    @if (in_array(auth()->user()->role, ['technician', 'admin']))
                        <li>
                            <a href="{{ route('tickets.assigned') }}">
                                <i class="flaticon-381-user-7"></i> Assigned to Me
                                @php
                                    $assignedCount = App\Models\Ticket::where('assigned_to', auth()->id())
                                        ->whereIn('status', ['open', 'in_progress', 'pending'])
                                        ->count();
                                @endphp
                                @if ($assignedCount > 0)
                                    <span class="badge badge-info badge-sm">{{ $assignedCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <!-- Unassigned - Admin only -->
                    @if (auth()->user()->role === 'admin')
                        <li>
                            <a href="{{ route('tickets.unassigned') }}">
                                <i class="flaticon-381-folder-1"></i> Unassigned
                                @php
                                    $unassignedCount = App\Models\Ticket::whereNull('assigned_to')
                                        ->where('approval_status', 'approved')
                                        ->whereNotIn('status', ['closed', 'cancelled'])
                                        ->count();
                                @endphp
                                @if ($unassignedCount > 0)
                                    <span class="badge badge-warning badge-sm">{{ $unassignedCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

            <!-- ========================= APPROVAL CENTER (MANAGER, GM, OM, ADMIN) ========================= -->
            @if (in_array(auth()->user()->role, ['manager', 'gm', 'om', 'admin']))
                <li>
                    <a class="ai-icon" href="#" aria-expanded="false">
                        <i class="flaticon-381-box"></i>
                        <span class="nav-text">Approval Center</span>
                        @php
                            $pendingApprovalCount = App\Models\Ticket::where(
                                'approval_status',
                                'pending_approval',
                            )->count();
                        @endphp
                        @if ($pendingApprovalCount > 0)
                            <span class="badge badge-danger badge-sm">{{ $pendingApprovalCount }}</span>
                        @endif
                    </a>
                </li>
            @endif

            <!-- ========================= CALENDAR (ALL) ========================= -->
            <li>
                <a class="ai-icon" href="#" aria-expanded="false">
                    <i class="flaticon-381-calendar-1"></i>
                    <span class="nav-text">Calendar</span>
                </a>
            </li>

            <!-- ========================= VOUCHER REQUESTS (ADMIN ONLY) ========================= -->
            @if (auth()->user()->role === 'admin')
                <li>
                    <a class="ai-icon" href="#" aria-expanded="false">
                        <i class="flaticon-381-price-tag"></i>
                        <span class="nav-text">Voucher Requests</span>
                        @php
                            // Nanti ganti dengan real query setelah tabel voucher_requests dibuat
                            $pendingVRCount = 0;
                        @endphp
                        @if ($pendingVRCount > 0)
                            <span class="badge badge-warning badge-sm">{{ $pendingVRCount }}</span>
                        @endif
                    </a>
                </li>
            @endif

            <!-- ========================= MASTER DATA (ADMIN ONLY) ========================= -->
            @if (auth()->user()->role === 'admin')
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

            <!-- ========================= USER MANAGEMENT (ADMIN ONLY) ========================= -->
            @if (auth()->user()->role === 'admin')
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
                            <a href="{{ route('admin.users.index') }}?role=technician&status=pending">
                                <i class="flaticon-381-clock"></i> Pending Technicians
                                @php
                                    $pendingTechCount = App\Models\User::where('role', 'technician')
                                        ->where('status', 'pending')
                                        ->count();
                                @endphp
                                @if ($pendingTechCount > 0)
                                    <span class="badge badge-warning badge-sm">{{ $pendingTechCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}?role[]=admin&role[]=manager&role[]=gm&role[]=om">
                                <i class="flaticon-381-settings-6"></i> Admins & Managers
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}?status=inactive">
                                <i class="flaticon-381-trash-1"></i> Inactive Accounts
                                @php
                                    $inactiveCount = App\Models\User::where('status', 'inactive')->count();
                                @endphp
                                @if ($inactiveCount > 0)
                                    <span class="badge badge-danger badge-sm">{{ $inactiveCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <!-- ========================= REPORTS ========================= -->
            <li>
                <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-diploma"></i>
                    <span class="nav-text">Reports</span>
                </a>
                <ul aria-expanded="false">
                    <!-- Admin Reports -->
                    @if (auth()->user()->role === 'admin')
                        <li><a href="#"><i class="flaticon-381-controls-3"></i> Ticket Reports</a></li>
                        <li><a href="#"><i class="flaticon-381-television"></i> User Activity</a></li>
                        <li><a href="#"><i class="flaticon-381-line-chart"></i> SLA Report</a></li>
                        <li><a href="#"><i class="flaticon-381-controls-1"></i> Department Performance</a></li>
                    @endif

                    <!-- Manager/GM/OM Reports -->
                    @if (in_array(auth()->user()->role, ['manager', 'gm', 'om']))
                        <li><a href="#"><i class="flaticon-381-controls-1"></i> Department Report</a></li>
                        <li><a href="#"><i class="flaticon-381-line-chart"></i> Performance Report</a></li>
                    @endif

                    <!-- Technician Reports -->
                    @if (auth()->user()->role === 'technician')
                        <li><a href="#"><i class="flaticon-381-controls"></i> My Performance</a></li>
                        <li><a href="#"><i class="flaticon-381-file-1"></i> Completed Tickets</a></li>
                    @endif

                    <!-- User Reports -->
                    @if (auth()->user()->role === 'user')
                        <li><a href="#"><i class="flaticon-381-file-1"></i> My Ticket History</a></li>
                    @endif
                </ul>
            </li>

            <!-- ========================= NOTIFICATIONS (ALL) ========================= -->
            <li>
                <a class="ai-icon" href="#" aria-expanded="false">
                    <i class="flaticon-381-ring"></i>
                    <span class="nav-text">Notifications</span>
                    @php
                        $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="badge badge-danger badge-sm">{{ $unreadCount }}</span>
                    @endif
                </a>
            </li>

            <!-- ========================= ACTIVITY LOGS (ADMIN ONLY) ========================= -->
            @if (auth()->user()->role === 'admin')
                <li>
                    <a class="ai-icon" href="#" aria-expanded="false">
                        <i class="flaticon-381-notebook"></i>
                        <span class="nav-text">Activity Logs</span>
                    </a>
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
