@extends('layouts.main')

@section('title', 'Location Management | ' . config('app.name'))

@section('page-title', 'Location Management')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Admin', 'url' => 'javascript:void(0)'],
            ['title' => 'Location Management', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        /* Custom DataTable Styling - Larger Size */
        #locationsTable_wrapper .dataTables_length,
        #locationsTable_wrapper .dataTables_filter,
        #locationsTable_wrapper .dataTables_info,
        #locationsTable_wrapper .dataTables_paginate {
            padding: 20px 0;
            font-size: 14px;
        }

        /* Length Menu Styling */
        #locationsTable_wrapper .dataTables_length select {
            padding: 8px 35px 8px 15px;
            font-size: 14px;
            border: 1px solid #f0f1f5;
            border-radius: 0.75rem;
            background-color: #fff;
            margin: 0 10px;
        }

        #locationsTable_wrapper .dataTables_length label {
            font-weight: 500;
            color: #6e6e6e;
        }

        /* Search Input Styling */
        #locationsTable_wrapper .dataTables_filter input {
            padding: 10px 20px;
            font-size: 14px;
            border: 1px solid #f0f1f5;
            border-radius: 0.75rem;
            width: 250px;
            margin-left: 10px;
        }

        #locationsTable_wrapper .dataTables_filter label {
            font-weight: 500;
            color: #6e6e6e;
        }

        /* Info Text Styling */
        #locationsTable_wrapper .dataTables_info {
            font-size: 14px;
            font-weight: 500;
            color: #6e6e6e;
        }

        /* Pagination Styling */
        #locationsTable_wrapper .dataTables_paginate {
            float: right;
        }

        #locationsTable_wrapper .dataTables_paginate .pagination {
            margin: 0;
        }

        #locationsTable_wrapper .dataTables_paginate .paginate_button {
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

        #locationsTable_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary);
            color: white !important;
            border-color: var(--primary);
        }

        #locationsTable_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary);
            color: white !important;
            border-color: var(--primary);
        }

        #locationsTable_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8f9fa;
        }

        #locationsTable_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #f8f9fa;
            color: #6e6e6e !important;
            border-color: #f0f1f5;
        }

        #locationsTable_wrapper .dataTables_paginate .paginate_button.previous,
        #locationsTable_wrapper .dataTables_paginate .paginate_button.next {
            padding: 8px 12px;
        }

        #locationsTable_wrapper .dataTables_paginate .paginate_button i {
            font-size: 16px;
        }

        /* Table Styling */
        #locationsTable {
            font-size: 14px;
        }

        #locationsTable thead th {
            font-size: 14px;
            font-weight: 600;
            padding: 15px 10px;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        #locationsTable tbody td {
            padding: 12px 10px;
            vertical-align: middle;
        }

        #locationsTable tbody tr:hover {
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
            #locationsTable_wrapper .dataTables_filter input {
                width: 100%;
                margin-top: 10px;
            }

            #locationsTable_wrapper .dataTables_length,
            #locationsTable_wrapper .dataTables_filter {
                text-align: left;
            }

            #locationsTable_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                margin-top: 15px;
            }
        }
    </style>
@endpush


@section('content')
    <!-- Add Location Modal -->
    <div class="modal fade" id="addLocationModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addLocationForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Location Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name"
                                placeholder="e.g., Lobby, Room 101, Restaurant" required>
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

    <!-- Edit Location Modal -->
    <div class="modal fade" id="editLocationModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editLocationForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="location_id" id="edit_location_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="text-black font-w500">Location Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
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

    <!-- Locations Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Location Management</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#addLocationModal">
                        <i class="fa fa-plus me-2"></i>Add Location
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="locationsTable" class="display min-w850">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Location Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($locations as $index => $location)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $location->name }}</strong></td>
                                        <td>{{ $location->description ?? '-' }}</td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $location->status === 'active' ? 'success' : 'danger' }}">
                                                {{ ucfirst($location->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $location->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <button type="button"
                                                    class="btn btn-primary shadow btn-xs sharp me-1 edit-location"
                                                    data-id="{{ $location->id }}" data-name="{{ $location->name }}"
                                                    data-description="{{ $location->description }}"
                                                    data-status="{{ $location->status }}" title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-warning shadow btn-xs sharp me-1 toggle-status"
                                                    data-id="{{ $location->id }}" data-name="{{ $location->name }}"
                                                    data-status="{{ $location->status }}" title="Toggle Status">
                                                    <i class="fa fa-power-off"></i>
                                                </button>
                                                <button type="button"
                                                    class="btn btn-danger shadow btn-xs sharp delete-location"
                                                    data-id="{{ $location->id }}" data-name="{{ $location->name }}"
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
            // Toastr config
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "5000",
                "closeButton": true,
                "progressBar": true
            };

            // DataTable
            // DataTable
            var table = $('#locationsTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "language": {
                    "search": "",
                    "searchPlaceholder": "Search locations...",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ locations",
                    "infoEmpty": "Showing 0 to 0 of 0 locations",
                    "infoFiltered": "(filtered from _MAX_ total locations)",
                    "zeroRecords": "No matching locations found",
                    "emptyTable": "No locations available",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "<i class='fa fa-angle-right'></i>",
                        "previous": "<i class='fa fa-angle-left'></i>"
                    }
                },
                "columnDefs": [{
                        "orderable": false,
                        "targets": [5]
                    }, // Disable sorting for Action column
                    {
                        "className": "text-center",
                        "targets": [0, 3, 5]
                    } // Center align specific columns
                ],
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                "drawCallback": function() {
                    // Custom pagination styling
                    $('.dataTables_paginate > .pagination').addClass('pagination-gutter');
                }
            });

            // Add Location
            $('#addLocationForm').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

                $.ajax({
                    url: "{{ route('admin.locations.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#addLocationModal').modal('hide');
                        $('#addLocationForm')[0].reset();
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

            // Edit Location
            $(document).on('click', '.edit-location', function() {
                $('#edit_location_id').val($(this).data('id'));
                $('#edit_name').val($(this).data('name'));
                $('#edit_description').val($(this).data('description'));
                $('#edit_status').val($(this).data('status'));
                $('#editLocationModal').modal('show');
            });

            $('#editLocationForm').on('submit', function(e) {
                e.preventDefault();
                var locationId = $('#edit_location_id').val();
                var formData = new FormData(this);
                var submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: "{{ route('admin.locations.update', ':id') }}".replace(':id', locationId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#editLocationModal').modal('hide');
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
                var locationId = $(this).data('id');
                var locationName = $(this).data('name');
                var currentStatus = $(this).data('status');
                var newStatus = currentStatus === 'active' ? 'inactive' : 'active';

                Swal.fire({
                    title: 'Change Status?',
                    html: `Change <strong>${locationName}</strong> to <span style="color: ${newStatus === 'active' ? '#28a745' : '#dc3545'}">${newStatus.toUpperCase()}</span>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.locations.toggle-status', ':id') }}"
                                .replace(':id', locationId),
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

            // Delete Location
            $(document).on('click', '.delete-location', function() {
                var locationId = $(this).data('id');
                var locationName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    html: `Delete location <strong>${locationName}</strong>?<br>This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.locations.destroy', ':id') }}".replace(
                                ':id', locationId),
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
