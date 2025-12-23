<!--**********************************
    Chat box start
***********************************-->
<div class="chatbox">
    <div class="chatbox-close"></div>
    <div class="custom-tab-1">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#quick-notes">
                    <i class="fa fa-sticky-note me-2"></i>Notes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#quick-alerts">
                    <i class="fa fa-exclamation-triangle me-2"></i>Alerts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#quick-tickets">
                    <i class="fa fa-ticket-alt me-2"></i>Recent Tickets
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <!-- Quick Notes Tab -->
            <div class="tab-pane fade" id="quick-notes" role="tabpanel">
                <div class="card mb-sm-3 mb-md-0 contacts_card dz-chat-user-box">
                    <div class="card-header chat-list-header text-center">
                        <a href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.3"
                                        transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000)"
                                        x="4" y="11" width="16" height="2" rx="1" />
                                </g>
                            </svg></a>
                        <div>
                            <h6 class="mb-1">Quick Notes</h6>
                            <p class="mb-0">Personal notes & reminders</p>
                        </div>
                        <a href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <circle fill="#000000" cx="5" cy="12" r="2" />
                                    <circle fill="#000000" cx="12" cy="12" r="2" />
                                    <circle fill="#000000" cx="19" cy="12" r="2" />
                                </g>
                            </svg></a>
                    </div>
                    <div class="card-body contacts_body p-0 dz-scroll" id="DZ_W_Contacts_Body">
                        <ul class="contacts">
                            <li class="note-item">
                                <div class="d-flex bd-highlight">
                                    <div class="note_icon bg-primary">
                                        <i class="fa fa-sticky-note"></i>
                                    </div>
                                    <div class="note_info">
                                        <span>Follow up ticket #MR-2024-0001</span>
                                        <p>Check status tomorrow</p>
                                    </div>
                                    <div class="ms-auto">
                                        <small>2h ago</small>
                                    </div>
                                </div>
                            </li>
                            <li class="note-item">
                                <div class="d-flex bd-highlight">
                                    <div class="note_icon bg-warning">
                                        <i class="fa fa-clock"></i>
                                    </div>
                                    <div class="note_info">
                                        <span>Meeting with IT team</span>
                                        <p>Discuss maintenance schedule</p>
                                    </div>
                                    <div class="ms-auto">
                                        <small>5h ago</small>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Quick Alerts Tab -->
            <div class="tab-pane fade" id="quick-alerts" role="tabpanel">
                <div class="card mb-sm-3 mb-md-0 contacts_card dz-chat-user-box">
                    <div class="card-header chat-list-header text-center">
                        <a href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect fill="#000000" x="4" y="11" width="16" height="2"
                                        rx="1" />
                                    <rect fill="#000000" opacity="0.3"
                                        transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000)"
                                        x="4" y="11" width="16" height="2" rx="1" />
                                </g>
                            </svg></a>
                        <div>
                            <h6 class="mb-1">System Alerts</h6>
                            <p class="mb-0">Important notifications</p>
                        </div>
                        <a href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <circle fill="#000000" cx="5" cy="12" r="2" />
                                    <circle fill="#000000" cx="12" cy="12" r="2" />
                                    <circle fill="#000000" cx="19" cy="12" r="2" />
                                </g>
                            </svg></a>
                    </div>
                    <div class="card-body contacts_body p-0 dz-scroll" id="DZ_W_Alerts_Body">
                        <ul class="contacts">
                            @php
                                $overdueTickets = auth()->user()->tickets()->overdue()->count();
                                $pendingTickets = auth()->user()->tickets()->pending()->count();
                            @endphp

                            @if ($overdueTickets > 0)
                                <li class="alert-item border-danger">
                                    <div class="d-flex bd-highlight">
                                        <div class="alert_icon bg-danger">
                                            <i class="fa fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="alert_info">
                                            <span class="text-danger">Overdue Tickets</span>
                                            <p>You have {{ $overdueTickets }} overdue ticket(s)</p>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            @if ($pendingTickets > 0)
                                <li class="alert-item border-warning">
                                    <div class="d-flex bd-highlight">
                                        <div class="alert_icon bg-warning">
                                            <i class="fa fa-clock"></i>
                                        </div>
                                        <div class="alert_info">
                                            <span class="text-warning">Pending Tickets</span>
                                            <p>{{ $pendingTickets }} ticket(s) awaiting response</p>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            @if ($overdueTickets == 0 && $pendingTickets == 0)
                                <li class="text-center py-5">
                                    <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                                    <p class="text-muted">All clear! No alerts</p>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Recent Tickets Tab -->
            <div class="tab-pane fade show active" id="quick-tickets" role="tabpanel">
                <div class="card mb-sm-3 mb-md-0 contacts_card dz-chat-user-box">
                    <div class="card-header chat-list-header text-center">
                        <a href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect fill="#000000" x="4" y="11" width="16" height="2"
                                        rx="1" />
                                    <rect fill="#000000" opacity="0.3"
                                        transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000)"
                                        x="4" y="11" width="16" height="2" rx="1" />
                                </g>
                            </svg></a>
                        <div>
                            <h6 class="mb-1">Recent Tickets</h6>
                            <p class="mb-0">Latest maintenance requests</p>
                        </div>
                        <a href="javascript:void(0)"><svg xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px"
                                viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <circle fill="#000000" cx="5" cy="12" r="2" />
                                    <circle fill="#000000" cx="12" cy="12" r="2" />
                                    <circle fill="#000000" cx="19" cy="12" r="2" />
                                </g>
                            </svg></a>
                    </div>
                    <div class="card-body contacts_body p-0 dz-scroll" id="DZ_W_Tickets_Body">
                        <ul class="contacts">
                            @forelse(auth()->user()->tickets()->latest()->take(5)->get() as $ticket)
                                <li class="ticket-item">
                                    <div class="d-flex bd-highlight">
                                        <div class="ticket_icon"
                                            style="background-color: {{ $ticket->priority->color }}20;">
                                            <i class="fa fa-ticket-alt"
                                                style="color: {{ $ticket->priority->color }};"></i>
                                        </div>
                                        <div class="ticket_info">
                                            <span>{{ $ticket->ticket_number }}</span>
                                            <p>{{ Str::limit($ticket->title, 30) }}</p>
                                            <small class="text-muted">
                                                {!! $ticket->status_badge !!}
                                                <span class="ms-1">{{ $ticket->created_at->diffForHumans() }}</span>
                                            </small>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-center py-5">
                                    <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No tickets yet</p>
                                    <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#addTicketModal">
                                        Create New Ticket
                                    </a>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        <a href="#" class="btn btn-primary btn-sm btn-block">View All Tickets</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--**********************************
    Chat box End
***********************************-->

<style>
    .note-item,
    .alert-item,
    .ticket-item {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .note-item:hover,
    .alert-item:hover,
    .ticket-item:hover {
        background-color: #f8f9fa;
    }

    .note_icon,
    .alert_icon,
    .ticket_icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: white;
    }

    .note_info,
    .alert_info,
    .ticket_info {
        flex: 1;
    }

    .note_info span,
    .alert_info span,
    .ticket_info span {
        font-weight: 600;
        font-size: 14px;
        display: block;
        margin-bottom: 3px;
    }

    .note_info p,
    .alert_info p,
    .ticket_info p {
        margin: 0;
        font-size: 12px;
        color: #888;
    }

    .alert-item {
        border-left: 3px solid;
    }

    .badge.light {
        position: absolute;
        top: -5px;
        right: -5px;
        padding: 3px 6px;
        font-size: 10px;
    }
</style>
