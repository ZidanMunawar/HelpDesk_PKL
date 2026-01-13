@extends('layouts.main')

@section('title', 'Create New Ticket | ' . config('app.name'))

@section('page-title', 'Create New Ticket')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Tickets', 'url' => route('tickets.index')],
            ['title' => 'Create New Ticket', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <style>
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select,
        .select2-container .select2-selection--single,
        .select2-container .select2-selection--multiple {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            min-height: 46px;
        }

        .form-control:focus,
        .form-select:focus,
        .select2-container--focus .select2-selection--single,
        .select2-container--focus .select2-selection--multiple {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.15);
        }

        .ticket-form-card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .section-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 16px;
        }

        .file-upload-area {
            border: 2px dashed #e0e0e0;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: var(--primary);
            background: #f0f4ff;
        }

        .file-upload-area i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
        }

        .file-list {
            margin-top: 15px;
        }

        .file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .file-item i {
            color: var(--primary);
            margin-right: 10px;
        }

        .file-item .remove-file {
            color: #dc3545;
            cursor: pointer;
            font-size: 18px;
        }

        .ck-editor__editable {
            min-height: 250px;
        }

        .required-mark {
            color: #dc3545;
            margin-left: 3px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
        }

        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 44px;
        }

        .location-type-group {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }

        .location-type-option {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .location-type-option:hover {
            background: #f0f4ff;
            border-color: var(--primary);
        }

        .location-type-option.active {
            background: #e8f4ff;
            border-color: var(--primary);
        }

        .location-type-radio {
            margin-right: 10px;
        }

        .location-manual-input {
            display: none;
        }

        .location-manual-input.show {
            display: block;
        }

        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card ticket-form-card">
                <div class="card-body">
                    <form id="createTicketForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Ticket Information -->
                        <div class="section-header">
                            <i class="fas fa-clipboard-list me-2"></i> Ticket Information
                        </div>

                        <div class="row">
                            <!-- Title -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    Ticket Title / Subject <span class="required-mark">*</span>
                                </label>
                                <input type="text" class="form-control" name="title"
                                    placeholder="e.g., Perbaikan AC di Room 201" required>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Brief description of the issue</small>
                            </div>

                            <!-- Category -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    Category <span class="required-mark">*</span>
                                </label>
                                <select class="form-select select2-single" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Priority -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    Priority Level <span class="required-mark">*</span>
                                </label>
                                <select class="form-select select2-single" name="priority_id" required>
                                    <option value="">Select Priority</option>
                                    @foreach ($priorities as $priority)
                                        <option value="{{ $priority->id }}">
                                            {{ $priority->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Department -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    Department
                                </label>
                                <select class="form-select select2-single" name="department_id">
                                    <option value="">Select Department</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Location -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    Location
                                </label>

                                <!-- Location Type Selection -->
                                <div class="location-type-group">
                                    <div class="location-type-option" data-type="predefined">
                                        <input type="radio" name="location_type" value="predefined"
                                            id="location_predefined" class="location-type-radio" checked>
                                        <label for="location_predefined" class="mb-0">
                                            <strong>Select from Predefined Locations</strong><br>
                                            <small class="text-muted">Choose from registered rooms, floors, or areas</small>
                                        </label>
                                    </div>

                                    <div class="location-type-option" data-type="manual">
                                        <input type="radio" name="location_type" value="manual" id="location_manual"
                                            class="location-type-radio">
                                        <label for="location_manual" class="mb-0">
                                            <strong>Enter Manual Location</strong><br>
                                            <small class="text-muted">If location is not in the list</small>
                                        </label>
                                    </div>
                                </div>

                                <!-- Predefined Location Select (Default) -->
                                <div class="mt-3" id="predefinedLocationSection">
                                    <select class="form-select select2-single" id="location_id" name="location_id">
                                        <option value="">Select Location</option>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}" data-type="{{ $location->location_type }}"
                                                data-floor="{{ $location->floor_number }}">
                                                {{ $location->name }}
                                                @if ($location->location_type == 'room')
                                                    (Room{{ $location->floor_number ? ' - Floor ' . $location->floor_number : '' }})
                                                @elseif($location->location_type == 'floor')
                                                    (Floor{{ $location->floor_number ? ' ' . $location->floor_number : '' }})
                                                @elseif($location->location_type == 'department')
                                                    (Department)
                                                @elseif($location->location_type == 'facility')
                                                    (Facility)
                                                @else
                                                    (Area)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>

                                <!-- Manual Location Input -->
                                <div class="mt-3 location-manual-input" id="manualLocationSection">
                                    <input type="text" class="form-control" id="location_manual_input"
                                        name="location_manual"
                                        placeholder="Enter location manually (e.g., Room 305, Lobby Area, etc.)">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">
                                    Due Date (Optional)
                                </label>
                                <input type="datetime-local" class="form-control" name="due_date">
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Expected completion date</small>
                            </div>

                        </div>

                        <!-- Description -->
                        <div class="section-header mt-4">
                            <i class="fas fa-align-left me-2"></i> Ticket Description
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    Detailed Description <span class="required-mark">*</span>
                                </label>
                                <textarea id="description" name="description" class="form-control"></textarea>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Please provide detailed information about the issue, follow-up
                                    notes, or any additional information</small>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="section-header mt-4">
                            <i class="fas fa-paperclip me-2"></i> Attachments (Optional)
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="file-upload-area" onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h5>Click to upload files</h5>
                                    <p class="text-muted mb-0">Supported: JPG, PNG, PDF, DOC, DOCX (Max 5MB per file)</p>
                                </div>
                                <input type="file" id="fileInput" name="attachments[]" multiple
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" style="display: none;">
                                <div class="file-list" id="fileList"></div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-lg me-2">
                                    <i class="fas fa-paper-plane me-2"></i> Submit Ticket
                                </button>
                                <a href="{{ route('tickets.index') }}" class="btn btn-light btn-lg">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>
    <!-- CKEditor -->
    <script src="{{ asset('assets/vendor/ckeditor/ckeditor.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2-single').select2({
                theme: "classic",
                width: '100%',
                placeholder: "Select an option",
                allowClear: true
            });

            // Initialize CKEditor
            let editor;
            ClassicEditor
                .create(document.querySelector('#description'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                        'blockQuote', 'insertTable', '|', 'undo', 'redo'
                    ],
                    height: '300px'
                })
                .then(newEditor => {
                    editor = newEditor;
                })
                .catch(error => {
                    console.error(error);
                });

            // Toastr configuration
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "5000"
            };

            // Location type toggle
            $('.location-type-option').on('click', function() {
                $('.location-type-option').removeClass('active');
                $(this).addClass('active');
                $(this).find('.location-type-radio').prop('checked', true);

                const type = $(this).data('type');
                if (type === 'manual') {
                    $('#predefinedLocationSection').hide();
                    $('#manualLocationSection').addClass('show');
                    $('#location_id').val('').trigger('change');
                } else {
                    $('#predefinedLocationSection').show();
                    $('#manualLocationSection').removeClass('show');
                    $('#location_manual_input').val('');
                }
            });

            // Initialize first option as active
            $('.location-type-option:first-child').addClass('active');

            // File upload handling
            let selectedFiles = [];

            $('#fileInput').on('change', function(e) {
                const files = Array.from(e.target.files);

                files.forEach(file => {
                    // Check file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        toastr.error(`File ${file.name} exceeds 5MB limit`);
                        return;
                    }

                    selectedFiles.push(file);
                    addFileToList(file);
                });

                // Reset input
                this.value = '';
            });

            function addFileToList(file) {
                const fileItem = $(`
                    <div class="file-item" data-filename="${file.name}">
                        <div>
                            <i class="fas fa-file"></i>
                            <span>${file.name}</span>
                            <small class="text-muted ms-2">(${formatFileSize(file.size)})</small>
                        </div>
                        <i class="fas fa-times remove-file"></i>
                    </div>
                `);

                fileItem.find('.remove-file').on('click', function() {
                    const fileName = $(this).closest('.file-item').data('filename');
                    selectedFiles = selectedFiles.filter(f => f.name !== fileName);
                    $(this).closest('.file-item').remove();
                });

                $('#fileList').append(fileItem);
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            }

            // Form submission
            $('#createTicketForm').on('submit', function(e) {
                e.preventDefault();

                // Get CKEditor content
                const description = editor.getData();

                if (!description.trim()) {
                    toastr.error('Please provide a description');
                    return;
                }

                const formData = new FormData(this);
                formData.set('description', description);

                // Handle location based on selection
                const locationType = $('input[name="location_type"]:checked').val();
                if (locationType === 'manual') {
                    formData.delete('location_id');
                    if (!$('#location_manual_input').val()) {
                        toastr.error('Please enter a manual location');
                        return;
                    }
                } else {
                    formData.delete('location_manual');
                    if (!$('#location_id').val()) {
                        toastr.error('Please select a location from the list');
                        return;
                    }
                }

                // Append selected files
                selectedFiles.forEach((file, index) => {
                    formData.append(`attachments[${index}]`, file);
                });

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-2"></i>Creating...');

                $.ajax({
                    url: "{{ route('tickets.store') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                html: `Ticket <strong>#${response.ticket_number}</strong> has been created successfully!`,
                                showConfirmButton: true,
                                confirmButtonText: 'View Ticket',
                                showCancelButton: true,
                                cancelButtonText: 'Create Another'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href =
                                        "{{ route('tickets.index') }}";
                                } else {
                                    location.reload();
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html(originalText);

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            $('.form-control, .form-select, .select2-container .select2-selection--single')
                                .removeClass('is-invalid');

                            $.each(errors, function(key, value) {
                                const element = $(`[name="${key}"]`);
                                element.addClass('is-invalid');

                                // For Select2, add class to container
                                if (element.hasClass('select2-hidden-accessible')) {
                                    element.next('.select2-container').find(
                                        '.select2-selection').addClass('is-invalid');
                                }

                                element.siblings('.invalid-feedback').text(value[0]);
                            });

                            toastr.error('Please check the form for errors');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message ||
                                    'An error occurred while creating the ticket'
                            });
                        }
                    }
                });
            });

            // Remove invalid class on input
            $('.form-control, .form-select').on('input change', function() {
                $(this).removeClass('is-invalid');
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                }
            });
        });
    </script>
@endpush
