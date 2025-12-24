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
        }

        .table thead th {
            font-size: 14px;
            font-weight: 600;
            padding: 15px 10px;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody td {
            padding: 12px 10px;
            vertical-align: middle;
            font-size: 14px;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
        }

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
    </style>
@endpush

@section('content')
    <!-- Add Priority Modal -->
    <div class="modal fade" id="addPriorityModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Priority</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addPriorityForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Priority Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name"
                                placeholder="e.g., Low, Medium, High, Urgent" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Color <span class="text-danger">*</span></label>
                                    <div class="color-picker-wrapper">
                                        <input type="color" class="form-control" name="color" value="#6c757d" required>
                                    </div>
                                    <small class="text-muted">Badge color</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Level <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="level" min="1" max="10"
                                        value="1" required>
                                    <small class="text-muted">1=Lowest, 10=Highest</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i>Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Priority Modal -->
    <div class="modal fade" id="editPriorityModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Priority</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editPriorityForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="priority_id" id="edit_priority_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Priority Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Color <span class="text-danger">*</span></label>
                                    <div class="color-picker-wrapper">
                                        <input type="color" class="form-control" name="color" id="edit_color" required>
                                    </div>
                                    <small class="text-muted">Badge color</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="text-black font-w500">Level <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="level" id="edit_level"
                                        min="1" max="10" required>
                                    <small class="text-muted">1=Lowest, 10=Highest</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" id="edit_status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i>Update
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
                <div class="card-header">
                    <h4 class="card-title">Priority Management</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addPriorityModal">
                        <i class="fa fa-plus me-2"></i>Add Priority
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th style="width:80px;"><strong>#</strong></th>
                                    <th><strong>Priority Name</strong></th>
                                    <th><strong>Color</strong></th>
                                    <th><strong>Level</strong></th>
                                    <th><strong>Preview</strong></th>
                                    <th><strong>Status</strong></th>
                                    <th><strong>Created</strong></th>
                                    <th><strong>Action</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($priorities as $index => $priority)
                                    <tr>
                                        <td><strong>{{ $index + 1 }}</strong></td>
                                        <td>{{ $priority->name }}</td>
                                        <td>
                                            <span class="color-preview"
                                                style="background-color: {{ $priority->color }}"></span>
                                            <span class="ms-2">{{ $priority->color }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-dark">Level {{ $priority->level }}</span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $priority->color }}">
                                                {{ $priority->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $priority->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($priority->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $priority->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button type="button"
                                                    class="btn btn-primary shadow btn-xs sharp me-1 edit-priority"
                                                    data-id="{{ $priority->id }}" data-name="{{ $priority->name }}"
                                                    data-color="{{ $priority->color }}"
                                                    data-level="{{ $priority->level }}"
                                                    data-status="{{ $priority->status }}" title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-warning shadow btn-xs sharp me-1 toggle-status"
                                                    data-id="{{ $priority->id }}" data-name="{{ $priority->name }}"
                                                    data-status="{{ $priority->status }}" title="Toggle Status">
                                                    <i class="fa fa-power-off"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-danger shadow btn-xs sharp delete-priority"
                                                    data-id="{{ $priority->id }}" data-name="{{ $priority->name }}"
                                                    title="Delete">
                                                    <i class="fa fa-trash"></i>
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
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "5000",
                "closeButton": true,
                "progressBar": true
            };

            // Add Priority
            $('#addPriorityForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                '<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

                $.ajax({
                    url: "{{ route('admin.priorities.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#addPriorityModal').modal('hide');
                        $('#addPriorityForm')[0].reset();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(
                            '<i class="fa fa-save me-1"></i>Save');
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('[name="' + key + '"]').addClass('is-invalid')
                                    .siblings('.invalid-feedback').text(value[0]);
                            });
                            toastr.error('Please check the form for errors');
                        }
                    }
                });
            });

            // Edit Priority
            $(document).on('click', '.edit-priority', function() {
                $('#edit_priority_id').val($(this).data('id'));
                $('#edit_name').val($(this).data('name'));
                $('#edit_color').val($(this).data('color'));
                $('#edit_level').val($(this).data('level'));
                $('#edit_status').val($(this).data('status'));
                $('.form-control').removeClass('is-invalid');
                $('#editPriorityModal').modal('show');
            });

            $('#editPriorityForm').on('submit', function(e) {
                e.preventDefault();
                var priorityId = $('#edit_priority_id').val();
                var formData = new FormData(this);
                var submitBtn = $(this).find('button[type="submit"]');
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
                        $('#editPriorityModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(
                            '<i class="fa fa-save me-1"></i>Update');
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                $('#edit_' + key).addClass('is-invalid')
                                    .siblings('.invalid-feedback').text(value[0]);
                            });
                            toastr.error('Please check the form for errors');
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

                Swal.fire({
                    title: 'Change Status?',
                    html: `Change <strong>${priorityName}</strong> to <span style="color: ${newStatus === 'active' ? '#28a745' : '#dc3545'}">${newStatus.toUpperCase()}</span>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.priorities.toggle-status', ':id') }}"
                                .replace(':id', priorityId),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Changed!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => location.reload());
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
                    html: `Delete priority <strong>${priorityName}</strong>?<br>This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.priorities.destroy', ':id') }}".replace(
                                ':id', priorityId),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            }
                        });
                    }
                });
            });

            // Reset form when modal closed
            $('#addPriorityModal, #editPriorityModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('.form-control').removeClass('is-invalid');
            });
        });
    </script>
@endpush
