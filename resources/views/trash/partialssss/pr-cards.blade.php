@if ($voucherRequests->count() > 0)
    @foreach ($voucherRequests as $vr)
        <div class="pr-card" data-vr-id="{{ $vr->id }}">
            <div class="pr-card-body">
                <!-- Thumbnail -->
                <div class="pr-thumbnail">
                    @php $firstPhoto = $vr->attachments->first(); @endphp
                    @if ($firstPhoto)
                        <img src="{{ Storage::url($firstPhoto->file_path) }}" alt="PR Photo"
                            onclick="previewPhoto('{{ Storage::url($firstPhoto->file_path) }}')">
                    @else
                        <div class="no-image"><i class="fas fa-image"></i></div>
                    @endif
                </div>

                <!-- Info -->
                <div class="pr-info">
                    <div class="pr-header">
                        <span class="pr-number">{{ $vr->vr_number }}</span>
                        <span
                            class="pr-status {{ $vr->status }}">{{ str_replace('_', ' ', ucfirst($vr->status)) }}</span>
                    </div>
                    <div class="pr-ticket">
                        <i class="fas fa-ticket-alt"></i>
                        <a
                            href="{{ route('tickets.show', $vr->ticket_id) }}">#{{ $vr->ticket->ticket_number ?? 'N/A' }}</a>
                    </div>
                    <div class="pr-title">{{ Str::limit($vr->ticket->title ?? 'N/A', 80) }}</div>
                    <div class="pr-meta">
                        <span><i class="fas fa-user"></i> {{ $vr->creator->name ?? 'Unknown' }}</span>
                        <span><i class="fas fa-calendar"></i> {{ $vr->created_at->format('d M Y') }}</span>
                        <span><i class="fas fa-camera"></i> {{ $vr->attachments->count() }} photo(s)</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="pr-actions">
                    <button class="pr-action-btn view" onclick="viewPr({{ $vr->id }})">
                        <i class="fas fa-eye"></i> View
                    </button>

                    @php $canApprove = $vr->canApprove(Auth::user()); @endphp
                    @if ($canApprove)
                        <button class="pr-action-btn approve" onclick="approvePr({{ $vr->id }})">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="pr-action-btn reject" onclick="rejectPr({{ $vr->id }})">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    @endif

                    @if ($vr->canMarkPaid(Auth::user()))
                        <button class="pr-action-btn paid" onclick="markAsPaid({{ $vr->id }})">
                            <i class="fas fa-money-bill-wave"></i> Mark Paid
                        </button>
                    @endif

                    @if ($vr->canDelete() && (Auth::user()->role == 'superadmin' || Auth::id() == $vr->created_by))
                        <button class="pr-action-btn delete" onclick="deletePr({{ $vr->id }})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    @endif

                    <button class="pr-action-btn print" onclick="printPr({{ $vr->id }})">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $voucherRequests->links() }}
    </div>
@else
    <div class="empty-state">
        <div class="empty-state-icon"><i class="fas fa-receipt"></i></div>
        <div class="empty-state-title">No Purchase Requests Found</div>
        <div class="empty-state-text">There are no purchase requests matching your criteria.</div>
        @if (in_array(Auth::user()->role, ['admin_eng', 'superadmin']))
            <div class="empty-state-buttons">
                <button class="btn-create-pr" id="emptyStateCreateBtn">
                    <i class="fas fa-plus-circle"></i> Create Purchase Request
                </button>
            </div>
        @endif
    </div>
@endif
