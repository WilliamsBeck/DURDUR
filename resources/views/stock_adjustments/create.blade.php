@extends('layouts.app')

@section('title', 'Create New Adjustment')

@section('content')
{{-- Menggunakan class .form-card dari transaction-form.css --}}
<div class="form-card">
    <h3 class="mb-4">Create New Adjustment</h3>

    <form action="{{ route('stock-adjustments.store') }}" method="POST" id="adjustmentForm">
        @csrf
        
        {{-- SECTION 1: Header Input (Adjust By & Time) --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label fw-bold small text-muted">Adjust By</label>
                {{-- Background abu-abu muda untuk readonly --}}
                <input type="text" class="form-control" style="background-color: #f1f3f5; border: none;" value="{{ Auth::user()->name }}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small text-muted">Adjustment Time</label>
                <input type="text" class="form-control" style="background-color: #f1f3f5; border: none;" value="{{ now()->format('d F Y - H:i:s') }}" readonly>
            </div>
        </div>

        <hr class="my-4" style="border-top: 1px solid #e0e0e0;">

        {{-- SECTION 2: Product Table Header --}}
        <h5 class="fw-bold mb-3">Select Stock</h5>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Grid Header (Hidden on Mobile) --}}
        <div class="d-none d-md-flex row mb-2 px-1">
            <div class="col-3"><small class="fw-bold text-muted text-uppercase">Product</small></div>
            <div class="col-1 text-center"><small class="fw-bold text-muted text-uppercase">Old Stock</small></div>
            <div class="col-2 text-center"><small class="fw-bold text-muted text-uppercase">New Stock</small></div>
            <div class="col-1 text-center"><small class="fw-bold text-muted text-uppercase">Diff</small></div>
            <div class="col-2"><small class="fw-bold text-muted text-uppercase">Reason</small></div>
            <div class="col-2"><small class="fw-bold text-muted text-uppercase">Note</small></div>
            <div class="col-1"></div> {{-- Spacer for delete button --}}
        </div>

        {{-- Container Rows --}}
        <div id="itemsContainer">
            {{-- JavaScript akan mengisi row di sini --}}
        </div>

        {{-- Tombol Add Product (Hitam Lonjong Sesuai Figma) --}}
        <button type="button" class="btn rounded-pill mt-3 px-4 py-2 fw-bold" id="addProductBtn" 
                style="background-color: black; color: white; border: none; font-size: 0.9rem;">
            <i class="fas fa-plus me-2"></i>Add Product
        </button>

        {{-- SECTION 3: Action Buttons (Save & Cancel) --}}
        {{-- Menggunakan class .form-actions dari CSS Anda --}}
        <div class="form-actions mt-5 pt-3 border-top">
            <a href="{{ route('stock-adjustments.index') }}" class="btn-cancel text-decoration-none px-4 py-2 rounded-3" style="background-color: #e0e0e0; color: #333; border: none;">Cancel</a>
            
            {{-- Tombol Save warna Lime (class .btn-save dari CSS Anda) --}}
            <button type="submit" class="btn-save px-5 py-2 rounded-3 border-0 fw-bold">Save</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowIdx = 0;
        const itemsContainer = document.getElementById('itemsContainer');
        const addProductBtn = document.getElementById('addProductBtn');

        function addNewRow() {
            const div = document.createElement('div');
            // G-2 untuk gutter (jarak antar kolom) yang lebih rapat
            div.classList.add('row', 'align-items-center', 'mb-3', 'g-2'); 
            div.id = `row_${rowIdx}`;
            
            div.innerHTML = `
                {{-- Product Select (Lebar) --}}
                <div class="col-md-3">
                    <select name="items[${rowIdx}][product_id]" class="form-select product-select shadow-none bg-light border-0" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">{{ $product->title }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Old Stock (Kecil, Readonly Abu-abu) --}}
                <div class="col-md-1">
                    <input type="text" class="form-control text-center old-stock shadow-none border-0" style="background-color: #e9ecef;" readonly value="0">
                </div>

                {{-- New Stock (Input Putih Border Halus) --}}
                <div class="col-md-2">
                    <input type="number" name="items[${rowIdx}][new_stock]" class="form-control text-center new-stock shadow-none border" required min="0" placeholder="0">
                </div>

                {{-- Difference (Kecil, Readonly, Bold) --}}
                <div class="col-md-1">
                    <input type="text" class="form-control text-center difference border-0 bg-transparent fw-bold shadow-none" readonly value="0">
                </div>

                {{-- Reason Select --}}
                <div class="col-md-2">
                    <select name="items[${rowIdx}][reason]" class="form-select shadow-none bg-light border-0" required>
                        <option value="" disabled selected>Select Reason</option>
                        <option value="Rusak">Rusak</option>
                        <option value="Expired">Expired</option>
                        <option value="Salah SO">Salah SO</option>
                        <option value="Lost">Lost</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                {{-- Note Input --}}
                <div class="col-md-2">
                    <input type="text" name="items[${rowIdx}][note]" class="form-control shadow-none bg-light border-0" placeholder="Note">
                </div>

                {{-- Delete Button (Merah Bulat) --}}
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-danger rounded-circle remove-row p-0 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="fas fa-times" style="font-size: 0.9rem;"></i>
                    </button>
                </div>
            `;

            itemsContainer.appendChild(div);
            attachEvents(div);
            rowIdx++;
        }

        function attachEvents(row) {
            const productSelect = row.querySelector('.product-select');
            const oldStockInput = row.querySelector('.old-stock');
            const newStockInput = row.querySelector('.new-stock');
            const differenceInput = row.querySelector('.difference');
            const removeBtn = row.querySelector('.remove-row');

            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const stock = selectedOption.getAttribute('data-stock') || 0;
                oldStockInput.value = stock;
                calculateDiff();
            });

            newStockInput.addEventListener('input', calculateDiff);

            function calculateDiff() {
                const oldVal = parseInt(oldStockInput.value) || 0;
                const newVal = parseInt(newStockInput.value); 
                
                if (!isNaN(newVal)) {
                    const diff = newVal - oldVal;
                    differenceInput.value = diff > 0 ? `+${diff}` : diff;
                    // Warna teks: Hijau kalau plus, Merah kalau minus
                    differenceInput.style.color = diff < 0 ? '#dc3545' : (diff > 0 ? '#198754' : 'black');
                } else {
                    differenceInput.value = 0;
                }
            }

            removeBtn.addEventListener('click', function() {
                row.remove();
            });
        }

        // Tambah baris pertama otomatis saat load
        addNewRow(); 

        addProductBtn.addEventListener('click', addNewRow);
    });
</script>
@endsection