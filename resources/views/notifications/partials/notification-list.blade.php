{{-- resources/views/notifications/partials/notification-list.blade.php --}}
@if (count($groupedData) > 0)
    @php
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
    @endphp

    <div class="notifications-container">
        @foreach ($groupedData as $date => $notifs)
            @php
                $isToday = $date === $today;
                $isYesterday = $date === $yesterday;

                if ($isToday) {
                    $dateLabel = 'Today';
                } elseif ($isYesterday) {
                    $dateLabel = 'Yesterday';
                } else {
                    $d = \Carbon\Carbon::parse($date);
                    $dateLabel = $d->format('l, d F Y');
                }

                $unreadCount = collect($notifs)
                    ->filter(function ($n) {
                        return !$n->is_read;
                    })
                    ->count();
                $isOpen = $isToday;
            @endphp

            <div class="date-accordion" data-date="{{ $date }}">
                <div class="date-accordion-header {{ $isOpen ? 'active' : '' }}">
                    <div>
                        <h4>
                            {{ $dateLabel }}
                            @if ($unreadCount > 0)
                                <span class="badge-count">{{ $unreadCount }} new</span>
                            @endif
                            <small class="text-muted ms-2">({{ count($notifs) }} items)</small>
                        </h4>
                    </div>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="date-accordion-content {{ $isOpen ? 'show' : '' }}">
                    @foreach ($notifs as $notification)
                        @php
                            $icons = [
                                'info' => '<i class="fas fa-info-circle"></i>',
                                'success' => '<i class="fas fa-check-circle"></i>',
                                'warning' => '<i class="fas fa-exclamation-triangle"></i>',
                                'danger' => '<i class="fas fa-times-circle"></i>',
                                'approval' => '<i class="fas fa-clipboard-check"></i>',
                                'assignment' => '<i class="fas fa-user-plus"></i>',
                                'vr_request' => '<i class="fas fa-file-invoice-dollar"></i>',
                                'closure' => '<i class="fas fa-check-circle"></i>',
                                'comment' => '<i class="fas fa-comment"></i>',
                                'broadcast' => '<i class="fas fa-bullhorn"></i>',
                            ];
                            $icon = $icons[$notification->type] ?? '<i class="fas fa-bell"></i>';
                            $bgClasses = [
                                'info' => 'bg-info-light',
                                'success' => 'bg-success-light',
                                'warning' => 'bg-warning-light',
                                'danger' => 'bg-danger-light',
                                'approval' => 'bg-primary-light',
                                'assignment' => 'bg-success-light',
                                'vr_request' => 'bg-warning-light',
                                'closure' => 'bg-success-light',
                                'comment' => 'bg-info-light',
                                'broadcast' => 'bg-gradient',
                            ];
                            $bgClass = $bgClasses[$notification->type] ?? 'bg-secondary-light';
                            $iconHtml = "<div class=\"notification-icon {$bgClass}\">{$icon}</div>";
                        @endphp

                        <div class="notification-card {{ !$notification->is_read ? 'unread' : '' }}"
                            data-id="{{ $notification->id }}" data-ticket-id="{{ $notification->ticket_id ?? '' }}">
                            <div class="notification-content">
                                {!! $iconHtml !!}
                                <div class="notification-details">
                                    <div class="notification-header">
                                        <h5 class="notification-title">{{ $notification->title }}</h5>
                                        <span class="notification-time">
                                            {{ $notification->created_at->format('H:i') }}
                                        </span>
                                    </div>
                                    <p class="notification-message">{{ $notification->message }}</p>

                                    @if ($notification->ticket_id)
                                        <div class="notification-ticket-info">
                                            <div class="ticket-number">
                                                <i class="fas fa-ticket-alt me-1"></i>
                                                #{{ $notification->ticket->ticket_number ?? 'N/A' }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="notification-actions">
                                        @if (!$notification->is_read)
                                            <button class="btn-notification mark-read"
                                                onclick="markSingleAsRead({{ $notification->id }}, event)">
                                                <i class="fas fa-check"></i> Mark Read
                                            </button>
                                        @endif
                                        @if ($notification->ticket_id)
                                            <a href="{{ route('tickets.show', $notification->ticket_id) }}"
                                                class="btn-notification view">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        @endif
                                        <button class="btn-notification delete"
                                            onclick="deleteNotification({{ $notification->id }}, event)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination Info --}}
    <div class="pagination-info">
        <small>
            <i class="fas fa-calendar-alt me-1"></i>
            Page {{ $notifications->currentPage() }} of {{ $notifications->lastPage() }}
            ({{ count($groupedData) }} dates, {{ $totalNotifications ?? 0 }} total notifications)
        </small>
    </div>

    {{-- Pagination Links --}}
    <div class="pagination-wrapper">
        @if ($notifications->hasPages())
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center custom-pagination">
                    {{-- Previous --}}
                    @if ($notifications->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fa fa-angle-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link ajax-page" href="javascript:void(0)"
                                data-page="{{ $notifications->currentPage() - 1 }}">
                                <i class="fa fa-angle-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Window Pagination --}}
                    @php
                        $currentPage = $notifications->currentPage();
                        $lastPage = $notifications->lastPage();
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $currentPage + 2);
                    @endphp

                    @if ($start > 1)
                        <li class="page-item">
                            <a class="page-link ajax-page" href="javascript:void(0)" data-page="1">1</a>
                        </li>
                        @if ($start > 2)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endif

                    @for ($page = $start; $page <= $end; $page++)
                        <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                            <a class="page-link ajax-page" href="javascript:void(0)"
                                data-page="{{ $page }}">{{ $page }}</a>
                        </li>
                    @endfor

                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                        <li class="page-item">
                            <a class="page-link ajax-page" href="javascript:void(0)"
                                data-page="{{ $lastPage }}">{{ $lastPage }}</a>
                        </li>
                    @endif

                    {{-- Next --}}
                    @if ($notifications->hasMorePages())
                        <li class="page-item">
                            <a class="page-link ajax-page" href="javascript:void(0)"
                                data-page="{{ $notifications->currentPage() + 1 }}">
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fa fa-angle-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>
@else
    <div class="notifications-container">
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <h4>No Notifications Found</h4>
            <p>You're all caught up! No notifications match your current filters.</p>
        </div>
    </div>
@endif
