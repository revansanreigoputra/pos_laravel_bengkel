<div class="modal fade" id="editModal-{{ $transaction->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel-{{ $transaction->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('transaction.update', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel-{{ $transaction->id }}">Edit Transaksi #{{ $transaction->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Informasi Umum --}}
                    <div class="mb-3">
                        <label class="form-label">Nama Pelanggan</label>
                        <input type="text" class="form-control" name="customer_name" value="{{ old('customer_name', $transaction->customer_name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Kendaraan</label>
                        <input type="text" class="form-control" name="vehicle_number" value="{{ old('vehicle_number', $transaction->vehicle_number) }}" required>
                    </div>

                    {{-- Edit Item --}}
                    <h6>Detail Item:</h6>
                    <div id="items-container-edit-{{ $transaction->id }}"> {{-- Unique ID for each edit modal's container --}}
                        @foreach ($transaction->items as $index => $item)
                            <div class="item-row mb-3 row align-items-end">
                                <div class="col-md-5">
                                    <label for="edit-item-select-{{ $transaction->id }}-{{ $index }}" class="form-label">Item</label>
                                    <select name="items[{{ $index }}][item_full_id]" class="form-select item-select" id="edit-item-select-{{ $transaction->id }}-{{ $index }}" required>
                                        <option value="">Pilih Item</option>
                                        <optgroup label="Layanan">
                                            @foreach ($services as $service)
                                                <option value="service-{{ $service->id }}"
                                                    data-price="{{ $service->harga_standar }}" {{-- Diganti ke harga_standar --}}
                                                    @if ($item->item_type === 'service' && $item->item_id == $service->id) selected @endif>
                                                    {{ $service->nama }} (Rp {{ number_format($service->harga_standar, 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                        <optgroup label="Sparepart">
                                            @foreach ($spareparts as $spare)
                                                <option value="sparepart-{{ $spare->id }}"
                                                    data-price="{{ $spare->final_selling_price }}" {{-- Diganti ke final_selling_price --}}
                                                    @if ($item->item_type === 'sparepart' && $item->item_id == $spare->id) selected @endif>
                                                    {{ $spare->name }} 
                                                    @if($spare->isDiscountActive())
                                                        (Normal: Rp {{ number_format($spare->selling_price, 0, ',', '.') }}) 
                                                        Diskon {{ $spare->discount_percentage }}% - 
                                                        Final: Rp {{ number_format($spare->final_selling_price, 0, ',', '.') }}
                                                    @else
                                                        (Rp {{ number_format($spare->selling_price, 0, ',', '.') }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    {{-- Hidden inputs to store item_type and item_id separately --}}
                                    <input type="hidden" name="items[{{ $index }}][item_type]" class="item-type-input" value="{{ $item->item_type }}">
                                    <input type="hidden" name="items[{{ $index }}][item_id]" class="item-id-input" value="{{ $item->item_id }}">
                                </div>

                                <div class="col-md-3">
                                    <label for="edit-price-{{ $transaction->id }}-{{ $index }}" class="form-label">Harga</label>
                                    <input type="number" name="items[{{ $index }}][price]" class="form-control price-input"
                                        id="edit-price-{{ $transaction->id }}-{{ $index }}"
                                        value="{{ old('items.' . $index . '.price', $item->price) }}" step="0.01" required>
                                </div>

                                <div class="col-md-2">
                                    <label for="edit-qty-{{ $transaction->id }}-{{ $index }}" class="form-label">Qty</label>
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control"
                                        id="edit-qty-{{ $transaction->id }}-{{ $index }}"
                                        min="1" value="{{ old('items.' . $index . '.quantity', $item->quantity) }}" required>
                                </div>

                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger remove-item w-100">Hapus</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-secondary mt-3 add-item-edit" data-transaction-id="{{ $transaction->id }}">Tambah Item</button>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>