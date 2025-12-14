{{-- File: resources/views/products/archived.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Produk Diarsipkan (Inactive)</h2>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            <i class="fas fa-list"></i> Kembali ke Daftar Aktif
        </a>
    </div>

    {{-- SweetAlert Global Handler dari layouts/app.blade.php akan menangani pesan sukses/error di sini --}}

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <img src="{{ asset('storage/images/' . $product->image) }}" alt="{{ $product->title }}" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>{{ $product->title }}</td>
                            <td>{{ $product->category_product->product_category_name ?? 'N/A' }}</td>
                            <td>{{ $product->supplier->supplier_name ?? 'N/A' }}</td>
                            <td class="text-center">
                                {{-- FORM RESTORE --}}
                                <form id="restore-form-{{ $product->id }}" 
                                      action="{{ route('products.restore', $product->id) }}" 
                                      method="POST" 
                                      style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" 
                                            class="btn btn-success btn-sm btn-restore"
                                            data-product-id="{{ $product->id }}"
                                            data-product-title="{{ $product->title }}">
                                        <i class="fas fa-undo"></i> Pulihkan
                                    </button>
                                </form>
                                {{-- Tombol delete permanen (optional, hati-hati menggunakannya) --}}
                                {{-- <button class="btn btn-danger btn-sm" onclick="confirmDeletePermanent({{ $product->id }})">Hapus Permanen</button> --}}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada produk yang diarsipkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script SweetAlert2 untuk Konfirmasi Restore di Halaman Arsip --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-restore').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); 

                const productId = this.getAttribute('data-product-id');
                const productTitle = this.getAttribute('data-product-title');
                const form = document.getElementById(`restore-form-${productId}`);

                Swal.fire({
                    title: 'Pulihkan Produk?',
                    html: `
                        Anda akan memulihkan produk 
                        <span class="fw-bold text-primary">'${productTitle}'</span> 
                        dan mengubah statusnya kembali menjadi **AKTIF**.
                        <p class="mt-2 text-info">Produk akan muncul kembali dalam daftar utama.</p>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745', 
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Pulihkan Sekarang',
                    cancelButtonText: 'Batal'
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