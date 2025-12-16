@extends('layouts.app')

@section('title', 'Sales Dashboard')

@section('content')



{{-- BARIS 1: CARD PENDAPATAN, CARD PRODUK TERJUAL, & TOMBOL TRANSAKSI BARU --}}
<div class="row mb-4 align-items-center">
    <div class="card-dash col-lg-4 col-md-6">
        <div class="today-rev card shadow-sm border-0 h-100 border-start border-5">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-sack-dollar fa-2x"></i>
                    </div>
                    <div class="col">
                        <div class="today-list">
                            Today's Revenue
                        </div>
                        <div class="today-data">
                            Rp {{ number_format($pendapatanHariIni ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- CARD TOTAL PRODUK TERJUAL HARI INI --}}
    <div class="card-dash col-lg-4 col-md-6">
        <div class="today-sold card shadow-sm border-0 h-100 border-start border-5">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <i class="fas fa-box-open fa-2x"></i>
                    </div>
                    <div class="col">
                        <div class="today-list">
                            Total Products Sold Today
                        </div>
                        <div class="today-data">
                            {{ number_format($totalProdukTerjualHariIni ?? 0, 0, ',', '.') }} pcs
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-12 text-end mt-4 mt-lg-0">
        <a href="{{ route('transactions.create') }}" class="btn btn-lg btn-custom shadow-sm w-100">
            <i class="fas fa-plus-circle me-2"></i> Create New Transaction
        </a>
    </div>

</div>
    


{{-- BARIS 2: GRAFIK & PRODUK TERLARIS --}}
<div class="row">
    {{-- GRAFIK PENJUALAN --}}
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header py-3 bg-white border-bottom">
                <h5 class="m-0 font-weight-bold text-dark" style="font-weight: 500">
                    Sales Revenue Chart for the Last 7 Days
                </h5>
            </div>
            <div class="card-body p-2"> 
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="penjualanChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- PRODUK TERLARIS --}}
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header py-3 bg-white border-bottom">
                <h5 class="m-0 font-weight-bold text-dark" style="font-weight: 500;">
                    Top 3 Best-Selling Products Today
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse($produkTertinggi as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-dark me-2">{{ $loop->iteration }}</span>
                                <strong class="d-block">{{ $item->product->title ?? 'Product Deleted' }}</strong>
                                <small class="text-muted">
                                    Revenue : Rp {{ number_format($item->total_omzet, 0, ',', '.') }}
                                </small>
                            </div>
                            <span class="top-pcs">
                                {{ $item->total_quantity }} pcs
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">
                            No sales have been made today.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- BARIS 3: PRODUK STOK RENDAH --}}
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header py-3 bg-danger text-white border-bottom">
                <h6 class="m-0 font-weight-bold">
                    ⚠️ Low Stock Products (Need Restocking !)
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 text-center">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Product</th>
                                <th>Remaining Stock</th>
                                <th>Supplier</th>
                                <th>Action</th>
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
                                    <td colspan="5" class="text-center text-muted">
                                        All products have sufficient stock.
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

{{-- ===========================
    SCRIPT UNTUK CHART.JS
=========================== --}}
@push('scripts')
<script>
    const labels = @json($tanggal7Hari ?? []);
    const dataPenjualan = @json($penjualan7Hari ?? []);
    const ctx = document.getElementById('penjualanChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Omzet Penjualan (Rp)',
                data: dataPenjualan,
                backgroundColor: 'rgba(220, 237, 99, 0.3)',
                borderColor: 'rgba(0, 0, 0, 1)',
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(166,157,238,2)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            
            
            animation: { duration: 0 },

          
            layout: {
                padding: 0 
            },

            transitions: {
                show: { animations: { x: { duration: 0 }, y: { duration: 0 } } },
                hide: { animations: { x: { duration: 0 }, y: { duration: 0 } } }
            },

            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: false }, 
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                            if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                            return 'Rp ' + value;
                        }
                    }
                },
                x: {
                    title: { display: false } 
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (context.parsed.y !== null) {
                                label += ': ' + new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR'
                                }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush