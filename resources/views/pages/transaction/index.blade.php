@extends('layouts.master')

@section('title', 'Data Transaksi')

@section('action')
    @can('transaction.create')
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTransactionModal">Tambah Transaksi</button>
        {{-- Ensure modal-create.blade.php is updated as described above --}}
        @include('pages.transaction.modal-create', [
            'services' => $services, 
            'spareparts' => $spareparts
        ])
    @endcan
    {{-- <a href="{{ route('transaction.export-pdf') }}" target="_blank" class="btn btn-danger">Export PDF</a> --}}
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="transaction-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggan</th>
                            <th>No. Kendaraan</th>
                            <th>Tanggal Transaksi</th>
                            <th>Total Harga</th>
                            <th>Item</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $trx)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $trx->customer_name }}</td>
                                <td>{{ $trx->vehicle_number }}</td>
                                <td>{{ $trx->transaction_date->format('d-m-Y') }}</td>
                                <td>Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                                <td>
                                    <ul>
                                        @foreach ($trx->items as $item)
                                            <li>
                                                {{ $item->item_type === 'service' ? ($item->service ? $item->service->nama : 'Layanan Tidak Ditemukan') : ($item->sparepart ? $item->sparepart->name : 'Sparepart Tidak Ditemukan') }}
                                                (Rp {{ number_format($item->price, 0, ',', '.') }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    @canany(['transaction.update', 'transaction.delete'])
                                        @can('transaction.update')
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#editModal-{{ $trx->id }}">
                                                Edit
                                            </button>
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
                                    @else
                                        <span class="text-muted">Tidak ada aksi</span>
                                    @endcanany
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Modal edit diletakkan setelah table --}}
            @foreach ($transactions as $trx)
                {{-- Ensure modal-edit.blade.php is updated as described in previous responses --}}
                @include('pages.transaction.modal-edit', [
                    'transaction' => $trx,
                    'services' => $services,
                    'spareparts' => $spareparts
                ])
            @endforeach
        </div>
    </div>
@endsection

