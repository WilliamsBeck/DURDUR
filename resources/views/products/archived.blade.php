@extends('layouts.app')

@section('title', 'Archived Products')

@section('content')

<div class="container-fluid">
    {{-- Main Content Card --}}
    <div class="main-content-card">

        {{-- 1. TITLE --}}
        <h3>Archived Products</h3>

        {{-- 2. CONTROLS --}}
        <div class="table-controls">
            {{-- Tombol Back --}}
            <a href="{{ route('products.index') }}" class="btn btn-secondary rounded-pill px-4 py-2 fw-bold">
                <i class="fas fa-arrow-left me-2"></i> Back to Active List
            </a>
        </div>

        {{-- 3. TABLE --}}
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center" width="5%">#</th>
                        <th class="text-center" width="10%">Image</th>
                        <th width="25%">Title</th>
                        <th width="20%">Category</th>
                        <th width="20%">Supplier</th>
                        <th class="text-center" width="20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($products as $product)
                    <tr>
                        {{-- Iteration --}}
                        <td class="text-center text-muted">{{ $loop->iteration }}</td>

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
                        <td class="fw-bold-dark text-muted">{{ $product->title }}</td>
                        
                        {{-- Category --}}
                        <td class="text-muted">{{ $product->category_product->product_category_name ?? 'N/A' }}</td>

                        {{-- Supplier --}}
                        <td class="text-muted">{{ $product->supplier->supplier_name ?? 'N/A' }}</td>

                        {{-- Action (Restore) --}}
                        <td>
                            <div class="action-icons">
                                <form id="restore-form-{{ $product->id }}" 
                                      action="{{ route('products.restore', $product->id) }}" 
                                      method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    {{-- PERBAIKAN: Menggunakan .btn-green-solid --}}
                                    <button type="button" 
                                            class="btn-circle btn-green-solid btn-restore" 
                                            title="Restore Product"
                                            data-product-id="{{ $product->id }}"
                                            data-product-title="{{ $product->title }}">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-archive fa-3x mb-3 text-light"></i><br>
                            No archived products found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-4">
            {{ $products->links() }}
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

                const productId = this.getAttribute('data-product-id');
                const productTitle = this.getAttribute('data-product-title');
                const form = document.getElementById(`restore-form-${productId}`);

                Swal.fire({
                    // HTML Custom Icon Hijau
                    html: `
                        <div style="width: 90px; height: 90px; background-color: rgba(16, 185, 129, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                            <span style="font-size: 3.5rem; font-weight: 700; color: #10b981; line-height: 1;"><i class="fas fa-undo"></i></span>
                        </div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 0.5rem; line-height: 1.4;">
                            Restore <strong>"${productTitle}"</strong> ?
                        </div>
                        <div style="font-size: 0.95rem; color: #666; margin-bottom: 1.5rem;">
                            This product will be moved back to the active list.
                        </div>
                    `,
                    icon: null,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Restore',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow-lg p-4',
                        // Style inline manual via class bootstrap/custom agar tombol popup juga hijau
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