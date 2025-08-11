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
            padding: 1.5rem 2rem 0;
        }

        .card-body {
            padding: 1.5rem 2rem;
        }

        /* Custom styling for top navigation tabs (Pills) - Green Theme */
        .nav-pills .nav-link {
            padding: 10px 20px;
            background-color: #f0f2f5;
            border: none;
            border-radius: 6px;
            color: #6a7f8e;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: none;
            margin-right: 8px;
        }

        .nav-pills .nav-link.active {
            background-color: #22c55e !important;
            /* Green for active tab */
            color: white !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1) !important;
            transform: translateY(-1px);
        }

        .nav-pills .nav-link:hover:not(.active) {
            background-color: #e0e2e5;
        }

        /* Search and Filter Area - Green Primary Button */
        .filter-area {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .filter-area .form-select {
            height: 38px;
            border-radius: 6px;
            font-size: 0.95rem;
            border: 1px solid #d1d9e6;
            padding: 0.375rem 1rem;
            flex-grow: 1;
            max-width: 300px;
        }

        .filter-area .btn {
            height: 38px;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            padding: 0.375rem 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .filter-area .btn-primary {
            background-color: #22c55e;
            /* Green for primary button */
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

        /* Table Styling */
        .table {
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-bottom: 0;
        }

        .table th,
        .table td {
            padding: 1rem 1.2rem;
            vertical-align: middle;
            background-color: #ffffff;
            border: none;
        }

        .table thead th {
            background-color: #f7f9fc;
            color: #6a7f8e;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
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
            transform: translateY(-2px);
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

        /* Status Badges */
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.75rem;
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

        /* Pagination */
        .pagination-custom {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-top: 25px;
            gap: 8px;
        }

        .pagination-custom .page-item .page-link {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            border: 1px solid #e0e6ed;
            border-radius: 8px;
            text-decoration: none;
            color: #6a7f8e;
            font-weight: 500;
            transition: all 0.2s ease;
            background-color: #f7f9fc;
        }

        .pagination-custom .page-item.active .page-link {
            background-color: #22c55e !important;
            /* Green for active pagination */
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
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center border-bottom border-light ">
            <div class="d-flex ">
                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-available-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-available" type="button" role="tab" aria-controls="pills-available"
                            aria-selected="true">Stok Tersedia</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-expired-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-expired" type="button" role="tab" aria-controls="pills-expired"
                            aria-selected="false">Stok
                            Kadaluarsa</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-empty-tab" data-bs-toggle="pill" data-bs-target="#pills-empty"
                            type="button" role="tab" aria-controls="pills-empty" aria-selected="false">Stok
                            Kosong</button>
                    </li>
                </ul>
            </div>
            <div class=" d-flex justify-between">
                <button class="  btn btn-outline-primary mb-2 mb-md-0 me-md-2" onclick="window.print()">
                    {{-- Added mb-2 and me-md-2 --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                        <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                    </svg>
                    Cetak Laporan
                </button>
                <a href="{{ route('reports.export-excel', ['report_type' => 'stock', 'sparepart_id' => request('sparepart_id'), 'export_title' => 'Laporan_Stok_Sparepart']) }}"
                    class="btn btn-outline-success  ">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                        <path d="M10 12l4 4m0 -4l-4 4"></path>
                        <path d="M12 8v8m-2 -2l2 2l2 -2"></path>
                    </svg>
                    Export Excel
                </a>
            </div>
        </div> 
        <div class="card-body">
            {{-- date filter --}}
            <form action="{{ route('report.sparepart-report') }}" method="GET" class="mb-4 filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2 d-flex justify-content-between align-items-center g-5">
                        <button type="submit" class="btn btn-primary me-2">Cari</button>
                        <a href="{{ route('report.sparepart-report') }}" class="btn btn-secondary">Reset</a>
                    </div>
                     
                    <input type="hidden" name="tab" id="active_tab_input" value="{{ $activeTab }}">
                </div>
            </form>


            <div class="tab-content" id="pills-tabContent">
                {{-- Available Stock Tab --}}
                <div class="tab-pane fade show {{ $activeTab == 'available' ? 'active' : '' }}" id="pills-available"
                    role="tabpanel" aria-labelledby="pills-available-tab">
                    <div class="table-responsive">
                        <table class="table" id="sparepart_report_table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Part</th>
                                    <th>Nama Sparepart</th>
                                    <th>Kategori</th>
                                    <th>Stok Tersedia</th>
                                    <th>Harga Beli Terakhir</th>
                                    <th>Tgl Kadaluarsa Terdekat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($spareparts->filter(function($s) { return $s->available_stock > 0; }) as $index => $sparepart)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $sparepart->code_part ?? '-' }}</td>
                                        <td>{{ $sparepart->name }}</td>
                                        <td>{{ $sparepart->category->name ?? 'N/A' }}</td>
                                        <td>
                                            <span
                                                class="status-badge available">{{ number_format($sparepart->available_stock) }}</span>
                                        </td>
                                        <td>
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
                                        <td>
                                            @php
                                                // Ambil item pembelian dengan tanggal kedaluwarsa terdekat yang stoknya > 0 DAN belum kedaluwarsa.
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
                                        <td colspan="8" class="text-center text-muted py-4">Tidak ada stok sparepart
                                            yang
                                            tersedia.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        {{ $spareparts->links() }}
                    </div>
                </div>

                {{-- Expired Stock Tab --}}
                <div class="tab-pane fade show {{ $activeTab == 'expired' ? 'active' : '' }}" id="pills-expired"
                    role="tabpanel" aria-labelledby="pills-expired-tab">
                    <div class="table-responsive">
                        <table class="table" id="sparepart_report_table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Part</th>
                                    <th>Nama Sparepart</th>
                                    <th>Kategori</th>
                                    <th>Supplier</th>
                                    <th>Jumlah Kadaluarsa</th>
                                    <th>Harga Beli</th>
                                    <th>Tgl Kadaluarsa</th>
                                    <th>Catatan</th>
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
                                            <td>{{ $sparepart->supplier->name ?? 'N/A' }}</td>
                                            <td>
                                                <span
                                                    class="status-badge expired">{{ number_format($item->quantity) }}</span>
                                            </td>
                                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->expired_date)->format('d M Y') }}</td>
                                            <td>{{ $item->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">Tidak ada stok sparepart
                                            yang kadaluarsa.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Empty Stock Tab --}}
                <div class="tab-pane fade show {{ $activeTab == 'empty' ? 'active' : '' }}" id="pills-empty"
                    role="tabpanel" aria-labelledby="pills-empty-tab">
                    <div class="table-responsive ">
                        <table class="table" id="sparepart_report_table">
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
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $sparepart->code_part ?? '-' }}</td>
                                        <td>{{ $sparepart->name }}</td>
                                        <td>{{ $sparepart->category->name ?? 'N/A' }}</td>
                                        <td>{{ $sparepart->supplier->name ?? 'N/A' }}</td>
                                        <td>Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Tidak ada sparepart dengan
                                            stok kosong.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            $('#sparepart_report_table').DataTable({
                order: [
                    [5, 'desc']
                ]
            });
        })
        // tab start
        $(document).ready(function() {
            //   an event listener to the tab buttons to update the hidden input
            $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                const targetTab = $(e.target).attr('data-bs-target');
                const tabId = targetTab.replace('#pills-', '');
                $('#active_tab_input').val(tabId); // Update the hidden input value

                //  search history
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('tab', tabId);
                const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
                history.pushState(null, '', newUrl);
            });

            // Set the initial active tab from the URL on page load
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab');
            if (activeTab) {
                $(`#pills-${activeTab}-tab`).tab('show');
            }
        });
        // tab end
        // filter dropdown search
        // $(document).ready(function() {
        //     $('#sparepart_filter').select2({
        //         theme: "bootstrap-5",
        //         width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' :
        //             'style',
        //         placeholder: "-- Semua Sparepart --",
        //         allowClear: true,
        //     });
        // unused tab
        // const urlParams = new URLSearchParams(window.location.search);
        // const activeTab = urlParams.get('tab');
        // if (activeTab) {
        //     $(`#pills-${activeTab}-tab`).tab('show');
        // }

        // $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
        //     const targetTab = $(e.target).attr('data-bs-target');
        //     const tabId = targetTab.replace('#pills-', '');
        //     const urlParams = new URLSearchParams(window.location.search);
        //     urlParams.set('tab', tabId);
        //     const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
        //     history.pushState(null, '', newUrl);
        // });
    </script>
@endpush
