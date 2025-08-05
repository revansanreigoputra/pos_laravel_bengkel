@extends('layouts.master')

@section('title', 'Data Jenis Kendaraan')

@section('action')
    @can('jenis-kendaraan.create')
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createJenisKendaraanModal">Tambah Jenis</button>
        @include('pages.jenis_kendaraan.modal-create')
    @endcan
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="jenis-kendaraan-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Nama Jenis</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jenisKendaraans as $index => $jenis)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="">{{ $jenis->nama }}</td>
                            <td class="text-center">
                                @canany(['jenis-kendaraan.update', 'jenis-kendaraan.delete'])
                                    @can('jenis-kendaraan.update')
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editJenisKendaraanModal-{{ $jenis->id }}">Edit</button>
                                        @include('pages.jenis_kendaraan.modal-edit', ['jenisKendaraan' => $jenis])
                                    @endcan

                                    @can('jenis-kendaraan.delete')
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#delete-jenis-{{ $jenis->id }}">Hapus</button>
                                        <x-modal.delete-confirm
                                            id="delete-jenis-{{ $jenis->id }}"
                                            :route="route('jenis-kendaraan.destroy', $jenis->id)"
                                            item="{{ $jenis->nama }}"
                                            title="Hapus Jenis Kendaraan?"
                                            description="Data tidak bisa dikembalikan setelah dihapus." />
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
        $(document).ready(function () {
            $('#jenis-kendaraan-table').DataTable();
        });
    </script>
@endpush
