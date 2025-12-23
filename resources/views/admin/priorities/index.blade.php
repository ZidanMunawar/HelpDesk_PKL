{{-- resources/views/admin/priorities/index.blade.php --}}

@extends('layouts.main')

@section('title', 'Priorities Management | ' . config('app.name'))

@section('page-title', 'Priorities Management')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Admin', 'url' => route('admin.dashboard')],
            ['title' => 'Master Data', 'url' => 'javascript:void(0)'],
            ['title' => 'Priorities', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

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
                            <input type="text" class="form-control" name="name" id="add_name" required
                                placeholder="e.g., Critical, High, Medium, Low">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Priority Level <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="level" id="add_level" required min="1"
                                placeholder="1 = Highest priority">
                            <small class="text-muted">Lower number = Higher priority (1 is the highest)</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Color <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" name="color" id="add_color"
                                    value="#dc3545" required style="width: 60px;">
                                <input type="text" class="form-control" id="add_color_hex" value="#dc3545" readonly>
                            </div>
                            <small class="text-muted">Choose a color for this priority level</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Quick Color Selection</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm color-preset" data-color="#dc3545"
                                    style="background-color: #dc3545; width: 40px; height: 40px; border-radius: 5px;"
                                    title="Critical - Red"></button>
                                <button type="button" class="btn btn-sm color-preset" data-color="#fd7e14"
                                    style="background-color: #fd7e14; width: 40px; height: 40px; border-radius: 5px;"
                                    title="High - Orange"></button>
                                <button type="button" class="btn btn-sm color-preset" data-color="#ffc107"
                                    style="background-color: #ffc107; width: 40px; height: 40px; border-radius: 5px;"
                                    title="Medium - Yellow"></button>
                                <button type="button" class="btn btn-sm color-preset" data-color="#0dcaf0"
                                    style="background-color: #0dcaf0; width: 40px; height: 40px; border-radius: 5px;"
                                    title="Low - Cyan"></button>
                                <button type="button" class="btn btn-sm color-preset" data-color="#6c757d"
                                    style="background-color: #6c757d; width: 40px; height: 40px; border-radius: 5px;"
                                    title="Very Low - Gray"></button>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Preview</label>
                            <div class="p-3 border rounded">
                                <span class="badge" id="add_preview_badge"
                                    style="background-color: #dc3545; font-size: 14px;">
                                    Sample Priority
                                </span>
                            </div>
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
                            <i class="fa fa-save me-2"></i>Save Priority
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
                    <input type="hidden" id="edit_priority_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Priority Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Priority Level <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="level" id="edit_level" required
                                min="1">
                            <small class="text-muted">Lower number = Higher priority (1 is the highest)</small>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Color <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" name="color"
                                    id="edit_color" required style="width: 60px;">
                                <input type="text" class="form-control" id="edit_color_hex" readonly>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Quick Color Selection</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm color-preset-edit" data-color="#dc3545"
                                    style="background-color: #dc3545; width: 40px; height: 40px; border-radius: 5px;"></button>
                                <button type="button" class="btn btn-sm color-preset-edit" data-color="#fd7e14"
                                    style="background-color: #fd7e14; width: 40px; height: 40px; border-radius: 5px;"></button>
                                <button type="button" class="btn btn-sm color-preset-edit" data-color="#ffc107"
                                    style="background-color: #ffc107; width: 40px; height: 40px; border-radius: 5px;"></button>
                                <button type="button" class="btn btn-sm color-preset-edit" data-color="#0dcaf0"
                                    style="background-color: #0dcaf0; width: 40px; height: 40px; border-radius: 5px;"></button>
                                <button type="button" class="btn btn-sm color-preset-edit" data-color="#6c757d"
                                    style="background-color: #6c757d; width: 40px; height: 40px; border-radius: 5px;"></button>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Preview</label>
                            <div class="p-3 border rounded">
                                <span class="badge" id="edit_preview_badge" style="font-size: 14px;">
                                    Sample Priority
                                </span>
                            </div>
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
                            <i class="fa fa-save me-2"></i>Update Priority
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
                    <h4 class="card-title">Priorities List</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addPriorityModal">
                        <i class="fa fa-plus me-2"></i>Add New Priority
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        <strong>Priority Levels:</strong> Lower numbers indicate higher priority.
                        Level 1 is the highest priority (e.g., Critical), while higher numbers represent lower priorities.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-responsive-md" id="prioritiesTable">
                            <thead>
                                <tr>
                                    <th style="width:50px;">
                                        <div class="form-check custom-checkbox checkbox-success check-lg me-3">
                                            <input type="checkbox" class="form-check-input" id="checkAll">
                                            <label class="form-check-label" for="checkAll"></label>
                                        </div>
                                    </th>
                                    <th><strong>LEVEL</strong></th>
                                    <th><strong>NAME</strong></th>
                                    <th><strong>COLOR</strong></th>
                                    <th><strong>PREVIEW</strong></th>
                                    <th><strong>TICKETS</strong></th>
                                    <th><strong>STATUS</strong></th>
                                    <th><strong>CREATED</strong></th>
                                    <th><strong>ACTION</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($priorities as $priority)
                                    <tr>
                                        <td>
                                            <div class="form-check custom-checkbox checkbox-success check-lg me-3">
                                                <input type="checkbox" class="form-check-input"
                                                    id="customCheckBox{{ $priority->id }}">
                                                <label class="form-check-label"
                                                    for="customCheckBox{{ $priority->id }}"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-rounded badge-dark"
                                                style="font-size: 16px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                {{ $priority->level }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2"
                                                    style="background-color: {{ $priority->color }}20;">
                                                    <span class="avatar-title rounded-circle"
                                                        style="background-color: {{ $priority->color }}; color: white;">
                                                        {{ substr($priority->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <span class="w-space-no"><strong>{{ $priority->name }}</strong></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    style="width: 30px; height: 30px; background-color: {{ $priority->color }}; border-radius: 5px; border: 1px solid #ddd;">
                                                </div>
                                                <code class="ms-2">{{ $priority->color }}</code>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge"
                                                style="background-color: {{ $priority->color }}; font-size: 13px; padding: 8px 15px;">
                                                {{ $priority->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-rounded badge-info">
                                                {{ $priority->tickets_count }} tickets
                                            </span>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input status-toggle" type="checkbox"
                                                    data-id="{{ $priority->id }}"
                                                    {{ $priority->status === 'active' ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>{{ $priority->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button type="button"
                                                    class="btn btn-primary shadow btn-xs sharp me-1 btn-edit"
                                                    data-id="{{ $priority->id }}">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-danger shadow btn-xs sharp btn-delete"
                                                    data-id="{{ $priority->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="fa fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No priorities found</p>
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#addPriorityModal">
                                                <i class="fa fa-plus me-2"></i>Add First Priority
                                            </button>
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
    <style>
        .color-preset,
        .color-preset-edit {
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .color-preset:hover,
        .color-preset-edit:hover {
            border-color: #000;
            transform: scale(1.1);
        }
    </style>
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

            // Color picker sync for Add Modal
            $('#add_color').on('input', function() {
                let color = $(this).val();
                $('#add_color_hex').val(color);
                $('#add_preview_badge').css('background-color', color);
            });

            // Color preset buttons for Add Modal
            $('.color-preset').on('click', function() {
                let color = $(this).data('color');
                $('#add_color').val(color);
                $('#add_color_hex').val(color);
                $('#add_preview_badge').css('background-color', color);
            });

            // Update preview badge text on name change
            $('#add_name').on('input', function() {
                let name = $(this).val() || 'Sample Priority';
                $('#add_preview_badge').text(name);
            });

            // Color picker sync for Edit Modal
            $('#edit_color').on('input', function() {
                let color = $(this).val();
                $('#edit_color_hex').val(color);
                $('#edit_preview_badge').css('background-color', color);
            });

            // Color preset buttons for Edit Modal
            $('.color-preset-edit').on('click', function() {
                let color = $(this).data('color');
                $('#edit_color').val(color);
                $('#edit_color_hex').val(color);
                $('#edit_preview_badge').css('background-color', color);
            });

            // Update preview badge text on name change
            $('#edit_name').on('input', function() {
                let name = $(this).val() || 'Sample Priority';
                $('#edit_preview_badge').text(name);
            });

            // Add Priority Form Submit
            $('#addPriorityForm').on('submit', function(e) {
                e.preventDefault();

                let formData = $(this).serialize();

                $.ajax({
                    url: '{{ route('admin.priorities.store') }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#addPriorityModal').modal('hide');
                            $('#addPriorityForm')[0].reset();
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
                            toastr.error('Failed to create priority');
                        }
                    }
                });
            });

            // Edit Button Click
            $('.btn-edit').on('click', function() {
                let priorityId = $(this).data('id');

                $.ajax({
                    url: `/admin/priorities/${priorityId}`,
                    type: 'GET',
                    success: function(priority) {
                        $('#edit_priority_id').val(priority.id);
                        $('#edit_name').val(priority.name);
                        $('#edit_level').val(priority.level);
                        $('#edit_color').val(priority.color);
                        $('#edit_color_hex').val(priority.color);
                        $('#edit_status').val(priority.status);
                        $('#edit_preview_badge').text(priority.name).css('background-color',
                            priority.color);
                        $('#editPriorityModal').modal('show');
                    },
                    error: function() {
                        toastr.error('Failed to load priority data');
                    }
                });
            });

            // Edit Priority Form Submit
            $('#editPriorityForm').on('submit', function(e) {
                e.preventDefault();

                let priorityId = $('#edit_priority_id').val();
                let formData = $(this).serialize();

                $.ajax({
                    url: `/admin/priorities/${priorityId}`,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            $('#editPriorityModal').modal('hide');
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
                            toastr.error('Failed to update priority');
                        }
                    }
                });
            });

            // Delete Button Click
            $('.btn-delete').on('click', function() {
                let priorityId = $(this).data('id');

                if (confirm('Are you sure you want to delete this priority?')) {
                    $.ajax({
                        url: `/admin/priorities/${priorityId}`,
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
                                toastr.error('Failed to delete priority');
                            }
                        }
                    });
                }
            });

            // Toggle Status
            $('.status-toggle').on('change', function() {
                let priorityId = $(this).data('id');

                $.ajax({
                    url: `/admin/priorities/${priorityId}/toggle-status`,
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
