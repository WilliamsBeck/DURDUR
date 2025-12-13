@extends('layouts.app') 

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">
                    Purchase Order #{{ $purchase->id }} 
                    <span class="badge {{ $purchase->status == 'done' ? 'bg-success' : ($purchase->status == 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                        {{ strtoupper($purchase->status) }}
                    </span>
                </h2>
                
                <div>
                    {{-- Tombol Void, hanya jika status DONE atau PENDING --}}
                    @if ($purchase->status == 'done' || $purchase->status == 'pending') 
                    <a href="{{ route('purchases.voidForm', $purchase->id) }}" class="btn btn-danger me-2">
                        <i class="fas fa-times-circle"></i> Void Transaction
                    </a>
                    @endif
                    
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            {{-- LOGIKA ALERT SEDERHANA --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            {{-- AKHIR LOGIKA ALERT SEDERHANA --}}

            {{-- TOMBOL UPDATE STATUS (Hanya Tampil jika statusnya pending) --}}
            @if ($purchase->status == 'pending')
                <div class="card shadow mb-4 border-warning">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0">Transaksi ini masih berstatus **PENDING**. Stok produk belum ditambahkan.</p>
                        </div>
                        
                        <form action="{{ route('purchases.updateStatus', $purchase->id) }}" method="POST" id="receiveForm">
                            @csrf
                            @method('PUT')
                            
                            <button type="button" 
                                    class="btn btn-success btn-lg"
                                    id="btnReceiveStock">
                                <i class="fas fa-check-circle"></i> Mark as DONE & Receive Stock
                            </button>
                        </form>
                    </div>
                </div>
            @endif
            
            {{-- DETAIL HEADER --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white">Transaction Info</div>
                        <div class="card-body">
                            <p><strong>Supplier:</strong> {{ $purchase->supplier->supplier_name }}</p>
                            <p><strong>Created By:</strong> {{ $purchase->user->name }}</p>
                            <p><strong>Date Created:</strong> {{ $purchase->created_at->format('d M Y H:i:s') }}</p>
                            
                            @if ($purchase->status == 'done')
                               
                                @if (isset($purchase->received_at))
                                <p><strong>Date Received:</strong> {{ $purchase->received_at->format('d M Y H:i:s') }}</p>
                                @endif
                            @endif

                            @if ($purchase->status == 'void')
                                <p class="text-danger"><strong>Void Reason:</strong> {{ $purchase->void_reason }}</p>
                                <p class="text-danger"><strong>Void By:</strong> {{ $purchase->voidBy->name ?? 'System' }} at {{ $purchase->void_at->format('d M Y H:i:s') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">Financial Summary</div>
                        <div class="card-body text-end">
                            <h3>Total Cost:</h3>
                            <h1 class="text-primary fw-bold">Rp {{ number_format($purchase->total_cost, 0, ',', '.') }}</h1>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAIL ITEMS --}}
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">Purchase Items</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchase->details as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $detail->product->title ?? 'Product Deleted' }}</td>
                                        <td class="text-end">{{ number_format($detail->quantity) }}</td>
                                        <td class="text-end">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Script SweetAlert2 untuk Konfirmasi Receive Stock --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnReceive = document.getElementById('btnReceiveStock');
        const receiveForm = document.getElementById('receiveForm');

        if (btnReceive && receiveForm) {
            btnReceive.addEventListener('click', function(e) {
                e.preventDefault(); 

                Swal.fire({
                    title: 'Konfirmasi Penerimaan Barang?',
                    html: `
                        Anda akan menandai Purchase Order 
                        <span class="fw-bold text-primary">#{{ $purchase->id }}</span> 
                        sebagai <span class="fw-bold text-success">SELESAI (DONE)</span>.
                        <p class="text-danger mt-2">Aksi ini akan **menambahkan** stok produk ke inventaris Anda.</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Ya, Terima dan Tambah Stok',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        receiveForm.submit();
                    }
                });
            });
        }
    });
</script>
@endpush