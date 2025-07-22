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
                    <div class="mb-3">
                        <label class="form-label">Pilih Supplier</label>
                        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror"
                            required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <!-- Existing Sparepart Dropdown -->
                    <div class="mb-3">
                        <label for="sparepart_id">Pilih Sparepart (optional)</label>
                        <select name="sparepart_id" id="sparepart_id" class="form-select">
                            <option value="">-- Tambah Baru Sparepart --</option>
                            @foreach ($spareparts as $sparepart)
                                <option value="{{ $sparepart->id }}">{{ $sparepart->name }}
                                    ({{ $sparepart->code_part }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- New Sparepart Fields -->
                    <div class="mb-3 new-sparepart-field">
                        <label for="name">Nama Sparepart (Jika baru)</label>
                        <input type="text" name="name" class="form-control" placeholder="Sparepart Name">
                    </div>

                    <div class="mb-3 new-sparepart-field">
                        <label for="code_part">Kode Sparepart (Jika Baru)</label>
                        <input type="text" name="code_part" class="form-control" placeholder="Unique Code">
                    </div>


                    <div class="mb-3">
                        <label class="form-label">Jumlah Stok (Kuantitas)</label>
                        <input type="number" name="quantity"
                            class="form-control @error('quantity') is-invalid @enderror" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Beli</label>
                        <input type="number" name="purchase_price"
                            class="form-control @error('purchase_price') is-invalid @enderror" required>
                        @error('purchase_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Diterima</label>
                        <input type="date" name="received_date"
                            class="form-control @error('received_date') is-invalid @enderror">
                        @error('received_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="note" class="form-control @error('note') is-invalid @enderror"></textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div> 
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sparepartSelect = document.getElementById('sparepart_id');
        const newSparepartFields = document.querySelectorAll('.new-sparepart-field');

        function toggleNewFields() {
            const isNew = !sparepartSelect.value; // true if value is empty
            newSparepartFields.forEach(field => {
                field.style.display = isNew ? 'block' : 'none';
            });
        }

        // Initial check on load
        toggleNewFields();

        // Listen to changes
        sparepartSelect.addEventListener('change', toggleNewFields);
    });
</script> 