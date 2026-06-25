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
                    @if ($vr->notes)
                        <div class="pr-notes">
                            <i class="fas fa-sticky-note"></i> {{ Str::limit($vr->notes, 60) }}
                        </div>
                    @endif
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
                </div>
            </div>
        </div>
    @endforeach

    <!-- Pagination dengan styling custom (sama seperti Notifikasi) -->
    @if ($voucherRequests->hasPages())
        <div class="pagination-wrapper">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center custom-pagination">
                    {{-- Previous Page Link --}}
                    @if ($voucherRequests->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fa fa-angle-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $voucherRequests->previousPageUrl() }}" rel="prev">
                                <i class="fa fa-angle-left"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($voucherRequests->getUrlRange(1, $voucherRequests->lastPage()) as $page => $url)
                        @if ($page == $voucherRequests->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link"
                                    href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($voucherRequests->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $voucherRequests->nextPageUrl() }}" rel="next">
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
        </div>
    @endif
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
