@extends('layouts.app')

@section('content')
<<<<<<< HEAD
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">
=======
<<<<<<< Updated upstream
=======
{{-- Load CSS Khusus Form --}}
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">
>>>>>>> Stashed changes
>>>>>>> nikeisha

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- Container Utama (.form-card) --}}
            <div class="form-card">
                
<<<<<<< HEAD
                {{-- Judul --}}
                <h3>Create New Transaction</h3>

                {{-- Error Messages --}}
=======
<<<<<<< Updated upstream
=======
                {{-- Judul Halaman --}}
                <h3 class="page-title-form">Create New Transaction</h3>

                {{-- Error Messages --}}
>>>>>>> Stashed changes
>>>>>>> nikeisha
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
<<<<<<< HEAD

                    {{-- BAGIAN 1: INFORMASI UMUM --}}
                    <div class="row g-4 mb-4">
                        {{-- Cashier (Readonly) --}}
                        <div class="col-md-6">
                            <label class="form-label">Cashier</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name ?? 'Cashier' }}" readonly>
=======
<<<<<<< Updated upstream
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="cashier_name" class="form-label">Cashier Name</label>
    
                                <select id="cashier_name" class="form-select" name="cashier_name" required>
                                    <option value="" disabled selected>Choose Cahsier</option> 
                                    
                                    @php
                                        $cashiers = ['Williams', 'Nikeisha', 'Louis', 'Agnes'];
                                    @endphp
                                    
                                    @foreach ($cashiers as $name)
                                        <option value="{{ $name }}" {{ old('cashier_name') == $name ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                    
                                </select>
>>>>>>> nikeisha
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

<<<<<<< HEAD
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
=======
                    <hr class="my-4">
                    <h5>Select Products</h5>
                    
                    <div id="product-rows-header" class="d-none d-md-grid">
                        <div>Product</div>
                        <div>Unit Price</div>
                        <div>Quantity</div>
                        <div>Subtotal</div>
                        <div></div>
=======

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
>>>>>>> Stashed changes
                    </div>
                    
                    <div id="product-rows"></div>

<<<<<<< Updated upstream
                    <button type="button" class="btn btn-secondary mt-2" id="add-product-btn">
                        <i class="fa-solid fa-plus me-1"></i> Add Product
=======
                    {{-- Container Baris Produk --}}
                    <div id="product-rows-container">
                        {{-- Baris produk akan ditambahkan di sini oleh JS --}}
                    </div>

                    {{-- Tombol Tambah Produk (.btn-add-product) --}}
                    <button type="button" class="btn-add-product mt-3" id="add-product-btn">
                        + Add Product
>>>>>>> Stashed changes
                    </button>
                    
                    <hr class="my-4">
                    <div class="form-actions">
<<<<<<< Updated upstream
                        <h4 class="me-auto" id="grand-total-display">Grand Total: <span id="grand-total">Rp 0</span></h4>
                        <a href="{{ route('transactions.index') }}" class="btn btn-cancel">Cancel</a>
                        <button type="submit" class="btn btn-save">Checkout</button>
=======
                        
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
>>>>>>> Stashed changes
>>>>>>> nikeisha
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
<<<<<<< HEAD
<script>
    const container = document.getElementById('product-rows-container');
    const addBtn = document.getElementById('add-product-btn');
    const grandTotalDisplay = document.getElementById('grand-total-display');

    // Format Rupiah
    const formatRupiah = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
=======
<<<<<<< Updated upstream
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const productsData = @json($productsJson);
        const productRowsContainer = document.getElementById('product-rows');
        const addProductBtn = document.getElementById('add-product-btn');
        const grandTotalElement = document.getElementById('grand-total');

        function calculateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.product-row').forEach(row => {
                const price = parseFloat(row.dataset.price) || 0;
                const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
                const subtotal = price * quantity;
                row.querySelector('.subtotal-text').textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
                total += subtotal;
            });
            grandTotalElement.textContent = `Rp ${total.toLocaleString('id-ID')}`;
        }
=======
<script>
    const container = document.getElementById('product-rows-container');
    const addBtn = document.getElementById('add-product-btn');
    const totalAmountSpan = document.getElementById('total-amount');

    // Format Rupiah
    const formatRupiah = (num) => 'Rp. ' + new Intl.NumberFormat('id-ID').format(num);
>>>>>>> Stashed changes
>>>>>>> nikeisha

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
<<<<<<< HEAD
        grandTotalDisplay.innerText = formatRupiah(total);
    }
=======
<<<<<<< Updated upstream
=======
        totalAmountSpan.innerText = formatRupiah(total);
    }
>>>>>>> Stashed changes
>>>>>>> nikeisha

    function addRow() {
        const index = Date.now();
        const row = document.createElement('div');
        
        // CLASS .product-row (Penting agar Grid CSS bekerja)
        row.className = 'product-row'; 
        row.dataset.price = 0;

<<<<<<< HEAD
=======
<<<<<<< Updated upstream
        document.addEventListener('DOMContentLoaded', function() {
            addProductRow();
        });
    </script>
@endpush
=======
>>>>>>> nikeisha
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
<<<<<<< HEAD
@endpush
=======
@endpush    
>>>>>>> Stashed changes
>>>>>>> nikeisha
