@extends('layouts.master')

@section('title', 'Detail Pesanan Pembelian')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> Detail Pesanan Pembelian
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Nomor Invoice:</div>
                        <div class="col-md-8">
                            <span class="badge bg-gray-500 text-white">{{ $purchaseOrder->invoice_number }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Supplier:</div>
                        <div class="col-md-8">
                            {{ $purchaseOrder->supplier->name ?? 'N/A' }}
                            @if($purchaseOrder->supplier)
                                <small class="text-muted d-block">Telp: {{ $purchaseOrder->supplier->phone ?? '-' }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Tanggal Pesanan:</div>
                        <div class="col-md-8">
                            {{ $purchaseOrder->order_date ? $purchaseOrder->order_date->translatedFormat('d F Y H:i') : '-' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Metode Pembayaran:</div>
                        <div class="col-md-8">
                            {{ $purchaseOrder->payment_method ? ucfirst($purchaseOrder->payment_method) : '-' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Total Harga:</div>
                        <div class="col-md-8">
                            <span class="fw-bold">Rp{{ number_format($purchaseOrder->total_price, 0, ',', '.') }}</span>
                            @if($purchaseOrder->global_discount > 0)
                                <small class="text-muted d-block">(Termasuk diskon Rp{{ number_format($purchaseOrder->global_discount, 0, ',', '.') }})</small>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Status:</div>
                        <div class="col-md-8">
                            @php
                                $statusClass = '';
                                $statusTitle = '';
                                if ($purchaseOrder->status == 'pending') {
                                    $statusClass = 'badge bg-warning text-white';
                                    $statusTitle = 'Menunggu konfirmasi';
                                } elseif ($purchaseOrder->status == 'received') {
                                    $statusClass = 'badge bg-success text-white';
                                    $statusTitle = 'Pesanan telah diterima';
                                } else {
                                    $statusClass = 'badge bg-danger text-white';
                                    $statusTitle = 'Pesanan dibatalkan';
                                }
                            @endphp
                            <span class="{{ $statusClass }}" title="{{ $statusTitle }}">{{ ucfirst($purchaseOrder->status) }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-semibold">Catatan:</div>
                        <div class="col-md-8">
                            {{ $purchaseOrder->notes ?: '-' }}
                            @if($purchaseOrder->notes)
                                <button class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="tooltip" title="{{ $purchaseOrder->notes }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('purchase_orders.edit', $purchaseOrder->id) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> Edit Pesanan
                        </a>
                        <form action="{{ route('purchase_orders.destroy', $purchaseOrder->id) }}" method="POST" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?\\n\\nNomor Invoice: {{ $purchaseOrder->invoice_number }}\\nTotal: Rp{{ number_format($purchaseOrder->total_price, 0, ',', '.') }}');">
                            @csrf
                            @method('DELETE')
                            {{-- <button type="submit" class="btn btn-danger" {{ $purchaseOrder->status == 'received' ? 'disabled' : '' }}>
                                <i class="fas fa-trash-alt me-1"></i> Hapus Pesanan
                            </button> --}}
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
                    {{-- <a href="{{ route('purchase_order_items.create', ['purchase_order_id' => $purchaseOrder->id]) }}" 
                       class="btn btn-success btn-sm ms-auto" {{ $purchaseOrder->status == 'canceled' ? 'disabled' : '' }}>
                        <i class="fas fa-plus me-1"></i> Tambah Item Baru
                    </a> --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Sparepart</th>
                                    <th>Kode Part</th>
                                    <th class="text-end">Kuantitas</th>
                                    <th class="text-end">Harga Beli</th>
                                    <th class="text-end">Stok Saat Ini</th>
                                    <th class="text-end">Total Item</th>
                                    <th>Tanggal Kadaluarsa</th>
                                    <th>Catatan Item</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchaseOrder->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->sparepart->name ?? 'N/A' }}
                                        @if(!$item->sparepart)
                                            <span class="badge bg-danger">Sparepart dihapus</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->sparepart->code_part ?? 'N/A' }}</td>
                                    <td class="text-end">{{ number_format($item->quantity, 0) }}</td>
                                    <td class="text-end">Rp{{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        @if($item->sparepart)
                                            {{ number_format($item->sparepart->stock, 0) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">Rp{{ number_format($item->quantity * $item->purchase_price, 0, ',', '.') }}</td>
                                    <td>
                                        @if($item->expired_date)
                                            {{ $item->expired_date->translatedFormat('d M Y') }}
                                            @if($item->expired_date->isPast())
                                                <span class="badge bg-danger">Kadaluarsa</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->notes)
                                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="{{ $item->notes }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-box-open fa-2x mb-2"></i><br>
                                        Belum ada item untuk pesanan ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <th colspan="5" class="text-end">Total:</th>
                                    <th class="text-end">Rp{{ number_format($purchaseOrder->total_price, 0, ',', '.') }}</th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('purchase_orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Pesanan
        </a>
        
        @if($purchaseOrder->status == 'pending')
            <form action="{{ route('purchase_orders.update-status', $purchaseOrder->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="received">
                <button type="submit" class="btn btn-success me-2">
                    <i class="fas fa-check-circle me-1"></i> Tandai sebagai Diterima
                </button>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi tooltip
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endpush