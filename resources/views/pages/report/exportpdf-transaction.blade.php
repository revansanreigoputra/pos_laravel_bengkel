<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        .header h4 {
            margin: 5px 0 0 0;
            font-weight: normal;
        }
        .report-info {
            margin-bottom: 20px;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            border-radius: .25rem;
        }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; }
        .bg-danger { background-color: #dc3545; }
        .summary-box {
            border: 1px solid #ccc;
            padding: 15px;
            margin-top: 20px;
        }
        .summary-box h3 {
            margin-top: 0;
        }
        .summary-box table {
            border: none;
        }
        .summary-box td {
            border: none;
            padding: 4px 0;
        }
        .footer {
            text-align: right;
            margin-top: 30px;
            font-size: 9px;
            color: #555;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>PT. BENGKEL OTOMOTIF</h2>
    <h4>Laporan Transaksi Penjualan</h4>
</div>

<div class="report-info">
    <strong>Periode:</strong> 
    @if ($startDate && $endDate)
        {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
    @else
        Semua Data
    @endif
    <br>
    <strong>Status:</strong> {{ $status ? ucfirst($status) : 'Semua Status' }}<br>
    <strong>Metode Pembayaran:</strong> {{ $paymentMethod ? ucfirst($paymentMethod) : 'Semua Metode' }}
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>No Invoice</th>
            <th>Pelanggan</th>
            <th>No. Kendaraan</th>
            <th>Tanggal</th>
            <th>Metode Pembayaran</th>
            <th>Diskon</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transactions as $transaction)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $transaction->invoice_number }}</td>
                <td>{{ $transaction->customer->name ?? 'N/A' }}</td>
                <td>{{ $transaction->vehicle_number }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}</td>
                <td class="text-center">{{ ucfirst($transaction->payment_method) }}</td>
                <td class="text-right">Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
                <td class="text-center">
                    <span class="badge {{ $transaction->status == 'completed' ? 'bg-success' : ($transaction->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data transaksi penjualan.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="summary-box">
    <h3>Ringkasan</h3>
    <table>
        <tr>
            <td><strong>Total Transaksi:</strong></td>
            <td>{{ $transactions->count() }} transaksi</td>
        </tr>
        <tr>
            <td><strong>Total Pendapatan:</strong></td>
            <td>Rp {{ number_format($transactions->sum('total_price'), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Total Diskon:</strong></td>
            <td>Rp {{ number_format($transactions->sum('discount_amount'), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Pendapatan Bersih:</strong></td>
            <td>Rp {{ number_format($transactions->sum('total_price') - $transactions->sum('discount_amount'), 0, ',', '.') }}</td>
        </tr>
    </table>
</div>

<div class="footer">
    Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y ') }}
</div>

</body>
</html>