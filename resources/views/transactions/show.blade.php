@extends('layouts.app')

@section('content')
<div class="container">

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <h5>Transaction Detail #{{ $transaction->id }}</h5>
            
            <div>
                <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>

                {{-- TOMBOL VOID (Hanya muncul jika status 'done') --}}
                @if($transaction->status === 'done')
                    <a href="{{ route('transactions.void.form', $transaction->id) }}"
                       class="btn btn-sm btn-danger">
                        <i class="bi bi-x-circle"></i> Void Transaction
                    </a>
                @endif

                {{-- Tampilkan status void jika sudah dibatalkan --}}
                @if($transaction->status === 'void')
                    <button class="btn btn-sm btn-danger disabled">
                        <i class="bi bi-slash-circle"></i> Transaction Voided
                    </button>
                @endif
            </div>
        </div>

        <div class="card-body">
            <h6 class="border-bottom pb-2 mb-3">General Information</h6>
            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Invoice ID:</strong> INV-TXN-{{ $transaction->id }}</p>
                    <p class="mb-1"><strong>Date:</strong> {{ $transaction->transaction_date->format('d F Y H:i') }}</p>
                    <p class="mb-1"><strong>Cashier:</strong> {{ $transaction->cashier->name ?? '-' }}</p>
                    <p class="mb-1"><strong>Payment Method:</strong> {{ $transaction->payment->method_name ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Customer Email:</strong> {{ $transaction->customer_email ?? '-' }}</p>
                    <p class="mb-1"><strong>Grand Total:</strong> <span class="fw-bold text-success">Rp. {{ number_format($transaction->grand_total, 0, ',', '.') }}</span></p>
                    <p class="mb-1"><strong>Status:</strong>
                        @php
                            $badgeClass = $transaction->status === 'done' ? 'bg-success' : ($transaction->status === 'void' ? 'bg-danger' : 'bg-warning text-dark');
                        @endphp
                        <span class="badge {{ $badgeClass }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </p>
                </div>
            </div>
            
            @if($transaction->status === 'void')
                <h6 class="border-bottom pb-2 mb-3 text-danger">Void Information</h6>
                <div class="alert alert-light border p-3">
                    <p class="mb-1"><strong>Void By:</strong> {{ $transaction->voidBy->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Void At:</strong> {{ $transaction->void_at->format('d F Y H:i') }}</p>
                    <p class="mb-0"><strong>Reason:</strong> {{ $transaction->void_reason ?? 'No Reason Provided' }}</p>
                </div>
            @endif


            <h6 class="border-bottom pb-2 mb-3 mt-4">Product Details</h6>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->details as $detail)
                        <tr>
                            <td>{{ $detail->product->title ?? 'N/A' }}</td>
                            <td>Rp. {{ number_format($detail->price, 0, ',', '.') }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>Rp. {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection