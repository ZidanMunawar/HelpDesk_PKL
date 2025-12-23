<!--**********************************
    Sidebar start
***********************************-->
<div class="deznav">
    <div class="deznav-scroll">
        <a href="#" class="add-menu-sidebar" data-bs-toggle="modal" data-bs-target="#addOrderModalside">
            <i class="flaticon-381-add"></i> New Ticket
        </a>
        <ul class="metismenu" id="menu">
            <!-- Dashboard - Untuk Admin -->
            @if (auth()->user()->role === 'admin')
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-networking"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    </ul>
                </li>
            @endif

            <!-- All Tickets - Untuk User & Admin -->
            <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-list"></i>
                    <span class="nav-text">All Tickets</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">All Tickets</a></li>
                    <li><a href="#">My Tickets</a></li>
                    @if (auth()->user()->role === 'admin')
                        <li><a href="#">Assigned to Me</a></li>
                        <li><a href="#">Unassigned</a></li>
                    @endif
                </ul>
            </li>

            <!-- Support Center -->
            <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-help"></i>
                    <span class="nav-text">Support Center</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">Knowledge Base</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </li>

            <!-- Calendar -->
            <li><a class="ai-icon" href="#" aria-expanded="false">
                    <i class="flaticon-381-calendar-1"></i>
                    <span class="nav-text">Calendar</span>
                </a>
            </li>

            <!-- Admin Menu - Hanya untuk Admin -->
            @if (auth()->user()->role === 'admin')
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-settings-2"></i>
                        <span class="nav-text">Master Data</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="#">Categories</a></li>
                        <li><a href="#">Priorities</a></li>
                        <li><a href="{{ route('admin.users.index') }}">User Management</a></li>
                        <li><a href="{{ route('admin.locations.index') }}">Location Management</a></li>
                    </ul>
                </li>


                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-notepad"></i>
                        <span class="nav-text">Reports</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="#">Ticket Reports</a></li>
                        <li><a href="#">User Activity</a></li>
                        <li><a href="#">Performance Report</a></li>
                    </ul>
                </li>

                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-file"></i>
                        <span class="nav-text">Activity Logs</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="#">All Activities</a></li>
                        <li><a href="#">Ticket Activities</a></li>
                        <li><a href="#">User Activities</a></li>
                    </ul>
                </li>
            @endif

            <!-- Reports untuk User -->
            @if (auth()->user()->role === 'user')
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-notepad"></i>
                        <span class="nav-text">My Reports</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="#">My Ticket History</a></li>
                        <li><a href="#">My Statistics</a></li>
                    </ul>
                </li>
            @endif

            <!-- Notifications -->
            <li><a class="ai-icon" href="#" aria-expanded="false">
                    <i class="flaticon-381-ring"></i>
                    <span class="nav-text">Notifications</span>
                    @php
                        $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
                    @endphp
                    @if ($unreadCount > 0)
                        <span class="badge badge-danger">{{ $unreadCount }}</span>
                    @endif
                </a>
            </li>

            <!-- Release Notes -->
            <li><a class="ai-icon" href="#" aria-expanded="false">
                    <i class="flaticon-381-notebook-1"></i>
                    <span class="nav-text">Release Notes</span>
                </a>
            </li>

            <!-- Help Center -->
            <li><a class="ai-icon" href="#" aria-expanded="false">
                    <i class="flaticon-381-help-1"></i>
                    <span class="nav-text">Help Center</span>
                </a>
            </li>

            <!-- Account -->
            <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-user-7"></i>
                    <span class="nav-text">Account</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">My Profile</a></li>
                    <li><a href="#">Settings</a></li>
                    <li><a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a></li>
                </ul>
            </li>
        </ul>

        <div class="copyright">
            <p><strong>VHP Helpdesk Ticketing</strong> © {{ date('Y') }} All Rights Reserved</p>
            <p class="fs-12">Made with <span class="heart"></span> by Your Team</p>
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
