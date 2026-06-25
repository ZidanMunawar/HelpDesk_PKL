{{-- resources/views/signature/index.blade.php --}}
@extends('layouts.main')

@section('title', 'Digital Signature | ' . config('app.name'))

@section('page-title', 'Digital Signature')

@section('content')
    <div class="signature-card">
        <!-- Header -->
        <div class="signature-header">
            <h3>
                <i class="fas fa-signature"></i>
                Digital Signature
            </h3>
            <p>Create and manage your digital signature for ticket approvals</p>
        </div>

        <!-- Status Bar -->
        <div class="status-section">
            <div class="status-info">
                <div class="status-badge {{ $user->has_signature ? 'uploaded' : 'not-uploaded' }}">
                    <i class="fas {{ $user->has_signature ? 'fa-check-circle' : 'fa-exclamation-circle' }}"></i>
                    {{ $user->has_signature ? 'Uploaded' : 'No Signature' }}
                </div>
                @if ($user->has_signature)
                    <div class="last-updated">
                        <i class="fas fa-clock"></i>
                        Last updated: {{ \Carbon\Carbon::parse($user->signature_updated_at)->format('d M Y H:i') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Preview Section -->
        <div class="preview-section">
            <div class="preview-title">
                <i class="fas fa-eye"></i>
                Signature Preview
            </div>
            <div class="preview-box" id="previewBox">
                @if ($user->has_signature && $user->signature_url)
                    <img src="{{ $user->signature_url }}" alt="Digital Signature" id="previewImage">
                @else
                    <div class="preview-placeholder">
                        <i class="fas fa-signature"></i>
                        <p>No signature uploaded yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Draw Button -->
        <div class="draw-btn-container">
            <button type="button" class="btn-draw" id="createSignatureBtn">
                <i class="fas fa-pen"></i>
                Draw New Signature
            </button>
        </div>

        <!-- Form Section -->
        <div class="form-section">
            <div class="alert-security">
                <i class="fas fa-shield-alt"></i>
                <div>
                    <strong>Security Verification</strong>
                    <p>Please enter your password to confirm changes to your signature</p>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Your Password <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password" placeholder="Enter your password to continue"
                    autocomplete="off">
                <div class="invalid-feedback" id="passwordError"></div>
            </div>

            <div class="action-buttons">
                <button type="button" class="btn-save" id="saveBtn" disabled>
                    <i class="fas fa-save"></i>
                    Save
                </button>
                @if ($user->has_signature)
                    <button type="button" class="btn-remove" id="removeBtn">
                        <i class="fas fa-trash"></i>
                        Remove
                    </button>
                @endif
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <div class="instructions-title">
                    <i class="fas fa-info-circle"></i>
                    Drawing Tips
                </div>
                <ul class="instructions-list">
                    <li><i class="fas fa-check"></i> Use finger or stylus to draw</li>
                    <li><i class="fas fa-check"></i> Sign with black ink for best results</li>
                    <li><i class="fas fa-check"></i> Background will be transparent automatically</li>
                    <li><i class="fas fa-check"></i> Make sure signature is clear and legible</li>
                    <li><i class="fas fa-check"></i> Use Undo/Redo to correct mistakes</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ========== SIGNATURE PAD MODAL - UKURAN 300x200 DENGAN KOTAK-KOTAK ========== -->
    <div class="modal fade" id="signaturePadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: white;">
                        <i class="fas fa-pen me-2" style="color: var(--orange);"></i>
                        Draw Your Signature
                    </h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="signature-pad-container">
                        <div class="signature-pad-wrapper">
                            <canvas id="modalSignaturePad" class="signature-pad" width="300" height="200"></canvas>
                        </div>

                        <div class="signature-actions">
                            <button type="button" class="btn btn-outline-secondary" id="modalClearBtn">
                                <i class="fas fa-eraser me-2"></i>Clear
                            </button>
                            <button type="button" class="btn btn-outline-navy" id="modalUndoBtn" disabled>
                                <i class="fas fa-undo me-2"></i>Undo
                            </button>
                            <button type="button" class="btn btn-outline-success" id="modalRedoBtn" disabled>
                                <i class="fas fa-redo me-2"></i>Redo
                            </button>
                        </div>
                    </div>

                    {{-- <div class="signature-requirements mt-3 p-3 bg-light rounded">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle mt-1" style="color: var(--orange);"></i>
                            <div class="ms-2">
                                <strong class="small">Instructions:</strong>
                                <ul class="small text-muted mb-0 mt-1 ps-3">
                                    <li>Draw your signature in the box above with black ink</li>
                                    <li>Use clear and legible handwriting</li>
                                    <li><strong>Background kotak-kotak menandakan area transparan</strong></li>
                                    <li>Optimal size: 300 x 200 pixels</li>
                                    <li>Click "Use Signature" when you're satisfied</li>
                                </ul>
                            </div>
                        </div>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-orange" id="modalUseSignatureBtn">
                        <i class="fas fa-check me-2"></i>Use Signature
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">
    <style>
        :root {
            --navy: #003366;
            --navy-light: #e6f0ff;
            --orange: #ff6600;
            --orange-light: #fff0e6;
            --gray-bg: #f8f9fa;
            --gray-border: #e9ecef;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --white: #ffffff;
        }

        /* Card Utama */
        .signature-card {
            background: var(--white);
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 51, 102, 0.06);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        /* Header Card */
        .signature-header {
            background: linear-gradient(135deg, var(--navy) 0%, #002244 100%);
            padding: 2rem;
            color: var(--white);
            position: relative;
        }

        .signature-header h3 {
            font-weight: 600;
            margin: 0;
            font-size: 1.5rem;
            color: var(--white);
        }

        .signature-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .signature-header i {
            color: var(--orange);
            margin-right: 0.5rem;
        }

        /* Status Section */
        .status-section {
            background: var(--white);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--gray-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .status-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 500;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge.uploaded {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.not-uploaded {
            background: #fee2e2;
            color: #991b1b;
        }

        .last-updated {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .last-updated i {
            color: var(--orange);
            margin-right: 0.25rem;
        }

        /* Preview Section */
        .preview-section {
            padding: 2rem;
            border-bottom: 1px solid var(--gray-border);
        }

        .preview-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 1rem;
        }

        .preview-title i {
            color: var(--orange);
            margin-right: 0.5rem;
        }

        .preview-box {
            background: var(--gray-bg);
            border: 2px dashed var(--orange);
            border-radius: 1rem;
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 150px;
        }

        .preview-box img {
            max-width: 100%;
            max-height: 120px;
            object-fit: contain;
        }

        .preview-placeholder {
            text-align: center;
            color: var(--text-muted);
        }

        .preview-placeholder i {
            font-size: 3rem;
            color: var(--orange);
            opacity: 0.3;
            margin-bottom: 0.5rem;
        }

        .preview-placeholder p {
            margin: 0;
            font-size: 0.875rem;
        }

        /* Draw Button */
        .draw-btn-container {
            padding: 0 2rem 2rem 2rem;
        }

        .btn-draw {
            background: var(--navy);
            color: var(--white);
            border: none;
            padding: 1rem;
            border-radius: 1rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            cursor: pointer;
        }

        .btn-draw:hover {
            background: #002244;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 51, 102, 0.3);
        }

        .btn-draw i {
            color: var(--orange);
            font-size: 1.25rem;
        }

        /* Form Section */
        .form-section {
            padding: 0 2rem 2rem 2rem;
        }

        .alert-security {
            background: var(--navy-light);
            border-left: 4px solid var(--navy);
            padding: 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .alert-security i {
            color: var(--orange);
            font-size: 1.25rem;
        }

        .alert-security strong {
            color: var(--navy);
            display: block;
            margin-bottom: 0.25rem;
        }

        .alert-security p {
            color: var(--text-dark);
            margin: 0;
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            font-weight: 500;
            color: var(--navy);
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.875rem;
        }

        .form-control {
            border: 1.5px solid var(--gray-border);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.3s;
            font-size: 0.875rem;
        }

        .form-control:focus {
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(255, 102, 0, 0.1);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-save {
            background: var(--orange);
            color: var(--white);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 2rem;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .btn-save:hover:not(:disabled) {
            background: #e65c00;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 102, 0, 0.3);
        }

        .btn-save:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-remove {
            background: var(--white);
            border: 1.5px solid #dc3545;
            color: #dc3545;
            padding: 0.75rem 2rem;
            border-radius: 2rem;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .btn-remove:hover {
            background: #dc3545;
            color: var(--white);
        }

        /* Instructions */
        .instructions {
            margin-top: 2rem;
            padding: 1rem;
            background: var(--gray-bg);
            border-radius: 0.75rem;
        }

        .instructions-title {
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .instructions-title i {
            color: var(--orange);
        }

        .instructions-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.5rem;
        }

        .instructions-list li {
            color: var(--text-dark);
            font-size: 0.8125rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .instructions-list li i {
            color: var(--orange);
            font-size: 0.75rem;
            width: 1rem;
        }

        /* ========== SIGNATURE PAD MODAL STYLES - UKURAN 300x200 ========== */
        .btn-orange {
            background: var(--orange);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-orange:hover {
            background: #cc5200;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 102, 0, 0.3);
        }

        .btn-outline-navy {
            background: transparent;
            color: var(--navy);
            border: 2px solid var(--navy);
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-outline-navy:hover {
            background: var(--navy);
            color: white;
        }

        .btn-outline-secondary {
            background: transparent;
            color: #6c757d;
            border: 2px solid #6c757d;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }

        .btn-outline-success {
            background: transparent;
            color: #28a745;
            border: 2px solid #28a745;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-outline-success:hover {
            background: #28a745;
            color: white;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Signature Pad - UKURAN 300x200 DENGAN KOTAK-KOTAK */
        .signature-pad-container {
            background: white;
            border: 2px dashed var(--orange);
            border-radius: 1rem;
            padding: 20px;
            margin-bottom: 20px;
        }

        .signature-pad-wrapper {
            background: white;
            border-radius: 0.75rem;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid var(--gray-border);
            padding: 10px;
            /* Background kotak-kotak untuk menunjukkan transparansi */
            background-color: white;
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }

        .signature-pad {
            width: 100%;
            height: auto;
            aspect-ratio: 300/200;
            background: transparent;
            /* Transparan biar keliatan kotak-kotak di belakang */
            cursor: crosshair;
            display: block;
            max-width: 300px;
            margin: 0 auto;
            touch-action: none;
            border-radius: 4px;
            box-shadow: 2px 2px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .signature-actions {
            display: flex;
            gap: 12px;
            margin-top: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .signature-actions .btn {
            min-width: 100px;
        }

        /* Modal customization */
        .modal-content {
            border: none;
            border-radius: 1.5rem;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--navy) 0%, #002244 100%);
            color: white;
            padding: 1.5rem;
            border-bottom: none;
        }

        .modal-header .modal-title {
            color: white;
            font-size: 1.25rem;
        }

        .modal-header .btn-close {
            background: var(--navy);
            color: var(--orange);
            opacity: 1;
            padding: 0.75rem;
            border-radius: 50%;
        }

        .modal-header .btn-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .btn-close-custom {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.1rem;
            padding: 0;
            margin: 0;
        }

        .btn-close-custom:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .btn-close-custom i {
            font-size: 1.1rem;
        }


        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--gray-border);
        }

        .signature-requirements {
            background: var(--navy-light) !important;
            border-left: 4px solid var(--navy);
        }

        /* Loading Spinner */
        .spinner-sm {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: var(--white);
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive Breakpoints */
        @media (max-width: 768px) {

            .signature-header,
            .status-section,
            .preview-section,
            .draw-btn-container,
            .form-section {
                padding: 1.5rem;
            }

            .status-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-save,
            .btn-remove {
                width: 100%;
                justify-content: center;
            }

            .instructions-list {
                grid-template-columns: 1fr;
            }

            /* Modal adjustments */
            .modal-body {
                padding: 1.5rem;
            }

            .signature-pad-container {
                padding: 12px;
            }

            .signature-actions {
                flex-wrap: wrap;
            }

            .signature-actions .btn {
                flex: 1 1 auto;
                min-width: 80px;
                padding: 0.5rem 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .signature-pad-container {
                padding: 10px;
            }

            .signature-actions {
                gap: 8px;
            }

            .signature-actions .btn {
                font-size: 0.75rem;
                padding: 0.5rem 0.5rem;
                min-width: 70px;
            }

            .signature-actions .btn i {
                margin-right: 4px;
                font-size: 0.8rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script src="{{ asset('assets/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Toastr Config
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": "5000",
                "closeButton": true,
                "progressBar": true,
                "preventDuplicates": true
            };

            // ============ SIGNATURE PAD MODAL DENGAN UKURAN 300x200 ============
            let modalSignaturePad;
            let undoStack = [];
            let redoStack = [];

            // Open Signature Pad Modal
            $('#createSignatureBtn').on('click', function() {
                $('#signaturePadModal').modal('show');
            });

            // Initialize Signature Pad when modal is shown
            // Initialize Signature Pad when modal is shown
            $('#signaturePadModal').on('shown.bs.modal', function() {
                const canvas = document.getElementById('modalSignaturePad');

                // Set ukuran fixed 300x200
                canvas.width = 300;
                canvas.height = 200;

                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // Reset stacks
                undoStack = [];
                redoStack = [];

                // Initialize SignaturePad dengan background TRANSPARAN
                modalSignaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgba(0,0,0,0)', // TRANSPARAN PENTING!
                    penColor: 'rgb(0, 0, 0)',
                    velocityFilterWeight: 0.7,
                    minWidth: 0.5,
                    maxWidth: 1.8,
                    throttle: 16,
                    dotSize: 0.4,
                    minDistance: 1
                });

                // Touch optimization
                canvas.addEventListener('touchstart', (e) => {
                    e.preventDefault();
                }, {
                    passive: false
                });

                canvas.addEventListener('touchmove', (e) => {
                    e.preventDefault();
                }, {
                    passive: false
                });

                // Fungsi untuk menyimpan state ke undo stack
                function saveState() {
                    if (modalSignaturePad.isEmpty()) {
                        // Jika kosong, simpan empty array sebagai state
                        undoStack.push([]);
                    } else {
                        // Simpan data signature
                        const data = modalSignaturePad.toData();
                        // Deep clone untuk menghindari referensi
                        undoStack.push(JSON.parse(JSON.stringify(data)));
                    }
                    redoStack = [];
                    updateButtons();
                }

                // Simpan state awal (kosong)
                saveState();

                // Simpan state setiap kali selesai stroke
                modalSignaturePad.addEventListener('endStroke', () => {
                    saveState();
                });

                // Clear button
                $('#modalClearBtn').off('click').on('click', function() {
                    modalSignaturePad.clear();
                    undoStack = [];
                    redoStack = [];
                    // Simpan state kosong setelah clear
                    saveState();
                    updateButtons();
                });

                // Undo button
                $('#modalUndoBtn').off('click').on('click', function() {
                    if (undoStack.length > 1) { // Minimal harus ada 1 state tersisa
                        // Pindahkan state terakhir ke redo stack
                        const lastState = undoStack.pop();
                        redoStack.push(lastState);

                        // Ambil state sebelumnya
                        const previousState = undoStack[undoStack.length - 1];

                        if (previousState && previousState.length > 0) {
                            modalSignaturePad.fromData(previousState);
                        } else {
                            modalSignaturePad.clear();
                        }

                        updateButtons();
                    }
                });

                // Redo button
                $('#modalRedoBtn').off('click').on('click', function() {
                    if (redoStack.length > 0) {
                        // Ambil state dari redo stack
                        const redoState = redoStack.pop();

                        // Kembalikan ke undo stack
                        undoStack.push(redoState);

                        if (redoState && redoState.length > 0) {
                            modalSignaturePad.fromData(redoState);
                        } else {
                            modalSignaturePad.clear();
                        }

                        updateButtons();
                    }
                });

                // Update button states
                function updateButtons() {
                    $('#modalUndoBtn').prop('disabled', undoStack.length <=
                        1); // Minimal 1 state untuk undo
                    $('#modalRedoBtn').prop('disabled', redoStack.length === 0);
                }

                // Disable buttons initially
                updateButtons();
            });
            // Clean up on modal hide
            $('#signaturePadModal').on('hidden.bs.modal', function() {
                if (modalSignaturePad) {
                    modalSignaturePad.clear();
                }
                $('#saveBtn').data('signature-file', null);
                undoStack = [];
                redoStack = [];
            });

            // Use signature - Output tetap 300x200 dengan background transparan
            $('#modalUseSignatureBtn').on('click', function() {
                if (!modalSignaturePad || modalSignaturePad.isEmpty()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Empty Signature',
                        text: 'Please draw your signature first',
                        confirmButtonColor: '#ff6600'
                    });
                    return;
                }

                // Buat canvas baru dengan ukuran 300x200
                const canvas = document.getElementById('modalSignaturePad');
                const outputCanvas = document.createElement('canvas');
                outputCanvas.width = 300;
                outputCanvas.height = 200;
                const outputCtx = outputCanvas.getContext('2d');

                // Clear dengan transparan
                outputCtx.clearRect(0, 0, outputCanvas.width, outputCanvas.height);

                // Gambar signature dari modalCanvas ke outputCanvas
                outputCtx.drawImage(canvas, 0, 0, 300, 200);

                // Convert ke PNG dengan background transparan
                outputCanvas.toBlob(function(blob) {
                    const file = new File([blob], 'signature.png', {
                        type: 'image/png'
                    });

                    $('#saveBtn').data('signature-file', file);
                    $('#saveBtn').prop('disabled', false);

                    // Show preview dengan background pattern untuk menunjukkan transparansi
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewBox').html(`
                            <div style="position: relative; display: inline-block;">
                                <img src="${e.target.result}" alt="New Signature" id="previewImage" style="max-width: 100%; max-height: 120px;">
                            </div>
                        `);
                    };
                    reader.readAsDataURL(blob);

                    $('#signaturePadModal').modal('hide');
                    toastr.success('Signature ready to save. Enter password and click Save.');
                }, 'image/png');
            });

            // ============ MAIN PAGE SAVE ============
            $('#saveBtn').on('click', function() {
                const password = $('#password').val();
                const signatureFile = $(this).data('signature-file');

                if (!password) {
                    $('#password').addClass('is-invalid');
                    $('#passwordError').text('Password is required');
                    return;
                }

                if (!signatureFile) {
                    toastr.error('Please draw your signature first');
                    return;
                }

                const formData = new FormData();
                formData.append('signature', signatureFile);
                formData.append('password', password);
                formData.append('_token', '{{ csrf_token() }}');

                const $btn = $(this);
                const originalText = $btn.html();
                $btn.prop('disabled', true).html('<span class="spinner-sm me-2"></span>Saving...');

                $.ajax({
                    url: "{{ route('signature.upload') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html(originalText);
                        $('#saveBtn').data('signature-file', null);

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            if (errors?.password) {
                                $('#password').addClass('is-invalid');
                                $('#passwordError').text(errors.password[0]);
                            } else if (errors?.signature) {
                                toastr.error(errors.signature[0]);
                            } else {
                                toastr.error(xhr.responseJSON?.message || 'Validation error');
                            }
                        } else {
                            toastr.error(xhr.responseJSON?.message ||
                                'Failed to save signature');
                        }
                    }
                });
            });

            // Remove signature
            $('#removeBtn').on('click', function() {
                const password = $('#password').val();

                if (!password) {
                    $('#password').addClass('is-invalid');
                    $('#passwordError').text('Password is required');
                    return;
                }

                Swal.fire({
                    title: 'Remove Signature?',
                    text: 'Are you sure you want to remove your digital signature?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        return $.ajax({
                                url: "{{ route('signature.remove') }}",
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    password: password
                                }
                            }).then(response => response)
                            .catch(error => {
                                Swal.showValidationMessage(
                                    error.responseJSON?.message || 'Invalid password'
                                );
                            });
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value?.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Removed!',
                            text: result.value.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            });

            // Clear validation on input
            $('#password').on('input', function() {
                $(this).removeClass('is-invalid');
                $('#passwordError').text('');

                if (!$('#saveBtn').data('signature-file')) {
                    $('#saveBtn').prop('disabled', true);
                }
            });

            // ESC key for modal
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#signaturePadModal').hasClass('show')) {
                    $('#signaturePadModal').modal('hide');
                }
            });
        });
    </script>
@endpush
