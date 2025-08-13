<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
        }
        .header p {
            font-size: 12px;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .details-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .details-list li {
            margin-bottom: 5px;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BENGKELKU</h1>
        <h4>Laporan Penjualan</h4>
        @if($startDate && $endDate)
            <p>Periode: {{ Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Pelanggan</th>
                <th>Tanggal Transaksi</th>
                <th>Metode Pembayaran</th>
                <th>Detail Item</th>
                <th>Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $trx)
                <tr>
                    <td>{{ $trx->invoice_number }}</td>
                    <td>{{ $trx->customer->name ?? '-' }}</td>
                    <td>{{ $trx->transaction_date->format('d-m-Y') }}</td>
                    <td>{{ $trx->payment_method ?? '-' }}</td>
                    <td>
                        <ul class="details-list">
                            @foreach ($trx->items as $item)
                                <li>
                                    {{ $item->item_type === 'sparepart' ? $item->sparepart->name ?? '-' : $item->service->nama ?? '-' }}<br>
                                    Qty: {{ $item->quantity }} x Rp {{ number_format($item->price) }}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($trx->items->sum(fn($item) => $item->quantity * $item->price)) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">Tidak ada data penjualan yang tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan dibuat pada: {{ Carbon\Carbon::now()->format('d F Y') }}</p>
    </div>
</body>
</html>