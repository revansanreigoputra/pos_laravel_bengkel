{{-- resources/views/spareparts/index.blade.php --}}
@extends('layouts.master')

@section('title', 'Data Sparepart')

@section('action')
    @can('sparepart.create')
        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#createSparepartModal">
            Tambah Sparepart
        </button>
        @include('pages.sparepart.create')
    @endcan
    <button class="btn btn-primary">Unduh</button>
@endsection


@section('content')
<div class="container-fluid">
   

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                     

                    <div class="table-responsive">
                        <table id="spareparts-table" class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Part</th>
                                    <th>Nama Sparepart</th>
                                     <th>Kategori</th>
                                    <th>Stok Total</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th> {{-- Judul kolom tetap --}}
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($spareparts as $index => $sparepart)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $sparepart->code_part }}</td>
                                    <td>{{ $sparepart->name }}</td>
                                    <td>{{ $sparepart->category->name ?? 'Tidak Diketahui' }}</td>
                                            {{-- Menampilkan kategori sparepart --}}
                                            {{-- Jika kategori tidak ada, tampilkan 'Tidak Diketahui' --}}
                                            
                                    <td>{{ $sparepart->quantity }}</td>
                                    <td>{{ number_format($sparepart->purchase_price, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($sparepart->isDiscountActive())
                                            <span class="text-danger fw-bold">
                                                {{ number_format($sparepart->final_selling_price, 0, ',', '.') }}
                                            </span>
                                            <small class="text-muted text-decoration-line-through">
                                                {{ number_format($sparepart->selling_price, 0, ',', '.') }}
                                            </small>
                                            <span class="badge bg-success ms-1">{{ $sparepart->discount_percentage }}% OFF</span>
                                        @else
                                            {{-- Jika tidak ada diskon aktif, tampilkan harga normal --}}
                                            {{ number_format($sparepart->selling_price, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @can('sparepart.update')
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editSparepartModal-{{ $sparepart->id }}">Edit</button>
                                            @include('pages.sparepart.edit', ['sparepart' => $sparepart])
                                        @endcan

                                        @can('sparepart.delete')
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteSparepartModal-{{ $sparepart->id }}">Hapus</button>
                                            <x-modal.delete-confirm
                                                id="deleteSparepartModal-{{ $sparepart->id }}"
                                                :route="route('sparepart.destroy', $sparepart->id)"
                                                item="{{ $sparepart->name }}"
                                                title="Hapus Sparepart?"
                                                description="Data sparepart yang dihapus tidak bisa dikembalikan." />
                                        @endcan
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- New Note --}}
                    <div class="alert alert-info mt-3" role="alert">
                        <b>Perhatian:</b> Untuk memastikan sparepart dapat dijual, pastikan Anda telah memperbarui **Harga Jual** dan mengatur **Diskon** (jika ada) melalui tombol "Edit" pada setiap baris data.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection