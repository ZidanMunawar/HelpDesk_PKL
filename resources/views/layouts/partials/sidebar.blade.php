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
                        <li><a href="{{ route('dashboard') }}">
                                <i class="flaticon-381-internet"></i> Dashboard
                            </a></li>
                    </ul>
                </li>
            @endif


            <!-- All Tickets - Untuk User & Admin -->
            <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                    <i class="flaticon-381-notepad"></i>
                    <span class="nav-text">All Tickets</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="#">
                            <i class="flaticon-381-list"></i> All Tickets
                        </a></li>
                    <li><a href="#">
                            <i class="flaticon-381-file"></i> My Tickets
                        </a></li>
                    @if (auth()->user()->role === 'admin')
                        <li><a href="#">
                                <i class="flaticon-381-user-7"></i> Assigned to Me
                            </a></li>
                        <li><a href="#">
                                <i class="flaticon-381-folder-1"></i> Unassigned
                            </a></li>
                    @endif
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
                        <i class="flaticon-381-database-2"></i>
                        <span class="nav-text">Master Data</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('admin.locations.index') }}">
                                <i class="flaticon-381-location-1"></i> Locations
                            </a></li>
                        <li><a href="{{ route('admin.categories.index') }}">
                                <i class="flaticon-381-folder"></i> Categories
                            </a></li>
                        <li><a href="{{ route('admin.priorities.index') }}">
                                <i class="flaticon-381-flag"></i> Priorities
                            </a></li>
                        <li><a href="{{ route('admin.users.index') }}">
                                <i class="flaticon-381-user-9"></i> User Management
                            </a></li>
                    </ul>
                </li>


                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-diploma"></i>
                        <span class="nav-text">Reports</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="#">
                                <i class="flaticon-381-controls-3"></i> Ticket Reports
                            </a></li>
                        <li><a href="#">
                                <i class="flaticon-381-television"></i> User Activity
                            </a></li>
                        <li><a href="#">
                                <i class="flaticon-381-controls-1"></i> Performance Report
                            </a></li>
                    </ul>
                </li>
            @endif


            <!-- Reports untuk User -->
            @if (auth()->user()->role === 'user')
                <li><a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                        <i class="flaticon-381-diploma"></i>
                        <span class="nav-text">My Reports</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="#">
                                <i class="flaticon-381-file-1"></i> My Ticket History
                            </a></li>
                        <li><a href="#">
                                <i class="flaticon-381-controls"></i> My Statistics
                            </a></li>
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
                    <li><a href="{{ route('profile.index') }}">
                            <i class="flaticon-381-user-3"></i> My Profile
                        </a></li>
                    <li><a href="#">
                            <i class="flaticon-381-settings-2"></i> Settings
                        </a></li>
                    <li><a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="flaticon-381-exit"></i> Logout
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
