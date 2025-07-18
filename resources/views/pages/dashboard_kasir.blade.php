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
                        <a href="{{ route('category.index') }}" class="stat-link">Lihat detail <i class="fas fa-arrow-right"></i></a>
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
                        <a href="{{ route('customer.index') }}" class="stat-link">Lihat detail <i class="fas fa-arrow-right"></i></a>
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
                        <a href="{{ route('supplier.index') }}" class="stat-link">Lihat detail <i class="fas fa-arrow-right"></i></a>
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
                        <a href="{{ route('user.index') }}" class="stat-link">Lihat detail <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bagian Baru untuk Grafik --}}
    <div class="row mt-4 dashboard-charts">
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title">Transaksi Bulanan</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTransactionsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header">
                    <h5 class="card-title">Top 5 Item Terlaris</h5>
                </div>
                <div class="card-body">
                    <canvas id="topSellingItemsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('styles')
<style>
    /* Color Variables */
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --success-color: #4cc9f0;
        --text-dark: #2b2d42;
        --text-light: #8d99ae;
        --bg-light: #f8f9fa;
    }

    /* Dashboard Header */
    .dashboard-header {
        margin-bottom: 2rem;
    }
    
    .welcome-message {
        font-size: 2rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }
    
    .user-name {
        color: var(--primary-color);
    }
    
    .welcome-subtext {
        color: var(--text-light);
        font-size: 1.1rem;
    }

    /* Stat Cards */
    .stat-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        font-size: 1.25rem;
    }
    
    .category-card .stat-icon {
        background-color: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
    }
    
    .customer-card .stat-icon {
        background-color: rgba(76, 201, 240, 0.1);
        color: var(--success-color);
    }
    
    .supplier-card .stat-icon {
        background-color: rgba(72, 149, 239, 0.1);
        color: var(--accent-color);
    }
    
    .user-card .stat-icon {
        background-color: rgba(63, 55, 201, 0.1);
        color: var(--secondary-color);
    }
    
    .stat-title {
        color: var(--text-light);
        font-size: 1rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .stat-number {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1rem;
    }
    
    .stat-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding-top: 0.75rem;
    }
    
    .stat-link {
        color: var(--primary-color);
        font-size: 0.85rem;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .stat-link:hover {
        color: var(--secondary-color);
    }

    /* Activity Card - (Keep if you have an activity card, otherwise remove) */
    .activity-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    
    .activity-card .card-header {
        background: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .activity-card .card-title {
        margin-bottom: 0;
        color: var(--text-dark);
    }
    
    .view-all {
        color: var(--primary-color);
        font-size: 0.85rem;
        text-decoration: none;
    }
    
    .view-all:hover {
        color: var(--secondary-color);
    }
    
    .activity-item {
        display: flex;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: rgba(67, 97, 238, 0.1);
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
    }
    
    .activity-content {
        flex: 1;
    }
    
    .activity-content p {
        margin-bottom: 0.25rem;
        color: var(--text-dark);
    }
    
    .activity-content small {
        font-size: 0.75rem;
    }

    /* Chart Cards */
    .chart-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }
    .chart-card .card-header {
        background: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1rem 1.5rem;
    }
    .chart-card .card-body {
        padding: 1.5rem;
    }
</style>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation for stat cards on load
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });

        // --- Monthly Transactions Chart ---
        const monthlyTransactionsCtx = document.getElementById('monthlyTransactionsChart').getContext('2d');
        const monthlyTransactionsChart = new Chart(monthlyTransactionsCtx, {
            type: 'line', // You can change this to 'bar' if preferred
            data: {
                labels: @json($months), // Data dari controller
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: @json($transactionCounts), // Data dari controller
                    backgroundColor: 'rgba(67, 97, 238, 0.2)',
                    borderColor: 'rgba(67, 97, 238, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4 // Membuat garis sedikit melengkung
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (Number.isInteger(value)) {
                                    return value;
                                }
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Sembunyikan legenda jika hanya ada satu dataset
                    },
                    title: {
                        display: true,
                        text: 'Grafik Transaksi Bulanan (12 Bulan Terakhir)'
                    }
                }
            }
        });

        // --- Top Selling Items Chart ---
        const topSellingItemsCtx = document.getElementById('topSellingItemsChart').getContext('2d');
        const topSellingItemsChart = new Chart(topSellingItemsCtx, {
            type: 'bar',
            data: {
                labels: @json($itemLabels), // Data dari controller
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: @json($itemQuantities), // Data dari controller
                    backgroundColor: [
                        'rgba(76, 201, 240, 0.7)', // success-color
                        'rgba(67, 97, 238, 0.7)', // primary-color
                        'rgba(72, 149, 239, 0.7)', // accent-color
                        'rgba(63, 55, 201, 0.7)', // secondary-color
                        'rgba(144, 12, 63, 0.7)' // A different color for variety
                    ],
                    borderColor: [
                        'rgba(76, 201, 240, 1)',
                        'rgba(67, 97, 238, 1)',
                        'rgba(72, 149, 239, 1)',
                        'rgba(63, 55, 201, 1)',
                        'rgba(144, 12, 63, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (Number.isInteger(value)) {
                                    return value;
                                }
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Top 5 Item (Layanan/Sparepart) Terlaris'
                    }
                }
            }
        });
    });
</script>
@endsection