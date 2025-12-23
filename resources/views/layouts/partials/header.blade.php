<!--**********************************
    Header start
***********************************-->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        @yield('page-title', 'Dashboard')
                    </div>
                </div>
                <ul class="navbar-nav header-right">
                    <!-- Search Area -->
                    <li class="nav-item">
                        <div class="input-group search-area d-xl-inline-flex d-none">
                            <input type="text" class="form-control" placeholder="Search tickets..." id="global-search"
                                aria-label="Search">
                            <span class="input-group-text" id="header-search">
                                <a href="javascript:void(0);">
                                    <i class="flaticon-381-search-2"></i>
                                </a>
                            </span>
                        </div>
                    </li>

                    <!-- Quick Actions (New Ticket) -->
                    <li class="nav-item">
                        <a class="nav-link ai-icon" href="#" data-bs-toggle="modal"
                            data-bs-target="#addTicketModal" title="New Ticket">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M14 2.33331C7.55667 2.33331 2.33334 7.55665 2.33334 14C2.33334 20.4433 7.55667 25.6666 14 25.6666C20.4433 25.6666 25.6667 20.4433 25.6667 14C25.6667 7.55665 20.4433 2.33331 14 2.33331ZM19.8333 15.1666H15.1667V19.8333H12.8333V15.1666H8.16667V12.8333H12.8333V8.16665H15.1667V12.8333H19.8333V15.1666Z"
                                    fill="#3B4CB8" />
                            </svg>
                        </a>
                    </li>

                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link ai-icon" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M12.8333 5.91732V3.49998C12.8333 2.85598 13.356 2.33331 14 2.33331C14.6428 2.33331 15.1667 2.85598 15.1667 3.49998V5.91732C16.9003 6.16698 18.5208 6.97198 19.7738 8.22498C21.3057 9.75681 22.1667 11.8346 22.1667 14V18.3913L23.1105 20.279C23.562 21.1831 23.5142 22.2565 22.9822 23.1163C22.4513 23.9761 21.5122 24.5 20.5018 24.5H15.1667C15.1667 25.144 14.6428 25.6666 14 25.6666C13.356 25.6666 12.8333 25.144 12.8333 24.5H7.49817C6.48667 24.5 5.54752 23.9761 5.01669 23.1163C4.48469 22.2565 4.43684 21.1831 4.88951 20.279L5.83333 18.3913V14C5.83333 11.8346 6.69319 9.75681 8.22502 8.22498C9.47919 6.97198 11.0985 6.16698 12.8333 5.91732ZM14 8.16664C12.4518 8.16664 10.969 8.78148 9.87469 9.87581C8.78035 10.969 8.16666 12.453 8.16666 14V18.6666C8.16666 18.8475 8.12351 19.026 8.04301 19.1881C8.04301 19.1881 7.52384 20.2265 6.9755 21.322C6.88567 21.5028 6.89501 21.7186 7.00117 21.8901C7.10734 22.0616 7.29517 22.1666 7.49817 22.1666H20.5018C20.7037 22.1666 20.8915 22.0616 20.9977 21.8901C21.1038 21.7186 21.1132 21.5028 21.0234 21.322C20.475 20.2265 19.9558 19.1881 19.9558 19.1881C19.8753 19.026 19.8333 18.8475 19.8333 18.6666V14C19.8333 12.453 19.2185 10.969 18.1242 9.87581C17.0298 8.78148 15.547 8.16664 14 8.16664Z"
                                    fill="#FE634E" />
                            </svg>
                            @php
                                $unreadCount = auth()->user()->unreadNotifications()->count();
                            @endphp
                            @if ($unreadCount > 0)
                                <span class="badge light text-white bg-danger rounded-circle">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu rounded dropdown-menu-end">
                            <div id="DZ_W_Notification1" class="widget-media dz-scroll p-3" style="height:380px;">
                                <ul class="timeline">
                                    @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                        <li>
                                            <div class="timeline-panel">
                                                <div class="media me-2">
                                                    @if ($notification->ticket && $notification->ticket->user)
                                                        <img alt="{{ $notification->ticket->user->name }}"
                                                            width="50"
                                                            src="{{ $notification->ticket->user->profile_picture_url }}"
                                                            class="rounded-circle">
                                                    @else
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                            style="width: 50px; height: 50px;">
                                                            <i class="fa fa-bell"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="media-body">
                                                    <h6 class="mb-1">{{ $notification->title }}</h6>
                                                    <small
                                                        class="d-block">{{ $notification->created_at->diffForHumans() }}</small>
                                                    @if ($notification->ticket)
                                                        <a href="#" class="badge badge-sm badge-primary mt-1">
                                                            {{ $notification->ticket->ticket_number }}
                                                        </a>
                                                    @endif
                                                </div>
                                                @if (!$notification->is_read)
                                                    <span class="badge badge-xs badge-danger">New</span>
                                                @endif
                                            </div>
                                        </li>
                                    @empty
                                        <li class="text-center py-3">
                                            <i class="fa fa-bell-slash fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">No notifications</p>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                            <a class="all-notification" href="#">
                                See all notifications <i class="ti-arrow-right"></i>
                            </a>
                        </div>
                    </li>

                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->profile_picture_url }}" width="20"
                                alt="{{ auth()->user()->name }}" class="rounded-circle">
                            <div class="header-info">
                                <span class="text-black">{{ auth()->user()->name }}</span>
                                <p class="fs-12 mb-0">{{ ucfirst(auth()->user()->role) }}</p>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="#" class="dropdown-item ai-icon">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary"
                                    width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="ms-2">Profile</span>
                            </a>
                            <a href="#" class="dropdown-item ai-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="text-info" width="18"
                                    height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span class="ms-2">Settings</span>
                            </a>
                            <a href="#" class="dropdown-item ai-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="text-success" width="18"
                                    height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                    </path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <span class="ms-2">Inbox
                                    <span class="badge badge-xs badge-danger ms-1">3</span>
                                </span>
                            </a>
                            <a href="{{ route('logout') }}" class="dropdown-item ai-icon"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger"
                                    width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span class="ms-2">Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!--**********************************
    Header end
***********************************-->
