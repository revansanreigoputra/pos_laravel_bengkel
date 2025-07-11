@extends('layouts.master')

@section('title', 'Data Supplier')

@section('action')
    @can('category.create')
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDataModal">Tambah Data</button>
        @include('pages.supplier.modal-create')
    @endcan
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="categories-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Supplier</th>
                            <th>No. Telp</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th>Nama Barang</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Tanggal Masuk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->email ?? '-' }}</td>
                                <td>{{ $supplier->address ?? '-' }}</td>
                                <td>{{ $supplier->nama_barang ?? '-' }}</td>
                                <td>{{ ucfirst($supplier->tipe_barang) }}</td>
                                <td>{{ $supplier->jumlah }}</td>
                                <td>Rp {{ number_format($supplier->harga, 0, ',', '.') }}</td>
                                <td>{{ \Carbon\Carbon::parse($supplier->tanggal_masuk)->format('d-m-Y') }}</td>
                                <td>
                                    @canany(['supplier.update', 'supplier.delete'])
                                        @can('supplier.update')
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#editModal-{{ $supplier->id }}">
                                                <!-- Icon Edit -->
                                                Edit
                                            </button>
                                            @include('pages.supplier.modal-edit')
                                        @endcan
                                        @can('supplier.delete')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#delete-supplier-{{ $supplier->id }}">
                                                <!-- Icon Delete -->
                                                Hapus
                                            </button>
                                            <x-modal.delete-confirm id="delete-supplier-{{ $supplier->id }}" :route="route('supplier.destroy', $supplier->id)"
                                                item="{{ $supplier->name }}" title="Hapus Supplier?"
                                                description="Supplier yang dihapus tidak bisa dikembalikan." />
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
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#categories-table').DataTable();
        });
    </script>
@endpush
