@php
    use Illuminate\Support\Carbon;
@endphp

@extends('layouts.master')

@section('title', 'Laporan Sparepart')

@section('styles')
    <style>
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            background-color: #fff;
        }

        .card-header {
            border-bottom: none;
            background-color: #fff;
            padding: 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        /* Custom styling for top navigation tabs (Pills) - Green Theme */
        .nav-pills .nav-link {
            padding: 8px 12px;
            background-color: #f0f2f5;
            border: none;
            border-radius: 6px;
            color: #6a7f8e;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: none;
            margin-right: 4px;
            margin-bottom: 8px;
            font-size: 0.85rem;
        }

        .nav-pills .nav-link.active {
            background-color: #22c55e !important;
            color: white !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1) !important;
            transform: translateY(-1px);
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: #e0e2e5;
        }

        /* Mobile Navigation Tabs */
        @media (max-width: 768px) {
            .nav-pills {
                flex-direction: column;
            }
            
            .nav-pills .nav-link {
                margin-right: 0;
                margin-bottom: 8px;
                text-align: center;
                width: 100%;
            }
        }

        /* Search and Filter Area */
        .filter-area {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-area .form-select {
            height: 38px;
            border-radius: 6px;
            font-size: 0.9rem;
            border: 1px solid #d1d9e6;
            padding: 0.375rem 1rem;
            flex-grow: 1;
            max-width: 300px;
        }

        .filter-area .btn {
            height: 38px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.375rem 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .filter-area .btn-primary {
            background-color: #22c55e;
            border-color: #22c55e;
            color: white;
        }

        .filter-area .btn-outline-success {
            color: #22c55e;
            border-color: #22c55e;
            background-color: transparent;
            transition: all 0.2s ease;
        }

        .filter-area .btn-outline-success:hover {
            background-color: #22c55e;
            color: white;
        }

        .filter-area .btn-outline-primary {
            color: #22c55e;
            border-color: #22c55e;
            background-color: transparent;
            transition: all 0.2s ease;
        }

        .filter-area .btn-outline-primary:hover {
            background-color: #22c55e;
            color: white;
        }

        /* Mobile Header Actions */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column !important;
                align-items: stretch !important;
            }
            
            .card-header .d-flex:last-child {
                margin-top: 1rem;
                flex-direction: column;
                gap: 8px;
            }
            
            .card-header .btn {
                width: 100%;
                justify-content: center;
                font-size: 0.9rem;
                padding: 10px;
            }
        }

        /* Mobile Filter Form */
        @media (max-width: 768px) {
            .filter-form .row {
                margin: 0;
            }
            
            .filter-form .col-md-3,
            .filter-form .col-md-2 {
                padding: 0 5px;
                margin-bottom: 15px;
            }
            
            .filter-form .form-select,
            .filter-form .form-control {
                font-size: 0.9rem;
            }
            
            .filter-form .d-flex {
                flex-direction: column;
                gap: 8px;
            }
            
            .filter-form .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Table Styling */
        .table {
            border-collapse: separate;
            border-spacing: 0 8px;
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        .table th,
        .table td {
            padding: 0.8rem;
            vertical-align: middle;
            background-color: #ffffff;
            border: none;
        }

        .table thead th {
            background-color: #f7f9fc;
            color: #6a7f8e;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .table thead tr th:first-child {
            border-top-left-radius: 8px;
        }

        .table thead tr th:last-child {
            border-top-right-radius: 8px;
        }

        .table tbody tr {
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .table tbody tr:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }

        .table tbody tr td:first-child {
            border-bottom-left-radius: 8px;
            border-top-left-radius: 8px;
        }

        .table tbody tr td:last-child {
            border-bottom-right-radius: 8px;
            border-top-right-radius: 8px;
        }

        /* Mobile Table Responsiveness */
        @media (max-width: 768px) {
            .table {
                font-size: 0.8rem;
                border-spacing: 0 5px;
            }
            
            .table th,
            .table td {
                padding: 0.6rem 0.5rem;
            }
            
            .table thead th {
                font-size: 0.7rem;
                padding: 0.8rem 0.5rem;
            }
            
            /* Hide less important columns on mobile */
            .table .d-none.d-md-table-cell {
                display: none !important;
            }
            
            .table .d-md-none {
                display: table-cell !important;
            }
            
            /* Stack table data vertically on very small screens */
            @media (max-width: 576px) {
                .table-mobile-stack {
                    display: block;
                }
                
                .table-mobile-stack thead {
                    display: none;
                }
                
                .table-mobile-stack tr {
                    display: block;
                    margin-bottom: 1rem;
                    padding: 1rem;
                    border-radius: 8px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }
                
                .table-mobile-stack td {
                    display: block;
                    padding: 0.5rem 0;
                    border: none;
                    text-align: left;
                    background: none !important;
                }
                
                .table-mobile-stack td:before {
                    content: attr(data-label) ": ";
                    font-weight: bold;
                    color: #6a7f8e;
                    display: inline-block;
                    min-width: 120px;
                }
            }
        }

        /* Status Badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.7rem;
            white-space: nowrap;
            display: inline-block;
            text-align: center;
        }

        .status-badge.available {
            background-color: #e6f7ed;
            color: #28a745;
        }

        .status-badge.expired {
            background-color: #fcebeb;
            color: #dc3545;
        }

        .status-badge.empty {
            background-color: #fff3cd;
            color: #856404;
        }

        .text-muted {
            color: #a0a0a0 !important;
        }

        /* Mobile Status Badges */
        @media (max-width: 768px) {
            .status-badge {
                font-size: 0.65rem;
                padding: 3px 6px;
            }
        }

        /* Pagination */
        .pagination-custom {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 20px;
            gap: 4px;
            flex-wrap: wrap;
        }

        .pagination-custom .page-item .page-link {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border: 1px solid #e0e6ed;
            border-radius: 6px;
            text-decoration: none;
            color: #6a7f8e;
            font-weight: 500;
            transition: all 0.2s ease;
            background-color: #f7f9fc;
            font-size: 0.85rem;
        }

        .pagination-custom .page-item.active .page-link {
            background-color: #22c55e !important;
            border-color: #23ad55 !important;
            color: white !important;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .pagination-custom .page-item .page-link:hover:not(.active) {
            background-color: #eaf1f9;
            border-color: #d1e0f0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        /* Mobile Pagination */
        @media (max-width: 768px) {
            .pagination-custom .page-item .page-link {
                min-width: 28px;
                height: 28px;
                font-size: 0.8rem;
                padding: 0 6px;
            }
        }

        /* Mobile Utility Classes */
        @media (max-width: 768px) {
            .mobile-scroll {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .mobile-text-sm {
                font-size: 0.8rem !important;
            }
            
            .mobile-p-2 {
                padding: 0.5rem !important;
            }
        }

        /* Small text for mobile */
        @media (max-width: 576px) {
            .small-mobile {
                font-size: 0.75rem;
                line-height: 1.2;
            }
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-start border-bottom border-light">
            <div class="w-100">
                <ul class="nav nav-pills mb-3 mb-md-0" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-available-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-available" type="button" role="tab" aria-controls="pills-available"
                            aria-selected="true">Stok Tersedia</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-expired-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-expired" type="button" role="tab" aria-controls="pills-expired"
                            aria-selected="false">Stok Kadaluarsa</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-empty-tab" data-bs-toggle="pill" data-bs-target="#pills-empty"
                            type="button" role="tab" aria-controls="pills-empty" aria-selected="false">Stok Kosong</button>
                    </li>
                </ul>
            </div>
            <div class="d-flex flex-column flex-md-row">
                <a id="export-pdf-btn" class="btn btn-outline-primary mb-2 mb-md-0 me-md-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                        <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                    </svg>
                    <span class="d-none d-sm-inline">Cetak Laporan</span>
                    <span class="d-inline d-sm-none">PDF</span>
                </a>
                <a href="{{ route('report.export-sparepart-report',  ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                    class="btn btn-outline-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                        <path d="M10 12l4 4m0 -4l-4 4"></path>
                        <path d="M12 8v8m-2 -2l2 2l2 -2"></path>
                    </svg>
                    <span class="d-none d-sm-inline">Export Excel</span>
                    <span class="d-inline d-sm-none">Excel</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Date Filter --}}
            <form action="{{ route('report.sparepart-report') }}" method="GET" class="mb-4 filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="payment_method" class="form-label small-mobile">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" class="form-select">
                            <option value="">-- Semua Metode --</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="credit" {{ request('payment_method') == 'credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="start_date" class="form-label small-mobile">Dari Tanggal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="end_date" class="form-label small-mobile">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-12 col-md-3 d-flex flex-column flex-md-row gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Cari</button>
                        <a href="{{ route('report.sparepart-report') }}" class="btn btn-secondary flex-fill">Reset</a>
                    </div>
                    <input type="hidden" name="tab" id="active_tab_input" value="{{ $activeTab }}">
                </div>
            </form>

            <div class="tab-content" id="pills-tabContent">
                {{-- Available Stock Tab --}}
                <div class="tab-pane fade show {{ $activeTab == 'available' ? 'active' : '' }}" id="pills-available"
                    role="tabpanel" aria-labelledby="pills-available-tab">
                    <div class="table-responsive mobile-scroll">
                        <table class="table d-none d-md-table" id="available_table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Part</th>
                                    <th>Nama Sparepart</th>
                                    <th>Kategori</th>
                                    <th>Stok Tersedia</th>
                                    <th class="d-none d-lg-table-cell">Harga Beli Terakhir</th>
                                    <th class="d-none d-xl-table-cell">Tgl Kadaluarsa Terdekat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($spareparts->filter(function($s) { return $s->available_stock > 0; }) as $index => $sparepart)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $sparepart->code_part ?? '-' }}</td>
                                        <td>{{ $sparepart->name }}</td>
                                        <td>{{ $sparepart->category->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="status-badge available">{{ number_format($sparepart->available_stock) }}</span>
                                        </td>
                                        <td class="d-none d-lg-table-cell">
                                            @php
                                                $latestPurchase = $sparepart->purchaseOrderItems->first();
                                            @endphp
                                            @if ($latestPurchase)
                                                Rp {{ number_format($latestPurchase->purchase_price, 0, ',', '.') }}
                                                <br>
                                                <small class="text-muted">
                                                    ({{ \Carbon\Carbon::parse($latestPurchase->created_at)->format('d-m-Y') }})
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="d-none d-xl-table-cell">
                                            @php
                                                $nearestValidExpiredItem = $sparepart->purchaseOrderItems
                                                    ->filter(function ($item) {
                                                        return $item->expired_date &&
                                                            Carbon::parse($item->expired_date)->isFuture() &&
                                                            $item->quantity - $item->sold_quantity > 0;
                                                    })
                                                    ->sortBy('expired_date')
                                                    ->first();
                                            @endphp
                                            @if ($nearestValidExpiredItem)
                                                {{ \Carbon\Carbon::parse($nearestValidExpiredItem->expired_date)->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Tidak ada stok sparepart yang tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Mobile Stack Table --}}
                        <div class="d-md-none">
                            @forelse ($spareparts->filter(function($s) { return $s->available_stock > 0; }) as $index => $sparepart)
                                <div class="card mb-3 mobile-p-2">
                                    <div class="card-body p-3">
                                        <div class="row">
                                            <div class="col-12">
                                                <h6 class="mb-2">{{ $sparepart->name }}</h6>
                                                <div class="small text-muted mb-2">
                                                    <strong>Kode:</strong> {{ $sparepart->code_part ?? '-' }} | 
                                                    <strong>Kategori:</strong> {{ $sparepart->category->name ?? 'N/A' }}
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="status-badge available">{{ number_format($sparepart->available_stock) }} unit</span>
                                                    @php
                                                        $latestPurchase = $sparepart->purchaseOrderItems->first();
                                                    @endphp
                                                    @if ($latestPurchase)
                                                        <small class="text-muted">
                                                            Rp {{ number_format($latestPurchase->purchase_price, 0, ',', '.') }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">Tidak ada stok sparepart yang tersedia.</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $spareparts->links() }}
                    </div>
                </div>

                {{-- Expired Stock Tab --}}
                <div class="tab-pane fade show {{ $activeTab == 'expired' ? 'active' : '' }}" id="pills-expired"
                    role="tabpanel" aria-labelledby="pills-expired-tab">
                    <div class="table-responsive mobile-scroll">
                        <table class="table d-none d-md-table" id="expired_table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Part</th>
                                    <th>Nama Sparepart</th>
                                    <th>Kategori</th>
                                    <th class="d-none d-lg-table-cell">Supplier</th>
                                    <th>Jumlah Kadaluarsa</th>
                                    <th class="d-none d-lg-table-cell">Harga Beli</th>
                                    <th class="d-none d-xl-table-cell">Tgl Kadaluarsa</th>
                                    <th class="d-none d-xl-table-cell">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $expiredCounter = 0; @endphp
                                @forelse ($spareparts as $sparepart)
                                    @foreach ($sparepart->purchaseOrderItems->whereNotNull('expired_date')->where('expired_date', '<', \Carbon\Carbon::today())->where('quantity', '>', 0) as $item)
                                        @php $expiredCounter++; @endphp
                                        <tr>
                                            <td>{{ $expiredCounter }}</td>
                                            <td>{{ $sparepart->code_part ?? '-' }}</td>
                                            <td>{{ $sparepart->name }}</td>
                                            <td>{{ $sparepart->category->name ?? 'N/A' }}</td>
                                            <td class="d-none d-lg-table-cell">{{ $sparepart->supplier->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="status-badge expired">{{ number_format($item->quantity) }}</span>
                                            </td>
                                            <td class="d-none d-lg-table-cell">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td class="d-none d-xl-table-cell">{{ \Carbon\Carbon::parse($item->expired_date)->format('d M Y') }}</td>
                                            <td class="d-none d-xl-table-cell">{{ $item->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">Tidak ada stok sparepart yang kadaluarsa.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Mobile Stack Table for Expired --}}
                        <div class="d-md-none">
                            @php $expiredCounter = 0; @endphp
                            @forelse ($spareparts as $sparepart)
                                @foreach ($sparepart->purchaseOrderItems->whereNotNull('expired_date')->where('expired_date', '<', \Carbon\Carbon::today())->where('quantity', '>', 0) as $item)
                                    @php $expiredCounter++; @endphp
                                    <div class="card mb-3 mobile-p-2">
                                        <div class="card-body p-3">
                                            <h6 class="mb-2">{{ $sparepart->name }}</h6>
                                            <div class="small text-muted mb-2">
                                                <strong>Kode:</strong> {{ $sparepart->code_part ?? '-' }} | 
                                                <strong>Kategori:</strong> {{ $sparepart->category->name ?? 'N/A' }}
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="status-badge expired">{{ number_format($item->quantity) }} unit</span>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($item->expired_date)->format('d M Y') }}</small>
                                            </div>
                                            <div class="small text-muted">
                                                <strong>Harga:</strong> Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @empty
                                <div class="text-center text-muted py-4">Tidak ada stok sparepart yang kadaluarsa.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Empty Stock Tab --}}
                <div class="tab-pane fade show {{ $activeTab == 'empty' ? 'active' : '' }}" id="pills-empty"
                    role="tabpanel" aria-labelledby="pills-empty-tab">
                    <div class="table-responsive mobile-scroll">
                        <table class="table d-none d-md-table" id="empty_table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Part</th>
                                    <th>Nama Sparepart</th>
                                    <th>Kategori</th>
                                    <th>Harga Jual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($spareparts->filter(function($s) { return $s->available_stock <= 0; }) as $index => $sparepart)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $sparepart->code_part ?? '-' }}</td>
                                        <td>{{ $sparepart->name }}</td>
                                        <td>{{ $sparepart->category->name ?? 'N/A' }}</td>
                                        <td>Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Tidak ada sparepart dengan stok kosong.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Mobile Stack Table for Empty Stock --}}
                        <div class="d-md-none">
                            @forelse ($spareparts->filter(function($s) { return $s->available_stock <= 0; }) as $index => $sparepart)
                                <div class="card mb-3 mobile-p-2">
                                    <div class="card-body p-3">
                                        <h6 class="mb-2">{{ $sparepart->name }}</h6>
                                        <div class="small text-muted mb-2">
                                            <strong>Kode:</strong> {{ $sparepart->code_part ?? '-' }} | 
                                            <strong>Kategori:</strong> {{ $sparepart->category->name ?? 'N/A' }}
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="status-badge empty">Stok Kosong</span>
                                            <small class="text-muted">
                                                <strong>Harga Jual:</strong> Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">Tidak ada sparepart dengan stok kosong.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'available';
            $('#pills-' + activeTab + '-tab').tab('show');
            $('#active_tab_input').val(activeTab);

            function updateExportPdfLink() {
                const tab = $('#active_tab_input').val();
                const start = $('#start_date').val();
                const end = $('#end_date').val();

                let url = `{{ route('report.exportPDF-sparepart') }}?tab=${tab}`;

                // Tambahkan parameter tanggal hanya jika terisi
                if (start) {
                    url += `&start_date=${start}`;
                }
                if (end) {
                    url += `&end_date=${end}`;
                }

                $('#export-pdf-btn').attr('href', url);
            }
            
            // Run once on page load
            updateExportPdfLink();

            // On tab change
            $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                const tabId = $(e.target).attr('data-bs-target').replace('#pills-', '');
                $('#active_tab_input').val(tabId);
                updateExportPdfLink();
            });

            // On date change
            $('.filter-form input[type="date"]').on('change', updateExportPdfLink);

            // Mobile responsive table handling
            function handleMobileView() {
                if (window.innerWidth < 768) {
                    // Additional mobile-specific JavaScript if needed
                    console.log('Mobile view active');
                } else {
                    console.log('Desktop view active');
                }
            }

            // Check on load and resize
            handleMobileView();
            $(window).resize(handleMobileView);

            // Smooth scrolling for mobile tables
            $('.mobile-scroll').on('scroll', function() {
                // Add any scroll-based functionality if needed
            });

            // Touch-friendly interactions for mobile
            if ('ontouchstart' in window) {
                $('.table tbody tr, .card').addClass('touch-friendly');
            }
        });
    </script>
@endpush