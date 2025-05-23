@extends('layouts.master')

@section('title', 'Role & Permission')

@section('content')
    <div class="card">
        <div class="card-body">
            <div id="table-default" class="table-responsive">
                <table id="roles-table" class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Role</th>
                            @can('role.update')
                                <th>Aksi</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                @can('role.update')
                                    <td>
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">
                                            Edit Permission
                                        </a>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada data role</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#roles-table').DataTable({
                // opsi konfigurasi tambahan bisa ditambah di sini, misal:
                // paging: true,
                // searching: true,
                // ordering: true,
            });
        });
    </script>
@endpush
