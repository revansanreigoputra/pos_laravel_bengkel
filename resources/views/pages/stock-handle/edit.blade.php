<div class="modal fade" id="editStockHandleModal-{{ $stock->id }}" tabindex="-1" role="dialog" aria-labelledby="editStockHandleModalLabel-{{ $stock->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> {{-- Menggunakan modal-lg untuk ruang lebih --}}
        {{-- Penting: Tambahkan enctype="multipart/form-data" untuk upload file --}}
        <form action="{{ route('stock-handle.update', $stock->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStockHandleModalLabel-{{ $stock->id }}">Edit Stok Sparepart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Pilih Supplier</label>
                        <select name="supplier_id" class="form-select select2-modal @error('supplier_id') is-invalid @enderror" data-modal-id="editStockHandleModal-{{ $stock->id }}" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $stock->supplier_id) == $supplier->id ? 'selected' : '' }}>
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
                        <select name="sparepart_id" class="form-select select2-modal @error('sparepart_id') is-invalid @enderror" data-modal-id="editStockHandleModal-{{ $stock->id }}" required>
                            <option value="">-- Pilih Sparepart --</option>
                            @foreach ($spareparts as $sparepart)
                                <option value="{{ $sparepart->id }}" {{ old('sparepart_id', $stock->sparepart_id) == $sparepart->id ? 'selected' : '' }}>
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
                            value="{{ old('quantity', $stock->quantity) }}" required min="1">
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Beli</label>
                        <input type="number" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror"
                            value="{{ old('purchase_price', $stock->purchase_price) }}" required min="0">
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

                    {{--- START: Tambahan untuk Invoice ---}}
                    <div class="mb-3">
                        <label for="invoice_number-{{ $stock->id }}" class="form-label">Nomor Invoice</label>
                        <input type="text" name="invoice_number" id="invoice_number-{{ $stock->id }}" class="form-control @error('invoice_number') is-invalid @enderror"
                            value="{{ old('invoice_number', $stock->invoice_number) }}" placeholder="Contoh: INV-202307001">
                        @error('invoice_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="invoice_file-{{ $stock->id }}" class="form-label">Upload Invoice (PDF, JPG, JPEG, PNG)</label>
                        <input type="file" name="invoice_file" id="invoice_file-{{ $stock->id }}" class="form-control-file @error('invoice_file') is-invalid @enderror">
                        <small class="form-text text-muted">Maksimal ukuran file: 2MB. Kosongkan jika tidak ingin mengubah.</small>
                        @error('invoice_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if($stock->invoice_file_path)
                            <div class="mt-2">
                                <p>File invoice saat ini:
                                    <a href="{{ route('stock-handle.download-invoice', $stock->id) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-download"></i> Download Invoice
                                    </a>
                                    <span class="ms-3">
                                        <input type="checkbox" name="remove_invoice_file" value="1" id="remove_invoice_file-{{ $stock->id }}">
                                        <label for="remove_invoice_file-{{ $stock->id }}" class="form-check-label">Hapus File Ini</label>
                                    </span>
                                </p>
                            </div>
                        @endif
                    </div>
                    {{--- END: Tambahan untuk Invoice ---}}

                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="note" class="form-control @error('note') is-invalid @enderror" placeholder="Contoh: Satuan Kuantitas: ...">{{ old('note', $stock->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Select2 untuk setiap modal edit
        // Karena modal dibuat dinamis dengan ID unik, kita perlu inisialisasi setiap kali modal dibuka
        $('#editStockHandleModal-{{ $stock->id }}').on('shown.bs.modal', function () {
            $(this).find('.select2-modal').each(function() {
                var elementId = $(this).attr('id');
                if (!$(this).data('select2')) { // Pastikan belum diinisialisasi
                    $(this).select2({
                        placeholder: $(this).find('option:first').text(),
                        allowClear: true,
                        dropdownParent: $(this).closest('.modal-content') // Penting untuk posisi dropdown
                    });
                }
            });
        });

        // Contoh bagaimana menangani error validasi dari server saat modal dibuka
        // Jika ada error, modal akan otomatis muncul lagi
        @if ($errors->any() && old('_token')) {{-- Periksa apakah ada error dan form ini yang disubmit (melalui old('_token')) --}}
            @php
                // Coba temukan ID stok yang sedang diedit dari error (jika memungkinkan)
                // Ini sedikit tricky karena form di dalam loop, jadi $stock->id harus sesuai
                $currentStockId = $stock->id; // Diasumsikan $stock tersedia di loop blade ini
                $isCurrentModalError = false;
                foreach ($errors->keys() as $key) {
                    // Cek apakah ada error yang spesifik untuk form ini
                    // Contoh sederhana, bisa lebih kompleks tergantung cara Anda handle error
                    if (str_contains($key, 'supplier_id') || str_contains($key, 'quantity') || str_contains($key, 'invoice_number')) {
                        $isCurrentModalError = true;
                        break;
                    }
                }
            @endphp
            @if($isCurrentModalError)
                // Hanya tampilkan modal yang sesuai dengan error
                $('#editStockHandleModal-{{ $stock->id }}').modal('show');
            @endif
        @endif
    });
</script>
@endpush