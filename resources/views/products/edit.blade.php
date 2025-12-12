@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Edit Product</h3>
                    <a href="{{ route('products.index') }}" class="btn btn-cancel">
                         Back to List
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

                <form action="{{ route('products.update', $data['product']->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="title" class="form-label">Product Name</label>
                        <input type="text" id="title" class="form-control" name="title" value="{{ old('title', $data['product']->title) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="product_category_id" class="form-label">Category</label>
                            <select id="product_category_id" name="product_category_id" class="form-select" required>
                                @foreach ($data['categories'] as $category)
                                    <option value="{{ $category->id }}" {{ old('product_category_id', $data['product']->product_category_id) == $category->id ? 'selected' : '' }}>{{ $category->product_category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="supplier_id" class="form-label">Supplier</label>
                            <select id="supplier_id" name="supplier_id" class="form-select" required>
                                @foreach ($data['suppliers'] as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $data['product']->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->supplier_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="4" required>{{ old('description', $data['product']->description) }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="cost_price" class="form-label">Cost Price</label>
                            <input type="number" id="cost_price" name="cost_price" class="form-control" value="{{ old('cost_price', $data['product']->cost_price) }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <label for="price" class="form-label">Sales Price</label>
                            <input type="number" id="price" name="price" class="form-control" value="{{ old('price', $data['product']->price) }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label">Product Image (Leave empty if not changed)</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Current Image</label>
                        @if ($data['product']->image)
                            <div class="current-image-preview mt-2" style="width: 150px; height: 150px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <img src="{{ asset('/storage/images/'.$data['product']->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @else
                            <div class="current-image-preview mt-2" style="width: 150px; height: 150px; border: 1px solid #ccc; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                No Image
                            </div>
                        @endif
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