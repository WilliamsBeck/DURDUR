@extends('layouts.app')

@section('title', 'Sales Transactions')

@section('content')

{{-- Style Tambahan Khusus untuk SweetAlert Custom (Ungu) --}}
@push('styles')
<style>
    /* Container Popup Bulat */
    .swal-void-popup {
        border-radius: 30px !important;
        padding: 2rem !important;
        width: 450px !important;
    }
    /* Ikon Custom (Lingkaran Ungu Pucat) */
    .void-icon-bg {
        width: 90px; height: 90px;
        background-color: #a69dee33;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.5rem auto;
    }
    /* Tanda Seru (!) Ungu Solid */
    .void-icon-text {
        font-size: 3.5rem; font-weight: 700; color: #a69dee; line-height: 1;
    }
    /* Text Konfirmasi */
    .void-text {
        font-size: 1.1rem; font-weight: 600; color: #000; margin-bottom: 1.5rem;
    }
    /* Tombol Cancel */
    .btn-swal-cancel {
        background-color: #e0e0e0 !important; color: #000 !important;
        font-weight: 600 !important; padding: 12px 30px !important;
        border-radius: 12px !important; border: none !important; margin-right: 10px !important;
    }
    /* Tombol Yes Void */
    .btn-swal-confirm {
        background-color: #a69dee !important; color: #000 !important;
        font-weight: 600 !important; padding: 12px 30px !important;
        border-radius: 12px !important; border: none !important; box-shadow: none !important;
    }
</style>
@endpush

<div class="container-fluid">
    {{-- Card Putih Besar (.main-content-card dari transaction.css) --}}
    <div class="main-content-card">

        {{-- 1. TITLE --}}
        <h3>Sales Transaction</h3>

        {{-- 2. CONTROLS --}}
        <div class="table-controls">
            {{-- Tombol Add Hitam --}}
            <a href="{{ route('transactions.create') }}" class="add-btn">
                <i class="fas fa-plus"></i> Add Transaction
            </a>

            {{-- Search Bar Abu-abu Pill --}}
            <form method="GET" action="{{ route('transactions.index') }}" class="m-0">
                <div class="search-bar-new">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Transaction Date">
                </div>
            </form>
        </div>

        {{-- Flash Message --}}
        @if(session('success'))
            <div class="alert alert-success border-0 bg-success-subtle rounded-3 mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        {{-- 3. TABLE --}}
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center" width="8%">ID</th>
                        <th width="20%">Date</th>
                        <th width="25%">Customer Email</th>
                        <th width="20%">Grand Total</th>
                        <th class="text-center" width="10%">Status</th>
                        <th class="text-center" width="15%">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($transactions as $trx)
                    <tr>
                        {{-- ID --}}
                        <td class="text-center text-muted">#{{ $trx->id }}</td>
                        
                        {{-- Date --}}
                        <td>{{ $trx->transaction_date->format('d, F Y') }}</td>
                        
                        {{-- Email --}}
                        <td>{{ $trx->customer_email ?? '-' }}</td>
                        
                        {{-- Grand Total (Bold Dark) --}}
                        <td class="fw-bold-dark">
                            Rp. {{ number_format($trx->grand_total, 2, ',', '.') }}
                        </td>

                        {{-- Status Icons --}}
                        <td>
                            @if($trx->status == 'void')
                                {{-- Badge Merah untuk Void --}}
                                <span class="badge-status badge-void">
                                    <i class="fas fa-times-circle"></i> VOID
                                </span>
                            @else
                                {{-- Badge Hijau untuk Sukses/Check --}}
                                <span class="badge-status badge-check">
                                    <i class="fas fa-check-circle"></i> SUCCESS
                                </span>
                            @endif
                        </td>

                        {{-- Action Buttons --}}
                        <td>
                            <div class="action-icons">
                                
                                {{-- Tombol Void (Merah) dengan SweetAlert --}}
                                {{-- PENTING: Panggil fungsi confirmVoid dengan URL route 'transactions.void.form' (GET) --}}
                                {{-- <button type="button" 
                                        class="btn-circle btn-red-solid" 
                                        title="Void"
                                        onclick="confirmVoid('{{ route('transactions.void.form', $trx->id) }}', 'INV-TXN-{{ $trx->id }}')">
                                    <i class="fas fa-times"></i>
                                </button> --}}

                                {{-- Tombol Detail (Ungu) --}}
                                <a href="{{ route('transactions.show', $trx->id) }}" class="btn-circle btn-purple-solid" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-2x mb-3 text-light"></i><br>
                            No Transactions Found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-4">
            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

{{-- Script SweetAlert Custom --}}
@push('scripts')
<script>
    // Fungsi ini menerima URL redirect (halaman form void)
    function confirmVoid(urlRedirect, transactionCode) {
        Swal.fire({
            html: `
                <div class="void-icon-bg">
                    <span class="void-icon-text">!</span>
                </div>
                <div class="void-text">
                    Are you sure you want to void the <br>
                    transaction <strong>${transactionCode}</strong> ?
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Yes, Void',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'swal-void-popup',
                confirmButton: 'btn-swal-confirm',
                cancelButton: 'btn-swal-cancel'
            },
            buttonsStyling: false,
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect browser ke halaman Void Form
                window.location.href = urlRedirect;
            }
        });
    }
</script>
@endpush