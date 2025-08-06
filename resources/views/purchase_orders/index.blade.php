@extends('layouts.master')

@section('title', 'Daftar Pesanan Pembelian')

@section('action')
    <a href="{{ route('purchase_orders.create') }}" class="btn btn-primary">
        Tambah Pembelian
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
             

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
                                    <a href="{{ route('purchase_orders.show', $order->id) }}" class="btn btn-sm btn-info">Lihat</a>
                                    <a href="{{ route('purchase_orders.edit', $order->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#delete-order-{{ $order->id }}">
                                        Hapus
                                    </button>
                                    {{-- Asumsikan Anda memiliki komponen modal delete-confirm --}}
                                    <x-modal.delete-confirm
                                        id="delete-order-{{ $order->id }}"
                                        :route="route('purchase_orders.destroy', $order->id)"
                                        item="{{ $order->invoice_number }}"
                                        title="Hapus Pesanan Pembelian?"
                                        description="Data pesanan pembelian yang dihapus tidak bisa dikembalikan."
                                    />
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
        <div class="card-footer">
            {{ $purchaseOrders->links() }}
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var table = $('#purchaseOrderTable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    paginate: {
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    zeroRecords: "Data tidak ditemukan",
                    infoEmpty: "Menampilkan 0 - 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)"
                },
                pageLength: 10
            });

            // Event listener untuk filter status
            $('#statusFilter').on('change', function() {
                var status = $(this).val();
                table.column(5).search(status).draw(); // Kolom 'Status' ada di indeks ke-5 (dimulai dari 0)
            });
        });
    </script>
@endpush