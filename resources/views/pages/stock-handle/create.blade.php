@extends('layouts.master')
@section('title', 'Tambah Pembelian Sparepart')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('stock-handle.store') }}" method="POST">
                            @csrf


                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Pilih Supplier</label>
                                <select name="supplier_id" id="supplier_id" class="form-select select2-init">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Pilih Sparepart</label>
                                {{-- Changed ID for clarity as it's no longer 'sparepart_id_0' but the main picker --}}
                                <select id="main_sparepart_picker" class="form-select select2-init"  >
                                    <option value="">-- Cari atau Pilih Sparepart --</option>
                                    @foreach ($spareparts as $sparepart)
                                        <option value="{{ $sparepart->id }}" data-name="{{ $sparepart->name }}"
                                            data-category="{{ $sparepart->category->name }}"
                                            data-default-price="{{ $sparepart->purchase_price ?? 0 }}">
                                            {{ $sparepart->name }} ({{ $sparepart->code_part }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <hr>
                            <h4>Daftar Item Sparepart</h4>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered" id="sparepart_items_table">
                                    <thead>
                                        <tr>
                                            <th>Nama Sparepart</th>
                                            <th style="width: 150px;">Kuantitas</th>
                                            <th style="width: 200px;">Harga Beli (Rp)</th>
                                            <th style="width: 150px;">Subtotal (Rp)</th>
                                            <th style="width: 80px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Dynamic rows will be inserted here by JavaScript --}}
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

                            <div class="mb-3">
                                <label class="form-label">Tanggal Terima</label>
                                <input type="date" name="received_date" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="note" class="form-control" placeholder="Satuan Kuantitas: ..."></textarea>
                            </div>

                            <div id="hidden_sparepart_inputs">
                                {{-- Hidden inputs for spareparts will be added here --}}
                                {{-- Example: <input type="hidden" name="spareparts[0][sparepart_id]" value="1"> --}}
                                {{--          <input type="hidden" name="spareparts[0][quantity]" value="5"> --}}
                                {{--          <input type="hidden" name="spareparts[0][purchase_price]" value="10000"> --}}
                            </div>

                            <div class="mb-3 d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary me-auto"
                                    data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-success">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#category_id').select2({
                    placeholder: 'Pilih Kategori',
                    allowClear: true
                });
                ActiveXObject
            });
        </script>
        <script>
            let itemCounter = 0; // To keep track of unique item indices for form submission
            let currentItems = {}; // To store selected items and prevent duplicates
            let grandTotal = 0;

            function updateGrandTotal() {
                grandTotal = 0;
                document.querySelectorAll('.item-subtotal').forEach(function(el) {
                    grandTotal += parseFloat(el.textContent.replace('Rp ', '').replace('.',
                        '')); // Assuming Rp 10.000 format
                });
                document.getElementById('grand_total_display').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
            }

            // Function to render an item row
            function renderItemRow(sparepartId, sparepartName, defaultPrice) {
                // Prevent adding duplicate items
                if (currentItems[sparepartId]) {
                    alert('Sparepart ini sudah ada dalam daftar. Silakan ubah kuantitasnya.');
                    return;
                }

                const tableBody = document.querySelector('#sparepart_items_table tbody');
                const newRow = tableBody.insertRow(); // Create a new table row

                newRow.innerHTML = `
        <td>${sparepartName}</td>
        <td>
            <input type="number" name="spareparts[${itemCounter}][quantity]"
                   class="form-control item-quantity" value="1" min="1" required
                   data-item-id="${sparepartId}" data-item-index="${itemCounter}">
        </td>
        <td>
            <input type="number" name="spareparts[${itemCounter}][purchase_price]"
                   class="form-control item-price" value="${defaultPrice}" min="0" required
                   data-item-id="${sparepartId}" data-item-index="${itemCounter}">
        </td>
        <td><span class="item-subtotal">Rp ${defaultPrice.toLocaleString('id-ID')}</span></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-item-btn"
                    data-item-id="${sparepartId}" data-item-index="${itemCounter}">Hapus</button>
        </td>
    `;

                // Add hidden inputs for form submission
                const hiddenInputsDiv = document.getElementById('hidden_sparepart_inputs');
                hiddenInputsDiv.innerHTML += `
                <input type="hidden" name="spareparts[${itemCounter}][sparepart_id]" value="${sparepartId}" id="hidden_sparepart_id_${itemCounter}">
            `;

                currentItems[sparepartId] = {
                    index: itemCounter,
                    quantity: 1,
                    price: defaultPrice
                }; // Store current item's data

                itemCounter++; // Increment global counter for next item
                updateGrandTotal();
            }

            // Main DOMContentLoaded listener
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Select2 for the main sparepart picker and supplier picker
                // No need for a loop, as these are specific elements
                $('#main_sparepart_picker').select2({
                    placeholder: '-- Cari atau Pilih Sparepart --',
                    allowClear: true,
                    dropdownParent: $('body') // Crucial for positioning, or your specific modal ID
                });

                $('#supplier_id').select2({
                    placeholder: '--Pilih Supplier--',
                    allowClear: true,
                    dropdownParent: $('body') // Crucial for positioning, or your specific modal ID
                });


                // Event listener for when a sparepart is selected from the main picker
                $('#main_sparepart_picker').on('select2:select', function(e) {
                    const data = e.params.data;
                    const sparepartId = data.id;
                    const sparepartName = data.element.dataset.name; // Get data-name from option
                    const defaultPrice = parseFloat(data.element.dataset
                        .defaultPrice); // Get data-default-price

                    if (sparepartId) {
                        renderItemRow(sparepartId, sparepartName, defaultPrice);
                        $(this).val(null).trigger('change'); // Clear the select2 input after selection
                    }
                });

                // Event listener for quantity or price changes (delegated for dynamic rows)
                document.querySelector('#sparepart_items_table tbody').addEventListener('input', function(e) {
                    if (e.target.classList.contains('item-quantity') || e.target.classList.contains(
                            'item-price')) {
                        const row = e.target.closest('tr');
                        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
                        const price = parseFloat(row.querySelector('.item-price').value) || 0;
                        const subtotal = quantity * price;

                        row.querySelector('.item-subtotal').textContent = 'Rp ' + subtotal.toLocaleString(
                            'id-ID');

                        // Update the hidden input value for purchase_price and quantity
                        const itemIndex = e.target.dataset.itemIndex;
                        document.querySelector(`input[name="spareparts[${itemIndex}][quantity]"]`).value =
                            quantity;
                        document.querySelector(`input[name="spareparts[${itemIndex}][purchase_price]"]`).value =
                            price;


                        updateGrandTotal();
                    }
                });

                // Event listener for removing an item row (delegated)
                document.querySelector('#sparepart_items_table tbody').addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-item-btn')) {
                        const rowToRemove = e.target.closest('tr');
                        const sparepartId = e.target.dataset.itemId;
                        const itemIndex = e.target.dataset.itemIndex;

                        // Remove hidden inputs
                        document.getElementById(`hidden_sparepart_id_${itemIndex}`).remove();
                        document.querySelector(`input[name="spareparts[${itemIndex}][quantity]"]`).remove();
                        document.querySelector(`input[name="spareparts[${itemIndex}][purchase_price]"]`)
                            .remove();


                        rowToRemove.remove(); // Remove the table row
                        delete currentItems[sparepartId]; // Remove from tracking object
                        updateGrandTotal();
                    }
                });

                // Handle default initial item if any (e.g., in edit mode)
                // You'd add logic here to loop through existing items and call renderItemRow for each
            });
        </script>
    @endpush
