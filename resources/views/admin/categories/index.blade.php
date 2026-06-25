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

        .form-control,
        .form-select,
        textarea.form-control {
            color: #1e1e1e;
            font-weight: 400;
        }

        /* Saat typing/input juga tetep hitam */
        .form-control:focus,
        .form-select:focus,
        textarea.form-control:focus {
            color: #000000 !important;
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

        #categoriesTable thead th:nth-child(3) {
            width: 100px;
        }

        #categoriesTable thead th:nth-child(4) {
            width: 120px;
        }

        #categoriesTable thead th:nth-child(5) {
            width: 100px;
        }

        #categoriesTable thead th:nth-child(6) {
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

        #categoriesTable tbody td:nth-child(3) {
            text-align: center;
        }

        #categoriesTable tbody td:nth-child(5) {
            text-align: center;
        }

        #categoriesTable tbody td:nth-child(6) {
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

        /* Error styling */
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
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
                <form id="addCategoryForm" novalidate>
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
                <form id="editCategoryForm" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_category_id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Category Name -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
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
                    @if (auth()->user()->role === 'superadmin')
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus me-1"></i> Add New Category
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="categoriesTable" class="display table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Category Name</th>
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
                                            <span class="badge badge-info">{{ $category->tickets_count ?? 0 }}</span>
                                        </td>
                                        <td>{{ $category->description ? Str::limit($category->description, 50) : '-' }}
                                        </td>
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
                                        <td>{{ $category->created_at ? $category->created_at->format('d M Y') : '-' }}</td>
                                        <td>
                                            @if (auth()->user()->role === 'superadmin')
                                                <div class="d-flex justify-content-center">
                                                    <button type="button"
                                                        class="btn btn-primary btn-sm shadow btn-xs sharp me-1 edit-category"
                                                        data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                                        data-description="{{ $category->description }}"
                                                        data-status="{{ $category->status }}" title="Edit">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-{{ $category->status === 'active' ? 'warning' : 'success' }} btn-sm shadow btn-xs sharp me-1 toggle-status"
                                                        data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                                        data-status="{{ $category->status }}"
                                                        title="{{ $category->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                        <i
                                                            class="fas fa-{{ $category->status === 'active' ? 'ban' : 'check' }}"></i>
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-danger btn-sm shadow btn-xs sharp delete-category"
                                                        data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-muted">No Actions</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    {{-- TIDAK ADA TR KOSONG DI SINI --}}
                                @endforelse
                            </tbody>
                            <tfoot style="display: none;">
                                <tr>
                                    <td colspan="7" class="text-center">No categories found</td>
                                </tr>
                            </tfoot>
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

            // Initialize DataTable dengan setting yang benar
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
                        "targets": [6] // Index action column
                    },
                    {
                        "className": "text-center",
                        "targets": [0, 2, 4, 5, 6] // Update indeks kolom
                    }
                ],
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                "drawCallback": function(settings) {
                    // Handle empty table
                    var api = this.api();
                    if (api.rows().count() === 0) {
                        $(this).find('tbody').html(
                            '<tr><td colspan="7" class="text-center">No categories found</td></tr>'
                        );
                    }
                    $('.dataTables_paginate > .pagination').addClass(
                        'pagination-gutter pagination-primary');
                },
                "initComplete": function(settings, json) {
                    // Pastikan tbody ada
                    if (this.api().rows().count() === 0) {
                        $('#categoriesTable tbody').html(
                            '<tr><td colspan="7" class="text-center">No categories found</td></tr>'
                        );
                    }
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
                        submitBtn.prop('disabled', false).html(originalText);

                        if (response.success) {
                            $('#addCategoryModal').modal('hide');
                            $('#addCategoryForm')[0].reset();
                            $('.form-control').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $('.form-control').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            $.each(errors, function(key, value) {
                                var input = $('[name="' + key + '"]',
                                    '#addCategoryForm');
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(value[0]);
                            });

                            toastr.error('Please check the form for errors');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message ||
                                    'An error occurred. Please try again.'
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
                var status = $(this).data('status');

                $('#edit_category_id').val(id);
                $('#edit_name').val(name);
                $('#edit_description').val(description);
                $('#edit_status').val(status);

                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
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
                        submitBtn.prop('disabled', false).html(originalText);

                        if (response.success) {
                            $('#editCategoryModal').modal('hide');
                            $('#editCategoryForm')[0].reset();
                            $('.form-control').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $('.form-control').removeClass('is-invalid');
                            $('.invalid-feedback').text('');

                            $.each(errors, function(key, value) {
                                var input = $('#edit_' + key);
                                if (input.length === 0) {
                                    input = $('[name="' + key + '"]',
                                        '#editCategoryForm');
                                }
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(value[0]);
                            });

                            toastr.error('Please check the form for errors');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message ||
                                    'An error occurred. Please try again.'
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

                var statusText = {
                    'active': 'Active',
                    'inactive': 'Inactive'
                };

                Swal.fire({
                    title: 'Change Category Status?',
                    html: `Are you sure you want to change <strong>${categoryName}</strong>'s status from <span style="color: ${statusColors[currentStatus]}">${statusText[currentStatus]}</span> to <span style="color: ${statusColors[newStatus]}">${statusText[newStatus]}</span>?`,
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
                                        title: 'Success!',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message ||
                                        'An error occurred. Please try again.'
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
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Cannot Delete',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message ||
                                        'An error occurred. Please try again.'
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

            // Form validation on input
            $('.form-control').on('input', function() {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').text('');
            });
        });
    </script>
@endpush
