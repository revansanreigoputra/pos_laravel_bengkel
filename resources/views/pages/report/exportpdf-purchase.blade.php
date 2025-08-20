<!DOCTYPE html>
<html>
<head>
    <title>{{ $reportTitle ?? 'Laporan Pembelian Sparepart' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
        }
        .header h4 {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #555;
        }
        .report-info {
            text-align: left;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 4px;
            color: white;
            font-size: 9px;
            text-transform: capitalize;
        }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; color: #333; }
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
            font-size: 9px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>BENGKELKU</h2>
        <h4>Laporan Pembelian Sparepart</h4>
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
                <th>Supplier</th>
                <th>Tanggal</th>
                <th>Total Harga</th>
                <th>Pembayaran</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($purchaseOrders as $order)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $order->invoice_number }}</td>
                    <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
                    <td class="text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ ucfirst($order->payment_method) }}</td>
                    <td class="text-center">
                        <span class="badge {{ $order->status == 'received' ? 'bg-success' : ($order->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data pembelian sparepart.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <h3>Ringkasan</h3>
        <table>
            <tr>
                <td><strong>Total Pembelian:</strong></td>
                <td>{{ $purchaseOrders->count() }} transaksi</td>
            </tr>
            <tr>
                <td><strong>Total Pengeluaran:</strong></td>
                <td>Rp {{ number_format($purchaseOrders->sum('total_price'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Pembelian Diterima:</strong></td>
                <td>{{ $purchaseOrders->where('status', 'received')->count() }} transaksi</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y ') }}
    </div>
</body>
</html>