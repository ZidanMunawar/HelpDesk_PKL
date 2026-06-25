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
                    <!-- Quick Actions (New Ticket) -->
                    <li class="nav-item">
                        @if (in_array(auth()->user()->role, ['user', 'manager', 'admin_eng']))
                            <a class="nav-link ai-icon" href="{{ route('tickets.create') }}" title="New Ticket">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M14 2.33331C7.55667 2.33331 2.33334 7.55665 2.33334 14C2.33334 20.4433 7.55667 25.6666 14 25.6666C20.4433 25.6666 25.6667 20.4433 25.6667 14C25.6667 7.55665 20.4433 2.33331 14 2.33331ZM19.8333 15.1666H15.1667V19.8333H12.8333V15.1666H8.16667V12.8333H12.8333V8.16665H15.1667V12.8333H19.8333V15.1666Z"
                                        fill="#FF7B00" />
                                </svg>
                            </a>
                        @endif
                    </li>

                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="nav-link ai-icon" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                            <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M12.8333 5.91732V3.49998C12.8333 2.85598 13.356 2.33331 14 2.33331C14.6428 2.33331 15.1667 2.85598 15.1667 3.49998V5.91732C16.9003 6.16698 18.5208 6.97198 19.7738 8.22498C21.3057 9.75681 22.1667 11.8346 22.1667 14V18.3913L23.1105 20.279C23.562 21.1831 23.5142 22.2565 22.9822 23.1163C22.4513 23.9761 21.5122 24.5 20.5018 24.5H15.1667C15.1667 25.144 14.6428 25.6666 14 25.6666C13.356 25.6666 12.8333 25.144 12.8333 24.5H7.49817C6.48667 24.5 5.54752 23.9761 5.01669 23.1163C4.48469 22.2565 4.43684 21.1831 4.88951 20.279L5.83333 18.3913V14C5.83333 11.8346 6.69319 9.75681 8.22502 8.22498C9.47919 6.97198 11.0985 6.16698 12.8333 5.91732ZM14 8.16664C12.4518 8.16664 10.969 8.78148 9.87469 9.87581C8.78035 10.969 8.16666 12.453 8.16666 14V18.6666C8.16666 18.8475 8.12351 19.026 8.04301 19.1881C8.04301 19.1881 7.52384 20.2265 6.9755 21.322C6.88567 21.5028 6.89501 21.7186 7.00117 21.8901C7.10734 22.0616 7.29517 22.1666 7.49817 22.1666H20.5018C20.7037 22.1666 20.8915 22.0616 20.9977 21.8901C21.1038 21.7186 21.1132 21.5028 21.0234 21.322C20.475 20.2265 19.9558 19.1881 19.9558 19.1881C19.8753 19.026 19.8333 18.8475 19.8333 18.6666V14C19.8333 12.453 19.2185 10.969 18.1242 9.87581C17.0298 8.78148 15.547 8.16664 14 8.16664Z"
                                    fill="#FF7B00" />
                            </svg>
                            @php
                                $unreadCount = auth()->user()->unreadNotifications()->count();
                            @endphp
                            @if ($unreadCount > 0)
                                <span class="badge light text-white bg-danger rounded-circle"
                                    id="headerNotificationBadge">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu rounded dropdown-menu-end" style="min-width: 380px;">
                            <!-- Dropdown Header dengan All Read Button -->
                            <div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-2 border-bottom">
                                <h6 class="mb-0 fw-bold" style="color: var(--primary-navy);">
                                    <i class="fas fa-bell me-2" style="color: #FF7B00;"></i>Notifications
                                </h6>
                                @if ($unreadCount > 0)
                                    <button class="btn btn-sm btn-link text-decoration-none"
                                        onclick="markAllNotificationsRead(event)"
                                        style="color: #FF7B00; font-size: 12px;">
                                        <i class="fas fa-check-double me-1"></i>Mark All Read
                                    </button>
                                @endif
                            </div>

                            <!-- Notification List -->
                            <div id="DZ_W_Notification1" class="widget-media dz-scroll p-3" style="height:350px;">
                                <ul class="timeline" id="headerNotificationList">
                                    @forelse(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                        <li class="notification-item {{ !$notification->is_read ? 'unread' : '' }}"
                                            data-id="{{ $notification->id }}"
                                            style="{{ !$notification->is_read ? 'background: #fff9f5; border-left: 3px solid #FF7B00;' : '' }} padding: 10px; border-radius: 8px; margin-bottom: 8px;">
                                            <div class="timeline-panel d-flex">
                                                <div class="me-3">
                                                    @php
                                                        $iconMap = [
                                                            'info' => ['icon' => 'info-circle', 'color' => '#3498db'],
                                                            'success' => [
                                                                'icon' => 'check-circle',
                                                                'color' => '#2ecc71',
                                                            ],
                                                            'warning' => [
                                                                'icon' => 'exclamation-triangle',
                                                                'color' => '#f39c12',
                                                            ],
                                                            'danger' => [
                                                                'icon' => 'times-circle',
                                                                'color' => '#e74c3c',
                                                            ],
                                                            'error' => ['icon' => 'times-circle', 'color' => '#e74c3c'],
                                                            'approval' => [
                                                                'icon' => 'clipboard-check',
                                                                'color' => '#1a2b4c',
                                                            ],
                                                            'assignment' => [
                                                                'icon' => 'user-plus',
                                                                'color' => '#27ae60',
                                                            ],
                                                            'vr_request' => [
                                                                'icon' => 'file-invoice-dollar',
                                                                'color' => '#e67e22',
                                                            ],
                                                            'closure' => [
                                                                'icon' => 'check-circle',
                                                                'color' => '#27ae60',
                                                            ],
                                                            'comment' => ['icon' => 'comment', 'color' => '#3498db'],
                                                            'broadcast' => ['icon' => 'bullhorn', 'color' => '#FF7B00'],
                                                        ];
                                                        $iconData = $iconMap[$notification->type] ?? [
                                                            'icon' => 'bell',
                                                            'color' => '#95a5a6',
                                                        ];
                                                    @endphp
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px; background: {{ $iconData['color'] }}15;">
                                                        <i class="fas fa-{{ $iconData['icon'] }}"
                                                            style="color: {{ $iconData['color'] }}; font-size: 18px;"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <h6 class="mb-1 fw-semibold"
                                                            style="font-size: 13px; color: #333;">
                                                            {{ $notification->title }}</h6>
                                                        @if (!$notification->is_read)
                                                            <span class="badge badge-xs"
                                                                style="background: #FF7B00; color: white; font-size: 9px;">NEW</span>
                                                        @endif
                                                    </div>
                                                    <p class="mb-1 text-muted"
                                                        style="font-size: 11px; line-height: 1.4;">
                                                        {{ \Illuminate\Support\Str::limit($notification->message, 60) }}
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <small class="text-muted" style="font-size: 10px;">
                                                            <i
                                                                class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                                        </small>
                                                        <div>
                                                            @if ($notification->ticket_id)
                                                                <a href="{{ route('tickets.show', $notification->ticket_id) }}"
                                                                    class="badge badge-sm text-decoration-none"
                                                                    style="background: #1a2b4c; color: white; font-size: 10px; padding: 4px 8px;">
                                                                    <i class="fas fa-ticket-alt me-1"></i>View Ticket
                                                                </a>
                                                            @endif
                                                            @if (!$notification->is_read)
                                                                <button
                                                                    class="badge badge-sm border-0 ms-1 mark-single-read"
                                                                    onclick="markSingleNotificationRead(event, {{ $notification->id }})"
                                                                    style="background: #2ecc71; color: white; font-size: 10px; padding: 4px 8px; cursor: pointer;">
                                                                    <i class="fas fa-check me-1"></i>Read
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="text-center py-4">
                                            <div class="mb-3">
                                                <i class="fas fa-bell-slash fa-3x" style="color: #ddd;"></i>
                                            </div>
                                            <p class="text-muted mb-0" style="font-size: 13px;">No notifications yet</p>
                                            <small class="text-muted">We'll notify you when something arrives</small>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>

                            <!-- Dropdown Footer -->
                            <div class="border-top p-2">
                                <a class="all-notification d-block text-center py-2 text-decoration-none"
                                    href="{{ route('notifications.index') }}"
                                    style="color: #FF7B00; font-size: 13px; font-weight: 500;">
                                    <i class="fas fa-arrow-right me-2"></i>See All Notifications
                                </a>
                            </div>
                        </div>
                    </li>

                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0)" role="button" data-bs-toggle="dropdown">
                            <div class="profile-image-container"
                                style="width: 52px; height: 52px; border-radius: 50%; overflow: hidden; margin-right: 8px; background-color: #f0f0f0;">
                                <img src="{{ auth()->user()->profile_picture_url }}" alt="{{ auth()->user()->name }}"
                                    style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
                            </div>
                            <div class="header-info">
                                <span class="text-black">{{ auth()->user()->name }}</span>
                                <p class="fs-12 mb-0">{{ ucfirst(auth()->user()->role) }}</p>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- Profile -->
                            <a href="{{ route('profile.index') }}" class="dropdown-item ai-icon">
                                <i class="fas fa-user-circle text-primary me-2"></i>
                                <span>Profile</span>
                            </a>

                            <!-- Settings - TAMBAHKAN INI! -->
                            <a href="{{ route('settings.index') }}" class="dropdown-item ai-icon">
                                <i class="fas fa-cog text-warning me-2"></i>
                                <span>Settings</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            <!-- Logout -->
                            <a href="javascript:void(0)" onclick="confirmLogout()" class="dropdown-item ai-icon">
                                <i class="fas fa-sign-out-alt text-danger me-2"></i>
                                <span>Logout</span>
                            </a>
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

