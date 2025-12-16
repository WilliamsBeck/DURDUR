@extends('layouts.app')

@section('title', 'Product Management')

@section('content')

<div class="container-fluid">
    {{-- Main Content Card (.main-content-card dari CSS global) --}}
    <div class="main-content-card">

        {{-- 1. TITLE --}}
        <h3>Product Management</h3>

        {{-- Flash Message (Opsional, jika SweetAlert gagal load) --}}
        @if (session('success'))
            <div class="alert alert-success border-0 bg-success-subtle rounded-3 mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 2. CONTROLS --}}
        <div class="table-controls">
            <div class="d-flex gap-2">
                {{-- Tombol Add New Product (Hitam Pill .add-btn) --}}
                <a href="{{ route('products.create') }}" class="add-btn">
                    <i class="fas fa-plus"></i> Add Product
                </a>

                {{-- Tombol Lihat Arsip (Menggunakan style inline sementara agar beda warna, atau buat class baru di CSS global nanti) --}}
                <a href="{{ route('products.archived') }}" class="btn-circle btn-purple-solid text-white text-decoration-none px-4" 
                   style="border-radius: 50px; width: auto; height: auto; padding: 0.8rem 1.5rem;" title="View Archived">
                    <i class="fas fa-archive me-2"></i> Archive
                </a>
            </div>

            {{-- Search Bar (Abu-abu Pill .search-bar-new) --}}
            <form method="GET" action="{{ route('products.index') }}" class="m-0" id="searchForm">
                <div class="search-bar-new">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" name="search" placeholder="Search products..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('products.index') }}" class="text-muted ms-2" title="Clear Search">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- 3. TABLE --}}
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center" width="10%">Image</th>
                        <th width="25%">Title</th>
                        <th width="15%">Category</th>
                        <th width="15%">Supplier</th>
                        <th width="15%" class="text-end">Price</th>
                        <th width="10%" class="text-center">Stock</th>
                        <th width="10%" class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($products as $product)
                    <tr>
                        {{-- Image --}}
                        <td class="text-center">
                            @if($product->image)
                                <img src="{{ asset('storage/images/' . $product->image) }}" 
                                     class="rounded" 
                                     style="width: 50px; height: 50px; object-fit: cover;" 
                                     alt="{{ $product->title }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" 
                                     style="width: 50px; height: 50px; margin: 0 auto;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>

                        {{-- Title --}}
                        <td class="fw-bold-dark">{{ $product->title }}</td>
                        
                        {{-- Category --}}
                        <td>{{ $product->category_product->product_category_name ?? '-' }}</td>

                        {{-- Supplier --}}
                        <td>{{ $product->supplier->supplier_name ?? '-' }}</td>

                        {{-- Price (Right Aligned) --}}
                        <td class="text-end fw-bold-dark">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </td>
                        
                        {{-- Stock (Centered) --}}
                        <td class="text-center">
                            @if($product->stock <= 5)
                                <span class="badge bg-danger rounded-pill px-3">{{ $product->stock }}</span>
                            @else
                                <span class="badge bg-success rounded-pill px-3">{{ $product->stock }}</span>
                            @endif
                        </td>
                        
                        {{-- Actions --}}
                        <td>
                            <div class="action-icons">
                                {{-- View --}}
                                <a href="{{ route('products.show', $product->id) }}" class="btn-circle btn-purple-solid" title="View Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                {{-- Edit (Menggunakan warna kuning/orange, bisa tambahkan class btn-yellow-solid di CSS nanti, sementara pakai style inline atau reuse yang ada) --}}
                                <a href="{{ route('products.edit', $product->id) }}" class="btn-circle" style="background-color: #f59e0b;" title="Edit">
                                    <i class="fas fa-pencil-alt text-white"></i>
                                </a>
                                
                                {{-- Archive/Delete --}}
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-circle btn-red-solid btn-delete" title="Archive" data-name="{{ $product->title }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 text-light"></i><br>
                            No Products Found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-4">
            {{ $products->appends(request()->query())->links() }}
        </div>

    </div>
</div>
@endsection

@push('scripts')
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // SweetAlert Success
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000,
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                }
            });
        @endif

        // SweetAlert untuk konfirmasi Archive (Custom Red Theme)
const deleteButtons = document.querySelectorAll('.btn-delete');
deleteButtons.forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();
        const dataName = this.getAttribute('data-name');
        const form = this.closest('form');

        Swal.fire({
            // HTML Custom untuk meniru gambar referensi (Ikon Merah)
            html: `
                <div class="archive-icon-bg">
                    <span class="archive-icon-text">!</span>
                </div>
                <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 0.5rem; line-height: 1.4;">
                    Are you sure you want to archive <strong>${dataName}</strong> ?
                </div>
                <div style="font-size: 0.95rem; color: #666; margin-bottom: 1.5rem;">
                    You can restore it later from the archive.
                </div>
            `,
            // Matikan icon bawaan
            icon: null,
            
            showCancelButton: true,
            confirmButtonText: 'Yes, Archive',
            cancelButtonText: 'Cancel',
            
            // Gunakan Class CSS dari transaction-form.css
            customClass: {
                popup: 'swal-archive-popup',
                confirmButton: 'btn-swal-confirm-red',
                cancelButton: 'btn-swal-cancel-grey',
                actions: 'swal2-actions'
            },
            buttonsStyling: false,
            reverseButtons: true, // Cancel kiri, Archive kanan
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
    </script>
@endpush