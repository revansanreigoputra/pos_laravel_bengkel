@extends('layouts.master')
@section('title', 'Notifikasi')

@push('styles')
<style>
    .notification-card {
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .notification-item {
        transition: all 0.2s ease;
        border: none;
        border-bottom: 1px solid #e9ecef;
        padding: 1rem 1.5rem;
        position: relative;
    }
    
    .notification-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .notification-item.unread {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border-left: 4px solid #2196f3;
    }
    
    .notification-item.unread::before {
        content: '';
        position: absolute;
        top: 50%;
        right: 15px;
        width: 8px;
        height: 8px;
        background: #f44336;
        border-radius: 50%;
        animation: pulse 2s infinite;
        transform: translateY(-50%);
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(244, 67, 54, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(244, 67, 54, 0); }
        100% { box-shadow: 0 0 0 0 rgba(244, 67, 54, 0); }
    }
    
    .status-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: white;
        margin-right: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .status-icon.stock-alert {
        background: linear-gradient(135deg, #ff5722, #f44336);
        animation: shake 1s ease-in-out infinite;
    }
    
    .status-icon.purchase {
        background: linear-gradient(135deg, #4caf50, #2e7d32);
    }
    
    .status-icon.info {
        background: linear-gradient(135deg, #2196f3, #1976d2);
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-2px); }
        75% { transform: translateX(2px); }
    }
    
    .notification-message {
        font-size: 1rem;
        font-weight: 500;
        color: #333;
        margin-bottom: 0.25rem;
        line-height: 1.4;
    }
    
    .notification-time {
        font-size: 0.85rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .notification-actions {
        opacity: 0;
        transform: translateX(20px);
        transition: all 0.3s ease;
    }
    
    .notification-item:hover .notification-actions {
        opacity: 1;
        transform: translateX(0);
    }
    
    .btn-action {
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
        font-weight: 500;
        border-radius: 20px;
        border: none;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .btn-read {
        background: linear-gradient(135deg, #4caf50, #45a049);
        color: white;
    }
    
    .btn-read:hover {
        background: linear-gradient(135deg, #45a049, #3d8b40);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
    }
    
    .btn-delete {
        background: linear-gradient(135deg, #f44336, #d32f2f);
        color: white;
    }
    
    .btn-delete:hover {
        background: linear-gradient(135deg, #d32f2f, #c62828);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(244, 67, 54, 0.3);
    }
    
    .bulk-actions {
        gap: 0.75rem;
    }
    
    .bulk-actions .btn {
        border-radius: 25px;
        font-weight: 500;
        padding: 0.6rem 1.2rem;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .bulk-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    
    .btn-outline-primary:hover {
        border-color: #2196f3;
        background: #2196f3;
    }
    
    .btn-outline-danger:hover {
        border-color: #f44336;
        background: #f44336;
    }
    
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .card-header {
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        border-bottom: 2px solid #e9ecef;
        padding: 1.5rem;
    }
    
    .notification-count {
        background: #f44336;
        color: white;
        border-radius: 15px;
        padding: 0.2rem 0.6rem;
        font-size: 0.75rem;
        font-weight: bold;
        margin-left: 0.5rem;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
    @media (max-width: 768px) {
        .notification-item {
            padding: 1rem;
        }
        
        .notification-actions {
            opacity: 1;
            transform: translateX(0);
            margin-top: 0.5rem;
        }
        
        .bulk-actions {
            flex-direction: column;
        }
        
        .status-icon {
            width: 35px;
            height: 35px;
            font-size: 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-xl fade-in">
    <div class="card notification-card">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
            <div class="d-flex align-items-center">
                <h3 class="card-title mb-0 d-flex align-items-center">
                    <i class="ti ti-bell me-2" style="font-size: 1.5rem; color: #2196f3;"></i>
                    Notifikasi
                    @if($notifications->where('read_at', null)->count() > 0)
                        <span class="notification-count">{{ $notifications->where('read_at', null)->count() }}</span>
                    @endif
                </h3>
            </div>
            <div class="bulk-actions d-flex">
                @if($notifications->where('read_at', null)->count() > 0)
                    <form method="POST" action="{{ route('notifications.readAll') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary" title="Tandai semua sebagai dibaca">
                            <i class="ti ti-checks me-1"></i>
                            Tandai semua dibaca
                        </button>
                    </form>
                @endif
                
                @if($notifications->count() > 0)
                    <form method="POST" action="{{ route('notifications.destroyAll') }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" 
                                onclick="return confirm('Apakah Anda yakin ingin menghapus semua notifikasi? Tindakan ini tidak dapat dibatalkan.')"
                                title="Hapus semua notifikasi">
                            <i class="ti ti-trash me-1"></i>
                            Hapus semua
                        </button>
                    </form>
                @endif
            </div>
        </div>
        
        <div class="list-group list-group-flush">
            @forelse ($notifications as $n)
                <div class="list-group-item notification-item @if(is_null($n->read_at)) unread @endif" 
                     data-notification-id="{{ $n->id }}">
                    <div class="d-flex align-items-start">
                        <div class="status-icon 
                            @if($n->type === 'stock_alert') stock-alert 
                            @elseif($n->type === 'purchase') purchase 
                            @else info @endif">
                            @if($n->type === 'stock_alert')
                                <i class="ti ti-alert-triangle"></i>
                            @elseif($n->type === 'purchase')
                                <i class="ti ti-shopping-cart"></i>
                            @else
                                <i class="ti ti-info-circle"></i>
                            @endif
                        </div>
                        
                        <div class="flex-fill">
                            <div class="notification-message">{{ $n->message }}</div>
                            <div class="notification-time">
                                <i class="ti ti-clock" style="font-size: 0.8rem;"></i>
                                {{ $n->created_at->diffForHumans() }}
                            </div>
                        </div>
                        
                        <div class="notification-actions d-flex gap-2 align-items-center">
                            @if(is_null($n->read_at))
                                <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-action btn-read" title="Tandai sebagai dibaca">
                                        <i class="ti ti-check"></i>
                                        Baca
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('notifications.destroy', $n->id) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action bnt btn-delete" 
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')"
                                        title="Hapus notifikasi">
                                    <i class="ti ti-trash"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="list-group-item empty-state">
                    <i class="ti ti-bell-off"></i>
                    <div>
                        <h5 class="mb-2">Tidak ada notifikasi</h5>
                        <p class="text-muted mb-0">Semua notifikasi akan muncul di sini ketika ada aktivitas baru.</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        @if($notifications->hasPages())
            <div class="card-footer bg-light">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-refresh notifications every 30 seconds
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                // Only refresh if page is visible
                // You can implement AJAX refresh here
            }
        }, 30000);
        
        // Add loading state to buttons
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const button = form.querySelector('button[type="submit"]');
                if (button) {
                    button.classList.add('loading');
                    button.disabled = true;
                    
                    // Re-enable after 3 seconds as fallback
                    setTimeout(() => {
                        button.classList.remove('loading');
                        button.disabled = false;
                    }, 3000);
                }
            });
        });
        
        // Smooth scroll to top when pagination is clicked
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function() {
                setTimeout(() => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }, 100);
            });
        });
        
        // Mark notification as read on hover (optional)
        const unreadNotifications = document.querySelectorAll('.notification-item.unread');
        unreadNotifications.forEach(item => {
            let hoverTimeout;
            item.addEventListener('mouseenter', function() {
                hoverTimeout = setTimeout(() => {
                    // Optional: Auto-mark as read after hovering for 3 seconds
                    // You can implement this feature if desired
                }, 3000);
            });
            
            item.addEventListener('mouseleave', function() {
                clearTimeout(hoverTimeout);
            });
        });
    });
</script>
@endpush
@endsection