<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    
    <link href="{{ asset('css/transaction.css') }}" rel="stylesheet"> 
    <link href="{{ asset('css/transaction-form.css') }}" rel="stylesheet">
</head>



<body>
    {{-- SIDEBAR KIRI --}}
    <div class="sidebar">
       <div class="brand">
            <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" style="height: 100px;">
        </div>
        <a href="{{ route('dashboard') }}" class="nav-link @if(request()->routeIs('dashboard')) active @endif">
            <i class="fas fa-chart-line me-2"></i> Dashboard
        </a>
        <a href="{{ route('transactions.create') }}" class="nav-link @if(request()->routeIs('transactions.create')) active @endif">
            <i class="fas fa-cash-register me-2"></i> Create Transaction
        </a>
        <a href="{{ route('transactions.index') }}" class="nav-link @if(request()->routeIs('transactions.*') && !request()->routeIs('transactions.create')) active @endif">
            <i class="fas fa-history me-2"></i> Transaction History
        </a>
        <a href="{{ route('products.index') }}" class="nav-link @if(request()->routeIs('products.*')) active @endif">
            <i class="fas fa-box me-2"></i> Product
        </a>
        <a href="{{ route('category_products.index') }}" class="nav-link @if(request()->routeIs('category_products.*')) active @endif">
            <i class="fas fa-tags me-2"></i> Product Category
        </a>
        <a href="{{ route('suppliers.index') }}" class="nav-link @if(request()->routeIs('suppliers.*')) active @endif">
            <i class="fas fa-truck me-2"></i> Supplier
        </a>

        <form action="{{ route('logout') }}" method="POST" class="mt-auto text-center mb-3">
            @csrf
            <button type="submit" class="btn btn-danger w-75">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>



    </div>

    {{-- KONTEN UTAMA --}}
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="page-title">@yield('title')</h2>
            @yield('content')
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    @stack('scripts')
</body>
</html>