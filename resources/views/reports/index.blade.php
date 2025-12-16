@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')

<div class="container-fluid">
    
    {{-- Container Utama (.main-content-card dari transaction.css) --}}
    <div class="main-content-card">

        {{-- HEADER TITLE --}}
        <h3 class="mb-4 fw-bold">Report</h3>

        {{-- 1. TAB MENU (Lime Active State) --}}
        <div class="report-tabs">
            <a href="{{ route('reports.sales') }}" class="report-tab-item active">
                Sales Report
            </a>
            <a href="{{ route('reports.purchasement') }}" class="report-tab-item">
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
        <form action="{{ route('reports.sales') }}" method="GET" class="report-filter-container">
            
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
            
            {{-- SECTION: GENERAL STATS (Layout Baris Sederhana seperti Mockup) --}}
            <div class="mb-4">
                {{-- Total Sales --}}
                <div class="report-row">
                    <div class="report-item-label">Total Sales Amount</div>
                    <div class="report-item-amount">Rp. {{ number_format($totalSales, 0, ',', '.') }}</div>
                </div>

                {{-- Total Bill --}}
                <div class="report-row">
                    <div class="report-item-label">Total Bill</div>
                    <div class="report-item-amount" style="font-weight: 600;">{{ $totalBill }}</div>
                </div>

                {{-- Average Sales --}}
                <div class="report-row">
                    <div class="report-item-label">Average Sales per Bill</div>
                    <div class="report-item-amount">Rp. {{ number_format($averageSales, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="report-divider"></div>

            {{-- SECTION: TRANSACTION STATUS --}}
            <div class="mb-4">
                {{-- Successful Transaction --}}
                <div class="report-row">
                    <div class="report-item-label">Successfull Transaction</div>
                    <div class="report-item-count">{{ $successfulTrxCount }}</div>
                    <div class="report-item-amount">Rp. {{ number_format($successfulTrxValue, 0, ',', '.') }}</div>
                </div>

                {{-- Void Transaction --}}
                <div class="report-row">
                    <div class="report-item-label">Void Transaction</div>
                    <div class="report-item-count">{{ $voidTrxCount }}</div>
                    <div class="report-item-amount">Rp. {{ number_format($voidTrxValue, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="report-divider"></div>

            {{-- SECTION: PAYMENT BREAKDOWN --}}
            <div class="mb-3">
                @forelse($paymentStats as $payment)
                    <div class="report-row">
                        <div class="report-item-label">{{ $payment->method_name }}</div>
                        <div class="report-item-count">{{ $payment->total_count }}</div>
                        <div class="report-item-amount">Rp. {{ number_format($payment->total_amount, 0, ',', '.') }}</div>
                    </div>
                @empty
                    <div class="text-muted fst-italic py-3">No payment data available for this period.</div>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection