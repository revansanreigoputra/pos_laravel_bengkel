@extends('layouts.master') {{-- Menggunakan layout master sesuai permintaan Anda --}}

@section('title', 'Tambah Pesanan Pembelian Baru')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>
                            Tambah Pesanan Pembelian Baru
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('purchase_orders.store') }}" method="POST" id="createPurchaseOrderForm">
                            @csrf

                            {{-- Section 1: Invoice Information --}}
                            <div class="section-card mb-4">
                                <div class="section-header d-flex align-items-center">
                                    <i class="fas fa-file-invoice text-primary me-2"></i>
                                    <h5 class="mb-0">Informasi Pesanan</h5>
                                </div>
                                <div class="section-body p-3 border rounded bg-light">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="invoice_number" class="form-label fw-semibold">
                                                    <i class="fas fa-hashtag me-1"></i>
                                                    Nomor Invoice
                                                </label>
                                                <input type="text" class="form-control @error('invoice_number') is-invalid @enderror"
                                                       id="invoice_number" name="invoice_number"
                                                       value="{{ old('invoice_number', 'PO-' . date('Ymd') . '-' . mt_rand(1000, 9999)) }}" readonly>
                                                @error('invoice_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="order_date" class="form-label fw-semibold">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    Tanggal Pesanan
                                                </label>
                                                <input type="date" class="form-control @error('order_date') is-invalid @enderror"
                                                       id="order_date" name="order_date"
                                                       value="{{ old('order_date', now()->format('Y-m-d')) }}" required>
                                                @error('order_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="supplier_id" class="form-label fw-semibold">
                                            <i class="fas fa-truck me-1"></i>
                                            Supplier <span class="text-danger">*</span>
                                        </label>
                                        <select name="supplier_id" id="supplier_id" class="form-select select2-init @error('supplier_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Supplier --</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: Purchase Order Items --}}
                            <div class="section-card mb-4">
                                <div class="section-header d-flex align-items-center">
                                    <i class="fas fa-boxes text-warning me-2"></i>
                                    <h5 class="mb-0">Detail Item Pembelian</h5>
                                    <button type="button" class="btn btn-success btn ms-auto px-5" id="add-item">
                                        <i class="fas fa-plus me-1"></i>
                                        Tambah Item
                                    </button>
                                </div>
                                <div class="section-body p-3 border rounded bg-light">
                                    <div id="items-container">
                                        {{-- Item Row Template --}}
                                        <div class="item-row mb-3 p-3 border rounded bg-white" data-item-index="0">
                                            <div class="row align-items-end">
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="sparepart-0" class="form-label fw-semibold">
                                                            <i class="fas fa-box me-1"></i>
                                                            Pilih Sparepart <span class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-select sparepart-select @error('items.0.sparepart_id') is-invalid @enderror"
                                                                name="items[0][sparepart_id]" id="sparepart-0" required>
                                                            <option value="">-- Pilih Sparepart --</option>
                                                            @foreach ($spareparts as $sparepart)
                                                                <option value="{{ $sparepart->id }}"
                                                                        data-purchase-price="{{ $sparepart->purchase_price ?? 0 }}"
                                                                        {{ old('items.0.sparepart_id') == $sparepart->id ? 'selected' : '' }}>
                                                                    {{ $sparepart->name }} ({{ $sparepart->code_part }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('items.0.sparepart_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="purchase_price-0" class="form-label fw-semibold">
                                                            <i class="fas fa-tag me-1"></i>
                                                            Harga Beli
                                                        </label>
                                                        <input type="number" class="form-control purchase-price-input @error('items.0.purchase_price') is-invalid @enderror"
                                                               name="items[0][purchase_price]" id="purchase_price-0" step="0.01" placeholder="0" required
                                                               value="{{ old('items.0.purchase_price') }}">
                                                        @error('items.0.purchase_price')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="quantity-0" class="form-label fw-semibold">
                                                            <i class="fas fa-sort-numeric-up me-1"></i>
                                                            Jumlah
                                                        </label>
                                                        <div class="input-group">
                                                            <button class="btn btn-outline-secondary btn-qty-minus" type="button" data-action="minus">
                                                                <i class="fas fa-minus text-dark">-</i>
                                                            </button>
                                                            <input type="number" class="form-control qty-input @error('items.0.quantity') is-invalid @enderror"
                                                                   name="items[0][quantity]" id="quantity-0" value="{{ old('items.0.quantity', 1) }}" min="1" required>
                                                            <button class="btn btn-outline-secondary btn-qty-plus" type="button" data-action="plus">
                                                                <i class="fas fa-plus text-dark">+</i>
                                                            </button>
                                                        </div>
                                                        @error('items.0.quantity')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="expired_date-0" class="form-label fw-semibold">
                                                            <i class="fas fa-calendar-times me-1"></i>
                                                            Tgl Kadaluarsa (Opsional)
                                                        </label>
                                                        <input type="date" class="form-control @error('items.0.expired_date') is-invalid @enderror"
                                                               id="expired_date-0" name="items[0][expired_date]" value="{{ old('items.0.expired_date') }}">
                                                        @error('items.0.expired_date')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="notes-0" class="form-label fw-semibold">
                                                            <i class="fas fa-sticky-note me-1"></i>
                                                            Catatan Item (Opsional)
                                                        </label>
                                                        <textarea class="form-control" name="items[0][notes]" id="notes-0" rows="1" placeholder="Catatan untuk item ini">{{ old('items.0.notes') }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="item_subtotal_display-0" class="form-label fw-semibold">
                                                            <i class="fas fa-calculator me-1"></i>
                                                            Subtotal Item
                                                        </label>
                                                        <input type="text" class="form-control item-subtotal-display bg-white"
                                                               id="item_subtotal_display-0" value="Rp 0" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
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

                            {{-- Section 3: Payment Summary --}}
                            <div class="section-card mb-4">
                                <div class="section-header d-flex align-items-center">
                                    <i class="fas fa-money-bill-wave text-info me-2"></i>
                                    <h5 class="mb-0">Ringkasan Pembayaran</h5>
                                </div>
                                <div class="section-body p-3 border rounded bg-light">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="summary-box">
                                                <div class="form-group mb-3">
                                                    <label for="overall_sub_total_display" class="form-label fw-semibold">
                                                        <i class="fas fa-receipt me-1"></i>
                                                        Total Harga Semua Item
                                                    </label>
                                                    <input type="text" class="form-control bg-light"
                                                           id="overall_sub_total_display" value="Rp 0" readonly>
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label for="global_discount" class="form-label fw-semibold">
                                                        <i class="fas fa-percent me-1"></i>
                                                        Diskon Pesanan (Rp)
                                                    </label>
                                                    <input type="number" class="form-control @error('global_discount') is-invalid @enderror"
                                                           id="global_discount" name="global_discount"
                                                           value="{{ old('global_discount', 0) }}" min="0" step="0.01" placeholder="0">
                                                    @error('global_discount')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="form-group mb-3">
                                                    <label for="final_total_display" class="form-label fw-bold text-primary">
                                                        <i class="fas fa-dollar-sign me-1"></i>
                                                        TOTAL AKHIR
                                                    </label>
                                                    <input type="text" class="form-control fw-bold text-primary border-primary"
                                                           id="final_total_display" value="Rp 0" readonly>
                                                    <input type="hidden" id="final_total_hidden" name="total_price">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="payment_method" class="form-label fw-semibold">
                                                    <i class="fas fa-credit-card me-1"></i>
                                                    Metode Pembayaran <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select @error('payment_method') is-invalid @enderror"
                                                        id="payment_method" name="payment_method" required>
                                                    <option value="">-- Pilih Metode --</option>
                                                    <option value="Tunai" {{ old('payment_method') == 'Tunai' ? 'selected' : '' }}>
                                                        Tunai
                                                    </option>
                                                    <option value="Transfer Bank" {{ old('payment_method') == 'Transfer Bank' ? 'selected' : '' }}>
                                                        Transfer Bank
                                                    </option>
                                                    <option value="Kartu Debit" {{ old('payment_method') == 'Kartu Debit' ? 'selected' : '' }}>
                                                        Kartu Debit
                                                    </option>
                                                    <option value="E-Wallet" {{ old('payment_method') == 'E-Wallet' ? 'selected' : '' }}>
                                                        E-Wallet
                                                    </option>
                                                </select>
                                                @error('payment_method')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="status" class="form-label fw-semibold">
                                                    <i class="fas fa-flag me-1"></i>
                                                    Status Pesanan <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select @error('status') is-invalid @enderror"
                                                        id="status" name="status" required>
                                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>
                                                        Pending
                                                    </option>
                                                    <option value="received" {{ old('status') == 'received' ? 'selected' : '' }}>
                                                        Diterima
                                                    </option>
                                                    <option value="canceled" {{ old('status') == 'canceled' ? 'selected' : '' }}>
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
                                    <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary btn">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary btn">
                                        <i class="fas fa-save me-1"></i>
                                        Simpan Pesanan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
<script>
    let itemIndex = 0; // Mulai dari 0 untuk item pertama

    // Fungsi untuk menginisialisasi Select2 pada elemen baru
    function initializeSelect2(element) {
        $(element).select2({
            theme: "bootstrap-5", // Jika menggunakan Bootstrap 5
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });
    }

    // Fungsi untuk menghitung ulang subtotal item
    function calculateItemSubtotal(itemRow) {
        const price = parseFloat(itemRow.find('.purchase-price-input').val()) || 0;
        const qty = parseInt(itemRow.find('.qty-input').val()) || 0;
        const subtotal = price * qty;
        itemRow.find('.item-subtotal-display').val('Rp ' + subtotal.toLocaleString('id-ID'));
        calculateOverallTotal();
    }

    // Fungsi untuk menghitung ulang total keseluruhan
    function calculateOverallTotal() {
        let overallSubTotal = 0;
        $('.item-row').each(function() {
            const price = parseFloat($(this).find('.purchase-price-input').val()) || 0;
            const qty = parseInt($(this).find('.qty-input').val()) || 0;
            overallSubTotal += (price * qty);
        });

        const globalDiscount = parseFloat($('#global_discount').val()) || 0;
        let finalTotal = overallSubTotal - globalDiscount;
        if (finalTotal < 0) finalTotal = 0; // Pastikan total tidak negatif

        $('#overall_sub_total_display').val('Rp ' + overallSubTotal.toLocaleString('id-ID'));
        $('#final_total_display').val('Rp ' + finalTotal.toLocaleString('id-ID'));
        $('#final_total_hidden').val(finalTotal); // Simpan nilai numerik di hidden input
    }

    // Fungsi untuk menambahkan item baru
    function addItemRow() {
        itemIndex++; // Tingkatkan indeks untuk item baru
        const originalRow = $('#items-container').find('.item-row[data-item-index="0"]').first();
        const newRow = originalRow.clone(true); // Clone dengan event handler

        newRow.attr('data-item-index', itemIndex);

        // Update name dan id atribut untuk input di baris baru
        newRow.find('[name^="items[0]"]').each(function() {
            const oldName = $(this).attr('name');
            const newName = oldName.replace(/items\[0\]/, `items[${itemIndex}]`);
            $(this).attr('name', newName);
            const oldId = $(this).attr('id');
            if (oldId) {
                const newId = oldId.replace(/-\d+$/, `-${itemIndex}`);
                $(this).attr('id', newId);
            }
        });

        // Update for dan id atribut untuk label di baris baru
        newRow.find('label[for^="item-0"], label[for^="price-0"], label[for^="qty-0"], label[for^="expired_date-0"], label[for^="notes-0"], label[for^="item_subtotal_display-0"]').each(function() {
            const oldFor = $(this).attr('for');
            if (oldFor) {
                const newFor = oldFor.replace(/-\d+$/, `-${itemIndex}`);
                $(this).attr('for', newFor);
            }
        });

        // Reset nilai input
        newRow.find('input').val('');
        newRow.find('.qty-input').val(1);
        newRow.find('.purchase-price-input').val(0);
        newRow.find('.item-subtotal-display').val('Rp 0');
        newRow.find('textarea').val('');
        newRow.find('select').val(''); // Reset select dropdown

        // Re-initialize Select2 for the cloned select element
        // Hapus Select2 yang lama dan inisialisasi ulang
        newRow.find('.sparepart-select').removeClass('select2-hidden-accessible').next('.select2-container').remove();
        initializeSelect2(newRow.find('.sparepart-select')); // Inisialisasi Select2 pada elemen select yang baru

        $('#items-container').append(newRow);
        calculateOverallTotal(); // Hitung ulang total setelah menambah item
    }

    $(document).ready(function() {
        // Inisialisasi Select2 untuk dropdown supplier
        initializeSelect2($('#supplier_id'));

        // Inisialisasi Select2 untuk item pertama
        initializeSelect2($('#sparepart-0'));

        // Event listener untuk tombol "Tambah Item"
        $('#add-item').on('click', addItemRow);

        // Event listener untuk tombol "Hapus" item (delegasi event)
        $('#items-container').on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) { // Pastikan setidaknya ada satu item tersisa
                $(this).closest('.item-row').remove();
                calculateOverallTotal(); // Hitung ulang total setelah menghapus item
            } else {
                alert('Tidak bisa menghapus semua item. Minimal harus ada satu item.');
            }
        });

        // Event listener untuk perubahan harga beli atau kuantitas (delegasi event)
        $('#items-container').on('input', '.purchase-price-input, .qty-input', function() {
            calculateItemSubtotal($(this).closest('.item-row'));
        });

        // Event listener untuk tombol plus/minus kuantitas (delegasi event)
        $('#items-container').on('click', '.btn-qty-minus', function() {
            const qtyInput = $(this).siblings('.qty-input');
            let currentVal = parseInt(qtyInput.val());
            if (currentVal > 1) {
                qtyInput.val(currentVal - 1);
                calculateItemSubtotal($(this).closest('.item-row'));
            }
        });

        $('#items-container').on('click', '.btn-qty-plus', function() {
            const qtyInput = $(this).siblings('.qty-input');
            let currentVal = parseInt(qtyInput.val());
            qtyInput.val(currentVal + 1);
            calculateItemSubtotal($(this).closest('.item-row'));
        });

        // Event listener untuk perubahan Select2 item sparepart
        $('#items-container').on('change', '.sparepart-select', function() {
            const selectedOption = $(this).find('option:selected');
            const itemRow = $(this).closest('.item-row');
            const purchasePriceInput = itemRow.find('.purchase-price-input');

            // Ambil harga beli dari data-purchase-price atribut option
            const purchasePrice = selectedOption.data('purchase-price');
            if (purchasePrice !== undefined) {
                purchasePriceInput.val(purchasePrice);
            } else {
                purchasePriceInput.val(0); // Reset jika tidak ada harga
            }
            calculateItemSubtotal(itemRow);
        });

        // Event listener untuk perubahan diskon global
        $('#global_discount').on('input', calculateOverallTotal);

        // Hitung total awal saat halaman dimuat
        calculateOverallTotal();
    });
</script>
@endpush

<style>
    /* Custom styles for section cards */
    .section-card {
        border: 1px solid #e0e0e0;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .section-header {
        background-color: #f8f9fa;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
    }
    .section-header h5 {
        font-weight: 600;
        color: #343a40;
    }
    .section-body {
        padding: 1.25rem;
    }
    .summary-box .form-group {
        margin-bottom: 1rem; /* Adjust spacing in summary box */
    }
</style>