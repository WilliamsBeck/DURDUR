@extends('layouts.app')

@section('title', 'Remaining Stock Report')

@section('content')

<div class="container-fluid">
    
    {{-- Container Utama (.main-content-card dari transaction.css) --}}
    <div class="main-content-card">

        {{-- HEADER TITLE --}}
        <h3 class="mb-4 fw-bold">Report</h3>

        {{-- 1. TAB MENU --}}
        <div class="report-tabs">
            <a href="{{ route('reports.sales') }}" class="report-tab-item">
                Sales Report
            </a>
            <a href="{{ route('reports.purchasement') }}" class="report-tab-item">
                Purchasement Report
            </a>
            <a href="{{ route('reports.product_sales') }}" class="report-tab-item">
                Product Sold Report
            </a>
            <a href="{{ route('reports.remaining_stock') }}" class="report-tab-item active">
                Remaining Stock Report
            </a>
        </div>

        {{-- 2. ACTION BAR (Search & Buttons) --}}
        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
            
            {{-- Search Bar (Kiri) --}}
            <form action="{{ route('reports.remaining_stock') }}" method="GET" class="m-0 flex-grow-1" style="max-width: 400px;">
                <div class="search-bar-new">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search product or category..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('reports.remaining_stock') }}" class="text-muted ms-2" title="Clear">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>

            {{-- Buttons Group (Kanan) --}}
            <div class="d-flex gap-2">
                {{-- Tombol Stock Adjustment (Hitam Pill) --}}
                {{-- Pastikan route ini mengarah ke halaman yang benar, misal: stock-adjustments.create --}}
                <a href="{{ route('stock-adjustments.create') }}" class="add-btn text-decoration-none">
                    <i class="fas fa-boxes me-2"></i> Stock Adjustment
                </a>
            </div>
        </div>

        {{-- 3. CONTENT DATA --}}
        <div class="px-1">
            
            {{-- Header Kolom (Manual styling agar rapi) --}}
            <div class="d-flex text-muted fw-bold mb-2 px-3 small text-uppercase border-bottom pb-2">
                <div style="width: 5%;">No.</div>
                <div style="width: 75%;">Product Name</div>
                <div style="width: 20%; text-align: right;">Current Stock</div>
            </div>

            {{-- Loop Data --}}
            @forelse($reportData as $categoryName => $products)
                
                {{-- Category Header --}}
                <div class="report-table-header mt-3 text-uppercase">
                    Category - {{ $categoryName }}
                </div>

                {{-- List Products --}}
                <div class="mb-2">
                    @foreach($products as $index => $product)
                        <div class="report-row">
                            {{-- No --}}
                            <div style="width: 5%; color: #6b7280; font-weight: 500;">
                                {{ $loop->iteration }}
                            </div>
                            
                            {{-- Product Name --}}
                            <div class="report-item-label" style="width: 75%; font-weight: 600; color: #374151;">
                                {{ $product->title }}
                            </div>
                            
                            {{-- Stock --}}
                            <div class="report-item-amount" style="width: 20%; text-align: right;">
                                @if($product->stock <= 5)
                                    <span class="text-danger fw-bold">{{ $product->stock }}</span> <i class="fas fa-exclamation-circle text-danger small ms-1" title="Low Stock"></i>
                                @else
                                    {{ $product->stock }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            @empty
                <div class="text-center text-muted py-5 fst-italic">
                    <i class="fas fa-box-open fa-3x mb-3 text-light"></i><br>
                    No products found based on your search.
                </div>
            @endforelse

        </div>
    </div>
</div>
@endsection