@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- Container Utama (.form-card) --}}
            <div class="form-card">
                
                {{-- Judul --}}
                <h3>Create New Transaction</h3>

                {{-- Error Messages --}}
                @if ($errors->any())
                    <div class="alert alert-danger mb-4 rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('transactions.store') }}" method="POST" id="transaction-form">
                    @csrf

                    {{-- BAGIAN 1: INFORMASI UMUM --}}
                    <div class="row g-4 mb-4">
                        {{-- Cashier (Readonly) --}}
                        <div class="col-md-6">
                            <label class="form-label">Cashier</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name ?? 'Cashier' }}" readonly>
                        </div>

                        {{-- Customer Email --}}
                        <div class="col-md-6">
                            <label class="form-label">Customer Email (Optional)</label>
                            <input type="email" name="customer_email" class="form-control" placeholder="example@email.com" value="{{ old('customer_email') }}">
                        </div>

                        {{-- Payment Method --}}
                        <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_id" class="form-select @error('payment_id') is-invalid @enderror">
                                <option value="">-- Select Payment --</option>
                                @foreach($payments as $payment)
                                    <option value="{{ $payment->id }}" {{ old('payment_id') == $payment->id ? 'selected' : '' }}>
                                        {{ $payment->method_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Transaction Time (Readonly) --}}
                        <div class="col-md-6">
                            <label class="form-label">Transaction Time</label>
                            <input type="text" class="form-control" value="{{ now()->format('d F Y - H:i:s') }}" readonly>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: #e0e0e0;">

                    {{-- BAGIAN 2: DAFTAR PRODUK (Sesuai CSS Grid) --}}
                    <h5 class="mb-3" style="font-weight: 600;">Select Products</h5>

                    {{-- Header Grid (#product-rows-header) --}}
                    <div id="product-rows-header">
                        <div>Product Name</div>         {{-- 4fr --}}
                        <div>Unit Price</div>           {{-- 2fr --}}
                        <div>Quantity</div>             {{-- 2fr --}}
                        <div>Subtotal</div>             {{-- 2fr --}}
                        <div class="text-center">Act</div> {{-- 1fr --}}
                    </div>

                    {{-- Container Baris Produk --}}
                    <div id="product-rows-container">
                        {{-- Baris produk akan ditambahkan di sini oleh JS --}}
                    </div>

                    {{-- Tombol Tambah Produk --}}
                    <button type="button" class="btn btn-dark mt-3 btn-sm" id="add-product-btn" style="border-radius: 8px;">
                        + Add Product
                    </button>

                    {{-- BAGIAN 3: TOTAL & TOMBOL AKSI (.form-actions) --}}
                    <div class="form-actions">
                        <div class="me-auto">
                            <span class="text-muted">Grand Total:</span>
                            {{-- #grand-total-display --}}
                            <span id="grand-total-display" class="ms-2">Rp 0</span>
                        </div>

                        {{-- .btn-cancel --}}
                        <a href="{{ route('transactions.index') }}" class="btn btn-cancel text-decoration-none">
                            Cancel
                        </a>

                        {{-- .btn-save --}}
                        <button type="submit" class="btn btn-save">
                            Save Transaction
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const container = document.getElementById('product-rows-container');
    const addBtn = document.getElementById('add-product-btn');
    const grandTotalDisplay = document.getElementById('grand-total-display');

    // Format Rupiah
    const formatRupiah = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num);

    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const price = parseFloat(row.dataset.price) || 0;
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            const subtotal = price * qty;
            
            // Update Text Subtotal per baris
            row.querySelector('.subtotal-text').innerText = formatRupiah(subtotal);
            total += subtotal;
        });
        grandTotalDisplay.innerText = formatRupiah(total);
    }

    function addRow() {
        const index = Date.now();
        const row = document.createElement('div');
        
        // CLASS .product-row (Penting agar Grid CSS bekerja)
        row.className = 'product-row'; 
        row.dataset.price = 0;

        row.innerHTML = `
            {{-- Kolom 1: Select Product (4fr) --}}
            <div>
                <select name="products[${index}][id]" class="form-select product-select" required>
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                            {{ $product->title }} (Stok: {{ $product->stock }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kolom 2: Unit Price (2fr) --}}
            <div class="unit-price-text">Rp 0</div>

            {{-- Kolom 3: Quantity (2fr) --}}
            <div>
                <input type="number" name="products[${index}][quantity]" class="form-control qty-input" value="1" min="1" required>
            </div>

            {{-- Kolom 4: Subtotal (2fr) --}}
            <div class="subtotal-text">Rp 0</div>

            {{-- Kolom 5: Action (1fr) --}}
            <div class="d-flex justify-content-center">
                <button type="button" class="btn-remove-product remove-row" title="Remove">
                    &times;
                </button>
            </div>
        `;

        container.appendChild(row);
    }

    // Event Delegation
    container.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const row = e.target.closest('.product-row');
            const option = e.target.selectedOptions[0];
            const price = option.dataset.price || 0;
            
            row.dataset.price = price;
            row.querySelector('.unit-price-text').innerText = formatRupiah(price);
            
            calculateGrandTotal();
        }
    });

    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input')) {
            calculateGrandTotal();
        }
    });

    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.product-row').remove();
            calculateGrandTotal();
        }
    });

    addBtn.addEventListener('click', addRow);

    // Tambah 1 baris saat load
    document.addEventListener('DOMContentLoaded', addRow);
</script>
@endpush