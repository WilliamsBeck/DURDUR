@extends('layouts.app')

@section('content')
{{-- Load CSS Khusus Form --}}
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- Container Utama (.form-card) --}}
            <div class="form-card">
                
                {{-- Judul Halaman --}}
                <h3 class="page-title-form">Create New Transaction</h3>

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
                        {{-- Customer Email --}}
                        <div class="col-md-12">
                            <label class="form-label">Customer Email</label>
                            <input type="email" name="customer_email" class="form-control" placeholder="Optional" value="{{ old('customer_email') }}">
                        </div>

                        {{-- Transaction Time (Readonly) --}}
                        <div class="col-md-6">
                            <label class="form-label">Transaction Time</label>
                            <input type="text" class="form-control" value="{{ now()->format('d F Y - H:i:s') }}" readonly>
                        </div>
                        
                        {{-- Payment Method --}}
                         <div class="col-md-6">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_id" class="form-select">
                                <option value="">-- Select Payment --</option>
                                @foreach($payments as $payment)
                                    <option value="{{ $payment->id }}" {{ old('payment_id') == $payment->id ? 'selected' : '' }}>
                                        {{ $payment->method_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- BAGIAN 2: DAFTAR PRODUK --}}
                    <div class="product-section-title">Select Products</div>

                    {{-- Header Grid (Sesuai CSS Grid baru) --}}
                    <div id="product-rows-header">
                        <div>Product</div>             {{-- 4fr --}}
                        <div>Unit Price</div>          {{-- 2fr --}}
                        <div>Quantity</div>            {{-- 2fr --}}
                        <div>Subtotal</div>            {{-- 2fr --}}
                        <div class="text-center"></div> {{-- 0.5fr (Action) --}}
                    </div>

                    {{-- Container Baris Produk --}}
                    <div id="product-rows-container">
                        {{-- Baris produk akan ditambahkan di sini oleh JS --}}
                    </div>

                    {{-- Tombol Tambah Produk (.btn-add-product) --}}
                    <button type="button" class="btn-add-product mt-3" id="add-product-btn">
                        + Add Product
                    </button>

                    {{-- BAGIAN 3: TOTAL & TOMBOL AKSI (.form-actions) --}}
                    <div class="form-actions">
                        
                        {{-- Grand Total (kiri) --}}
                        <div id="grand-total-display">
                            Grand Total: <span id="total-amount">Rp 0</span>
                        </div>

                        {{-- Tombol Cancel (.btn-cancel) --}}
                        <a href="{{ route('transactions.index') }}" class="btn-cancel">
                            Cancel
                        </a>

                        {{-- Tombol Save (.btn-save) --}}
                        <button type="submit" class="btn-save">
                            Save
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
    const totalAmountSpan = document.getElementById('total-amount');

    // Format Rupiah
    const formatRupiah = (num) => 'Rp. ' + new Intl.NumberFormat('id-ID').format(num);

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
        totalAmountSpan.innerText = formatRupiah(total);
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
<<<<<<< HEAD
                            {{ $product->title }} (Stok: {{ $product->stock }})
=======
                            {{ $product->title }}
>>>>>>> nikeisha
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kolom 2: Unit Price (2fr) --}}
<<<<<<< HEAD
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
=======
            <div class="unit-price-text">Rp. 0</div>

            {{-- Kolom 3: Quantity (2fr) --}}
            <div>
                <input type="number" name="products[${index}][quantity]" class="form-control qty-input" placeholder="Qty" value="1" min="1" required>
            </div>

            {{-- Kolom 4: Subtotal (2fr) --}}
            <div class="subtotal-text">Rp. 0</div>

            {{-- Kolom 5: Action (0.5fr) --}}
            <div class="d-flex justify-content-center">
                <button type="button" class="btn-remove-product remove-row" title="Remove">
                    <i class="fas fa-times"></i>
>>>>>>> nikeisha
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
<<<<<<< HEAD
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.product-row').remove();
            calculateGrandTotal();
=======
        // Cek tombol atau icon di dalamnya
        if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            const row = e.target.closest('.product-row');
            if(row) {
                row.remove();
                calculateGrandTotal();
            }
>>>>>>> nikeisha
        }
    });

    addBtn.addEventListener('click', addRow);

    // Tambah 1 baris saat load
    document.addEventListener('DOMContentLoaded', addRow);
</script>
@endpush