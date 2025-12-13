@extends('layouts.app')

@section('title', 'Void Transaction')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card shadow-lg" style="width: 100%; max-width: 600px; margin-top: 50px;">
        <div class="card-body p-4 p-md-5">
            <h2 class="card-title text-center mb-4 text-danger">Void Transaction</h2>
            <p class="text-center text-muted mb-4">
                Silakan masukkan alasan pembatalan dan **password Anda** untuk mengonfirmasi aksi ini.
            </p>
            
            {{-- GENERAL FLASH MESSAGE (Untuk Error Umum atau Error yang tidak terikat field) --}}
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{-- Tampilkan error yang tidak terkait field (seperti error DB atau status) --}}
                    @if ($errors->has('error'))
                        <p class="mb-0">{{ $errors->first('error') }}</p>
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row mb-4 border-bottom pb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Invoice ID:</strong></p>
                    <h5 class="text-primary">INV-TXN-{{ $transaction->id }}</h5>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1"><strong>Sales Amount:</strong></p>
                    <h5 class="text-success">Rp. {{ number_format($transaction->grand_total, 0, ',', '.') }}</h5>
                </div>
                <div class="col-12 mt-2">
                    <small class="text-muted">Transaction Time: {{ $transaction->transaction_date->format('d F Y - H:i') }}</small>
                    <br>
                    <small class="text-muted">Will be Voided By: **{{ Auth::user()->name }}**</small>
                </div>
            </div>

            {{-- Form Void akan POST ke route transactions.void --}}
            <form action="{{ route('transactions.void', $transaction->id) }}" method="POST">
                @csrf
                
                {{-- FIELD ALASAN VOID --}}
                <div class="mb-4">
                    <label for="void_reason" class="form-label fw-bold">Reason for Void <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('void_reason') is-invalid @enderror" 
                              id="void_reason" 
                              name="void_reason" 
                              rows="3" 
                              placeholder="Enter the reason for cancelling this transaction..." 
                              required>{{ old('void_reason') }}</textarea>
                    @error('void_reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- FIELD PASSWORD --}}
                <div class="mb-4">
                    <label for="password" class="form-label fw-bold">Your Password (Confirmation) <span class="text-danger">*</span></label>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           required>
                    
                    {{-- INI ADALAH BLOCK YANG MENAMPILKAN NOTICE JIKA PASSWORD SALAH --}}
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between pt-3">
                    <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> **Confirm Void**
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection