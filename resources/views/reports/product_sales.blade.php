@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <h2 class="mb-4 fw-bold" style="color: #111827;">Report</h2>

    <div class="report-card">
        {{-- 1. TAB MENU --}}
        <div class="d-flex flex-wrap mb-4">

            <a href="{{ route('reports.sales') }}" class="btn-tab">Sales Report</a>
            <a href="{{ route('reports.purchasement') }}" class="btn-tab">Purchasement Report</a>
            <a href="{{ route('reports.product_sales') }}" class="btn-tab active">Product Sold Report</a>
            <a href="{{ route('reports.remaining_stock') }}" class="btn-tab">Remaining Stock Report</a>
        </div>

        {{-- 2. FILTER & DOWNLOAD --}}
        <form action="{{ route('reports.product_sales') }}" method="GET" class="d-flex justify-content-between align-items-center mb-5 flex-wrap">
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
            
            {{-- Section 1: General Stats --}}
            <div class="row mb-3">
                <div class="col-md-6 stat-label">Total Product Quantity</div>
                <div class="col-md-6 stat-value">{{ $totalProductQuantity }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 stat-label">Total Product Sales</div>
                <div class="col-md-6 stat-value">Rp. {{ number_format($totalProductSales, 0, ',', '.') }}</div>
            </div>

            <div class="divider"></div>

            {{-- Section 2: List per Kategori --}}
            @forelse($reportData as $categoryName => $products)
                {{-- Header Kategori (Contoh: Category - Besi) --}}
                <div class="category-header">
                    Category - {{ $categoryName }}
                </div>

                {{-- List Produk dalam kategori tersebut --}}
                @foreach($products as $product)
                <div class="row mb-3">
                    <div class="col-md-4 stat-label fw-normal">{{ $product['product_name'] }}</div>
                    <div class="col-md-2 stat-value text-center">{{ $product['total_qty'] }}</div>
                    <div class="col-md-6 stat-value text-end">Rp. {{ number_format($product['total_price'], 0, ',', '.') }}</div>
                </div>
                @endforeach

            @empty
                <div class="text-center text-muted py-5">
                    No product sales found in this period.
                </div>
            @endforelse

        </div>
    </div>
</div>
@endsection