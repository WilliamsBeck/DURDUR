@extends('layouts.app')

@section('title', 'Supplier Archives')

@section('content')

<div class="container-fluid">
    {{-- Main Content Card --}}
    <div class="main-content-card">

        {{-- 1. TITLE --}}
        <h3>Supplier Archives</h3>

        {{-- 2. CONTROLS --}}
        <div class="table-controls">
            {{-- Tombol Back --}}
            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary rounded-pill px-4 py-2 fw-bold">
                <i class="fas fa-arrow-left me-2"></i> Back to Active List
            </a>

            {{-- Search Bar --}}
            <div class="search-bar-new">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" name="search" placeholder="Search archived..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('suppliers.archived') }}" class="text-muted ms-2" title="Clear Search">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- 3. TABLE --}}
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center" width="8%">ID</th>
                        <th width="25%">Supplier Name</th>
                        <th width="15%">PIC</th>
                        <th width="15%">Phone</th>
                        <th width="20%">Deleted At</th>
                        <th class="text-center" width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            {{-- ID --}}
                            <td class="text-center text-muted">#{{ $supplier->id }}</td>
                            
                            {{-- Name --}}
                            <td class="fw-bold-dark text-muted">{{ $supplier->supplier_name }}</td>
                            
                            {{-- PIC --}}
                            <td class="text-muted">{{ $supplier->pic_supplier ?? '-' }}</td>
                            
                            {{-- Phone --}}
                            <td class="text-muted">{{ $supplier->supplier_phone ?? '-' }}</td>
                            
                            {{-- Deleted At --}}
                            <td class="text-muted">
                                {{ $supplier->deleted_at ? $supplier->deleted_at->format('d M Y') : '-' }}
                            </td>
                            
                            {{-- Action (Restore) --}}
                            <td>
                                <div class="action-icons">
                                    <form id="restore-form-{{ $supplier->id }}" 
                                          action="{{ route('suppliers.restore', $supplier->id) }}" 
                                          method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        {{-- Tombol Restore (Hijau Solid - defined in transaction.css) --}}
                                        <button type="button" 
                                                class="btn-circle btn-green-solid btn-restore" 
                                                title="Restore Supplier"
                                                data-id="{{ $supplier->id }}"
                                                data-name="{{ $supplier->supplier_name }}">
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
                                No archived suppliers found.
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

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // SweetAlert Restore Confirmation
            document.querySelectorAll('.btn-restore').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); 

                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const form = document.getElementById(`restore-form-${id}`);

                    Swal.fire({
                        // HTML Custom Icon Hijau (Konsisten dengan Product Archive)
                        html: `
                            <div style="width: 90px; height: 90px; background-color: rgba(16, 185, 129, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem auto;">
                                <span style="font-size: 3.5rem; font-weight: 700; color: #10b981; line-height: 1;"><i class="fas fa-undo"></i></span>
                            </div>
                            <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 0.5rem; line-height: 1.4;">
                                Restore Supplier <strong>"${name}"</strong> ?
                            </div>
                            <div style="font-size: 0.95rem; color: #666; margin-bottom: 1.5rem;">
                                This supplier will be moved back to the active list.
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

            // Search Bar Logic
            const searchInput = document.getElementById('searchInput');
            if(searchInput){
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.set('search', this.value);
                        currentUrl.searchParams.delete('page');
                        window.location.href = currentUrl.toString();
                    }
                });
            }
        });
    </script>
@endpush