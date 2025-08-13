@extends('layouts.master')

@section('title', 'Log Pembelian')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Laporan Pembelian </h3>
            <div class="card-actions d-flex flex-column flex-md-row"> {{-- Added d-flex, flex-column, and flex-md-row --}}
                <a href="{{ route('logs.pembelian.pdf', ['status' => request('status'), 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                    class="btn btn-outline-primary mb-2 mb-md-0 me-md-2" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                        <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                    </svg>
                    Cetak Laporan
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Filter Tanggal --}}
            <form action="{{ route('logs.pembelian') }}" method="GET" class="mb-4 filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">-- Semua Status --</option>
                            <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Diterima
                            </option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Dibatalkan
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ $startDate ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ $endDate ?? '' }}">
                    </div>
                    <div class="col-md-2 d-flex justify-content-between align-items-center g-5">
                        <button type="submit" class="btn btn-primary me-2">Cari</button>
                        <a href="{{ route('logs.pembelian') }}" class="btn btn-secondary">Reset</a>
                    </div>
                    <input type="hidden" name="tab" id="active_tab_input" value="{{ request('tab', 'semua') }}">
                </div>
            </form>
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
                            @foreach ($purchaseOrders as $order)
                                <tr>
                                    <td>{{ $order->invoice_number }}</td>
                                    <td>{{ $order->supplier->name }}</td>
                                    <td>{{ $order->order_date->format('d-m-Y') }}</td>
                                    <td>{{ $order->payment_method ?? '-' }}</td>
                                    <td>
                                        @if ($order->status == 'pending')
                                            <span class="badge bg-warning text-white">Pending</span>
                                        @elseif($order->status == 'received')
                                            <span class="badge bg-success text-white">Received</span>
                                        @else
                                            <span class="badge bg-danger text-white">Canceled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="mb-0">
                                            @foreach ($order->items as $item)
                                                <li>
                                                    {{ $item->sparepart->name }}<br>
                                                    Qty: {{ $item->quantity }} x Rp
                                                    {{ number_format($item->purchase_price) }} <br>
                                                    <strong>Total: Rp
                                                        {{ number_format($item->quantity * $item->purchase_price) }}</strong>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        <strong>Rp
                                            {{ number_format($order->items->sum(fn($item) => $item->quantity * $item->purchase_price)) }}</strong>
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
