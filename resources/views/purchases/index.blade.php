@extends('layouts.app')

@section('title', 'Purchase Transactions')

@section('content')
{{-- Tidak perlu load CSS internal karena sudah menggunakan transaction.css global --}}

<div class="container-fluid">
    {{-- Main Content Card (.main-content-card dari CSS) --}}
    <div class="main-content-card">

        {{-- 1. TITLE --}}
        <h3>Purchase Transactions</h3>

        {{-- 2. CONTROLS --}}
        <div class="table-controls">
            {{-- Tombol Add Hitam (.add-btn) --}}
            <a href="{{ route('purchases.create') }}" class="add-btn">
                <i class="fas fa-plus"></i> Add Purchase
            </a>

            {{-- Search Bar Abu-abu Pill (.search-bar-new) --}}
            <form method="GET" action="{{ route('purchases.index') }}" class="m-0">
                <div class="search-bar-new">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID or Supplier...">
                </div>
            </form>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success border-0 bg-success-subtle rounded-3 mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 3. TABLE --}}
        <div class="table-responsive">
            {{-- Gunakan class .table agar dapat style floating rows --}}
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center" width="8%">ID</th>
                        <th width="15%">Date</th>
                        <th width="20%">Supplier</th>
                        <th width="15%">Created By</th>
                        <th width="15%">Total Cost</th>
                        <th class="text-center" width="10%">Status</th>
                        <th class="text-center" width="15%">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($purchases as $purchase)
                    <tr>
                        {{-- ID --}}
                        <td class="text-center text-muted">#{{ $purchase->id }}</td>
                        
                        {{-- Date --}}
                        <td>
                            {{ $purchase->created_at->format('d M Y') }}
                            <small class="d-block text-muted">{{ $purchase->created_at->format('H:i') }}</small>
                        </td>
                        
                        {{-- Supplier --}}
                        <td>{{ $purchase->supplier->supplier_name ?? 'N/A' }}</td>
                        
                        {{-- Created By --}}
                        <td>{{ $purchase->user->name ?? 'System' }}</td>
                        
                        {{-- Total Cost (Bold Dark) --}}
                        <td class="fw-bold-dark">
                            Rp. {{ number_format($purchase->total_cost, 0, ',', '.') }}
                        </td>

                        {{-- Status (Icons) --}}
                        <td>
                            @if($purchase->status === 'void')
                                <div class="status-icon-void"><i class="fas fa-times-circle"></i></div>
                            @elseif($purchase->status === 'done')
                                <div class="status-icon-check"><i class="fas fa-check-circle"></i></div>
                            @else
                                <div class="status-icon-pending"><i class="fas fa-exclamation-circle"></i></div>
                            @endif
                        </td>

                        {{-- Action Buttons --}}
                        <td>
                            <div class="action-icons">
                                {{-- Tombol Detail (Ungu) --}}
                                <a href="{{ route('purchases.show', $purchase->id) }}" class="btn-circle btn-purple-solid" title="View Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                {{-- Jika butuh tombol Void di sini, tambahkan logikanya nanti. 
                                     Saat ini hanya tombol detail agar sesuai desain tabel minimalis --}}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-2x mb-3 text-light"></i><br>
                            No Purchases Found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-4">
            {{ $purchases->withQueryString()->links() }}
        </div>

    </div>
</div>
@endsection