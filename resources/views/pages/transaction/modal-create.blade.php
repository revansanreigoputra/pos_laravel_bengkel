{{-- This is a simplified example of modal-create.blade.php --}}
{{-- You need to ensure your actual modal structure reflects this --}}

<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-labelledby="createTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTransactionModalLabel">Tambah Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transaction.store') }}" method="POST" id="createTransactionForm"> {{-- Added ID for easier JS targeting --}}
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="vehicle_number" class="form-label">Nomor Kendaraan</label>
                        <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" required>
                    </div>

                    <h6>Detail Item:</h6>
                    <div id="items-container">
                        <div class="row mb-2 item-row align-items-end" data-item-index="0"> {{-- Added data-item-index --}}
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
                                                    Final: Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }}
                                                @else
                                                    (Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                {{-- Hidden inputs to send item_type and item_id separately to the backend --}}
                                <input type="hidden" class="item-type-input" name="items[0][item_type]">
                                <input type="hidden" class="item-id-input" name="items[0][item_id]">
                            </div>
                            <div class="col-md-3">
                                <label for="price-0" class="form-label">Harga</label>
                                <input type="number" class="form-control price-input" name="items[0][price]" id="price-0" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <label for="qty-0" class="form-label">Qty</label>
                                <input type="number" class="form-control qty-input" name="items[0][quantity]" id="qty-0" value="1" min="1" required> {{-- Added qty-input class --}}
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item w-100">Hapus</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary mt-3" id="add-item">Tambah Item</button>

                    <hr class="mt-4 mb-4">

                    <div class="mb-3">
                        <label for="sub_total_display" class="form-label">Sub Total</label>
                        <input type="text" class="form-control" id="sub_total_display" value="Rp 0" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="global_discount" class="form-label">Diskon Transaksi (Rp)</label>
                        <input type="number" class="form-control" id="global_discount" name="global_discount" value="0" min="0" step="0.01">
                    </div>

                    <div class="mb-3">
                        <label for="final_total_display" class="form-label">Total Akhir</label>
                        <input type="text" class="form-control" id="final_total_display" value="Rp 0" readonly>
                        <input type="hidden" id="final_total_hidden" name="total_price"> {{-- Hidden input to send final total to backend --}}
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('addon-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = 1;

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
            finalTotalHidden.value = finalTotal.toFixed(2); // Harus ada ini!

            console.log("Total Price set:", finalTotalHidden.value);
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
                qtyInput.addEventListener('input', calculateTotals);
            }

            if (removeItemButton) {
                removeItemButton.addEventListener('click', function () {
                    row.remove();
                    calculateTotals();
                });
            }
        }

        addItemButton.addEventListener('click', function () {
            const newItemRowHtml = `
                <div class="row mb-2 item-row align-items-end" data-item-index="${itemIndex}">
                    <div class="col-md-5">
                        <label for="item-${itemIndex}" class="form-label">Item</label>
                        <select class="form-select item-select" name="items[${itemIndex}][item_full_id]" id="item-${itemIndex}" required>
                            <option value="">Pilih Item</option>
                            @foreach ($services as $service)
                                <option value="service-{{ $service->id }}" data-price="{{ $service->harga_standar }}">
                                    {{ $service->nama }} (Rp {{ number_format($service->harga_standar, 0, ',', '.') }})
                                </option>
                            @endforeach
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
                        </select>
                        <input type="hidden" class="item-type-input" name="items[${itemIndex}][item_type]">
                        <input type="hidden" class="item-id-input" name="items[${itemIndex}][item_id]">
                    </div>
                    <div class="col-md-3">
                        <label for="price-${itemIndex}" class="form-label">Harga</label>
                        <input type="number" class="form-control price-input" name="items[${itemIndex}][price]" id="price-${itemIndex}" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <label for="qty-${itemIndex}" class="form-label">Qty</label>
                        <input type="number" class="form-control qty-input" name="items[${itemIndex}][quantity]" id="qty-${itemIndex}" value="1" min="1" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-item w-100">Hapus</button>
                    </div>
                </div>
            `;
            itemsContainer.insertAdjacentHTML('beforeend', newItemRowHtml);
            const newItemRowElement = itemsContainer.lastElementChild;
            addEventListenersToNewRow(newItemRowElement);
            itemIndex++;
            calculateTotals();
        });

        document.querySelectorAll('.item-row').forEach(row => {
            addEventListenersToNewRow(row);
        });

        globalDiscountInput.addEventListener('input', calculateTotals);

        // 🔥 FIX PENTING: pastikan total dihitung ulang sebelum submit
        form.addEventListener('submit', function (e) {
            calculateTotals();

            // Validasi: kalau total kosong, cegah submit
            const total = finalTotalHidden.value;
            if (total === '' || isNaN(parseFloat(total))) {
                e.preventDefault();
                alert('Total harga tidak valid. Periksa kembali data item.');
            }
        });

        calculateTotals();
    });
</script>
@endpush
