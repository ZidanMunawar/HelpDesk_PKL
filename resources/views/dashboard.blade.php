@extends('layouts.main')

@section('title', 'Dashboard | ' . config('app.name'))

@section('page-title', 'Dashboard')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'App', 'url' => 'javascript:void(0)'],
            ['title' => 'Dashboard', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Welcome, {{ auth()->user()->name }}!</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Your Account Information</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Name:</th>
                                        <td>{{ auth()->user()->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ auth()->user()->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td>{{ auth()->user()->phone ?? 'Not set' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Role:</th>
                                        <td>
                                            <span class="badge bg-{{ auth()->user()->isAdmin() ? 'danger' : 'primary' }}">
                                                {{ ucfirst(auth()->user()->role) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <span class="badge bg-{{ auth()->user()->isActive() ? 'success' : 'warning' }}">
                                                {{ ucfirst(auth()->user()->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Registered:</th>
                                        <td>{{ auth()->user()->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h5>Note:</h5>
                                <p>This is your main dashboard page. You can customize this page according to your
                                    application needs.</p>
                                @if (!auth()->user()->isActive())
                                    <p class="text-danger mb-0">
                                        <strong>Important:</strong> Your account is currently inactive. Please wait for
                                        administrator approval.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Section -->
    <div class="row">
        <div class="col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-intro-title">Calendar</h4>
                    <div class="">
                        <div id="external-events" class="my-3">
                            <p>Drag and drop your event or click in the calendar</p>
                            <div class="external-event btn-primary light" data-class="bg-primary"><i
                                    class="fa fa-move"></i><span>New Theme Release</span></div>
                            <div class="external-event btn-warning light" data-class="bg-warning"><i
                                    class="fa fa-move"></i>My Event</div>
                            <div class="external-event btn-danger light" data-class="bg-danger"><i
                                    class="fa fa-move"></i>Meet manager</div>
                            <!-- More events -->
                        </div>
                        <div class="checkbox form-check checkbox-event custom-checkbox pt-3 pb-5">
                            <input type="checkbox" class="form-check-input" id="drop-remove">
                            <label class="form-check-label" for="drop-remove">Remove After Drop</label>
                        </div>
                        <a href="javascript:void()" data-bs-toggle="modal" data-bs-target="#add-category"
                            class="btn btn-primary btn-event w-100">
                            <span class="align-middle"><i class="ti-plus me-2"></i></span> Create New
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9">
            <div class="card">
                <div class="card-body">
                    <div id="calendar" class="app-fullcalendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- BEGIN MODAL -->
    <div class="modal fade none-border" id="event-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong>Add New Event</strong></h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success save-event waves-effect waves-light">Create event</button>
                    <button type="button" class="btn btn-danger delete-event waves-effect waves-light"
                        data-bs-dismiss="modal">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Category -->
    <div class="modal fade none-border" id="add-category">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong>Add a category</strong></h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Category Name</label>
                                <input class="form-control form-white" placeholder="Enter name" type="text"
                                    name="category-name">
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Choose Category Color</label>
                                <select class="form-control form-white" data-placeholder="Choose a color..."
                                    name="category-color">
                                    <option value="success">Success</option>
                                    <option value="danger">Danger</option>
                                    <option value="info">Info</option>
                                    <option value="pink">Pink</option>
                                    <option value="primary">Primary</option>
                                    <option value="warning">Warning</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light waves-effect" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light save-category"
                        data-bs-dismiss="modal">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Dashboard specific scripts here
        $(document).ready(function() {
            // Initialize calendar if needed
            // Your dashboard-specific JavaScript code
        });
    </script>
@endpush
