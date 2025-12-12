@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="form-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Product Detail</h3>
                    </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="product-image-container rounded" style="width: 100%; height: 250px; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            @if ($product->image)
                                <img src="{{ asset('storage/images/'.$product->image) }}" class="img-fluid" alt="{{ $product->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="font-size: 40px; color: #ccc;">⛰️</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-8">
                        <h4 class="mb-4" style="font-weight: bold;">{{ $product->title }}</h4>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted" style="font-size: 14px;">Category</label>
                                <p class="fs-6 fw-bold mb-0">{{ $product->category_product->product_category_name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted" style="font-size: 14px;">Supplier</label>
                                <p class="fs-6 fw-bold mb-0">{{ $product->supplier->supplier_name ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted" style="font-size: 14px;">Cost Price</label>
                                <p class="fs-5 fw-bold mb-0">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted" style="font-size: 14px;">Sales Price</label>
                                <p class="fs-5 fw-bold mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted" style="font-size: 14px;">Stock</label>
                                <p class="fs-5 fw-bold mb-0">{{ $product->stock ?? 0 }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted" style="font-size: 14px;">Description</label>
                            <div class="p-3 bg-light rounded product-description-box" style="border: 1px solid #e0e0e0; min-height: 100px;">
                                {!! $product->description !!}
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary me-2">
                                Back
                            </a>
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection