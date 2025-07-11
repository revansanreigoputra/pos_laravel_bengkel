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

    /* Activity Card */
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
</style>
@endsection

@section('scripts')
<!-- Include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Additional JavaScript if needed -->
<script>
    // You can add dashboard-specific JavaScript here
    document.addEventListener('DOMContentLoaded', function() {
        // Animation for stat cards on load
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endsection