@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-header mb-4">
        <h1 class="welcome-message">Selamat datang, <span class="user-name">{{ auth()->user()->name }}</span>!</h1>
        <p class="welcome-subtext">Berikut ringkasan aktivitas sistem</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row dashboard-cards">
        @php
            $cards = [
                [
                    'title' => 'Kategori',
                    'count' => $categoryCount,
                    'icon' => 'master-data-icon',
                    'route' => 'category.index',
                    'class' => 'category-card',
                    'color' => 'primary'
                ],
                [
                    'title' => 'Pelanggan', 
                    'count' => $customerCount,
                    'icon' => 'user-management-icon',
                    'route' => 'customer.index',
                    'class' => 'customer-card',
                    'color' => 'success'
                ],
                [
                    'title' => 'Supplier',
                    'count' => $supplierCount, 
                    'icon' => 'transaction-icon',
                    'route' => 'supplier.index',
                    'class' => 'supplier-card',
                    'color' => 'warning'
                ],
                [
                    'title' => 'User',
                    'count' => $userCount,
                    'icon' => 'user-management-icon', 
                    'route' => 'user.index',
                    'class' => 'user-card',
                    'color' => 'info'
                ]
            ];
        @endphp

        @foreach($cards as $card)
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card stat-card {{ $card['class'] }} h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="stat-icon-wrapper me-3">
                            <div class="stat-icon bg-{{ $card['color'] }}">
                                @if($card['icon'] == 'master-data-icon')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 3l8 4.5v9l-8 4.5l-8-4.5v-9z" />
                                        <path d="M12 12l8-4.5" />
                                        <path d="M12 12v9" />
                                        <path d="M12 12l-8-4.5" />
                                        <path d="M16 5.25l-8 4.5" />
                                    </svg>
                                @elseif($card['icon'] == 'user-management-icon')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        <path d="M8 3.13a4 4 0 0 0 0 7.75" />
                                        <path d="M2 21v-2a4 4 0 0 1 4-4h12a4 4 0 0 1 4 4v2" />
                                    </svg>
                                @elseif($card['icon'] == 'transaction-icon')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 12h18" />
                                        <path d="M3 6h18" />
                                        <path d="M3 18h18" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="card-title stat-title mb-1 text-muted">{{ $card['title'] }}</h5>
                            <h2 class="stat-number text-dark mb-0 fw-bold">{{ number_format($card['count']) }}</h2>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pt-0">
                        <a href="{{ route($card['route']) }}" class="btn btn-sm btn-{{ $card['color'] }} w-100">
                            Lihat Semua 
                            <svg xmlns="http://www.w3.org/2000/svg" class="ms-1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12l14 0" />
                                <path d="M15 16l4 -4" />
                                <path d="M15 8l4 4" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts Section -->
    <div class="row mt-4">
        <div class="col-lg-5 mb-4">
            <div class="card chart-card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18" />
                            <path d="M9 17l3-3l4 4" />
                            <path d="M13 13l2-2l3 3" />
                        </svg>Grafik Stok Sparepart
                    </h5>
                    <a href="{{ route('report.sparepart-report') }}" class="btn btn-sm btn-outline-primary text-white">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body text-center">
                    @if(isset($sparepartStockChartData) && !empty($sparepartStockChartData['data']))
                        <canvas id="sparepartStockChartData" width="400" height="300"></canvas>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 300px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-3">
                                <path d="M3 3v18h18" />
                                <path d="M9 17l3-3l4 4" />
                                <path d="M13 13l2-2l3 3" />
                            </svg>
                            <p class="text-muted">Tidak ada data stok tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-7 mb-4">
            <div class="card chart-card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18" />
                            <path d="M9 17l3-3l4 4" />
                            <path d="M13 13l2-2l3 3" />
                        </svg>Grafik Penjualan
                    </h5>
                    <a href="{{ route('report.transaction') }}" class="btn btn-sm btn-outline-success text-white">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($monthlySalesChartData) && !empty($monthlySalesChartData['data']))
                        <canvas id="monthlySalesChartData" width="400" height="300"></canvas>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 300px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-3">
                                <path d="M3 3v18h18" />
                                <path d="M9 17l3-3l4 4" />
                                <path d="M13 13l2-2l3 3" />
                            </svg>
                            <p class="text-muted">Tidak ada data penjualan tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Financial Charts Section -->
    <div class="row mt-4">
        <div class="col-lg-6 mb-4">
            <div class="card chart-card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18" />
                            <path d="M9 17l3-3l4 4" />
                            <path d="M13 13l2-2l3 3" />
                        </svg>Grafik Pengeluaran Perbulan
                    </h5>
                    <a href="{{ route('report.purchase') }}" class="btn btn-sm btn-outline-warning text-white">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($expenseCounts) && !empty($expenseCounts))
                        <canvas id="monthlyExpenseChart" width="400" height="300"></canvas>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 300px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-3">
                                <path d="M3 3v18h18" />
                                <path d="M9 17l3-3l4 4" />
                                <path d="M13 13l2-2l3 3" />
                            </svg>
                            <p class="text-muted">Tidak ada data pengeluaran tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card chart-card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18" />
                            <path d="M9 17l3-3l4 4" />
                            <path d="M13 13l2-2l3 3" />
                        </svg>Grafik Pendapatan Perbulan
                    </h5>
                    <a href="{{ route('report.transaction') }}" class="btn btn-sm btn-outline-success text-white">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($incomeAmounts) && !empty($incomeAmounts))
                        <canvas id="monthlyIncomeChart" width="400" height="300"></canvas>
                    @else
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 300px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-3">
                                <path d="M3 3v18h18" />
                                <path d="M9 17l3-3l4 4" />
                                <path d="M13 13l2-2l3 3" />
                            </svg>
                            <p class="text-muted">Tidak ada data pendapatan tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Section -->
    <div class="row mt-4">
        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12h18" />
                            <path d="M3 6h18" />
                            <path d="M3 18h18" />
                        </svg>Pembelian Sparepart Terakhir
                    </h5>
                    <a href="{{ route('purchase_orders.index') }}" class="btn btn-sm btn-outline-warning">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentPurchaseOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="60">No</th>
                                        <th>Supplier</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Items</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentPurchaseOrders as $order)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ Str::limit($order->supplier->name, 20) }}</strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $order->order_date->format('d M Y') }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning text-white">
                                                    {{ $order->items->count() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-3">
                                <path d="M3 12h18" />
                                <path d="M3 6h18" />
                                <path d="M3 18h18" />
                            </svg>
                            <p class="text-muted">Belum ada pembelian terbaru</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12h18" />
                            <path d="M3 6h18" />
                            <path d="M3 18h18" />
                        </svg>Transaksi Penjualan Terakhir
                    </h5>
                    <a href="{{ route('transaction.index') }}" class="btn btn-sm btn-outline-success">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="60">No</th>
                                        <th>Pelanggan</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Items</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentTransactions as $transaction)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ Str::limit($transaction->customer->name, 20) }}</strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success text-white">
                                                    {{ $transaction->items->count() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-3">
                                <path d="M3 12h18" />
                                <path d="M3 6h18" />
                                <path d="M3 18h18" />
                            </svg>
                            <p class="text-muted">Belum ada transaksi terbaru</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #49a25c 0%, #58c774 100%);
        color: white;
        padding: 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
    }

    .welcome-message {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .user-name {
        color: #ffd700;
        font-weight: 700;
    }

    .stat-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        background: white;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
    }

    .stat-card .card-body {
        position: relative;
        z-index: 2;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--bs-primary), var(--bs-success));
        z-index: 1;
    }

    .stat-card.category-card::before {
        background: linear-gradient(90deg, #0d6efd, #198754);
    }

    .stat-card.customer-card::before {
        background: linear-gradient(90deg, #198754, #20c997);
    }

    .stat-card.supplier-card::before {
        background: linear-gradient(90deg, #ffc107, #fd7e14);
    }

    .stat-card.user-card::before {
        background: linear-gradient(90deg, #0dcaf0, #6f42c1);
    }

    .stat-icon-wrapper {
        min-width: 60px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .stat-icon svg {
        width: 24px !important;
        height: 24px !important;
        stroke: white;
    }

    .stat-number {
        font-weight: 700;
        font-size: 2rem;
    }

    .chart-card {
        border: none;
        border-radius: 10px;
    }

    .chart-card .card-header {
        background: linear-gradient(135deg, #49a25c 0%, #58c774 100%);
        color: white;
        border-radius: 10px 10px 0 0 !important;
        border: none;
    }

    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.03);
    }

    .badge {
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
    }

    .btn {
        transition: all 0.2s ease;
        border-radius: 6px;
        font-weight: 500;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    .card {
        border-radius: 10px;
    }

    .card-header h5 i {
        color: rgba(255,255,255,0.8);
    }

    .text-muted {
        color: #6c757d !important;
    }

    @media (max-width: 768px) {
        .welcome-message {
            font-size: 1.5rem;
        }
        
        .stat-number {
            font-size: 1.5rem;
        }
        
        .dashboard-header {
            padding: 1.5rem;
        }
    }
</style>
@endsection

@push('addon-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Chart.js default configuration
    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
    Chart.defaults.color = '#6b7280';

    // --- Sparepart Stock Chart ---
    const ctxSparepart = document.getElementById('sparepartStockChartData');
    if (ctxSparepart && @json(isset($sparepartStockChartData) && !empty($sparepartStockChartData['data']))) {
        new Chart(ctxSparepart, {
            type: 'doughnut',
            data: {
                labels: @json($sparepartStockChartData['labels'] ?? []),
                datasets: [{
                    label: 'Jumlah Item',
                    data: @json($sparepartStockChartData['data'] ?? []),
                    backgroundColor: @json($sparepartStockChartData['colors'] ?? []),
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 8,
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            boxWidth: 12,
                            boxHeight: 12,
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} items (${percentage}%)`;
                            }
                        }
                    }
                },
                elements: {
                    arc: {
                        spacing: 2
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1000
                }
            }
        });
    }

    // --- Monthly Sales Chart ---
    const salesCtx = document.getElementById('monthlySalesChartData');
    if (salesCtx && @json(isset($monthlySalesChartData) && !empty($monthlySalesChartData['data']))) {
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: @json($monthlySalesChartData['labels'] ?? []),
                datasets: [{
                    label: 'Total Penjualan',
                    data: @json($monthlySalesChartData['data'] ?? []),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                    hoverBackgroundColor: 'rgba(59, 130, 246, 1)',
                    hoverBorderColor: 'rgb(37, 99, 235)',
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: 'rgba(59, 130, 246, 0.5)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Penjualan: Rp ${new Intl.NumberFormat('id-ID').format(context.parsed.y)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                    notation: 'compact',
                                    compactDisplay: 'short'
                                }).format(value);
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // --- Monthly Expense Chart ---
    const expenseCtx = document.getElementById('monthlyExpenseChart');
    if (expenseCtx && @json(isset($expenseCounts) && !empty($expenseCounts))) {
        new Chart(expenseCtx, {
            type: 'line',
            data: {
                labels: @json($months ?? []),
                datasets: [{
                    label: 'Total Pengeluaran',
                    data: @json($expenseCounts ?? []),
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    borderColor: 'rgb(255, 193, 7)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: 'rgb(255, 193, 7)',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: 'rgba(255, 193, 7, 0.5)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Pengeluaran: Rp ${new Intl.NumberFormat('id-ID').format(context.parsed.y)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                    notation: 'compact',
                                    compactDisplay: 'short'
                                }).format(value);
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // --- Monthly Income Chart ---
    const incomeCtx = document.getElementById('monthlyIncomeChart');
    if (incomeCtx && @json(isset($incomeAmounts) && !empty($incomeAmounts))) {
        new Chart(incomeCtx, {
            type: 'line',
            data: {
                labels: @json($months ?? []),
                datasets: [{
                    label: 'Total Pendapatan',
                    data: @json($incomeAmounts ?? []),
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgb(40, 167, 69)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: 'rgb(40, 167, 69)',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: 'rgba(40, 167, 69, 0.5)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Pendapatan: Rp ${new Intl.NumberFormat('id-ID').format(context.parsed.y)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                    notation: 'compact',
                                    compactDisplay: 'short'
                                }).format(value);
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // Add loading states and error handling
    const charts = ['sparepartStockChartData', 'monthlySalesChartData', 'monthlyExpenseChart', 'monthlyIncomeChart'];
    charts.forEach(chartId => {
        const canvas = document.getElementById(chartId);
        if (canvas) {
            canvas.style.opacity = '0';
            setTimeout(() => {
                canvas.style.transition = 'opacity 0.5s ease';
                canvas.style.opacity = '1';
            }, 200);
        }
    });
});
</script>
@endpush
