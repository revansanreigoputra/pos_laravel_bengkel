@extends('layouts.master')

@section('title', 'Edit Pesanan Pembelian')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i> Edit Pesanan Pembelian
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('purchase_orders.update', $purchaseOrder->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Gunakan metode PUT untuk update --}}

                        {{-- Section 1: Informasi Pesanan --}}
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
                                                <i class="fas fa-hashtag me-1"></i> Nomor Invoice
                                            </label>
                                            <input type="text" name="invoice_number" id="invoice_number"
                                                   class="form-control @error('invoice_number') is-invalid @enderror"
                                                   value="{{ old('invoice_number', $purchaseOrder->invoice_number) }}" required>
                                            @error('invoice_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="order_date" class="form-label fw-semibold">
                                                <i class="fas fa-calendar me-1"></i> Tanggal Pesanan
                                            </label>
                                            <input type="date" name="order_date" id="order_date"
                                                   class="form-control @error('order_date') is-invalid @enderror"
                                                   value="{{ old('order_date', $purchaseOrder->order_date ? $purchaseOrder->order_date->format('Y-m-d') : '') }}">
                                            @error('order_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="supplier_id" class="form-label fw-semibold">
                                        <i class="fas fa-truck me-1"></i> Supplier <span class="text-danger">*</span>
                                    </label>
                                    <select name="supplier_id" id="supplier_id"
                                            class="form-select select2-init @error('supplier_id') is-invalid @enderror" required>
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="notes" class="form-label fw-semibold">
                                        <i class="fas fa-sticky-note me-1"></i> Catatan (Opsional)
                                    </label>
                                    <textarea name="notes" id="notes" rows="3"
                                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Detail Item Pembelian --}}
                        <div class="section-card mb-4">
                            <div class="section-header d-flex align-items-center">
                                <i class="fas fa-boxes text-warning me-2"></i>
                                <h5 class="mb-0">Detail Item Pembelian</h5>
                                <button type="button" class="btn btn-success btn ms-auto px-5" id="add-item">
                                    <i class="fas fa-plus me-1"></i> Tambah Item
                                </button>
                            </div>
                            <div class="section-body p-3 border rounded bg-light">
                                <div id="items-container">
                                    @forelse (old('items', $purchaseOrder->items) as $index => $item)
                                        <div class="item-row mb-3 p-3 border rounded bg-white" data-item-index="{{ $index }}">
                                            <div class="row align-items-end">
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="sparepart-{{ $index }}" class="form-label fw-semibold">
                                                            <i class="fas fa-box me-1"></i> Pilih Sparepart <span class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-select sparepart-select @error("items.{$index}.sparepart_id") is-invalid @enderror"
                                                                name="items[{{ $index }}][sparepart_id]" id="sparepart-{{ $index }}" required>
                                                            <option value="">-- Pilih Sparepart --</option>
                                                            @foreach ($spareparts as $sparepart)
                                                                <option value="{{ $sparepart->id }}"
                                                                        data-purchase-price="{{ $sparepart->purchase_price ?? 0 }}"
                                                                        {{ old("items.{$index}.sparepart_id", $item->sparepart_id ?? '') == $sparepart->id ? 'selected' : '' }}>
                                                                    {{ $sparepart->name }} ({{ $sparepart->code_part }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error("items.{$index}.sparepart_id")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id ?? '' }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="purchase_price-{{ $index }}" class="form-label fw-semibold">
                                                            <i class="fas fa-tag me-1"></i> Harga Beli
                                                        </label>
                                                        <input type="number" class="form-control purchase-price-input @error("items.{$index}.purchase_price") is-invalid @enderror"
                                                               name="items[{{ $index }}][purchase_price]" id="purchase_price-{{ $index }}" step="0.01" placeholder="0" required
                                                               value="{{ old("items.{$index}.purchase_price", $item->purchase_price ?? 0) }}">
                                                        @error("items.{$index}.purchase_price")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="quantity-{{ $index }}" class="form-label fw-semibold">
                                                            <i class="fas fa-sort-numeric-up me-1"></i> Jumlah
                                                        </label>
                                                        <div class="input-group">
                                                            <button class="btn btn-outline-secondary btn-qty-minus" type="button" data-action="minus">
                                                                <i class="fas fa-minus text-dark"></i>
                                                            </button>
                                                            <input type="number" class="form-control qty-input @error("items.{$index}.quantity") is-invalid @enderror"
                                                                   name="items[{{ $index }}][quantity]" id="quantity-{{ $index }}" value="{{ old("items.{$index}.quantity", $item->quantity ?? 1) }}" min="1" required>
                                                            <button class="btn btn-outline-secondary btn-qty-plus" type="button" data-action="plus">
                                                                <i class="fas fa-plus text-dark"></i>
                                                            </button>
                                                        </div>
                                                        @error("items.{$index}.quantity")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="expired_date-{{ $index }}" class="form-label fw-semibold">
                                                            <i class="fas fa-calendar-times me-1"></i> Tgl Kadaluarsa (Opsional)
                                                        </label>
                                                        <input type="date" class="form-control @error("items.{$index}.expired_date") is-invalid @enderror"
                                                               id="expired_date-{{ $index }}" name="items[{{ $index }}][expired_date]"
                                                               value="{{ old("items.{$index}.expired_date", $item->expired_date ? $item->expired_date->format('Y-m-d') : '') }}">
                                                        @error("items.{$index}.expired_date")
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="item_notes-{{ $index }}" class="form-label fw-semibold">
                                                            <i class="fas fa-sticky-note me-1"></i> Catatan Item (Opsional)
                                                        </label>
                                                        <textarea class="form-control" name="items[{{ $index }}][notes]" id="item_notes-{{ $index }}" rows="1" placeholder="Catatan untuk item ini">{{ old("items.{$index}.notes", $item->notes ?? '') }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="item_subtotal_display-{{ $index }}" class="form-label fw-semibold">
                                                            <i class="fas fa-calculator me-1"></i> Subtotal Item
                                                        </label>
                                                        <input type="text" class="form-control item-subtotal-display bg-white"
                                                               id="item_subtotal_display-{{ $index }}" value="Rp 0" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <button type="button" class="btn btn-danger remove-item w-100">
                                                            <i class="fas fa-trash me-1"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        {{-- Template untuk item pertama jika tidak ada item yang dimuat --}}
                                        <div class="item-row mb-3 p-3 border rounded bg-white" data-item-index="0">
                                            <div class="row align-items-end">
                                                <div class="col-md-4">
                                                    <div class="form-group mb-3">
                                                        <label for="sparepart-0" class="form-label fw-semibold">
                                                            <i class="fas fa-box me-1"></i> Pilih Sparepart <span class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-select sparepart-select" name="items[0][sparepart_id]" id="sparepart-0" required>
                                                            <option value="">-- Pilih Sparepart --</option>
                                                            @foreach ($spareparts as $sparepart)
                                                                <option value="{{ $sparepart->id }}" data-purchase-price="{{ $sparepart->purchase_price ?? 0 }}">
                                                                    {{ $sparepart->name }} ({{ $sparepart->code_part }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="purchase_price-0" class="form-label fw-semibold">
                                                            <i class="fas fa-tag me-1"></i> Harga Beli
                                                        </label>
                                                        <input type="number" class="form-control purchase-price-input" name="items[0][purchase_price]" id="purchase_price-0" step="0.01" placeholder="0" required value="0">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="quantity-0" class="form-label fw-semibold">
                                                            <i class="fas fa-sort-numeric-up me-1"></i> Jumlah
                                                        </label>
                                                        <div class="input-group">
                                                            <button class="btn btn-outline-secondary btn-qty-minus" type="button" data-action="minus">
                                                                <i class="fas fa-minus text-dark">_</i>
                                                            </button>
                                                            <input type="number" class="form-control qty-input" name="items[0][quantity]" id="quantity-0" value="1" min="1" required>
                                                            <button class="btn btn-outline-secondary btn-qty-plus" type="button" data-action="plus">
                                                                <i class="fas fa-plus text-dark">+</i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="expired_date-0" class="form-label fw-semibold">
                                                            <i class="fas fa-calendar-times me-1"></i> Tgl Kadaluarsa (Opsional)
                                                        </label>
                                                        <input type="date" class="form-control" id="expired_date-0" name="items[0][expired_date]">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="item_notes-0" class="form-label fw-semibold">
                                                            <i class="fas fa-sticky-note me-1"></i> Catatan Item (Opsional)
                                                        </label>
                                                        <textarea class="form-control" name="items[0][notes]" id="item_notes-0" rows="1" placeholder="Catatan untuk item ini"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <label for="item_subtotal_display-0" class="form-label fw-semibold">
                                                            <i class="fas fa-calculator me-1"></i> Subtotal Item
                                                        </label>
                                                        <input type="text" class="form-control item-subtotal-display bg-white" id="item_subtotal_display-0" value="Rp 0" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group mb-3">
                                                        <button type="button" class="btn btn-danger remove-item w-100">
                                                            <i class="fas fa-trash me-1"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Ringkasan Pembayaran --}}
                        <div class="section-card mb-4">
                            <div class="section-header d-flex align-items-center">
                                <i class="fas fa-money-bill-wave text-info me-2"></i>
                                <h5 class="mb-0">Ringkasan Pembayaran</h5>
                            </div>
                            <div class="section-body p-3 border rounded bg-light">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="overall_sub_total_display" class="form-label fw-semibold">
                                                <i class="fas fa-receipt me-1"></i> Total Harga Semua Item
                                            </label>
                                            <input type="text" class="form-control bg-light" id="overall_sub_total_display" value="Rp 0" readonly>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="global_discount" class="form-label fw-semibold">
                                                <i class="fas fa-percent me-1"></i> Diskon Pesanan (Rp)
                                            </label>
                                            <input type="number" class="form-control @error('global_discount') is-invalid @enderror"
                                                   id="global_discount" name="global_discount"
                                                   value="{{ old('global_discount', $purchaseOrder->global_discount ?? 0) }}" min="0" step="0.01" placeholder="0">
                                            @error('global_discount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="final_total_display" class="form-label fw-bold text-primary">
                                                <i class="fas fa-dollar-sign me-1"></i> TOTAL AKHIR
                                            </label>
                                            <input type="text" class="form-control fw-bold text-primary border-primary" id="final_total_display" value="Rp 0" readonly>
                                            <input type="hidden" id="final_total_hidden" name="total_price" value="{{ old('total_price', $purchaseOrder->total_price ?? 0) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="payment_method" class="form-label fw-semibold">
                                                <i class="fas fa-credit-card me-1"></i> Metode Pembayaran <span class="text-danger">*</span>
                                            </label>
                                            <select name="payment_method" id="payment_method"
                                                    class="form-select @error('payment_method') is-invalid @enderror" required>
                                                <option value="">-- Pilih Metode --</option>
                                                <option value="Tunai" {{ old('payment_method', $purchaseOrder->payment_method) == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                                                <option value="Transfer Bank" {{ old('payment_method', $purchaseOrder->payment_method) == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                                                <option value="Kartu Debit" {{ old('payment_method', $purchaseOrder->payment_method) == 'Kartu Debit' ? 'selected' : '' }}>Kartu Debit</option>
                                                <option value="E-Wallet" {{ old('payment_method', $purchaseOrder->payment_method) == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="status" class="form-label fw-semibold">
                                                <i class="fas fa-flag me-1"></i> Status Pesanan <span class="text-danger">*</span>
                                            </label>
                                            <select name="status" id="status"
                                                    class="form-select @error('status') is-invalid @enderror" required>
                                                <option value="pending" {{ old('status', $purchaseOrder->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="received" {{ old('status', $purchaseOrder->status) == 'received' ? 'selected' : '' }}>Diterima</option>
                                                <option value="canceled" {{ old('status', $purchaseOrder->status) == 'canceled' ? 'selected' : '' }}>Dibatalkan</option>
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
                        <div class="form-actions d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Perbarui Pesanan
                            </button>
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
    let itemIndex = {{ count(old('items', $purchaseOrder->items)) > 0 ? count(old('items', $purchaseOrder->items)) -1 : 0 }}; // Inisialisasi itemIndex berdasarkan jumlah item yang ada

    // Fungsi untuk menginisialisasi Select2 pada elemen baru
    function initializeSelect2(element) {
        $(element).select2({
            theme: "bootstrap-5",
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
        if (finalTotal < 0) finalTotal = 0;

        $('#overall_sub_total_display').val('Rp ' + overallSubTotal.toLocaleString('id-ID'));
        $('#final_total_display').val('Rp ' + finalTotal.toLocaleString('id-ID'));
        $('#final_total_hidden').val(finalTotal);
    }

    // Fungsi untuk menambahkan item baru
    function addItemRow() {
        itemIndex++;
        // Gunakan template dari item pertama jika ada, atau buat template kosong
        const originalRow = $('#items-container').find('.item-row').first();
        let newRow;

        if (originalRow.length > 0) {
            newRow = originalRow.clone(true);
        } else {
            // Jika tidak ada item sama sekali (misal, saat pertama kali edit PO tanpa item)
            // Anda perlu membuat template HTML kosong di sini atau mengarahkannya ke template tersembunyi
            // Untuk saat ini, saya akan membuat versi sederhana.
            newRow = $(`
                <div class="item-row mb-3 p-3 border rounded bg-white" data-item-index="${itemIndex}">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="sparepart-${itemIndex}" class="form-label fw-semibold">
                                    <i class="fas fa-box me-1"></i> Pilih Sparepart <span class="text-danger">*</span>
                                </label>
                                <select class="form-select sparepart-select" name="items[${itemIndex}][sparepart_id]" id="sparepart-${itemIndex}" required>
                                    <option value="">-- Pilih Sparepart --</option>
                                    @foreach ($spareparts as $sparepart)
                                        <option value="{{ $sparepart->id }}" data-purchase-price="{{ $sparepart->purchase_price ?? 0 }}">
                                            {{ $sparepart->name }} ({{ $sparepart->code_part }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label for="purchase_price-${itemIndex}" class="form-label fw-semibold">
                                    <i class="fas fa-tag me-1"></i> Harga Beli
                                </label>
                                <input type="number" class="form-control purchase-price-input" name="items[${itemIndex}][purchase_price]" id="purchase_price-${itemIndex}" step="0.01" placeholder="0" required value="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label for="quantity-${itemIndex}" class="form-label fw-semibold">
                                    <i class="fas fa-sort-numeric-up me-1"></i> Jumlah
                                </label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary btn-qty-minus" type="button" data-action="minus">
                                        <i class="fas fa-minus text-dark"></i>
                                    </button>
                                    <input type="number" class="form-control qty-input" name="items[${itemIndex}][quantity]" id="quantity-${itemIndex}" value="1" min="1" required>
                                    <button class="btn btn-outline-secondary btn-qty-plus" type="button" data-action="plus">
                                        <i class="fas fa-plus text-dark"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label for="expired_date-${itemIndex}" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-times me-1"></i> Tgl Kadaluarsa (Opsional)
                                </label>
                                <input type="date" class="form-control" id="expired_date-${itemIndex}" name="items[${itemIndex}][expired_date]">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="item_notes-${itemIndex}" class="form-label fw-semibold">
                                    <i class="fas fa-sticky-note me-1"></i> Catatan Item (Opsional)
                                </label>
                                <textarea class="form-control" name="items[${itemIndex}][notes]" id="item_notes-${itemIndex}" rows="1" placeholder="Catatan untuk item ini"></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label for="item_subtotal_display-${itemIndex}" class="form-label fw-semibold">
                                    <i class="fas fa-calculator me-1"></i> Subtotal Item
                                </label>
                                <input type="text" class="form-control item-subtotal-display bg-white" id="item_subtotal_display-${itemIndex}" value="Rp 0" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <button type="button" class="btn btn-danger remove-item w-100">
                                    <i class="fas fa-trash me-1"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }

        // Update name dan id atribut untuk input di baris baru
        newRow.find('[name^="items["]').each(function() { // Cari semua input yang namanya dimulai dengan 'items['
            const oldName = $(this).attr('name');
            const newName = oldName.replace(/items\[\d+\]/, `items[${itemIndex}]`);
            $(this).attr('name', newName);
            const oldId = $(this).attr('id');
            if (oldId) {
                const newId = oldId.replace(/-\d+$/, `-${itemIndex}`);
                $(this).attr('id', newId);
            }
        });

        // Update for dan id atribut untuk label di baris baru
        newRow.find('label[for^="sparepart-"], label[for^="purchase_price-"], label[for^="quantity-"], label[for^="expired_date-"], label[for^="item_notes-"], label[for^="item_subtotal_display-"]').each(function() {
            const oldFor = $(this).attr('for');
            if (oldFor) {
                const newFor = oldFor.replace(/-\d+$/, `-${itemIndex}`);
                $(this).attr('for', newFor);
            }
        });

        // Reset nilai input untuk baris baru
        newRow.find('input[type="text"], input[type="number"], input[type="date"], textarea').val('');
        newRow.find('.qty-input').val(1);
        newRow.find('.purchase-price-input').val(0);
        newRow.find('.item-subtotal-display').val('Rp 0');
        newRow.find('select').val(''); // Reset select dropdown

        // Re-initialize Select2 for the cloned select element
        newRow.find('.sparepart-select').removeClass('select2-hidden-accessible').next('.select2-container').remove();
        initializeSelect2(newRow.find('.sparepart-select'));

        $('#items-container').append(newRow);
        calculateOverallTotal(); // Hitung ulang total setelah menambah item
    }

    $(document).ready(function() {
        // Inisialisasi Select2 untuk dropdown supplier
        initializeSelect2($('#supplier_id'));

        // Inisialisasi Select2 untuk semua item yang sudah ada saat halaman dimuat
        $('.sparepart-select').each(function() {
            initializeSelect2($(this));
        });

        // Hitung subtotal untuk setiap item yang sudah ada saat halaman dimuat
        $('.item-row').each(function() {
            calculateItemSubtotal($(this));
        });

        // Event listener untuk tombol "Tambah Item"
        $('#add-item').on('click', addItemRow);

        // Event listener untuk tombol "Hapus" item (delegasi event)
        $('#items-container').on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
                calculateOverallTotal();
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

            const purchasePrice = selectedOption.data('purchase-price');
            if (purchasePrice !== undefined) {
                purchasePriceInput.val(purchasePrice);
            } else {
                purchasePriceInput.val(0);
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
        margin-bottom: 1rem;
    }
</style>