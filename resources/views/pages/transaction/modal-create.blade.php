{{-- This is a simplified example of modal-create.blade.php --}}
{{-- You need to ensure your actual modal structure reflects this --}}

<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-labelledby="createTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTransactionModalLabel">Tambah Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transaction.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="vehicle_number" class="form-label">Nomor Kendaraan</label>
                        <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" required>
                    </div>

                    <h6>Detail Item:</h6>
                    <div id="items-container">
                        <div class="row mb-2 item-row align-items-end">
                            <div class="col-md-5">
                                <label for="item-0" class="form-label">Item</label>
                                <select class="form-select item-select" name="items[0][item_full_id]" id="item-0" required>
                                    <option value="">Pilih Item</option>
                                    <optgroup label="Layanan">
                                        @foreach ($services as $service)
                                            <option value="service-{{ $service->id }}" data-price="{{ $service->harga_standar }}">
                                                {{ $service->nama }} (Rp {{ number_format($service->harga_standar, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Sparepart">
                                        @foreach ($spareparts as $sparepart)
                                            <option value="sparepart-{{ $sparepart->id }}" data-price="{{ $sparepart->final_selling_price }}">
                                                {{ $sparepart->name }} 
                                                @if($sparepart->isDiscountActive())
                                                    (Normal: Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}) 
                                                    Diskon {{ $sparepart->discount_percentage }}% - 
                                                    Final: Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }}
                                                @else
                                                    (Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                {{-- Hidden inputs to send item_type and item_id separately to the backend --}}
                                <input type="hidden" class="item-type-input" name="items[0][item_type]">
                                <input type="hidden" class="item-id-input" name="items[0][item_id]">
                            </div>
                            <div class="col-md-3">
                                <label for="price-0" class="form-label">Harga</label>
                                {{-- Remove readonly if you want to allow manual editing, otherwise keep it --}}
                                <input type="number" class="form-control price-input" name="items[0][price]" id="price-0" step="0.01"  required>
                            </div>
                            <div class="col-md-2">
                                <label for="qty-0" class="form-label">Qty</label>
                                <input type="number" class="form-control" name="items[0][quantity]" id="qty-0" value="1" min="1" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-item w-100">X</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary mt-3" id="add-item">Tambah Item</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>