@push('addon-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemCounter = 0; // Global counter to ensure unique names/IDs across all dynamic forms

        // Function to update the price and hidden type/id fields based on selected item
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

            // Clear previous values
            if (priceInput) priceInput.value = '';
            if (itemTypeInput) itemTypeInput.value = '';
            if (itemIdInput) itemIdInput.value = '';
            

            if (selectedOption && selectedOption.value) { // Ensure an option is selected and has a value
                const price = selectedOption.getAttribute('data-price');
                const fullId = selectedOption.value; // e.g., "service-1" or "sparepart-5"
                const [itemType, itemId] = fullId.split('-');

                console.log('Selected option value:', fullId, 'Price:', price, 'Type:', itemType, 'ID:', itemId);
                
                if (price) {
                    if (priceInput) priceInput.value = parseFloat(price); // Set numeric value
                } else {
                    console.warn("No 'data-price' attribute found or price is empty for selected option:", selectedOption);
                }

                // Set hidden fields
                if (itemTypeInput) itemTypeInput.value = itemType;
                if (itemIdInput) itemIdInput.value = itemId;

            } else {
                console.log("No item selected, clearing price and hidden fields.");
            }
        }
        
        // Function to initialize event listeners for a new item row
        function initializeItemRow(rowElement) {
            console.log('Initializing item row:', rowElement.id || rowElement.className);
            const itemSelect = rowElement.querySelector('.item-select');
            const removeItemButton = rowElement.querySelector('.remove-item');

            if (!itemSelect) {
                console.error("Item select dropdown not found in row:", rowElement);
                return;
            }

            // Attach change listener to the item select dropdown
            // Remove existing listener first to prevent multiple attachments on re-initialization
            itemSelect.removeEventListener('change', function() { updatePriceAndHiddenFields(this); }); 
            itemSelect.addEventListener('change', function() {
                updatePriceAndHiddenFields(this);
            });

            // Attach click listener to the remove button
            if (removeItemButton) { // Check if the button exists
                removeItemButton.removeEventListener('click', handleRemoveItem); // Remove existing listener
                removeItemButton.addEventListener('click', handleRemoveItem);
            }

            // Trigger updatePriceAndHiddenFields immediately for existing/initial rows
            // This handles pre-selected values in edit modals or if the first option has a default price
            updatePriceAndHiddenFields(itemSelect);
        }

        // Centralized remove item handler
        function handleRemoveItem(event) {
            const row = event.target.closest('.item-row');
            const container = row.parentElement;
            
            if (!container) {
                console.error("Items container not found for remove button.");
                return;
            }

            // Count only actual item rows that are not hidden
            const visibleItemRows = container.querySelectorAll('.item-row:not(.d-none)').length;
            
            if (visibleItemRows > 1) {
                row.remove();
            } else {
                // Optionally, clear the first row instead of removing it if it's the last one
                const selectEl = row.querySelector('.item-select');
                if (selectEl) selectEl.selectedIndex = 0;
                const priceInput = row.querySelector('.price-input');
                if (priceInput) priceInput.value = '';
                const qtyInput = row.querySelector('input[type="number"][name*="quantity"]');
                if (qtyInput) qtyInput.value = 1;
                
                // Also clear the hidden type/id inputs
                const itemTypeInput = row.querySelector('.item-type-input');
                if (itemTypeInput) itemTypeInput.value = '';
                const itemIdInput = row.querySelector('.item-id-input');
                if (itemIdInput) itemIdInput.value = '';

                console.log("Last item row cleared instead of removed.");
            }
        }

        // --- Handle Create Modal ---
        const createTransactionModal = document.getElementById('createTransactionModal');
        if (createTransactionModal) {
            createTransactionModal.addEventListener('shown.bs.modal', function () {
                console.log('Create Transaction Modal shown. Resetting form.');
                const itemsContainer = document.getElementById('items-container');
                
                // Remove all but the first row to ensure a clean state
                const allRows = itemsContainer.querySelectorAll('.item-row');
                for (let i = 1; i < allRows.length; i++) { // Start from the second row
                    allRows[i].remove();
                }

                // Reset the first item row
                const firstRow = itemsContainer.querySelector('.item-row');
                if (firstRow) {
                    const selectEl = firstRow.querySelector('.item-select');
                    if (selectEl) selectEl.selectedIndex = 0; // Reset select to "Pilih Item"
                    const priceInput = firstRow.querySelector('.price-input');
                    if (priceInput) priceInput.value = ''; // Clear price
                    const qtyInput = firstRow.querySelector('input[type="number"][name*="quantity"]');
                    if (qtyInput) qtyInput.value = 1; // Reset quantity

                    // Also clear the hidden type/id inputs
                    const itemTypeInput = firstRow.querySelector('.item-type-input');
                    if (itemTypeInput) itemTypeInput.value = '';
                    const itemIdInput = firstRow.querySelector('.item-id-input');
                    if (itemIdInput) itemIdInput.value = '';

                    initializeItemRow(firstRow); // Re-initialize the first row to ensure its listeners are fresh
                }
                itemCounter = 0; // Reset counter for a new transaction form, so the first added item gets index 0
                console.log('Initial itemCounter for create modal:', itemCounter);
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
                    itemCounter++; // Increment counter for unique names/IDs

                    newRow.querySelectorAll('input, select, label').forEach(el => {
                        // Update name attribute for array indexing (e.g., items[0][item_full_id] -> items[1][item_full_id])
                        if (el.name) {
                            el.name = el.name.replace(/\[\d+\]/, `[${itemCounter}]`);
                        }
                        // Update ID attribute for uniqueness
                        if (el.id) {
                            el.id = el.id.replace(/-\d+/, `-${itemCounter}`);
                        }
                        // Update 'for' attribute for labels
                        if (el.tagName === 'LABEL' && el.htmlFor) {
                            el.htmlFor = el.htmlFor.replace(/-\d+/, `-${itemCounter}`);
                        }
                        
                        // Reset values for cloned elements
                        if (el.classList.contains('price-input') || el.classList.contains('item-type-input') || el.classList.contains('item-id-input')) {
                            el.value = ''; // Clear price and hidden fields
                        } else if (el.tagName === 'SELECT') {
                            el.selectedIndex = 0; // Select first option ("Pilih Item")
                        } else if (el.type === 'number') {
                            el.value = 1; // Reset quantity to 1
                        } else if (el.type === 'text') {
                            el.value = ''; // Clear any text inputs
                        }
                    });

                    container.appendChild(newRow);
                    initializeItemRow(newRow); // Initialize the newly added row with listeners
                });
            }
        }

        // --- Handle Edit Modals ---
        // We use a general selector for all modals whose IDs start with 'editModal-'
        document.querySelectorAll('[id^="editModal-"]').forEach(editModal => {
            editModal.addEventListener('shown.bs.modal', function () {
                console.log('Edit Modal shown:', editModal.id);
                const transactionId = editModal.id.split('-')[1]; // Extract ID from modal ID
                // Use the correct, specific ID for the container in this modal
                const itemsContainer = this.querySelector(`#items-container-edit-${transactionId}`); 
                
                if (itemsContainer) {
                    // Initialize all existing item rows in the edit modal
                    itemsContainer.querySelectorAll('.item-row').forEach((row) => {
                        initializeItemRow(row);
                    });
                    // Set itemCounter to the number of existing rows for the next new item
                    itemCounter = itemsContainer.querySelectorAll('.item-row').length;
                    console.log('Initial itemCounter for edit modal:', itemCounter);
                }
            });

            // Also attach add item functionality if edit modals have it
            const addItemButtonEdit = editModal.querySelector('.add-item-edit'); 
            if (addItemButtonEdit) {
                addItemButtonEdit.addEventListener('click', function() {
                    console.log('Add Item button clicked in Edit Modal for:', editModal.id);
                    const transactionId = this.dataset.transactionId; // Get ID from data attribute
                    // Use the correct, specific ID for the container in this modal
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
                });
            }
        });

        // Initial setup for the first item row in the create modal if it's already present on page load
        // This targets the first row inside the #items-container (for the create modal)
        const initialCreateModalFirstRow = document.querySelector('#items-container .item-row');
        if (initialCreateModalFirstRow) {
            initializeItemRow(initialCreateModalFirstRow);
        }
    });
</script>
@endpush