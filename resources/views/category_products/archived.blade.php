@extends('layouts.app')

@section('title', 'Category Archives')

@section('content')

    <div class="main-content-card">
        <div class="table-controls">
            <h3 class="mb-0">📦 Daftar Kategori yang Diarsip</h3>
        </div>
        
        {{-- TOMBOL KEMBALI DAN SEARCH BAR (Sesuaikan design dan class Anda) --}}
        <div class="table-controls" style="justify-content: flex-start; margin-bottom: 20px;">
            <a href="{{ route('category_products.index') }}" class="btn btn-primary" style="background-color: #333; color: white;">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Daftar Aktif
            </a>
            
            <div class="search-bar-new" style="margin-left: auto;">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="searchInput" name="search" placeholder="Search archived categories..." class="form-control" value="{{ request('search') }}">
                <span class="clear-search-btn" id="clearSearchBtn" style="{{ request('search') ? 'display:block;' : 'display:none;' }}">&times;</span>
            </div>
        </div>

        @if(session('success'))
        {{-- Pesan sukses ini akan ditangkap oleh SweetAlert di script bawah --}}
        <div class="alert alert-success mt-3" style="display: none;">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Category Name</th>
                        <th style="width: 150px;">Dibuat Pada</th>
                        <th style="width: 150px;">Dihapus Pada</th>
                        <th class="text-center" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($category_products as $category)
                        <tr>
                            <td><strong>#{{ $category->id }}</strong></td>
                            <td>{{ $category->product_category_name }}</td>
                            <td>{{ $category->created_at ? $category->created_at->format('d F Y') : '-' }}</td>
                            <td>{{ $category->deleted_at ? $category->deleted_at->format('d F Y H:i:s') : 'N/A' }}</td>
                            <td class="text-center">
                                <div class="action-icons">
                                    {{-- FORM RESTORE --}}
                                    <form class="d-inline" action="{{ route('category_products.restore', $category->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        {{-- Gunakan class btn-restore untuk ditangkap oleh SweetAlert --}}
                                        <button type="submit" class="btn-restore" title="Pulihkan Kategori" data-name="{{ $category->product_category_name }}" data-action="restore" style="background: none; border: none; padding: 0;">
                                            <i class="fa-solid fa-trash-arrow-up" style="color: #28a745;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <div class="alert alert-secondary mt-3">
                                    Tidak ada data kategori yang diarsip.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- BAGIAN PAGINATION (PENTING AGAR appends() BERFUNGSI) --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $category_products->appends(request()->query())->links() }}
        </div>
    </div>

@endsection

@push('scripts')
    {{-- PASTIKAN INI TIDAK MENGINCLUDE FILE INDEX.BLADE.PHP --}}
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

        // SweetAlert untuk konfirmasi restore
        const restoreButtons = document.querySelectorAll('.btn-restore');
        restoreButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const dataName = this.getAttribute('data-name');
                const form = this.closest('form');
                
                Swal.fire({
                    title: `Pulihkan kategori "${dataName}"?`,
                    text: "Kategori akan dikembalikan ke daftar aktif.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745', 
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Pulihkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Script untuk Search Bar (Sama seperti di index.blade.php)
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');

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