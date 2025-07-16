<div class="modal fade" id="createSparepartModal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <form action="{{ route('sparepart.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Stok Barang Sparepart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Sparepart</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode Sparepart</label>
                        <input type="text" class="form-control" name="code_part" required>
                    </div>

                   
                    {{-- <div class="mb-3">
                        <label class="form-label">Harga Beli</label>
                        <input type="number" class="form-control" name="purchase_price" required>
                    </div> --}}

                    <div class="mb-3">
                        <label class="form-label">Harga Jual</label>
                        <input type="number" class="form-control" name="selling_price" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expired Date</label>
                        <input type="date" class="form-control" name="expired_date" required>
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
