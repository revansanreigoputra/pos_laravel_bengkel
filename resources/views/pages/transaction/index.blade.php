@extends('layouts.master')

@section('title', 'Data Transaksi')

@section('action')
    @can('transaction.create')
        <a href="{{ route('transaction.create') }}" class="btn btn-primary">Tambah Transaksi</a>
    @endcan
@endsection

@section('content')
    <div class="card">
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

            {{-- Filter Status Transaksi --}}
            <div class="mb-3 d-flex justify-content-end align-items-center">
                <label for="statusFilter" class="form-label mb-0 me-2">Filter Status:</label>
                <select id="statusFilter" class="form-select" style="width: auto;">
                    <option value="">Semua</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            {{-- End Filter Status Transaksi --}}

            <div class="table-responsive">
                <table id="transaction-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Invoice</th>
                            <th>Nama Pelanggan</th>
                            <th>No. Kendaraan</th>
                            <th>Merk/Model</th>
                            <th>Tanggal Transaksi</th>
                            <th>Total Harga</th>
                            <th>Metode Bayar</th>
                            <th>Status</th>
                            {{-- <th>Item</th> --}}
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $trx)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $trx->invoice_number }}</td>
                                <td>{{ $trx->customer_name }}</td>
                                <td>{{ $trx->vehicle_number }}</td>
                                <td>{{ $trx->vehicle_model ?? '-' }}</td>
                                <td>{{ $trx->transaction_date->format('d-m-Y') }}</td>
                                <td>Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                                <td>{{ $trx->payment_method }}</td>
                                <td class="justify-content-center align-items-center">
                                    {{-- Status Transaksi --}}
                                    {{-- Status Badge --}}
                                    @if ($trx->status == 'completed')
                                        <span class="badge bg-success text-white p-2">{{ $trx->status }}</span>
                                    @elseif ($trx->status == 'pending')
                                        <span class="badge bg-warning text-white p-2">{{ $trx->status }}</span>
                                    @else
                                        <span class="badge bg-danger text-white p-2">{{ $trx->status }}</span>
                                    @endif
                                </td>
                                {{-- <td>
                                    <ul>
                                        @foreach ($trx->items as $item)
                                            <li>
                                                {{ $item->item_type === 'service' ? ($item->service ? $item->service->nama : 'Layanan Tidak Ditemukan') : ($item->sparepart ? $item->sparepart->name : 'Sparepart Tidak Ditemukan') }}
                                                (Qty: {{ $item->quantity }}, Rp {{ number_format($item->price, 0, ',', '.') }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </td> --}}
                                <td>
                                    @canany(['transaction.edit', 'transaction.delete'])
                                        @can('transaction.edit')
                                            <a href="{{ route('transaction.edit', $trx->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        @endcan
                                        @can('transaction.delete')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#delete-transaction-{{ $trx->id }}">
                                                Hapus
                                            </button>
                                            <x-modal.delete-confirm
                                                id="delete-transaction-{{ $trx->id }}"
                                                :route="route('transaction.destroy', $trx->id)"
                                                item="{{ $trx->customer_name }}"
                                                title="Hapus Transaksi?"
                                                description="Data transaksi yang dihapus tidak bisa dikembalikan." />
                                        @endcan
                                        @if ($trx->proof_of_transfer_url)
                                            <a href="{{ asset($trx->proof_of_transfer_url) }}" target="_blank" class="btn btn-sm btn-info mt-1">Lihat Bukti TF</a>
                                        @endif
                                        {{-- ADDED CONDITION HERE --}}
                                        @if ($trx->status == 'completed')
                                            <a href="{{ route('transaction.exportPdf', $trx->id) }}" class="btn btn-sm btn-info mt-1" target="_blank">Cetak Invoice</a>
                                        @endif
                                    @else
                                        <span class="text-muted">Tidak ada aksi</span>
                                    @endcanany
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTables
        var table = $('#transaction-table').DataTable(); // Simpan instance DataTables ke variabel

        // Event listener untuk filter status
        $('#statusFilter').on('change', function() {
            var status = $(this).val(); // Dapatkan nilai yang dipilih dari dropdown
            // Gunakan `column().search()` untuk memfilter kolom 'Status'
            // Kolom 'Status' ada di indeks ke-8 (dimulai dari 0)
            table.column(8).search(status).draw();
        });

        // ... (kode JavaScript lainnya yang sudah ada) ...

        let itemCounter = 0; // Global counter to ensure unique names/IDs across all dynamic forms

        function updatePriceAndHiddenFields(selectElement) {
            console.log('updatePriceAndHiddenFields called for:', selectElement.id);
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const itemRow = selectElement.closest('.item-row');

            if (!itemRow) {
                console.error("Item row not found for select element:", selectElement);
                return;
            }

            const priceInput = itemRow.querySelector('.price-input');
            const itemTypeInput = itemRow.querySelector('.item-type-input');
            const itemIdInput = itemRow.querySelector('.item-id-input');

            if (priceInput) priceInput.value = '';
            if (itemTypeInput) itemTypeInput.value = '';
            if (itemIdInput) itemIdInput.value = '';

            if (selectedOption && selectedOption.value) {
                const price = selectedOption.getAttribute('data-price');
                const fullId = selectedOption.value;
                const [itemType, itemId] = fullId.split('-');

                console.log('Selected option value:', fullId, 'Price:', price, 'Type:', itemType, 'ID:', itemId);

                if (price) {
                    if (priceInput) priceInput.value = parseFloat(price);
                } else {
                    console.warn("No 'data-price' attribute found or price is empty for selected option:", selectedOption);
                }

                if (itemTypeInput) itemTypeInput.value = itemType;
                if (itemIdInput) itemIdInput.value = itemId;

            } else {
                console.log("No item selected, clearing price and hidden fields.");
            }
            recalculateTotal(selectElement.closest('form'));
        }

        function initializeItemRow(rowElement) {
            console.log('Initializing item row:', rowElement.id || rowElement.className);
            const itemSelect = rowElement.querySelector('.item-select');
            const removeItemButton = rowElement.querySelector('.remove-item');
            const quantityInput = rowElement.querySelector('input[type="number"][name*="quantity"]');
            const priceInput = rowElement.querySelector('.price-input');

            if (!itemSelect) {
                console.error("Item select dropdown not found in row:", rowElement);
                return;
            }

            itemSelect.removeEventListener('change', function() { updatePriceAndHiddenFields(this); });
            itemSelect.addEventListener('change', function() {
                updatePriceAndHiddenFields(this);
            });

            if (quantityInput) {
                quantityInput.removeEventListener('input', handleItemChange);
                quantityInput.addEventListener('input', handleItemChange);
            }
            if (priceInput) {
                priceInput.removeEventListener('input', handleItemChange);
                priceInput.addEventListener('input', handleItemChange);
            }

            if (removeItemButton) {
                removeItemButton.removeEventListener('click', handleRemoveItem);
                removeItemButton.addEventListener('click', handleRemoveItem);
            }

            if (itemSelect.value) {
                updatePriceAndHiddenFields(itemSelect);
            } else {
                const itemRow = itemSelect.closest('.item-row');
                const priceInput = itemRow.querySelector('.price-input');
                const itemTypeInput = itemRow.querySelector('.item-type-input');
                const itemIdInput = itemRow.querySelector('.item-id-input');
                if (priceInput) priceInput.value = '';
                if (itemTypeInput) itemTypeInput.value = '';
                if (itemIdInput) itemIdInput.value = '';
            }
        }

        function handleItemChange(event) {
            recalculateTotal(event.target.closest('form'));
        }

        function handleRemoveItem(event) {
            const row = event.target.closest('.item-row');
            const container = row.parentElement;

            if (!container) {
                console.error("Items container not found for remove button.");
                return;
            }

            const visibleItemRows = container.querySelectorAll('.item-row:not(.d-none)').length;

            if (visibleItemRows > 1) {
                row.remove();
            } else {
                const selectEl = row.querySelector('.item-select');
                if (selectEl) selectEl.selectedIndex = 0;
                const priceInput = row.querySelector('.price-input');
                if (priceInput) priceInput.value = '';
                const qtyInput = row.querySelector('input[type="number"][name*="quantity"]');
                if (qtyInput) qtyInput.value = 1;

                const itemTypeInput = row.querySelector('.item-type-input');
                if (itemTypeInput) itemTypeInput.value = '';
                const itemIdInput = row.querySelector('.item-id-input');
                if (itemIdInput) itemIdInput.value = '';

                console.log("Last item row cleared instead of removed.");
            }
            recalculateTotal(container.closest('form'));
        }

        function recalculateTotal(formElement) {
            if (!formElement) {
                console.error("Form element not found for recalculation.");
                return;
            }

            const itemRows = formElement.querySelectorAll('.item-row');
            let subtotal = 0;

            itemRows.forEach(row => {
                const priceInput = row.querySelector('.price-input');
                const quantityInput = row.querySelector('input[type="number"][name*="quantity"]');

                const price = parseFloat(priceInput ? priceInput.value : 0) || 0;
                const quantity = parseInt(quantityInput ? quantityInput.value : 0) || 0;

                subtotal += (price * quantity);
            });

            const globalDiscountInput = formElement.querySelector('.global-discount-input');
            const globalDiscount = parseFloat(globalDiscountInput ? globalDiscountInput.value : 0) || 0;

            let finalTotal = subtotal - globalDiscount;
            if (finalTotal < 0) {
                finalTotal = 0;
            }

            const totalDisplayInput = formElement.querySelector('.total-price-display');
            if (totalDisplayInput) {
                totalDisplayInput.value = finalTotal.toFixed(0);
            }
        }


        // --- Handle Create Form ---
        const createTransactionModal = document.getElementById('createTransactionModal');
        if (createTransactionModal) {
            createTransactionModal.addEventListener('shown.bs.modal', function () {
                console.log('Create Transaction Modal shown. Resetting form.');
                const itemsContainer = document.getElementById('items-container');

                const allRows = itemsContainer.querySelectorAll('.item-row');
                for (let i = 1; i < allRows.length; i++) {
                    allRows[i].remove();
                }

                const firstRow = itemsContainer.querySelector('.item-row');
                if (firstRow) {
                    const selectEl = firstRow.querySelector('.item-select');
                    if (selectEl) selectEl.selectedIndex = 0;
                    const priceInput = firstRow.querySelector('.price-input');
                    if (priceInput) priceInput.value = '';
                    const qtyInput = firstRow.querySelector('input[type="number"][name*="quantity"]');
                    if (qtyInput) qtyInput.value = 1;

                    const itemTypeInput = firstRow.querySelector('.item-type-input');
                    if (itemTypeInput) itemTypeInput.value = '';
                    const itemIdInput = firstRow.querySelector('.item-id-input');
                    if (itemIdInput) itemIdInput.value = '';

                    initializeItemRow(firstRow);
                }
                itemCounter = 0;
                console.log('Initial itemCounter for create modal:', itemCounter);
                recalculateTotal(createTransactionModal.querySelector('form'));
            });

            const addItemButton = document.getElementById('add-item');
            if (addItemButton) {
                addItemButton.addEventListener('click', function () {
                    console.log('Add Item button clicked for Create Modal.');
                    const container = document.getElementById('items-container');
                    const firstRowTemplate = container.querySelector('.item-row');

                    if (!firstRowTemplate) {
                        console.error("Cannot add item: No template row found in #items-container.");
                        return;
                    }

                    const newRow = firstRowTemplate.cloneNode(true);
                    itemCounter++;

                    newRow.querySelectorAll('input, select, label').forEach(el => {
                        if (el.name) {
                            el.name = el.name.replace(/\[\d+\]/, `[${itemCounter}]`);
                        }
                        if (el.id) {
                            el.id = el.id.replace(/-\d+/, `-${itemCounter}`);
                        }
                        if (el.tagName === 'LABEL' && el.htmlFor) {
                            el.htmlFor = el.htmlFor.replace(/-\d+/, `-${itemCounter}`);
                        }

                        if (el.classList.contains('price-input') || el.classList.contains('item-type-input') || el.classList.contains('item-id-input')) {
                            el.value = '';
                        } else if (el.tagName === 'SELECT') {
                            el.selectedIndex = 0;
                        } else if (el.type === 'number') {
                            el.value = 1;
                        } else if (el.type === 'text') {
                            el.value = '';
                        }
                    });

                    container.appendChild(newRow);
                    initializeItemRow(newRow);
                    recalculateTotal(container.closest('form'));
                });
            }

            const createGlobalDiscountInput = createTransactionModal.querySelector('.global-discount-input');
            if (createGlobalDiscountInput) {
                createGlobalDiscountInput.addEventListener('input', function() {
                    recalculateTotal(createTransactionModal.querySelector('form'));
                });
            }
        }

        // --- Handle Edit Modals ---
        document.querySelectorAll('[id^="editModal-"]').forEach(editModal => {
            editModal.addEventListener('shown.bs.modal', function () {
                console.log('Edit Modal shown:', editModal.id);
                const transactionId = editModal.id.split('-')[1];
                const itemsContainer = this.querySelector(`#items-container-edit-${transactionId}`);

                if (itemsContainer) {
                    itemsContainer.querySelectorAll('.item-row').forEach((row) => {
                        initializeItemRow(row);
                    });
                    itemCounter = itemsContainer.querySelectorAll('.item-row').length;
                    console.log('Initial itemCounter for edit modal:', itemCounter);
                    recalculateTotal(editModal.querySelector('form'));
                }
            });

            const addItemButtonEdit = editModal.querySelector('.add-item-edit');
            if (addItemButtonEdit) {
                addItemButtonEdit.addEventListener('click', function() {
                    console.log('Add Item button clicked in Edit Modal for:', editModal.id);
                    const transactionId = this.dataset.transactionId;
                    const container = editModal.querySelector(`#items-container-edit-${transactionId}`);
                    const firstRowTemplate = container.querySelector('.item-row');

                    if (!firstRowTemplate) {
                        console.error("Cannot add item: No template row found in #items-container-edit for edit modal.", editModal.id);
                        return;
                    }

                    const newRow = firstRowTemplate.cloneNode(true);
                    itemCounter++;

                    newRow.querySelectorAll('input, select, label').forEach(el => {
                        if (el.name) {
                            el.name = el.name.replace(/\[\d+\]/, `[${itemCounter}]`);
                        }
                        if (el.id) {
                            el.id = el.id.replace(/-\d+/, `-${itemCounter}`);
                        }
                        if (el.tagName === 'LABEL' && el.htmlFor) {
                            el.htmlFor = el.htmlFor.replace(/-\d+/, `-${itemCounter}`);
                        }

                        if (el.classList.contains('price-input') || el.classList.contains('item-type-input') || el.classList.contains('item-id-input')) {
                            el.value = '';
                        } else if (el.tagName === 'SELECT') {
                            el.selectedIndex = 0;
                        } else if (el.type === 'number') {
                            el.value = 1;
                        } else if (el.type === 'text') {
                            el.value = '';
                        }
                    });

                    container.appendChild(newRow);
                    initializeItemRow(newRow);
                    recalculateTotal(container.closest('form'));
                });
            }

            const editGlobalDiscountInput = editModal.querySelector('.global-discount-input');
            if (editGlobalDiscountInput) {
                editGlobalDiscountInput.addEventListener('input', function() {
                    recalculateTotal(editModal.querySelector('form'));
                });
            }
        });

        const initialCreateModalFirstRow = document.querySelector('#items-container .item-row');
        if (initialCreateModalFirstRow) {
            initializeItemRow(initialCreateModalFirstRow);
            const createForm = initialCreateModalFirstRow.closest('form');
            if (createForm) {
                recalculateTotal(createForm);
            }
        }
    });
</script>
@endpush