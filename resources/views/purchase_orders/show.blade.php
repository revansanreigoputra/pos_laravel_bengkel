@extends('layouts.master') {{-- Menggunakan layout master --}}

@section('title', 'Detail Pesanan Pembelian')

@section('content')
<div class="container-fluid"> {{-- Menggunakan container-fluid untuk lebar penuh --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4"> {{-- Card untuk informasi utama --}}
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Detail Pesanan Pembelian
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Nomor Invoice:</div>
                        <div class="col-md-8">{{ $purchaseOrder->invoice_number }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Supplier:</div>
                        <div class="col-md-8">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Tanggal Pesanan:</div>
                        <div class="col-md-8">{{ $purchaseOrder->order_date ? $purchaseOrder->order_date->format('d M Y H:i') : '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Metode Pembayaran:</div>
                        <div class="col-md-8">{{ $purchaseOrder->payment_method ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Total Harga:</div>
                        <div class="col-md-8">Rp{{ number_format($purchaseOrder->total_price, 0, ',', '.') }}</div> {{-- Menggunakan 0 desimal untuk konsistensi --}}
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Status:</div>
                        <div class="col-md-8">
                            @php
                                $statusClass = '';
                                if ($purchaseOrder->status == 'pending') {
                                    $statusClass = 'badge bg-warning text-dark';
                                } elseif ($purchaseOrder->status == 'received') {
                                    $statusClass = 'badge bg-success';
                                } else {
                                    $statusClass = 'badge bg-danger';
                                }
                            @endphp
                            <span class="{{ $statusClass }}">{{ ucfirst($purchaseOrder->status) }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Catatan:</div>
                        <div class="col-md-8">{{ $purchaseOrder->notes ?? '-' }}</div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('purchase_orders.edit', $purchaseOrder->id) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> Edit Pesanan
                        </a>
                        <form action="{{ route('purchase_orders.destroy', $purchaseOrder->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-1"></i> Hapus Pesanan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-boxes me-2"></i> Item Pesanan
                    </h4>
                    <a href="{{ route('purchase_order_items.create', ['purchase_order_id' => $purchaseOrder->id]) }}" class="btn btn-success btn-sm ms-auto">
                        <i class="fas fa-plus me-1"></i> Tambah Item Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Sparepart</th>
                                    <th>Kode Part</th>
                                    <th>Kuantitas</th>
                                    <th>Harga Beli per Unit</th>
                                    <th>Total Item</th>
                                    <th>Tanggal Kadaluarsa</th>
                                    <th>Catatan Item</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchaseOrder->items as $item)
                                <tr>
                                    {{-- Menggunakan null coalescing operator untuk menghindari error jika sparepart tidak ada --}}
                                    <td>{{ $item->sparepart->name ?? 'N/A' }}</td>
                                    <td>{{ $item->sparepart->code_part ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>Rp{{ number_format($item->purchase_price, 0, ',', '.') }}</td> {{-- Menggunakan 0 desimal --}}
                                    <td>Rp{{ number_format($item->quantity * $item->purchase_price, 0, ',', '.') }}</td> {{-- Menggunakan 0 desimal --}}
                                    {{-- Menggunakan ternary operator untuk menghindari error jika tanggal kadaluarsa kosong --}}
                                    <td>{{ $item->expired_date ? $item->expired_date->format('d M Y') : '-' }}</td>
                                    <td>{{ $item->notes ?? '-' }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('purchase_order_items.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('purchase_order_items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus item ini?');" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        Belum ada item untuk pesanan ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-start mt-4">
        <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Pesanan
        </a>
    </div>
</div>
@endsection