@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/transaction-form.css') }}">

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="form-card">
                <h3 class="page-title-form">Create New Purchase Transaction</h3>

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
                        <div class="col-md-6">
                            <label class="form-label">Created By</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Transaction Date</label>
                            <input type="text" class="form-control" value="{{ now()->format('d F Y - H:i:s') }}" readonly>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" id="supplier-select" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                <option value="">-- Choose Supplier (or select product first) --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->supplier_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- BAGIAN 2: DAFTAR PRODUK --}}
                    <div class="product-section-title">Products to Purchase</div>

                    <div id="product-rows-header">
                        <div>Product</div>
                        <div>Unit Cost</div>
                        <div>Quantity</div>
                        <div>Subtotal</div>
                        <div class="text-center"></div>
                    </div>

                    <div id="product-rows-container">
                        {{-- Baris akan ditambah via JS --}}
                    </div>

                    <button type="button" class="btn-add-product mt-3" id="add-product-btn">
                        + Add Product
                    </button>

                    {{-- BAGIAN 3: TOTAL & AKSI --}}
                    <div class="form-actions">
                        <div id="grand-total-display">
                            Grand Total: <span id="grand-total-text">Rp 0</span>
                        </div>
                        
                        <input type="hidden" name="total_cost" id="total-cost-input" value="0">

                        <a href="{{ route('purchases.index') }}" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-save" id="checkout-btn">Save Purchase</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Ambil data produk awal dari server (dikirim dari Controller)
    const allProducts = @json($products);
    
    const container = document.getElementById('product-rows-container');
    const addBtn = document.getElementById('add-product-btn');
    const grandTotalText = document.getElementById('grand-total-text');
    const totalCostInput = document.getElementById('total-cost-input');
    const supplierSelect = document.getElementById('supplier-select'); 

    const formatRupiah = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num);

    // --- 1. CORE LOGIC ---

    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const costPrice = parseFloat(row.querySelector('.unit-cost-input').value) || 0;
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            const subtotal = costPrice * qty;
            row.querySelector('.subtotal-text').innerText = formatRupiah(subtotal);
            total += subtotal;
        });
        grandTotalText.innerText = formatRupiah(total);
        totalCostInput.value = total;
    }

    // Fungsi Fetch Supplier via AJAX
    async function fetchSupplierByProduct(productId) {
        if (!productId) return null;
        try {
            const response = await fetch(`/get-supplier-by-product/${productId}`);
            if (!response.ok) throw new Error('Not found');
            return await response.json();
        } catch (error) {
            console.error('Error fetching supplier:', error);
            return null;
        }
    }

    // --- 2. DROPDOWN SYNC LOGIC ---

    // Fungsi untuk memperbarui isi dropdown produk di baris tertentu
    function updateProductOptions(selectElement, supplierId, currentProductId = null) {
        // Jika supplier dipilih, filter produk. Jika tidak, tampilkan semua.
        const filtered = supplierId 
            ? allProducts.filter(p => p.supplier_id == supplierId)
            : allProducts;

        let optionsHTML = '<option value="">-- Select Product --</option>';
        filtered.forEach(p => {
            const selected = p.id == currentProductId ? 'selected' : '';
            optionsHTML += `<option value="${p.id}" data-cost-price="${p.cost_price}" ${selected}>${p.title}</option>`;
        });
        selectElement.innerHTML = optionsHTML;
    }

    // --- 3. ROW MANAGEMENT ---

    function addRow() {
        const index = Date.now();
        const row = document.createElement('div');
        row.className = 'product-row'; 

        row.innerHTML = `
            <div>
                <select name="products[${index}][id]" class="form-select product-select" required></select>
            </div>
            <div>
                <input type="number" name="products[${index}][price]" class="form-control unit-cost-input" value="0" min="0" required>
            </div>
            <div>
                <input type="number" name="products[${index}][quantity]" class="form-control qty-input" value="1" min="1" required>
            </div>
            <div class="subtotal-text">Rp 0</div>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn-remove-product remove-row" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        container.appendChild(row);
        
        // Isi dropdown produk di baris baru ini sesuai supplier yang sudah terpilih (jika ada)
        const newSelect = row.querySelector('.product-select');
        updateProductOptions(newSelect, supplierSelect.value);
        
        calculateGrandTotal();
    }

    // --- 4. EVENT LISTENERS ---

    // A. Saat Supplier diubah secara manual
    supplierSelect.addEventListener('change', function() {
        const supplierId = this.value;
        // Update semua baris dropdown produk agar sesuai supplier yang dipilih
        document.querySelectorAll('.product-select').forEach(select => {
            updateProductOptions(select, supplierId, select.value);
        });
    });

    // B. Klik tombol tambah baris
    addBtn.addEventListener('click', addRow);

    // C. Event Delegation untuk aksi di dalam baris
    container.addEventListener('change', async function(e) {
        // C.1 Saat Produk Dipilih
        if (e.target.classList.contains('product-select')) {
            const row = e.target.closest('.product-row');
            const productId = e.target.value;

            if (productId) {
                const supplierData = await fetchSupplierByProduct(productId);
                
                if (supplierData) {
                    const currentSupplier = supplierSelect.value;
                    
                    // Jika supplier masih kosong, isi otomatis dan filter produk lainnya
                    if (!currentSupplier) {
                        supplierSelect.value = supplierData.id;
                        // Trigger event change manual agar semua dropdown produk tersaring
                        supplierSelect.dispatchEvent(new Event('change'));
                    } 
                    // Jika ganti produk tapi supplier beda dari yang sudah terpilih
                    else if (currentSupplier != supplierData.id) {
                        alert(`Warning: This product belongs to ${supplierData.supplier_name}. Please stay with the same supplier.`);
                    }
                }

                // Set harga default
                const option = e.target.selectedOptions[0];
                row.querySelector('.unit-cost-input').value = option.dataset.costPrice || 0;
            }
            calculateGrandTotal();
        }
    });

    // D. Input Qty/Price
    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('unit-cost-input')) {
            calculateGrandTotal();
        }
    });

    // E. Hapus Baris
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
            const row = e.target.closest('.product-row');
            if (row) {
                row.remove();
                if (container.children.length === 0) addRow();
                calculateGrandTotal();
            }
        }
    });

    // Init: Baris Pertama
    document.addEventListener('DOMContentLoaded', async function() {
        // Ambil ID dari PHP (jika ada)
        const preselectedId = "{{ $selectedProductId ?? '' }}";

        if (preselectedId) {
            // 1. Tambah baris baru
            addRow(); 
            
            // 2. Cari dropdown di baris yang baru dibuat
            const firstSelect = container.querySelector('.product-select');
            if (firstSelect) {
                // 3. Set nilainya sesuai ID dari Dashboard
                firstSelect.value = preselectedId;
                
                // 4. Trigger event 'change' secara manual agar:
                //    - Supplier otomatis terisi (via AJAX)
                //    - Harga otomatis muncul
                //    - List produk lainnya tersaring sesuai supplier
                firstSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        } else {
            // Jika buka manual tanpa dari Dashboard, buat baris kosong biasa
            addRow();
        }
    });

</script>
@endpush