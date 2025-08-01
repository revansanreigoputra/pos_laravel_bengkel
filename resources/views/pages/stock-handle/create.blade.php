@extends('layouts.master')
@section('title', 'Tambah Pembelian Sparepart')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>
                            Tambah Pembelian Baru
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('stock-handle.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Section 1: Invoice Information --}}
                            <div class="section-card mb-4">
                                <div class="section-header">
                                    <i class="fas fa-file-invoice text-primary me-2"></i>
                                    <h5 class="mb-0">Informasi Pembelian</h5>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
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
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="received_date" class="form-label fw-semibold">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    Tanggal Terima <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" class="form-control @error('received_date') is-invalid @enderror"
                                                       id="received_date" name="received_date" value="{{ old('received_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                                                @error('received_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="invoice_number" class="form-label fw-semibold">
                                                    <i class="fas fa-hashtag me-1"></i>
                                                    Nomor Invoice
                                                </label>
                                                <input type="text" class="form-control @error('invoice_number') is-invalid @enderror"
                                                       id="invoice_number" name="invoice_number"
                                                       value="{{ old('invoice_number', 'INV-PEM-' . \Carbon\Carbon::now()->format('ymd') . '-' . mt_rand(1000, 9999)) }}"
                                                       placeholder="Contoh: INV-202307001" readonly> {{-- Tambahkan readonly --}}
                                                @error('invoice_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="invoice_file" class="form-label fw-semibold">
                                                    <i class="fas fa-file-upload me-1"></i>
                                                    Upload Invoice Dari Supplier
                                                </label>
                                                <input type="file" class="form-control-file @error('invoice_file') is-invalid @enderror"
                                                       id="invoice_file" name="invoice_file">
                                                <small class="form-text text-muted">Format: PDF, JPG, JPEG, PNG (Maks. 2MB)</small>
                                                @error('invoice_file')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: Items --}}
                            <div class="section-card mb-4">
                                <div class="section-header">
                                    <i class="fas fa-shopping-cart text-warning me-2"></i>
                                    <h5 class="mb-0">Daftar Item Pembelian</h5>
                                </div>
                                <div class="section-body">
                                    <div class="form-group">
                                        <label for="main_sparepart_picker" class="form-label fw-semibold">
                                            <i class="fas fa-search me-1"></i>
                                            Cari Sparepart
                                        </label>
                                        <select id="main_sparepart_picker" class="form-select select2-init">
                                            <option value="">-- Cari atau Pilih Sparepart --</option>
                                            @foreach ($spareparts as $sparepart)
                                                <option value="{{ $sparepart->id }}" data-name="{{ $sparepart->name }}"
                                                    data-category="{{ $sparepart->category->name ?? 'N/A' }}"
                                                    data-default-price="{{ $sparepart->purchase_price ?? 0 }}">
                                                    {{ $sparepart->name }} ({{ $sparepart->code_part }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('spareparts')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="table-responsive mt-3">
                                        <table class="table table-bordered" id="sparepart_items_table">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nama Sparepart</th>
                                                    <th style="width: 150px;">Kuantitas</th>
                                                    <th style="width: 200px;">Harga Beli (Rp)</th>
                                                    <th style="width: 150px;">Subtotal (Rp)</th>
                                                    <th style="width: 80px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(old('spareparts'))
                                                    @foreach(old('spareparts') as $index => $oldSparepart)
                                                        <tr>
                                                            <td>{{ \App\Models\Sparepart::find($oldSparepart['sparepart_id'])->name ?? 'N/A' }}</td>
                                                            <td>
                                                                <input type="number" name="spareparts[{{ $index }}][quantity]"
                                                                       class="form-control item-quantity @error('spareparts.' . $index . '.quantity') is-invalid @enderror"
                                                                       value="{{ old('spareparts.' . $index . '.quantity', $oldSparepart['quantity']) }}" min="1" required
                                                                       data-item-id="{{ $oldSparepart['sparepart_id'] }}" data-item-index="{{ $index }}">
                                                                @error('spareparts.' . $index . '.quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                            </td>
                                                            <td>
                                                                <input type="number" name="spareparts[{{ $index }}][purchase_price]"
                                                                       class="form-control item-price @error('spareparts.' . $index . '.purchase_price') is-invalid @enderror"
                                                                       value="{{ old('spareparts.' . $index . '.purchase_price', $oldSparepart['purchase_price']) }}" min="0" required
                                                                       data-item-id="{{ $oldSparepart['sparepart_id'] }}" data-item-index="{{ $index }}">
                                                                @error('spareparts.' . $index . '.purchase_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                            </td>
                                                            <td>
                                                                <span class="item-subtotal">
                                                                    Rp {{ number_format($oldSparepart['quantity'] * $oldSparepart['purchase_price'], 0, ',', '.') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger btn-sm remove-item-btn"
                                                                        data-item-id="{{ $oldSparepart['sparepart_id'] }}" data-item-index="{{ $index }}">Hapus</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-end">Total Pembelian:</th>
                                                    <th id="grand_total_display">Rp 0</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 3: Additional Information --}}
                            <div class="section-card mb-4">
                                <div class="section-header">
                                    <i class="fas fa-info-circle text-info me-2"></i>
                                    <h5 class="mb-0">Informasi Tambahan</h5>
                                </div>
                                <div class="section-body">
                                    <div class="form-group">
                                        <label for="note" class="form-label fw-semibold">
                                            <i class="fas fa-sticky-note me-1"></i>
                                            Keterangan
                                        </label>
                                        <textarea class="form-control @error('note') is-invalid @enderror"
                                                 id="note" name="note" rows="3"
                                                 placeholder="Contoh: Pembelian rutin bulanan, satuan kuantitas per karton">{{ old('note') }}</textarea>
                                        @error('note')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div id="hidden_sparepart_inputs">
                                @if(old('spareparts'))
                                    @foreach(old('spareparts') as $index => $oldSparepart)
                                        <input type="hidden" name="spareparts[{{ $index }}][sparepart_id]" value="{{ $oldSparepart['sparepart_id'] }}" id="hidden_sparepart_id_{{ $index }}">
                                        <input type="hidden" name="spareparts[{{ $index }}][quantity]" value="{{ $oldSparepart['quantity'] }}" id="hidden_quantity_{{ $index }}">
                                        <input type="hidden" name="spareparts[{{ $index }}][purchase_price]" value="{{ $oldSparepart['purchase_price'] }}" id="hidden_price_{{ $index }}">
                                    @endforeach
                                @endif
                            </div>

                            {{-- Form Actions --}}
                            <div class="form-actions">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('stock-handle.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Simpan Pembelian
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

        /* Style untuk Select2 */
        .select2-container .select2-selection--single {
            height: 42px;
            border: 2px solid #e3e6f0;
            border-radius: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 15px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .select2-container--default .select2-results__option--highlighted {
            background-color: #4e73df;
            color: white;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #f8f9fa;
        }

        .select2-container--default .select2-results__option[aria-selected=true]:hover {
            background-color: #4e73df;
            color: white;
        }

        @media (max-width: 768px) {
            .section-body {
                padding: 15px;
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        let itemCounter = {{ old('spareparts') ? count(old('spareparts')) : 0 }};
        let currentItems = {};
        let grandTotal = 0;

        // Initialize currentItems from old input if validation failed
        @if(old('spareparts'))
            @foreach(old('spareparts') as $index => $oldSparepart)
                currentItems[{{ $oldSparepart['sparepart_id'] }}] = {
                    index: {{ $index }},
                    quantity: {{ $oldSparepart['quantity'] }},
                    price: {{ $oldSparepart['purchase_price'] }}
                };
            @endforeach
        @endif


        function updateGrandTotal() {
            grandTotal = 0;
            document.querySelectorAll('.item-subtotal').forEach(function(el) {
                // Remove 'Rp ', then dots, then replace comma with dot for proper float parsing
                grandTotal += parseFloat(el.textContent.replace('Rp ', '').replace(/\./g, '').replace(',', '.'));
            });
            document.getElementById('grand_total_display').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
        }

        function renderItemRow(sparepartId, sparepartName, defaultPrice, quantity = 1, purchasePrice = null) {
            if (currentItems[sparepartId]) {
                alert('Sparepart ini sudah ada dalam daftar. Silakan ubah kuantitasnya.');
                return;
            }

            quantity = parseFloat(quantity);
            purchasePrice = parseFloat(purchasePrice !== null ? purchasePrice : defaultPrice);
            let subtotal = quantity * purchasePrice;

            const tableBody = document.querySelector('#sparepart_items_table tbody');
            const newRow = tableBody.insertRow();
            newRow.setAttribute('data-item-id', sparepartId);

            newRow.innerHTML = `
                <td>${sparepartName}</td>
                <td>
                    <input type="number" name="spareparts[${itemCounter}][quantity]"
                           class="form-control item-quantity" value="${quantity}" min="1" required
                           data-item-id="${sparepartId}" data-item-index="${itemCounter}">
                </td>
                <td>
                    <input type="number" name="spareparts[${itemCounter}][purchase_price]"
                           class="form-control item-price" value="${purchasePrice}" min="0" required
                           data-item-id="${sparepartId}" data-item-index="${itemCounter}">
                </td>
                <td><span class="item-subtotal">Rp ${subtotal.toLocaleString('id-ID')}</span></td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item-btn"
                            data-item-id="${sparepartId}" data-item-index="${itemCounter}">Hapus</button>
                </td>
            `;

            const hiddenInputsDiv = document.getElementById('hidden_sparepart_inputs');
            hiddenInputsDiv.innerHTML += `
                <input type="hidden" name="spareparts[${itemCounter}][sparepart_id]" value="${sparepartId}" id="hidden_sparepart_id_${itemCounter}">
                <input type="hidden" name="spareparts[${itemCounter}][quantity]" value="${quantity}" id="hidden_quantity_${itemCounter}">
                <input type="hidden" name="spareparts[${itemCounter}][purchase_price]" value="${purchasePrice}" id="hidden_price_${itemCounter}">
            `;

            currentItems[sparepartId] = {
                index: itemCounter,
                quantity: quantity,
                price: purchasePrice
            };

            itemCounter++;
            updateGrandTotal();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2
            $('#main_sparepart_picker, #supplier_id').select2({
                placeholder: '-- Pilih --',
                allowClear: true,
                dropdownParent: $('body')
            });

            // Update grand total if there are old items
            updateGrandTotal();

            // Event listener for when a sparepart is selected
            $('#main_sparepart_picker').on('select2:select', function(e) {
                const data = e.params.data;
                const sparepartId = data.id;
                const sparepartName = data.element.dataset.name;
                const defaultPrice = parseFloat(data.element.dataset.defaultPrice);

                if (sparepartId) {
                    renderItemRow(sparepartId, sparepartName, defaultPrice);
                    $(this).val(null).trigger('change'); // Clear Select2 after selection
                }
            });

            // Event listener for quantity or price changes
            document.querySelector('#sparepart_items_table tbody').addEventListener('input', function(e) {
                if (e.target.classList.contains('item-quantity') || e.target.classList.contains('item-price')) {
                    const row = e.target.closest('tr');
                    const quantityInput = row.querySelector('.item-quantity');
                    const priceInput = row.querySelector('.item-price');

                    let quantity = parseFloat(quantityInput.value) || 0;
                    let price = parseFloat(priceInput.value) || 0;

                    const subtotal = quantity * price;
                    row.querySelector('.item-subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');

                    const itemId = e.target.dataset.itemId;
                    const itemIndex = e.target.dataset.itemIndex;

                    // Update hidden inputs and currentItems object
                    document.getElementById(`hidden_quantity_${itemIndex}`).value = quantity;
                    document.getElementById(`hidden_price_${itemIndex}`).value = price;

                    // Update currentItems for the selected item only (important for 'old' data handling)
                    if (currentItems[itemId]) {
                        currentItems[itemId].quantity = quantity;
                        currentItems[itemId].price = price;
                    }


                    updateGrandTotal();
                }
            });

            // Event listener for removing an item row
            document.querySelector('#sparepart_items_table tbody').addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-item-btn')) {
                    const rowToRemove = e.target.closest('tr');
                    const sparepartId = e.target.dataset.itemId;
                    const itemIndex = e.target.dataset.itemIndex;

                    // Remove associated hidden inputs
                    document.getElementById(`hidden_sparepart_id_${itemIndex}`).remove();
                    const hiddenQuantityInput = document.getElementById(`hidden_quantity_${itemIndex}`);
                    if (hiddenQuantityInput) hiddenQuantityInput.remove();
                    const hiddenPriceInput = document.getElementById(`hidden_price_${itemIndex}`);
                    if (hiddenPriceInput) hiddenPriceInput.remove();

                    rowToRemove.remove();
                    delete currentItems[sparepartId]; // Remove from tracking object
                    updateGrandTotal();
                }
            });
        });
    </script>
@endpush