@extends('layouts.app')

@section('title', 'Product Management')

@section('content')

    <div class="main-content-card">
        {{-- Pesan Notifikasi Sukses --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-controls">
            {{-- Tombol Add New Product --}}
            <a href="{{ route('products.create') }}" class="btn add-btn">
                <i class="fa-solid fa-plus"></i>
                Add New Product
            </a>

            {{-- Tombol Lihat Arsip Produk (Style Disesuaikan dengan Contoh Anda) --}}
            <a href="{{ route('products.archived') }}" class="btn btn-warning" style="margin-left: 10px; background-color: #f7b825; color: #333; border: none;">
                <i class="fa-solid fa-archive"></i>
                Lihat Arsip Produk
            </a>
            
            {{-- Search Bar --}}
            <div class="search-bar-new">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="searchInput" name="search" placeholder="Search products..." class="form-control" value="{{ request('search') }}">
                <span class="clear-search-btn" id="clearSearchBtn" style="{{ request('search') ? 'display:block;' : 'display:none;' }}">&times;</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 100px; text-align: center;">Image</th>
                        <th style="width: auto;">Title</th>
                        <th style="width: 15%;">Category</th>
                        <th style="width: 15%;">Supplier</th>
                        
                        {{-- PERBAIKAN 1: Rata Kanan untuk Price --}}
                        <th style="width: 120px; text-align: right;">Price</th>
                        
                        {{-- PERBAIKAN 2: Rata Tengah untuk Stock --}}
                        <th style="width: 80px; text-align: center;">Stock</th>
                        
                        {{-- PERBAIKAN 3: Rata Tengah untuk Actions --}}
                        <th class="text-center" style="width: 120px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="text-center">
                                <img src="{{ asset('storage/images/' . $product->image) }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" alt="Product Image">
                            </td>
                            <td><strong>{{ $product->title }}</strong></td>
                            
                            <td>
                                {{ $product->category_product->product_category_name ?? 'No Category' }}
                            </td>

                            <td>
                                {{ $product->supplier->supplier_name ?? 'No Supplier' }}
                            </td>

                            {{-- PERBAIKAN: Rata Kanan untuk data Price --}}
                            <td style="text-align: right;">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            
                            {{-- PERBAIKAN: Rata Tengah untuk data Stock --}}
                            <td style="text-align: center;">{{ $product->stock }}</td>
                            
                            <td class="text-center">
                                <div class="action-icons">
                                    {{-- Tombol View --}}
                                    <a href="{{ route('products.show', $product->id) }}" title="Show Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('products.edit', $product->id) }}" title="Edit Product">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    
                                    {{-- Tombol Delete/Archive (Menggunakan Form) --}}
                                    <form class="d-inline" action="{{ route('products.destroy', $product->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        {{-- Class btn-delete akan dipicu oleh SweetAlert --}}
                                        <button type="submit" class="btn-delete" title="Archive Product" data-name="{{ $product->title }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="alert alert-secondary mt-3">
                                    No Product Data Available.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>

@endsection

@push('scripts')
    {{-- SweetAlert dan Script Search (Tetap sama) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // SweetAlert untuk pesan sukses
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'SUCCESS',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        @endif

        // SweetAlert untuk konfirmasi hapus (sekarang Soft Delete/Arsip)
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const dataName = this.getAttribute('data-name');
                const form = this.closest('form');

                Swal.fire({
                    title: `Archive product "${dataName}"?`,
                    text: "This product will be moved to archive and can be restored later.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Archive it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Script untuk Search Bar
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');

        // Menggunakan event keyup untuk memicu pencarian saat Enter ditekan atau membersihkan saat tombol clear diklik
        searchInput.addEventListener('keyup', function(event) {
            clearSearchBtn.style.display = this.value.length > 0 ? 'block' : 'none';
            if (event.key === 'Enter') {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('search', this.value);
                currentUrl.searchParams.delete('page');
                window.location.href = currentUrl.toString();
            }
        });
        
        clearSearchBtn.addEventListener('click', function() {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.delete('search');
            currentUrl.searchParams.delete('page');
            window.location.href = currentUrl.toString();
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (searchInput.value && searchInput.value.length > 0) {
                clearSearchBtn.style.display = 'block';
            }
        });
    </script>
@endpush