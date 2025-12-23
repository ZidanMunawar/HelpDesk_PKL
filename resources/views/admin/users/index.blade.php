@extends('layouts.main')

@section('title', 'User Management | ' . config('app.name'))

@section('page-title', 'User Management')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Admin', 'url' => 'javascript:void(0)'],
            ['title' => 'User Management', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <!-- Datatable -->
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        /* Custom DataTable Styling - Larger Size */
        #usersTable_wrapper .dataTables_length,
        #usersTable_wrapper .dataTables_filter,
        #usersTable_wrapper .dataTables_info,
        #usersTable_wrapper .dataTables_paginate {
            padding: 20px 0;
            font-size: 14px;
        }

        /* Length Menu Styling */
        #usersTable_wrapper .dataTables_length select {
            padding: 8px 35px 8px 15px;
            font-size: 14px;
            border: 1px solid #f0f1f5;
            border-radius: 0.75rem;
            background-color: #fff;
            margin: 0 10px;
        }

        #usersTable_wrapper .dataTables_length label {
            font-weight: 500;
            color: #6e6e6e;
        }

        /* Search Input Styling */
        #usersTable_wrapper .dataTables_filter input {
            padding: 10px 20px;
            font-size: 14px;
            border: 1px solid #f0f1f5;
            border-radius: 0.75rem;
            width: 250px;
            margin-left: 10px;
        }

        #usersTable_wrapper .dataTables_filter label {
            font-weight: 500;
            color: #6e6e6e;
        }

        /* Info Text Styling */
        #usersTable_wrapper .dataTables_info {
            font-size: 14px;
            font-weight: 500;
            color: #6e6e6e;
        }

        /* Pagination Styling */
        #usersTable_wrapper .dataTables_paginate {
            float: right;
        }

        #usersTable_wrapper .dataTables_paginate .pagination {
            margin: 0;
        }

        #usersTable_wrapper .dataTables_paginate .paginate_button {
            padding: 8px 16px;
            margin: 0 3px;
            border: 1px solid #f0f1f5;
            background: #fff;
            border-radius: 0.75rem;
            font-size: 14px;
            font-weight: 500;
            color: #6e6e6e;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #usersTable_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary);
            color: white !important;
            border-color: var(--primary);
        }

        #usersTable_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary);
            color: white !important;
            border-color: var(--primary);
        }

        #usersTable_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8f9fa;
        }

        #usersTable_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #f8f9fa;
            color: #6e6e6e !important;
            border-color: #f0f1f5;
        }

        #usersTable_wrapper .dataTables_paginate .paginate_button.previous,
        #usersTable_wrapper .dataTables_paginate .paginate_button.next {
            padding: 8px 12px;
        }

        #usersTable_wrapper .dataTables_paginate .paginate_button i {
            font-size: 16px;
        }

        /* Table Styling */
        #usersTable {
            font-size: 14px;
        }

        #usersTable thead th {
            font-size: 14px;
            font-weight: 600;
            padding: 15px 10px;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        #usersTable tbody td {
            padding: 12px 10px;
            vertical-align: middle;
        }

        #usersTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Badge Styling */
        .badge {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
        }

        /* Button Action Styling */
        .btn-xs.sharp {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-xs.sharp i {
            font-size: 14px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #usersTable_wrapper .dataTables_filter input {
                width: 100%;
                margin-top: 10px;
            }

            #usersTable_wrapper .dataTables_length,
            #usersTable_wrapper .dataTables_filter {
                text-align: left;
            }

            #usersTable_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                margin-top: 15px;
            }
        }
    </style>
@endpush


