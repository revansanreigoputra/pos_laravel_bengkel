<div class="modal fade" id="editServiceModal-{{ $service->id }}" tabindex="-1">
    <div class="modal-dialog" role="document">
        <form action="{{ route('service.update', $service->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" name="nama" value="{{ old('nama', $service->nama) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <input type="text" class="form-control" name="jenis" value="{{ old('jenis', $service->jenis) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Durasi Estimasi</label>
                        <input type="text" class="form-control" name="durasi_estimasi" value="{{ old('durasi_estimasi', $service->durasi_estimasi) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Standar (Rp)</label>
                        <input type="number" class="form-control" name="harga_standar" value="{{ old('harga_standar', $service->harga_standar) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="aktif" {{ $service->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $service->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi">{{ old('deskripsi', $service->deskripsi) }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
