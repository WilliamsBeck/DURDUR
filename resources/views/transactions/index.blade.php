@extends('layouts.app')

@section('title', 'Transaction Management')

@section('content')

<div class="container-fluid">
    {{-- Card Putih Besar --}}
    <div class="main-content-card">
        <div class="table-controls">
            {{-- Tombol Add Hitam --}}
            <a href="{{ route('transactions.create') }}" class="add-btn">
                <i class="bi bi-plus-lg"></i> Add Transaction
            </a>
            <div class="header-actions">
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
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cashier Name</th>
                        <th>Customer Email</th>
                        <th>Date</th>
                        <th>Grand Total</th>
                        <th class="text-center">Actions</th>
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

        <div class="d-flex justify-content-center mt-4">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
    </div>

@endsection