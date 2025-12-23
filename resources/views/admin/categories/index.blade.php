{{-- resources/views/admin/categories/index.blade.php --}}

@extends('layouts.main')

@section('title', 'Categories Management | ' . config('app.name'))

@section('page-title', 'Categories Management')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Admin', 'url' => route('admin.dashboard')],
            ['title' => 'Master Data', 'url' => 'javascript:void(0)'],
            ['title' => 'Categories', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

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
                            <input type="text" class="form-control" name="name" id="add_name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" id="add_code" required maxlength="10"
                                style="text-transform: uppercase;">
                            <small class="text-muted">Max 10 characters (e.g., HW, SW, NW)</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Description</label>
                            <textarea class="form-control" name="description" id="add_description" rows="3"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" id="add_status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>Save Category
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
                    <input type="hidden" id="edit_category_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" id="edit_code" required maxlength="10"
                                style="text-transform: uppercase;">
                            <small class="text-muted">Max 10 characters (e.g., HW, SW, NW)</small>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" id="edit_status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-2"></i>Update Category
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
                    <h4 class="card-title">Categories List</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addCategoryModal">
                        <i class="fa fa-plus me-2"></i>Add New Category
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md" id="categoriesTable">
                            <thead>
                                <tr>
                                    <th style="width:50px;">
                                        <div class="form-check custom-checkbox checkbox-success check-lg me-3">
                                            <input type="checkbox" class="form-check-input" id="checkAll">
                                            <label class="form-check-label" for="checkAll"></label>
                                        </div>
                                    </th>
                                    <th><strong>CODE</strong></th>
                                    <th><strong>NAME</strong></th>
                                    <th><strong>DESCRIPTION</strong></th>
                                    <th><strong>TICKETS</strong></th>
                                    <th><strong>STATUS</strong></th>
                                    <th><strong>CREATED</strong></th>
                                    <th><strong>ACTION</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>
                                            <div class="form-check custom-checkbox checkbox-success check-lg me-3">
                                                <input type="checkbox" class="form-check-input"
                                                    id="customCheckBox{{ $category->id }}">
                                                <label class="form-check-label"
                                                    for="customCheckBox{{ $category->id }}"></label>
                                            </div>
                                        </td>
                                        <td><strong class="text-primary">{{ $category->code }}</strong></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-title rounded-circle bg-primary">
                                                        {{ substr($category->code, 0, 2) }}
                                                    </span>
                                                </div>
                                                <span class="w-space-no">{{ $category->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($category->description, 50) ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-rounded badge-info">
                                                {{ $category->tickets_count }} tickets
                                            </span>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle" type="checkbox"
                                                    data-id="{{ $category->id }}"
                                                    {{ $category->status === 'active' ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>{{ $category->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button type="button"
                                                    class="btn btn-primary shadow btn-xs sharp me-1 btn-edit"
                                                    data-id="{{ $category->id }}">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-danger shadow btn-xs sharp btn-delete"
                                                    data-id="{{ $category->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No categories found</p>
                                        </td>
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

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/toastr/css/toastr.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('vendor/toastr/js/toastr.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Toastr configuration
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };

            // Auto-generate code from name
            $('#add_name').on('input', function() {
                let name = $(this).val();
                let code = name.split(' ').map(word => word.charAt(0)).join('').toUpperCase().substring(0,
                    10);
                $('#add_code').val(code);
            });

            // Add Category Form Submit
            $('#addCategoryForm').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: '{{ route('admin.categories.store') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#addCategoryModal').modal('hide');
                            $('#addCategoryForm')[0].reset();
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $(`#add_${key}`).addClass('is-invalid')
                                    .siblings('.invalid-feedback').text(value[0]);
                            });
                        } else {
                            toastr.error('Failed to create category');
                        }
                    }
                });
            });

            // Edit Button Click
            $('.btn-edit').on('click', function() {
                let categoryId = $(this).data('id');

                $.ajax({
                    url: `/admin/categories/${categoryId}`,
                    type: 'GET',
                    success: function(category) {
                        $('#edit_category_id').val(category.id);
                        $('#edit_name').val(category.name);
                        $('#edit_code').val(category.code);
                        $('#edit_description').val(category.description);
                        $('#edit_status').val(category.status);
                        $('#editCategoryModal').modal('show');
                    },
                    error: function() {
                        toastr.error('Failed to load category data');
                    }
                });
            });

            // Edit Category Form Submit
            $('#editCategoryForm').on('submit', function(e) {
                e.preventDefault();

                let categoryId = $('#edit_category_id').val();
                let formData = $(this).serialize();

                $.ajax({
                    url: `/admin/categories/${categoryId}`,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#editCategoryModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $(`#edit_${key}`).addClass('is-invalid')
                                    .siblings('.invalid-feedback').text(value[0]);
                            });
                        } else {
                            toastr.error('Failed to update category');
                        }
                    }
                });
            });

            // Delete Button Click
            $('.btn-delete').on('click', function() {
                let categoryId = $(this).data('id');

                if (confirm('Are you sure you want to delete this category?')) {
                    $.ajax({
                        url: `/admin/categories/${categoryId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                toastr.warning(xhr.responseJSON.message);
                            } else {
                                toastr.error('Failed to delete category');
                            }
                        }
                    });
                }
            });

            // Toggle Status
            $('.status-toggle').on('change', function() {
                let categoryId = $(this).data('id');

                $.ajax({
                    url: `/admin/categories/${categoryId}/toggle-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Failed to update status');
                        location.reload();
                    }
                });
            });

            // Check All
            $('#checkAll').on('change', function() {
                $('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
            });

            // Clear validation on modal close
            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('.is-invalid').removeClass('is-invalid');
                $(this).find('.invalid-feedback').text('');
            });
        });
    </script>
@endpush
