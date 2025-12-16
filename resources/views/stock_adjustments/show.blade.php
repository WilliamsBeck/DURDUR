@extends('layouts.app')

@section('title', 'Adjustment Detail')

@section('content')
<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Stock Adjustment Detail</h3>
        {{-- Tombol Back menggunakan style cancel --}}
        <a href="{{ route('stock-adjustments.index') }}" class="btn-cancel text-decoration-none">
            <i class="fas fa-arrow-left me-2"></i> Back To List
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h5 class="fw-bold">#{{ $adjustment->id }}</h5>
            <p class="text-muted mb-1">Transaction Time</p>
            <p class="fw-bold fs-5">{{ $adjustment->transaction_date->format('d F Y - H:i:s') }}</p>
            
            <p class="text-muted mb-1 mt-3">Adjusted By</p>
            <p class="fw-bold fs-5">{{ $adjustment->user->name ?? 'User Deleted' }}</p>
        </div>
    </div>

    <hr>

    <h5 class="fw-bold mb-3">Stock Adjusted</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
            <thead class="bg-light">
                <tr>
                    <th class="text-start">Product</th>
                    <th>Old Stock</th>
                    <th>New Stock</th>
                    <th>Difference</th>
                    <th>Reason</th>
                    <th class="text-start">Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($adjustment->details as $detail)
                <tr>
                    <td class="text-start fw-bold">{{ $detail->product->title ?? 'Product Deleted' }}</td>
                    <td>{{ $detail->old_stock }}</td>
                    <td>{{ $detail->new_stock }}</td>
                    <td class="{{ $detail->difference < 0 ? 'text-danger fw-bold' : ($detail->difference > 0 ? 'text-success fw-bold' : '') }}">
                        {{ $detail->difference > 0 ? '+' : '' }}{{ $detail->difference }}
                    </td>
                    <td><span class="badge bg-secondary rounded-pill fw-normal text-white px-3 py-2">{{ $detail->reason }}</span></td>
                    <td class="text-start text-muted">{{ $detail->note ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection