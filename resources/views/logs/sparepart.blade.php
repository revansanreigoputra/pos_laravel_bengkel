@extends('layouts.master')

@section('title', 'Detail Barang Masuk & Keluar Sparepart')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">* Lengkapi filter pencarian
                <br>
                <span class="text-muted small">untuk memilah data laporan </span>

            </h3>
            <a href="{{ route('logs.stok.export', ['tipe' => request('tipe'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                class="btn btn-outline-success">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                    <path d="M10 12l4 4m0 -4l-4 4"></path>
                    <path d="M12 8v8m-2 -2l2 2l2 -2"></path>
                </svg>
                Export</a>
        </div>
        <div class="card-body">

            <form method="GET" class="mb-4 row align-items-end">
                <div class="col-md-3">
                    <label for="tipe" class="form-label">Tipe Laporan:</label>
                    <select name="tipe" id="tipe" class="form-control">
                        <option value="stok_saat_ini" {{ request('tipe', $tipe) == 'stok_saat_ini' ? 'selected' : '' }}>Stok
                            Tersedia</option>
                        <option value="stok_masuk" {{ request('tipe', $tipe) == 'stok_masuk' ? 'selected' : '' }}>Stok Masuk
                        </option>
                        <option value="stok_keluar" {{ request('tipe', $tipe) == 'stok_keluar' ? 'selected' : '' }}>Stok
                            Keluar</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Dari Tanggal:</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                        class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Sampai Tanggal:</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                        class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                </div>
                <div class="col-md-1">

                </div>
            </form>

            <div class="table-responsive">
                <table id="sparepart-detail-table" class="table table-bordered table-striped w-100">
                    <thead>
                        @if ($tipe == 'stok_saat_ini')
                            <tr>
                                <th>No</th>
                                <th>Nama Sparepart</th>
                                <th>Kategori</th>
                                <th>Stok Tersedia</th>
                            </tr>
                        @elseif($tipe == 'stok_masuk')
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Sparepart</th>
                                <th>Kategori</th>
                                <th>Jumlah Masuk</th>
                            </tr>
                        @elseif($tipe == 'stok_keluar')
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Sparepart</th>
                                <th>Kategori</th>
                                <th>Jumlah Keluar</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            <tr>
                                @if ($tipe == 'stok_saat_ini')
                                    <td>{{ $row['no'] }}</td>
                                    <td>{{ $row['nama_sparepart'] }}</td>
                                    <td>{{ $row['kategori'] }}</td>
                                    <td>{{ $row['stok_tersedia'] }}</td>
                                @elseif($tipe == 'stok_masuk')
                                    <td>{{ $row['no'] }}</td>
                                    <td>{{ $row['tanggal'] }}</td>
                                    <td>{{ $row['nama_sparepart'] }}</td>
                                    <td>{{ $row['kategori'] }}</td>
                                    <td>{{ $row['jumlah_masuk'] }}</td>
                                @elseif($tipe == 'stok_keluar')
                                    <td>{{ $row['no'] }}</td>
                                    <td>{{ $row['tanggal'] }}</td>
                                    <td>{{ $row['nama_sparepart'] }}</td>
                                    <td>{{ $row['kategori'] }}</td>
                                    <td>{{ $row['jumlah_keluar'] }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Data tidak tersedia.</td>
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
            $('#sparepart-detail-table').DataTable();
        });
    </script>
@endpush
