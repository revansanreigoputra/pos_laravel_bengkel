@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-header">
        <h1 class="welcome-message">Selamat datang, <span class="user-name">{{ auth()->user()->name }}</span>!</h1>
        <p class="welcome-subtext">Berikut ringkasan aktivitas sistem</p>
    </div>

    <div class="row mt-4 dashboard-cards">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card category-card">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h5 class="card-title stat-title">Kategori</h5>
                    <p class="display-6 stat-number">{{ $categoryCount }}</p>
                    <div class="stat-footer">
                        <a href="{{ route('category.index') }}" class="stat-link">Lihat Semua <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card customer-card">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title stat-title">Konsumen</h5>
                    <p class="display-6 stat-number">{{ $customerCount }}</p>
                    <div class="stat-footer">
                        <a href="{{ route('customer.index') }}" class="stat-link">Lihat Semua <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card supplier-card">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h5 class="card-title stat-title">Supplier</h5>
                    <p class="display-6 stat-number">{{ $supplierCount }}</p>
                    <div class="stat-footer">
                        <a href="{{ route('supplier.index') }}" class="stat-link">Lihat Semua <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card stat-card user-card">
                <div class="card-body">
                    <div class="stat-icon">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h5 class="card-title stat-title">User</h5>
                    <p class="display-6 stat-number">{{ $userCount }}</p>
                    <div class="stat-footer">
                        <a href="{{ route('user.index') }}" class="stat-link">Lihat Semua <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- report stok --}}
    <div class="row mt-4 dashboard-cards">
        <div class="col-lg-5 mb-4 d-flex">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title"> Grafik Stok Sparepart </h5>
                    <div class="m-3">
                        <a href="{{ route('report.sparepart-report') }}" class="stat-link">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body text-center">
                    <canvas id="sparepartStockChartData"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7 mb-4 d-flex">
            <div class="card chart-card  flex-fill">
                <div class="card-header">
                    <h5 class="card-title">Grafik Penjualan</h5>
                    <div class="m-3">
                        <a href="{{ route('report.transaction') }}" class="stat-link">Lihat Semua</a>
                    </div>

                </div>
                <div class="card-body justify-content-center  align-items-center">
                    <canvas id="monthlySalesChartData"></canvas>
                </div>
            </div>
        </div>
    </div>
    {{-- Bagian report transaksi --}}
    <div class="row mt-4 dashboard-report">
        <div class="col-lg-6 mb-4 d-flex">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title">Pembelian Sparepart Terakhir</h5>
                    <div class="m-3 flex-end">
                        <a href="{{ route('purchase_orders.index') }}" class="stat-link">Lihat Semua</a>
                    </div>
                </div>
                <div class=" table-responsive ">

                    <table class=" table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Supplier</th>
                                <th>Tanggal Pesanan</th>
                                <th>Total Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentPurchaseOrders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ Str::limit($order->supplier->name, 10) }}</td>
                                    <td>{{ $order->order_date->format('d-m-Y') }}</td>
                                    <td class="status-badge">{{ $order->items->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>



                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4 d-flex">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title">Transaksi Penjualan Terakhir</h5>
                    <div class="m-3 flex-end">
                        <a href="{{ route('transaction.index') }}" class="stat-link">Lihat Semua</a>
                    </div>
                </div>
                <div class=" table-responsive ">

                    <table class=" table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Pelanggan</th>
                                <th>Tanggal Pesanan</th>
                                <th>Total Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ Str::limit($transaction->customer->name, 10) }}</td>
                                    <td>{{ $transaction->transaction_date }}</td>
                                    <td>{{ $transaction->items->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>



                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .dashboard-report {
            height: 100%;
        }
    </style>

@endsection

@push('addon-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {

        // --- Inisialisasi Chart Stok Sparepart ---
        const ctxSparepart = document.getElementById('sparepartStockChartData');
        if (ctxSparepart) {
            const sparepartStockChart = new Chart(ctxSparepart, {
                type: 'doughnut',
                data: {
                    labels: @json($sparepartStockChartData['labels']),
                    datasets: [{
                        label: 'Jumlah Item',
                        data: @json($sparepartStockChartData['data']),
                        backgroundColor: @json($sparepartStockChartData['colors']),
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12,
                                    weight: 'light'
                                },
                                boxWidth: 5,
                                padding: 5
                            }
                        },
                        title: {
                            display: false,
                        }
                    },
                    elements: {
                        arc: {
                            spacing: 2
                        }
                    },
                    scales: {
                        y: {
                            display: false,
                            grid: {
                                display: false
                            },
                            ticks: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // --- Inisialisasi Chart Penjualan Bulanan ---
        const salesCtx = document.getElementById('monthlySalesChartData');
        if (salesCtx) {
            const monthlySalesChart = new Chart(salesCtx.getContext('2d'), { // Perbaikan di sini
                type: 'bar',
                data: {
                    labels: @json($monthlySalesChartData['labels']),
                    datasets: [{
                        label: 'Total Penjualan (Rp)',
                        data: @json($monthlySalesChartData['data']),
                        backgroundColor: '#3b82f6',
                        borderColor: '#1e40af',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        title: {
                            display: false,
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            display: false,
                            grid: {
                                display: false
                            },
                            ticks: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush