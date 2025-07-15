<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Konsumen</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
        }
        .header small {
            display: block;
            font-size: 12px;
            margin-top: 4px;
            color: #666;
        }
        .date {
            text-align: right;
            font-size: 12px;
            margin-bottom: 10px;
            color: #444;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        th, td {
            border: 1px solid #888;
            padding: 6px;
            text-align: center;
        }
        tfoot td {
            font-style: italic;
            text-align: left;
            border: none;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>BENGKELKU</h2>
        <small>Laporan Data Konsumen</small>
    </div>

    <div class="date">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y, H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Konsumen</th>
                <th>No. Telp</th>
                <th>Email</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone ?? '-' }}</td>
                    <td>{{ $customer->email ?? '-' }}</td>
                    <td>{{ $customer->address ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>
    <footer>
        <div style="text-align: right; font-size: 11px;">
            &copy; {{ date('Y') }} Bengkelku
        </div>
    </footer>

</body>
</html>
