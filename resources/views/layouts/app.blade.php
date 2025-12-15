<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    {{-- CSS LINKS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- Load CSS Custom --}}
    <link href="{{ asset('css/transaction.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/transaction-form.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body>
    {{-- SIDEBAR KIRI --}}
    <div class="sidebar">
        <div class="brand">
            {{-- Pastikan logo ada, atau ganti dengan Text jika logo belum dimuat --}}
            <img src="{{ asset('storage/images/logo.png') }}" alt="EM Logo" style="height: 50px;">
        </div>

        {{-- 1. DASHBOARD --}}
        <a href="{{ route('dashboard') }}" class="nav-link @if(request()->routeIs('dashboard')) active @endif">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>

        {{-- 2. SALES --}}
        <a href="{{ route('transactions.create') }}" class="nav-link @if(request()->routeIs('transactions.create')) active @endif">
            <i class="fas fa-cash-register"></i> Create Sales
        </a>
        <a href="{{ route('transactions.index') }}" class="nav-link @if(request()->routeIs('transactions.index') || request()->routeIs('transactions.show') || request()->routeIs('transactions.void.form')) active @endif">
            <i class="fas fa-history"></i> Sales History
        </a>

        {{-- 3. PURCHASES --}}
        <a href="{{ route('purchases.create') }}" class="nav-link @if(request()->routeIs('purchases.create')) active @endif">
            <i class="fas fa-cart-plus"></i> Create Purchase
        </a>
        <a href="{{ route('purchases.index') }}" class="nav-link @if(request()->routeIs('purchases.index') || request()->routeIs('purchases.show') || request()->routeIs('purchases.void.form')) active @endif">
            <i class="fas fa-clipboard-list"></i> Purchase History
        </a>

        {{-- 4. MASTER DATA --}}
        <a href="{{ route('products.index') }}" class="nav-link @if(request()->routeIs('products.*')) active @endif">
            <i class="fas fa-box"></i> Product
        </a>
        <a href="{{ route('category_products.index') }}" class="nav-link @if(request()->routeIs('category_products.*')) active @endif">
            <i class="fas fa-tags"></i> Product Category
        </a>
        <a href="{{ route('suppliers.index') }}" class="nav-link @if(request()->routeIs('suppliers.*') && !request()->routeIs('purchases.*')) active @endif">
            <i class="fas fa-truck"></i> Supplier
        </a>

        {{-- 5. STOCK ADJUSTMENT --}}
        <a href="{{ route('stock-adjustments.index') }}" class="nav-link @if(request()->routeIs('stock-adjustments.*')) active @endif">
            <i class="fas fa-sliders-h"></i> Stock Adjustment
        </a>

        {{-- 6. REPORT --}}
        <a href="{{ route('reports.sales') }}" class="nav-link @if(request()->routeIs('reports.*')) active @endif">
           <i class="fas fa-file-alt"></i> Report
        </a>

        {{-- LOGOUT --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="main-content">
        <div class="container-fluid">
            {{-- Page Title (Opsional, jika sudah ada di dalam view child, ini bisa dihapus) --}}
            {{-- <h2 class="page-title">@yield('title')</h2> --}}
            
            @yield('content')
        </div>
    </div>


    {{-- JAVASCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>