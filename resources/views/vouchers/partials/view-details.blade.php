<div class="vr-details">
    <!-- VR Header -->
    <div class="vr-header-details mb-4">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="mb-1">#{{ $vr->vr_number }}</h4>
                <div class="text-muted">
                    Created: {{ $vr->created_at->format('d F Y, H:i') }}
                    @if ($vr->created_by === auth()->id())
                        <span class="badge bg-info ms-2">Created by me</span>
                    @endif
                </div>
            </div>
            <div>
                <span
                    class="badge bg-{{ $vr->status === 'paid' ? 'success' : ($vr->status === 'rejected' ? 'danger' : 'warning') }} fs-6">
                    {{ strtoupper(str_replace('_', ' ', $vr->status)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Info -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted">TOTAL AMOUNT</h6>
                    <h3 class="text-primary">Rp {{ number_format($vr->total_amount, 0, ',', '.') }}</h3>
                    <p class="card-text small">{{ $vr->items->count() }} items</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted">CREATED BY</h6>
                    <h5>{{ $vr->creator->name }}</h5>
                    <p class="card-text small">{{ ucfirst($vr->creator->role) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title text-muted">TICKET</h6>
                    <h5>#{{ $vr->ticket->ticket_number }}</h5>
                    <p class="card-text small">{{ Str::limit($vr->ticket->title, 30) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Timeline -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0">Approval Status</h6>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col">
                    <div class="mb-2">
                        <i class="fas fa-user-cog fa-2x {{ $vr->admin_approved ? 'text-success' : 'text-muted' }}"></i>
                    </div>
                    <div class="fw-bold">Admin Engineering</div>
                    <div class="small">{{ $vr->admin_approved ? 'Approved' : 'Pending' }}</div>
                    @if ($vr->admin_approved_at)
                        <div class="small text-muted">{{ $vr->admin_approved_at->format('d M, H:i') }}</div>
                        @if ($vr->adminApprover)
                            <div class="small text-muted">{{ $vr->adminApprover->name }}</div>
                        @endif
                    @endif
                </div>

                <div class="col">
                    <div class="mb-2">
                        <i class="fas fa-user-tie fa-2x {{ $vr->om_approved ? 'text-success' : 'text-muted' }}"></i>
                    </div>
                    <div class="fw-bold">Operation Manager</div>
                    <div class="small">{{ $vr->om_approved ? 'Approved' : 'Pending' }}</div>
                    @if ($vr->om_approved_at)
                        <div class="small text-muted">{{ $vr->om_approved_at->format('d M, H:i') }}</div>
                        @if ($vr->omApprover)
                            <div class="small text-muted">{{ $vr->omApprover->name }}</div>
                        @endif
                    @endif
                </div>

                <div class="col">
                    <div class="mb-2">
                        <i class="fas fa-user-shield fa-2x {{ $vr->gm_approved ? 'text-success' : 'text-muted' }}"></i>
                    </div>
                    <div class="fw-bold">General Manager</div>
                    <div class="small">{{ $vr->gm_approved ? 'Approved' : 'Pending' }}</div>
                    @if ($vr->gm_approved_at)
                        <div class="small text-muted">{{ $vr->gm_approved_at->format('d M, H:i') }}</div>
                        @if ($vr->gmApprover)
                            <div class="small text-muted">{{ $vr->gmApprover->name }}</div>
                        @endif
                    @endif
                </div>

                <div class="col">
                    <div class="mb-2">
                        <i
                            class="fas fa-money-check-alt fa-2x {{ $vr->status === 'paid' ? 'text-success' : 'text-muted' }}"></i>
                    </div>
                    <div class="fw-bold">Payment</div>
                    <div class="small">{{ $vr->status === 'paid' ? 'Paid' : 'Pending' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Items List</h6>
            <span class="badge bg-primary">{{ $vr->items->count() }} items</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Item Name</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Subtotal</th>
                            <th>Vendor</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vr->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="text-end">Rp
                                    {{ number_format($item->qty * $item->unit_price, 0, ',', '.') }}</td>
                                <td>{{ $item->vendor ?? '-' }}</td>
                                <td>{{ $item->description ?? '-' }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-success">
                            <td colspan="4" class="text-end fw-bold">TOTAL AMOUNT</td>
                            <td colspan="3" class="text-end fw-bold fs-5">
                                Rp {{ number_format($vr->total_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if ($vr->notes)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Notes & History</h6>
            </div>
            <div class="card-body">
                <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit;">{{ $vr->notes }}</pre>
            </div>
        </div>
    @endif
</div>
