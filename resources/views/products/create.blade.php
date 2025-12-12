@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Add New Product</h3>
                    <a href="{{ route('products.index') }}" class="btn btn-cancel">
                        <i class="fa-solid fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="title" class="form-label">Product Name</label>
                        <input type="text" id="title" class="form-control" name="title" value="{{ old('title') }}" placeholder="Enter product name" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="product_category_id" class="form-label">Category</label>
                            <select id="product_category_id" name="product_category_id" class="form-select" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->product_category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select id="supplier_id" name="supplier_id" class="form-select" required>
                                <option value="">Select supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->supplier_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter product description" required>{{ old('description') }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="cost_price" class="form-label">Cost Price</label>
                            <input type="number" id="cost_price" name="cost_price" class="form-control" value="{{ old('cost_price') }}" placeholder="0" required>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="price" class="form-label">Sales Price</label>
                            <input type="number" id="price" name="price" class="form-control" value="{{ old('price') }}" placeholder="0" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
                        <div class="form-text text-muted">Format: JPG, JPEG, PNG. Max: 2MB</div>
                    </div>
                    
                    <div class="form-actions text-end"> 
                        <a href="{{ route('products.index') }}" class="btn btn-cancel me-2">Cancel</a>
                        <button type="submit" class="btn btn-save">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection