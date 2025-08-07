@extends('layouts.master')
@section('title', 'Log Pergerakan Stok (FIFO)')

@section('content')
    <div class="container">
        @foreach($spareparts as $sparepart)
            <div class="card mb-4">
                <div class="card-header">
                    <strong>{{ $sparepart->name }}</strong> (Kode: {{ $sparepart->code_part }})
                </div>
                <div class="card-body">
                    <p><strong>Masuk (Purchase Order - FIFO):</strong></p>
                    <ul>
                        @foreach($sparepart->purchaseOrderItems->sortBy('expired_date') as $in)
                            <li>
                                Tgl: {{ $in->purchaseOrder->order_date->format('d-m-Y') }},
                                Exp: {{ optional($in->expired_date)->format('d-m-Y') ?? '-' }},
                                Qty: {{ $in->quantity }},
                                Harga Beli: Rp {{ number_format($in->purchase_price) }}
                            </li>
                        @endforeach
                    </ul>

                    <p><strong>Keluar (Transaksi):</strong></p>
                    <ul>
                        @php
                            $stockLayers = $sparepart->purchaseOrderItems
                                ->sortBy('expired_date')
                                ->map(fn($po) => [
                                    'id' => $po->id,
                                    'qty' => $po->quantity,
                                    'sisa' => $po->quantity,
                                    'harga' => $po->purchase_price,
                                    'tanggal' => $po->purchaseOrder->order_date->format('d-m-Y'),
                                ]);
                        @endphp

                        @foreach($sparepart->transactionItems->sortBy('created_at') as $out)
                            @php
                                $jumlahKeluar = $out->quantity;
                                $rincianFIFO = [];
                                foreach ($stockLayers as &$layer) {
                                    if ($jumlahKeluar <= 0) break;
                                    if ($layer['sisa'] > 0) {
                                        $pakai = min($layer['sisa'], $jumlahKeluar);
                                        $rincianFIFO[] = $pakai . ' dari PO ' . $layer['tanggal'] . ' (Rp ' . number_format($layer['harga']) . ')';
                                        $layer['sisa'] -= $pakai;
                                        $jumlahKeluar -= $pakai;
                                    }
                                }
                            @endphp
                            <li>
                                Tgl: {{ $out->created_at->format('d-m-Y') }},
                                Qty: {{ $out->quantity }},
                                Harga Jual: Rp {{ number_format($out->price) }}<br>
                                <small><em>FIFO: {!! implode(', ', $rincianFIFO) !!}</em></small>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach

        {{ $spareparts->links() }}
    </div>
@endsection
