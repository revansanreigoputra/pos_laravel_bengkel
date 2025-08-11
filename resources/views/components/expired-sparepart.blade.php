 <table class="table" id="expired_table">
     <thead>
         <tr>
             <th>No</th>
             <th>Kode Part</th>
             <th>Nama Sparepart</th>
             <th>Kategori</th>
             <th>Supplier</th>
             <th>Jumlah Kadaluarsa</th>
             <th>Harga Beli</th>
             <th>Tgl Kadaluarsa</th>
             <th>Catatan</th>
         </tr>
     </thead>
     <tbody>
         @php $expiredCounter = 0; @endphp
         @forelse ($spareparts as $sparepart)
             @foreach ($sparepart->purchaseOrderItems->whereNotNull('expired_date')->where('expired_date', '<', \Carbon\Carbon::today())->where('quantity', '>', 0) as $item)
                 @php $expiredCounter++; @endphp
                 <tr>
                     <td>{{ $expiredCounter }}</td>
                     <td>{{ $sparepart->code_part ?? '-' }}</td>
                     <td>{{ $sparepart->name }}</td>
                     <td>{{ $sparepart->category->name ?? 'N/A' }}</td>
                     <td>{{ $sparepart->supplier->name ?? 'N/A' }}</td>
                     <td>
                         <span class="status-badge expired">{{ number_format($item->quantity) }}</span>
                     </td>
                     <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                     <td>{{ \Carbon\Carbon::parse($item->expired_date)->format('d M Y') }}</td>
                     <td>{{ $item->notes ?? '-' }}</td>
                 </tr>
             @endforeach
         @empty
             <tr>
                 <td colspan="9" class="text-center text-muted py-4">Tidak ada stok sparepart
                     yang kadaluarsa.</td>
             </tr>
         @endforelse
     </tbody>
 </table>
