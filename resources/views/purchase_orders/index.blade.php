@extends('layouts.master')

@section('title', 'Daftar Pesanan Pembelian')

@section('action')
    @can('purchase_order.create')
        {{-- Only show button if user has permission --}}
        <a href="{{ route('purchase_orders.create') }}" class="btn btn-primary">
            Tambah Pembelian
        </a>
    @endcan
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Filter Status Pesanan --}}
            <div class="mb-3 d-flex justify-content-end align-items-center">
                <label for="statusFilter" class="form-label mb-0 me-2">Filter Status:</label>
                <select id="statusFilter" class="form-select" style="width: auto;">
                    <option value="">Semua</option>
                    <option value="pending">Pending</option>
                    <option value="received">Received</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            {{-- End Filter Status Pesanan --}}

            <div class="table-responsive">
                <table id="purchaseOrderTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No PO</th>
                            <th>Supplier</th>
                            <th>Tanggal</th>
                            <th>Total Harga</th>
                            <th>Metode Pembayaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchaseOrders as $order)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $order->invoice_number }}</td>
                                <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                                <td>
                                    {{ $order->order_date ? $order->order_date->format('d M Y') : '-' }}
                                </td>
                                <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td>{{ $order->payment_method ?? '-' }}</td>
                                <td>
                                    @if ($order->status == 'pending')
                                        <span class="badge bg-warning text-white p-2">Pending</span>
                                    @elseif ($order->status == 'received')
                                        <span class="badge bg-success text-white p-2">Received</span>
                                    @else
                                        <span class="badge bg-danger text-white p-2">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('purchase_orders.show', $order->id) }}"
                                        class="btn btn-sm btn-info">Lihat</a>

                                    @can('purchase_order.edit')
                                        {{-- Edit button permission --}}
                                        <a href="{{ route('purchase_orders.edit', $order->id) }}"
                                            class="btn btn-sm btn-warning">Edit</a>
                                    @endcan

                                    @can('purchase_order.delete')
                                        {{-- Delete button permission --}}
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#delete-order-{{ $order->id }}">
                                            Hapus
                                        </button>
                                    @endcan

                                    @can('purchase_order.delete')
                                        {{-- Delete modal permission --}}
                                        <x-modal.delete-confirm id="delete-order-{{ $order->id }}" :route="route('purchase_orders.destroy', $order->id)"
                                            item="{{ $order->invoice_number }}" title="Hapus Pesanan Pembelian?"
                                            description="Data pesanan pembelian yang dihapus tidak bisa dikembalikan." />
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Belum ada pesanan pembelian.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#purchaseOrderTable').DataTable();
        });
    </script>
@endpush
