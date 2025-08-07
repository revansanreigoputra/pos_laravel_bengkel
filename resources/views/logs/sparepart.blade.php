@extends('layouts.master')

@section('title', 'Log Sparepart')

@section('content')
<div class="container">
    <form method="GET" class="mb-4 row align-items-end">
        <div class="col-md-3">
            <label for="start_date" class="form-label">Dari Tanggal:</label>
            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">Sampai Tanggal:</label>
            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
        </div>
    </form>

    <div class="table-responsive">
        <table id="sparepart-table" class="table table-bordered table-striped w-100">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Sparepart</th>
                    <th>Kode Sparepart</th>
                    <th>Stok Awal</th>
                    <th>Barang Masuk</th>
                    <th>Barang Keluar</th>
                    <th>Stok Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($spareparts as $index => $s)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $s->name }}</td>
                        <td>{{ $s->code_part }}</td>
                        <td>{{ $s->stok_awal }}</td>
                        <td>{{ $s->barang_masuk }}</td>
                        <td>{{ $s->barang_keluar }}</td>
                        <td>{{ $s->stok_akhir }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Data tidak tersedia untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#sparepart-table').DataTable();
        });
    </script>
@endpush