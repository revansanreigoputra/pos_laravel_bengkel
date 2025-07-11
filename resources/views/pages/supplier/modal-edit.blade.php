<div class="modal fade" id="editModal-{{ $supplier->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('supplier.update', $supplier->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Supplier</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $supplier->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Telp</label>
                        <input type="number" class="form-control" name="phone" value="{{ old('phone', $supplier->phone) }}" required>
                    </div>
{{-- 
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $supplier->email) }}">
                    </div> --}}

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="address">{{ old('address', $supplier->address) }}</textarea>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" name="nama_barang" value="{{ old('nama_barang', $supplier->nama_barang) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe Barang</label>
                        <select class="form-select" name="tipe_barang">
                            <option value="satuan" {{ old('tipe_barang', $supplier->tipe_barang) == 'satuan' ? 'selected' : '' }}>Satuan</option>
                            <option value="set" {{ old('tipe_barang', $supplier->tipe_barang) == 'set' ? 'selected' : '' }}>Set</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" value="{{ old('jumlah', $supplier->jumlah) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" class="form-control" name="harga" value="{{ old('harga', $supplier->harga) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" class="form-control" name="tanggal_masuk" value="{{ old('tanggal_masuk', $supplier->tanggal_masuk ? \Carbon\Carbon::parse($supplier->tanggal_masuk)->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
