<table class="table" id="available_table">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Part</th>
            <th>Nama Sparepart</th>
            <th>Kategori</th>
            <th>Stok Tersedia</th>
            <th>Harga Beli Terakhir</th>
            <th>Tgl Kadaluarsa Terdekat</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($spareparts->filter(function($s) { return $s->available_stock > 0; }) as $index => $sparepart)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $sparepart->code_part ?? '-' }}</td>
                <td>{{ $sparepart->name }}</td>
                <td>{{ $sparepart->category->name ?? 'N/A' }}</td>
                <td>
                    <span class="status-badge available">{{ number_format($sparepart->available_stock) }}</span>
                </td>
                <td>
                    @php
                        $latestPurchase = $sparepart->purchaseOrderItems->first();
                    @endphp

                    @if ($latestPurchase)
                        Rp {{ number_format($latestPurchase->purchase_price, 0, ',', '.') }}
                        <br>
                        <small class="text-muted">
                            ({{ \Carbon\Carbon::parse($latestPurchase->created_at)->format('d-m-Y') }})
                        </small>
                    @else
                        -
                    @endif
                </td>
                <td>
                    @php
                        // Ambil item pembelian dengan tanggal kedaluwarsa terdekat yang stoknya > 0 DAN belum kedaluwarsa.
                        $nearestValidExpiredItem = $sparepart->purchaseOrderItems
                            ->filter(function ($item) {
                                return $item->expired_date &&
                                    \Carbon\Carbon::parse($item->expired_date)->isFuture() &&
                                    $item->quantity - $item->sold_quantity > 0;
                            })
                            ->sortBy('expired_date')
                            ->first();
                    @endphp
                    @if ($nearestValidExpiredItem)
                        {{ \Carbon\Carbon::parse($nearestValidExpiredItem->expired_date)->format('d M Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-4">Tidak ada stok sparepart
                    yang
                    tersedia.</td>
            </tr>
        @endforelse
    </tbody>
</table>
