@extends('layouts.master')

@section('title', 'Laporan Penjualan')

@section('styles')
    <style>
        /* Styles untuk tampilan normal */
        .table ul {
            padding-left: 15px;
            margin-bottom: 0;
        }

        .table ul li {
            font-size: 0.85em;
            margin-bottom: 2px;
        }

        /* Penyesuaian lebar kolom */
        .table th:nth-child(1), .table td:nth-child(1) { width: 5%; }
        .table th:nth-child(2), .table td:nth-child(2) { width: 12%; }
        .table th:nth-child(3), .table td:nth-child(3) { width: 15%; }
        .table th:nth-child(4), .table td:nth-child(4) { width: 10%; }
        .table th:nth-child(5), .table td:nth-child(5) { width: 12%; }
        .table th:nth-child(6), .table td:nth-child(6) { width: 10%; }
        .table th:nth-child(7), .table td:nth-child(7) { width: 10%; }
        .table th:nth-child(8), .table td:nth-child(8) { width: 8%; }
        .table th:nth-child(9), .table td:nth-child(9) { width: 10%; }
        .table th:nth-child(10), .table td:nth-child(10) { width: 8%; }

        .filter-form .col-md-4 {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .summary-card {
            background: linear-gradient(135deg, #49a25c 0%, #58c774 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .summary-item {
            text-align: center;
            padding: 15px;
        }

        .summary-item h4 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: bold;
        }

        .summary-item p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }

        /* Print Styles */
        @media print {
            body {
                font-size: 12px;
                line-height: 1.3;
                color: #000;
            }

            /* Sembunyikan elemen yang tidak perlu dicetak */
            .card-actions,
            .filter-form,
            .btn,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate,
            .navbar,
            .sidebar,
            .breadcrumb {
                display: none !important;
            }

            /* Header untuk print */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #000;
                padding-bottom: 15px;
            }

            .print-header h1 {
                font-size: 20px;
                margin: 0;
                font-weight: bold;
            }

            .print-header h2 {
                font-size: 16px;
                margin: 5px 0;
                color: #666;
            }

            .print-header .print-info {
                font-size: 11px;
                margin-top: 10px;
                color: #888;
            }

            /* Table styles untuk print */
            .table {
                font-size: 10px;
                width: 100%;
                border-collapse: collapse;
            }

            .table th,
            .table td {
                border: 1px solid #000;
                padding: 4px;
                text-align: left;
                vertical-align: top;
            }

            .table th {
                background-color: #f0f0f0 !important;
                font-weight: bold;
                text-align: center;
            }

            .table tbody tr:nth-child(even) {
                background-color: #f9f9f9 !important;
            }

            /* Badge styles untuk print */
            .badge {
                border: 1px solid #000 !important;
                padding: 2px 4px !important;
                font-size: 8px !important;
                color: #000 !important;
                background: none !important;
            }

            /* Summary untuk print */
            .print-summary {
                margin-top: 20px;
                border-top: 2px solid #000;
                padding-top: 15px;
                display: block !important;
            }

            .print-summary h3 {
                font-size: 14px;
                margin-bottom: 10px;
            }

            .print-summary-content {
                font-size: 11px;
                line-height: 1.5;
            }

            /* Page break */
            .page-break {
                page-break-before: always;
            }

            /* Card adjustments */
            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .card-header {
                display: none !important;
            }

            .summary-card {
                display: none !important;
            }
        }

        /* Sembunyikan print header di tampilan normal */
        .print-header,
        .print-summary {
            display: none;
        }
    </style>
@endsection

