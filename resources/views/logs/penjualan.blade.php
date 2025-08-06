@extends('layouts.master')

@section('title', 'Log Penjualan')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="container">
            <div class="table-responsive">
                <table id="penjualan-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Pelanggan</th>
                            <th>Tanggal Transaksi</th>
                            <th>Detail Item</th>
                            <th>Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $trx)
                            <tr>
                                <td>{{ $trx->invoice_number }}</td>
                                <td>{{ $trx->customer->name }}</td>
                                <td>{{ $trx->transaction_date->format('d-m-Y') }}</td>
                                <td>
                                    <ul>
                                        @foreach($trx->items as $item)
                                            <li>
                                                {{ $item->item_type === 'sparepart' ? $item->sparepart->name : $item->service->nama }}
                                                - Qty: {{ $item->quantity }}
                                                - Rp {{ number_format($item->price) }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    Rp {{ number_format($trx->items->sum(function($item) {
                                        return $item->quantity * $item->price;
                                    })) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#penjualan-table').DataTable();
        });
    </script>
@endpush