<script>
    // =============================================
    // FUNGSI HEADER - LOGOUT
    // =============================================
    function confirmHeaderLogout() {
        Swal.fire({
            title: 'Logout?',
            text: 'Are you sure you want to logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ff6600',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-sign-out-alt me-2"></i>Yes, Logout',
            cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }

    // =============================================
    // FUNGSI GLOBAL UNTUK SINKRONISASI
    // =============================================

    // Update badge di header
    function updateHeaderBadge() {
        $.ajax({
            url: "{{ route('notifications.unread-count') }}",
            method: 'GET',
            success: function(response) {
                const $badge = $('#headerNotificationBadge');

                if (response.count > 0) {
                    if ($badge.length) {
                        $badge.text(response.count);
                    } else {
                        $('.notification_dropdown .nav-link').append(
                            '<span class="badge light text-white bg-danger rounded-circle" id="headerNotificationBadge">' +
                            response.count + '</span>'
                        );
                    }
                } else {
                    $badge.remove();
                    $('button[onclick="markAllNotificationsRead(event)"]').remove();
                }
            }
        });
    }

    // Refresh daftar notifikasi di header
    function refreshHeaderNotificationList() {
        $.ajax({
            url: "{{ route('notifications.latest') }}",
            method: 'GET',
            success: function(notifications) {
                const $list = $('#headerNotificationList');

                if (!notifications || notifications.length === 0) {
                    $list.html(`
                        <li class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-bell-slash fa-3x" style="color: #ddd;"></i>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 13px;">No notifications yet</p>
                            <small class="text-muted">We'll notify you when something arrives</small>
                        </li>
                    `);
                    return;
                }

                let html = '';
                notifications.forEach(notif => {
                    const isUnread = !notif.is_read;
                    const iconMap = {
                        info: {
                            icon: 'info-circle',
                            color: '#3498db'
                        },
                        success: {
                            icon: 'check-circle',
                            color: '#2ecc71'
                        },
                        warning: {
                            icon: 'exclamation-triangle',
                            color: '#f39c12'
                        },
                        danger: {
                            icon: 'times-circle',
                            color: '#e74c3c'
                        },
                        approval: {
                            icon: 'clipboard-check',
                            color: '#1a2b4c'
                        },
                        assignment: {
                            icon: 'user-plus',
                            color: '#27ae60'
                        },
                        vr_request: {
                            icon: 'file-invoice-dollar',
                            color: '#e67e22'
                        },
                        closure: {
                            icon: 'check-circle',
                            color: '#27ae60'
                        },
                        comment: {
                            icon: 'comment',
                            color: '#3498db'
                        },
                        broadcast: {
                            icon: 'bullhorn',
                            color: '#FF7B00'
                        }
                    };
                    const iconData = iconMap[notif.type] || {
                        icon: 'bell',
                        color: '#95a5a6'
                    };

                    html += `
                        <li class="notification-item ${isUnread ? 'unread' : ''}"
                            data-id="${notif.id}"
                            style="${isUnread ? 'background: #fff9f5; border-left: 3px solid #FF7B00;' : ''} padding: 10px; border-radius: 8px; margin-bottom: 8px; cursor: pointer;">
                            <div class="timeline-panel d-flex">
                                <div class="me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; background: ${iconData.color}15;">
                                        <i class="fas fa-${iconData.icon}"
                                            style="color: ${iconData.color}; font-size: 18px;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-1 fw-semibold" style="font-size: 13px; color: #333;">
                                            ${escapeHtml(notif.title)}</h6>
                                        ${isUnread ? '<span class="badge badge-xs" style="background: #FF7B00; color: white; font-size: 9px;">NEW</span>' : ''}
                                    </div>
                                    <p class="mb-1 text-muted" style="font-size: 11px; line-height: 1.4;">
                                        ${escapeHtml(notif.message.substring(0, 60))}${notif.message.length > 60 ? '...' : ''}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted" style="font-size: 10px;">
                                            <i class="far fa-clock me-1"></i>${notif.time}
                                        </small>
                                        <div>
                                            ${notif.ticket_id ? `<a href="{{ url('tickets') }}/${notif.ticket_id}" class="badge badge-sm text-decoration-none" style="background: #1a2b4c; color: white; font-size: 10px; padding: 4px 8px;" onclick="event.stopPropagation();">
                                                <i class="fas fa-ticket-alt me-1"></i>View Ticket
                                            </a>` : ''}
                                            ${isUnread ? `<button class="badge badge-sm border-0 ms-1 mark-single-read" onclick="markSingleNotificationRead(event, ${notif.id})" style="background: #2ecc71; color: white; font-size: 10px; padding: 4px 8px; cursor: pointer;">
                                                <i class="fas fa-check me-1"></i>Read
                                            </button>` : ''}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    `;
                });

                $list.html(html);

                // Tambahin event click ke seluruh notification item
                $('.notification-item').off('click').on('click', function(e) {
                    if ($(e.target).closest('a, button').length) return;
                    const id = $(this).data('id');
                    if (id) {
                        window.location.href = "{{ route('notifications.index') }}";
                    }
                });
            }
        });
    }

    // Escape HTML helper
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // =============================================
    // MARK ALL NOTIFICATIONS READ (GLOBAL)
    // =============================================
    window.markAllNotificationsRead = function(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        $.ajax({
            url: "{{ route('notifications.mark-all-read') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    // Update header
                    $('#headerNotificationList .notification-item').css({
                        'background': 'transparent',
                        'border-left': 'none'
                    });
                    $('#headerNotificationList .notification-item .badge-danger, #headerNotificationList .notification-item span[style*="background: #FF7B00"]')
                        .remove();
                    $('#headerNotificationList .mark-single-read').remove();
                    $('#headerNotificationBadge').remove();
                    $('button[onclick="markAllNotificationsRead(event)"]').remove();

                    // Refresh header list
                    refreshHeaderNotificationList();

                    // Jika halaman notifikasi sedang terbuka, refresh juga
                    if (typeof loadNotifications === 'function') {
                        let url = "{{ route('notifications.index') }}?";
                        const type = $('#filterType').val();
                        const startDate = $('#startDateFilter').val();
                        const endDate = $('#endDateFilter').val();

                        if (type && type !== 'all') url += "type=" + type + "&";
                        if (startDate) url += "start_date=" + startDate + "&";
                        if (endDate) url += "end_date=" + endDate + "&";

                        loadNotifications(url);
                    }

                    toastr.success('All notifications marked as read');
                }
            },
            error: function() {
                toastr.error('Failed to mark all as read');
            }
        });
    };

    // =============================================
    // MARK SINGLE NOTIFICATION READ (GLOBAL)
    // =============================================
    window.markSingleNotificationRead = function(event, id) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        const $item = $(event.target).closest('.notification-item');

        $.ajax({
            url: "{{ url('notifications') }}/mark-read/" + id,
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    // Update header
                    $item.css({
                        'background': 'transparent',
                        'border-left': 'none'
                    });
                    $item.find('.badge-danger, span[style*="background: #FF7B00"]').remove();
                    $item.find('.mark-single-read').remove();

                    // Update badge count
                    const $badge = $('#headerNotificationBadge');
                    if ($badge.length) {
                        const count = parseInt($badge.text()) - 1;
                        if (count > 0) {
                            $badge.text(count);
                        } else {
                            $badge.remove();
                            $('button[onclick="markAllNotificationsRead(event)"]').remove();
                        }
                    }

                    // Refresh header list
                    refreshHeaderNotificationList();

                    // Jika halaman notifikasi sedang terbuka, refresh juga
                    if (typeof loadNotifications === 'function') {
                        let url = "{{ route('notifications.index') }}?";
                        const type = $('#filterType').val();
                        const startDate = $('#startDateFilter').val();
                        const endDate = $('#endDateFilter').val();
                        const currentPage = $('.pagination .active .ajax-page').data('page') || 1;

                        if (type && type !== 'all') url += "type=" + type + "&";
                        if (startDate) url += "start_date=" + startDate + "&";
                        if (endDate) url += "end_date=" + endDate + "&";
                        url += "page=" + currentPage;

                        loadNotifications(url);
                    }

                    toastr.success('Marked as read');
                }
            },
            error: function() {
                toastr.error('Failed to mark as read');
            }
        });
    };

    // =============================================
    // POLLING UPDATE HEADER (SETIAP 30 DETIK)
    // =============================================
    setInterval(function() {
        updateHeaderBadge();
        refreshHeaderNotificationList();
    }, 30000);

    // =============================================
    // INITIAL LOAD
    // =============================================
    $(document).ready(function() {
        updateHeaderBadge();
    });
</script>

<style>
    /* Additional styles for header notifications */
    .notification_dropdown .dropdown-menu {
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
    }

    .notification-item {
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .notification-item:hover {
        background: #f8f9fa !important;
    }

    .notification-item.unread {
        position: relative;
    }

    .widget-media.dz-scroll {
        overflow-y: auto;
    }

    .widget-media.dz-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .widget-media.dz-scroll::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 4px;
    }

    .all-notification:hover {
        background: #fff5eb;
        border-radius: 8px;
    }

    .mark-single-read:hover {
        opacity: 0.8 !important;
    }
</style>
