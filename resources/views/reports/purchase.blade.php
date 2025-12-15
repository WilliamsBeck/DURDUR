@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <h2 class="mb-4 fw-bold" style="color: #111827;">Report</h2>

    <div class="report-card">
        {{-- 1. TAB MENU --}}
        <div class="d-flex flex-wrap mb-4">

            <a href="{{ route('reports.sales') }}" class="btn-tab">Sales Report</a>
            <a href="{{ route('reports.purchasement') }}" class="btn-tab active">Purchasement Report</a>
            <a href="{{ route('reports.product_sales') }}" class="btn-tab">Product Sold Report</a>
            <a href="{{ route('reports.remaining_stock') }}" class="btn-tab">Remaining Stock Report</a>
        </div>

        {{-- 2. FILTER & DOWNLOAD --}}
        <form action="{{ route('reports.purchasement') }}" method="GET" class="d-flex justify-content-between align-items-center mb-5 flex-wrap">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold me-2">Date</span>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-date" onchange="this.form.submit()">
                <span>-</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-date" onchange="this.form.submit()">
            </div>
            
            <button type="button" class="btn-purple">
                Download PDF
            </button>
        </form>

        {{-- 3. CONTENT DATA --}}
        <div class="px-2">
            
            {{-- Section 1: General Stats (Done/Received) --}}
            <div class="row mb-3">
                <div class="col-md-6 stat-label">Total Purchase Amount</div>
                <div class="col-md-6 stat-value">Rp. {{ number_format($totalPurchaseAmount, 0, ',', '.') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 stat-label">Total Bill</div>
                <div class="col-md-6 stat-value">{{ $totalBill }}</div>
            </div>
            <div class="row mb-3">
                {{-- Sesuai gambar "Total Item purchased" yang nilainya rata-rata --}}
                <div class="col-md-6 stat-label">Average Purchase per Bill</div>
                <div class="col-md-6 stat-value">Rp. {{ number_format($averagePurchase, 0, ',', '.') }}</div>
            </div>

            <div class="divider"></div>

            {{-- Section 2: Transaction Status Breakdown --}}
            <div class="row mb-3">
                <div class="col-md-4 stat-label">Received Purchases (Done)</div>
                <div class="col-md-2 stat-value text-center">{{ $receivedCount }}</div>
                <div class="col-md-6 stat-value text-end">Rp. {{ number_format($receivedValue, 0, ',', '.') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 stat-label">Pending Purchases</div>
                <div class="col-md-2 stat-value text-center">{{ $pendingCount }}</div>
                <div class="col-md-6 stat-value text-end">Rp. {{ number_format($pendingValue, 0, ',', '.') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 stat-label">Cancelled Purchases (Void)</div>
                <div class="col-md-2 stat-value text-center">{{ $voidCount }}</div>
                <div class="col-md-6 stat-value text-end">Rp. {{ number_format($voidValue, 0, ',', '.') }}</div>
            </div>

            <div class="divider"></div>

            {{-- Section 3: Supplier Breakdown --}}
            @forelse($supplierStats as $supplier)
            <div class="row mb-3">
                <div class="col-md-4 stat-label">{{ $supplier['supplier_name'] }}</div>
                <div class="col-md-2 stat-value text-center">{{ $supplier['count'] }}</div>
                <div class="col-md-6 stat-value text-end">Rp. {{ number_format($supplier['total_amount'], 0, ',', '.') }}</div>
            </div>
            @empty
            <div class="text-muted text-center py-3">No completed purchase data available for this period.</div>
            @endforelse

        </div>
    </div>
</div>
@endsection