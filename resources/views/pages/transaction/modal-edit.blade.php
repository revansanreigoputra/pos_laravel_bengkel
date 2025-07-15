<div class="modal fade" id="editModal-{{ $transaction->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel-{{ $transaction->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('transaction.update', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel-{{ $transaction->id }}">Edit Transaksi #{{ $transaction->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Informasi Umum --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" name="customer_name" value="{{ old('customer_name', $transaction->customer_name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Kendaraan</label>
                        <input type="text" class="form-control" name="vehicle_number" value="{{ old('vehicle_number', $transaction->vehicle_number) }}" required>
                    </div>

                    {{-- Edit Item --}}
                    <h6>Detail Item:</h6>
                    <div id="items-container-edit-{{ $transaction->id }}"> {{-- Unique ID for each edit modal's container --}}
                        @foreach ($transaction->items as $index => $item)
                            <div class="item-row mb-3 row align-items-end">
                                <div class="col-md-5">
                                    <label for="edit-item-select-{{ $transaction->id }}-{{ $index }}" class="form-label">Item</label>
                                    <select name="items[{{ $index }}][item_full_id]" class="form-select item-select" id="edit-item-select-{{ $transaction->id }}-{{ $index }}" required>
                                        <option value="">Pilih Item</option>
                                        <optgroup label="Layanan">
                                            @foreach ($services as $service)
                                                <option value="service-{{ $service->id }}"
                                                    data-price="{{ $service->harga_jual }}" {{-- Use harga_jual for consistency --}}
                                                    @if ($item->item_type === 'service' && $item->item_id == $service->id) selected @endif>
                                                    {{ $service->nama }} (Rp {{ number_format($service->harga_jual, 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Sparepart">
                                            @foreach ($spareparts as $spare)
                                                <option value="sparepart-{{ $spare->id }}"
                                                    data-price="{{ $spare->price }}" {{-- Use price for consistency --}}
                                                    @if ($item->item_type === 'sparepart' && $item->item_id == $spare->id) selected @endif>
                                                    {{ $spare->name }} (Rp {{ number_format($spare->price, 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    {{-- Hidden inputs to store item_type and item_id separately --}}
                                    <input type="hidden" name="items[{{ $index }}][item_type]" class="item-type-input" value="{{ $item->item_type }}">
                                    <input type="hidden" name="items[{{ $index }}][item_id]" class="item-id-input" value="{{ $item->item_id }}">
                                </div>

                                <div class="col-md-3">
                                    <label for="edit-price-{{ $transaction->id }}-{{ $index }}" class="form-label">Harga</label>
                                    {{-- Removed 'readonly' to allow manual input --}}
                                    <input type="number" name="items[{{ $index }}][price]" class="form-control price-input"
                                        id="edit-price-{{ $transaction->id }}-{{ $index }}"
                                        value="{{ old('items.' . $index . '.price', $item->price) }}" step="0.01" required>
                                </div>

                                <div class="col-md-2">
                                    <label for="edit-qty-{{ $transaction->id }}-{{ $index }}" class="form-label">Qty</label>
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control"
                                        id="edit-qty-{{ $transaction->id }}-{{ $index }}"
                                        min="1" value="{{ old('items.' . $index . '.quantity', $item->quantity) }}" required>
                                </div>

                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-item w-100">X</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-secondary mt-3 add-item-edit" data-transaction-id="{{ $transaction->id }}">Tambah Item</button>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

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
            // Use 'once' to prevent multiple listeners if the row is re-initialized (e.g., from modal show)
            itemSelect.removeEventListener('change', updatePriceAndHiddenFields); // Remove existing listener first
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
                itemCounter = 0; // Reset counter for a new transaction form
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
        document.querySelectorAll('[id^="editModal-"]').forEach(editModal => {
            editModal.addEventListener('shown.bs.modal', function () {
                console.log('Edit Modal shown:', editModal.id);
                const transactionId = editModal.id.split('-')[1]; // Extract ID from modal ID
                const itemsContainer = this.querySelector(`#items-container-edit-${transactionId}`); 
                
                if (itemsContainer) {
                    // Initialize all existing item rows in the edit modal
                    itemsContainer.querySelectorAll('.item-row').forEach((row, index) => {
                        // Set the initial itemCounter based on the number of existing rows
                        itemCounter = Math.max(itemCounter, index);
                        initializeItemRow(row);
                    });
                     // After initializing all existing rows, increment itemCounter for any new ones added later
                     itemCounter = itemsContainer.querySelectorAll('.item-row').length > 0 ? itemsContainer.querySelectorAll('.item-row').length - 1 : 0;
                }
            });

            // Handle adding items in the edit modal
            const addItemButtonEdit = editModal.querySelector('.add-item-edit'); 
            if (addItemButtonEdit) {
                addItemButtonEdit.addEventListener('click', function() {
                    console.log('Add Item button clicked in Edit Modal for:', editModal.id);
                    const transactionId = this.dataset.transactionId; // Get ID from data attribute
                    const container = editModal.querySelector(`#items-container-edit-${transactionId}`); 
                    const firstRowTemplate = container.querySelector('.item-row'); 

                    if (!firstRowTemplate) {
                        console.error("Cannot add item: No template row found in #items-container-edit for edit modal.", editModal.id);
                        return;
                    }

                    const newRow = firstRowTemplate.cloneNode(true);
                    itemCounter++; // Increment counter for new items in this session

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
        const initialCreateModalFirstRow = document.querySelector('#items-container .item-row');
        if (initialCreateModalFirstRow) {
            initializeItemRow(initialCreateModalFirstRow);
        }
    });
</script>
@endpush