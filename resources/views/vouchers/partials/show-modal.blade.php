<div class="vr-details">
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="mb-2">
                <strong>VR Number:</strong>
                <span class="badge bg-primary ms-2">{{ $vr->vr_number }}</span>
            </div>
            <div class="mb-2">
                <strong>Status:</strong>
                <span
                    class="badge
                    @if ($vr->status === 'pending') bg-warning
                    @elseif($vr->status === 'admin_approved') bg-info
                    @elseif($vr->status === 'om_approved') bg-primary
                    @elseif($vr->status === 'gm_approved') bg-success
                    @elseif($vr->status === 'paid') bg-success
                    @elseif($vr->status === 'rejected') bg-danger @endif">
                    {{ str_replace('_', ' ', $vr->status) }}
                </span>
            </div>
            <div class="mb-2">
                <strong>Created By:</strong> {{ $vr->creator->name }}
            </div>
            <div class="mb-2">
                <strong>Created Date:</strong> {{ $vr->created_at->format('d M Y, H:i') }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-2">
                <strong>Ticket:</strong>
                <a href="{{ route('tickets.show', $vr->ticket_id) }}" target="_blank">
                    #{{ $vr->ticket->ticket_number }}
                </a>
            </div>
            <div class="mb-2">
                <strong>Ticket Title:</strong> {{ $vr->ticket->title }}
            </div>
            <div class="mb-2">
                <strong>Total Amount:</strong>
                <h4 class="text-success">Rp {{ number_format($vr->total_amount, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    @if ($vr->notes)
        <div class="alert alert-light mb-3">
            <strong>Notes:</strong>
            <p class="mb-0">{{ $vr->notes }}</p>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-header bg-light">
            <strong>Items</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0 vr-items-table">
                <thead>
                    <tr>
                        <th width="40%">Item Name</th>
                        <th width="15%">Qty</th>
                        <th width="20%">Unit Price</th>
                        <th width="20%">Total</th>
                        <th width="15%">Vendor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vr->items as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->qty * $item->unit_price, 0, ',', '.') }}</td>
                            <td>{{ $item->vendor ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" class="text-end"><strong>GRAND TOTAL</strong></td>
                        <td colspan="2"><strong>Rp {{ number_format($vr->total_amount, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="approval-timeline">
        <h6><strong>Approval Timeline</strong></h6>

        <div class="timeline-item">
            <div
                class="timeline-dot {{ $vr->admin_approved ? 'dot-approved' : ($vr->status === 'rejected' ? 'dot-not-done' : 'dot-pending') }}">
            </div>
            <div class="timeline-text">
                <strong>Admin Engineering</strong>
                @if ($vr->admin_approved)
                    <div>Approved by {{ $vr->adminApprover->name ?? 'Unknown' }}</div>
                    <small>{{ $vr->admin_approved_at ? $vr->admin_approved_at->format('d M Y, H:i') : '' }}</small>
                @elseif($vr->status === 'rejected')
                    <div>Not approved (rejected)</div>
                @else
                    <div>Pending approval</div>
                @endif
            </div>
        </div>

        <div class="timeline-item">
            <div
                class="timeline-dot {{ $vr->om_approved ? 'dot-approved' : ($vr->status === 'rejected' ? 'dot-not-done' : 'dot-pending') }}">
            </div>
            <div class="timeline-text">
                <strong>Operation Manager (OM)</strong>
                @if ($vr->om_approved)
                    <div>Approved by {{ $vr->omApprover->name ?? 'Unknown' }}</div>
                    <small>{{ $vr->om_approved_at ? $vr->om_approved_at->format('d M Y, H:i') : '' }}</small>
                @elseif($vr->status === 'rejected')
                    <div>Not approved (rejected)</div>
                @else
                    <div>Pending approval</div>
                @endif
            </div>
        </div>

        <div class="timeline-item">
            <div
                class="timeline-dot {{ $vr->gm_approved ? 'dot-approved' : ($vr->status === 'rejected' ? 'dot-not-done' : 'dot-pending') }}">
            </div>
            <div class="timeline-text">
                <strong>General Manager (GM)</strong>
                @if ($vr->gm_approved)
                    <div>Approved by {{ $vr->gmApprover->name ?? 'Unknown' }}</div>
                    <small>{{ $vr->gm_approved_at ? $vr->gm_approved_at->format('d M Y, H:i') : '' }}</small>
                @elseif($vr->status === 'rejected')
                    <div>Not approved (rejected)</div>
                @else
                    <div>Pending approval</div>
                @endif
            </div>
        </div>

        @if ($vr->status === 'paid')
            <div class="timeline-item">
                <div class="timeline-dot dot-approved"></div>
                <div class="timeline-text">
                    <strong>Payment Status</strong>
                    <div>Marked as Paid</div>
                </div>
            </div>
        @endif
    </div>
</div>
