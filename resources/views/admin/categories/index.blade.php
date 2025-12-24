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
        /* DataTable Wrapper Styling */
        #categoriesTable_wrapper .dataTables_length,
        #categoriesTable_wrapper .dataTables_filter,
        #categoriesTable_wrapper .dataTables_info,
        #categoriesTable_wrapper .dataTables_paginate {
            padding: 20px 0;
            font-size: 14px;
        }

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

        #categoriesTable_wrapper .dataTables_info {
            font-size: 14px;
            font-weight: 500;
            color: #6e6e6e;
        }

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

        #categoriesTable {
            font-size: 14px;
        }

        #categoriesTable thead th {
            font-size: 14px;
            font-weight: 600;
            padding: 15px 10px;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        #categoriesTable tbody td {
            padding: 12px 10px;
            vertical-align: middle;
        }

        #categoriesTable tbody tr:hover {
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
        }
    </style>
@endpush


@section('content')
    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCategoryForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name"
                                placeholder="e.g., IT Support, Maintenance" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Category Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" placeholder="e.g., IT, MNT" required>
                            <small class="text-muted">Unique code for category</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Optional description"></textarea>
                            <div class="invalid-feedback"></div>
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

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCategoryForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="category_id" id="edit_category_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Category Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" id="edit_code" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                            <div class="invalid-feedback"></div>
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

    <!-- Categories Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Category Management</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addCategoryModal">
                        <i class="fa fa-plus me-2"></i>Add Category
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="categoriesTable" class="display min-w850">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Code</th>
                                    <th>Category Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $index => $category)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><span class="badge badge-primary">{{ $category->code }}</span></td>
                                        <td><strong>{{ $category->name }}</strong></td>
                                        <td>{{ $category->description ?? '-' }}</td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $category->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($category->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $category->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button type="button"
                                                    class="btn btn-primary shadow btn-xs sharp me-1 edit-category"
                                                    data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                                    data-code="{{ $category->code }}"
                                                    data-description="{{ $category->description }}"
                                                    data-status="{{ $category->status }}" title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-warning shadow btn-xs sharp me-1 toggle-status"
                                                    data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                                    data-status="{{ $category->status }}" title="Toggle Status">
                                                    <i class="fa fa-power-off"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-danger shadow btn-xs sharp delete-category"
                                                    data-id="{{ $category->id }}" data-name="{{ $category->name }}"
                                                    title="Delete">
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
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
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

            var table = $('#categoriesTable').DataTable({
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search categories...",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ categories",
                    "infoEmpty": "Showing 0 to 0 of 0 categories",
                    "infoFiltered": "(filtered from _MAX_ total categories)",
                    "paginate": {
                        "next": "<i class='fa fa-angle-right'></i>",
                        "previous": "<i class='fa fa-angle-left'></i>"
                    }
                },
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                "drawCallback": function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-gutter');
                }
            });

            // Add Category
            $('#addCategoryForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

                $.ajax({
                    url: "{{ route('admin.categories.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#addCategoryModal').modal('hide');
                        $('#addCategoryForm')[0].reset();
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
                        }
                    }
                });
            });

            // Edit Category
            $(document).on('click', '.edit-category', function() {
                $('#edit_category_id').val($(this).data('id'));
                $('#edit_name').val($(this).data('name'));
                $('#edit_code').val($(this).data('code'));
                $('#edit_description').val($(this).data('description'));
                $('#edit_status').val($(this).data('status'));
                $('#editCategoryModal').modal('show');
            });

            $('#editCategoryForm').on('submit', function(e) {
                e.preventDefault();
                var categoryId = $('#edit_category_id').val();
                var formData = new FormData(this);
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: "{{ route('admin.categories.update', ':id') }}".replace(':id',
                        categoryId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#editCategoryModal').modal('hide');
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
                    }
                });
            });

            // Toggle Status
            $(document).on('click', '.toggle-status', function() {
                var categoryId = $(this).data('id');
                var categoryName = $(this).data('name');
                var currentStatus = $(this).data('status');
                var newStatus = currentStatus === 'active' ? 'inactive' : 'active';

                Swal.fire({
                    title: 'Change Status?',
                    html: `Change <strong>${categoryName}</strong> to <span style="color: ${newStatus === 'active' ? '#28a745' : '#dc3545'}">${newStatus.toUpperCase()}</span>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.categories.toggle-status', ':id') }}"
                                .replace(':id', categoryId),
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

            // Delete Category
            $(document).on('click', '.delete-category', function() {
                var categoryId = $(this).data('id');
                var categoryName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    html: `Delete category <strong>${categoryName}</strong>?<br>This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.categories.destroy', ':id') }}".replace(
                                ':id', categoryId),
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
        });
    </script>
@endpush
