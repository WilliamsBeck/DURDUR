@extends('layouts.app')

@section('content')
{{-- Load CSS Form --}}
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- Container Utama (.form-card) --}}
            <div class="form-card">

                {{-- Header Title --}}
                <h3 class="page-title-form">Create New Purchase Transaction</h3>

                {{-- ERROR MESSAGES --}}
                @if ($errors->any())
                    <div class="alert alert-danger mb-4 rounded-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form">
                    @csrf

                    {{-- BAGIAN 1: INFORMASI UMUM --}}
                    <div class="row g-4 mb-4">
                        {{-- Created By (Readonly) --}}
                        <div class="col-md-6">
                            <label class="form-label">Created By</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                        </div>

                        {{-- Transaction Date (Readonly) --}}
                        <div class="col-md-6">
                            <label class="form-label">Transaction Date</label>
                            <input type="text" class="form-control" value="{{ now()->format('d F Y - H:i:s') }}" readonly>
                        </div>
                        
                        {{-- Supplier Select --}}
                        <div class="col-md-12">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" id="supplier-select" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                <option value="">-- Choose Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->supplier_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- BAGIAN 2: DAFTAR PRODUK --}}
                    <div class="product-section-title">Products to Purchase</div>

                    {{-- Header Grid (#product-rows-header) --}}
                    <div id="product-rows-header">
                        <div>Product</div>             {{-- 4fr --}}
                        <div>Unit Cost</div>           {{-- 2fr --}}
                        <div>Quantity</div>            {{-- 2fr --}}
                        <div>Subtotal</div>            {{-- 2fr --}}
                        <div class="text-center"></div> {{-- 0.5fr (Action) --}}
                    </div>

                    {{-- Container Baris Produk --}}
                    <div id="product-rows-container">
                        {{-- Rows will be added via JS --}}
                    </div>

                    {{-- Tombol Tambah Produk --}}
                    <button type="button" class="btn-add-product mt-3" id="add-product-btn" disabled>
                        + Add Product
                    </button>

                    {{-- BAGIAN 3: TOTAL & TOMBOL AKSI (.form-actions) --}}
                    <div class="form-actions">
                        
                        {{-- Grand Total Display --}}
                        <div id="grand-total-display">
                            Grand Total: <span id="grand-total-text">Rp 0</span>
                        </div>
                        
                        {{-- Hidden Input Total Cost --}}
                        <input type="hidden" name="total_cost" id="total-cost-input" value="0">

                        {{-- Tombol Cancel --}}
                        <a href="{{ route('purchases.index') }}" class="btn-cancel">
                            Cancel
                        </a>

                        {{-- Tombol Save --}}
                        <button type="submit" class="btn-save" id="checkout-btn" disabled>
                            Save Purchase
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
    // --- ELEMENT REFERENCES ---
    const container = document.getElementById('product-rows-container'); // Container baris produk
    const addBtn = document.getElementById('add-product-btn');
    const grandTotalText = document.getElementById('grand-total-text');
    const totalCostInput = document.getElementById('total-cost-input');
    const supplierSelect = document.getElementById('supplier-select'); 
    const checkoutBtn = document.getElementById('checkout-btn');

    // Format Rupiah Helper
    const formatRupiah = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num);

    // --- 1. CORE LOGIC ---

    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            // Purchase berbeda dgn Sales: User bisa input harga beli (Unit Cost)
            const costPrice = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            const subtotal = costPrice * qty;
            
            // Update Subtotal Text
            row.querySelector('.subtotal-text').innerText = formatRupiah(subtotal);
            total += subtotal;
        });
        
        grandTotalText.innerText = formatRupiah(total);
        totalCostInput.value = total;
    }

    // --- 2. AJAX & PRODUCT LOADING ---

    async function loadProductsBySupplier(supplierId) {
        if (!supplierId) return [];
        try {
            // Placeholder URL replacement
            const baseUrl = "{{ route('purchases.products-by-supplier', ['supplierId' => 0]) }}";
            const url = baseUrl.replace('/0', '/' + supplierId);

            const response = await fetch(url);
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        } catch (error) {
            console.error('Error fetching products:', error);
            return [];
        }
    }

    // Populate dropdown produk di SEMUA baris yang ada
    function populateProductDropdowns(products) {
        // Reset tombol Add & Checkout jika list kosong
        const hasProducts = products.length > 0;
        addBtn.disabled = !hasProducts;
        checkoutBtn.disabled = !hasProducts;

        // Loop semua dropdown product yang ada di form
        document.querySelectorAll('.product-select').forEach(select => {
            const currentVal = select.value;
            
            // Reset options
            select.innerHTML = '<option value="">-- Select Product --</option>';
            
            products.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.title;
                opt.dataset.costPrice = p.cost_price; // Simpan harga beli
                if (p.id == currentVal) opt.selected = true;
                select.appendChild(opt);
            });

            // Trigger reset row values jika produk yang dipilih sebelumnya hilang
            if(select.value !== currentVal) {
                const row = select.closest('.product-row');
                if(row) {
                    row.querySelector('.unit-cost-input').value = 0;
                    row.querySelector('.qty-input').value = 1;
                    row.querySelector('.subtotal-text').innerText = 'Rp 0';
                }
            }
        });
        
        calculateGrandTotal();
    }

    // --- 3. ROW MANAGEMENT ---

    function addRow() {
    const index = Date.now();
    const row = document.createElement('div');
    row.className = 'product-row'; 

    const existingSelect = document.querySelector('.product-select');
    const optionsHTML = existingSelect 
        ? existingSelect.innerHTML 
        : '<option value="">-- Select Product --</option>'; // Pastikan value kosong

    row.innerHTML = `
        <div>
            <select name="products[${index}][id]" class="form-select product-select" required>
                ${optionsHTML}
            </select>
        </div>

        <div>
            <input type="number" 
                   name="products[${index}][price]" 
                   class="form-control unit-cost-input" 
                   value="0" min="0" required> </div>

        <div>
            <input type="number" 
                   name="products[${index}][quantity]" 
                   class="form-control qty-input" 
                   value="1" min="1" required> </div>

        <div class="subtotal-text">Rp 0</div>

        <div class="d-flex justify-content-center">
            <button type="button" class="btn-remove-product remove-row" title="Remove">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    container.appendChild(row);
    calculateGrandTotal();
}

    // --- 4. EVENT LISTENERS ---

    // Supplier Change -> Load Products
    supplierSelect.addEventListener('change', async function() {
        const supplierId = this.value;
        const products = await loadProductsBySupplier(supplierId);
        populateProductDropdowns(products);
    });

    // Add Row Button
    addBtn.addEventListener('click', addRow);

    // Event Delegation untuk Input/Change di dalam Row
    container.addEventListener('change', function(e) {
        // Jika Produk dipilih -> Set Default Unit Cost
        if (e.target.classList.contains('product-select')) {
            const row = e.target.closest('.product-row');
            const option = e.target.selectedOptions[0];
            const cost = option.dataset.costPrice || 0;
            
            // Set harga beli default ke input
            row.querySelector('.unit-cost-input').value = cost;
            calculateGrandTotal();
        }
    });

    container.addEventListener('input', function(e) {
        // Jika Qty atau Unit Cost berubah -> Hitung Ulang
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('unit-cost-input')) {
            calculateGrandTotal();
        }
    });

    container.addEventListener('click', function(e) {
        // Remove Row
        if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            const row = e.target.closest('.product-row');
            if(row) {
                row.remove();
                calculateGrandTotal();
                
                // Jika kosong, tambah 1 baris baru
                if (container.children.length === 0) {
                    addRow();
                }
            }
        }
    });

    // --- INIT ---
    document.addEventListener('DOMContentLoaded', function() {
        addRow(); // Tambah baris pertama
        
        // Jika kembali dari error validation (old input ada), trigger change supplier
        if (supplierSelect.value) {
            supplierSelect.dispatchEvent(new Event('change'));
        }
    });

</script>
@endpush