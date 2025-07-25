@extends('layouts.master')

@section('title', 'Pembelian Stok Sparepart')

@section('action')
    @can('stock-handle.create')
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStockHandleModal">Tambah Pembelian</button>
        @include('pages.stock-handle.create')
    @endcan
   @can('stock-handle.quick-create-sparepart')
    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#quickAddSparepartModal">
        Tambah Sparepart
    </button>
    @include('pages.stock-handle.quick-create-sparepart')
@endcan

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
                                            <td>{{ $stock->sparepart?->name ?? 'N/A' }} ({{ $stock->sparepart?->code_part ?? 'N/A' }})</td>
                                            <td>{{ $stock->sparepart?->name ?? 'N/A' }} ({{ $stock->sparepart?->code_part ?? 'N/A' }})</td>
                                            <td>{{ $stock->sparepart?->name ?? 'N/A' }} ({{ $stock->sparepart?->code_part ?? 'N/A' }})</td>
                                            <td>{{ $stock->sparepart?->name ?? 'N/A' }} ({{ $stock->sparepart?->code_part ?? 'N/A' }})</td>
                                            <td>{{ $stock->sparepart?->name ?? 'N/A' }} ({{ $stock->sparepart?->code_part ?? 'N/A' }})</td>
                                            <td>{{ $stock->sparepart?->name ?? 'N/A' }} ({{ $stock->sparepart?->code_part ?? 'N/A' }})</td>
                                            <td>{{ $stock->sparepart?->name ?? 'N/A' }} ({{ $stock->sparepart?->code_part ?? 'N/A' }})</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No stock records available.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
