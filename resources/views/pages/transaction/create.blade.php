@extends('layouts.master')

@section('title', 'Tambah Transaksi Baru')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>
                            Tambah Transaksi Baru
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('transaction.store') }}" method="POST" id="createTransactionForm">
                            @csrf

                            {{-- Section 1: Invoice Information --}}
                            <div class="section-card mb-4">
                                <div class="section-header">
                                    <i class="fas fa-file-invoice text-primary me-2"></i>
                                    <h5 class="mb-0">Informasi Invoice</h5>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="invoice_number" class="form-label fw-semibold">
                                                    <i class="fas fa-hashtag me-1"></i>
                                                    Nomor Invoice
                                                </label>
                                                <input type="text" class="form-control form-control @error('invoice_number') is-invalid @enderror" 
                                                       id="invoice_number" name="invoice_number" 
                                                       value="{{ old('invoice_number', 'INV-' . date('Ymd') . '-' . mt_rand(1000, 9999)) }}" readonly>
                                                @error('invoice_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="transaction_date" class="form-label fw-semibold">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    Tanggal Transaksi
                                                </label>
                                                <input type="date" class="form-control form-control @error('transaction_date') is-invalid @enderror" 
                                                       id="transaction_date" name="transaction_date" 
                                                       value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                                                @error('transaction_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: Customer Information --}}
                            <div class="section-card mb-4">
                                <div class="section-header">
                                    <i class="fas fa-user text-success me-2"></i>
                                    <h5 class="mb-0">Informasi Pelanggan</h5>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customer_name" class="form-label fw-semibold">
                                                    <i class="fas fa-user-tag me-1"></i>
                                                    Nama Pelanggan <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                                       id="customer_name" name="customer_name" value="{{ old('customer_name') }}" 
                                                       placeholder="Masukkan nama pelanggan" required>
                                                @error('customer_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="vehicle_number" class="form-label fw-semibold">
                                                    <i class="fas fa-id-card me-1"></i>
                                                    Nomor Kendaraan <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control @error('vehicle_number') is-invalid @enderror" 
                                                       id="vehicle_number" name="vehicle_number" value="{{ old('vehicle_number') }}" 
                                                       placeholder="Contoh: B 1234 XYZ" required>
                                                @error('vehicle_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="vehicle_model" class="form-label fw-semibold">
                                                    <i class="fas fa-car me-1"></i>
                                                    Merk/Model Kendaraan
                                                </label>
                                                <input type="text" class="form-control @error('vehicle_model') is-invalid @enderror" 
                                                       id="vehicle_model" name="vehicle_model" value="{{ old('vehicle_model') }}" 
                                                       placeholder="Contoh: Toyota Avanza, Honda Vario">
                                                @error('vehicle_model')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 3: Transaction Items --}}
                            <div class="section-card mb-4">
                                <div class="section-header">
                                    <i class="fas fa-shopping-cart text-warning me-2"></i>
                                    <h5 class="mb-0">Detail Item Transaksi</h5>
                                    <button type="button" class="btn btn-success btn-sm ms-auto" id="add-item">
                                        <i class="fas fa-plus me-1"></i>
                                        Tambah Item
                                    </button>
                                </div>
                                <div class="section-body">
                                    <div class="table-responsive">
                                        <div id="items-container">
                                            {{-- Item Row Template --}}
                                            <div class="item-row mb-3 p-3 border rounded bg-light" data-item-index="0">
                                                <div class="row align-items-end">
                                                    <div class="col-md-4">
                                                        <label for="item-0" class="form-label fw-semibold">
                                                            <i class="fas fa-box me-1"></i>
                                                            Pilih Item <span class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-select item-select @error('items.0.item_full_id') is-invalid @enderror" 
                                                                name="items[0][item_full_id]" id="item-0" required>
                                                            <option value="">-- Pilih Item --</option>
                                                            <optgroup label="🔧 Layanan Service">
                                                                @foreach ($services as $service)
                                                                    <option value="service-{{ $service->id }}" data-price="{{ $service->harga_standar }}">
                                                                        {{ $service->nama }} (Rp {{ number_format($service->harga_standar, 0, ',', '.') }})
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                            <optgroup label="🔩 Sparepart">
                                                                @foreach ($spareparts as $sparepart)
                                                                    <option value="sparepart-{{ $sparepart->id }}" data-price="{{ $sparepart->final_selling_price }}">
                                                                        {{ $sparepart->name }}
                                                                        @if($sparepart->isDiscountActive())
                                                                            (Diskon {{ $sparepart->discount_percentage }}% - Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }})
                                                                        @else
                                                                            (Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
                                                                        @endif
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        </select>
                                                        @error('items.0.item_full_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <input type="hidden" class="item-type-input" name="items[0][item_type]">
                                                        <input type="hidden" class="item-id-input" name="items[0][item_id]">
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label for="price-0" class="form-label fw-semibold">
                                                            <i class="fas fa-tag me-1"></i>
                                                            Harga
                                                        </label>
                                                        <input type="number" class="form-control price-input @error('items.0.price') is-invalid @enderror" 
                                                               name="items[0][price]" id="price-0" step="0.01" placeholder="0" required>
                                                        @error('items.0.price')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label for="qty-0" class="form-label fw-semibold">
                                                            <i class="fas fa-sort-numeric-up me-1"></i>
                                                            Jumlah
                                                        </label>
                                                        <div class="input-group">
                                                            <button class="btn btn-outline-secondary btn-qty-minus" type="button" data-action="minus">
                                                                <i class="fas fa-minus text-dark">-</i>
                                                            </button>
                                                            <input type="number" class="form-control qty-input @error('items.0.quantity') is-invalid @enderror" 
                                                                name="items[0][quantity]" id="qty-0" value="1" required> {{-- min="1" MUST BE REMOVED HERE --}}
                                                            <button class="btn btn-outline-secondary btn-qty-plus" type="button" data-action="plus">
                                                                <i class="fas fa-plus text-dark">+</i>
                                                            </button>
                                                        </div>
                                                        @error('items.0.quantity')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-2">
                                                        <label for="item_subtotal_display-0" class="form-label fw-semibold">
                                                            <i class="fas fa-calculator me-1"></i>
                                                            Subtotal
                                                        </label>
                                                        <input type="text" class="form-control item-subtotal-display bg-white" 
                                                               id="item_subtotal_display-0" value="Rp 0" readonly>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger remove-item w-100">
                                                            <i class="fas fa-trash me-1"></i>
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 4: Payment Summary --}}
                            <div class="section-card mb-4">
                                <div class="section-header">
                                    <i class="fas fa-money-bill-wave text-info me-2"></i>
                                    <h5 class="mb-0">Ringkasan Pembayaran</h5>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="summary-box">
                                                <div class="form-group">
                                                    <label for="overall_sub_total_display" class="form-label fw-semibold">
                                                        <i class="fas fa-receipt me-1"></i>
                                                        Total Harga Semua Item
                                                    </label>
                                                    <input type="text" class="form-control form-control bg-light" 
                                                           id="overall_sub_total_display" value="Rp 0" readonly>
                                                </div>

                                                <div class="form-group">
                                                    <label for="global_discount" class="form-label fw-semibold">
                                                        <i class="fas fa-percent me-1"></i>
                                                        Diskon Transaksi (Rp)
                                                    </label>
                                                    <input type="number" class="form-control @error('global_discount') is-invalid @enderror" 
                                                           id="global_discount" name="global_discount" 
                                                           value="{{ old('global_discount', 0) }}" min="0" step="0.01" placeholder="0">
                                                    @error('global_discount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="final_total_display" class="form-label fw-bold text-primary">
                                                        <i class="fas fa-dollar-sign me-1"></i>
                                                        TOTAL AKHIR
                                                    </label>
                                                    <input type="text" class="form-control form-control fw-bold text-primary border-primary" 
                                                           id="final_total_display" value="Rp 0" readonly>
                                                    <input type="hidden" id="final_total_hidden" name="total_price">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="payment_method" class="form-label fw-semibold">
                                                    <i class="fas fa-credit-card me-1"></i>
                                                    Metode Pembayaran <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select @error('payment_method') is-invalid @enderror" 
                                                        id="payment_method" name="payment_method" required>
                                                    <option value="">-- Pilih Metode --</option>
                                                    <option value="tunai" {{ old('payment_method') == 'tunai' ? 'selected' : '' }}>
                                                        Tunai
                                                    </option>
                                                    <option value="transfer bank" {{ old('payment_method') == 'transfer bank' ? 'selected' : '' }}>
                                                        Transfer Bank
                                                    </option>
                                                    <option value="kartu debit" {{ old('payment_method') == 'kartu debit' ? 'selected' : '' }}>
                                                        Kartu Debit
                                                    </option>
                                                    <option value="e-wallet" {{ old('payment_method') == 'e-wallet' ? 'selected' : '' }}>
                                                        E-Wallet
                                                    </option>
                                                </select>
                                                @error('payment_method')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="status" class="form-label fw-semibold">
                                                    <i class="fas fa-flag me-1"></i>
                                                    Status Transaksi <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select @error('status') is-invalid @enderror" 
                                                        id="status" name="status" required>
                                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                                        Pending
                                                    </option>
                                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>
                                                        Selesai
                                                    </option>
                                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>
                                                        Dibatalkan
                                                    </option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Form Actions --}}
                            <div class="form-actions">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('transaction.index') }}" class="btn btn-secondary btn">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary btn">
                                        <i class="fas fa-save me-1"></i>
                                        Simpan Transaksi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .section-card {
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            background: #fff;
        }

        .section-header {
            background: linear-gradient(135deg, #f8f9fc 0%, #eaecf4 100%);
            padding: 15px 20px;
            border-bottom: 1px solid #e3e6f0;
            border-radius: 10px 10px 0 0;
            display: flex;
            align-items: center;
        }

        .section-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: #5a5c69;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* .form-control-lg {
            padding: 12px 20px;
            font-size: 1.1rem;
        } */

        .item-row {
            background: #f8f9fc;
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .item-row:hover {
            border-color: #4e73df;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .summary-box {
            background: linear-gradient(135deg, #f8f9fc 0%, #eaecf4 100%);
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e3e6f0;
        }

        .form-actions {
            background: #f8f9fc;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e3e6f0;
            margin-top: 20px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        /* .btn-lg {
            padding: 12px 30px;
            font-size: 1.1rem;
        } */

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .text-danger {
            color: #e74a3b !important;
        }

        .bg-light {
            background-color: #f8f9fc !important;
        }

        .border-primary {
            border-color: #4e73df !important;
        }

        @media (max-width: 768px) {
            .section-body {
                padding: 15px;
            }
            
            .item-row {
                margin-bottom: 1rem;
            }
            
            .item-row .col-md-2, 
            .item-row .col-md-4 {
                margin-bottom: 1rem;
            }
        }
    </style>
@endsection

@push('addon-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = 1;

        const itemsContainer = document.getElementById('items-container');
        const addItemButton = document.getElementById('add-item');
        const globalDiscountInput = document.getElementById('global_discount');
        const overallSubTotalDisplay = document.getElementById('overall_sub_total_display');
        const finalTotalDisplay = document.getElementById('final_total_display');
        const finalTotalHidden = document.getElementById('final_total_hidden');
        const form = document.getElementById('createTransactionForm');

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        function calculateTotals() {
            let overallSubTotal = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                const priceInput = row.querySelector('.price-input');
                const qtyInput = row.querySelector('.qty-input');
                const itemSubtotalDisplay = row.querySelector('.item-subtotal-display');

                const price = priceInput ? parseFloat(priceInput.value) : 0;
                // Parse quantity, if empty or invalid, treat as 0 for calculation, but
                // the event listener on qtyInput will correct it to 1
                const qty = qtyInput ? parseInt(qtyInput.value) || 0 : 0; 

                let itemSubtotal = 0;
                if (!isNaN(price) && !isNaN(qty)) {
                    itemSubtotal = price * qty;
                }
                
                if (itemSubtotalDisplay) {
                    itemSubtotalDisplay.value = formatRupiah(itemSubtotal);
                }

                overallSubTotal += itemSubtotal;
            });

            const globalDiscount = parseFloat(globalDiscountInput.value) || 0;
            let finalTotal = overallSubTotal - globalDiscount;
            if (finalTotal < 0) finalTotal = 0;

            overallSubTotalDisplay.value = formatRupiah(overallSubTotal);
            finalTotalDisplay.value = formatRupiah(finalTotal);
            finalTotalHidden.value = finalTotal.toFixed(2);
        }

        function addEventListenersToNewRow(row) {
            const itemSelect = row.querySelector('.item-select');
            const priceInput = row.querySelector('.price-input');
            const qtyInput = row.querySelector('.qty-input');
            const removeItemButton = row.querySelector('.remove-item');
            const itemTypeInput = row.querySelector('.item-type-input');
            const itemIdInput = row.querySelector('.item-id-input');
            const itemSubtotalDisplay = row.querySelector('.item-subtotal-display');
            
            // New: Quantity buttons
            const qtyMinusButton = row.querySelector('.btn-qty-minus');
            const qtyPlusButton = row.querySelector('.btn-qty-plus');

            if (itemSelect) {
                itemSelect.addEventListener('change', function () {
                    const selected = this.options[this.selectedIndex];
                    const price = selected.dataset.price || 0;

                    if (priceInput) priceInput.value = price;

                    const fullId = selected.value;
                    if (fullId) {
                        const [type, id] = fullId.split('-');
                        if (itemTypeInput) itemTypeInput.value = type;
                        if (itemIdInput) itemIdInput.value = id;
                    } else {
                        if (itemTypeInput) itemTypeInput.value = '';
                        if (itemIdInput) itemIdInput.value = '';
                    }
                    calculateTotals();
                });
            }

            if (priceInput) {
                priceInput.addEventListener('input', calculateTotals);
            }

            // Updated: Quantity input event listener
            if (qtyInput) {
                qtyInput.addEventListener('input', function() {
                    let value = this.value;
                    // Allow empty input for a moment, but then correct to 1 if it's invalid
                    if (value.trim() === '' || isNaN(parseInt(value)) || parseInt(value) < 1) {
                        this.value = 1; // Default to 1 if empty, not a number, or less than 1
                    }
                    calculateTotals();
                });
            }

            // New: Event listeners for plus/minus buttons
            if (qtyMinusButton) {
                qtyMinusButton.addEventListener('click', function() {
                    let currentVal = parseInt(qtyInput.value) || 0; // Handle empty/invalid as 0
                    if (currentVal > 1) { // Ensure it doesn't go below 1
                        qtyInput.value = currentVal - 1;
                        qtyInput.dispatchEvent(new Event('input')); // Trigger input event to recalculate
                    } else if (currentVal === 1) {
                        // If it's 1, allow it to be cleared if you want, but the input handler will set it back to 1
                        // Or, you can just do nothing to prevent going below 1
                        qtyInput.value = ''; // Temporarily clear
                        qtyInput.dispatchEvent(new Event('input')); // Trigger input event
                    }
                });
            }

            if (qtyPlusButton) {
                qtyPlusButton.addEventListener('click', function() {
                    let currentVal = parseInt(qtyInput.value) || 0; // Handle empty/invalid as 0
                    qtyInput.value = currentVal + 1;
                    qtyInput.dispatchEvent(new Event('input')); // Trigger input event to recalculate
                });
            }

            if (removeItemButton) {
                removeItemButton.addEventListener('click', function () {
                    const currentRows = itemsContainer.querySelectorAll('.item-row');
                    if (currentRows.length > 1) {
                        // Add fade out animation
                        row.style.transition = 'opacity 0.3s ease';
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            calculateTotals();
                        }, 300);
                    } else {
                        // Reset the last row
                        const selectEl = row.querySelector('.item-select');
                        if (selectEl) selectEl.selectedIndex = 0;
                        const priceIn = row.querySelector('.price-input');
                        if (priceIn) priceIn.value = '';
                        const qtyIn = row.querySelector('.qty-input');
                        if (qtyIn) qtyIn.value = 1; // Reset to 1
                        const typeIn = row.querySelector('.item-type-input');
                        if (typeIn) typeIn.value = '';
                        const idIn = row.querySelector('.item-id-input');
                        if (idIn) idIn.value = '';
                        if (itemSubtotalDisplay) itemSubtotalDisplay.value = formatRupiah(0);
                        calculateTotals();
                    }
                });
            }

            if (itemSelect.value) {
                const selectedOption = itemSelect.options[itemSelect.selectedIndex];
                const price = selectedOption.dataset.price || 0;
                if (priceInput) priceInput.value = price;

                const fullId = selectedOption.value;
                if (fullId) {
                    const [type, id] = fullId.split('-');
                    if (itemTypeInput) itemTypeInput.value = type;
                    if (itemIdInput) itemIdInput.value = id;
                }
            }
        }

        addItemButton.addEventListener('click', function () {
            const firstRowTemplate = itemsContainer.querySelector('.item-row');
            if (!firstRowTemplate) {
                console.error("No item-row template found to clone.");
                return;
            }

            const newRow = firstRowTemplate.cloneNode(true);
            newRow.setAttribute('data-item-index', itemIndex);
            
            // Add fade in animation
            newRow.style.opacity = '0';
            newRow.style.transform = 'translateY(20px)';

            newRow.querySelectorAll('input, select, label').forEach(el => {
                if (el.name) {
                    el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
                }
                if (el.id) {
                    el.id = el.id.replace(/-\d+/, `-${itemIndex}`);
                }
                if (el.tagName === 'LABEL' && el.htmlFor) {
                    el.htmlFor = el.htmlFor.replace(/-\d+/, `-${itemIndex}`);
                }
                
                // Clear values for the new row
                if (el.classList.contains('price-input') || el.classList.contains('item-type-input') || el.classList.contains('item-id-input')) {
                    el.value = '';
                } else if (el.tagName === 'SELECT') {
                    el.selectedIndex = 0;
                } else if (el.type === 'number') {
                    el.value = 1; // Always start new quantity at 1
                } else if (el.type === 'text' && el.classList.contains('item-subtotal-display')) {
                    el.value = formatRupiah(0);
                } else if (el.type === 'text') {
                    el.value = '';
                }
            });

            itemsContainer.appendChild(newRow);
            
            // Animate in
            setTimeout(() => {
                newRow.style.transition = 'all 0.3s ease';
                newRow.style.opacity = '1';
                newRow.style.transform = 'translateY(0)';
            }, 10);
            
            addEventListenersToNewRow(newRow);
            itemIndex++;
            calculateTotals();
        });

        // Initialize listeners for the first item row
        const initialItemRow = document.querySelector('.item-row[data-item-index="0"]');
        if (initialItemRow) {
            addEventListenersToNewRow(initialItemRow);
        }

        globalDiscountInput.addEventListener('input', calculateTotals);

        calculateTotals();
        
        form.addEventListener('submit', function (e) {
            // Re-calculate totals one last time before submission to ensure accuracy
            calculateTotals(); 

            const total = parseFloat(finalTotalHidden.value);
            if (isNaN(total) || total < 0) {
                e.preventDefault();
                alert('Total harga tidak valid. Periksa kembali data item dan diskon.');
            }
        });
    });
</script>
@endpush