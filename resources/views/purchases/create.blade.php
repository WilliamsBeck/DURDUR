@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">

            {{-- HEADER TITLE & BACK BUTTON --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Create New Purchase Transaction</h3>
                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            {{-- ERROR MESSAGES --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('purchases.store') }}" method="POST">
                @csrf

                {{-- HEADER INFORMATION --}}
                <div class="row">
                    {{-- DIBUAT OLEH (User Otomatis) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Created By</label>
                        <input type="text"
                               class="form-control"
                               value="{{ auth()->user()->name }}"
                               readonly>
                    </div>

                    {{-- TANGGAL OTOMATIS (Menggunakan Waktu Server Saat ini) --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Transaction Date</label>
                        <input type="text"
                               class="form-control"
                               value="{{ now()->format('d F Y - H:i:s') }}"
                               readonly>
                        <small class="text-muted">Tanggal ini akan dicatat sebagai **created_at**.</small>
                    </div>
                    
                    {{-- SUPPLIER SELECT --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Supplier</label>
                        <select name="supplier_id"
                                class="form-select @error('supplier_id') is-invalid @enderror"
                                id="supplier-select"
                                required>
                            <option value="">-- Choose Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <hr>

                {{-- PRODUCTS DETAIL --}}
                <h5 class="mb-3">Products to Purchase</h5>

                <div class="row fw-bold d-none d-md-flex mb-2">
                    <div class="col-md-4">Product</div>
                    <div class="col-md-2">Unit Cost</div>
                    <div class="col-md-2">Qty</div>
                    <div class="col-md-2 text-end">Subtotal</div>
                    <div class="col-md-2 text-end">Action</div>
                </div>

                <div id="product-rows">
                    {{-- Product rows will be dynamically added here by JS --}}
                </div>

                <button type="button"
                        class="btn btn-outline-primary mt-2"
                        id="add-product"
                        disabled> {{-- Disabled default agar user memilih supplier dulu --}}
                    + Add Product
                </button>

                <hr>

                {{-- FOOTER / TOTAL --}}
                <div class="d-flex justify-content-between align-items-center">
                    <h4>
                        Grand Total:
                        <span id="grand-total" class="text-success">Rp 0</span>
                    </h4>
                    
                    {{-- HIDDEN INPUT FOR TOTAL COST --}}
                    <input type="hidden" name="total_cost" id="total-cost-input" value="0">

                    <div>
                        <a href="{{ route('purchases.index') }}"
                           class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit"
                                class="btn btn-success"
                                id="checkout-btn"
                                disabled> {{-- Disabled default --}}
                            <i class="fas fa-save me-1"></i> Save Purchase
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const productRowsContainer = document.getElementById('product-rows');
const addProductBtn = document.getElementById('add-product');
const grandTotalText = document.getElementById('grand-total');
const totalCostInput = document.getElementById('total-cost-input');
const supplierSelect = document.getElementById('supplier-select'); 
const checkoutBtn = document.getElementById('checkout-btn');

// --- 1. CORE LOGIC & CALCULATIONS ---

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.product-row').forEach(row => {
        const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const subtotal = price * qty;
        
        row.querySelector('.subtotal').innerText =
            'Rp ' + subtotal.toLocaleString('id-ID');
        total += subtotal;
    });
    
    grandTotalText.innerText =
        'Rp ' + total.toLocaleString('id-ID');
    totalCostInput.value = total; 
}

// --- 2. AJAX AND PRODUCT FILTERING ---

async function loadProductsBySupplier(supplierId) {
    if (!supplierId) {
        return [];
    }
    try {
        // PERBAIKAN URL: Memberikan nilai 0 sebagai placeholder di Blade, lalu menggantinya di JS
        const baseUrl = "{{ route('purchases.products-by-supplier', ['supplierId' => 0]) }}";
        const url = baseUrl.replace('/0', '/' + supplierId);

        const response = await fetch(url);
        
        if (!response.ok) {
            console.error('Failed to fetch products: ' + response.statusText);
            return [];
        }
        return response.json();
    } catch (error) {
        console.error('Error fetching products:', error);
        return [];
    }
}

function populateProductDropdowns(products) {
    document.querySelectorAll('.product-select').forEach(selectElement => {
        const selectedProductId = selectElement.value;
        
        // Kosongkan dropdown & beri placeholder
        selectElement.innerHTML = '<option value="" data-cost-price="0">-- Select Product --</option>';

        // Isi dengan produk baru
        products.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            // Menggunakan product.title
            option.textContent = product.title; 
            // Menggunakan data-cost-price
            option.setAttribute('data-cost-price', product.cost_price); 
            
            if (product.id == selectedProductId) {
                option.selected = true;
            }

            selectElement.appendChild(option);
        });
        
        // Setelah mengisi ulang dropdown, pastikan baris pertama terisi
        if (selectElement.closest('.product-row')) {
            const row = selectElement.closest('.product-row');
            // Jika produk yang dipilih sebelumnya tidak ada, reset nilai unit price dan qty
            if (selectElement.value !== selectedProductId || selectElement.value === "") {
                row.querySelector('.unit-price-input').value = 0;
                row.querySelector('.qty-input').value = 1;
            }
        }
    });
    calculateTotal(); 
}

// --- 3. ROW MANAGEMENT ---

function addRow() {
    const index = Date.now();
    const row = document.createElement('div');
    row.className = 'row align-items-center mb-2 product-row';

    // Dapatkan opsi yang saat ini ada di dropdown produk pertama yang sudah dimuat.
    // Ini penting agar baris baru otomatis memiliki daftar produk yang sudah difilter.
    const productSelectElement = document.querySelector('.product-select');
    const currentOptions = productSelectElement 
        ? productSelectElement.innerHTML 
        : '<option value="" data-cost-price="0">-- Select Supplier First --</option>';

    row.innerHTML = `
    <div class="col-md-4">
        <select name="products[${index}][id]"
                class="form-select product-select"
                required>
            ${currentOptions} 
        </select>
    </div>

    <div class="col-md-2">
        <input type="number"
                name="products[${index}][price]" // DIGANTI
                class="form-control unit-price-input text-end"
                value="0"
                min="0"
                step="1"
                required>
    </div>

    <div class="col-md-2">
        <input type="number"
                name="products[${index}][quantity]" // DIGANTI
                class="form-control qty-input"
                min="1"
                value="1"
                required>
    </div>

        <div class="col-md-2 fw-bold subtotal text-end">Rp 0</div>

        <div class="col-md-2 text-end">
            <button type="button"
                    class="btn btn-danger btn-sm remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    productRowsContainer.appendChild(row);

    // Setelah row baru ditambahkan, pastikan dropdown pertama (jika ada) ter-trigger change
    // agar unit price dan total terhitung jika opsi sudah ada.
    calculateTotal();
}


// --- 4. EVENT LISTENERS ---

// Listener untuk Tombol Add Row
addProductBtn.addEventListener('click', addRow);

// Listener untuk Dropdown Supplier (TRIGGER AJAX)
supplierSelect.addEventListener('change', async function() {
    const supplierId = this.value;

    if (!supplierId) {
        populateProductDropdowns([]);
        addProductBtn.disabled = true;
        checkoutBtn.disabled = true;
        return;
    }

    const products = await loadProductsBySupplier(supplierId);
    populateProductDropdowns(products);
    
    // Aktifkan tombol Add Product dan Checkout setelah produk dimuat
    addProductBtn.disabled = false;
    checkoutBtn.disabled = false;
});

// Listener untuk Select Produk (Mengisi Unit Price)
productRowsContainer.addEventListener('change', e => {
    if (!e.target.classList.contains('product-select')) return;

    const option = e.target.selectedOptions[0];
    const row = e.target.closest('.product-row');

    const costPrice = option.dataset.costPrice || 0; 

    // Masukkan harga beli default ke input unit-price
    const unitPriceInput = row.querySelector('.unit-price-input');
    unitPriceInput.value = costPrice;
    
    // Set Quantity menjadi 1
    const qtyInput = row.querySelector('.qty-input');
    qtyInput.value = 1;

    calculateTotal();
});

// Listener untuk Quantity atau Unit Price berubah (Menghitung Subtotal)
productRowsContainer.addEventListener('input', e => {
    if (e.target.classList.contains('qty-input') || e.target.classList.contains('unit-price-input')) {
        calculateTotal();
    }
});

// Listener untuk Remove Row
productRowsContainer.addEventListener('click', e => {
    if (e.target.closest('.remove-row')) {
        e.target.closest('.product-row').remove();
        calculateTotal();
        
        // Jika tidak ada baris produk lagi, tambahkan satu baris kosong
        if (productRowsContainer.children.length === 0) {
            addRow();
        }
    }
});

// Inisialisasi: Panggil addRow saat DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    // 1. Tambahkan baris produk pertama
    addRow(); 
    
    // 2. Jika supplier sudah terpilih (misal: karena form kembali setelah validasi gagal),
    //    paksa event 'change' untuk memuat produk.
    if (supplierSelect.value) {
        supplierSelect.dispatchEvent(new Event('change')); 
    }
});
</script>
@endpush