@section('content')
    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addUserForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" placeholder="Enter full name"
                                        required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" placeholder="Enter email"
                                        required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Phone</label>
                                    <input type="text" class="form-control" name="phone"
                                        placeholder="Enter phone number">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Profile Picture</label>
                                    <input type="file" class="form-control" name="profile_picture" accept="image/*">
                                    <small class="text-muted">Max size: 2MB (jpg, jpeg, png, gif)</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" placeholder="Enter password"
                                        required>
                                    <small class="text-muted">Minimum 8 characters</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        placeholder="Confirm password" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Role <span class="text-danger">*</span></label>
                                    <select class="form-control" name="role" required>
                                        <option value="">Select Role</option>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i>Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Full Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="edit_name"
                                        placeholder="Enter full name" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" id="edit_email"
                                        placeholder="Enter email" required>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Phone</label>
                                    <input type="text" class="form-control" name="phone" id="edit_phone"
                                        placeholder="Enter phone number">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Profile Picture</label>
                                    <input type="file" class="form-control" name="profile_picture" accept="image/*">
                                    <small class="text-muted">Max size: 2MB (jpg, jpeg, png, gif)</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Password</label>
                                    <input type="password" class="form-control" name="password" id="edit_password"
                                        placeholder="Enter new password">
                                    <small class="text-muted">Leave blank to keep current password</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation"
                                        id="edit_password_confirmation" placeholder="Confirm new password">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Role <span class="text-danger">*</span></label>
                                    <select class="form-control" name="role" id="edit_role" required>
                                        <option value="">Select Role</option>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" name="status" id="edit_status" required>
                                        <option value="">Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">User Management</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addUserModal">
                        <i class="fa fa-plus me-2"></i>Add New User
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="usersTable" class="display min-w850">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}"
                                                class="rounded-circle" width="35" height="35"
                                                style="object-fit: cover;">
                                        </td>
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $user->role === 'admin' ? 'danger' : 'primary' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusBadge = [
                                                    'active' => 'success',
                                                    'inactive' => 'danger',
                                                    'pending' => 'warning',
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $statusBadge[$user->status] }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button type="button"
                                                    class="btn btn-primary shadow btn-xs sharp me-1 edit-user"
                                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                    data-email="{{ $user->email }}" data-phone="{{ $user->phone }}"
                                                    data-role="{{ $user->role }}" data-status="{{ $user->status }}"
                                                    title="Edit User">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-warning shadow btn-xs sharp me-1 toggle-status"
                                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                    data-status="{{ $user->status }}" title="Toggle Status">
                                                    <i class="fa fa-power-off"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-danger shadow btn-xs sharp delete-user"
                                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                    title="Delete User">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Datatable -->
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Toastr configuration
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Initialize DataTable with Bootstrap styling
            var table = $('#usersTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search users...",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "zeroRecords": "No matching records found",
                    "emptyTable": "No data available in table",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "<i class='fa fa-angle-right'></i>",
                        "previous": "<i class='fa fa-angle-left'></i>"
                    }
                },
                "columnDefs": [{
                        "orderable": false,
                        "targets": [1, 8]
                    }, // Disable sorting for Photo and Action columns
                    {
                        "className": "text-center",
                        "targets": [0, 1, 5, 6, 8]
                    } // Center align specific columns
                ],
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                "drawCallback": function() {
                    // Custom pagination styling
                    $('.dataTables_paginate > .pagination').addClass(
                        'pagination-gutter pagination-primary');
                }
            });

            // Add User Form Submit
            $('#addUserForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                // Disable button and show loading
                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

                $.ajax({
                    url: "{{ route('admin.users.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#addUserModal').modal('hide');
                            $('#addUserForm')[0].reset();
                            $('.form-control').removeClass('is-invalid');

                            // SweetAlert Success
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $('.form-control').removeClass('is-invalid');
                            $.each(errors, function(key, value) {
                                $('[name="' + key + '"]').addClass('is-invalid')
                                    .siblings('.invalid-feedback').text(value[0]);
                            });
                            toastr.error('Please check the form for errors');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: xhr.responseJSON.message || 'An error occurred'
                            });
                        }
                    }
                });
            });

            // Edit User Button Click
            $(document).on('click', '.edit-user', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var email = $(this).data('email');
                var phone = $(this).data('phone');
                var role = $(this).data('role');
                var status = $(this).data('status');

                $('#edit_user_id').val(id);
                $('#edit_name').val(name);
                $('#edit_email').val(email);
                $('#edit_phone').val(phone);
                $('#edit_role').val(role);
                $('#edit_status').val(status);
                $('#edit_password').val('');
                $('#edit_password_confirmation').val('');

                $('.form-control').removeClass('is-invalid');
                $('#editUserModal').modal('show');
            });

            // Edit User Form Submit
            $('#editUserForm').on('submit', function(e) {
                e.preventDefault();

                var userId = $('#edit_user_id').val();
                var formData = new FormData(this);
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                // Disable button and show loading
                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: "{{ route('admin.users.update', ':id') }}".replace(':id', userId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#editUserModal').modal('hide');
                            $('#editUserForm')[0].reset();
                            $('.form-control').removeClass('is-invalid');

                            // SweetAlert Success
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $('.form-control').removeClass('is-invalid');
                            $.each(errors, function(key, value) {
                                $('#edit_' + key).addClass('is-invalid')
                                    .siblings('.invalid-feedback').text(value[0]);
                            });
                            toastr.error('Please check the form for errors');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: xhr.responseJSON.message || 'An error occurred'
                            });
                        }
                    }
                });
            });

            // Toggle Status Button Click with SweetAlert2
            $(document).on('click', '.toggle-status', function() {
                var userId = $(this).data('id');
                var userName = $(this).data('name');
                var currentStatus = $(this).data('status');
                var newStatus = currentStatus === 'active' ? 'inactive' : 'active';

                // Status badge colors
                var statusColors = {
                    'active': '#28a745',
                    'inactive': '#dc3545',
                    'pending': '#ffc107'
                };

                Swal.fire({
                    title: 'Change User Status?',
                    html: `Are you sure you want to change <strong>${userName}</strong>'s status to <span style="color: ${statusColors[newStatus]}">${newStatus.toUpperCase()}</span>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Processing...',
                            html: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('admin.users.toggle-status', ':id') }}".replace(
                                ':id', userId),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Status Changed!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed!',
                                    text: xhr.responseJSON.message ||
                                        'An error occurred'
                                });
                            }
                        });
                    }
                });
            });

            // Delete User Button Click with SweetAlert2
            $(document).on('click', '.delete-user', function() {
                var userId = $(this).data('id');
                var userName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to delete user <strong>${userName}</strong>.<br>This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Deleting...',
                            html: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('admin.users.destroy', ':id') }}".replace(':id',
                                userId),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed!',
                                    text: xhr.responseJSON.message ||
                                        'An error occurred'
                                });
                            }
                        });
                    }
                });
            });

            // Reset form when modal is closed
            $('#addUserModal, #editUserModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            });
        });
    </script>
@endpush
