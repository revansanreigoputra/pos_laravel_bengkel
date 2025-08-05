@extends('layouts.master')

@section('title', 'Laporan Ringkasan Stok')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Laporan Ringkasan Stok</h4>
                </div>
                <div class="card-body">
                    {{-- Menampilkan total stok keseluruhan --}}
                    <h5 class="mb-4">Total Stok Keseluruhan: <span class="badge bg-primary">{{ number_format($totalStock) }}</span> unit</h5>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Sparepart</th>
                                    <th>Kode Part</th>
                                    <th>Stok Saat Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($spareparts as $sparepart)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $sparepart->name }}</td>
                                    <td>{{ $sparepart->code_part }}</td>
                                    <td>{{ number_format($sparepart->stock_level) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Tidak ada data sparepart.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-start mt-4">
        <a href="#" onclick="window.history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection