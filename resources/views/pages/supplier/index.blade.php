@extends('layouts.master')

@section('title', 'Data Supplier')

@section('action')
    @can('supplier.create')
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDataModal">Tambah Data</button>
        @include('pages.supplier.modal-create')
    @endcan
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="suppliers-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama Supplier</th>
                            <th class="text-center">No. Telp</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Alamat</th>
                            <th class="text-center">Terdaftar</th>
                            <th class="text-center">Catatan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $supplier->name }}</td>
                                <td class="text-center">{{ $supplier->phone ?? '-' }}</td>
                                <td class="text-center">{{ $supplier->email ?? '-' }}</td>
                                <td class="text-center">{{ $supplier->address ?? '-' }}</td>
                                <td class="text-center">{{ $supplier->created_at->format('d-m-Y') }}</td>
                                <td class="text-center">{{ $supplier->note ?? '-' }}</td>
                                <td class="text-center">
                                    @canany(['supplier.update', 'supplier.delete'])
                                        @can('supplier.update')
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#editModal-{{ $supplier->id }}">
                                                Edit
                                            </button>
                                            @include('pages.supplier.modal-edit')
                                        @endcan
                                        @can('supplier.delete')
                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#delete-supplier-{{ $supplier->id }}">
                                                Hapus
                                            </button>
                                            <x-modal.delete-confirm 
                                                id="delete-supplier-{{ $supplier->id }}" 
                                                :route="route('supplier.destroy', $supplier->id)"
                                                item="{{ $supplier->name }}" 
                                                title="Hapus Supplier?"
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
            $('#suppliers-table').DataTable();
        });
    </script>
@endpush
