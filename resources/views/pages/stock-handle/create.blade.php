<div class="modal fade" id="createStockHandleModal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <form action="{{ route('stock-handle.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Stok Sparepart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <!-- Supplier Select -->
                    <div class="mb-3">
                        <label class="form-label">Pilih Supplier</label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">--Pilih Supplier--</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Container for dynamic item rows -->
                    <div id="sparepart-items">
                        <div class="row mb-3 item-row align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Pilih Sparepart</label>
                                <select name="spareparts[0][sparepart_id]" class="form-select">
                                    <option value="">--Pilih--</option>
                                    @foreach ($spareparts as $sparepart)
                                        <option value="{{ $sparepart->id }}">{{ $sparepart->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kuantitas</label>
                                <input type="number" name="spareparts[0][quantity]" class="form-control"
                                    placeholder="Kuantitas">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Harga Beli</label>
                                <input type="number" name="spareparts[0][purchase_price]" class="form-control"
                                    placeholder="Harga Beli">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <!-- Remove button (only shown on clone) -->
                            </div>
                        </div>
                    </div>

                    <!-- Add item button -->
                    <div class="mb-3 text-center">
                        <button type="button" class="btn btn-dark btn-sm w-full add-item  py-2">+ Tambah Item</button>
                    </div>

                    <!-- Other Inputs -->
                    <div class="mb-3">
                        <label class="form-label">Tanggal Terima</label>
                        <input type="date" name="received_date" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="note" class="form-control" placeholder="Satuan Kuantitas: ..."></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let index = 1;
        const container = document.getElementById('sparepart-items');

        document.addEventListener('click', function(e) {
            // Add item
            if (e.target.classList.contains('add-item')) {
                e.preventDefault();

                const lastRow = container.querySelector('.item-row:last-child');
                const clone = lastRow.cloneNode(true);

                // Clear input and update name attribute indexes
                clone.querySelectorAll('input, select').forEach(el => {
                    el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
                    el.value = '';
                });

                // Add or update remove button in .col-md-2
                const buttonCol = clone.querySelector('.col-md-2');
                buttonCol.innerHTML = ''; // clear previous button if any
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-danger btn-sm py-2 remove-item';
                removeBtn.textContent = 'Hapus';
                buttonCol.appendChild(removeBtn);

                container.appendChild(clone);
                index++;
            }

            // Remove item
            if (e.target.classList.contains('remove-item')) {
                e.preventDefault();
                const row = e.target.closest('.item-row');
                if (row) row.remove();
            }
        });
    });
</script>
