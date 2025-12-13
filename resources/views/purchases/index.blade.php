@extends('layouts.app')

@section('title', 'Purchase Transactions')

@section('content')
<div class="container-fluid">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- HEADER & TOOLBAR --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-gray-800">
            <i class="fas fa-shopping-cart text-primary me-2"></i> Purchase Transactions
        </h2>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Create New Purchase
        </a>
    </div>

    {{-- SEARCH & FILTER CARD --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form action="{{ route('purchases.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" 
                           class="form-control" 
                           name="search" 
                           placeholder="Search by Transaction ID or Supplier Name..." 
                           value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary" title="Reset Search">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE CARD --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Transaction List</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 ps-3">#ID</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Supplier</th>
                            <th class="py-3">Created By</th>
                            <th class="py-3 text-end">Total Cost</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td class="ps-3 fw-bold text-primary">#{{ $purchase->id }}</td>
                            <td>
                                {{ $purchase->created_at
                                
                                ->format('d M Y') }}
                                <small class="text-muted d-block">{{ $purchase->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                {{-- Menggunakan optional() atau null coalescing untuk mencegah error jika supplier dihapus --}}
                                {{ $purchase->supplier->supplier_name ?? 'N/A (Deleted)' }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-user mb-1"></i> {{ $purchase->user->name ?? 'System' }}
                                </span>
                            </td>
                            <td class="text-end fw-bold">
                                Rp {{ number_format($purchase->total_cost, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if($purchase->status === 'done')
                                    <span class="badge bg-success rounded-pill px-3">
                                        <i class="fas fa-check me-1"></i> Done
                                    </span>
                                @elseif($purchase->status === 'void')
                                    <span class="badge bg-danger rounded-pill px-3">
                                        <i class="fas fa-ban me-1"></i> Void
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark rounded-pill px-3">
                                        {{ ucfirst($purchase->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-3">
                                <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-sm btn-info text-white shadow-sm" data-bs-toggle="tooltip" title="View Details">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3"></i>
                                <p class="mb-0">No purchase transactions found.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- PAGINATION --}}
        <div class="card-footer bg-white d-flex justify-content-end">
            {{-- withQueryString memastikan parameter search tetap ada saat pindah halaman --}}
            {{ $purchases->withQueryString()->links() }} 
        </div>
    </div>

</div>
@endsection