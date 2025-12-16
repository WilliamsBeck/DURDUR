@extends('layouts.app')

@section('title', 'Supplier Management')

@section('content')
<<<<<<< HEAD
    
=======
<<<<<<< Updated upstream
        <div class="main-content-card">
            <div class="table-controls">
>>>>>>> nikeisha

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

<<<<<<< HEAD
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
=======
=======

<div class="container-fluid">
    {{-- Main Content Card --}}
    <div class="main-content-card">

        {{-- 1. TITLE --}}
        <h3>Supplier Management</h3>

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
                {{-- Tombol Add Supplier (Hitam Pill) --}}
                <a href="{{ route('suppliers.create') }}" class="add-btn">
                    <i class="fas fa-plus"></i> Add Supplier
                </a>

                {{-- Tombol Lihat Arsip (Ungu Pill - Konsisten) --}}
                <a href="{{ route('suppliers.archived') }}" 
                   class="btn-circle btn-purple-solid text-white text-decoration-none px-4" 
                   style="border-radius: 50px; width: auto; height: auto; padding: 0.8rem 1.5rem;" 
                   title="View Archived Suppliers">
                    <i class="fas fa-archive me-2"></i> Archives
                </a>
            </div>

            {{-- Search Bar (Abu-abu Pill) --}}
            <form method="GET" action="{{ route('suppliers.index') }}" class="m-0" id="searchForm">
                <div class="search-bar-new">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" name="search" placeholder="Search supplier..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('suppliers.index') }}" class="text-muted ms-2" title="Clear Search">
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
                        <th class="text-center" width="8%">ID</th>
                        <th width="25%">Supplier Name</th>
                        <th width="20%">PIC</th>
                        <th width="15%">Phone</th>
                        <th width="20%">Email</th>
                        <th class="text-center" width="12%">Action</th>
>>>>>>> nikeisha
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
<<<<<<< HEAD
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
=======
                            {{-- ID --}}
                            <td class="text-center text-muted">#{{ $supplier->id }}</td>
                            
                            {{-- Name --}}
                            <td class="fw-bold-dark">{{ $supplier->supplier_name }}</td>
                            
                            {{-- PIC --}}
                            <td>{{ $supplier->pic_supplier ?? '-' }}</td>
                            
                            {{-- Phone --}}
                            <td>{{ $supplier->supplier_phone ?? '-' }}</td>
                            
                            {{-- Email --}}
                            <td>{{ $supplier->supplier_email ?? '-' }}</td>
                            
                            {{-- Actions --}}
                            <td>
                                <div class="action-icons">

                                    {{-- View (Ungu) --}}
                                    <a href="{{ route('suppliers.show', $supplier->id) }}" class="btn-circle btn-purple-solid" title="Show Details">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Edit (Kuning) --}}
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn-circle" style="background-color: #f59e0b;" title="Edit Supplier">
                                        <i class="fas fa-pencil-alt text-white"></i>
                                    </a>

                                    {{-- Archive (Merah) --}}
                                    <form class="d-inline" action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                class="btn-circle btn-red-solid btn-delete" 
                                                title="Archive Supplier" 
                                                data-name="{{ $supplier->supplier_name }}">
                                            <i class="fas fa-trash-alt"></i>
>>>>>>> nikeisha
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
<<<<<<< HEAD
                            <td colspan="6" class="text-center">
                                <div class="alert alert-secondary mt-3">
                                    No Supplier Data Available.
                                </div>
=======
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-truck fa-3x mb-3 text-light"></i><br>
                                No Suppliers Found
>>>>>>> nikeisha
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

<<<<<<< HEAD
        <div class="d-flex justify-content-center mt-4">
            {{ $suppliers->appends(request()->query())->links() }}
        </div>
    </div>

=======
        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-4">
            {{ $suppliers->appends(request()->query())->links() }}
        </div>
    </div>
</div>

>>>>>>> Stashed changes
>>>>>>> nikeisha
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
<<<<<<< HEAD
        // SweetAlert for success messages (TIDAK BERUBAH)
=======
<<<<<<< Updated upstream
        // SweetAlert for success messages
=======
        // SweetAlert Success
>>>>>>> Stashed changes
>>>>>>> nikeisha
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000,
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        @endif

<<<<<<< HEAD
        // SweetAlert for archive confirmation (TIDAK BERUBAH)
=======
<<<<<<< Updated upstream
        // SweetAlert for delete confirmation
=======
        // SweetAlert Confirm Archive (Custom Red Theme)
>>>>>>> Stashed changes
>>>>>>> nikeisha
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const dataName = this.getAttribute('data-name');
                const actionType = this.getAttribute('data-action');
                const form = this.closest('form');

<<<<<<< HEAD
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
=======
                Swal.fire({
<<<<<<< Updated upstream
                    title: `Are you sure you want to delete ${dataName}?`,
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#B80000',
                    cancelButtonColor: '#a4a4a4ff',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
=======
                    // HTML Custom (Ikon Merah)
                    html: `
                        <div class="archive-icon-bg">
                            <span class="archive-icon-text">!</span>
                        </div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 0.5rem; line-height: 1.4;">
                            Archive supplier <strong>"${dataName}"</strong> ?
                        </div>
                        <div style="font-size: 0.95rem; color: #666; margin-bottom: 1.5rem;">
                            Supplier will be moved to archive.
                        </div>
                    `,
                    icon: null,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Archive',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal-archive-popup',
                        confirmButton: 'btn-swal-confirm-red',
                        cancelButton: 'btn-swal-cancel-grey',
                        actions: 'swal2-actions'
                    },
                    buttonsStyling: false,
                    reverseButtons: true
>>>>>>> Stashed changes
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
>>>>>>> nikeisha
            });
        });
<<<<<<< Updated upstream
        
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
=======

        // Script Search Bar
        const searchInput = document.getElementById('searchInput');
        if(searchInput){
            searchInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('searchForm').submit();
                }
            });
        }
>>>>>>> Stashed changes
    </script>
@endpush