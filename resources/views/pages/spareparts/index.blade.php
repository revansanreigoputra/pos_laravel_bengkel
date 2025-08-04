@extends('layouts.master')

@section('title', 'Data Sparepart')

@section('action')
    @can('sparepart.create')
        <a href="{{ route('spareparts.create') }}" class="btn btn-primary">Tambah Sparepart</a>
    @endcan
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table id="sparepart-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Part</th>
                            <th>Nama Sparepart</th>
                            <th>Kategori</th>
                            <th>Jumlah Stok</th>
                            <th>Stok Saat Ini</th>
                            <th>Kadaluarsa Terdekat</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($spareparts as $sparepart)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sparepart->code_part ?? '-' }}</td>
                                <td>{{ $sparepart->name }}</td>
                                <td>{{ $sparepart->category->name ?? 'N/A' }}</td>
                                <td>{{ $sparepart->available_stock }}</td> 
                                <td>{{ $sparepart->stock }}</td>
                                <td>
                                    @php
                                        // Ambil item pembelian dengan tanggal kadaluarsa terdekat yang belum kadaluarsa
                                        // Filter juga untuk quantity > 0 agar hanya stok yang masih ada
                                        $nearestExpiredItem = $sparepart->purchaseOrderItems
                                            ->filter(function($item) {
                                                return $item->quantity > 0 && $item->expired_date && $item->expired_date->isFuture();
                                            })
                                            ->sortBy('expired_date')
                                            ->first();
                                    @endphp
                                    @if ($nearestExpiredItem)
                                        {{ $nearestExpiredItem->expired_date->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <ul>
                                        @foreach($sparepart->purchaseOrderItems as $item)
                                            <li>
                                                Rp {{ number_format($item->purchase_price, 0, ',', '.') }} 
                                                ({{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    @if($sparepart->isDiscountActive())
                                        <span class="text-decoration-line-through text-muted">Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}</span><br>
                                        Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }}
                                        <span class="badge bg-success">{{ $sparepart->discount_percentage }}% Off</span>
                                    @else
                                        Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td>
                                    @canany(['sparepart.edit', 'sparepart.delete'])
                                        @can('sparepart.edit')
                                            <a href="{{ route('spareparts.edit', $sparepart->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        @endcan
                                        @can('sparepart.delete')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#delete-sparepart-{{ $sparepart->id }}">
                                                Hapus
                                            </button>
                                            <x-modal.delete-confirm
                                                id="delete-sparepart-{{ $sparepart->id }}"
                                                :route="route('spareparts.destroy', $sparepart->id)"
                                                item="{{ $sparepart->name }}"
                                                title="Hapus Sparepart?"
                                                description="Data sparepart yang dihapus tidak bisa dikembalikan." />
                                        @endcan
                                    @else
                                        <span class="text-muted">Tidak ada aksi</span>
                                    @endcanany
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $spareparts->links() }}
            </div>

            <p class="mt-4 text-muted">
                **Perhatian:** Untuk memastikan sparepart dapat dijual, pastikan Anda telah mengisi **Harga Jual** dan mengatur **Diskon** (jika ada) melalui tombol "Edit" pada setiap baris data.
            </p>
        </div>
    </div>
@endsection

@push('addon-script')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#sparepart-table').DataTable({
            "paging": true,      // Aktifkan paginasi
            "lengthChange": true, // Izinkan perubahan jumlah entri per halaman
            "searching": true,   // Aktifkan fitur pencarian
            "ordering": true,    // Aktifkan pengurutan kolom
            "info": true,        // Tampilkan informasi halaman
            "autoWidth": false,  // Nonaktifkan penyesuaian lebar kolom otomatis
            "responsive": true,  // Aktifkan responsif
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json" // Bahasa Indonesia
            }
        });
    });
</script>
@endpush
