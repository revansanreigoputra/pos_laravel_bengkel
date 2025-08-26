@extends('layouts.master')

@section('title', 'Data Service')

@section('action')
    @can('service.create')
        <a href="{{ route('service.modal-create') }}" class="btn btn-primary">
            Tambah Service
        </a>
    @endcan
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table id="services-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama</th>
                            <th class="text-center">Jenis Kendaraan</th>
                            <th class="text-center">Durasi Estimasi</th>
                            <th class="text-center">Harga Standar</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Deskripsi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $index => $service)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ $service->nama }}</td>
                                <td class="text-center">
                                    {{ $service->jenisKendaraan->nama ?? '-' }}</td>
                                <td class="text-center">{{ $service->durasi_estimasi }}</td>
                                <td class="text-center">Rp
                                    {{ number_format($service->harga_standar, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $service->status === 'aktif' ? 'success' : 'secondary' }} text-white p-2">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{ Str::limit($service->deskripsi, 50) }}
                                </td>
                                <td class="d-flex flex-wrap gap-2">
                                    @can('service.update')
                                        <a href="{{ route('service.modal-edit', $service->id) }}"
                                            class="btn btn-sm btn-warning">
                                            Edit
                                        </a>
                                        <form action="{{ route('service.changeStatus', $service->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            <select name="status" onchange="this.form.submit()"
                                                class="form-select form-select-sm d-inline w-auto">
                                                <option value="aktif" {{ $service->status == 'aktif' ? 'selected' : '' }}>
                                                    Aktif</option>
                                                <option value="nonaktif"
                                                    {{ $service->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif
                                                </option>
                                            </select>
                                        </form>
                                    @endcan
                                    @can('service.delete')
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#delete-service-{{ $service->id }}">
                                            Hapus
                                        </button>
                                        <x-modal.delete-confirm id="delete-service-{{ $service->id }}" :route="route('service.destroy', $service->id)"
                                            item="{{ $service->nama }}" title="Hapus Service?"
                                            description="Service yang dihapus tidak bisa dikembalikan." />
                                    @endcan
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
            $('#services-table').DataTable();
        });
    </script>
@endpush
