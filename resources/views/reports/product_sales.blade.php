@extends('layouts.app')

@section('title', 'Product Sold Report')

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
            <a href="{{ route('reports.product_sales') }}" class="report-tab-item active">
                Product Sold Report
            </a>
            <a href="{{ route('reports.remaining_stock') }}" class="report-tab-item">
                Remaining Stock Report
            </a>
        </div>

        {{-- 2. FILTER & DOWNLOAD --}}
        <form action="{{ route('reports.product_sales') }}" method="GET" class="report-filter-container">
            
            {{-- Date Filter --}}
            <div class="d-flex align-items-center">
                <span class="report-label">Date</span>
                
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="report-date-input" 
                       onchange="this.form.submit()">
                
                <span class="report-separator">-</span>
                
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="report-date-input" 
                       onchange="this.form.submit()">
            </div>
            
            {{-- Download Button --}}
            <button type="button" class="btn-download-pdf">
                Download PDF
            </button>
        </form>

        {{-- 3. CONTENT DATA --}}
        <div class="px-1">
            
            {{-- SECTION: GENERAL STATS --}}
            <div class="mb-4">
                <div class="report-row">
                    <div class="report-item-label">Total Product Quantity</div>
                    {{-- Menggunakan style font-weight agak tebal untuk angka quantity --}}
                    <div class="report-item-amount" style="font-weight: 600;">{{ $totalProductQuantity }}</div>
                </div>
                <div class="report-row">
                    <div class="report-item-label">Total Product Sales</div>
                    <div class="report-item-amount">Rp. {{ number_format($totalProductSales, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="report-divider"></div>

            {{-- SECTION: LIST PER KATEGORI --}}
            @forelse($reportData as $categoryName => $products)
                
                {{-- Header Kategori (Custom Style Background Abu Muda) --}}
                <div class="report-table-header mt-4 text-uppercase">
                    Category - {{ $categoryName }}
                </div>

                {{-- List Produk --}}
                <div class="mb-2">
                    @foreach($products as $product)
                        <div class="report-row">
                            {{-- Product Name (Font Normal agar beda dengan Label Utama) --}}
                            <div class="report-item-label" style="font-weight: 500; color: #374151;">
                                {{ $product['product_name'] }}
                            </div>
                            
                            {{-- Qty --}}
                            <div class="report-item-count">
                                {{ $product['total_qty'] }}
                            </div>
                            
                            {{-- Total Price --}}
                            <div class="report-item-amount">
                                Rp. {{ number_format($product['total_price'], 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

            @empty
                <div class="text-center text-muted py-5 fst-italic">
                    No product sales found in this period.
                </div>
            @endforelse

        </div>
    </div>
</div>
@endsection