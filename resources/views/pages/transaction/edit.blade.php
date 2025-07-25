@extends('layouts.master') {{-- Pastikan Anda menggunakan layout master yang sama --}}

@section('title', 'Edit Transaksi: ' . $transaction->customer_name)

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Edit Transaksi</h4>
        </div>
        <div class="card-body">
            {{-- Form starts here --}}
            <form action="{{ route('transaction.update', $transaction->id) }}" method="POST" id="editTransactionForm">
                @csrf
                @method('PUT')

                {{-- Customer Information --}}
                <div class="mb-3">
                    <label for="invoice_number" class="form-label">Nomor Invoice</label>
                    <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $transaction->invoice_number) }}" required>
                    @error('invoice_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    {{-- kiri --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Nama Pelanggan</label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', $transaction->customer_name) }}" required>
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="vehicle_model" class="form-label">Merk/Model Kendaraan</label>
                            <input type="text" class="form-control @error('vehicle_model') is-invalid @enderror" id="vehicle_model" name="vehicle_model" value="{{ old('vehicle_model', $transaction->vehicle_model) }}" placeholder="Contoh: Toyota Avanza, Honda Vario">
                            @error('vehicle_model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                
                    {{-- kanan --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="vehicle_number" class="form-label">Nomor Kendaraan</label>
                            <input type="text" class="form-control @error('vehicle_number') is-invalid @enderror" id="vehicle_number" name="vehicle_number" value="{{ old('vehicle_number', $transaction->vehicle_number) }}" required>
                            @error('vehicle_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="transaction_date" class="form-label">Tanggal Transaksi</label>
                            <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required>
                            @error('transaction_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr>
                <h5>Detail Item Transaksi</h5>
                <div id="items-container">
                    {{-- Loop through existing transaction items to pre-fill --}}
                    @forelse ($transaction->items as $index => $item)
                        <div class="row mb-2 item-row align-items-end" data-item-index="{{ $index }}">
                            <div class="col-md-5">
                                <label for="item-{{ $index }}" class="form-label">Item</label>
                                <select class="form-select item-select @error('items.' . $index . '.item_full_id') is-invalid @enderror" name="items[{{ $index }}][item_full_id]" id="item-{{ $index }}" required>
                                    <option value="">Pilih Item</option>
                                    <optgroup label="Layanan">
                                        @foreach ($services as $service)
                                            <option value="service-{{ $service->id }}" data-price="{{ $service->harga_standar }}" {{ ($item->item_type == 'service' && $item->item_id == $service->id) ? 'selected' : '' }}>
                                                {{ $service->nama }} (Rp {{ number_format($service->harga_standar, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Sparepart">
                                        @foreach ($spareparts as $sparepart)
                                            <option value="sparepart-{{ $sparepart->id }}" data-price="{{ $sparepart->final_selling_price }}" {{ ($item->item_type == 'sparepart' && $item->item_id == $sparepart->id) ? 'selected' : '' }}>
                                                {{ $sparepart->name }}
                                                @if($sparepart->isDiscountActive())
                                                    (Normal: Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
                                                    Diskon {{ $sparepart->discount_percentage }}% -
                                                    Final: Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }}
                                                @else
                                                    (Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                @error('items.' . $index . '.item_full_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                {{-- Hidden inputs to send item_type and item_id separately to the backend --}}
                                <input type="hidden" class="item-type-input" name="items[{{ $index }}][item_type]" value="{{ $item->item_type }}">
                                <input type="hidden" class="item-id-input" name="items[{{ $index }}][item_id]" value="{{ $item->item_id }}">
                            </div>
                            <div class="col-md-3">
                                <label for="price-{{ $index }}" class="form-label">Harga</label>
                                <input type="number" class="form-control price-input @error('items.' . $index . '.price') is-invalid @enderror" name="items[{{ $index }}][price]" id="price-{{ $index }}" value="{{ old('items.' . $index . '.price', $item->price) }}" step="0.01" required>
                                @error('items.' . $index . '.price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="qty-{{ $index }}" class="form-label">Qty</label>
                                <input type="number" class="form-control qty-input @error('items.' . $index . '.quantity') is-invalid @enderror" name="items[{{ $index }}][quantity]" id="qty-{{ $index }}" value="{{ old('items.' . $index . '.quantity', $item->quantity) }}" min="1" required>
                                @error('items.' . $index . '.quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item w-100">Hapus</button>
                            </div>
                        </div>
                    @empty
                        {{-- If no items exist, render a single empty item row like in create.blade.php --}}
                        <div class="row mb-2 item-row align-items-end" data-item-index="0">
                            <div class="col-md-5">
                                <label for="item-0" class="form-label">Item</label>
                                <select class="form-select item-select" name="items[0][item_full_id]" id="item-0" required>
                                    <option value="">Pilih Item</option>
                                    <optgroup label="Layanan">
                                        @foreach ($services as $service)
                                            <option value="service-{{ $service->id }}" data-price="{{ $service->harga_standar }}">
                                                {{ $service->nama }} (Rp {{ number_format($service->harga_standar, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Sparepart">
                                        @foreach ($spareparts as $sparepart)
                                            <option value="sparepart-{{ $sparepart->id }}" data-price="{{ $sparepart->final_selling_price }}">
                                                {{ $sparepart->name }}
                                                @if($sparepart->isDiscountActive())
                                                    (Normal: Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
                                                    Diskon {{ $sparepart->discount_percentage }}% -
                                                    Final: Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }})
                                                @else
                                                    (Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <input type="hidden" class="item-type-input" name="items[0][item_type]">
                                <input type="hidden" class="item-id-input" name="items[0][item_id]">
                            </div>
                            <div class="col-md-3">
                                <label for="price-0" class="form-label">Harga</label>
                                <input type="number" class="form-control price-input" name="items[0][price]" id="price-0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <label for="qty-0" class="form-label">Qty</label>
                                <input type="number" class="form-control qty-input" name="items[0][quantity]" id="qty-0" value="1" min="1" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item w-100">Hapus</button>
                            </div>
                        </div>
                    @endforelse
                </div>
                <button type="button" class="btn btn-secondary mt-3" id="add-item">Tambah Item</button>

                <hr class="mt-4 mb-4">

                {{-- Totals and Discount --}}
                <div class="mb-3">
                    <label for="sub_total_display" class="form-label">Sub Total</label>
                    <input type="text" class="form-control" id="sub_total_display" value="Rp 0" readonly>
                </div>

                <div class="mb-3">
                    <label for="global_discount" class="form-label">Diskon Transaksi (Rp)</label>
                    <input type="number" class="form-control @error('global_discount') is-invalid @enderror" id="global_discount" name="global_discount" value="{{ old('global_discount', $transaction->discount_amount) }}" min="0" step="0.01">
                    @error('global_discount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="final_total_display" class="form-label">Total Akhir</label>
                    <input type="text" class="form-control" id="final_total_display" value="Rp 0" readonly>
                    <input type="hidden" id="final_total_hidden" name="total_price" value="{{ old('total_price', $transaction->total_price) }}"> {{-- Hidden input to send final total --}}
                </div>

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Metode Pembayaran</label>
                    <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="tunai" {{ old('payment_method', $transaction->payment_method) == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer bank" {{ old('payment_method', $transaction->payment_method) == 'transfer bank' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="kartu debit" {{ old('payment_method', $transaction->payment_method) == 'kartu debit' ? 'selected' : '' }}>Kartu Debit</option>
                        <option value="e-wallet" {{ old('payment_method', $transaction->payment_method) == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                        </select>
                    @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="proof_of_transfer_file" class="form-label">Bukti Transfer (Opsional)</label>
                    <input type="file" class="form-control @error('proof_of_transfer_file') is-invalid @enderror" id="proof_of_transfer_file" name="proof_of_transfer_file" accept="image/*,application/pdf">
                    @error('proof_of_transfer_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Format: JPG, PNG, PDF. Max 2MB.</small>
                    @if ($transaction->proof_of_transfer_url)
                        <p class="mt-2">File saat ini: <a href="{{ asset($transaction->proof_of_transfer_url) }}" target="_blank">Lihat Bukti</a></p>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="clear_proof_of_transfer" id="clear_proof_of_transfer">
                            <label class="form-check-label" for="clear_proof_of_transfer">Hapus Bukti Transfer Saat Ini</label>
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status Transaksi</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ old('status', $transaction->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ old('status', $transaction->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Form Actions --}}
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('transaction.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
            {{-- Form ends here --}}
        </div>
    </div>
@endsection

@push('addon-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize itemIndex based on the number of existing items
        let itemIndex = document.querySelectorAll('.item-row').length;

        const itemsContainer = document.getElementById('items-container');
        const addItemButton = document.getElementById('add-item');
        const globalDiscountInput = document.getElementById('global_discount');
        const subTotalDisplay = document.getElementById('sub_total_display');
        const finalTotalDisplay = document.getElementById('final_total_display');
        const finalTotalHidden = document.getElementById('final_total_hidden');
        const form = document.getElementById('editTransactionForm'); // Changed form ID

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        function calculateTotals() {
            let subTotal = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                const priceInput = row.querySelector('.price-input');
                const qtyInput = row.querySelector('.qty-input');
                const price = priceInput ? parseFloat(priceInput.value) : 0;
                const qty = qtyInput ? parseInt(qtyInput.value) : 0;

                if (!isNaN(price) && !isNaN(qty)) {
                    subTotal += (price * qty);
                }
            });

            const globalDiscount = parseFloat(globalDiscountInput.value) || 0;
            let finalTotal = subTotal - globalDiscount;
            if (finalTotal < 0) finalTotal = 0;

            subTotalDisplay.value = formatRupiah(subTotal);
            finalTotalDisplay.value = formatRupiah(finalTotal);
            finalTotalHidden.value = finalTotal.toFixed(2); // Crucial for backend

            console.log("Total Price hidden field set to:", finalTotalHidden.value);
        }

        function addEventListenersToRow(row) { // Renamed from addEventListenersToNewRow for clarity
            const itemSelect = row.querySelector('.item-select');
            const priceInput = row.querySelector('.price-input');
            const qtyInput = row.querySelector('.qty-input');
            const removeItemButton = row.querySelector('.remove-item');
            const itemTypeInput = row.querySelector('.item-type-input');
            const itemIdInput = row.querySelector('.item-id-input');

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

            if (qtyInput) {
                qtyInput.addEventListener('input', function() {
                    if (this.value < 1) {
                        this.value = 1; // Ensure quantity is always at least 1
                    }
                    calculateTotals();
                });
            }

            if (removeItemButton) {
                removeItemButton.addEventListener('click', function () {
                    const currentRows = itemsContainer.querySelectorAll('.item-row');
                    if (currentRows.length > 1) {
                        row.remove();
                    } else {
                        // If it's the last row, just reset its values
                        const selectEl = row.querySelector('.item-select');
                        if (selectEl) selectEl.selectedIndex = 0; // Reset to "Pilih Item"
                        const priceIn = row.querySelector('.price-input');
                        if (priceIn) priceIn.value = '';
                        const qtyIn = row.querySelector('.qty-input');
                        if (qtyIn) qtyIn.value = 1; // Reset quantity to 1
                        const typeIn = row.querySelector('.item-type-input');
                        if (typeIn) typeIn.value = '';
                        const idIn = row.querySelector('.item-id-input');
                        if (idIn) idIn.value = '';
                    }
                    calculateTotals();
                });
            }
            // No need to set initial price/type/id here for pre-existing rows
            // as they are already populated by Laravel. This is primarily for newly added rows.
        }

        addItemButton.addEventListener('click', function () {
            // Create a new row by cloning the first existing row (or a template)
            const firstRowTemplate = itemsContainer.querySelector('.item-row');
            if (!firstRowTemplate) {
                console.error("No item-row template found to clone. Add at least one default item row in your HTML.");
                return;
            }

            const newRow = firstRowTemplate.cloneNode(true);
            newRow.setAttribute('data-item-index', itemIndex);

            newRow.querySelectorAll('input, select, label').forEach(el => {
                // Reset IDs and names
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
                    el.selectedIndex = 0; // Reset select to "Pilih Item"
                } else if (el.type === 'number') {
                    el.value = 1; // Reset quantity to 1
                } else if (el.type === 'text') {
                    el.value = '';
                }
            });

            itemsContainer.appendChild(newRow);
            addEventListenersToRow(newRow); // Attach listeners to the new row
            itemIndex++; // Increment for the next item
            calculateTotals();
        });

        // Attach event listeners to all existing item rows on page load
        document.querySelectorAll('.item-row').forEach(row => {
            addEventListenersToRow(row);
        });

        // Event listener for global discount input
        globalDiscountInput.addEventListener('input', calculateTotals);

        // Calculate totals initially when the page loads
        calculateTotals();

        // Ensure totals are calculated again just before form submission
        form.addEventListener('submit', function (e) {
            calculateTotals(); 

            // Basic validation: ensure total is a valid number and not negative
            const total = parseFloat(finalTotalHidden.value);
            if (isNaN(total) || total < 0) {
                e.preventDefault();
                alert('Total harga tidak valid. Periksa kembali data item dan diskon.');
            }
        });
    });
</script>
@endpush