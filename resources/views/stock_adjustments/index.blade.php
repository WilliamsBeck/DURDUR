@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')
{{-- Container Putih Utama (Sesuai CSS Anda) --}}
<div class="main-content-card">
    
    <div class="top-header">
        <h3>Stock Adjustment</h3>
    </div>

    {{-- Controls: Tombol Add (Hitam) & Search --}}
    <div class="table-controls">
        {{-- Tombol Add Hitam (Sesuai Figma & CSS .add-btn) --}}
        <a href="{{ route('stock-adjustments.create') }}" class="add-btn">
            <i class="fas fa-plus"></i> Add Adjustment
        </a>

        {{-- Search Bar (Sesuai Figma & CSS .search-bar-new) --}}
        <form action="{{ route('stock-adjustments.index') }}" method="GET">
            <div class="search-bar-new">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search Adjustment" value="{{ request('search') }}">
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel Data --}}
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- Header Sesuai Figma --}}
                    <th width="10%">ID</th>
                    <th width="30%">Date</th>
                    <th width="40%">Adjusted By</th>
                    <th width="20%" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($adjustments as $adj)
                <tr>
                    <td class="fw-bold">#{{ $adj->id }}</td>
                    {{-- Fix Error Format Date Null --}}
                    <td>{{ $adj->transaction_date ? $adj->transaction_date->format('d, F Y') : '-' }}</td>
                    <td>{{ $adj->user->name ?? 'Unknown' }}</td>
                    <td>
                        <div class="action-icons justify-content-center">
                            {{-- Tombol Mata Ungu (Inline Style agar sesuai Figma persis) --}}
                            <a href="{{ route('stock-adjustments.show', $adj->id) }}" 
                               style="background-color: #CEBEFF; color: black; border: none;">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No adjustment data found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $adjustments->links() }}
    </div>
</div>
@endsection