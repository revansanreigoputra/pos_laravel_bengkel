<div class="modal fade" id="editJenisKendaraanModal-{{ $jenisKendaraan->id }}" tabindex="-1">
    <div class="modal-dialog" role="document">
        <form action="{{ route('jenis-kendaraan.update', $jenisKendaraan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jenis Kendaraan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Jenis</label>
                        <input type="text" class="form-control" name="nama" value="{{ old('nama', $jenisKendaraan->nama) }}" required>
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
