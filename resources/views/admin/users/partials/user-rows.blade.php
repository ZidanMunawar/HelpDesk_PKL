<div class="table-responsive">
    <table id="usersTable" class="display table table-hover" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Photo</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Department</th>
                <th>Status</th>
                <th>Signature</th>
                <th>Joined</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
                <tr>
                    <td>{{ $users->firstItem() + $index }}</td>
                    <td class="text-center">
                        <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" class="profile-img">
                    </td>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>
                        {{ $user->email }}
                        @if ($user->email_verified_at)
                            <i class="fas fa-check-circle verified-icon" title="Email Verified"></i>
                        @else
                            <i class="fas fa-clock unverified-icon" title="Pending Verification"></i>
                        @endif
                    </td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $user->role }}">
                            {{ $user->role_name }}
                        </span>
                    </td>
                    <td>
                        @if ($user->department)
                            <span class="badge badge-info">{{ $user->department->name }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $user->status }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if ($user->has_signature)
                            <i class="fas fa-signature text-success" title="Has Signature"></i>
                        @else
                            <i class="fas fa-times-circle text-muted" title="No Signature"></i>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-primary btn-sm shadow btn-xs sharp me-1 edit-user"
                                data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}" data-phone="{{ $user->phone }}"
                                data-role="{{ $user->role }}" data-status="{{ $user->status }}"
                                data-department-id="{{ $user->department_id }}" title="Edit User">
                                <i class="fas fa-pencil-alt"></i>
                            </button>

                            @if ($user->status === 'pending')
                                <button type="button"
                                    class="btn btn-success btn-sm shadow btn-xs sharp me-1 activate-pending"
                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                    title="Activate Pending User">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                <button type="button"
                                    class="btn btn-info btn-sm shadow btn-xs sharp me-1 quick-activate-pending"
                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                    title="Quick Activate (Skip Department)">
                                    <i class="fas fa-bolt"></i>
                                </button>
                            @else
                                <button type="button"
                                    class="btn btn-{{ $user->status === 'active' ? 'warning' : 'success' }} btn-sm shadow btn-xs sharp me-1 toggle-status"
                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                    data-status="{{ $user->status }}"
                                    title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas fa-{{ $user->status === 'active' ? 'ban' : 'check' }}"></i>
                                </button>
                            @endif

                            <button type="button" class="btn btn-danger btn-sm shadow btn-xs sharp delete-user"
                                data-id="{{ $user->id }}" data-name="{{ $user->name }}" title="Delete User">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="color: #6C757D; padding: 40px;">
                        No users found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-wrapper" style="margin-top: 20px; text-align: right;">
    {{ $users->withQueryString()->links('pagination::bootstrap-4') }}
</div>