@section('content')
    <!-- Print Header (hanya tampil saat print) -->
    <div class="print-header">
        <h1>LAPORAN PENJUALAN</h1>
        <h2>PT. BENGKEL OTOMOTIF</h2>
        <div class="print-info">
            <p>
                Periode: 
                @if (request('start_date') && request('end_date'))
                    {{ \Carbon\Carbon::parse(request('start_date'))->format('d F Y') }} - 
                    {{ \Carbon\Carbon::parse(request('end_date'))->format('d F Y') }}
                @else
                    Semua Data
                @endif
            </p>
            <p>Status: {{ request('status') ? ucfirst(request('status')) : 'Semua Status' }}</p>
            <p>Metode Pembayaran: {{ request('payment_method') ? ucfirst(request('payment_method')) : 'Semua Metode' }}</p>
            <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</p>
        </div>
    </div>

    <!-- Summary Cards (tidak tampil saat print) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="summary-card">
                <div class="row">
                    <div class="col-md-4 summary-item">
                        <h4>{{ $transactions->count() }}</h4>
                        <p>Total Transaksi</p>
                    </div>
                    <div class="col-md-4 summary-item">
                        <h4>Rp {{ number_format($transactions->sum('total_price'), 0, ',', '.') }}</h4>
                        <p>Total Pendapatan</p>
                    </div>
                    <div class="col-md-4 summary-item">
                        <h4>Rp {{ number_format($transactions->sum('discount_amount'), 0, ',', '.') }}</h4>
                        <p>Total Diskon</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Laporan Penjualan</h3>
            <div class="card-actions d-flex flex-column flex-md-row">
                <button class="btn btn-outline-primary mb-2 mb-md-0 me-md-2" onclick="printReport()">
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
                <a href="{{ route('report.transaction.export.excel', array_merge(request()->query(), ['export_title' => 'Laporan_Transaksi_Selesai'])) }}"
                    class="btn btn-outline-success">
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
            {{-- Filter Form --}}
            <form action="{{ route('report.transaction') }}" method="GET" class="mb-4 filter-form">
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
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="payment_method" class="form-label">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" class="form-select">
                            <option value="">-- Semua Metode --</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="e-wallet" {{ request('payment_method') == 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex justify-content-end align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route('report.transaction') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            {{-- Tabel Laporan --}}
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="transactionReportTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Invoice</th>
                            <th>Pelanggan</th>
                            <th>No. Kendaraan</th>
                            <th>Model Kendaraan</th>
                            <th>Tanggal</th>
                            <th>Pembayaran</th>
                            <th>Diskon</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $transaction->invoice_number }}</td>
                                <td>{{ $transaction->customer->name ?? 'N/A' }}</td>
                                <td>{{ $transaction->vehicle_number }}</td>
                                <td>{{ $transaction->vehicle_model ?? '-' }}</td>
                                <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($transaction->payment_method) }}</td>
                                <td>Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge 
                                        {{ $transaction->status == 'completed' ? 'bg-success' : 
                                           ($transaction->status == 'pending' ? 'bg-warning' : 'bg-danger') }} 
                                        text-white">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada data transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Print Summary (hanya tampil saat print) -->
    <div class="print-summary">
        <h3>RINGKASAN LAPORAN</h3>
        <div class="print-summary-content">
            <table style="width: 100%; border: 1px solid #000;">
                <tr>
                    <td style="border: 1px solid #000; padding: 5px;"><strong>Total Transaksi:</strong></td>
                    <td style="border: 1px solid #000; padding: 5px;">{{ $transactions->count() }} transaksi</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px;"><strong>Total Pendapatan:</strong></td>
                    <td style="border: 1px solid #000; padding: 5px;">Rp {{ number_format($transactions->sum('total_price'), 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px;"><strong>Total Diskon:</strong></td>
                    <td style="border: 1px solid #000; padding: 5px;">Rp {{ number_format($transactions->sum('discount_amount'), 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px;"><strong>Pendapatan Bersih:</strong></td>
                    <td style="border: 1px solid #000; padding: 5px;">Rp {{ number_format($transactions->sum('total_price') - $transactions->sum('discount_amount'), 0, ',', '.') }}</td>
                </tr>
            </table>
            <p style="margin-top: 20px; font-size: 10px; color: #666;">
                * Laporan ini mencakup semua transaksi dalam periode yang dipilih.<br>
                * Data dicetak pada {{ \Carbon\Carbon::now()->format('d F Y \p\u\k\u\l H:i:s') }}
            </p>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#transactionReportTable').DataTable();
        });

        function printReport() {
            // Simpan title asli
            var originalTitle = document.title;
            
            // Set title untuk print
            document.title = 'Laporan Penjualan - ' + new Date().toLocaleDateString('id-ID');
            
            // Print
            window.print();
            
            // Restore title asli
            document.title = originalTitle;
        }
    </script>
@endpush