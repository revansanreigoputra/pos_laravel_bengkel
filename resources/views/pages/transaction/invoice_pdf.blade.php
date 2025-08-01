<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $transaction->invoice_number }}</title>
    <style>
        @page {
            size: landscape; /* Tetap landscape */
            margin: 10mm; /* Mengurangi margin halaman */
        }

        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #555; /* Warna teks lebih gelap sedikit */
            font-size: 11px; /* Mengurangi ukuran font dasar */
            margin: 0;
            padding: 0;
        }

        .invoice-box {
            max-width: 100%; /* Gunakan lebar penuh yang tersedia di halaman landscape */
            margin: 0 auto; /* Tengah secara horizontal */
            padding: 15px; /* Mengurangi padding */
            border: 1px solid #eee;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1); /* Sedikit mengurangi bayangan */
            font-size: 13px; /* Ukuran font utama invoice */
            line-height: 18px; /* Mengurangi line-height */
            color: #333; /* Warna teks utama */
            box-sizing: border-box;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 4px; /* Mengurangi padding sel tabel */
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 15px; /* Mengurangi padding */
        }

        .invoice-box table tr.top table td.title {
            font-size: 26px; /* Mengurangi ukuran font judul */
            line-height: 26px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 8px; /* Mengurangi padding */
        }

        .invoice-box table tr.heading td {
            background: #f0f0f0; /* Warna latar belakang heading sedikit lebih terang */
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            padding: 6px 8px; /* Mengurangi padding */
        }

        .invoice-box table tr.details td {
            padding-bottom: 10px; /* Mengurangi padding */
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
            padding: 6px 8px; /* Mengurangi padding */
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
            text-align: right;
            padding-top: 8px; /* Mengurangi padding */
        }

        /* Adjusted column widths for landscape - more aggressive */
        .invoice-box .column-header {
            width: 55%; /* Deskripsi lebih lebar */
            text-align: left !important;
        }
        .invoice-box .column-qty {
            width: 10%; /* Kuantitas lebih kecil */
            text-align: right;
        }
        .invoice-box .column-price {
            width: 18%; /* Harga satuan */
            text-align: right;
        }
        .invoice-box .column-total {
            width: 17%; /* Subtotal */
            text-align: right;
        }

        .signature-box {
            margin-top: 20px; /* Mengurangi margin atas */
            width: 100%;
            text-align: center;
        }
        .signature-box table {
            width: 100%;
        }
        .signature-box td {
            width: 50%;
            padding-top: 25px; /* Mengurangi padding untuk tanda tangan */
            vertical-align: top;
            text-align: center;
            font-size: 11px; /* Mengurangi ukuran font di tanda tangan */
        }

        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }

        /* Styling untuk logo */
        .invoice-box .logo {
            width: 100%;
            max-width: 100px; /* Mengurangi ukuran maksimal logo */
            height: auto;
            display: block; /* Pastikan logo adalah block element */
            margin-bottom: 5px; /* Sedikit margin bawah */
        }

        /* Styling untuk nama bengkel di header */
        .invoice-box .company-name-header {
            font-size: 18px; /* Ukuran font nama bengkel di header */
            color: #333;
            margin-top: 5px;
            margin-bottom: 0;
        }

        /* Styling untuk informasi bengkel di information section */
        .invoice-box .company-info {
            font-size: 12px; /* Ukuran font info bengkel */
            line-height: 16px;
        }

        /* Styling untuk informasi pelanggan */
        .invoice-box .customer-info {
            font-size: 12px; /* Ukuran font info pelanggan */
            line-height: 16px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title text-left">
                                <img src="{{ public_path('assets/logo.png') }}" class="logo">
                                <h1 class="company-name-header">{{ $nama_bengkel ?? 'Nama Bengkel Anda' }}</h1>
                            </td>

                            <td class="text-right">
                                Invoice : <span style="font-weight: bold;">{{ $transaction->invoice_number }}</span><br>
                                Tanggal : {{ $transaction->transaction_date->format('d-m-Y') }}<br>
                                Status  : {{ ucfirst($transaction->status) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="company-info text-left">
                                {{ $nama_bengkel ?? 'Nama Bengkel Anda' }}<br>
                                {{ $alamat_bengkel ?? 'Alamat Bengkel Anda' }}<br>
                                Telp: {{ $telepon_bengkel ?? 'Nomor Telepon Bengkel' }}
                            </td>

                            <td class="customer-info text-right">
                                Pelanggan: {{ $transaction->customer->name ?? 'N/A' }}<br>
                                No. Telp: {{ $transaction->customer->phone ?? 'N/A' }}<br>
                                No. Kendaraan: {{ $transaction->vehicle_number ?? '-' }}<br>
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
                        <a href="{{ asset($transaction->proof_of_transfer_url) }}" target="_blank" style="color: #4e73df; text-decoration: none;">Lihat Bukti Transfer</a>
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
                <tr class="item {{ $loop->last ? 'last' : '' }}">
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

            {{-- Baris total disatukan untuk menghemat ruang --}}
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
                <td colspan="3" class="text-right" style="font-size: 15px;">**TOTAL AKHIR:**</td>
                <td class="text-right" style="font-size: 15px;">**Rp {{ number_format($transaction->total_price, 0, ',', '.') }}**</td>
            </tr>
        </table>

        <div class="signature-box">
            <table>
                <tr>
                    <td>
                        Hormat Kami,<br><br>
                        (..............................)<br>
                        {{ $nama_bengkel ?? 'Nama Bengkel Anda' }}
                    </td>
                    <td>
                        Tanda Tangan Pelanggan,<br><br>
                        (..............................)<br>
                        {{ $transaction->customer->name ?? 'Pelanggan' }}
                    </td>
                </tr>
            </table>
        </div>

        <p style="margin-top: 20px; font-size: 11px;">
            Terima kasih atas kunjungan Anda!
        </p>
    </div>
</body>
</html>