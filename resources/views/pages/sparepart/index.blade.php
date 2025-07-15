{{-- resources/views/spareparts/index.blade.php --}}
@extends('layouts.master')

@section('title', 'Data Sparepart')
 
@section('action') 
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSparepartModal">Tambah Sparepart</button>
        @include('pages.sparepart.create')
    
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
                                    <th>Supplier</th>
                                    <th>Stok</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($spareparts as $index => $sparepart)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $sparepart->code_part }}</td>
                                    <td>{{ $sparepart->name }}</td>
                                    <td>{{ $sparepart->supplier->name ?? '-' }}</td>
                                    <td>{{ $sparepart->quantity }}</td>
                                    <td>{{ number_format($sparepart->purchase_price, 0, ',', '.') }}</td>
                                    <td>{{ number_format($sparepart->selling_price, 0, ',', '.') }}</td>
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

