@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="form-card">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Create New Transaction</h3>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                    ← Back
                </a>
            </div>

            {{-- ERROR --}}
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

                {{-- HEADER --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cashier</label>
                        <input type="text"
                               class="form-control"
                               value="{{ auth()->user()->name }}"
                               readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Customer Email (Optional)</label>
                        <input type="email"
                               name="customer_email"
                               class="form-control"
                               value="{{ old('customer_email') }}">
                    </div>

                    {{-- PAYMENT --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_id"
                                class="form-select"
                                required>
                            <option value="">-- Choose Payment --</option>
                            @foreach ($payments as $payment)
                                <option value="{{ $payment->id }}"
                                    {{ old('payment_id') == $payment->id ? 'selected' : '' }}>
                                    {{ $payment->method_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Transaction Time</label>
                        <input type="text"
                               class="form-control"
                               value="{{ now()->format('d F Y - H:i:s') }}"
                               readonly>
                    </div>
                </div>

                <hr>

                {{-- PRODUCTS --}}
                <h5 class="mb-3">Products</h5>

                <div class="row fw-bold d-none d-md-flex mb-2">
                    <div class="col-md-4">Product</div>
                    <div class="col-md-2">Price</div>
                    <div class="col-md-2">Qty</div>
                    <div class="col-md-2">Subtotal</div>
                    <div class="col-md-2"></div>
                </div>

                <div id="product-rows"></div>

                <button type="button"
                        class="btn btn-outline-primary mt-2"
                        id="add-product">
                    + Add Product
                </button>

                <hr>

                {{-- FOOTER --}}
                <div class="d-flex justify-content-between align-items-center">
                    <h4>
                        Grand Total:
                        <span id="grand-total">Rp 0</span>
                    </h4>

                    <div>
                        <a href="{{ route('transactions.index') }}"
                           class="btn btn-secondary">
                            Cancel
                        </a>
                        <button type="submit"
                                class="btn btn-success">
                            Checkout
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const productRows = document.getElementById('product-rows');
const addProductBtn = document.getElementById('add-product');
const grandTotalText = document.getElementById('grand-total');

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.product-row').forEach(row => {
        const price = parseFloat(row.dataset.price) || 0;
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const subtotal = price * qty;
        row.querySelector('.subtotal').innerText =
            'Rp ' + subtotal.toLocaleString('id-ID');
        total += subtotal;
    });
    grandTotalText.innerText =
        'Rp ' + total.toLocaleString('id-ID');
}

function addRow() {
    const index = Date.now();
    const row = document.createElement('div');
    row.className = 'row align-items-center mb-2 product-row';
    row.dataset.price = 0;
    row.dataset.stock = 0;

    row.innerHTML = `
        <div class="col-md-4">
            <select name="products[${index}][id]"
                    class="form-select product-select"
                    required>
                <option value="">-- Select Product --</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}"
                            data-price="{{ $product->price }}"
                            data-stock="{{ $product->stock }}">
                        {{ $product->title }} (Stock: {{ $product->stock }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <span class="price-text">Rp 0</span>
        </div>

        <div class="col-md-2">
            <input type="number"
                   name="products[${index}][quantity]"
                   class="form-control qty-input"
                   min="1"
                   required>
        </div>

        <div class="col-md-2 fw-bold subtotal">Rp 0</div>

        <div class="col-md-2 text-end">
            <button type="button"
                    class="btn btn-danger btn-sm remove-row">×</button>
        </div>
    `;
    productRows.appendChild(row);
}

addProductBtn.addEventListener('click', addRow);

productRows.addEventListener('change', e => {
    if (!e.target.classList.contains('product-select')) return;

    const option = e.target.selectedOptions[0];
    const row = e.target.closest('.product-row');

    const price = option.dataset.price || 0;
    const stock = option.dataset.stock || 0;

    row.dataset.price = price;
    row.dataset.stock = stock;

    row.querySelector('.price-text').innerText =
        'Rp ' + Number(price).toLocaleString('id-ID');

    const qtyInput = row.querySelector('.qty-input');
    qtyInput.value = 1;
    qtyInput.max = stock;

    calculateTotal();
});

productRows.addEventListener('input', e => {
    if (!e.target.classList.contains('qty-input')) return;

    const row = e.target.closest('.product-row');
    const stock = parseInt(row.dataset.stock);
    const qty = parseInt(e.target.value);

    if (qty > stock) {
        Swal.fire({
            icon: 'warning',
            title: 'Stock not enough',
            text: 'Max stock: ' + stock
        });
        e.target.value = stock;
    }

    calculateTotal();
});

productRows.addEventListener('click', e => {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('.product-row').remove();
        calculateTotal();
    }
});

document.addEventListener('DOMContentLoaded', addRow);
</script>
@endpush
