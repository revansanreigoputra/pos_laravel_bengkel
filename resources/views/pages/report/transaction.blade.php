@extends('layouts.master') {{-- Pastikan Anda menggunakan layout master yang sesuai --}}

@section('title', 'Laporan Transaksi Selesai')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        /* Mengurangi padding pada list item untuk tampilan yang lebih ringkas */
        .table ul {
            padding-left: 15px;
            margin-bottom: 0;
        }
        .table ul li {
            font-size: 0.85em; /* Ukuran font lebih kecil untuk item */
            margin-bottom: 2px;
        }
        /* Penyesuaian lebar kolom jika diperlukan */
        .table th:nth-child(1), .table td:nth-child(1) { width: 10%; } /* Invoice */
        .table th:nth-child(2), .table td:nth-child(2) { width: 12%; } /* Pelanggan */
        .table th:nth-child(3), .table td:nth-child(3) { width: 10%; } /* No. Kendaraan */
        .table th:nth-child(4), .table td:nth-child(4) { width: 10%; } /* Model Kendaraan */
        .table th:nth-child(5), .table td:nth-child(5) { width: 12%; } /* Tanggal Transaksi */
        .table th:nth-child(6), .table td:nth-child(6) { width: 8%; } /* Metode Pembayaran */
        .table th:nth-child(7), .table td:nth-child(7) { width: 8%; } /* Diskon */
        .table th:nth-child(8), .table td:nth-child(8) { width: 8%; } /* Total Harga */
        .table th:nth-child(9), .table td:nth-child(9) { width: 7%; } /* Status */
        .table th:nth-child(10), .table td:nth-child(10) { width: 15%; } /* Items */

        /* Centering the filter form elements if desired */
        .filter-form .col-md-4 {
            display: flex;
            flex-direction: column;
            justify-content: flex-end; /* Align to bottom for labels */
        }
    </style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Transaksi Selesai</h3>
        <div class="card-actions">
            <button class="btn btn-outline-primary" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path></svg>
                Cetak Laporan
            </button>
        </div>
    </div>
    <div class="card-body">
        {{-- Filter Tanggal --}}
        <form action="{{ route('report.transaction') }}" method="GET" class="mb-4 filter-form">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-4 d-flex justify-content-end"> {{-- Perubahan ada di baris ini --}}
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('report.transaction') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="transactionReportTable"> {{-- Tambahkan ID untuk DataTables --}}
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>No. Kendaraan</th>
                        <th>Model Kendaraan</th>
                        <th>Tanggal Transaksi</th>
                        <th>Metode Pembayaran</th>
                        <th>Diskon</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                        <th>Items</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->invoice_number }}</td>
                        <td>{{ $transaction->customer_name }}</td>
                        <td>{{ $transaction->vehicle_number }}</td>
                        <td>{{ $transaction->vehicle_model ?? '-' }}</td>
                        <td>{{ $transaction->transaction_date->format('d-m-Y H:i') }}</td>
                        <td>{{ ucfirst($transaction->payment_method) }}</td>
                        <td>Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $transaction->status == 'completed' ? 'bg-success' : ($transaction->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td>
                            <ul>
                                @foreach ($transaction->items as $item)
                                    <li>
                                        @if($item->item_type == 'service' && $item->service)
                                            {{ $item->service->name }} (Servis) - {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                        @elseif($item->item_type == 'sparepart' && $item->sparepart)
                                            {{ $item->sparepart->name }} (Sparepart) - {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                        @else
                                            {{ ucfirst($item->item_type) }} - {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">Tidak ada data transaksi yang selesai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#transactionReportTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "columnDefs": [
                    { "orderable": false, "targets": [9] } // Kolom "Items" tidak bisa diurutkan
                ]
            });
        });
    </script>
@endsection