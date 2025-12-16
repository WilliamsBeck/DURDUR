@extends('layouts.app')

@section('content')
{{-- Load CSS External Form --}}
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- Container Utama (.form-card) --}}
            <div class="form-card">
                
                {{-- Header: Title & Back Button --}}
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <h3>Product Detail</h3>
                    {{-- Tombol Back menggunakan style .btn-cancel --}}
                    <a href="{{ route('products.index') }}" class="btn-cancel text-decoration-none">
                        <i class="fas fa-arrow-left me-2"></i> Back
                    </a>
                </div>
                
                <div class="row g-5">
                    {{-- KOLOM KIRI: GAMBAR --}}
                    <div class="col-lg-4">
                        <div class="d-flex align-items-center justify-content-center bg-light overflow-hidden position-relative" 
                             style="height: 350px; border-radius: 20px; border: 1px solid #f1f1f1;">
                            
                            @if ($product->image)
                                <img src="{{ asset('storage/images/'.$product->image) }}" 
                                     class="img-fluid w-100 h-100" 
                                     alt="{{ $product->title }}" 
                                     style="object-fit: cover;">
                            @else
                                <div class="text-center text-muted">
                                    <i class="fas fa-image fa-4x mb-3 text-secondary"></i>
                                    <p class="mb-0 fw-bold">No Image</p>
                                </div>
                            @endif

                            {{-- Badge Stock Overlay --}}
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge rounded-pill px-3 py-2 shadow-sm {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: DETAIL INFO --}}
                    <div class="col-lg-8">
                        {{-- Product Title --}}
                        <h2 class="fw-bold text-dark mb-4">{{ $product->title }}</h2>
                        
                        {{-- Info Grid --}}
                        <div class="row g-4 mb-4">
                            {{-- Category --}}
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <div class="fs-5 fw-bold text-dark">
                                    {{ $product->category_product->product_category_name ?? '-' }}
                                </div>
                            </div>

                            {{-- Supplier --}}
                            <div class="col-md-6">
                                <label class="form-label">Supplier</label>
                                <div class="fs-5 fw-bold text-dark">
                                    {{ $product->supplier->supplier_name ?? '-' }}
                                </div>
                            </div>

                            {{-- Cost Price --}}
                            <div class="col-md-6">
                                <label class="form-label">Cost Price</label>
                                <div class="fs-5 fw-bold text-dark">
                                    Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                                </div>
                            </div>

                            {{-- Sales Price --}}
                            <div class="col-md-6">
                                <label class="form-label">Sales Price</label>
                                <div class="fs-5 fw-bold text-success">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </div>
                            </div>

                            {{-- Current Stock --}}
                            <div class="col-12">
                                <label class="form-label">Current Stock</label>
                                <div class="fs-4 fw-bold text-dark">
                                    {{ $product->stock ?? 0 }} <span class="fs-6 fw-normal text-muted">Units</span>
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="mb-5">
                            <label class="form-label">Description</label>
                            {{-- Menggunakan background style mirip input readonly agar konsisten --}}
                            <div class="p-3" style="background-color: #f8f9fa; border-radius: 12px; border: 1px solid #f1f1f1; min-height: 100px;">
                                {!! $product->description ?? '<span class="text-muted">No description provided.</span>' !!}
                            </div>
                        </div>
                        
                        {{-- Tombol Edit (.btn-save warna Lime) --}}
                        <div class="d-flex justify-content-end border-top pt-4">
                            <a href="{{ route('products.edit', $product->id) }}" class="btn-save text-decoration-none text-center" style="display: inline-block;">
                                <i class="fas fa-edit me-2"></i> Edit Product
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection