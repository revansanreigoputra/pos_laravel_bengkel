{{-- This is a simplified example of modal-create.blade.php --}}
{{-- You need to ensure your actual modal structure reflects this --}}

<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-labelledby="createTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTransactionModalLabel">Tambah Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transaction.store') }}" method="POST">
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
                        <div class="row mb-2 item-row align-items-end">
                            <div class="col-md-5">
                                <label for="item-0" class="form-label">Item</label>
                                <select class="form-select item-select" name="items[0][item_full_id]" id="item-0" required>
                                    <option value="">Pilih Item</option>
                                    <optgroup label="Layanan">
                                        @foreach ($services as $service)
                                            {{-- Menggunakan harga_standar sesuai migrasi services --}}
                                            <option value="service-{{ $service->id }}" data-price="{{ $service->harga_standar }}">
                                                {{ $service->nama }} (Rp {{ number_format($service->harga_standar, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Sparepart">
                                        @foreach ($spareparts as $sparep art)
                                            {{-- Menggunakan selling_price sesuai migrasi spareparts --}}
                                            <option value="sparepart-{{ $sparepart->id }}" data-price="{{ $sparepart->selling_price }}">
                                                {{ $sparepart->name }} (Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
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
                                {{-- Removed readonly if you want to allow manual editing, otherwise keep it --}}
                                <input type="number" class="form-control price-input" name="items[0][price]" id="price-0" step="0.01"  required>
                            </div>
                            <div class="col-md-2">
                                <label for="qty-0" class="form-label">Qty</label>
                                <input type="number" class="form-control" name="items[0][quantity]" id="qty-0" value="1" min="1" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item w-100">X</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary mt-3" id="add-item">Tambah Item</button>
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
    document.addEventListener('DOMContentLoaded', function() {
        let itemCounter = 0; // Use a counter for unique IDs/names

        // Function to update the price based on selected item
        function updatePrice(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const priceInput = selectElement.closest('.item-row').querySelector('.price-input');
            
            if (selectedOption && selectedOption.value) { // Ensure an option is selected and has a value
                const price = selectedOption.getAttribute('data-price');
                if (price) {
                    priceInput.value = parseFloat(price).toFixed(2); // Format to 2 decimal places
                } else {
                    priceInput.value = ''; // Clear if no price is found
                }
            } else {
                priceInput.value = ''; // Clear if "Pilih Item" is selected
            }
        }
        
        // Function to initialize event listeners for a new item row
        function initializeItemRow(rowElement) {
            const itemSelect = rowElement.querySelector('.item-select');
            const removeItemButton = rowElement.querySelector('.remove-item');

            // Attach change listener to the item select dropdown
            itemSelect.addEventListener('change', function() {
                updatePrice(this);
            });

            // Attach click listener to the remove button
            removeItemButton.addEventListener('click', function() {
                const container = document.getElementById('items-container');
                // Ensure there's at least one item row remaining
                if (container.children.length > 1) {
                    rowElement.remove();
                } else {
                    // Optionally, clear the first row instead of removing it if it's the last one
                    itemSelect.selectedIndex = 0;
                    rowElement.querySelector('.price-input').value = '';
                    rowElement.querySelector('input[type="number"][name*="quantity"]').value = 1;
                }
            });

            // Trigger updatePrice immediately for newly initialized rows
            // This handles cases where initial items might already be selected (e.g., on edit modal)
            updatePrice(itemSelect);
        }

        // --- Handle Create Modal ---
        const createTransactionModal = document.getElementById('createTransactionModal');
        if (createTransactionModal) {
            createTransactionModal.addEventListener('shown.bs.modal', function () {
                const itemsContainer = document.getElementById('items-container');
                
                // Clear existing items if any (important for re-opening modal)
                while (itemsContainer.children.length > 1) {
                    itemsContainer.removeChild(itemsContainer.lastChild);
                }
                // Reset the first item row
                const firstRow = itemsContainer.querySelector('.item-row');
                if (firstRow) {
                    firstRow.querySelector('.item-select').selectedIndex = 0;
                    firstRow.querySelector('.price-input').value = '';
                    firstRow.querySelector('input[type="number"][name*="quantity"]').value = 1;
                    initializeItemRow(firstRow); // Re-initialize in case modal was closed/reopened
                }
                itemCounter = 0; // Reset counter for new transaction
            });

            const addItemButton = document.getElementById('add-item');
            if (addItemButton) {
                addItemButton.addEventListener('click', function () {
                    const container = document.getElementById('items-container');
                    const firstRowTemplate = container.querySelector('.item-row');

                    if (!firstRowTemplate) return; // Exit if no template row exists

                    const newRow = firstRowTemplate.cloneNode(true); // Clone deeply
                    
                    itemCounter++; // Increment counter for unique names/IDs

                    newRow.querySelectorAll('input, select').forEach(el => {
                        // Update name attribute for array indexing (e.g., items[0][item] -> items[1][item])
                        el.name = el.name.replace(/\[\d+\]/, `[${itemCounter}]`);
                        // Update ID attribute for uniqueness
                        el.id = el.id.replace(/-\d+/, `-${itemCounter}`);
                        
                        // Reset values for cloned elements
                        if (el.classList.contains('price-input')) {
                            el.value = ''; // Clear price
                        } else if (el.tagName === 'SELECT') {
                            el.selectedIndex = 0; // Select first option ("Pilih Item")
                        } else if (el.type === 'number') {
                            el.value = 1; // Reset quantity to 1
                        } else if (el.type === 'text') {
                            el.value = ''; // Clear any text inputs
                        }
                    });

                    container.appendChild(newRow);
                    initializeItemRow(newRow); // Initialize the newly added row
                });
            }
        }

        // --- Handle Edit Modals ---
        // Iterate over all potential edit modals (since you include them in a loop)
        document.querySelectorAll('[id^="editModal-"]').forEach(editModal => {
            editModal.addEventListener('shown.bs.modal', function () {
                // When an edit modal is shown, initialize all its item rows
                const itemsContainer = this.querySelector('#items-container-edit'); // Assuming a different ID for edit modals
                if (itemsContainer) {
                     itemsContainer.querySelectorAll('.item-row').forEach(row => {
                        initializeItemRow(row);
                    });
                    // You might need to set itemCounter based on existing items if you allow adding more in edit
                    itemCounter = itemsContainer.children.length - 1; // Adjust if adding more items in edit
                }
            });

            // Also attach add item functionality if edit modals have it
            const addItemButtonEdit = editModal.querySelector('.add-item-edit'); // Assuming a different ID for edit modal's add button
            if (addItemButtonEdit) {
                addItemButtonEdit.addEventListener('click', function() {
                    const container = editModal.querySelector('#items-container-edit'); // Ensure correct container
                    const firstRowTemplate = container.querySelector('.item-row');

                    if (!firstRowTemplate) return;

                    const newRow = firstRowTemplate.cloneNode(true);
                    itemCounter++; 

                    newRow.querySelectorAll('input, select').forEach(el => {
                        el.name = el.name.replace(/\[\d+\]/, `[${itemCounter}]`);
                        el.id = el.id.replace(/-\d+/, `-${itemCounter}`);
                        
                        if (el.classList.contains('price-input')) {
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

        // Initialize any existing rows on page load (e.g., if rendering with pre-filled rows, though less common for a 'create' modal)
        // This is important if you have pre-existing item rows in your modal-edit.blade.php
        document.querySelectorAll('.item-row').forEach(row => {
            initializeItemRow(row);
        });
    });
</script>
@endpush