<div class="modal fade" id="createServiceModal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <form action="{{ route('service.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Service</label>
                        <input type="text" class="form-control" name="nama" value="{{ old('nama') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Kendaraan</label>
                        <select class="form-select" name="jenis_kendaraan_id" required>
                            <option value="">-- Pilih Jenis Kendaraan --</option>
                            @foreach($jenisKendaraans as $jenis)
                                <option value="{{ $jenis->id }}" {{ old('jenis_kendaraan_id') == $jenis->id ? 'selected' : '' }}>
                                    {{ $jenis->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Durasi Estimasi</label>
                        <input type="text" class="form-control" name="durasi_estimasi" value="{{ old('durasi_estimasi') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Standar (Rp)</label>
                        <input type="number" class="form-control" name="harga_standar" value="{{ old('harga_standar') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi">{{ old('deskripsi') }}</textarea>
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
