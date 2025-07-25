<!-- Quick Add Sparepart Modal -->
<div class="modal fade" id="quickAddSparepartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
    <form action="{{ route('stock-handle.quick-store') }}" method="POST" id="quickAddSparepartForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Sparepart Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama Sparepart</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Kode Sparepart</label>
                    <input type="text" name="code_part" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
