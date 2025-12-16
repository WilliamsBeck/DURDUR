@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')

<div class="container-fluid">
    {{-- Main Content Card (.main-content-card dari transaction.css) --}}
    <div class="main-content-card">
        
        {{-- 1. TITLE --}}
        <h3>Stock Adjustment</h3>

        {{-- 2. CONTROLS --}}
        <div class="table-controls">
            {{-- Tombol Add (Hitam Pill) --}}
            <a href="{{ route('stock-adjustments.create') }}" class="add-btn">
                <i class="fas fa-plus"></i> Add Adjustment
            </a>

            {{-- Search Bar (Abu-abu Pill) --}}
            <form action="{{ route('stock-adjustments.index') }}" method="GET" class="m-0">
                <div class="search-bar-new">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search Adjustment..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('stock-adjustments.index') }}" class="text-muted ms-2" title="Clear Search">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success border-0 bg-success-subtle rounded-3 mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 3. TABLE --}}
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center" width="10%">ID</th>
                        <th width="30%">Date</th>
                        <th width="40%">Adjusted By</th>
                        <th width="20%" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adj)
                    <tr>
                        {{-- ID --}}
                        <td class="text-center text-muted">#{{ $adj->id }}</td>
                        
                        {{-- Date --}}
                        <td>
                            {{ $adj->transaction_date ? $adj->transaction_date->format('d M Y') : '-' }}
                            <small class="d-block text-muted">
                                {{ $adj->transaction_date ? $adj->transaction_date->format('H:i') : '' }}
                            </small>
                        </td>
                        
                        {{-- User --}}
                        <td class="fw-bold-dark">{{ $adj->user->name ?? 'Unknown' }}</td>
                        
                        {{-- Action --}}
                        <td>
                            <div class="action-icons">
                                {{-- Tombol Detail (Ungu - Konsisten dengan index lain) --}}
                                <a href="{{ route('stock-adjustments.show', $adj->id) }}" 
                                   class="btn-circle btn-purple-solid" 
                                   title="View Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-clipboard-list fa-3x mb-3 text-light"></i><br>
                            No adjustment data found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-4">
            {{ $adjustments->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection