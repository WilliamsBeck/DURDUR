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
            <div class="d-flex gap-2">
                {{-- Tombol Add New Category (Hitam Pill) --}}
                <a href="{{ route('category_products.create') }}" class="add-btn">
                    <i class="fas fa-plus"></i> Add Category
                </a>

                {{-- Tombol Lihat Arsip (Ungu Pill - Konsisten dengan Product Index) --}}
                <a href="{{ route('category_products.archived') }}" 
                   class="btn-circle btn-purple-solid text-white text-decoration-none px-4" 
                   style="border-radius: 50px; width: auto; height: auto; padding: 0.8rem 1.5rem;" 
                   title="View Archived Categories">
                    <i class="fas fa-archive me-2"></i> Archives
                </a>
            </div>
            
            {{-- Search Bar (Abu-abu Pill) --}}
            <form method="GET" action="{{ route('category_products.index') }}" class="m-0" id="searchForm">
                <div class="search-bar-new">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" name="search" placeholder="Search categories..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('category_products.index') }}" class="text-muted ms-2" title="Clear Search">
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
                        <th class="text-center" style="width: 10%;">ID</th>
                        <th style="width: 40%;">Category Name</th>
                        <th style="width: 30%;">Created At</th>
                        <th class="text-center" style="width: 20%;">Actions</th>
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
                                    {{-- Edit (kuning) --}}
                                    <a href="{{ route('category_products.edit', $category->id) }}" class="btn-circle" style="background-color: #f59e0b;" title="Edit Category">
                                        <i class="fas fa-pencil-alt text-whitet"></i>
                                    </a>

                                    {{-- Archive (Merah) --}}
                                    <form class="d-inline" action="{{ route('category_products.destroy', $category->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                class="btn-circle btn-red-solid btn-delete" 
                                                title="Archive Category" 
                                                data-name="{{ $category->product_category_name }}">
                                            <i class="fas fa-trash-alt"></i>
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

        // SweetAlert untuk Konfirmasi Arsip (Custom Red Theme)
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const dataName = this.getAttribute('data-name');
                const form = this.closest('form');

                Swal.fire({
                    // HTML Custom (Ikon Merah & Teks)
                    html: `
                        <div class="archive-icon-bg">
                            <span class="archive-icon-text">!</span>
                        </div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 0.5rem; line-height: 1.4;">
                            Are you sure you want to archive <strong>"${dataName}"</strong> ?
                        </div>
                        <div style="font-size: 0.95rem; color: #666; margin-bottom: 1.5rem;">
                            Category will be moved to archive.
                        </div>
                    `,
                    icon: null,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Archive',
                    cancelButtonText: 'Cancel',
                    // Menggunakan Class dari transaction-form.css
                    customClass: {
                        popup: 'swal-archive-popup',
                        confirmButton: 'btn-swal-confirm-red',
                        cancelButton: 'btn-swal-cancel-grey',
                        actions: 'swal2-actions'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Script Search Bar (Submit on Enter)
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