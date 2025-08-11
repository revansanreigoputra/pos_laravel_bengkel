                        <table class="table" id="empty_table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Part</th>
                                    <th>Nama Sparepart</th>
                                    <th>Kategori</th>
                                    <th>Harga Jual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($spareparts->filter(function($s) { return $s->available_stock <= 0; }) as $index => $sparepart)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $sparepart->code_part ?? '-' }}</td>
                                        <td>{{ $sparepart->name }}</td>
                                        <td>{{ $sparepart->category->name ?? 'N/A' }}</td>
                                        <td>{{ $sparepart->supplier->name ?? 'N/A' }}</td>
                                        <td>Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Tidak ada sparepart dengan
                                            stok kosong.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
