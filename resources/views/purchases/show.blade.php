@extends('layouts.app')

@section('title', 'Purchase Detail')

@section('content')
{{-- Load CSS External Form --}}
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Container Utama (.form-card) --}}
            <div class="form-card">

                {{-- Header Detail (Title & Status) --}}
                <div class="detail-header-info align-items-center">
                    <div>
                        <h3>Purchase Detail</h3>
                        <div class="invoice-number">PO-TXN-{{ $purchase->id }}</div>
                    </div>
                    
                    {{-- Status Badge --}}
                    <div>
                        @php
                            $statusClass = match($purchase->status) {
                                'done' => 'bg-success',
                                'void' => 'bg-danger',
                                default => 'bg-warning text-dark'
                            };
                        @endphp
                        <span class="badge rounded-pill px-3 py-2 {{ $statusClass }}" style="font-size: 0.9rem;">
                            {{ strtoupper($purchase->status) }}
                        </span>
                    </div>
                </div>

                {{-- BAGIAN 1: INFORMASI UMUM (Readonly Inputs) --}}
                <div class="row g-4 mb-5">
                    {{-- Date Created --}}
                    <div class="col-md-6">
                        <label class="form-label">Date Created</label>
                        <input type="text" class="form-control" value="{{ $purchase->created_at->format('d M Y H:i:s') }}" readonly>
                    </div>

                    {{-- Created By --}}
                    <div class="col-md-6">
                        <label class="form-label">Created By</label>
                        <input type="text" class="form-control" value="{{ $purchase->user->name ?? '-' }}" readonly>
                    </div>

                    {{-- Supplier --}}
                    <div class="col-md-6">
                        <label class="form-label">Supplier</label>
                        <input type="text" class="form-control" value="{{ $purchase->supplier->supplier_name ?? 'N/A' }}" readonly>
                    </div>

                    {{-- Date Received (Only if Done) --}}
                    <div class="col-md-6">
                        <label class="form-label">Date Received</label>
                        <input type="text" class="form-control" 
                               value="{{ $purchase->received_at ? $purchase->received_at->format('d M Y H:i:s') : '-' }}" 
                               readonly>
                    </div>
                </div>

                {{-- ALERT KHUSUS JIKA STATUS PENDING (Tombol Receive Stock) --}}
                @if ($purchase->status == 'pending')
                    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between p-4 mb-5" style="border-radius: 16px;">
                        <div>
                            <h6 class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-2"></i> Action Required</h6>
                            <p class="mb-0 text-muted small">This purchase is still <strong>PENDING</strong>. Stock has not been added yet.</p>
                        </div>
                        
                        <form action="{{ route('purchases.updateStatus', $purchase->id) }}" method="POST" id="receiveForm">
                            @csrf
                            @method('PUT')
                            {{-- Tombol Receive (Override style .btn-save jadi Hijau) --}}
                            <button type="button" class="btn-save text-white text-decoration-none" 
                                    style="background-color: #198754; color: white; border: none;"
                                    id="btnReceiveStock">
                                Mark as Received
                            </button>
                        </form>
                    </div>
                @endif

                {{-- INFORMASI VOID (JIKA ADA) --}}
                @if($purchase->status === 'void')
                    <div class="product-section-title text-danger">Void Information</div>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label text-danger">Void By</label>
                            <input type="text" class="form-control text-danger" value="{{ $purchase->voidBy->name ?? '-' }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-danger">Void Date</label>
                            <input type="text" class="form-control text-danger" value="{{ $purchase->void_at ? $purchase->void_at->format('d M Y H:i') : '-' }}" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-danger">Reason</label>
                            <textarea class="form-control text-danger" rows="2" readonly>{{ $purchase->void_reason }}</textarea>
                        </div>
                    </div>
                @endif

                {{-- BAGIAN 2: ITEM PRODUK --}}
                <div class="product-section-title">Items Purchased</div>

                {{-- Header Grid (#product-rows-header dari CSS) --}}
                <div id="product-rows-header">
                    <div>Product</div>              {{-- 4fr --}}
                    <div>Unit Cost</div>            {{-- 2fr --}}
                    <div>Quantity</div>             {{-- 2fr --}}
                    <div>Subtotal</div>             {{-- 2fr --}}
                    <div></div>                     {{-- 0.5fr --}}
                </div>

                {{-- Item Rows (.product-row dari CSS) --}}
                @foreach($purchase->details as $detail)
                    <div class="product-row">
                        {{-- Product Name --}}
                        <div class="fw-bold text-dark">
                            {{ $detail->product->title ?? 'Product Deleted' }}
                        </div>

                        {{-- Unit Price --}}
                        <div class="unit-price-text">
                            Rp. {{ number_format($detail->price, 0, ',', '.') }}
                        </div>

                        {{-- Quantity --}}
                        <div>
                            <input type="text" class="form-control text-center bg-white border-0 fw-bold" 
                                   value="{{ number_format($detail->quantity) }}" readonly style="width: 60px; padding: 0.4rem;">
                        </div>

                        {{-- Subtotal --}}
                        <div class="subtotal-text">
                            Rp. {{ number_format($detail->subtotal, 0, ',', '.') }}
                        </div>

                        {{-- Kosong --}}
                        <div></div>
                    </div>
                @endforeach

                {{-- Grand Total (.detail-grand-total dari CSS) --}}
                <div class="detail-grand-total">
                    <span>Total Cost</span>
                    <span>Rp. {{ number_format($purchase->total_cost, 0, ',', '.') }}</span>
                </div>

                {{-- BAGIAN 3: TOMBOL AKSI (.form-actions) --}}
                <div class="form-actions mt-5">
                    {{-- Tombol Back --}}
                    <a href="{{ route('purchases.index') }}" class="btn-cancel text-decoration-none">
                        Back to List
                    </a>

                    {{-- Tombol Void (Hanya jika status done atau pending) --}}
                    @if ($purchase->status == 'done' || $purchase->status == 'pending') 
                        {{-- Override style .btn-save jadi Merah untuk Void --}}
                        <button type="button" 
                                class="btn-save text-decoration-none text-white" 
                                style="background-color: #dc3545; color: white; border: none;"
                                onclick="confirmVoid('{{ route('purchases.voidForm', $purchase->id) }}', 'PO-TXN-{{ $purchase->id }}')">
                            Void Transaction
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
        // --- 1. SCRIPT UNTUK RECEIVE STOCK (LIME THEME) ---
        const btnReceive = document.getElementById('btnReceiveStock');
        const receiveForm = document.getElementById('receiveForm');

        if (btnReceive && receiveForm) {
            btnReceive.addEventListener('click', function(e) {
                e.preventDefault(); 

                Swal.fire({
                    // HTML Custom untuk meniru gambar referensi (Ikon Tanda Tanya Lime)
                    html: `
                        <div class="receive-icon-bg">
                            <span class="receive-icon-text">?</span>
                        </div>
                        <div style="font-size: 1.1rem; font-weight: 600; color: #000; line-height: 1.5; margin-bottom: 0.5rem;">
                            Mark Purchase Order <strong>PO-TXN-{{ $purchase->id }}</strong> as <span style="color:#a4d123;">DONE</span> ?
                        </div>
                        <div style="font-size: 0.9rem; color: #666;">
                            This will add stock to your inventory
                        </div>
                    `,
                    // Matikan icon bawaan, kita pakai custom HTML di atas
                    icon: null, 
                    
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    
                    // Gunakan Class CSS yang baru dibuat di transaction-form.css
                    customClass: {
                        popup: 'swal-receive-popup',
                        confirmButton: 'btn-swal-confirm-lime',
                        cancelButton: 'btn-swal-cancel-grey',
                        actions: 'swal2-actions' // Helper class agar tombol ada jarak
                    },
                    buttonsStyling: false, // Matikan style default SweetAlert
                    reverseButtons: true,  // Cancel di kiri, Confirm di kanan
                    focusConfirm: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        receiveForm.submit();
                    }
                });
            });
        }
    });

    // --- 2. SCRIPT UNTUK VOID TRANSACTION (UNGU THEME) ---
    function confirmVoid(urlRedirect, transactionCode) {
        Swal.fire({
            // HTML Custom agar mirip dengan Index Transaction (Ikon Ungu)
            html: `
                <div style="width: 90px; height: 90px; background-color: #a69dee33; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                    <span style="font-size: 3.5rem; font-weight: 700; color: #a69dee; line-height: 1;">!</span>
                </div>
                <div style="font-size: 1.1rem; font-weight: 600; color: #000; margin-bottom: 1.5rem;">
                    Are you sure you want to void the <br>
                    transaction <strong>${transactionCode}</strong> ?
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Yes, Void',
            cancelButtonText: 'Cancel',
            
            // Styling Tombol via JS (karena tidak boleh CSS Internal)
            buttonsStyling: false,
            customClass: {
                popup: 'p-5 rounded-4 shadow-lg',
                confirmButton: 'btn btn-lg text-white mx-2', 
                cancelButton: 'btn btn-lg btn-light mx-2'
            },
            didOpen: () => {
                // Apply style ungu manual ke tombol confirm
                const confirmBtn = Swal.getConfirmButton();
                confirmBtn.style.backgroundColor = '#a69dee';
                confirmBtn.style.borderRadius = '12px';
                confirmBtn.style.padding = '10px 30px';
                confirmBtn.style.border = 'none';

                // Apply style ke tombol cancel
                const cancelBtn = Swal.getCancelButton();
                cancelBtn.style.backgroundColor = '#e0e0e0';
                cancelBtn.style.color = '#000';
                cancelBtn.style.borderRadius = '12px';
                cancelBtn.style.padding = '10px 30px';
                cancelBtn.style.border = 'none';
            },
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke halaman Form Void
                window.location.href = urlRedirect;
            }
        });
    }
</script>
@endpush