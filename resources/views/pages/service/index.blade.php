@extends('layouts.master')

@section('title', 'Data Service')

@section('action')
    @can('service.create')
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createServiceModal">Tambah Service</button>
        @include('pages.service.modal-create')
    @endcan
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="customers-table" class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Durasi Estimasi</th>
                        <th>Harga Standar</th>
                        <th>Status</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $index => $service)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $service->nama }}</td>
                            <td>{{ $service->jenis }}</td>
                            <td>{{ $service->durasi_estimasi }}</td>
                            <td>Rp {{ number_format($service->harga_standar, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $service->status === 'aktif' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </td>
                            <td>{{ $service->deskripsi }}</td>
                            <td>
                                @canany(['service.update', 'service.delete'])
                                    @can('service.update')
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editServiceModal-{{ $service->id }}">Edit</button>
                                        @include('pages.service.modal-edit', ['service' => $service])
                                    @endcan

                                    @can('service.delete')
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#delete-service-{{ $service->id }}">Hapus</button>
                                        <x-modal.delete-confirm
                                            id="delete-service-{{ $service->id }}"
                                            :route="route('service.destroy', $service->id)"
                                            item="{{ $service->nama }}"
                                            title="Hapus Service?"
                                            description="Data service yang dihapus tidak bisa dikembalikan." />
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
            $('#customers-table').DataTable();
        });
    </script>
@endpush
