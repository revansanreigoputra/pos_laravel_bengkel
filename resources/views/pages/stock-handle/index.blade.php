@extends('layouts.master')

@section('title', 'Pembelian Stok Sparepart')

@section('action')
    @can('stock-handle.create')
          <a class="btn btn-warning"  href="{{ route('stock-handle.create') }}">Tambah Pembelian</a>
    @endcan
   {{-- @can('stock-handle.quick-create-sparepart')
    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#quickAddSparepartModal">
        Tambah Sparepart
    </button>
    @include('pages.stock-handle.quick-create-sparepart') --}}
{{-- @endcan --}}

@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Supplier</th>
                                        <th>Sparepart</th>
                                        <th>Kuantitas</th>
                                        <th>Harga Beli</th>
                                        <th>Tanggal Terima</th>
                                        <th>Catatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($stocks as $index => $stock)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $stock->supplier->name }}</td>
                                            <td>{{ $stock->sparepart->name }} ({{ $stock->sparepart->code_part }})</td>
                                            <td>{{ $stock->quantity }}</td>
                                            <td>Rp {{ number_format($stock->purchase_price, 0, ',', '.') }}</td>
                                            <td>{{ $stock->received_date ? \Carbon\Carbon::parse($stock->received_date)->format('d M Y') : '-' }}</td>
                                            <td>{{ $stock->note ?? '-' }}</td>
                                            <td>
                                                @can('stock-handle.update')
                                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#editStockHandleModal-{{ $stock->id }}">Edit</button>
                                                    @include('pages.stock-handle.edit', ['stock' => $stock])
                                                @endcan
                                                @can('stock-handle.delete')
                                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#deleteStockHandleModal-{{ $stock->id }}">Hapus</button>
                                                    <x-modal.delete-confirm id="deleteStockHandleModal-{{ $stock->id }}"
                                                        :route="route('stock-handle.destroy', $stock->id)" item="{{ $stock->name }}"
                                                        title="Hapus Data Pembelian?"
                                                        description="Data Pembelian yang dihapus tidak bisa dikembalikan." />
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No stock records available.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{-- New Note --}}
                        <div class="alert alert-info mt-3" role="alert">
                            <b>Penting:</b> Jika sudah melakukan pembelian, harap periksa kembali pada bagian **Master Data Sparepart** untuk memastikan harga jual dan diskon telah diperbarui.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection