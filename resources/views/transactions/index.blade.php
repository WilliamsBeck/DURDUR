@extends('layouts.app')

@section('title', 'Sales Transactions')

@section('content')
{{-- Load CSS Custom Baru --}}
<link rel="stylesheet" href="{{ asset('css/transaction.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="container-fluid">
    {{-- Card Putih Besar --}}
    <div class="main-content-card">

        {{-- 1. TITLE --}}
        <h3>Sales Transaction</h3>

        {{-- 2. CONTROLS --}}
        <div class="table-controls">
            {{-- Tombol Add Hitam --}}
            <a href="{{ route('transactions.create') }}" class="add-btn">
                <i class="bi bi-plus-lg"></i> Add Transaction
            </a>

            {{-- Search Bar Abu-abu Pill --}}
            <form method="GET" action="{{ route('transactions.index') }}" class="m-0">
                <div class="search-bar-new">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search Transaction Date">
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 bg-success-subtle rounded-3 mb-4">
                <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
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
                        <td class="text-center text-muted">#{{ $trx->id }}</td>
                        <td>{{ $trx->transaction_date->format('d, F Y') }}</td>
                        <td>{{ $trx->customer_email ?? '-' }}</td>
                        
                        {{-- Class fw-bold-dark untuk menebalkan harga --}}
                        <td class="fw-bold-dark">
                            Rp. {{ number_format($trx->grand_total, 2, ',', '.') }}
                        </td>

                        <td>
                            @if($trx->status == 'void')
                                <div class="status-icon-void"><i class="bi bi-x-circle"></i></div>
                            @else
                                {{-- Default Lime Check --}}
                                <div class="status-icon-check"><i class="bi bi-check-circle"></i></div>
                            @endif
                        </td>

                        <td>
                            <div class="action-icons">
                                {{-- Tombol Void Merah --}}
                                <form action="{{ route('transactions.void', $trx->id) }}" method="POST" onsubmit="return confirm('Void?');">
                                    @csrf
                                    <button type="submit" class="btn-circle btn-red-solid" title="Void">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </form>

                                {{-- Tombol Detail Ungu --}}
                                <a href="{{ route('transactions.show', $trx->id) }}" class="btn-circle btn-purple-solid" title="View">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No Data Available</td>
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