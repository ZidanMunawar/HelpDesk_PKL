@extends('layouts.main')

@section('title', 'Technician Performance | ' . config('app.name'))

@section('page-title', 'Technician Performance')

@section('breadcrumb')
    @php
        $breadcrumb = [
            ['title' => 'Dashboard', 'url' => route('dashboard')],
            ['title' => 'Technician Performance', 'url' => 'javascript:void(0)'],
        ];
    @endphp
    @include('layouts.partials.breadcrumb')
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <style>
        :root {
            --navy: #003366;
            --orange: #ff6600;
            --navy-light: #1e4a7a;
        }

        .stats-summary {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card-summary {
            background: white;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--orange);
        }

        .stat-value-summary {
            font-size: 28px;
            font-weight: 700;
            color: var(--navy);
        }

        .stat-label-summary {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .stat-label-summary i {
            color: var(--orange);
            margin-right: 5px;
        }

        .technician-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
            cursor: pointer;
            border: 1px solid #f0f0f0;
        }

        .technician-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.1);
            border-color: var(--orange);
        }

        .tech-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--navy);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .tech-name {
            font-weight: 600;
            color: var(--navy);
            font-size: 16px;
        }

        .tech-dept {
            font-size: 12px;
            color: #888;
        }

        .progress-bar-custom {
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--orange);
            border-radius: 3px;
        }

        /* Search Section */
        .search-section {
            background: white;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 768px) {
            .stats-summary {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .stat-value-summary {
                font-size: 22px;
            }

            .stat-card-summary {
                padding: 12px;
            }
        }

        @media (max-width: 576px) {
            .stats-summary {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-0">
        <!-- Header Info -->
        <div class="info-card"
            style="background: linear-gradient(135deg, #003366 0%, #1e4a7a 100%); border-radius: 12px; padding: 20px; margin-bottom: 25px; color: white;">
            <div class="d-flex align-items-center" style="gap: 15px; flex-wrap: nowrap;">
                <div style="flex-shrink: 0;">
                    <i class="fas fa-chart-line fa-3x"></i>
                </div>
                <div>
                    <h4 class="mb-1" style="color: white">Technician Performance</h4>
                    <p class="mb-0 opacity-75">Monitor and evaluate technician performance metrics</p>
                </div>
            </div>
        </div>

        {{-- <!-- Search Section (tanpa filter department) -->
        <div class="search-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-label mb-0">Search Technician</label>
                    <input type="text" id="searchTechnician" class="form-control" placeholder="Search by name...">
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <button id="resetSearch" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
            </div>
        </div> --}}

        <!-- Stats Summary Cards -->
        <div class="stats-summary">
            <div class="stat-card-summary">
                <div class="stat-value-summary">{{ $totalTechs }}</div>
                <div class="stat-label-summary"><i class="fas fa-users"></i> Total Technicians</div>
            </div>
            <div class="stat-card-summary">
                <div class="stat-value-summary">{{ $totalTicketsAll }}</div>
                <div class="stat-label-summary"><i class="fas fa-ticket-alt"></i> Total MR</div>
            </div>
            <div class="stat-card-summary">
                <div class="stat-value-summary">{{ $avgRateAll }}%</div>
                <div class="stat-label-summary"><i class="fas fa-check-circle"></i> Avg Completion Rate</div>
            </div>
            <div class="stat-card-summary">
                <div class="stat-value-summary">{{ $avgTimeAll > 0 ? $avgTimeAll . 'h' : '-' }}</div>
                <div class="stat-label-summary"><i class="fas fa-clock"></i> Avg Completion Time</div>
            </div>
        </div>

        <!-- Technicians List -->
        <div class="row" id="techniciansList">
            @foreach ($technicians as $tech)
                <div class="col-md-6 col-lg-4" data-name="{{ strtolower($tech->name) }}">
                    <div class="technician-card"
                        onclick="window.location='{{ route('technician-performance.show', $tech->id) }}'">
                        <div class="d-flex align-items-center mb-3">
                            <div class="tech-avatar me-3">
                                {{ strtoupper(substr($tech->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="tech-name">{{ $tech->name }}</div>
                                <div class="tech-dept">{{ $tech->department->name ?? 'Engineering' }}</div>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                        <div class="row text-center mb-2">
                            <div class="col-4">
                                <div class="fw-bold">{{ $tech->total_tickets ?? 0 }}</div>
                                <small class="text-muted">Total</small>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-success">{{ $tech->completion_rate ?? 0 }}%</div>
                                <small class="text-muted">Rate</small>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold">
                                    {{ $tech->avg_completion_time > 0 ? $tech->avg_completion_time . 'h' : '-' }}
                                </div>
                                <small class="text-muted">Avg Time</small>
                            </div>
                        </div>
                        <div class="progress-bar-custom">
                            <div class="progress-fill" style="width: {{ $tech->completion_rate ?? 0 }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2 small text-muted flex-wrap">
                            <span><i class="fas fa-check-circle text-success"></i>
                                {{ ($tech->completed_count ?? 0) + ($tech->closed_count ?? 0) }} Done</span>
                            <span><i class="fas fa-spinner text-warning"></i> {{ $tech->in_progress_count ?? 0 }}
                                Progress</span>
                            <span><i class="fas fa-ban text-danger"></i> {{ $tech->cancelled_count ?? 0 }} Cancel</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($technicians->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-user-cog fa-4x text-muted mb-3"></i>
                <p class="text-muted">No technicians found.</p>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>

    <script>
        // Search functionality only (no department filter)
        const searchInput = document.getElementById('searchTechnician');
        const resetBtn = document.getElementById('resetSearch');
        const technicianCards = document.querySelectorAll('#techniciansList .col-md-6');

        function filterTechnicians() {
            const searchTerm = searchInput.value.toLowerCase();

            technicianCards.forEach(card => {
                const techName = card.getAttribute('data-name');
                const showBySearch = searchTerm === '' || techName.includes(searchTerm);
                card.style.display = showBySearch ? '' : 'none';
            });
        }

        searchInput.addEventListener('keyup', filterTechnicians);
        resetBtn.addEventListener('click', () => {
            searchInput.value = '';
            filterTechnicians();
        });

        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
    </script>
@endpush
