@extends('layouts.main')

@section('title', 'Category Management | ' . config('app.name'))

@section('page-title', 'Category Management')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Admin', 'url' => 'javascript:void(0)'],
            ['title' => 'Category Management', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        /* Custom DataTable Styling - Konsisten dengan Department & Location */
        #categoriesTable_wrapper .dataTables_length,
        #categoriesTable_wrapper .dataTables_filter,
        #categoriesTable_wrapper .dataTables_info,
        #categoriesTable_wrapper .dataTables_paginate {
            padding: 20px 0;
            font-size: 14px;
        }

        /* Length Menu Styling */
        #categoriesTable_wrapper .dataTables_length select {
            padding: 8px 35px 8px 15px;
            font-size: 14px;
            border: 1px solid #f0f1f5;
            border-radius: 0.75rem;
            background-color: #fff;
            margin: 0 10px;
        }

        #categoriesTable_wrapper .dataTables_length label {
            font-weight: 500;
            color: #6e6e6e;
        }

        /* Search Input Styling */
        #categoriesTable_wrapper .dataTables_filter input {
            padding: 10px 20px;
            font-size: 14px;
            border: 1px solid #f0f1f5;
            border-radius: 0.75rem;
            width: 250px;
            margin-left: 10px;
        }

        #categoriesTable_wrapper .dataTables_filter label {
            font-weight: 500;
            color: #6e6e6e;
        }

        /* Info Text Styling */
        #categoriesTable_wrapper .dataTables_info {
            font-size: 14px;
            font-weight: 500;
            color: #6e6e6e;
        }

        /* Pagination Styling */
        #categoriesTable_wrapper .dataTables_paginate {
            float: right;
        }

        #categoriesTable_wrapper .dataTables_paginate .pagination {
            margin: 0;
        }

        #categoriesTable_wrapper .dataTables_paginate .paginate_button {
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

        #categoriesTable_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary);
            color: white !important;
            border-color: var(--primary);
        }

        #categoriesTable_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary);
            color: white !important;
            border-color: var(--primary);
        }

        #categoriesTable_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8f9fa;
        }

        #categoriesTable_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #f8f9fa;
            color: #6e6e6e !important;
            border-color: #f0f1f5;
        }

        #categoriesTable_wrapper .dataTables_paginate .paginate_button.previous,
        #categoriesTable_wrapper .dataTables_paginate .paginate_button.next {
            padding: 8px 12px;
        }

        #categoriesTable_wrapper .dataTables_paginate .paginate_button i {
            font-size: 16px;
        }

        /* Table Styling */
        #categoriesTable {
            font-size: 14px;
            width: 100% !important;
        }

        #categoriesTable thead th {
            font-size: 14px;
            font-weight: 600;
            padding: 15px 10px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #dee2e6 !important;
            text-align: center;
            vertical-align: middle;
        }

        #categoriesTable thead th:first-child {
            width: 50px;
        }

        #categoriesTable thead th:nth-child(4) {
            width: 100px;
        }

        #categoriesTable thead th:nth-child(5) {
            width: 120px;
        }

        #categoriesTable thead th:nth-child(6) {
            width: 100px;
        }

        #categoriesTable thead th:nth-child(7) {
            width: 100px;
        }

        #categoriesTable thead th:last-child {
            width: 150px;
        }

        #categoriesTable tbody td {
            padding: 12px 10px;
            vertical-align: middle;
        }

        #categoriesTable tbody td:first-child {
            text-align: center;
        }

        #categoriesTable tbody td:nth-child(4) {
            text-align: center;
        }

        #categoriesTable tbody td:nth-child(6) {
            text-align: center;
        }

        #categoriesTable tbody td:nth-child(7) {
            text-align: center;
        }

        #categoriesTable tbody td:last-child {
            text-align: center;
        }

        #categoriesTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Badge Styling */
        .badge {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 0.375rem;
        }

        .badge-info {
            background-color: #17a2b8;
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

        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }

        /* Button Action Styling */
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #categoriesTable_wrapper .dataTables_filter input {
                width: 100%;
                margin-top: 10px;
            }

            #categoriesTable_wrapper .dataTables_length,
            #categoriesTable_wrapper .dataTables_filter {
                text-align: left;
            }

            #categoriesTable_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                margin-top: 15px;
            }

            #categoriesTable thead th,
            #categoriesTable tbody td {
                padding: 8px 5px;
            }
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
    </style>
@endpush

