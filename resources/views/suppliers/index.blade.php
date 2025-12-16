@extends('layouts.app')

@section('title', 'Supplier Management')

@section('content')
<<<<<<< Updated upstream
        <div class="main-content-card">
            <div class="table-controls">

                <a href="{{ route('suppliers.create') }}" class="btn add-btn">
                    <i class="fa-solid fa-plus"></i>
                    Add New Supplier
                </a>

                 <div class="search-bar-new">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="searchInput" name="search" placeholder="Search suppliers..." class="form-control" value="{{ request('search') }}">
                    <span class="clear-search-btn" id="clearSearchBtn" style="{{ request('search') ? 'display:block;' : 'display:none;' }}">&times;</span>
                </div>

            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>PIC</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td><strong>{{ $supplier->supplier_name }}</strong></td>
                                <td>{{ $supplier->pic_supplier ?? '-' }}</td>
                                <td>{{ $supplier->supplier_email ?? '-' }}</td>
                                <td>{{ $supplier->supplier_phone ?? '-' }}</td>
                                <td>{{ Str::limit($supplier->supplier_address, 30, '...') }}</td>
                                <td class="text-center">
                                    <div class="action-icons">
                                        <a href="{{ route('suppliers.show', $supplier->id) }}" title="Show Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}" title="Edit Supplier">
                                            <i class="fa-solid fa-pencil"></i>
                                        </a>
                                        <form class="d-inline" action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete" title="Delete Supplier" data-name="{{ $supplier->supplier_name }}">
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
                {{-- Menambahkan appends agar parameter search tidak hilang saat paginasi --}}
                {{ $suppliers->appends(request()->query())->links() }}
            </div>
        </div>

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
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
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
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-truck fa-3x mb-3 text-light"></i><br>
                                No Suppliers Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-4">
            {{ $suppliers->appends(request()->query())->links() }}
        </div>
    </div>
</div>

>>>>>>> Stashed changes
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
<<<<<<< Updated upstream
        // SweetAlert for success messages
=======
        // SweetAlert Success
>>>>>>> Stashed changes
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

<<<<<<< Updated upstream
        // SweetAlert for delete confirmation
=======
        // SweetAlert Confirm Archive (Custom Red Theme)
>>>>>>> Stashed changes
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const dataName = this.getAttribute('data-name');
                const form = this.closest('form');

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
            });
        });
<<<<<<< Updated upstream
        
        const searchInput = document.getElementById('searchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');

        searchInput.addEventListener('keyup', function(event) {
            clearSearchBtn.style.display = this.value.length > 0 ? 'block' : 'none';

            if (event.key === 'Enter') { 
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('search', this.value);
                // Hapus baris yang error terkait dateFilter
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