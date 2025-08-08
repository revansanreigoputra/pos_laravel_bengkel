@extends('layouts.master')

@section('title', 'Laporan Pembelian Sparepart')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    {{-- <style>
        .table ul {
            padding-left: 15px;
            margin-bottom: 0;
        }

        .table ul li {
            font-size: 0.85em;
            margin-bottom: 2px;
        }

        /* Penyesuaian lebar kolom jika diperlukan */
        .table th:nth-child(1),
        .table td:nth-child(1) {
            width: 10%;
        }

        .table th:nth-child(2),
        .table td:nth-child(2) {
            width: 12%;
        }

        .table th:nth-child(3),
        .table td:nth-child(3) {
            width: 10%;
        }

        .table th:nth-child(4),
        .table td:nth-child(4) {
            width: 10%;
        }

        .table th:nth-child(5),
        .table td:nth-child(5) {
            width: 12%;
        }

        .table th:nth-child(6),
        .table td:nth-child(6) {
            width: 8%;
        }
/* 
        .table th:nth-child(7),
        .table td:nth-child(7) {
            width: 8%;
        }

        .table th:nth-child(8),
        .table td:nth-child(8) {
            width: 8%;
        }

        .table th:nth-child(9),
        .table td:nth-child(9) {
            width: 7%;
        }

        .table th:nth-child(10),
        .table td:nth-child(10) {
            width: 15%;
        } */

        .filter-form .col-md-4 {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
    </style> --}}
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Laporan Pembelian Sparepart</h3>
            <div class="card-actions d-flex flex-column flex-md-row"> {{-- Added d-flex, flex-column, and flex-md-row --}}
                <button class="btn btn-outline-primary mb-2 mb-md-0 me-md-2" onclick="window.print()"> {{-- Added mb-2 and me-md-2 --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                        <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                    </svg>
                    Cetak Laporan
                </button>
                <a href="{{ route('report.purchase.export.excel', [
                        'start_date' => request('start_date'),
                        'end_date' => request('end_date'),
                        'status' => request('status')
                    ]) }}"
                    class="btn btn-outline-success"> {{-- Removed ms-2 as flexbox handles spacing --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                        <path d="M10 12l4 4m0 -4l-4 4"></path>
                        <path d="M12 8v8m-2 -2l2 2l2 -2"></path>
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Filter Tanggal --}}
            <form action="{{ route('report.purchase') }}" method="GET" class="mb-4 filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex justify-content-end align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route('report.purchase') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="purchaseReportTable">
                    <thead>
                        <tr>
                            <th>No.PO</th>
                            <th>Supplier</th>
                            <th>Tanggal Transaksi</th>
                            <th>Total Harga</th>
                            <th>Metode Pembayaran</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchaseOrders as $order)
                            <tr>
                                <td>{{ $order->invoice_number }}</td>
                                <td>
                                    @if($order->supplier)
                                        {{ $order->supplier->name }}
                                    @elseif($order->suppliers)
                                        {{ $order->suppliers->name }}
                                    @else
                                        <span class="text-muted">Supplier tidak ditemukan</span>
                                    @endif
                                </td>
                                <td>{{ $order->order_date->format('d-m-Y') }}</td>
                                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td>{{ ucfirst($order->payment_method) }}</td>
                                <td>
                                     
                                     @if ($order->status == 'pending')
                                        <span class="badge bg-warning text-white p-2">Pending</span>
                                    @elseif ($order->status == 'received')
                                        <span class="badge bg-success text-white p-2">Received</span>
                                    @else
                                        <span class="badge bg-danger text-white p-2">Cancelled</span>
                                    @endif
                                </td>
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data transaksi yang selesai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <h3 class="card-header">
            Total Pembelian Sparepart: {{ $purchaseOrders->count() }} transaksi
            @if (request('start_date') && request('end_date'))
                (Dari {{ request('start_date') }} sampai {{ request('end_date') }})
            @endif
        </h3>
        <div class="card-body">
            <p>Total Pengeluaran: Rp {{ number_format($purchaseOrders->sum('total_price'), 0, ',', '.') }}</p>
            <p class="text-muted">Data di atas mencakup semua transaksi pembelian dalam periode yang dipilih.</p>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#purchaseReportTable').DataTable();
        });
    </script>
@endpush