@section('content')
    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCategoryForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Category Name -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name"
                                    placeholder="e.g., Engineering, Housekeeping, IT Support" required>
                                <small class="text-muted">Nama kategori untuk pengelompokan ticket</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Department -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-control" name="department_id">
                                    <option value="">-- Select Department (Optional) --</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Department yang terkait dengan kategori ini</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Description -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"
                                    placeholder="e.g., AC, electrical, plumbing, technical issues"></textarea>
                                <small class="text-muted">Deskripsi jenis masalah yang termasuk kategori ini</small>
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
                            <i class="fas fa-save me-1"></i> Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCategoryForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_category_id">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Category Name -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Department -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-control" id="edit_department_id" name="department_id">
                                    <option value="">-- Select Department (Optional) --</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Description -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
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
                            <i class="fas fa-save me-1"></i> Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Category List</h4>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus me-1"></i> Add New Category
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="categoriesTable" class="display table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Category Name</th>
                                    <th>Department</th>
                                    <th>Total Tickets</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $index => $category)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                        </td>
                                        <td>
                                            @if ($category->department)
                                                <span
                                                    class="badge badge-secondary">{{ $category->department->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $category->tickets_count ?? 0 }}</span>
                                        </td>
                                        <td>{{ Str::limit($category->description, 50) ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusBadge = [
                                                    'active' => 'success',
                                                    'inactive' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $statusBadge[$category->status] }}">
                                                {{ ucfirst($category->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $category->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <button type="button"
                                                    class="btn btn-primary btn-sm shadow btn-xs sharp me-1 edit-category"
                                                    data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                                    data-description="{{ $category->description }}"
                                                    data-department_id="{{ $category->department_id }}"
                                                    data-status="{{ $category->status }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>

                                                <button type="button"
                                                    class="btn btn-{{ $category->status === 'active' ? 'warning' : 'success' }} btn-sm shadow btn-xs sharp me-1 toggle-status"
                                                    data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                                    data-status="{{ $category->status }}">
                                                    <i
                                                        class="fas fa-{{ $category->status === 'active' ? 'ban' : 'check' }}"></i>
                                                </button>

                                                <button type="button"
                                                    class="btn btn-danger btn-sm shadow btn-xs sharp delete-category"
                                                    data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No categories found</td>
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
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
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

            // Initialize DataTable
            var table = $('#categoriesTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search categories...",
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
                        "targets": [7]
                    },
                    {
                        "className": "text-center",
                        "targets": [0, 3, 5, 6, 7]
                    }
                ],
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                "drawCallback": function() {
                    $('.dataTables_paginate > .pagination').addClass(
                        'pagination-gutter pagination-primary');
                }
            });

            // Add Category Form Submit
            $('#addCategoryForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

                $.ajax({
                    url: "{{ route('admin.categories.store') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#addCategoryModal').modal('hide');
                            $('#addCategoryForm')[0].reset();
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

            // Edit Category Button Click
            $(document).on('click', '.edit-category', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var description = $(this).data('description');
                var department_id = $(this).data('department_id');
                var status = $(this).data('status');

                $('#edit_category_id').val(id);
                $('#edit_name').val(name);
                $('#edit_description').val(description);
                $('#edit_department_id').val(department_id);
                $('#edit_status').val(status);

                $('.form-control').removeClass('is-invalid');
                $('#editCategoryModal').modal('show');
            });

            // Edit Category Form Submit
            $('#editCategoryForm').on('submit', function(e) {
                e.preventDefault();

                var categoryId = $('#edit_category_id').val();
                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: "{{ route('admin.categories.update', ':id') }}".replace(':id',
                        categoryId),
                    type: 'PUT',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#editCategoryModal').modal('hide');
                            $('#editCategoryForm')[0].reset();
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
                var categoryId = $(this).data('id');
                var categoryName = $(this).data('name');
                var currentStatus = $(this).data('status');
                var newStatus = currentStatus === 'active' ? 'inactive' : 'active';

                var statusColors = {
                    'active': '#28a745',
                    'inactive': '#dc3545'
                };

                Swal.fire({
                    title: 'Change Category Status?',
                    html: `Are you sure you want to change <strong>${categoryName}</strong>'s status to <span style="color: ${statusColors[newStatus]}">${newStatus.toUpperCase()}</span>?`,
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
                            url: "{{ route('admin.categories.toggle-status', ':id') }}"
                                .replace(':id', categoryId),
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

            // Delete Category
            $(document).on('click', '.delete-category', function() {
                var categoryId = $(this).data('id');
                var categoryName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to delete <strong>${categoryName}</strong> category.<br>This action cannot be undone!`,
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
                            url: "{{ route('admin.categories.destroy', ':id') }}".replace(
                                ':id',
                                categoryId),
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
            $('#addCategoryModal, #editCategoryModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            });
        });
    </script>
@endpush
