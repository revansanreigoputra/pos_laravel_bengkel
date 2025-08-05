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
                        {{-- Menampilkan pesan sukses atau error dari session --}}
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

                        <form action="{{ route('transaction.store') }}" method="POST" id="createTransactionForm" enctype="multipart/form-data">
                            @csrf

                            {{-- Section 1: Informasi Invoice --}}
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
                                                <input type="text" class="form-control @error('invoice_number') is-invalid @enderror"
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
                                                <input type="date" class="form-control @error('transaction_date') is-invalid @enderror"
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

                            {{-- Section 2: Informasi Pelanggan --}}
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
                                                <label for="customer_phone" class="form-label fw-semibold">
                                                    <i class="fas fa-phone me-1"></i>
                                                    Nomor Telepon Pelanggan <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror"
                                                       id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}"
                                                       placeholder="Contoh: 081234567890" required>
                                                @error('customer_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customer_email" class="form-label fw-semibold">
                                                    <i class="fas fa-envelope me-1"></i>
                                                    Email Pelanggan (Opsional)
                                                </label>
                                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror"
                                                       id="customer_email" name="customer_email" value="{{ old('customer_email') }}"
                                                       placeholder="Masukkan email pelanggan">
                                                @error('customer_email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customer_address" class="form-label fw-semibold">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    Alamat Pelanggan (Opsional)
                                                </label>
                                                <textarea class="form-control @error('customer_address') is-invalid @enderror"
                                                          id="customer_address" name="customer_address" rows="1"
                                                          placeholder="Masukkan alamat lengkap pelanggan">{{ old('customer_address') }}</textarea>
                                                @error('customer_address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="vehicle_number" class="form-label fw-semibold">
                                                    <i class="fas fa-id-card me-1"></i>
                                                    Nomor Kendaraan
                                                </label>
                                                <input type="text" class="form-control @error('vehicle_number') is-invalid @enderror"
                                                       id="vehicle_number" name="vehicle_number" value="{{ old('vehicle_number') }}"
                                                       placeholder="Contoh: B 1234 XYZ">
                                                @error('vehicle_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
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

                            {{-- Section 3: Item Transaksi --}}
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
                                            {{-- Item Row Template (akan di-clone atau dibuat baru oleh JS) --}}
                                            <div class="item-row mb-3 p-3 border rounded bg-light" data-item-index="0">
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
                                                                    <option value="service-{{ $service->id }}"
                                                                            data-price="{{ $service->harga_standar }}">
                                                                        {{ $service->nama }} (Rp {{ number_format($service->harga_standar, 0, ',', '.') }})
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                            <optgroup label="🔩 Sparepart">
                                                                @foreach ($spareparts as $sparepart)
                                                                    <option value="sparepart-{{ $sparepart->id }}"
                                                                            data-price="{{ $sparepart->final_selling_price }}"
                                                                            data-available-stock="{{ $sparepart->available_stock }}"> {{-- UBAH INI --}}
                                                                        {{ $sparepart->name }}
                                                                        @if($sparepart->isDiscountActive())
                                                                            (Diskon {{ $sparepart->discount_percentage }}% - Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }})
                                                                        @else
                                                                            (Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
                                                                        @endif
                                                                        (Stok: {{ $sparepart->available_stock }}) {{-- UBAH INI --}}
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
                                                                <i class="fas fa-minus text-dark">-</i>
                                                            </button>
                                                            <input type="number" class="form-control qty-input @error('items.0.quantity') is-invalid @enderror"
                                                                   name="items[0][quantity]" id="qty-0" value="1" required min="1">
                                                            <button class="btn btn-outline-secondary btn-qty-plus" type="button" data-action="plus">
                                                                <i class="fas fa-plus text-dark">+</i>
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
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 4: Ringkasan Pembayaran --}}
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
                                                    <input type="text" class="form-control bg-light"
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
                                                    <input type="text" class="form-control fw-bold text-primary border-primary"
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
                                    <button type="submit" class="btn btn-primary btn" id="submitTransactionBtn">
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

    {{-- Custom Styles --}}
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
            /* Your existing styles */
        }
    </style>
@endsection

