@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
    /* --- DASHBOARD SPECIFIC STYLES --- */
    /* Warna Kartu Atas */
    .card-purple { background-color: #a69dee; color: #000; border: none; }
    .card-lime { background-color: #d2f865; color: #000; border: none; }
    .card-grey { background-color: #e5e5e5; color: #000; border: none; }
    
    /* Styling Angka Urutan di Top Product (Lingkaran Hitam) */
    .rank-circle {
        width: 28px; height: 28px;
        background-color: #000; color: #fff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 0.8rem;
        flex-shrink: 0;
    }

    /* Badge Quantity (Warna Lime) */
    .qty-badge {
        background-color: #d2f865; color: #000;
        font-weight: 600; padding: 5px 12px;
        border-radius: 12px; font-size: 0.85rem;
    }

    /* Tombol Purchase Ungu di Tabel */
    .btn-purchase-purple {
        background-color: #a69dee; color: #fff;
        border-radius: 50px; font-weight: 500;
        padding: 5px 20px; text-decoration: none;
        display: inline-block; border: none;
    }
    .btn-purchase-purple:hover { background-color: #928bd8; color: #fff; }

    /* Header Tabel Merah */
    .bg-header-red { background-color: #dc3545; color: white; }
    
    /* Rounded Card */
    .rounded-40 { border-radius: 25px; } /* Radius besar untuk kartu warna */
    .rounded-20 { border-radius: 15px; } /* Radius standar */
</style>

{{-- BARIS 1: 3 KARTU WARNA & TOMBOL CREATE SALES --}}
<div class="row mb-5 align-items-center">
    
    {{-- Card 1: Today's Revenue (Ungu) --}}
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="card card-purple h-100 rounded-40 shadow-sm p-2">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <div class="small fw-bold mb-1">Today's Revenue</div>
                <div class="h4 fw-bold mb-0">
                    Rp. {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Total Transaction (Lime) --}}
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="card card-lime h-100 rounded-40 shadow-sm p-2">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <div class="small fw-bold mb-1">Total Transaction</div>
                <div class="h4 fw-bold mb-0">
                    {{ $totalTransaksiHariIni ?? 0 }} pcs
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Product Sold (Abu-abu) --}}
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="card card-grey h-100 rounded-40 shadow-sm p-2">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <div class="small fw-bold mb-1">Product Sold</div>
                <div class="h4 fw-bold mb-0">
                    {{ $totalProdukTerjualHariIni ?? 0 }} pcs
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol: Create Sales (Hitam Pill) --}}
    <div class="col-lg-3 col-md-6 text-end">
        <a href="{{ route('transactions.create') }}" class="add-btn w-100 justify-content-center py-3 shadow-sm" style="font-size: 1.1rem;">
            <i class="fas fa-plus me-2"></i> Create Sales
        </a>
    </div>

</div>


{{-- BARIS 2: CHART & TOP PRODUCTS --}}
<div class="row mb-4">
    
    {{-- CHART (Kiri) --}}
    <div class="col-lg-8 mb-4">
        <div class="card border-0 bg-white rounded-20 shadow-sm h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4" style="font-size: 1rem;">Sales Revenue Chart for the Last 7 Days</h5>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="penjualanChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- TOP PRODUCTS LIST (Kanan) --}}
    <div class="col-lg-4 mb-4">
        <div class="card border-0 bg-white rounded-20 shadow-sm h-100">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4" style="font-size: 1rem;">Top 5 Best-Selling Product today</h5>
                
                <div class="d-flex flex-column gap-3">
                    @forelse($produkTertinggi as $item)
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                {{-- Angka Urutan Hitam --}}
                                <div class="rank-circle">{{ $loop->iteration }}</div>
                                <div>
                                    <div class="fw-bold" style="font-size: 0.9rem;">{{ $item->product->title ?? 'Deleted' }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        Revenue : Rp. {{ number_format($item->total_omzet, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            {{-- Badge Quantity Lime --}}
                            <div class="qty-badge">
                                {{ $item->total_quantity }} pcs
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5 small">No sales today</div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>


{{-- BARIS 3: LOW STOCK PRODUCTS (Header Merah) --}}
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-20 overflow-hidden">
            {{-- Header Merah --}}
            <div class="card-header bg-header-red py-3 px-4 border-0 d-flex align-items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                <h6 class="m-0 fw-bold">Low stock products (Need Restocking !)</h6>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle text-center">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4 text-start" width="5%">No.</th>
                                <th class="py-3" width="35%">Product</th>
                                <th class="py-3" width="20%">Remaining Stock</th>
                                <th class="py-3" width="20%">Supplier</th>
                                <th class="py-3 px-4" width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($produkRendahStok as $produk)
                                <tr>
                                    <td class="text-start px-4 fw-bold">{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $produk->title }}</td>
                                    <td class="fw-bold">{{ $produk->stock }}</td>
                                    <td class="fw-bold">{{ $produk->supplier->supplier_name ?? '-' }}</td>
                                    <td class="px-4">
                                        {{-- Tombol Purchase Ungu --}}
                                        <a href="{{ route('products.edit', $produk->id) }}" class="btn-purchase-purple">
                                            Purchase
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        All stocks are safe.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- SCRIPT CHART JS (Disesuaikan agar mirip gambar: Area Chart Hijau Lime) --}}
@push('scripts')
<script>
    const labels = @json($tanggal7Hari ?? []);
    const dataPenjualan = @json($penjualan7Hari ?? []);
    const ctx = document.getElementById('penjualanChart').getContext('2d');

    // Membuat Gradient untuk fill chart
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(210, 248, 101, 0.8)'); // Lime atas
    gradient.addColorStop(1, 'rgba(255, 255, 255, 0.1)'); // Putih bawah

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales Revenue',
                data: dataPenjualan,
                backgroundColor: gradient, // Pakai Gradient
                borderColor: '#000000',    // Garis Hitam
                borderWidth: 2.5,
                tension: 0.4,              // Garis melengkung halus
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#000',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }, // Sembunyikan legenda
                tooltip: {
                    backgroundColor: '#000',
                    titleColor: '#d2f865',
                    padding: 10,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (context.parsed.y !== null) {
                                label = ' Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [5, 5],
                        color: '#f0f0f0'
                    },
                    ticks: {
                        font: { size: 10 },
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000).toFixed(0) + 'jt';
                            if (value >= 1000) return (value / 1000).toFixed(0) + 'rb';
                            return value;
                        }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });
</script>
@endpush