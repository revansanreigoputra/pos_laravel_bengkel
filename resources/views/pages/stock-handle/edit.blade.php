<div class="modal fade" id="editStockHandleModal-{{ $stock->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('stock-handle.update', $stock->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Stok Sparepart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Pilih Supplier</label>
                        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $stock->supplier_id == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Sparepart</label>
                        <select name="sparepart_id" class="form-select @error('sparepart_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Sparepart --</option>
                            @foreach ($spareparts as $sparepart)
                                <option value="{{ $sparepart->id }}" {{ $stock->sparepart_id == $sparepart->id ? 'selected' : '' }}>
                                    {{ $sparepart->name }} ({{ $sparepart->code_part }})
                                </option>
                            @endforeach
                        </select>
                        @error('sparepart_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah Stok (Quantity)</label>
                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                            value="{{ old('quantity', $stock->quantity) }}" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Beli</label>
                        <input type="number" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror"
                            value="{{ old('purchase_price', $stock->purchase_price) }}" required>
                        @error('purchase_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Diterima</label>
                        <input type="date" name="received_date" class="form-control @error('received_date') is-invalid @enderror"
                            value="{{ old('received_date', $stock->received_date ? \Carbon\Carbon::parse($stock->received_date)->format('Y-m-d') : '') }}">
                        @error('received_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="note" class="form-control @error('note') is-invalid @enderror">{{ old('note', $stock->note) }}</textarea>
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

