{{-- resources/views/admin/locations/index.blade.php --}}
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
        /* Custom DataTable Styling */
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

        /* Table Styling */
        #locationsTable {
            font-size: 14px;
            width: 100% !important;
        }

        #locationsTable thead th {
            font-size: 14px;
            font-weight: 600;
            padding: 15px 10px;
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #dee2e6 !important;
            text-align: center;
            vertical-align: middle;
        }

        #locationsTable thead th:first-child {
            width: 50px;
        }

        #locationsTable thead th:nth-child(4),
        #locationsTable thead th:nth-child(5) {
            width: 80px;
        }

        #locationsTable thead th:nth-child(6) {
            width: 100px;
        }

        #locationsTable thead th:nth-child(7) {
            width: 120px;
        }

        #locationsTable thead th:last-child {
            width: 150px;
        }

        #locationsTable tbody td {
            padding: 12px 10px;
            vertical-align: middle;
        }

        #locationsTable tbody td:first-child {
            text-align: center;
        }

        #locationsTable tbody td:nth-child(4),
        #locationsTable tbody td:nth-child(5),
        #locationsTable tbody td:nth-child(6) {
            text-align: center;
        }

        #locationsTable tbody td:nth-child(7) {
            text-align: center;
        }

        #locationsTable tbody td:last-child {
            text-align: center;
        }

        #locationsTable tbody tr:hover {
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
            background-color: #007bff;
            color: white;
        }

        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }

        /* Location Type Badges */
        .badge-area {
            background-color: #6c757d;
            color: white;
        }

        .badge-floor {
            background-color: #17a2b8;
            color: white;
        }

        .badge-room {
            background-color: #28a745;
            color: white;
        }

        .badge-facility {
            background-color: #fd7e14;
            color: white;
        }

        .badge-department {
            background-color: #6610f2;
            color: white;
        }

        /* Hotel Badges */
        .badge-harris {
            background-color: #007bff;
            color: white;
        }

        .badge-pop {
            background-color: #28a745;
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

        /* Floor select styling */
        #add_floor_number,
        #edit_floor_number {
            padding: 10px;
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

            #locationsTable thead th,
            #locationsTable tbody td {
                padding: 8px 5px;
            }
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

        /* Hotel radio styling */
        .hotel-radio-group {
            display: flex;
            gap: 15px;
        }

        .hotel-radio-item {
            flex: 1;
        }

        .hotel-radio-item input[type="radio"] {
            display: none;
        }

        .hotel-radio-item label {
            display: block;
            padding: 10px 15px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .hotel-radio-item input[type="radio"]:checked+label {
            border-color: var(--primary);
            background-color: rgba(var(--primary-rgb), 0.1);
            color: var(--primary);
            font-weight: 600;
        }

        .hotel-radio-item label:hover {
            border-color: #adb5bd;
        }
    </style>
@endpush

@section('content')
    <!-- Add Location Modal -->
    <div class="modal fade" id="addLocationModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addLocationForm" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <!-- Location Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name"
                                    placeholder="e.g., Lobby, Room 101" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Hotel -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hotel <span class="text-danger">*</span></label>
                                <div class="hotel-radio-group">
                                    <div class="hotel-radio-item">
                                        <input type="radio" id="add_hotel_harris" name="hotel" value="harris" checked>
                                        <label for="add_hotel_harris">
                                            <i class="fas fa-hotel me-1"></i> Harris Hotel
                                        </label>
                                    </div>
                                    <div class="hotel-radio-item">
                                        <input type="radio" id="add_hotel_pop" name="hotel" value="pop">
                                        <label for="add_hotel_pop">
                                            <i class="fas fa-building me-1"></i> Pop Hotel
                                        </label>
                                    </div>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Location Type -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="location_type" id="add_location_type" required>
                                    <option value="">Select Type</option>
                                    <option value="area">Area</option>
                                    <option value="floor">Floor</option>
                                    <option value="room">Room</option>
                                    <option value="facility">Facility</option>
                                    <option value="department">Department</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Floor Number -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Floor Number</label>
                                <select class="form-control" name="floor_number" id="add_floor_number">
                                    <option value="">Select Floor</option>
                                    <!-- Harris Hotel Floors -->
                                    <optgroup label="Harris Hotel">
                                        <option value="GF">Ground Floor (GF)</option>
                                        <option value="3">3rd Floor</option>
                                        <option value="3A">3A Floor</option>
                                        <option value="5">5th Floor</option>
                                        <option value="6">6th Floor</option>
                                        <option value="7">7th Floor</option>
                                        <option value="8">8th Floor</option>
                                        <option value="9">9th Floor</option>
                                    </optgroup>
                                    <!-- Pop Hotel Floors -->
                                    <optgroup label="Pop Hotel">
                                        <option value="GF">Ground Floor (GF)</option>
                                        <option value="M">Mezzanine (M)</option>
                                        <option value="3A">3A Floor</option>
                                        <option value="5">5th Floor</option>
                                        <option value="6">6th Floor</option>
                                        <option value="7">7th Floor</option>
                                        <option value="8">8th Floor</option>
                                        <option value="9">9th Floor</option>
                                    </optgroup>
                                </select>
                                <small class="text-muted">Optional - for all location types</small>
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

                            <!-- Description -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Optional description"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Location
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Location Modal -->
    <div class="modal fade" id="editLocationModal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editLocationForm" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_location_id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Location Name -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Hotel -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hotel <span class="text-danger">*</span></label>
                                <div class="hotel-radio-group">
                                    <div class="hotel-radio-item">
                                        <input type="radio" id="edit_hotel_harris" name="hotel" value="harris">
                                        <label for="edit_hotel_harris">
                                            <i class="fas fa-hotel me-1"></i> Harris Hotel
                                        </label>
                                    </div>
                                    <div class="hotel-radio-item">
                                        <input type="radio" id="edit_hotel_pop" name="hotel" value="pop">
                                        <label for="edit_hotel_pop">
                                            <i class="fas fa-building me-1"></i> Pop Hotel
                                        </label>
                                    </div>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Location Type -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_location_type" name="location_type" required>
                                    <option value="area">Area</option>
                                    <option value="floor">Floor</option>
                                    <option value="room">Room</option>
                                    <option value="facility">Facility</option>
                                    <option value="department">Department</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Floor Number -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Floor Number</label>
                                <select class="form-control" id="edit_floor_number" name="floor_number">
                                    <option value="">Select Floor</option>
                                    <!-- Harris Hotel Floors -->
                                    <optgroup label="Harris Hotel">
                                        <option value="GF">Ground Floor (GF)</option>
                                        <option value="3">3rd Floor</option>
                                        <option value="3A">3A Floor</option>
                                        <option value="5">5th Floor</option>
                                        <option value="6">6th Floor</option>
                                        <option value="7">7th Floor</option>
                                        <option value="8">8th Floor</option>
                                        <option value="9">9th Floor</option>
                                    </optgroup>
                                    <!-- Pop Hotel Floors -->
                                    <optgroup label="Pop Hotel">
                                        <option value="GF">Ground Floor (GF)</option>
                                        <option value="M">Mezzanine (M)</option>
                                        <option value="3A">3A Floor</option>
                                        <option value="5">5th Floor</option>
                                        <option value="6">6th Floor</option>
                                        <option value="7">7th Floor</option>
                                        <option value="8">8th Floor</option>
                                        <option value="9">9th Floor</option>
                                    </optgroup>
                                </select>
                                <small class="text-muted">Optional - for all location types</small>
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

                            <!-- Description -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Location
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Location List</h4>
                    @if (auth()->user()->role === 'superadmin')
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addLocationModal">
                            <i class="fas fa-plus me-1"></i> Add New Location
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="locationsTable" class="display table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Location Name</th>
                                    <th>Hotel</th>
                                    <th>Type</th>
                                    <th>Floor</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($locations as $index => $location)
                                    @php
                                        $typeBadges = [
                                            'area' => 'badge-area',
                                            'floor' => 'badge-floor',
                                            'room' => 'badge-room',
                                            'facility' => 'badge-facility',
                                            'department' => 'badge-department',
                                        ];

                                        $hotelBadges = [
                                            'harris' => 'badge-harris',
                                            'pop' => 'badge-pop',
                                        ];
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $location->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge {{ $hotelBadges[$location->hotel] ?? 'badge-secondary' }}">
                                                {{ $location->hotel === 'harris' ? 'Harris Hotel' : 'Pop Hotel' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $typeBadges[$location->location_type] ?? 'badge-secondary' }}">
                                                {{ ucfirst($location->location_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($location->floor_number)
                                                <span class="badge badge-info">{{ $location->floor_number }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $location->description ? Str::limit($location->description, 50) : '-' }}
                                        </td>
                                        <td>
                                            @php
                                                $statusBadge = [
                                                    'active' => 'success',
                                                    'inactive' => 'danger',
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $statusBadge[$location->status] }}">
                                                {{ ucfirst($location->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $location->created_at ? $location->created_at->format('d M Y') : '-' }}</td>
                                        <td>
                                            @if (auth()->user()->role === 'superadmin')
                                                <div class="d-flex justify-content-center">
                                                    <button type="button"
                                                        class="btn btn-primary btn-sm shadow btn-xs sharp me-1 edit-location"
                                                        data-id="{{ $location->id }}" data-name="{{ $location->name }}"
                                                        data-hotel="{{ $location->hotel }}"
                                                        data-location_type="{{ $location->location_type }}"
                                                        data-floor_number="{{ $location->floor_number }}"
                                                        data-description="{{ $location->description }}"
                                                        data-status="{{ $location->status }}" title="Edit">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-{{ $location->status === 'active' ? 'warning' : 'success' }} btn-sm shadow btn-xs sharp me-1 toggle-status"
                                                        data-id="{{ $location->id }}" data-name="{{ $location->name }}"
                                                        data-status="{{ $location->status }}"
                                                        title="{{ $location->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                        <i
                                                            class="fas fa-{{ $location->status === 'active' ? 'ban' : 'check' }}"></i>
                                                    </button>

                                                    <button type="button"
                                                        class="btn btn-danger btn-sm shadow btn-xs sharp delete-location"
                                                        data-id="{{ $location->id }}" data-name="{{ $location->name }}"
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
            var table = $('#locationsTable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search locations...",
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
                        "targets": [0, 2, 3, 4, 6, 7, 8]
                    }
                ],
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                "drawCallback": function(settings) {
                    // Handle empty table on draw
                    var api = this.api();
                    if (api.rows().count() === 0) {
                        var tbody = $(this).find('tbody');
                        if (tbody.find('tr').length === 0) {
                            tbody.html(
                                '<tr><td colspan="9" class="text-center">No locations found</td></tr>'
                            );
                        }
                    }
                    $('.dataTables_paginate > .pagination').addClass(
                        'pagination-gutter pagination-primary');
                },
                "initComplete": function(settings, json) {
                    // Handle empty table on init
                    if (this.api().rows().count() === 0) {
                        var tbody = $('#locationsTable tbody');
                        if (tbody.find('tr').length === 0) {
                            tbody.html(
                                '<tr><td colspan="9" class="text-center">No locations found</td></tr>'
                            );
                        }
                    }
                }
            });

            // Delegasi event untuk edit, toggle, delete
            $(document).on('click', '.edit-location', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var hotel = $(this).data('hotel');
                var locationType = $(this).data('location_type');
                var floorNumber = $(this).data('floor_number');
                var description = $(this).data('description');
                var status = $(this).data('status');

                $('#edit_location_id').val(id);
                $('#edit_name').val(name);
                $('#edit_hotel_' + hotel).prop('checked', true);
                $('#edit_location_type').val(locationType);
                $('#edit_floor_number').val(floorNumber || '');
                $('#edit_description').val(description);
                $('#edit_status').val(status);

                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#editLocationModal').modal('show');
            });

            $(document).on('click', '.toggle-status', function() {
                var locationId = $(this).data('id');
                var locationName = $(this).data('name');
                var currentStatus = $(this).data('status');
                var newStatus = currentStatus === 'active' ? 'inactive' : 'active';

                Swal.fire({
                    title: 'Change Location Status?',
                    html: `Are you sure you want to change <strong>${locationName}</strong>'s status?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, change it!',
                    cancelButtonText: 'Cancel'
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

            $(document).on('click', '.delete-location', function() {
                var locationId = $(this).data('id');
                var locationName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    html: `You are about to delete <strong>${locationName}</strong> location.<br>This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.locations.destroy', ':id') }}".replace(
                                ':id',
                                locationId),
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

            // Add Location Form Submit - SIMPLE VERSION
            $('#addLocationForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Saving...');

                $.ajax({
                    url: "{{ route('admin.locations.store') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (response.success) {
                            $('#addLocationModal').modal('hide');
                            $('#addLocationForm')[0].reset();
                            $('.form-control').removeClass('is-invalid');
                            $('.invalid-feedback').text('');
                            $('#add_hotel_harris').prop('checked', true);

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
                                    '#addLocationForm');
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

            // Edit Location Form Submit - SIMPLE VERSION
            $('#editLocationForm').on('submit', function(e) {
                e.preventDefault();

                var locationId = $('#edit_location_id').val();
                var formData = $(this).serialize();
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: "{{ route('admin.locations.update', ':id') }}".replace(':id', locationId),
                    type: 'POST', // Use POST with _method
                    data: formData + '&_method=PUT',
                    success: function(response) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (response.success) {
                            $('#editLocationModal').modal('hide');
                            $('#editLocationForm')[0].reset();
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
                                    '#editLocationForm');
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
            $('#addLocationModal, #editLocationModal').on('hidden.bs.modal', function() {
                $(this).find('form')[0].reset();
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                if ($(this).attr('id') === 'addLocationModal') {
                    $('#add_hotel_harris').prop('checked', true);
                }
            });

            // Form validation on input
            $('.form-control').on('input', function() {
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').text('');
            });
        });
    </script>
@endpush
