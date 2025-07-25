<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
            font-size: 12px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            line-height: 20px;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 30px;
            line-height: 30px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 10px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            padding: 8px; 
        }

        .invoice-box table tr.details td {
            padding-bottom: 15px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
            padding: 8px;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
            text-align: right;
            padding-top: 10px;
        }

        .invoice-box .column-header {
            width: 60%;
        }
        .invoice-box .column-qty, .invoice-box .column-price, .invoice-box .column-total {
            width: 10%;
            text-align: right;
        }

        .signature-box {
            margin-top: 30px;
            width: 100%;
            text-align: center;
        }
        .signature-box table {
            width: 100%;
        }
        .signature-box td {
            width: 50%;
            padding-top: 40px;
            vertical-align: top;
            text-align: center;
        }

        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="{{ public_path('assets/logo.png') }}" style="width:100%; max-width:150px;">
                                <h1>{{ $nama_bengkel ?? 'Nama Bengkel Anda' }}</h1>
                            </td>

                            <td class="text-right">
                                Invoice : {{ $transaction->invoice_number }}<br>
                                Tanggal : {{ $transaction->transaction_date->format('d-m-Y') }}<br>
                                Status  : {{ ucfirst($transaction->status) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                {{ $nama_bengkel ?? 'Nama Bengkel Anda' }}<br>
                                {{ $alamat_bengkel ?? 'Alamat Bengkel Anda' }}<br>
                                Telp: {{ $telepon_bengkel ?? 'Nomor Telepon Bengkel' }}
                            </td>

                            <td class="text-right">
                                Pelanggan: {{ $transaction->customer_name }}<br>
                                No. Kendaraan: {{ $transaction->vehicle_number }}<br>
                                Merk/Model: {{ $transaction->vehicle_model ?? '-' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td colspan="3" class="text-left">Metode Pembayaran</td>
                <td class="text-right">Detail</td>
            </tr>
            <tr class="details">
                <td colspan="3" class="text-left">{{ ucfirst($transaction->payment_method) }}</td>
                <td class="text-right">
                    @if ($transaction->proof_of_transfer_url)
                        <a href="{{ asset($transaction->proof_of_transfer_url) }}" target="_blank">Lihat Bukti Transfer</a>
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr class="heading">
                <td class="column-header">Deskripsi</td>
                <td class="column-qty">Qty</td>
                <td class="column-price">Harga Satuan</td>
                <td class="column-total">Subtotal</td>
            </tr>

            @foreach ($transaction->items as $item)
                <tr class="item">
                    <td class="text-left">
                        @if ($item->item_type == 'service' && $item->service)
                            {{ $item->service->nama }} (Jasa)
                        @elseif ($item->item_type == 'sparepart' && $item->sparepart)
                            {{ $item->sparepart->name }} (Sparepart)
                        @else
                            Item Tidak Dikenal
                        @endif
                    </td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            <tr class="total">
                <td colspan="3" class="text-right">Subtotal:</td>
                <td class="text-right">Rp {{ number_format($transaction->total_price + $transaction->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @if ($transaction->discount_amount > 0)
            <tr class="total">
                <td colspan="3" class="text-right">Diskon:</td>
                <td class="text-right">- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total">
                <td colspan="3" class="text-right">**TOTAL AKHIR:**</td>
                <td class="text-right">**Rp {{ number_format($transaction->total_price, 0, ',', '.') }}**</td>
            </tr>
        </table>

        <div class="signature-box">
            <table>
                <tr>
                    <td>
                        Hormat Kami,<br><br><br>
                        (..............................)<br>
                        {{ $nama_bengkel ?? 'Nama Bengkel Anda' }}
                    </td>
                    <td>
                        Tanda Tangan Pelanggan,<br><br><br>
                        (..............................)<br>
                        {{ $transaction->customer_name }}
                    </td>
                </tr>
            </table>
        </div>

        <p style="margin-top: 30px;">
            Terima kasih atas kunjungan Anda!
        </p>
    </div>
</body>
</html>