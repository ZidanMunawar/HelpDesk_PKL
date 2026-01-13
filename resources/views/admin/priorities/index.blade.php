@extends('layouts.main')

@section('title', 'Priority Management | ' . config('app.name'))

@section('page-title', 'Priority Management')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Admin', 'url' => 'javascript:void(0)'],
            ['title' => 'Priority Management', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        /* Table Styling - Konsisten dengan Department & Category */
        .table {
            font-size: 14px;
            width: 100%;
            margin-bottom: 0;
        }

        .table thead th {
            font-size: 14px;
            font-weight: 600;
            padding: 15px 10px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #dee2e6 !important;
            text-align: center;
            vertical-align: middle;
        }

        .table thead th:first-child {
            width: 50px;
        }

        .table thead th:nth-child(3) {
            width: 120px;
        }

        .table thead th:nth-child(4) {
            width: 100px;
        }

        .table thead th:nth-child(6) {
            width: 100px;
        }

        .table thead th:nth-child(7) {
            width: 120px;
        }

        .table thead th:last-child {
            width: 150px;
        }

        .table tbody td {
            padding: 12px 10px;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
        }

        .table tbody td:first-child {
            text-align: center;
        }

        .table tbody td:nth-child(3) {
            text-align: center;
        }

        .table tbody td:nth-child(4) {
            text-align: center;
        }

        .table tbody td:nth-child(6) {
            text-align: center;
        }

        .table tbody td:nth-child(7) {
            text-align: center;
        }

        .table tbody td:last-child {
            text-align: center;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Badge Styling */
        .badge {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 0.375rem;
        }

        .badge-dark {
            background-color: #343a40;
            color: white;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        /* Color Preview Styling */
        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            border: 2px solid #dee2e6;
            display: inline-block;
            vertical-align: middle;
        }

        .color-picker-wrapper input[type="color"] {
            width: 60px;
            height: 40px;
            border: 2px solid #dee2e6;
            border-radius: 0.5rem;
            cursor: pointer;
            padding: 0;
        }

        /* Button Action Styling (Sama dengan Department & Category) */
        .btn-xs.sharp {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
        }

        .btn-xs.sharp i {
            font-size: 14px;
        }

        .d-flex .btn-xs.sharp {
            margin-right: 5px;
        }

        .d-flex .btn-xs.sharp:last-child {
            margin-right: 0;
        }

        /* Card Header Styling */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 1.5rem;
        }

        .card-header h4 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .card-header .btn {
            padding: 8px 16px;
            font-size: 14px;
        }

        /* Modal adjustments */
        .modal-footer .btn {
            padding: 8px 16px;
            font-size: 14px;
        }

        .modal-footer .btn i {
            font-size: 14px;
        }

        .form-label {
            font-weight: 500;
            color: #333;
        }

        .text-muted {
            color: #6c757d !important;
        }

        /* Form group styling */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group small.text-muted {
            display: block;
            margin-top: 5px;
            font-size: 12px;
        }

        /* Modal styling */
        .modal-content {
            border-radius: 0.75rem;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem 1.5rem;
        }

        .modal-header .modal-title {
            font-weight: 600;
            font-size: 1.125rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }

            .table thead th,
            .table tbody td {
                padding: 8px 5px;
                font-size: 13px;
            }

            .color-preview {
                width: 30px;
                height: 30px;
            }

            .badge {
                padding: 4px 8px;
                font-size: 11px;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .card-header .btn {
                align-self: flex-end;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Add Priority Modal -->
    <div class="modal fade" id="addPriorityModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Priority</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addPriorityForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Priority Name -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Priority Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name"
                                    placeholder="e.g., Low, Medium, High, Urgent" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Color and Level -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Color <span class="text-danger">*</span></label>
                                <div class="color-picker-wrapper">
                                    <input type="color" class="form-control" name="color" value="#6c757d" required>
                                </div>
                                <small class="text-muted">Badge color</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Level <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="level" min="1" max="10"
                                    value="1" required>
                                <small class="text-muted">1=Lowest, 10=Highest</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control" name="status" required>
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Priority
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Priority Modal -->
    <div class="modal fade" id="editPriorityModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Priority</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editPriorityForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_priority_id">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Priority Name -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Priority Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Color and Level -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Color <span class="text-danger">*</span></label>
                                <div class="color-picker-wrapper">
                                    <input type="color" class="form-control" id="edit_color" name="color" required>
                                </div>
                                <small class="text-muted">Badge color</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Level <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_level" name="level" min="1"
                                    max="10" required>
                                <small class="text-muted">1=Lowest, 10=Highest</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Priority
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Priorities Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Priority List</h4>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addPriorityModal">
                        <i class="fas fa-plus me-1"></i> Add New Priority
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Priority Name</th>
                                    <th>Color</th>
                                    <th>Level</th>
                                    <th>Preview</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($priorities as $index => $priority)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $priority->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="color-preview"
                                                style="background-color: {{ $priority->color }}"></span>
                                            <span class="ms-2">{{ $priority->color }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dark">Level {{ $priority->level }}</span>
                                        </td>
                                        <td>
                                            <span class="badge"
                                                style="background-color: {{ $priority->color }}; color: {{ \Illuminate\Support\Str::is('light*', $priority->color) ? '#000' : '#fff' }}">
                                                {{ $priority->name }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusBadge = [
                                                    'active' => 'success',
                                                    'inactive' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $statusBadge[$priority->status] }}">
                                                {{ ucfirst($priority->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $priority->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <button type="button"
                                                    class="btn btn-primary btn-sm shadow btn-xs sharp me-1 edit-priority"
                                                    data-id="{{ $priority->id }}" data-name="{{ $priority->name }}"
                                                    data-color="{{ $priority->color }}"
                                                    data-level="{{ $priority->level }}"
                                                    data-status="{{ $priority->status }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>

                                                <button type="button"
                                                    class="btn btn-{{ $priority->status === 'active' ? 'warning' : 'success' }} btn-sm shadow btn-xs sharp me-1 toggle-status"
                                                    data-id="{{ $priority->id }}" data-name="{{ $priority->name }}"
                                                    data-status="{{ $priority->status }}">
                                                    <i
                                                        class="fas fa-{{ $priority->status === 'active' ? 'ban' : 'check' }}"></i>
                                                </button>

                                                <button type="button"
                                                    class="btn btn-danger btn-sm shadow btn-xs sharp delete-priority"
                                                    data-id="{{ $priority->id }}" data-name="{{ $priority->name }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No priorities found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
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

            // Add Priority Form Submit
            $('#addPriorityForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

                $.ajax({
                    url: "{{ route('admin.priorities.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#addPriorityModal').modal('hide');
                            $('#addPriorityForm')[0].reset();
                            $('.form-control').removeClass('is-invalid');

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

            // Edit Priority Button Click
            $(document).on('click', '.edit-priority', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var color = $(this).data('color');
                var level = $(this).data('level');
                var status = $(this).data('status');

                $('#edit_priority_id').val(id);
                $('#edit_name').val(name);
                $('#edit_color').val(color);
                $('#edit_level').val(level);
                $('#edit_status').val(status);

                $('.form-control').removeClass('is-invalid');
                $('#editPriorityModal').modal('show');
            });

            // Edit Priority Form Submit
            $('#editPriorityForm').on('submit', function(e) {
                e.preventDefault();

                var priorityId = $('#edit_priority_id').val();
                var formData = new FormData(this);
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: "{{ route('admin.priorities.update', ':id') }}".replace(':id',
                        priorityId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#editPriorityModal').modal('hide');
                            $('#editPriorityForm')[0].reset();
                            $('.form-control').removeClass('is-invalid');

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

            // Toggle Status
            $(document).on('click', '.toggle-status', function() {
                var priorityId = $(this).data('id');
                var priorityName = $(this).data('name');
                var currentStatus = $(this).data('status');
                var newStatus = currentStatus === 'active' ? 'inactive' : 'active';

                var statusColors = {
                    'active': '#28a745',
                    'inactive': '#dc3545'
                };

                Swal.fire({
                    title: 'Change Priority Status?',
                    html: `Are you sure you want to change <strong>${priorityName}</strong>'s status to <span style="color: ${statusColors[newStatus]}">${newStatus.toUpperCase()}</span>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            html: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('admin.priorities.toggle-status', ':id') }}"
                                .replace(':id', priorityId),
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

            // Delete Priority
            $(document).on('click', '.delete-priority', function() {
                var priorityId = $(this).data('id');
                var priorityName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to delete <strong>${priorityName}</strong> priority.<br>This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Deleting...',
                            html: 'Please wait',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('admin.priorities.destroy', ':id') }}".replace(
                                ':id',
                                priorityId),
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
            $('#addPriorityModal, #editPriorityModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            });
        });
    </script>
@endpush
