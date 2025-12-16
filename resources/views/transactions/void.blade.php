@extends('layouts.app')

@section('title', 'Void Transaction')

@section('content')
{{-- Load CSS External --}}
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- Container Utama --}}
            <div class="form-card">
                
                <h3>Void Transaction</h3>

                <form id="voidTransactionForm" action="{{ route('transactions.void', $transaction->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="password" id="hidden_password">

                    {{-- Invoice ID --}}
                    <div class="mb-4">
                        <h5 class="fw-bold">INV-TXN-{{ $transaction->id }}</h5>
                    </div>

                    <div class="row g-4">
                        {{-- Transaction Time (Teks Langsung) --}}
                        <div class="col-md-6">
                            <div class="form-label mb-1">Transaction Time</div>
                            <div class="fw-bold text-dark">
                                {{ $transaction->transaction_date->format('d F Y - H:i:s') }}
                            </div>
                        </div>
                        
                        {{-- Void By (Teks Langsung) --}}
                        <div class="col-md-6">
                            <div class="form-label mb-1">Void By</div>
                            <div class="fw-bold text-dark">
                                {{ Auth::user()->name }}
                            </div>
                        </div>

                        {{-- Sales Amount (Teks Langsung & Lebih Besar) --}}
                        <div class="col-12">
                            <div class="form-label mb-1">Sales Amount</div>
                            <div class="fw-bold text-dark fs-5">
                                Rp. {{ number_format($transaction->grand_total, 0, ',', '.') }}
                            </div>
                        </div>

                        {{-- Reason Field (Input tetap ada) --}}
                        <div class="col-12">
                            <label class="form-label">Reason</label>
                            <textarea name="void_reason" class="form-control" rows="3" placeholder="Type reason here..." required>{{ old('void_reason') }}</textarea>
                            @error('void_reason')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="form-actions">
                        {{-- Tombol Cancel --}}
                        <a href="{{ route('transactions.index') }}" class="btn-cancel">Cancel</a>
                        
                        {{-- Tombol Void --}}
                        <button type="button" class="btn-save" style="background-color: #a69dee; color: #000;" onclick="showPasswordPopup()">
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

        // 2. SweetAlert Password Popup
        Swal.fire({
            html: `
                <div class="void-icon-bg">
                    <span class="void-icon-text">!</span>
                </div>
                <h4 style="font-weight:700; margin-bottom:10px; color:#000;">Authentication Required</h4>
                <p style="color:#666; margin-bottom:5px; font-size:0.95rem;">
                    Please enter your <strong>password</strong> to continue.
                </p>
                
                {{-- Input Password --}}
                <input type="password" id="swal_password" class="swal-custom-input" placeholder="Password">
            `,
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
            
            // Custom Classes
            customClass: {
                popup: 'swal-void-popup',
                confirmButton: 'btn-swal-confirm',
                cancelButton: 'btn-swal-cancel',
                actions: 'swal2-actions'
            },
            buttonsStyling: false,
            focusConfirm: false,
            
            // Logika Submit
            preConfirm: () => {
                const password = Swal.getPopup().querySelector('#swal_password').value;
                if (!password) {
                    Swal.showValidationMessage('Password is required');
                }
                return { password: password };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Set password & Submit
                document.getElementById('hidden_password').value = result.value.password;
                document.getElementById('voidTransactionForm').submit();
            }
        });
    }

    // Error Handling
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