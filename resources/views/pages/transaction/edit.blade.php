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

                        {{-- Form action points to update method --}}
                        <form action="{{ route('transaction.update', $transaction->id) }}" method="POST" id="editTransactionForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT') {{-- Use PUT method for update --}}

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
                                                       value="{{ old('invoice_number', $transaction->invoice_number) }}" readonly>
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
                                                       value="{{ old('transaction_date', Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d')) }}" required>
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
                                                       id="customer_name" name="customer_name" value="{{ old('customer_name', $transaction->customer->name) }}"
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
                                            <div class="form-group">
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
                                            <div class="form-group">
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
                                            <div class="form-group">
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
                                            <div class="form-group">
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

                            {{-- Section 3: Item Transaksi --}}
                            <div class="section-card mb-4">
                                <div class="section-header">
                                    <i class="fas fa-shopping-cart text-warning me-2"></i>
                                    <h5 class="mb-0">Detail Item Transaksi</h5>
                                    <button type="button" class="btn btn-success btn ms-auto px-2" id="add-item">
                                        <i class="fas fa-plus me-1"></i>
                                        Tambah Item
                                    </button>
                                </div>
                                <div class="section-body">
                                    <div class="table-responsive">
                                        <div id="items-container">
                                            {{-- Loop through existing transaction items to populate rows --}}
                                            @if(isset($transaction) && $transaction->items->isNotEmpty())
                                                @foreach($transaction->items as $idx => $item)
                                                    <div class="item-row mb-3 p-3 border rounded bg-light" data-item-index="{{ $idx }}">
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
                                                                                    data-stock="{{ $sparepart->stock }}"
                                                                                    {{ ($item->item_type == 'sparepart' && $item->item_id == $sparepart->id) ? 'selected' : '' }}>
                                                                                {{ $sparepart->name }}
                                                                                @if($sparepart->isDiscountActive())
                                                                                    (Diskon {{ $sparepart->discount_percentage }}% - Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }})
                                                                                @else
                                                                                    (Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
                                                                                @endif
                                                                                (Stok: {{ $sparepart->stock }})
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
                                                {{-- Initial empty row for new transactions or if no items exist on edit --}}
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
                                                                        <option value="service-{{ $service->id }}" data-price="{{ $service->harga_standar }}">
                                                                            {{ $service->nama }} (Rp {{ number_format($service->harga_standar, 0, ',', '.') }})
                                                                        </option>
                                                                    @endforeach
                                                                </optgroup>
                                                                <optgroup label="🔩 Sparepart">
                                                                    @foreach ($spareparts as $sparepart)
                                                                        <option value="sparepart-{{ $sparepart->id }}"
                                                                                data-price="{{ $sparepart->final_selling_price }}"
                                                                                data-stock="{{ $sparepart->stock }}">
                                                                            {{ $sparepart->name }}
                                                                            @if($sparepart->isDiscountActive())
                                                                                (Diskon {{ $sparepart->discount_percentage }}% - Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }})
                                                                            @else
                                                                                (Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
                                                                            @endif
                                                                            (Stok: {{ $sparepart->stock }})
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
                                                           value="{{ old('global_discount', $transaction->discount_amount) }}" min="0" step="0.01" placeholder="0">
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
                                                    <option value="tunai" {{ old('payment_method', $transaction->payment_method) == 'tunai' ? 'selected' : '' }}>
                                                        Tunai
                                                    </option>
                                                    <option value="transfer bank" {{ old('payment_method', $transaction->payment_method) == 'transfer bank' ? 'selected' : '' }}>
                                                        Transfer Bank
                                                    </option>
                                                    <option value="kartu debit" {{ old('payment_method', $transaction->payment_method) == 'kartu debit' ? 'selected' : '' }}>
                                                        Kartu Debit
                                                    </option>
                                                    <option value="e-wallet" {{ old('payment_method', $transaction->payment_method) == 'e-wallet' ? 'selected' : '' }}>
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
                                                    <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>
                                                        Pending
                                                    </option>
                                                    <option value="completed" {{ old('status', $transaction->status) == 'completed' ? 'selected' : '' }}>
                                                        Selesai
                                                    </option>
                                                    <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>
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
    </style>

    {{-- JavaScript for dynamic item addition, price calculation, and stock check --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemIndex = 0; // Default for create page

            // Find existing item rows and set itemIndex accordingly
            const existingItemRows = document.querySelectorAll('.item-row');
            if (existingItemRows.length > 0) {
                // Find the maximum data-item-index among existing rows
                let maxIndex = 0;
                existingItemRows.forEach(row => {
                    const index = parseInt(row.dataset.itemIndex);
                    if (!isNaN(index) && index > maxIndex) {
                        maxIndex = index;
                    }
                });
                itemIndex = maxIndex; // Start new items from the next available index
            }


            // Function to format number to Rupiah
            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            }

            // Function to calculate and update item subtotal and overall totals
            function updateTotals() {
                let overallSubTotal = 0;
                document.querySelectorAll('.item-row').forEach(function(row) {
                    const price = parseFloat(row.querySelector('.price-input').value) || 0;
                    const quantity = parseInt(row.querySelector('.qty-input').value) || 0;
                    const itemSubtotal = price * quantity;
                    row.querySelector('.item-subtotal-display').value = formatRupiah(itemSubtotal);
                    overallSubTotal += itemSubtotal;
                });

                const globalDiscount = parseFloat(document.getElementById('global_discount').value) || 0;
                const finalTotal = overallSubTotal - globalDiscount;

                document.getElementById('overall_sub_total_display').value = formatRupiah(overallSubTotal);
                document.getElementById('final_total_display').value = formatRupiah(finalTotal);
                document.getElementById('final_total_hidden').value = finalTotal; // Hidden input for form submission
            }

            // Function to check stock for a given item row
            function checkStock(itemRow) {
                const itemSelect = itemRow.querySelector('.item-select');
                const qtyInput = itemRow.querySelector('.qty-input');
                const stockWarning = itemRow.querySelector('.stock-warning');
                // Get the currently selected option from the native select element
                const selectedNativeOption = itemSelect.options[itemSelect.selectedIndex];

                // Only check stock for spareparts
                if (selectedNativeOption && selectedNativeOption.value.startsWith('sparepart-')) {
                    // Use the data-stock attribute directly from the native option
                    const availableStock = parseInt(selectedNativeOption.dataset.stock) || 0;
                    const currentQuantity = parseInt(qtyInput.value) || 0;

                    if (currentQuantity > availableStock) {
                        stockWarning.textContent = `Stok tidak cukup! (Sisa: ${availableStock})`; // More informative message
                        stockWarning.style.display = 'block';
                        qtyInput.classList.add('is-invalid');
                        return false; // Stock insufficient
                    } else {
                        stockWarning.style.display = 'none';
                        qtyInput.classList.remove('is-invalid');
                        return true; // Stock sufficient
                    }
                } else {
                    stockWarning.style.display = 'none'; // Hide warning for services or unselected items
                    qtyInput.classList.remove('is-invalid');
                    return true;
                }
            }

            // Function to add event listeners to a new item row
            function setupItemRowListeners(row) {
                const itemSelect = row.querySelector('.item-select');
                const priceInput = row.querySelector('.price-input');
                const qtyInput = row.querySelector('.qty-input');
                const itemTypeInput = row.querySelector('.item-type-input');
                const itemIdInput = row.querySelector('.item-id-input');
                const removeButton = row.querySelector('.remove-item');

                // Initialize Select2 (if available)
                if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                    // Destroy existing Select2 instance before re-initializing on cloned elements
                    if (jQuery(itemSelect).data('select2')) {
                        jQuery(itemSelect).select2('destroy');
                    }
                    jQuery(itemSelect).select2();

                    // Use Select2's custom 'change' event for better compatibility
                    jQuery(itemSelect).on('change', function() {
                        // Get the selected option using jQuery's find and data method
                        const selectedOption = jQuery(this).find(':selected');
                        if (selectedOption.length > 0) {
                            const fullId = selectedOption.val();
                            const [type, id] = fullId.split('-');
                            itemTypeInput.value = type;
                            itemIdInput.value = id;

                            // Get data-price using jQuery's .data() method
                            const price = parseFloat(selectedOption.data('price')) || 0;
                            priceInput.value = price;
                        } else {
                            itemTypeInput.value = '';
                            itemIdInput.value = '';
                            priceInput.value = 0;
                        }
                        checkStock(row);
                        updateTotals();
                    });
                } else {
                    // Fallback for native select if Select2 is not loaded
                    itemSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        if (selectedOption) {
                            const fullId = selectedOption.value;
                            const [type, id] = fullId.split('-');
                            itemTypeInput.value = type;
                            itemIdInput.value = id;
                            const price = parseFloat(selectedOption.dataset.price) || 0;
                            priceInput.value = price;
                        } else {
                            itemTypeInput.value = '';
                            itemIdInput.value = '';
                            priceInput.value = 0;
                        }
                        checkStock(row);
                        updateTotals();
                    });
                }


                // Event listeners for quantity changes
                qtyInput.addEventListener('input', function() {
                    if (this.value < 1) this.value = 1; // Ensure quantity is at least 1
                    checkStock(row); // Check stock when quantity changes
                    updateTotals();
                });

                row.querySelectorAll('.btn-qty-minus, .btn-qty-plus').forEach(button => {
                    button.addEventListener('click', function() {
                        let currentQty = parseInt(qtyInput.value);
                        if (this.dataset.action === 'minus' && currentQty > 1) {
                            qtyInput.value = currentQty - 1;
                        } else if (this.dataset.action === 'plus') {
                            qtyInput.value = currentQty + 1;
                        }
                        qtyInput.dispatchEvent(new Event('input')); // Trigger input event to update totals and check stock
                    });
                });

                // Event listener for remove item button
                removeButton.addEventListener('click', function() {
                    row.remove();
                    updateTotals();
                });

                // Initial calculation and stock check for the item row
                // Trigger change event to populate price and run initial stock check
                // This is crucial for pre-filled forms (edit page)
                if (itemSelect.value) {
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                        // For Select2, trigger the change event after Select2 has been initialized
                        // A small delay might be needed to ensure Select2 is fully ready
                        setTimeout(() => {
                            jQuery(itemSelect).trigger('change');
                        }, 50); // Small delay
                    } else {
                        itemSelect.dispatchEvent(new Event('change'));
                    }
                }
            }

            // Add new item row
            document.getElementById('add-item').addEventListener('click', function() {
                itemIndex++; // Increment index for the new item
                const originalRow = document.querySelector('.item-row');
                const newItemRow = originalRow.cloneNode(true);

                // Update IDs and names for new row elements
                newItemRow.setAttribute('data-item-index', itemIndex);
                newItemRow.querySelectorAll('[id]').forEach(el => {
                    el.id = el.id.replace(/-\d+$/, `-${itemIndex}`);
                });
                newItemRow.querySelectorAll('[name]').forEach(el => {
                    el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
                });

                // Reset values for new row
                newItemRow.querySelector('.item-select').value = ''; // Clear selected option
                newItemRow.querySelector('.price-input').value = '0';
                newItemRow.querySelector('.qty-input').value = '1';
                newItemRow.querySelector('.item-subtotal-display').value = 'Rp 0';
                newItemRow.querySelector('.item-type-input').value = '';
                newItemRow.querySelector('.item-id-input').value = '';
                newItemRow.querySelector('.stock-warning').style.display = 'none'; // Hide warning for new item
                newItemRow.querySelector('.qty-input').classList.remove('is-invalid'); // Remove invalid state

                document.getElementById('items-container').appendChild(newItemRow);
                setupItemRowListeners(newItemRow); // Setup listeners for the new row
                updateTotals();
            });

            // Initial setup for ALL existing item rows on page load
            document.querySelectorAll('.item-row').forEach(row => {
                setupItemRowListeners(row);
            });

            // Event listener for global discount input
            document.getElementById('global_discount').addEventListener('input', function() {
                updateTotals();
            });

            // Form submission validation for stock
            document.getElementById('editTransactionForm').addEventListener('submit', function(event) {
                let allStockSufficient = true;
                document.querySelectorAll('.item-row').forEach(function(row) {
                    if (!checkStock(row)) {
                        allStockSufficient = false;
                    }
                });

                if (!allStockSufficient) {
                    event.preventDefault(); // Prevent form submission
                    alert('Mohon periksa kembali jumlah item. Ada stok yang tidak mencukupi.');
                }
            });

            // Initial total calculation on page load (after all item rows are set up)
            updateTotals();
        });
    </script>
@endsection
