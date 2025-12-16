@extends('layouts.app')

@section('title', 'Transaction Detail')

@section('content')
{{-- Load CSS External --}}
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Flash Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Container Utama --}}
            <div class="form-card p-5">

                {{-- Header Detail (Nomor Invoice & Status) --}}
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h3 class="fw-bold mb-0">Transaction Detail</h3>
                    
                    {{-- Status Badge --}}
                    @if($transaction->status == 'void')
                        <span class="badge bg-danger rounded-pill px-3 py-2">Void</span>
                    @elseif($transaction->status == 'done')
                        <span class="badge bg-success rounded-pill px-3 py-2">Done</span>
                    @else
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">{{ ucfirst($transaction->status) }}</span>
                    @endif
                </div>

                <div class="mb-4">
                    <h5 class="fw-bold">INV-TXN-{{ $transaction->id }}</h5>
                </div>

                {{-- BAGIAN 1: INFORMASI UMUM (Style Label Tipis/Small) --}}
                <div class="row g-4 mb-4">
                    {{-- Transaction Time --}}
                    <div class="col-md-6">
                        <div class="text-muted small fw-bold mb-1">Transaction Time</div>
                        <div class="fw-bold text-dark">
                            {{ $transaction->transaction_date->format('d F Y - H:i:s') }}
                        </div>
                    </div>

                    {{-- Cashier --}}
                    <div class="col-md-6">
                        <div class="text-muted small fw-bold mb-1">Cashier</div>
                        <div class="fw-bold text-dark">
                            {{ $transaction->cashier->name ?? '-' }}
                        </div>
                    </div>

                    {{-- Customer Email --}}
                    <div class="col-md-6">
                        <div class="text-muted small fw-bold mb-1">Customer Email</div>
                        <div class="fw-bold text-dark">
                            {{ $transaction->customer_email ?? '-' }}
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="col-md-6">
                        <div class="text-muted small fw-bold mb-1">Payment Method</div>
                        <div class="fw-bold text-dark">
                            {{ $transaction->payment->method_name ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- Informasi Void (Jika ada) --}}
                @if($transaction->status === 'void')
                    <div class="border-bottom pb-2 mb-3 mt-5">
                        <h5 class="fw-bold text-danger">Void Information</h5>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="text-danger small fw-bold mb-1">Void By</div>
                            <div class="fw-bold text-dark">
                                {{ $transaction->voidBy->name ?? '-' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-danger small fw-bold mb-1">Void Date</div>
                            <div class="fw-bold text-dark">
                                {{ $transaction->void_at ? $transaction->void_at->format('d F Y - H:i') : '-' }}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-danger small fw-bold mb-1">Reason</div>
                            <div class="fw-bold text-dark">
                                {{ $transaction->void_reason }}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- BAGIAN 2: ITEM PRODUK --}}
                <div class="mt-5 mb-3">
                    <h5 class="fw-bold">Items Purchased</h5>
                </div>

                {{-- Header Grid (Style Label Tipis/Small) --}}
                <div class="row text-muted small fw-bold border-bottom pb-2 mb-2">
                    <div class="col-5">Product Name</div>
                    <div class="col-3">Unit Price</div>
                    <div class="col-2 text-center">Quantity</div>
                    <div class="col-2 text-end">Subtotal</div>
                </div>

                {{-- Item Rows --}}
                <div class="mb-4">
                    @foreach($transaction->details as $detail)
                        <div class="row py-2 border-bottom border-light align-items-center">
                            <div class="col-5 fw-bold text-dark">
                                {{ $detail->product->title ?? 'Product Deleted' }}
                            </div>
                            <div class="col-3 fw-bold text-dark">
                                Rp. {{ number_format($detail->price, 0, ',', '.') }}
                            </div>
                            <div class="col-2 text-center fw-bold text-dark">
                                {{ number_format($detail->quantity) }}
                            </div>
                            <div class="col-2 text-end fw-bold text-dark">
                                Rp. {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Grand Total --}}
                <div class="d-flex justify-content-between align-items-center mb-5 mt-4 pt-3 border-top">
                    <span class="fw-bold">Grand Total</span>
                    <span class="fs-4 fw-bold">Rp. {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                </div>

                {{-- BAGIAN 3: TOMBOL AKSI --}}
                <div class="d-flex justify-content-end gap-2">
                    {{-- Tombol Back --}}
                    <a href="{{ route('transactions.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                        Back to List
                    </a>

                    {{-- Tombol Void (Hanya jika status done) --}}
                    @if($transaction->status === 'done')
                        <a href="{{ route('transactions.void.form', $transaction->id) }}" 
                           class="btn btn-danger rounded-pill px-4 fw-bold text-white text-decoration-none">
                            Void Transaction
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection