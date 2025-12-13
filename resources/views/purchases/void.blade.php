@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            
            <div class="card shadow mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Void Purchase Transaction #{{ $purchase->id }}</h4>
                </div>
                
                <div class="card-body">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <p class="lead text-danger fw-bold">PERINGATAN! Aksi ini tidak dapat dibatalkan.</p>
                    <p>Membatalkan Purchase Order ini akan:</p>
                    <ul>
                        <li>Mengubah status PO menjadi **VOID**.</li>
                        @if ($purchase->status == 'done')
                            <li class="fw-bold text-danger">**Mengurangi kembali** stok produk yang sudah ditambahkan saat penerimaan barang (Reversal Stok).</li>
                        @else
                            <li class="fw-bold text-warning">Tidak ada perubahan stok yang terjadi karena transaksi masih **PENDING**.</li>
                        @endif
                    </ul>

                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="30%">Supplier</th>
                            <td>: {{ $purchase->supplier->supplier_name }}</td>
                        </tr>
                        <tr>
                            <th>Total Cost</th>
                            <td>: <span class="fw-bold text-primary">Rp {{ number_format($purchase->total_cost, 0, ',', '.') }}</span></td>
                        </tr>
                        <tr>
                            <th>Status Saat Ini</th>
                            <td>: <span class="badge bg-{{ $purchase->status == 'done' ? 'success' : 'warning text-dark' }}">{{ strtoupper($purchase->status) }}</span></td>
                        </tr>
                    </table>

                    <hr>

                    {{-- === TAMBAHKAN ID PADA FORM === --}}
                    <form action="{{ route('purchases.void', $purchase->id) }}" method="POST" id="voidForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="void_reason" class="form-label fw-bold">Alasan Pembatalan (Wajib)</label>
                            <textarea name="void_reason" id="void_reason" rows="3" 
                                class="form-control @error('void_reason') is-invalid @enderror" 
                                required>{{ old('void_reason') }}</textarea>
                            @error('void_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Konfirmasi Password Anda</label>
                            <input type="password" name="password" id="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                required>
                            <div class="form-text">Masukkan password Anda untuk mengonfirmasi bahwa Anda berhak membatalkan transaksi ini.</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Batal
                            </a>
                            {{-- === TAMBAHKAN ID PADA TOMBOL DAN HAPUS onclick="..." === --}}
                            <button type="submit" class="btn btn-danger" id="btnVoidSubmit">
                                <i class="fas fa-trash-alt"></i> Ya, Batalkan Transaksi Permanen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnVoid = document.getElementById('btnVoidSubmit');
        const voidForm = document.getElementById('voidForm');

        if (btnVoid && voidForm) {
            btnVoid.addEventListener('click', function(e) {
                // Mencegah form submit default
                e.preventDefault(); 
                
                // Cek validasi browser untuk memastikan form sudah terisi (terutama reason dan password)
                if (!voidForm.checkValidity()) {
                    // Jika form tidak valid, submit secara manual untuk menampilkan pesan error browser
                    voidForm.reportValidity();
                    return; 
                }

                Swal.fire({
                    title: 'YAKIN INGIN MEMBATALKAN TRANSAKSI?',
                    html: `
                        Anda akan mengubah status Purchase Order 
                        <span class="fw-bold text-primary">#{{ $purchase->id }}</span> 
                        menjadi <span class="fw-bold text-danger">VOID</span>.
                        <p class="mt-2 text-warning">Tindakan ini tidak dapat dibatalkan!</p>
                        @if ($purchase->status == 'done')
                            <p class="text-danger">Stok akan **DIKURANGI KEMBALI**.</p>
                        @endif
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545', // Warna merah untuk Void
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Void Transaksi Sekarang',
                    cancelButtonText: 'Tinjau Kembali'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika dikonfirmasi, kirim form
                        voidForm.submit();
                    }
                });
            });
        }
    });
</script>
@endpush