<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pembelian</title>
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

        th,
        td {
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
        <h2>{{ \App\Models\BengkelSetting::getSettings()->nama_bengkel }}</h2>
        <h4>Laporan Pembelian</h4>
        @if ($startDate && $endDate)
            <p>Periode: {{ Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                {{ Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        @endif
        @if ($status)
            <p>Status: {{ ucfirst($status) }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Supplier</th>
                <th>Tanggal Order</th>
                <th>Metode Pembayaran</th>
                <th>Status</th>
                <th>Detail Produk</th>
                <th>Total Pembelian</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($purchaseOrders as $order)
                <tr>
                    <td>{{ $order->invoice_number }}</td>
                    <td>{{ $order->supplier->name }}</td>
                    <td>{{ $order->order_date->format('d-m-Y') }}</td>
                    <td>{{ $order->payment_method ?? '-' }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>
                        <ul class="details-list">
                            @foreach ($order->items as $item)
                                <li>
                                    {{ $item->sparepart->name }} (Qty: {{ $item->quantity }})<br>
                                    Harga: Rp {{ number_format($item->purchase_price) }}
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-right">
                        Rp
                        {{ number_format($order->items->sum(fn($item) => $item->quantity * $item->purchase_price)) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data pembelian yang tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan dibuat pada: {{ Carbon\Carbon::now()->format('d F Y') }}</p>
    </div>
</body>

</html>
