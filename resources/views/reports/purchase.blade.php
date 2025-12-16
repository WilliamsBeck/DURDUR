@extends('layouts.app')

@section('title', 'Purchasement Report')

@section('content')

<div class="container-fluid">
    
    {{-- Container Utama (.main-content-card dari transaction.css) --}}
    <div class="main-content-card">

        {{-- HEADER TITLE --}}
        <h3 class="mb-4 fw-bold">Report</h3>

        {{-- 1. TAB MENU (Lime Active State) --}}
        <div class="report-tabs">
            <a href="{{ route('reports.sales') }}" class="report-tab-item">
                Sales Report
            </a>
            <a href="{{ route('reports.purchasement') }}" class="report-tab-item active">
                Purchasement Report
            </a>
            <a href="{{ route('reports.product_sales') }}" class="report-tab-item">
                Product Sold Report
            </a>
            <a href="{{ route('reports.remaining_stock') }}" class="report-tab-item">
                Remaining Stock Report
            </a>
        </div>

        {{-- 2. FILTER & DOWNLOAD SECTION --}}
        <form action="{{ route('reports.purchasement') }}" method="GET" class="report-filter-container">
            
            {{-- Date Filter Group --}}
            <div class="d-flex align-items-center">
                <span class="report-label">Date</span>
                
                {{-- Start Date --}}
                <input type="date" name="start_date" value="{{ $startDate }}" 
                       class="report-date-input" 
                       onchange="this.form.submit()">
                
                <span class="report-separator">-</span>
                
                {{-- End Date --}}
                <input type="date" name="end_date" value="{{ $endDate }}" 
                       class="report-date-input" 
                       onchange="this.form.submit()">
            </div>
        </form>

        {{-- 3. CONTENT DATA --}}
        <div class="px-1">
            
            {{-- SECTION: GENERAL STATS --}}
            <div class="mb-4">
                {{-- Total Purchase --}}
                <div class="report-row">
                    <div class="report-item-label">Total Purchase Amount</div>
                    <div class="report-item-amount">Rp. {{ number_format($totalPurchaseAmount, 0, ',', '.') }}</div>
                </div>

                {{-- Total Bill --}}
                <div class="report-row">
                    <div class="report-item-label">Total Bill</div>
                    <div class="report-item-amount" style="font-weight: 600;">{{ $totalBill }}</div>
                </div>

                {{-- Average --}}
                <div class="report-row">
                    <div class="report-item-label">Average Purchase per Bill</div>
                    <div class="report-item-amount">Rp. {{ number_format($averagePurchase, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="report-divider"></div>

            {{-- SECTION: TRANSACTION STATUS BREAKDOWN --}}
            <div class="mb-4">
                {{-- Received (Done) --}}
                <div class="report-row">
                    <div class="report-item-label">Received Purchases (Done)</div>
                    <div class="report-item-count">{{ $receivedCount }}</div>
                    <div class="report-item-amount">Rp. {{ number_format($receivedValue, 0, ',', '.') }}</div>
                </div>

                {{-- Pending --}}
                <div class="report-row">
                    <div class="report-item-label">Pending Purchases</div>
                    <div class="report-item-count">{{ $pendingCount }}</div>
                    <div class="report-item-amount">Rp. {{ number_format($pendingValue, 0, ',', '.') }}</div>
                </div>

                {{-- Void --}}
                <div class="report-row">
                    <div class="report-item-label">Cancelled Purchases (Void)</div>
                    <div class="report-item-count">{{ $voidCount }}</div>
                    <div class="report-item-amount">Rp. {{ number_format($voidValue, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="report-divider"></div>

            {{-- SECTION: SUPPLIER BREAKDOWN --}}
            <div class="mb-3">
                <h5 class="fw-bold mb-3">Top Suppliers (Completed)</h5>
                @forelse($supplierStats as $supplier)
                    <div class="report-row">
                        <div class="report-item-label">{{ $supplier['supplier_name'] }}</div>
                        <div class="report-item-count">{{ $supplier['count'] }}</div>
                        <div class="report-item-amount">Rp. {{ number_format($supplier['total_amount'], 0, ',', '.') }}</div>
                    </div>
                @empty
                    <div class="text-muted fst-italic py-3">No completed purchase data available for this period.</div>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection