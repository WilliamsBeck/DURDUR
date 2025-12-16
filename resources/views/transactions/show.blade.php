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
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Container Utama --}}
            <div class="form-card">

                {{-- Header Detail (Nomor Invoice & Status) --}}
                <div class="detail-header-info align-items-center">
                    <div>
                        <h3>Transaction Detail</h3>
                        <div class="invoice-number">INV-TXN-{{ $transaction->id }}</div>
                    </div>
                    
                    {{-- Status Badge --}}
                    <div>
                        <span class="badge rounded-pill px-3 py-2 
                            {{ $transaction->status === 'done' ? 'bg-success' : ($transaction->status === 'void' ? 'bg-danger' : 'bg-warning text-dark') }}"
                            style="font-size: 0.9rem;">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>

                {{-- BAGIAN 1: INFORMASI UMUM (Text Only) --}}
                <div class="row g-4 mb-5">
                    {{-- Transaction Date --}}
                    <div class="col-md-6">
                        <div class="form-label mb-1">Transaction Time</div>
                        <div class="fw-bold text-dark">
                            {{ $transaction->transaction_date->format('d F Y - H:i:s') }}
                        </div>
                    </div>

                    {{-- Cashier --}}
                    <div class="col-md-6">
                        <div class="form-label mb-1">Cashier</div>
                        <div class="fw-bold text-dark">
                            {{ $transaction->cashier->name ?? '-' }}
                        </div>
                    </div>

                    {{-- Customer Email --}}
                    <div class="col-md-6">
                        <div class="form-label mb-1">Customer Email</div>
                        <div class="fw-bold text-dark">
                            {{ $transaction->customer_email ?? '-' }}
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="col-md-6">
                        <div class="form-label mb-1">Payment Method</div>
                        <div class="fw-bold text-dark">
                            {{ $transaction->payment->method_name ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- Informasi Void (Jika ada - Text Only) --}}
                @if($transaction->status === 'void')
                    <div class="product-section-title text-danger">Void Information</div>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <div class="form-label text-danger mb-1">Void By</div>
                            <div class="fw-bold text-dark">
                                {{ $transaction->voidBy->name ?? '-' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-label text-danger mb-1">Void Date</div>
                            <div class="fw-bold text-dark">
                                {{ $transaction->void_at ? $transaction->void_at->format('d F Y - H:i') : '-' }}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-label text-danger mb-1">Reason</div>
                            <div class="fw-bold text-dark">
                                {{ $transaction->void_reason }}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- BAGIAN 2: ITEM PRODUK --}}
                <div class="product-section-title">Items Purchased</div>

                {{-- Header Grid --}}
                <div id="product-rows-header">
                    <div>Product Name</div>         {{-- 4fr --}}
                    <div>Unit Price</div>           {{-- 2fr --}}
                    <div>Quantity</div>             {{-- 2fr --}}
                    <div>Subtotal</div>             {{-- 2fr --}}
                    <div></div>                     {{-- 0.5fr --}}
                </div>

                {{-- Item Rows --}}
                @foreach($transaction->details as $detail)
                    <div class="product-row">
                        {{-- Product Name --}}
                        <div class="fw-bold text-dark">
                            {{ $detail->product->title ?? 'Product Deleted' }}
                        </div>

                        {{-- Unit Price --}}
                        <div class="unit-price-text">
                            Rp. {{ number_format($detail->price, 0, ',', '.') }}
                        </div>

                        {{-- Quantity (Tetap pakai input readonly kecil agar rapi di tengah) --}}
                        <div>
                            <input type="text" class="form-control text-center bg-white border-0 fw-bold" value="{{ $detail->quantity }}" readonly style="width: 60px; padding: 0.4rem;">
                        </div>

                        {{-- Subtotal --}}
                        <div class="subtotal-text">
                            Rp. {{ number_format($detail->subtotal, 0, ',', '.') }}
                        </div>

                        {{-- Kosong --}}
                        <div></div>
                    </div>
                @endforeach

                {{-- Grand Total --}}
                <div class="detail-grand-total">
                    <span>Grand Total</span>
                    <span>Rp. {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
                </div>

                {{-- BAGIAN 3: TOMBOL AKSI --}}
                <div class="form-actions mt-5">
                    {{-- Tombol Back --}}
                    <a href="{{ route('transactions.index') }}" class="btn-cancel text-decoration-none">
                        Back to List
                    </a>

                    {{-- Tombol Void (Hanya jika status done) --}}
                    @if($transaction->status === 'done')
                        <a href="{{ route('transactions.void.form', $transaction->id) }}" 
                           class="btn-save text-decoration-none text-white" 
                           style="background-color: #dc3545; color: white;">
                            Void Transaction
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection