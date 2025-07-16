<div class="modal fade" id="editSparepartModal-{{ $sparepart->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('sparepart.update', $sparepart->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Sparepart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Sparepart</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ old('name', $sparepart->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kode Sparepart</label>
                        <input type="text" class="form-control @error('code_part') is-invalid @enderror"
                            name="code_part" value="{{ old('code_part', $sparepart->code_part) }}" required>
                        @error('code_part')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    

                    {{-- <div class="mb-3">
                        <label class="form-label">Harga Beli</label>
                        <input type="number" class="form-control @error('purchase_price') is-invalid @enderror"
                            name="purchase_price" value="{{ old('purchase_price', $sparepart->purchase_price) }}"
                            required>
                        @error('purchase_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    <div class="mb-3">
                        <label class="form-label">Harga Jual</label>
                        <input type="number" class="form-control @error('selling_price') is-invalid @enderror"
                            name="selling_price" value="{{ old('selling_price', $sparepart->selling_price) }}"
                            required>
                        @error('selling_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Expired Date</label>
                        <input type="date" class="form-control @error('expired_date') is-invalid @enderror"
                            name="expired_date"
                            value="{{ old('expired_date', $sparepart->expired_date ? \Carbon\Carbon::parse($sparepart->expired_date)->format('Y-m-d') : '') }}">
                        @error('expired_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                            name="quantity" value="{{ old('quantity', $sparepart->quantity) }}"
                            required>
                        @error('quantity')
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
