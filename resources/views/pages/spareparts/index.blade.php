@extends('layouts.master')

@section('title', 'Data Sparepart')

@section('action')
    @can('sparepart.create')
        <a class="btn btn-primary" href="{{ route('spareparts.create') }}">
            <i class="fas fa-plus-circle me-2"></i> Tambah Sparepart
        </a>
    @endcan
    <button class="btn btn-info">Unduh</button>
@endsection


@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body table-responsive">
                    <table id="sparepartTable" class="table table-striped dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Part</th>
                                <th>Nama Sparepart</th>
                                <th>Kategori</th>
                                <th>Stok Total</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($spareparts as $index => $sparepart)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $sparepart->code_part }}</td>
                                    <td>{{ $sparepart->name }}</td>
                                    <td>{{ $sparepart->category->name ?? 'Tidak Diketahui' }}</td>
                                    {{-- Mengakses stok total dari hasil withSum --}}
                                    <td>{{ $sparepart->purchase_order_items_sum_quantity ?? 0 }}</td>
                                    {{-- Mengakses harga beli rata-rata dari hasil withAvg --}}
                                    <td>Rp{{ number_format($sparepart->purchase_order_items_avg_purchase_price ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($sparepart->isDiscountActive())
                                            <span class="text-danger fw-bold">
                                                Rp{{ number_format($sparepart->final_selling_price, 0, ',', '.') }}
                                            </span>
                                            <small class="text-muted text-decoration-line-through">
                                                Rp{{ number_format($sparepart->selling_price, 0, ',', '.') }}
                                            </small>
                                            <span class="badge bg-success ms-1">{{ $sparepart->discount_percentage }}% OFF</span>
                                        @else
                                            Rp{{ number_format($sparepart->selling_price, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                            @can('sparepart.update')
                                                <a class="btn btn-warning btn-sm" href="{{ route('spareparts.edit', $sparepart->id) }}">
                                                    Edit
                                                </a>
                                            @endcan

                                            @can('sparepart.delete')
                                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#deleteSparepartModal-{{ $sparepart->id }}">Hapus</button>
                                               <x-modal.delete-confirm
                                                    id="deleteSparepartModal-{{ $sparepart->id }}"
                                                    :route="route('spareparts.destroy', $sparepart->id)"
                                                    item="{{ $sparepart->name }}"
                                                    title="Hapus Sparepart?"
                                                    description="Data sparepart yang dihapus tidak bisa dikembalikan." />
                                            @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Belum ada sparepart.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info mt-3 mx-3" role="alert">
                    <b>Perhatian:</b> Untuk memastikan sparepart dapat dijual, pastikan Anda telah memperbarui **Harga Jual** dan mengatur **Diskon** (jika ada) melalui tombol "Edit" pada setiap baris data.
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3">
        {{ $spareparts->links() }}
    </div>
</div>
@endsection

@push('addon-script')
<script>
    $(document).ready(function () {
        $('#sparepartTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                },
                zeroRecords: "Data tidak ditemukan"
            },
            pageLength: 10,
            responsive: true,
            autoWidth: false,
        });
    });
</script>
@endpush
