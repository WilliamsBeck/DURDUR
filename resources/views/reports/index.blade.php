@extends('layouts.app') {{-- Pastikan ini mengarah ke layout utamamu --}}

@section('content')

<div class="container-fluid py-4">
    <h2 class="mb-4 fw-bold" style="color: #111827;">Report</h2>

    <div class="report-card">
        {{-- 1. TAB MENU --}}
        <div class="d-flex flex-wrap mb-4">
            
            <a href="{{ route('reports.sales') }}" class="btn-tab active">Sales Report</a>
            <a href="{{ route('reports.purchasement') }}" class="btn-tab">Purchasement Report</a>
            <a href="{{ route('reports.product_sales') }}" class="btn-tab">Product Sold Report</a>
            <a href="{{ route('reports.remaining_stock') }}" class="btn-tab">Remaining Stock Report</a>
        </div>

        {{-- 2. FILTER & DOWNLOAD --}}
        <form action="{{ route('reports.sales') }}" method="GET" class="d-flex justify-content-between align-items-center mb-5 flex-wrap">
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
            
            {{-- Section: General Stats --}}
            <div class="row mb-3">
                <div class="col-md-6 stat-label">Total Sales Amount</div>
                <div class="col-md-6 stat-value">Rp. {{ number_format($totalSales, 0, ',', '.') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 stat-label">Total Bill</div>
                <div class="col-md-6 stat-value">{{ $totalBill }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 stat-label">Average Sales per Bill</div>
                <div class="col-md-6 stat-value">Rp. {{ number_format($averageSales, 0, ',', '.') }}</div>
            </div>

            <div class="divider"></div>

            {{-- Section: Transaction Status --}}
            <div class="row mb-3">
                <div class="col-md-4 stat-label">Successfull Transaction</div>
                <div class="col-md-2 stat-value text-center">{{ $successfulTrxCount }}</div>
                <div class="col-md-6 stat-value text-end">Rp. {{ number_format($successfulTrxValue, 0, ',', '.') }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 stat-label">Void Transaction</div>
                <div class="col-md-2 stat-value text-center">{{ $voidTrxCount }}</div>
                <div class="col-md-6 stat-value text-end">Rp. {{ number_format($voidTrxValue, 0, ',', '.') }}</div>
            </div>

            <div class="divider"></div>

            {{-- Section: Payment Breakdown --}}
            {{-- Loop data payment dari database --}}
            @forelse($paymentStats as $payment)
            <div class="row mb-3">
                <div class="col-md-4 stat-label">{{ $payment->method_name }}</div>
                <div class="col-md-2 stat-value text-center">{{ $payment->total_count }}</div>
                <div class="col-md-6 stat-value text-end">Rp. {{ number_format($payment->total_amount, 0, ',', '.') }}</div>
            </div>
            @empty
            <div class="text-muted">No payment data available for this period.</div>
            @endforelse

            {{-- Contoh Hardcode jika data kosong agar mirip gambar (Bisa dihapus nanti) --}}
            @if($paymentStats->isEmpty())
                <div class="row mb-3 opacity-50">
                    <div class="col-md-4 stat-label">Cash (Sample)</div>
                    <div class="col-md-2 stat-value text-center">0</div>
                    <div class="col-md-6 stat-value text-end">Rp. 0</div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection