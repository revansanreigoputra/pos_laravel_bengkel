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
                                <th>Metode Pembayaran</th>
                                <th>Status</th>
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
                                    <td>{{ $order->payment_method ?? '-' }}</td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning text-white">Pending</span>
                                        @elseif($order->status == 'received')
                                            <span class="badge bg-success text-white">Received</span>
                                        @else
                                            <span class="badge bg-danger text-white">Canceled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="mb-0">
                                            @foreach($order->items as $item)
                                                <li>
                                                    {{ $item->sparepart->name }}<br>
                                                    Qty: {{ $item->quantity }} x Rp {{ number_format($item->purchase_price) }} <br>
                                                    <strong>Total: Rp {{ number_format($item->quantity * $item->purchase_price) }}</strong>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        <strong>Rp {{ number_format($order->items->sum(fn($item) => $item->quantity * $item->purchase_price)) }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- DataTables akan handle pagination --}}
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#pembelian-table').DataTable({
                responsive: true,
                autoWidth: false
            });
        });
    </script>
@endpush
