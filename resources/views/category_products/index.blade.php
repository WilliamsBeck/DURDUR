@extends('layouts.app')

@section('title', 'Category Management')

@section('content')

<div class="container-fluid">
    {{-- Main Content Card (.main-content-card dari transaction.css) --}}
    <div class="main-content-card">

        {{-- 1. TITLE --}}
        <h3>Category Management</h3>

        {{-- Flash Message --}}
        @if (session('success'))
            <div class="alert alert-success border-0 bg-success-subtle rounded-3 mb-4">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 2. CONTROLS --}}
        <div class="table-controls">
            <a href="{{ route('category_products.create') }}" class="btn add-btn">
                <i class="fa-solid fa-plus"></i>
                Add New Category
            </a>

            {{-- TOMBOL BARU: LIHAT ARSIP --}}
            <a href="{{ route('category_products.archived') }}" class="btn btn-warning" style="margin-left: 10px; background-color: #f7b825; color: #333; border: none;">
                <i class="fa-solid fa-archive"></i>
                Lihat Arsip Kategori
            </a>
            
            <div class="search-bar-new">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="searchInput" name="search" placeholder="Search categories..." class="form-control" value="{{ request('search') }}">
                <span class="clear-search-btn" id="clearSearchBtn" style="{{ request('search') ? 'display:block;' : 'display:none;' }}">&times;</span>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Category Name</th>
                        <th style="width: 150px;">Created At</th>
                        <th class="text-center" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($category_products as $category)
                        <tr>
                            {{-- ID --}}
                            <td class="text-center text-muted">#{{ $category->id }}</td>
                            
                            {{-- Name --}}
                            <td class="fw-bold-dark">{{ $category->product_category_name }}</td>
                            
                            {{-- Date --}}
                            <td class="text-muted">
                                {{ $category->created_at ? $category->created_at->format('d F Y') : '-' }}
                            </td>
                            
                            {{-- Actions --}}
                            <td>
                                <div class="action-icons">
                                    {{-- Edit (Ungu) --}}
                                    <a href="{{ route('category_products.edit', $category->id) }}" class="btn-circle btn-purple-solid" title="Edit Category">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    {{-- PERUBAHAN: Tombol sekarang melakukan Soft Delete (Arsip) --}}
                                    <form class="d-inline" action="{{ route('category_products.destroy', $category->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete" title="Archive Category" data-name="{{ $category->product_category_name }}" data-action="archive">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-light"></i><br>
                                No Categories Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-4">
            {{ $category_products->appends(request()->query())->links() }}
        </div>
    </div>
</div>

@endsection

@push('scripts')
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // SweetAlert untuk pesan sukses
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

        // SweetAlert untuk konfirmasi arsip
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const dataName = this.getAttribute('data-name');
                const form = this.closest('form');

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: action === 'restore' ? '#28a745' : '#d33', // Warna hijau untuk restore, merah untuk arsip
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Script untuk Search Bar (Tetap sama)
        const searchInput = document.getElementById('searchInput');
        if(searchInput){
            searchInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('searchForm').submit();
                }
            });
        }
    </script>
@endpush