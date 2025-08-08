@extends('layouts.master')

@section('title', 'Detail Barang Masuk & Keluar Sparepart')

@section('content')
<div class="card">
    <div class="card-body">
        <h3 class="card-title">Detail Barang Masuk & Keluar Sparepart</h3>
        <form method="GET" class="mb-4 row align-items-end">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Dari Tanggal:</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Sampai Tanggal:</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="jenis" class="form-label">Jenis Transaksi:</label>
                <select name="jenis" id="jenis" class="form-control">
                    <option value="">Semua</option>
                    <option value="masuk" {{ request('jenis') == 'masuk' ? 'selected' : '' }}>Barang Masuk</option>
                    <option value="keluar" {{ request('jenis') == 'keluar' ? 'selected' : '' }}>Barang Keluar</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>

        <div class="table-responsive">
            <table id="sparepart-detail-table" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Invoice</th>
                        <th>
                            Nama Supplier / Nama Pelanggan
                        </th>
                        <th>
                            Tanggal Masuk / Tanggal Keluar
                        </th>
                        <th>Jenis</th>
                        <th>Nama Sparepart</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row['no_invoice'] }}</td>
                            <td>
                                @if(isset($row['supplier']))
                                    {{ $row['supplier'] }}
                                @else
                                    {{ $row['customer'] ?? '-' }}
                                @endif
                            </td>
                            <td>
                                @if(isset($row['tanggal_masuk']))
                                    {{ $row['tanggal_masuk'] }}
                                @elseif(isset($row['tanggal_keluar']))
                                    {{ $row['tanggal_keluar'] }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $row['jenis'] }}</td>
                            <td>{{ $row['sparepart'] }}</td>
                            <td>{{ $row['quantity'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Data tidak tersedia untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <small class="text-muted">
                <strong>Catatan:</strong> 
                Pada transaksi <strong>Barang Masuk</strong>, kolom "Nama Supplier / Nama Pelanggan" menampilkan <strong>nama supplier</strong>. 
                Sedangkan pada transaksi <strong>Barang Keluar</strong>, kolom tersebut menampilkan <strong>nama pelanggan</strong>.
            </small>
        </div>
    </div>
</div>
@endsection

@push('addon-script')
<script>
    $(document).ready(function() {
        $('#sparepart-detail-table').DataTable();
    });
</script>
@endpush