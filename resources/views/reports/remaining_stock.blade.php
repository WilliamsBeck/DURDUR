@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">
    <h2 class="mb-4 fw-bold" style="color: #111827;">Report</h2>

    <div class="report-card">
        {{-- 1. TAB MENU --}}
        <div class="d-flex flex-wrap mb-4">

            <a href="{{ route('reports.sales') }}" class="btn-tab">Sales Report</a>
            <a href="{{ route('reports.purchasement') }}" class="btn-tab">Purchasement Report</a>
            <a href="{{ route('reports.product_sales') }}" class="btn-tab">Product Sold Report</a>
            <a href="{{ route('reports.remaining_stock') }}" class="btn-tab active">Remaining Stock Report</a>
        </div>

        {{-- 2. ACTION BAR (Search & Buttons) --}}
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap">
            {{-- Search Form --}}
            <form action="{{ route('reports.remaining_stock') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="search-bar-new" placeholder="Search Product or category" value="{{ request('search') }}">
                </div>
            </form>

            {{-- Buttons --}}
            <button type="button" class="btn-purple">
                Download PDF
            </button>
        </div>

        {{-- Tombol Stock Adjustment (Posisi di kanan bawah filter, sesuai gambar) --}}
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('products.index') }}" class="add-btn">Stock Adjustment</a>
        </div>

        {{-- 3. CONTENT DATA --}}
        <div class="px-2">
            
            {{-- Header Tabel Utama --}}
            <div class="row fw-bold text-muted border-bottom pb-2 mb-2 d-none d-md-flex">
                <div class="col-1">No.</div>
                <div class="col-6">Product Name</div>
                <div class="col-5">Stock</div>
            </div>

            {{-- Loop Categories --}}
            @forelse($reportData as $categoryName => $products)
                {{-- Nama Kategori --}}
                <div class="category-header">
                    Category - {{ $categoryName }}
                </div>

                {{-- Loop Products dalam Kategori --}}
                @foreach($products as $index => $product)
                <div class="row py-2 border-bottom border-light">
                    <div class="col-1">{{ $loop->iteration }}</div>
                    <div class="col-6 fw-bold">{{ $product->title }}</div>
                    <div class="col-5">{{ $product->stock }}</div>
                </div>
                @endforeach

            @empty
                <div class="text-center text-muted py-5">
                    No products found.
                </div>
            @endforelse

        </div>
    </div>
</div>
@endsection