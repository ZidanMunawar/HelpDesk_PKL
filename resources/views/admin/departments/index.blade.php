{{-- resources/views/admin/departments/index.blade.php --}}
@extends('layouts.main')

@section('title', 'Department Management | ' . config('app.name'))

@section('page-title', 'Department Management')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Admin', 'url' => 'javascript:void(0)'],
            ['title' => 'Department Management', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        /* Custom DataTable Styling - Larger Size (Konsisten dengan Location) */
        #departmentsTable_wrapper .dataTables_length,
        #departmentsTable_wrapper .dataTables_filter,
        #departmentsTable_wrapper .dataTables_info,
        #departmentsTable_wrapper .dataTables_paginate {
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
        #departmentsTable_wrapper .dataTables_length select {
            padding: 8px 35px 8px 15px;
            font-size: 14px;
            border: 1px solid #f0f1f5;
            border-radius: 0.75rem;
            background-color: #fff;
            margin: 0 10px;
        }

        #departmentsTable_wrapper .dataTables_length label {
            font-weight: 500;
            color: #6e6e6e;
        }

        /* Search Input Styling */
        #departmentsTable_wrapper .dataTables_filter input {
            padding: 10px 20px;
            font-size: 14px;
            border: 1px solid #f0f1f5;
            border-radius: 0.75rem;
            width: 250px;
            margin-left: 10px;
        }

        #departmentsTable_wrapper .dataTables_filter label {
            font-weight: 500;
            color: #6e6e6e;
        }

        /* Info Text Styling */
        #departmentsTable_wrapper .dataTables_info {
            font-size: 14px;
            font-weight: 500;
            color: #6e6e6e;
        }

        /* Pagination Styling */
        #departmentsTable_wrapper .dataTables_paginate {
            float: right;
        }

        #departmentsTable_wrapper .dataTables_paginate .pagination {
            margin: 0;
        }

        #departmentsTable_wrapper .dataTables_paginate .paginate_button {
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

        #departmentsTable_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary);
            color: white !important;
            border-color: var(--primary);
        }

        #departmentsTable_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary);
            color: white !important;
            border-color: var(--primary);
        }

        #departmentsTable_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8f9fa;
        }

        #departmentsTable_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #f8f9fa;
            color: #6e6e6e !important;
            border-color: #f0f1f5;
        }

        #departmentsTable_wrapper .dataTables_paginate .paginate_button.previous,
        #departmentsTable_wrapper .dataTables_paginate .paginate_button.next {
            padding: 8px 12px;
        }

        #departmentsTable_wrapper .dataTables_paginate .paginate_button i {
            font-size: 16px;
        }

        /* Table Styling */
        #departmentsTable {
            font-size: 14px;
            width: 100% !important;
        }

        #departmentsTable thead th {
            font-size: 14px;
            font-weight: 600;
            padding: 15px 10px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #dee2e6 !important;
            text-align: center;
            vertical-align: middle;
        }

        #departmentsTable thead th:first-child {
            width: 50px;
        }

        #departmentsTable thead th:nth-child(4) {
            width: 100px;
        }

        #departmentsTable thead th:nth-child(5) {
            width: 100px;
        }

        #departmentsTable thead th:nth-child(7) {
            width: 100px;
        }

        #departmentsTable thead th:nth-child(8) {
            width: 120px;
        }

        #departmentsTable thead th:last-child {
            width: 180px;
        }

        #departmentsTable tbody td {
            padding: 12px 10px;
            vertical-align: middle;
        }

        #departmentsTable tbody td:first-child {
            text-align: center;
        }

        #departmentsTable tbody td:nth-child(4) {
            text-align: center;
        }

        #departmentsTable tbody td:nth-child(5) {
            text-align: center;
        }

        #departmentsTable tbody td:nth-child(7) {
            text-align: center;
        }

        #departmentsTable tbody td:nth-child(8) {
            text-align: center;
        }

        #departmentsTable tbody td:last-child {
            text-align: center;
        }

        #departmentsTable tbody tr:hover {
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

        .badge-primary {
            background-color: #003366;
            color: white;
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

        /* Toggle Switch Styling */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 24px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: #003366;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(26px);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #departmentsTable_wrapper .dataTables_filter input {
                width: 100%;
                margin-top: 10px;
            }

            #departmentsTable_wrapper .dataTables_length,
            #departmentsTable_wrapper .dataTables_filter {
                text-align: left;
            }

            #departmentsTable_wrapper .dataTables_paginate {
                float: none;
                text-align: center;
                margin-top: 15px;
            }

            #departmentsTable thead th,
            #departmentsTable tbody td {
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

        /* Avatar styling for manager */
        .avatar-sm {
            width: 28px;
            height: 28px;
            object-fit: cover;
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
    <!-- Add Department Modal -->
    <div class="modal fade" id="addDepartmentModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addDepartmentForm" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Department Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name"
                                    placeholder="Enter department name" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Manager -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Manager</label>
                                <select class="form-control" name="manager_id">
                                    <option value="">Select Manager</option>
                                    @foreach ($managers as $manager)
                                        <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Manager Access -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Manager Access</label>
                                <select class="form-control" name="has_manager_access">
                                    <option value="0">No (Standard Department)</option>
                                    <option value="1">Yes (Has Manager Menu Access)</option>
                                </select>
                                <small class="text-muted">Enable if this department should have access to manager menu with
                                    statistics</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Description -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Enter department description"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editDepartmentForm" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_department_id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Department Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name"
                                    placeholder="Enter department name" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Manager -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Manager</label>
                                <select class="form-control" id="edit_manager_id" name="manager_id">
                                    <option value="">Select Manager</option>
                                    @foreach ($managers as $manager)
                                        <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Manager Access -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Manager Access</label>
                                <select class="form-control" id="edit_has_manager_access" name="has_manager_access">
                                    <option value="0">No (Standard Department)</option>
                                    <option value="1">Yes (Has Manager Menu Access)</option>
                                </select>
                                <small class="text-muted">Enable if this department should have access to manager menu with
                                    statistics</small>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Description -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3"
                                    placeholder="Enter department description"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Departments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Department List</h4>
                    @if (auth()->user()->role === 'superadmin')
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addDepartmentModal">
                            <i class="fas fa-plus me-1"></i> Add New Department
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="departmentsTable" class="display table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Department Name</th>
                                    <th>Manager</th>
                                    <th>Total Users</th>
                                    <th>Manager Access</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($departments as $index => $department)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $department->name }}</strong>
                                        </td>
                                        <td>
                                            @if ($department->manager)
                                                <div class="d-flex align-items-center">
                                                    @if ($department->manager->profile_picture)
                                                        <img src="{{ asset('storage/' . $department->manager->profile_picture) }}"
                                                            class="rounded-circle me-2" width="28" height="28"
                                                            alt="{{ $department->manager->name }}">
                                                    @else
                                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                                            style="width: 28px; height: 28px;">
                                                            {{ strtoupper(substr($department->manager->name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <span>{{ $department->manager->name }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-info">{{ $department->active_users_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            @if ($department->has_manager_access)
                                                <span class="badge badge-primary">
                                                    <i class="fas fa-user-tie me-1"></i> Yes
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-user me-1"></i> No
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $department->description ? Str::limit($department->description, 50) : '-' }}
                                        </td>
                                        <td>
                                            @php
                                                $statusBadge = [
                                                    'active' => 'success',
                                                    'inactive' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $statusBadge[$department->status] }}">
                                                {{ ucfirst($department->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $department->created_at ? $department->created_at->format('d M Y') : '-' }}
                                        </td>
                                        <td>
                                            @if (auth()->user()->role === 'superadmin')
                                                <div class="d-flex justify-content-center">
                                                    <button type="button"
                                                        class="btn btn-primary btn-sm shadow btn-xs sharp me-1 edit-department"
                                                        data-id="{{ $department->id }}"
                                                        data-name="{{ $department->name }}"
                                                        data-manager-id="{{ $department->manager_id }}"
                                                        data-description="{{ $department->description }}"
                                                        data-status="{{ $department->status }}"
                                                        data-has-manager-access="{{ $department->has_manager_access ? '1' : '0' }}"
                                                        title="Edit">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-{{ $department->status === 'active' ? 'warning' : 'success' }} btn-sm shadow btn-xs sharp me-1 toggle-status"
                                                        data-id="{{ $department->id }}"
                                                        data-name="{{ $department->name }}"
                                                        data-status="{{ $department->status }}"
                                                        title="{{ $department->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                        <i
                                                            class="fas fa-{{ $department->status === 'active' ? 'ban' : 'check' }}"></i>
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-{{ $department->has_manager_access ? 'secondary' : 'primary' }} btn-sm shadow btn-xs sharp me-1 toggle-manager-access"
                                                        data-id="{{ $department->id }}"
                                                        data-name="{{ $department->name }}"
                                                        data-has-access="{{ $department->has_manager_access ? '1' : '0' }}"
                                                        title="{{ $department->has_manager_access ? 'Remove Manager Access' : 'Grant Manager Access' }}">
                                                        <i
                                                            class="fas fa-{{ $department->has_manager_access ? 'user-slash' : 'user-tie' }}"></i>
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-danger btn-sm shadow btn-xs sharp delete-department"
                                                        data-id="{{ $department->id }}"
                                                        data-name="{{ $department->name }}" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-muted">No Actions</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    {{-- JANGAN TARUH TR KOSONG DI SINI --}}
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

            // Initialize DataTable dengan empty table handling
            var table = $('#departmentsTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search departments...",
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
                        "targets": [8]
                    },
                    {
                        "className": "text-center",
                        "targets": [0, 3, 4, 6, 7, 8]
                    }
                ],
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                "drawCallback": function(settings) {
                    var api = this.api();
                    if (api.rows().count() === 0) {
                        var tbody = $(this).find('tbody');
                        if (tbody.find('tr').length === 0) {
                            tbody.html(
                                '<tr><td colspan="9" class="text-center">No departments found</td>' +
                                '</tr>'
                            );
                        }
                    }
                    $('.dataTables_paginate > .pagination').addClass(
                        'pagination-gutter pagination-primary');
                },
                "initComplete": function(settings, json) {
                    if (this.api().rows().count() === 0) {
                        var tbody = $('#departmentsTable tbody');
                        if (tbody.find('tr').length === 0) {
                            tbody.html(
                                '<tr><td colspan="9" class="text-center">No departments found</td>' +
                                '</tr>'
                            );
                        }
                    }
                }
            });

            // Delegasi event untuk edit department
            $(document).on('click', '.edit-department', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var managerId = $(this).data('manager-id');
                var description = $(this).data('description');
                var status = $(this).data('status');
                var hasManagerAccess = $(this).data('has-manager-access');

                $('#edit_department_id').val(id);
                $('#edit_name').val(name);
                $('#edit_manager_id').val(managerId);
                $('#edit_description').val(description);
                $('#edit_status').val(status);
                $('#edit_has_manager_access').val(hasManagerAccess);

                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#editDepartmentModal').modal('show');
            });

            // Toggle Department Status
            $(document).on('click', '.toggle-status', function() {
                var departmentId = $(this).data('id');
                var departmentName = $(this).data('name');
                var currentStatus = $(this).data('status');
                var newStatus = currentStatus === 'active' ? 'inactive' : 'active';

                Swal.fire({
                    title: 'Change Department Status?',
                    html: `Are you sure you want to change <strong>${departmentName}</strong>'s status to <strong>${newStatus.toUpperCase()}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.departments.toggle-status', ':id') }}"
                                .replace(':id', departmentId),
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

            // Toggle Manager Access
            $(document).on('click', '.toggle-manager-access', function() {
                var departmentId = $(this).data('id');
                var departmentName = $(this).data('name');
                var hasAccess = $(this).data('has-access') == '1';
                var newAccess = !hasAccess;
                var actionText = newAccess ? 'grant' : 'remove';
                var actionTitle = newAccess ? 'Grant Manager Access' : 'Remove Manager Access';

                Swal.fire({
                    title: actionTitle,
                    html: `Are you sure you want to <strong>${actionText}</strong> manager access for <strong>${departmentName}</strong>?<br><br>` +
                        (newAccess ?
                            '<span class="text-primary">This department will have access to the Manager Menu with statistics.</span>' :
                            '<span class="text-warning">This department will lose access to the Manager Menu.</span>'
                        ),
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: newAccess ? '#28a745' : '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: newAccess ? 'Yes, Grant Access' : 'Yes, Remove Access',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.departments.toggle-manager-access', ':id') }}"
                                .replace(':id', departmentId),
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

            // Delete Department
            $(document).on('click', '.delete-department', function() {
                var departmentId = $(this).data('id');
                var departmentName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to delete <strong>${departmentName}</strong> department.<br>This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.departments.destroy', ':id') }}".replace(
                                ':id',
                                departmentId),
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

            // Add Department Form Submit
            $('#addDepartmentForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

                $.ajax({
                    url: "{{ route('admin.departments.store') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (response.success) {
                            $('#addDepartmentModal').modal('hide');
                            $('#addDepartmentForm')[0].reset();
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
                                    '#addDepartmentForm');
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

            // Edit Department Form Submit
            $('#editDepartmentForm').on('submit', function(e) {
                e.preventDefault();

                var departmentId = $('#edit_department_id').val();
                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: "{{ route('admin.departments.update', ':id') }}".replace(':id',
                        departmentId),
                    type: 'POST',
                    data: formData + '&_method=PUT',
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (response.success) {
                            $('#editDepartmentModal').modal('hide');
                            $('#editDepartmentForm')[0].reset();
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
                                    '#editDepartmentForm');
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

            // Reset form when modal is closed
            $('#addDepartmentModal, #editDepartmentModal').on('hidden.bs.modal', function() {
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
