@extends('layouts.app')

@section('title', 'Supplier Archives')

@section('content')

    <div class="main-content-card">
        <div class="table-controls">
            <h3 class="mb-0">📦 Daftar Supplier yang Diarsip</h3>
        </div>
        
        <div class="table-controls" style="justify-content: flex-start; margin-bottom: 20px;">
            <a href="{{ route('suppliers.index') }}" class="btn btn-primary" style="background-color: #333; color: white;">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Daftar Aktif
            </a>
            
            <div class="search-bar-new" style="margin-left: auto;">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="searchInput" name="search" placeholder="Search archived suppliers..." class="form-control" value="{{ request('search') }}">
                <span class="clear-search-btn" id="clearSearchBtn" style="{{ request('search') ? 'display:block;' : 'display:none;' }}">&times;</span>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success mt-3" style="display: none;">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Supplier Name</th>
                        <th>PIC</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Dihapus Pada</th>
                        <th class="text-center" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td><strong>{{ $supplier->supplier_name }}</strong></td>
                            <td>{{ $supplier->pic_supplier ?? '-' }}</td>
                            <td>{{ $supplier->supplier_email ?? '-' }}</td>
                            <td>{{ $supplier->supplier_phone ?? '-' }}</td>
                            <td>{{ $supplier->deleted_at ? $supplier->deleted_at->format('d F Y H:i') : 'N/A' }}</td>
                            <td class="text-center">
                                <div class="action-icons">
                                    {{-- Tombol RESTORE (Menggunakan method PUT) --}}
                                    <form class="d-inline" action="{{ route('suppliers.restore', $supplier->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn-restore" title="Pulihkan Supplier" data-name="{{ $supplier->supplier_name }}" style="background: none; border: none; padding: 0;">
                                            <i class="fa-solid fa-trash-arrow-up" style="color: #28a745;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="alert alert-secondary mt-3">
                                    Tidak ada data supplier yang diarsip.
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
                    title: `Pulihkan supplier "${dataName}"?`,
                    text: "Supplier akan dikembalikan ke daftar aktif.",
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