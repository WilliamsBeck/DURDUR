@extends('layouts.app')

@section('title', 'Supplier Management')

@section('content')
    

    <div class="main-content-card">
        {{-- Penyesuaian tata letak kontrol agar MIRIP MOCKUP --}}
        <div class="table-controls" style="justify-content: flex-start; gap: 10px; flex-wrap: wrap; align-items: center;">

            {{-- 1. Tombol Add Supplier (Gaya Hitam) --}}
            <a href="{{ route('suppliers.create') }}" class="btn add-btn" style="background-color: #000; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                <i class="fa-solid fa-plus"></i>
                Add Supplier
            </a>

            {{-- 2. Tombol Lihat Arsip Supplier (Gaya Kuning) --}}
            <a href="{{ route('suppliers.archived') }}" class="btn btn-warning" style="background-color: #f7b825; color: #333; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                <i class="fa-solid fa-archive"></i>
                Lihat Arsip Supplier
            </a>

            {{-- 3. Search Bar (Diposisikan di sisi kanan dengan placeholder 'Search Product' mengikuti mockup) --}}
            <div class="search-bar-new" style="margin-left: auto; max-width: 300px;">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="searchInput" name="search" placeholder="Search Product" class="form-control" value="{{ request('search') }}">
                <span class="clear-search-btn" id="clearSearchBtn" style="{{ request('search') ? 'display:block;' : 'display:none;' }}">&times;</span>
            </div>

        </div>

        @if(session('success'))
        <div class="alert alert-success mt-3" style="display: none;">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table">
                <thead>
                    {{-- Kolom Disesuaikan Agar Mirip Mockup: ID, Nama, PIC, Phone, Email, Action --}}
                    <tr>
                        <th style="width: 50px;">ID</th> 
                        <th>Supplier Name</th>
                        <th>PIC Supplier</th>
                        <th>Supplier Phone</th>
                        <th>Supplier Email</th>
                        <th class="text-center" style="width: 100px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            {{-- Menampilkan ID dengan format #ID --}}
                            <td>#{{ $supplier->id }}</td>
                            <td><strong>{{ $supplier->supplier_name }}</strong></td>
                            <td>{{ $supplier->pic_supplier ?? '-' }}</td>
                            <td>{{ $supplier->supplier_phone ?? '-' }}</td>
                            <td>{{ $supplier->supplier_email ?? '-' }}</td>
                            
                            <td class="text-center">
                                <div class="action-icons" style="gap: 5px;">
                                    
                                    {{-- Tombol Edit (fa-pencil) --}}
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" title="Edit Supplier" style="color: #ffc107;">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    
                                    {{-- Tombol View/Show (fa-eye) --}}
                                    <a href="{{ route('suppliers.show', $supplier->id) }}" title="Show Details" style="color: #007bff;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    {{-- Tombol Archive (Soft Delete/fa-trash-can) --}}
                                    <form class="d-inline" action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete" title="Archive Supplier" data-name="{{ $supplier->supplier_name }}" data-action="archive" style="background: none; border: none; color: #dc3545; padding: 0; margin-left: 5px;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="alert alert-secondary mt-3">
                                    No Supplier Data Available.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $suppliers->appends(request()->query())->links() }}
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // SweetAlert for success messages (TIDAK BERUBAH)
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'SUCCESS',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        @endif

        // SweetAlert for archive confirmation (TIDAK BERUBAH)
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                
                const dataName = this.getAttribute('data-name');
                const actionType = this.getAttribute('data-action');
                const form = this.closest('form');

                if (actionType === 'archive') {
                    Swal.fire({
                        title: `Arsipkan supplier "${dataName}"?`,
                        text: "Supplier akan dipindahkan ke arsip dan dapat dipulihkan.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33', 
                        cancelButtonColor: '#a4a4a4ff',
                        confirmButtonText: 'Ya, Arsipkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    form.submit();
                }
            });
        });
        
        // Script untuk Search Bar (TIDAK BERUBAH)
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