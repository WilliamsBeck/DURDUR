@extends('layouts.app')

@section('title', 'Sales Transactions')

@section('content')
<div class="container">

    {{-- FLASH MESSAGE SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- FLASH MESSAGE ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <h5 class="mb-0">Sales Transactions</h5>
            
            <div class="d-flex align-items-center">
                {{-- Form Pencarian --}}
                <form method="GET" class="me-3">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Search ID, Cashier, or Email...">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                {{-- Tombol Tambah Transaksi --}}
                <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Add Transaction
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Date</th>
                            <th scope="col">Cashier</th>
                            <th scope="col">Payment</th>
                            <th scope="col">Grand Total</th>
                            <th scope="col">Status</th>
                            <th scope="col" width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($transactions as $trx)
                        <tr>
                            <td>#{{ $trx->id }}</td>
                            <td>{{ $trx->transaction_date->format('d F, Y') }}</td>
                            <td>{{ $trx->cashier->name ?? '-' }}</td>
                            <td>{{ $trx->payment->method_name ?? '-' }}</td>
                            <td>Rp. {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $badgeClass = '';
                                    switch ($trx->status) {
                                        case 'done':
                                            $badgeClass = 'bg-success';
                                            break;
                                        case 'pending':
                                            $badgeClass = 'bg-warning text-dark';
                                            break;
                                        case 'void':
                                            $badgeClass = 'bg-danger';
                                            break;
                                        default:
                                            $badgeClass = 'bg-secondary';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            </td>
                            <td>
                                {{-- DETAIL --}}
                                <a href="{{ route('transactions.show', $trx->id) }}"
                                   class="btn btn-sm btn-info" title="Detail">
                                   Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <p class="lead text-muted">No sales transactions found.</p>
                                <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-success">Start New Transaction</a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            {{ $transactions->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
    {{-- Memastikan ikon tersedia (Bootstrap Icons) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush