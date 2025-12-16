@extends('layouts.app')

@section('title', 'Void Purchase')

@section('content')
{{-- Load CSS External Form --}}
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- Container Utama (.form-card) --}}
            <div class="form-card">
                
                {{-- Judul Halaman --}}
                <h3>Void Purchase Transaction</h3>

                <form id="voidPurchaseForm" action="{{ route('purchases.void', $purchase->id) }}" method="POST">
                    @csrf
                    {{-- Hidden Input Password (diisi via JS SweetAlert) --}}
                    <input type="hidden" name="password" id="hidden_password">

                    {{-- Invoice ID --}}
                    <div class="mb-4">
                        <h5 class="fw-bold">PO-TXN-{{ $purchase->id }}</h5>
                    </div>

                    {{-- Peringatan Stok (Jika status DONE) --}}
                    @if ($purchase->status == 'done')
                        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <i class="fas fa-exclamation-triangle me-2"></i> 
                            <strong>Warning:</strong> Voiding this transaction will <u>reverse (reduce)</u> stock inventory.
                        </div>
                    @else
                        <div class="alert alert-warning border-0 shadow-sm mb-4 text-dark" style="border-radius: 12px;">
                            <i class="fas fa-info-circle me-2"></i> 
                            Transaction is PENDING. No stock changes will occur.
                        </div>
                    @endif

                    <div class="row g-4">
                        {{-- Transaction Date --}}
                        <div class="col-md-6">
                            <div class="form-label mb-1">Transaction Date</div>
                            <div class="fw-bold text-dark">
                                {{ $purchase->created_at->format('d F Y - H:i:s') }}
                            </div>
                        </div>
                        
                        {{-- Supplier --}}
                        <div class="col-md-6">
                            <div class="form-label mb-1">Supplier</div>
                            <div class="fw-bold text-dark">
                                {{ $purchase->supplier->supplier_name }}
                            </div>
                        </div>

                        {{-- Total Cost --}}
                        <div class="col-12">
                            <div class="form-label mb-1">Total Cost</div>
                            <div class="fw-bold text-dark fs-5">
                                Rp. {{ number_format($purchase->total_cost, 0, ',', '.') }}
                            </div>
                        </div>

                        {{-- Reason Field --}}
                        <div class="col-12">
                            <label class="form-label">Reason</label>
                            <textarea name="void_reason" class="form-control" rows="3" placeholder="Type reason here..." required>{{ old('void_reason') }}</textarea>
                            @error('void_reason')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="form-actions mt-4 pt-3 border-top">
                        {{-- Tombol Cancel --}}
                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn-cancel text-decoration-none">
                            Cancel
                        </a>
                        
                        {{-- Tombol Void --}}
                        <button type="button" class="btn-save" style="background-color: #a69dee; color: #fff;" onclick="showPasswordPopup()">
                            Void
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showPasswordPopup() {
        // 1. Validasi Reason
        const reasonInput = document.querySelector('[name="void_reason"]');
        if (!reasonInput.value.trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Reason',
                text: 'Please enter a reason before voiding.',
                confirmButtonColor: '#000'
            });
            return;
        }

        // 2. SweetAlert Password Popup (Custom Style Ungu)
        Swal.fire({
            html: `
                <div style="width: 90px; height: 90px; background-color: #a69dee33; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                    <span style="font-size: 3.5rem; font-weight: 700; color: #a69dee; line-height: 1;">!</span>
                </div>
                <h4 style="font-weight:700; margin-bottom:10px; color:#000;">Authentication Required</h4>
                <p style="color:#666; margin-bottom:5px; font-size:0.95rem;">
                    Please enter your <strong>password</strong> to confirm voiding PO-TXN-{{ $purchase->id }}.
                </p>
                
                {{-- Input Password Manual --}}
                <input type="password" id="swal_password" class="form-control text-center" 
                       style="background-color: #f8f9fa; border-radius: 12px; padding: 12px; margin: 20px auto; width: 80%; display: block;" 
                       placeholder="Password">
            `,
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
            
            // Inline styling via customClass / didOpen karena tidak boleh CSS internal
            buttonsStyling: false,
            customClass: {
                popup: 'p-5 rounded-4 shadow-lg',
                confirmButton: 'btn btn-lg text-white mx-2',
                cancelButton: 'btn btn-lg btn-light mx-2'
            },
            didOpen: () => {
                const confirmBtn = Swal.getConfirmButton();
                confirmBtn.style.backgroundColor = '#a69dee';
                confirmBtn.style.borderRadius = '12px';
                
                const cancelBtn = Swal.getCancelButton();
                cancelBtn.style.borderRadius = '12px';
            },
            focusConfirm: false,
            
            // Logika Pre-Confirm
            preConfirm: () => {
                const password = Swal.getPopup().querySelector('#swal_password').value;
                if (!password) {
                    Swal.showValidationMessage('Password is required');
                }
                return { password: password };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Set password ke hidden input & Submit
                document.getElementById('hidden_password').value = result.value.password;
                document.getElementById('voidPurchaseForm').submit();
            }
        });
    }

    // Error Handling dari Backend (Password Salah)
    @if($errors->has('password'))
        Swal.fire({
            icon: 'error',
            title: 'Authentication Failed',
            text: '{{ $errors->first('password') }}',
            confirmButtonColor: '#000'
        });
    @endif
</script>
@endpush