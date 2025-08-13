@extends('layouts.master')

@section('title', 'Edit Transaksi')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            Edit Transaksi #{{ $transaction->invoice_number }}
                        </h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('transaction.update', $transaction->id) }}" method="POST" id="editTransactionForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="section-card mb-4">
                                <div class="section-header d-flex align-items-center">
                                    <i class="fas fa-file-invoice text-primary me-2"></i>
                                    <h5 class="mb-0">Informasi Invoice</h5>
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
                                                       value="{{ old('invoice_number', $transaction->invoice_number) }}" readonly>
                                                @error('invoice_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="transaction_date" class="form-label fw-semibold">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    Tanggal Transaksi
                                                </label>
                                                <input type="date" class="form-control @error('transaction_date') is-invalid @enderror"
                                                       id="transaction_date" name="transaction_date"
                                                       value="{{ old('transaction_date', Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d')) }}" required>
                                                @error('transaction_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-card mb-4">
                                <div class="section-header d-flex align-items-center">
                                    <i class="fas fa-user text-success me-2"></i>
                                    <h5 class="mb-0">Informasi Pelanggan</h5>
                                </div>
                                <div class="section-body p-3 border rounded bg-light">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="customer_name" class="form-label fw-semibold">
                                                    <i class="fas fa-user-tag me-1"></i>
                                                    Nama Pelanggan <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                                       id="customer_name" name="customer_name" value="{{ old('customer_name', $transaction->customer->name) }}"
                                                       placeholder="Masukkan nama pelanggan" required>
                                                @error('customer_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="customer_phone" class="form-label fw-semibold">
                                                    <i class="fas fa-phone me-1"></i>
                                                    Nomor Telepon Pelanggan <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                                       id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $transaction->customer->phone) }}"
                                                       placeholder="Contoh: 081234567890" required>
                                                @error('customer_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="customer_email" class="form-label fw-semibold">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    Email Pelanggan (Opsional)
                                                </label>
                                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                                                       id="customer_email" name="customer_email" value="{{ old('customer_email', $transaction->customer->email) }}"
                                                       placeholder="Masukkan email pelanggan">
                                                @error('customer_email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="customer_address" class="form-label fw-semibold">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    Alamat Pelanggan (Opsional)
                                                </label>
                                                <textarea class="form-control @error('customer_address') is-invalid @enderror"
                                                          id="customer_address" name="customer_address" rows="1"
                                                          placeholder="Masukkan alamat lengkap pelanggan">{{ old('customer_address', $transaction->customer->address) }}</textarea>
                                                @error('customer_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="vehicle_number" class="form-label fw-semibold">
                                                    <i class="fas fa-id-card me-1"></i>
                                                    Nomor Kendaraan
                                                </label>
                                                <input type="text" class="form-control @error('vehicle_number') is-invalid @enderror"
                                                       id="vehicle_number" name="vehicle_number" value="{{ old('vehicle_number', $transaction->vehicle_number) }}"
                                                       placeholder="Contoh: B 1234 XYZ">
                                                @error('vehicle_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="vehicle_model" class="form-label fw-semibold">
                                                    <i class="fas fa-car me-1"></i>
                                                    Merk/Model Kendaraan
                                                </label>
                                                <input type="text" class="form-control @error('vehicle_model') is-invalid @enderror"
                                                       id="vehicle_model" name="vehicle_model" value="{{ old('vehicle_model', $transaction->vehicle_model) }}"
                                                       placeholder="Contoh: Toyota Avanza, Honda Vario">
                                                @error('vehicle_model')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-card mb-4">
                                <div class="section-header d-flex align-items-center">
                                    <i class="fas fa-shopping-cart text-warning me-2"></i>
                                    <h5 class="mb-0">Detail Item Transaksi</h5>
                                    <button type="button" class="btn btn-success btn ms-auto px-2" id="add-item">
                                        <i class="fas fa-plus me-1"></i>
                                        Tambah Item
                                    </button>
                                </div>
                                <div class="section-body p-3 border rounded bg-light">
                                    <div id="items-container">
                                        @if(isset($transaction) && $transaction->items->isNotEmpty())
                                            @foreach($transaction->items as $idx => $item)
                                                <div class="item-row mb-3 p-3 border rounded bg-white" data-item-index="{{ $idx }}">
                                                    <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}">
                                                    <div class="row align-items-end">
                                                        <div class="col-md-4">
                                                            <label for="item-{{ $idx }}" class="form-label fw-semibold">
                                                                <i class="fas fa-box me-1"></i>
                                                                Pilih Item <span class="text-danger">*</span>
                                                            </label>
                                                            <select class="form-select item-select select2-init"
                                                                    name="items[{{ $idx }}][item_full_id]" id="item-{{ $idx }}" required>
                                                                <option value="">-- Pilih Item --</option>
                                                                <optgroup label="🔧 Layanan Service">
                                                                    @foreach ($services as $service)
                                                                        <option value="service-{{ $service->id }}" data-price="{{ $service->harga_standar }}"
                                                                            {{ ($item->item_type == 'service' && $item->item_id == $service->id) ? 'selected' : '' }}>
                                                                            {{ $service->nama }} (Rp {{ number_format($service->harga_standar, 0, ',', '.') }})
                                                                        </option>
                                                                    @endforeach
                                                                </optgroup>
                                                                <optgroup label="🔩 Sparepart">
                                                                    @foreach ($spareparts as $sparepart)
                                                                      <option value="sparepart-{{ $sparepart->id }}"
    data-price="{{ $sparepart->final_selling_price }}"
    data-stock="{{ $sparepart->available_stock }}"
    {{ ($item->item_type == 'sparepart' && $item->item_id == $sparepart->id) ? 'selected' : '' }}>
    {{ $sparepart->name }}
    @if($sparepart->isDiscountActive())
        (Diskon {{ $sparepart->discount_percentage }}% - Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }})
    @else
        (Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
    @endif
    (Stok: {{ $sparepart->available_stock }})
</option>

                                                                    @endforeach
                                                                </optgroup>
                                                            </select>
                                                            <input type="hidden" class="item-type-input" name="items[{{ $idx }}][item_type]" value="{{ $item->item_type }}">
                                                            <input type="hidden" class="item-id-input" name="items[{{ $idx }}][item_id]" value="{{ $item->item_id }}">
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label for="price-{{ $idx }}" class="form-label fw-semibold">
                                                                <i class="fas fa-tag me-1"></i>
                                                                Harga
                                                            </label>
                                                            <input type="number" class="form-control price-input"
                                                                   name="items[{{ $idx }}][price]" id="price-{{ $idx }}" step="0.01" value="{{ old('items.' . $idx . '.price', $item->price) }}" readonly>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label for="qty-{{ $idx }}" class="form-label fw-semibold">
                                                                <i class="fas fa-sort-numeric-up me-1"></i>
                                                                Jumlah
                                                            </label>
                                                            <div class="input-group">
                                                                <button class="btn btn-outline-secondary btn-qty-minus" type="button" data-action="minus">
                                                                    <i class="fas fa-minus text-dark"></i>
                                                                </button>
                                                                <input type="number" class="form-control qty-input"
                                                                       name="items[{{ $idx }}][quantity]" id="qty-{{ $idx }}" value="{{ old('items.' . $idx . '.quantity', $item->quantity) }}" required min="1">
                                                                <button class="btn btn-outline-secondary btn-qty-plus" type="button" data-action="plus">
                                                                    <i class="fas fa-plus text-dark"></i>
                                                                </button>
                                                            </div>
                                                            <div class="text-danger mt-1 stock-warning" style="display: none;">Stok tidak cukup!</div>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <label for="item_subtotal_display-{{ $idx }}" class="form-label fw-semibold">
                                                                <i class="fas fa-calculator me-1"></i>
                                                                Subtotal
                                                            </label>
                                                            <input type="text" class="form-control item-subtotal-display bg-white"
                                                                   id="item_subtotal_display-{{ $idx }}" value="Rp 0" readonly>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-danger remove-item w-100">
                                                                <i class="fas fa-trash me-1"></i> Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="item-row mb-3 p-3 border rounded bg-white" data-item-index="0">
                                                <input type="hidden" name="items[0][id]" value="">
                                                <div class="row align-items-end">
                                                    <div class="col-md-4">
                                                        <label for="item-0" class="form-label fw-semibold">
                                                            <i class="fas fa-box me-1"></i>
                                                            Pilih Item <span class="text-danger">*</span>
                                                        </label>
                                                        <select class="form-select item-select select2-init @error('items.0.item_full_id') is-invalid @enderror"
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
                                                                    <option value="sparepart-{{ $sparepart->id }}"
                                                                            data-price="{{ $sparepart->final_selling_price }}"
                                                                            data-stock="{{ $sparepart->available_stock }}">
                                                                        {{ $sparepart->name }}
                                                                        @if($sparepart->isDiscountActive())
                                                                            (Diskon {{ $sparepart->discount_percentage }}% - Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }})
                                                                        @else
                                                                            (Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
                                                                        @endif
                                                                        (Stok: {{ $sparepart->available_stock }})
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
                                                               name="items[0][price]" id="price-0" step="0.01" placeholder="0" required readonly>
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
                                                                <i class="fas fa-minus text-dark"></i>
                                                            </button>
                                                            <input type="number" class="form-control qty-input @error('items.0.quantity') is-invalid @enderror"
                                                                   name="items[0][quantity]" id="qty-0" value="1" required min="1">
                                                            <button class="btn btn-outline-secondary btn-qty-plus" type="button" data-action="plus">
                                                                <i class="fas fa-plus text-dark"></i>
                                                            </button>
                                                        </div>
                                                        <div class="text-danger mt-1 stock-warning" style="display: none;">Stok tidak cukup!</div>
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
                                        @endif
                                    </div>
                                </div>
                            </div>

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
                                                           value="{{ old('global_discount', $transaction->discount_amount) }}" min="0" step="0.01" placeholder="0">
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
                                                    <option value="Tunai" {{ old('payment_method', $transaction->payment_method) == 'Tunai' ? 'selected' : '' }}>
                                                        Tunai
                                                    </option>
                                                    <option value="Transfer Bank" {{ old('payment_method', $transaction->payment_method) == 'Transfer Bank' ? 'selected' : '' }}>
                                                        Transfer Bank
                                                    </option>
                                                    <option value="Kartu Debit" {{ old('payment_method', $transaction->payment_method) == 'Kartu Debit' ? 'selected' : '' }}>
                                                        Kartu Debit
                                                    </option>
                                                    <option value="E-Wallet" {{ old('payment_method', $transaction->payment_method) == 'E-Wallet' ? 'selected' : '' }}>
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
                                                    <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>
                                                        Pending
                                                    </option>
                                                    <option value="completed" {{ old('status', $transaction->status) == 'completed' ? 'selected' : '' }}>
                                                        Selesai
                                                    </option>
                                                    {{-- <option value="canceled" {{ old('status', $transaction->status) == 'canceled' ? 'selected' : '' }}>
                                                        Dibatalkan
                                                    </option> --}}
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('transaction.index') }}" class="btn btn-secondary btn">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary btn">
                                        <i class="fas fa-save me-1"></i>
                                        Simpan Perubahan
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
    let itemIndex = {{ $transaction->items->count() > 0 ? $transaction->items->count() : 0 }};

    function initializeSelect2(element) {
        if (element.data('select2')) {
            element.select2('destroy');
        }
        element.select2({
            theme: "bootstrap-5",
            width: element.data('width') ? element.data('width') : (element.hasClass('w-100') ? '100%' : 'style'),
            placeholder: element.data('placeholder'),
            allowClear: Boolean(element.data('allow-clear')),
        });
    }

    function calculateItemSubtotalAndStock(itemRow) {
        const price = parseFloat(itemRow.find('.price-input').val()) || 0;
        const qtyInput = itemRow.find('.qty-input');
        const qty = parseInt(qtyInput.val()) || 0;
        const subtotal = price * qty;
        itemRow.find('.item-subtotal-display').val('Rp ' + subtotal.toLocaleString('id-ID'));

        const itemSelect = itemRow.find('.item-select');
        const selectedOption = itemSelect.find('option:selected');
        const itemType = selectedOption.val().split('-')[0];
        const stockWarning = itemRow.find('.stock-warning');

        if (itemType === 'sparepart') {
            const availableStock = parseInt(selectedOption.data('stock')) || 0;
            if (qty > availableStock) {
                stockWarning.text(`Stok tidak cukup! Tersedia: ${availableStock}`).show();
                qtyInput.addClass('is-invalid');
            } else {
                stockWarning.hide();
                qtyInput.removeClass('is-invalid');
            }
        } else {
            stockWarning.hide();
            qtyInput.removeClass('is-invalid');
        }

        calculateOverallTotal();
    }

    function calculateOverallTotal() {
        let overallSubTotal = 0;
        $('.item-row').each(function() {
            const price = parseFloat($(this).find('.price-input').val()) || 0;
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

    function addItemRow() {
        // Get the template row from the initial empty row (if it exists) or clone the first existing item row
        let templateRow = $('#items-container').find('.item-row[data-item-index="0"]').first();

        // If no initial empty row, try to clone the first existing item row
        if (templateRow.length === 0 && $('.item-row').length > 0) {
            templateRow = $('.item-row').first();
        }

        const newRow = templateRow.clone(true);
        
        // Increment itemIndex for the new row
        itemIndex++;
        newRow.attr('data-item-index', itemIndex);

        // Update name dan id attributes
        newRow.find('[name^="items[0]"]').each(function() {
            const oldName = $(this).attr('name');
            const newName = oldName.replace(/items\[\d+\]/, `items[${itemIndex}]`);
            $(this).attr('name', newName);
            const oldId = $(this).attr('id');
            if (oldId) {
                const newId = oldId.replace(/-\d+$/, `-${itemIndex}`);
                $(this).attr('id', newId);
            }
        });

        // Update for attributes for labels
        newRow.find('label[for^="item-"], label[for^="price-"], label[for^="qty-"], label[for^="item_subtotal_display-"]').each(function() {
            const oldFor = $(this).attr('for');
            if (oldFor) {
                const newFor = oldFor.replace(/-\d+$/, `-${itemIndex}`);
                $(this).attr('for', newFor);
            }
        });

        // Reset values for new row
        newRow.find('input').val('');
        newRow.find('input[name$="[id]"]').val(''); // Clear the ID for new items
        newRow.find('.qty-input').val(1);
        newRow.find('.price-input').val(0);
        newRow.find('.item-subtotal-display').val('Rp 0');
        newRow.find('.stock-warning').hide();
        newRow.find('.qty-input').removeClass('is-invalid');

        // Reset select dropdown and re-initialize Select2
        const itemSelect = newRow.find('.item-select');
        itemSelect.val('');
        if (itemSelect.data('select2')) {
            itemSelect.select2('destroy');
        }
        initializeSelect2(itemSelect);

        // Clear hidden item_type and item_id for new rows
        newRow.find('.item-type-input').val('');
        newRow.find('.item-id-input').val('');

        $('#items-container').append(newRow);
        calculateOverallTotal();
    }

    $(document).ready(function() {
        // Initialize Select2 for all existing item rows
        $('.item-select').each(function() {
            initializeSelect2($(this));
        });

        // Event listener for "Tambah Item" button
        $('#add-item').on('click', addItemRow);

        // Event listener for "Hapus" item button
        $('#items-container').on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('.item-row').remove();
                calculateOverallTotal();
            } else {
                alert('Tidak bisa menghapus semua item. Minimal harus ada satu item.');
            }
        });

        // Event listener for price or quantity changes
        $('#items-container').on('input', '.price-input, .qty-input', function() {
            calculateItemSubtotalAndStock($(this).closest('.item-row'));
        });

        // Event listener for quantity plus/minus buttons
        $('#items-container').on('click', '.btn-qty-minus', function() {
            const qtyInput = $(this).siblings('.qty-input');
            let currentVal = parseInt(qtyInput.val());
            if (currentVal > 1) {
                qtyInput.val(currentVal - 1);
                calculateItemSubtotalAndStock($(this).closest('.item-row'));
            }
        });

        $('#items-container').on('click', '.btn-qty-plus', function() {
            const qtyInput = $(this).siblings('.qty-input');
            let currentVal = parseInt(qtyInput.val());
            qtyInput.val(currentVal + 1);
            calculateItemSubtotalAndStock($(this).closest('.item-row'));
        });

        // Event listener for item selection change in Select2
        $('#items-container').on('change', '.item-select', function() {
            const selectedOption = $(this).find('option:selected');
            const itemRow = $(this).closest('.item-row');
            const priceInput = itemRow.find('.price-input');
            const itemTypeInput = itemRow.find('.item-type-input');
            const itemIdInput = itemRow.find('.item-id-input');
            const qtyInput = itemRow.find('.qty-input');

            const fullId = selectedOption.val();
            if (fullId) {
                const parts = fullId.split('-');
                const type = parts[0];
                const id = parts[1];

                itemTypeInput.val(type);
                itemIdInput.val(id);

                const price = selectedOption.data('price');
                if (price !== undefined) {
                    priceInput.val(price);
                } else {
                    priceInput.val(0);
                }

                qtyInput.val(1);
            } else {
                priceInput.val(0);
                itemTypeInput.val('');
                itemIdInput.val('');
                qtyInput.val(1);
            }

            calculateItemSubtotalAndStock(itemRow);
        });

        // Event listener for global discount change
        $('#global_discount').on('input', calculateOverallTotal);

        // Initial calculation and stock check for existing items
        $('.item-row').each(function() {
            calculateItemSubtotalAndStock($(this));
        });
        calculateOverallTotal();
    });
</script>
@endpush

<style>
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