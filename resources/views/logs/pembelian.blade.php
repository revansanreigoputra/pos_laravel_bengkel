@extends('layouts.master')

@section('title', 'Log Pembelian')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="container">
                <div class="table-responsive">
                    <table id="pembelian-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No. Invoice</th>
                                <th>Supplier</th>
                                <th>Tanggal Order</th>
                                <th>Detail Produk</th>
                                <th>Total Pembelian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrders as $order)
                                <tr>
                                    <td>{{ $order->invoice_number }}</td>
                                    <td>{{ $order->supplier->name }}</td>
                                    <td>{{ $order->order_date->format('d-m-Y') }}</td>
                                    <td>
                                        <ul>
                                            @foreach($order->items as $item)
                                                <li>{{ $item->sparepart->name }} - Qty: {{ $item->quantity }} - Rp {{ number_format($item->purchase_price) }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        Rp {{ number_format($order->items->sum(function($item) {
                                            return $item->quantity * $item->purchase_price;
                                        })) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Menghapus paginasi manual karena akan ditangani oleh DataTables --}}
                {{-- {{ $purchaseOrders->links() }} --}}
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#pembelian-table').DataTable();
        });
    </script>
@endpush