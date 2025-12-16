@extends('layouts.app')

@section('content')
<<<<<<< Updated upstream
=======
{{-- Load CSS Khusus Form --}}
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">
>>>>>>> Stashed changes

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="form-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Create New Transaction</h3>
                    <a href="{{ route('transactions.index') }}" class="btn btn-cancel">
                        <i class="fa-solid fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
                
<<<<<<< Updated upstream
=======
                {{-- Judul Halaman --}}
                <h3 class="page-title-form">Create New Transaction</h3>

                {{-- Error Messages --}}
>>>>>>> Stashed changes
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
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
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="customer_email" class="form-label">Customer Email (Optional)</label>
                            <input type="email" id="customer_email" class="form-control" name="customer_email" value="{{ old('customer_email') }}">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Transaction Time</label>
                            <input type="text" class="form-control" value="{{ now()->format('d F Y - H:i:s') }}" readonly>
                        </div>
                    </div>

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
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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

        function addProductRow() {
            const index = new Date().getTime();
            const newRow = document.createElement('div');
            newRow.classList.add('product-row');
            newRow.dataset.price = "0";
            newRow.dataset.stock = "0"; 

            let productOptions = '<option value="">Select a Product</option>';
            @foreach ($products as $product)
                productOptions += `<option value="{{ $product->id }}">{{ $product->title }} (Stock: {{ $product->stock }})</option>`;
            @endforeach

            newRow.innerHTML = `
                <div><select name="products[${index}][id]" class="form-select product-select" required>${productOptions}</select></div>
                <div><span class="unit-price-text">Rp 0</span></div>
                <div><input type="number" name="products[${index}][quantity]" class="form-control quantity-input" min="1" placeholder="Qty" required></div>
                <div><span class="subtotal-text fw-bold">Rp 0</span></div>
                <div class="text-end"><button type="button" class="btn btn-remove-product">&times;</button></div>`;
            productRowsContainer.appendChild(newRow);
        }

        addProductBtn.addEventListener('click', addProductRow);

        productRowsContainer.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity-input')) {
                const row = e.target.closest('.product-row');
                if (!row) return;
                const stock = parseInt(row.dataset.stock) || 0;
                const quantity = parseInt(e.target.value) || 0;
                if (stock > 0 && quantity > stock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Insufficient Stock',
                        text: `Quantity cannot exceed available stock (${stock})!`,
                        confirmButtonColor: '#0d6efd'
                    }).then(() => {
                        e.target.value = stock;
                        calculateGrandTotal();
                    });
                } else {
                    calculateGrandTotal();
                }
            }
        });
<<<<<<< Updated upstream
=======
        totalAmountSpan.innerText = formatRupiah(total);
    }
>>>>>>> Stashed changes

        productRowsContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select')) {
                const row = e.target.closest('.product-row');
                if (!row) return;
                const selectedProductId = e.target.value;
                const unitPriceElement = row.querySelector('.unit-price-text');
                const quantityInput = row.querySelector('.quantity-input');
                if (selectedProductId && productsData[selectedProductId]) {
                    const product = productsData[selectedProductId];
                    row.dataset.price = product.price;
                    row.dataset.stock = product.stock; 
                    unitPriceElement.textContent = `Rp ${product.price.toLocaleString('id-ID')}`;
                    quantityInput.value = 1;
                    quantityInput.max = product.stock;
                } else {
                    row.dataset.price = 0;
                    row.dataset.stock = 0;
                    unitPriceElement.textContent = `Rp 0`;
                    quantityInput.value = '';
                    quantityInput.max = '';
                }
                calculateGrandTotal();
            }
        });
        
        productRowsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-product')) {
                e.target.closest('.product-row').remove();
                calculateGrandTotal();
            }
        });

<<<<<<< Updated upstream
        document.addEventListener('DOMContentLoaded', function() {
            addProductRow();
        });
    </script>
@endpush
=======
        row.innerHTML = `
            {{-- Kolom 1: Select Product (4fr) --}}
            <div>
                <select name="products[${index}][id]" class="form-select product-select" required>
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->stock }}">
                            {{ $product->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Kolom 2: Unit Price (2fr) --}}
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
        // Cek tombol atau icon di dalamnya
        if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            const row = e.target.closest('.product-row');
            if(row) {
                row.remove();
                calculateGrandTotal();
            }
        }
    });

    addBtn.addEventListener('click', addRow);

    // Tambah 1 baris saat load
    document.addEventListener('DOMContentLoaded', addRow);
</script>
@endpush    
>>>>>>> Stashed changes
