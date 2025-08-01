@extends('layouts.master') {{-- Asumsikan Anda memiliki layout dasar --}}

@section('title', 'Daftar Pesanan Pembelian')

@section('content')
<div class="container mx-auto p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-2xl font-bold">Daftar Pesanan Pembelian</h1>
        <a href="{{ route('purchase_orders.create') }}" class="btn btn-primary">
            Tambah Pembelian
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table id="purchaseOrderTable" class="table table-striped table-hover">
                <thead>
                    <tr>
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
                            <td>{{ $order->invoice_number }}</td>
                            <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                            <td>
                                {{-- Periksa apakah order_date tidak null sebelum memformat --}}
                                {{ $order->order_date ? $order->order_date->format('d M Y') : '-' }}
                            </td>
                            <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td>{{ $order->payment_method ?? '-' }}</td>
                            <td>
                                @if ($order->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif ($order->status == 'received')
                                    <span class="badge bg-success">Received</span>
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('purchase_orders.show', $order->id) }}" class="btn btn-sm btn-info">Lihat</a>
                                    <a href="{{ route('purchase_orders.edit', $order->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('purchase_orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada pesanan pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $purchaseOrders->links() }}
    </div>
</div>
@endsection

@push('addon-script')
<script>
    $(document).ready(function () {
        $('#purchaseOrderTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                },
                zeroRecords: "Data tidak ditemukan"
            },
            pageLength: 10
        });
    });
</script>
@endpush