@push('addon-script')
<script>
    let itemIndex = 0; // Mulai dari 0 untuk item pertama

    // Fungsi untuk menginisialisasi Select2 pada elemen baru
    function initializeSelect2(element) {
        $(element).select2({
            theme: "bootstrap-5",
            width: $(element).data('width') ? $(element).data('width') : ($(element).hasClass('w-100') ? '100%' : 'style'),
            placeholder: $(element).data('placeholder') || '-- Pilih Item --',
            allowClear: Boolean($(element).data('allow-clear')),
        });
    }

    // Fungsi untuk menghitung ulang subtotal item
    function calculateItemSubtotal(itemRow) {
        const price = parseFloat(itemRow.find('.price-input').val()) || 0;
        const qty = parseInt(itemRow.find('.qty-input').val()) || 0;
        const subtotal = price * qty;
        itemRow.find('.item-subtotal-display').val('Rp ' + subtotal.toLocaleString('id-ID'));
        calculateOverallTotal();
    }

    // Fungsi untuk menghitung ulang total keseluruhan
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

    // Fungsi untuk menambahkan item baru
    function addItemRow() {
        itemIndex++;
        const originalRow = $('#items-container').find('.item-row[data-item-index="0"]').first();
        const newRow = originalRow.clone(true);

        newRow.attr('data-item-index', itemIndex);

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

        newRow.find('label[for^="item-0"], label[for^="price-0"], label[for^="qty-0"], label[for^="item_subtotal_display-0"]').each(function() {
            const oldFor = $(this).attr('for');
            if (oldFor) {
                const newFor = oldFor.replace(/-\d+$/, `-${itemIndex}`);
                $(this).attr('for', newFor);
            }
        });

        newRow.find('input').val('');
        newRow.find('.qty-input').val(1);
        newRow.find('.price-input').val(0);
        newRow.find('.item-subtotal-display').val('Rp 0');
        newRow.find('.stock-warning').hide(); // Sembunyikan peringatan stok

        newRow.find('.item-select').removeClass('select2-hidden-accessible').next('.select2-container').remove();
        initializeSelect2(newRow.find('.item-select'));

        $('#items-container').append(newRow);
        calculateOverallTotal();
    }

    $(document).ready(function() {
        // Inisialisasi Select2 untuk item pertama
        initializeSelect2($('#item-0'));

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

        // Event listener untuk perubahan harga atau kuantitas (delegasi event)
        $('#items-container').on('input', '.price-input, .qty-input', function() {
            const itemRow = $(this).closest('.item-row');
            const selectedOption = itemRow.find('.item-select option:selected');
            const itemType = selectedOption.parent().attr('label'); // '🔧 Layanan Service' or '🔩 Sparepart'
            const currentQty = parseInt($(this).val()) || 0;
            const availableStock = parseInt(selectedOption.data('available-stock')) || 0;
            const stockWarning = itemRow.find('.stock-warning');

            if (itemType === '🔩 Sparepart' && currentQty > availableStock) {
                stockWarning.text(`Stok tidak cukup! Tersedia: ${availableStock}`).show();
                $(this).addClass('is-invalid');
                $('#submitTransactionBtn').prop('disabled', true); // Nonaktifkan tombol submit
            } else {
                stockWarning.hide();
                $(this).removeClass('is-invalid');
                // Cek semua item, jika tidak ada peringatan, aktifkan tombol submit
                if ($('.stock-warning:visible').length === 0) {
                    $('#submitTransactionBtn').prop('disabled', false);
                }
            }

            calculateItemSubtotal(itemRow);
        });

        // Event listener untuk tombol plus/minus kuantitas (delegasi event)
        $('#items-container').on('click', '.btn-qty-minus', function() {
            const qtyInput = $(this).siblings('.qty-input');
            let currentVal = parseInt(qtyInput.val());
            if (currentVal > 1) {
                qtyInput.val(currentVal - 1).trigger('input'); // Trigger input event for validation
            }
        });

        $('#items-container').on('click', '.btn-qty-plus', function() {
            const qtyInput = $(this).siblings('.qty-input');
            let currentVal = parseInt(qtyInput.val());
            qtyInput.val(currentVal + 1).trigger('input'); // Trigger input event for validation
        });

        // Event listener untuk perubahan Select2 item
        $('#items-container').on('change', '.item-select', function() {
            const selectedOption = $(this).find('option:selected');
            const itemRow = $(this).closest('.item-row');
            const priceInput = itemRow.find('.price-input');
            const qtyInput = itemRow.find('.qty-input');
            const itemTypeInput = itemRow.find('.item-type-input');
            const itemIdInput = itemRow.find('.item-id-input');
            const stockWarning = itemRow.find('.stock-warning');

            const fullId = selectedOption.val();
            if (fullId) {
                const [type, id] = fullId.split('-');
                itemTypeInput.val(type);
                itemIdInput.val(id);
            } else {
                itemTypeInput.val('');
                itemIdInput.val('');
            }

            const price = selectedOption.data('price');
            if (price !== undefined) {
                priceInput.val(price);
            } else {
                priceInput.val(0);
            }

            // Reset quantity to 1 and re-validate stock
            qtyInput.val(1).trigger('input'); 
            
            // Sembunyikan peringatan stok saat item baru dipilih
            stockWarning.hide();
            qtyInput.removeClass('is-invalid');
            // Cek semua item, jika tidak ada peringatan, aktifkan tombol submit
            if ($('.stock-warning:visible').length === 0) {
                $('#submitTransactionBtn').prop('disabled', false);
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