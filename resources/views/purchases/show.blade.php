@extends('layouts.app')

@section('title', 'Purchase Detail')

@section('content')
{{-- Load CSS External --}}
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Container Utama --}}
            <div class="form-card p-5">

                {{-- Header Detail --}}
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h3 class="fw-bold mb-0">Transaction Detail</h3> {{-- Sesuai gambar, judulnya "Transaction Detail" --}}
                    
                    {{-- Status Badge --}}
                    @if($purchase->status == 'void')
                        <span class="badge bg-danger rounded-pill px-3 py-2">Void</span>
                    @elseif($purchase->status == 'done')
                        <span class="badge bg-success rounded-pill px-3 py-2">Received</span>
                    @else
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Pending</span>
                    @endif
                </div>

                <div class="mb-4">
                    <h5 class="fw-bold">PO-TXN-{{ $purchase->id }}</h5>
                </div>

                {{-- INFORMASI UMUM --}}
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="text-muted small fw-bold mb-1">Transaction Time</div>
                        <div class="fw-bold text-dark">{{ $purchase->created_at->format('d F Y - H:i:s') }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small fw-bold mb-1">Supplier</div>
                        <div class="fw-bold text-dark">{{ $purchase->supplier->supplier_name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small fw-bold mb-1">Created By</div>
                        <div class="fw-bold text-dark">{{ $purchase->user->name ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small fw-bold mb-1">Date Received</div>
                        <div class="fw-bold text-dark">{{ $purchase->received_at ? $purchase->received_at->format('d F Y - H:i') : '-' }}</div>
                    </div>
                </div>

                {{-- INFORMASI VOID --}}
                @if($purchase->status === 'void')
                    <div class="border-bottom pb-2 mb-3 mt-5">
                        <h5 class="fw-bold text-danger">Void Information</h5>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="text-danger small fw-bold mb-1">Void By</div>
                            <div class="fw-bold text-dark">{{ $purchase->voidBy->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-danger small fw-bold mb-1">Void Date</div>
                            <div class="fw-bold text-dark">{{ $purchase->void_at ? $purchase->void_at->format('d F Y - H:i') : '-' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-danger small fw-bold mb-1">Reason</div>
                            <div class="fw-bold text-dark">{{ $purchase->void_reason }}</div>
                        </div>
                    </div>
                @endif

                {{-- ITEM PRODUK HEADER --}}
                <div class="mt-5 mb-3">
                    <h5 class="fw-bold">Items Purchased</h5>
                </div>

                {{-- Table Header --}}
                <div class="row text-muted small fw-bold border-bottom pb-2 mb-2">
                    <div class="col-5">Product Name</div>
                    <div class="col-3">Unit Price</div>
                    <div class="col-2 text-center">Quantity</div>
                    <div class="col-2 text-end">Subtotal</div>
                </div>

                {{-- Item Rows --}}
                <div class="mb-4">
                    @foreach($purchase->details as $detail)
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
                    <span class="fs-4 fw-bold">Rp. {{ number_format($purchase->total_cost, 0, ',', '.') }}</span>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('purchases.index') }}" class="btn btn-light rounded-pill px-4 fw-bold">Back to List</a>

                    @if ($purchase->status == 'pending')
                        <form action="{{ route('purchases.updateStatus', $purchase->id) }}" method="POST" id="receiveForm">
                            @csrf
                            @method('PUT')
                            <button type="button" class="btn btn-success rounded-pill px-4 fw-bold text-white" id="btnReceiveStock">Mark as Received</button>
                        </form>
                    @endif

                    @if ($purchase->status == 'done' || $purchase->status == 'pending')
                        <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" onclick="confirmVoid('{{ route('purchases.voidForm', $purchase->id) }}', 'PO-TXN-{{ $purchase->id }}')">Void Transaction</button>
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
    const btnReceive = document.getElementById('btnReceiveStock');
    const receiveForm = document.getElementById('receiveForm');

    if (btnReceive && receiveForm) {
        btnReceive.addEventListener('click', function(e) {
            e.preventDefault(); 
            Swal.fire({
                html: `
                    <div style="width: 90px; height: 90px; background-color: #d1e7dd; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                        <span style="font-size: 3.5rem; font-weight: 700; color: #198754; line-height: 1;">?</span>
                    </div>
                    <div style="font-size: 1.1rem; font-weight: 600; color: #000; margin-bottom: 0.5rem;">
                        Mark Purchase <strong>PO-TXN-{{ $purchase->id }}</strong> as Received?
                    </div>
                    <div style="font-size: 0.9rem; color: #666;">
                        This will add stock to inventory.
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel',
                buttonsStyling: false,
                customClass: {
                    popup: 'p-5 rounded-4 shadow-lg',
                    confirmButton: 'btn btn-lg btn-success mx-2 rounded-pill px-4',
                    cancelButton: 'btn btn-lg btn-light mx-2 rounded-pill px-4'
                },
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    receiveForm.submit();
                }
            });
        });
    }
});

function confirmVoid(urlRedirect, transactionCode) {
    Swal.fire({
        html: `
            <div style="width: 90px; height: 90px; background-color: #f8d7da; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                <span style="font-size: 3.5rem; font-weight: 700; color: #dc3545; line-height: 1;">!</span>
            </div>
            <div style="font-size: 1.1rem; font-weight: 600; color: #000; margin-bottom: 1.5rem;">
                Are you sure you want to void <br>
                <strong>${transactionCode}</strong> ?
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Yes, Void',
        cancelButtonText: 'Cancel',
        buttonsStyling: false,
        customClass: {
            popup: 'p-5 rounded-4 shadow-lg',
            confirmButton: 'btn btn-lg btn-danger mx-2 rounded-pill px-4', 
            cancelButton: 'btn btn-lg btn-light mx-2 rounded-pill px-4'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = urlRedirect;
        }
    });
}
</script>
@endpush