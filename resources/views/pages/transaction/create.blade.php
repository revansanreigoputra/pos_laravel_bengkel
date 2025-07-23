@extends('layouts.master')

@section('title', 'Tambah Transaksi Baru')

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>Tambah Transaksi Baru</h4>
        </div>
        <div class="card-body">
            {{-- Form starts here --}}
            <form action="{{ route('transaction.store') }}" method="POST" id="createTransactionForm">
                @csrf

                {{-- Customer Information --}}
                <div class="mb-3">
                    <label for="customer_name" class="form-label">Nama Pelanggan</label>
                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                    @error('customer_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="vehicle_number" class="form-label">Nomor Kendaraan</label>
                    <input type="text" class="form-control @error('vehicle_number') is-invalid @enderror" id="vehicle_number" name="vehicle_number" value="{{ old('vehicle_number') }}" required>
                    @error('vehicle_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="transaction_date" class="form-label">Tanggal Transaksi</label>
                    <input type="date" class="form-control @error('transaction_date') is-invalid @enderror" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                    @error('transaction_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr>
                <h5>Detail Item Transaksi</h5>
                <div id="items-container">
                    {{-- Initial item row. This structure will be cloned. --}}
                    <div class="row mb-2 item-row align-items-end" data-item-index="0">
                        <div class="col-md-5">
                            <label for="item-0" class="form-label">Item</label>
                            <select class="form-select item-select @error('items.0.item_full_id') is-invalid @enderror" name="items[0][item_full_id]" id="item-0" required>
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
                                                Final: Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }}
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
                            {{-- Hidden inputs to send item_type and item_id separately to the backend --}}
                            <input type="hidden" class="item-type-input" name="items[0][item_type]">
                            <input type="hidden" class="item-id-input" name="items[0][item_id]">
                        </div>
                        <div class="col-md-3">
                            <label for="price-0" class="form-label">Harga</label>
                            <input type="number" class="form-control price-input @error('items.0.price') is-invalid @enderror" name="items[0][price]" id="price-0" step="0.01" required>
                            @error('items.0.price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2">
                            <label for="qty-0" class="form-label">Qty</label>
                            <input type="number" class="form-control qty-input @error('items.0.quantity') is-invalid @enderror" name="items[0][quantity]" id="qty-0" value="1" min="1" required>
                            @error('items.0.quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-item w-100">Hapus</button>
                        </div>
                    </div>
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
                    <input type="number" class="form-control @error('global_discount') is-invalid @enderror" id="global_discount" name="global_discount" value="{{ old('global_discount', 0) }}" min="0" step="0.01">
                    @error('global_discount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="final_total_display" class="form-label">Total Akhir</label>
                    <input type="text" class="form-control" id="final_total_display" value="Rp 0" readonly>
                    <input type="hidden" id="final_total_hidden" name="total_price"> {{-- Hidden input to send final total --}}
                </div>

                {{-- Form Actions --}}
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('transaction.index') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
            {{-- Form ends here --}}
        </div>
    </div>
@endsection

@push('addon-script') {{-- Use addon-script as per your master layout --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = 1; // Start from 1 for new items, as the first one is already at index 0

        const itemsContainer = document.getElementById('items-container');
        const addItemButton = document.getElementById('add-item');
        const globalDiscountInput = document.getElementById('global_discount');
        const subTotalDisplay = document.getElementById('sub_total_display');
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

        function addEventListenersToNewRow(row) {
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
                    // Make sure not to remove the last item row
                    const currentRows = itemsContainer.querySelectorAll('.item-row');
                    if (currentRows.length > 1) {
                        row.remove();
                    } else {
                        // If it's the last row, just reset its values
                        const selectEl = row.querySelector('.item-select');
                        if (selectEl) selectEl.selectedIndex = 0;
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

            // Initialize price and hidden fields for pre-selected items (important if `old()` values are present)
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
            // Get the HTML content of the first item row to clone it
            const firstRowTemplate = itemsContainer.querySelector('.item-row');
            if (!firstRowTemplate) {
                console.error("No item-row template found to clone.");
                return;
            }

            const newRow = firstRowTemplate.cloneNode(true);
            newRow.setAttribute('data-item-index', itemIndex); // Update data-item-index

            // Update names and IDs for cloned elements to be unique and array-friendly
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
                    el.selectedIndex = 0; // Reset select to "Pilih Item"
                } else if (el.type === 'number') {
                    el.value = 1; // Reset quantity to 1
                } else if (el.type === 'text') {
                    el.value = '';
                }
            });

            itemsContainer.appendChild(newRow);
            addEventListenersToNewRow(newRow); // Attach listeners to the new row
            itemIndex++; // Increment for the next item
            calculateTotals();
        });

        // Initialize listeners for the first item row that's present on page load
        const initialItemRow = document.querySelector('.item-row[data-item-index="0"]');
        if (initialItemRow) {
            addEventListenersToNewRow(initialItemRow);
        }

        // Event listener for global discount
        globalDiscountInput.addEventListener('input', calculateTotals);

        // Ensure totals are calculated on page load and before form submission
        calculateTotals();
        form.addEventListener('submit', function (e) {
            calculateTotals(); // Recalculate just before submission to catch last-minute changes

            const total = parseFloat(finalTotalHidden.value);
            // Basic validation: ensure total is a valid number and not negative
            if (isNaN(total) || total < 0) {
                e.preventDefault();
                alert('Total harga tidak valid. Periksa kembali data item dan diskon.');
            }
        });
    });
</script>
@endpush