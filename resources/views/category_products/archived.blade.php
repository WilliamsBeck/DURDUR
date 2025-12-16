@extends('layouts.app')

@section('title', 'Archived Categories')

@section('content')

<div class="container-fluid">
    {{-- Main Content Card (.main-content-card dari transaction.css) --}}
    <div class="main-content-card">

        {{-- 1. TITLE --}}
        <h3>Archived Categories</h3>

        {{-- 2. CONTROLS --}}
        <div class="table-controls">
            {{-- Tombol Back to List (Rounded Pill) --}}
            <a href="{{ route('category_products.index') }}" class="btn btn-secondary rounded-pill px-4 py-2 fw-bold">
                <i class="fas fa-arrow-left me-2"></i> Back to Active List
            </a>

            {{-- Search Bar (Optional) --}}
            {{-- <div class="search-bar-new"> ... </div> --}}
        </div>

        {{-- 3. TABLE --}}
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center" width="10%">ID</th>
                        <th width="40%">Category Name</th>
                        <th width="20%">Deleted At</th>
                        <th class="text-center" width="20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($category_products as $category)
                    <tr>
                        {{-- ID --}}
                        <td class="text-center text-muted">#{{ $category->id }}</td>

                        {{-- Name --}}
                        <td class="fw-bold-dark text-muted">{{ $category->product_category_name }}</td>
                        
                        {{-- Deleted Date --}}
                        <td class="text-muted">
                            {{ $category->deleted_at ? $category->deleted_at->format('d M Y') : '-' }}
                        </td>

                        {{-- Action (Restore) --}}
                        <td>
                            <div class="action-icons">
                                <form id="restore-form-{{ $category->id }}" 
                                      action="{{ route('category_products.restore', $category->id) }}" 
                                      method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    {{-- Tombol Restore (Hijau) --}}
                                    <button type="button" 
                                            class="btn-circle btn-green-solid btn-restore" 
                                            title="Restore Category"
                                            data-category-id="{{ $category->id }}"
                                            data-category-name="{{ $category->product_category_name }}">
                                        <i class="fas fa-undo text-white"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-archive fa-3x mb-3 text-light"></i><br>
                            No archived categories found.
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
{{-- Script SweetAlert2 untuk Konfirmasi Restore --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-restore').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); 

                const categoryId = this.getAttribute('data-category-id');
                const categoryName = this.getAttribute('data-category-name');
                const form = document.getElementById(`restore-form-${categoryId}`);

                Swal.fire({
                    // HTML Custom Icon Hijau (Reuse dari Product Restore)
                    html: `
                        <div style="width: 90px; height: 90px; background-color: rgba(16, 185, 129, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                            <span style="font-size: 3.5rem; font-weight: 700; color: #10b981; line-height: 1;"><i class="fas fa-undo"></i></span>
                        </div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 0.5rem; line-height: 1.4;">
                            Restore Category <strong>"${categoryName}"</strong> ?
                        </div>
                        <div style="font-size: 0.95rem; color: #666; margin-bottom: 1.5rem;">
                            It will be moved back to the active list.
                        </div>
                    `,
                    icon: null,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Restore',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow-lg p-4',
                        confirmButton: 'btn btn-success px-4 py-2 mx-1 rounded-pill fw-bold', 
                        cancelButton: 'btn btn-light px-4 py-2 mx-1 rounded-pill text-muted fw-bold'
                    },
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush