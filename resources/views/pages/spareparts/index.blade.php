@php
    use Illuminate\Support\Carbon;
@endphp

@extends('layouts.master')

@section('title', 'Data Sparepart')

@section('action')
    @can('sparepart.create')
        <a href="{{ route('spareparts.create') }}" class="btn btn-primary">Tambah Sparepart</a>
    @endcan
@endsection

@push('addon-style')
<style>
    /* Mobile-first responsive styles */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            gap: 10px;
        }
        
        .card-header .btn {
            width: 100%;
            margin-bottom: 5px;
        }
        
        .card-header .me-2 {
            margin-right: 0 !important;
        }
        
        .card-header form {
            width: 100%;
        }
        
        .card-header form select {
            width: 100%;
            margin-right: 0 !important;
        }
        
        .mobile-card-view {
            display: block;
        }
        
        .desktop-table-view {
            display: none;
        }
        
        .sparepart-card {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: white;
        }
        
        .sparepart-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #dee2e6;
        }
        
        .sparepart-header > div:first-child {
            flex: 1;
            margin-right: 1rem;
            min-width: 0;
        }
        
        .sparepart-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
            color: #212529;
            word-wrap: break-word;
            line-height: 1.3;
        }
        
        .sparepart-code {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        
        .sparepart-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .detail-value {
            font-size: 0.875rem;
            color: #212529;
        }
        
        .price-section {
            grid-column: 1 / -1;
            padding-top: 0.75rem;
            border-top: 1px solid #f8f9fa;
        }
        
        .price-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        
        .sparepart-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .sparepart-actions .btn {
            flex: 1;
            min-width: 70px;
        }
        
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-badge.available {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-badge.unavailable {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .mobile-filter-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        
        .mobile-action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .mobile-action-buttons .btn {
            width: 100%;
        }
    }
    
    @media (min-width: 769px) {
        .mobile-card-view {
            display: none;
        }
        
        .desktop-table-view {
            display: block;
        }
    }
</style>
@endpush

@section('content')
    <div class="card">
        <div class="card-header mb-4 d-flex justify-content-end">
            <!-- Desktop Header (unchanged) -->
            <div class="d-none d-md-flex justify-content-end w-100 gap-2">
                <a href="{{ route('sparepart.download-template') }}" class="btn btn-outline-info me-2">Unduh Template</a>
                <span class="block text-muted font-thin w-full d-none d-lg-inline">Unduh template untuk mengimpor data sparepart.</span>

                <form action="{{ route('spareparts.index') }}" method="GET" class="d-flex align-items-center">
                    <select name="category_id" class="form-select me-2" onchange="this.form.submit()">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('sparepart.export') }}" class="btn btn-outline-success me-2">Export</a>
                <button type="button" class="btn btn-outline-warning me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                    Import
                </button>
            </div>
            
            <!-- Mobile Header -->
            <div class="d-md-none w-100">
                <div class="mobile-filter-section">
                    <form action="{{ route('spareparts.index') }}" method="GET">
                        <select name="category_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Semua Kategori --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                
                <div class="mobile-action-buttons">
                    <a href="{{ route('sparepart.download-template') }}" class="btn btn-outline-info">
                        <i class="fas fa-download me-1"></i> Unduh Template
                    </a>
                    <a href="{{ route('sparepart.export') }}" class="btn btn-outline-success">
                        <i class="fas fa-file-export me-1"></i> Export
                    </a>
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-import me-1"></i> Import
                    </button>
                </div>
            </div>

            <!-- Import Modal (unchanged) -->
            <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="importModalLabel">Import Data Sparepart</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('sparepart.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <p>Unduh Template, isi dengan data yang sesuai, dan unggah di sini.</p>
                                <div class="mb-3">
                                    <label for="fileInput" class="form-label">Pilih File Excel</label>
                                    <input type="file" name="file" class="form-control" id="fileInput" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-outline-warning">Import Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Desktop Table View (unchanged) -->
            <div class="desktop-table-view">
                <div class="table-responsive">
                    <table id="sparepart-table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Part</th>
                                <th>Nama Sparepart</th>
                                <th>Kategori</th>
                                <th>Stok Saat Ini</th>
                                <th>Kadaluarsa Terdekat</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($spareparts as $sparepart)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $sparepart->code_part ?? '-' }}</td>
                                    <td>{{ $sparepart->name }}</td>
                                    <td>{{ $sparepart->category->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="status-badge {{ $sparepart->available_stock > 0 ? 'available' : 'unavailable' }}">
                                            {{ number_format($sparepart->available_stock) }}
                                        </span>
                                    </td>
                                    <td>
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
                                        @if ($sparepart->isDiscountActive())
                                            <span class="text-decoration-line-through text-muted">Rp
                                                {{ number_format($sparepart->selling_price, 0, ',', '.') }}</span><br>
                                            Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }}
                                            <span class="badge bg-success">{{ $sparepart->discount_percentage }}% Off</span>
                                        @else
                                            Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td>
                                        @canany(['sparepart.edit', 'sparepart.delete'])
                                            @can('sparepart.edit')
                                                <a href="{{ route('spareparts.edit', $sparepart->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            @endcan
                                            @can('sparepart.delete')
                                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#delete-sparepart-{{ $sparepart->id }}">
                                                    Hapus
                                                </button>
                                                <x-modal.delete-confirm id="delete-sparepart-{{ $sparepart->id }}" :route="route('spareparts.destroy', $sparepart->id)" 
                                                    item="{{ $sparepart->name }}" title="Hapus Sparepart?" 
                                                    description="Data sparepart yang dihapus tidak bisa dikembalikan." />
                                            @endcan
                                        @else
                                            <span class="text-muted">Tidak ada aksi</span>
                                        @endcanany
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="mobile-card-view">
                @foreach ($spareparts as $sparepart)
                    <div class="sparepart-card">
                        <div class="sparepart-header">
                            <div>
                                <div class="sparepart-title">{{ $sparepart->name }}</div>
                                <div class="sparepart-code">{{ $sparepart->code_part ?? '-' }}</div>
                            </div>
                            <div class="text-end" style="flex-shrink: 0;">
                                <span class="status-badge {{ $sparepart->available_stock > 0 ? 'available' : 'unavailable' }}">
                                    {{ number_format($sparepart->available_stock) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="sparepart-details">
                            <div class="detail-item">
                                <div class="detail-label">Kadaluarsa Terdekat</div>
                                <div class="detail-value">
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
                                </div>
                            </div>
                            
                            <div class="price-section">
                                <div class="price-grid">
                                    <div class="detail-item">
                                        <div class="detail-label">Harga Beli</div>
                                        <div class="detail-value">
                                            @php
                                                $latestPurchase = $sparepart->purchaseOrderItems->first();
                                            @endphp
                                            @if ($latestPurchase)
                                                <div>Rp {{ number_format($latestPurchase->purchase_price, 0, ',', '.') }}</div>
                                                <small class="text-muted">
                                                    ({{ \Carbon\Carbon::parse($latestPurchase->created_at)->format('d-m-Y') }})
                                                </small>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <div class="detail-label">Harga Jual</div>
                                        <div class="detail-value">
                                            @if ($sparepart->isDiscountActive())
                                                <span class="text-decoration-line-through text-muted small">
                                                    Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}
                                                </span><br>
                                                <div>Rp {{ number_format($sparepart->final_selling_price, 0, ',', '.') }}</div>
                                                <span class="badge bg-success small">{{ $sparepart->discount_percentage }}% Off</span>
                                            @else
                                                Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="sparepart-actions">
                            @canany(['sparepart.edit', 'sparepart.delete'])
                                @can('sparepart.edit')
                                    <a href="{{ route('spareparts.edit', $sparepart->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit me-1"></i> Edit
                                    </a>
                                @endcan
                                @can('sparepart.delete')
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete-sparepart-{{ $sparepart->id }}">
                                        <i class="fas fa-trash me-1"></i> Hapus
                                    </button>
                                    <x-modal.delete-confirm id="delete-sparepart-{{ $sparepart->id }}" :route="route('spareparts.destroy', $sparepart->id)" 
                                        item="{{ $sparepart->name }}" title="Hapus Sparepart?" 
                                        description="Data sparepart yang dihapus tidak bisa dikembalikan." />
                                @endcan
                            @else
                                <span class="text-muted small">Tidak ada aksi</span>
                            @endcanany
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $spareparts->links() }}
            </div>

            <p class="mt-4 text-muted">
                **Perhatian:** Untuk memastikan sparepart dapat dijual, pastikan Anda telah mengisi **Harga Jual** dan
                mengatur **Diskon** (jika ada) melalui tombol "Edit" pada setiap baris data.
            </p>
        </div>
    </div>
@endsection

@push('addon-script')
    <script>
        $(document).ready(function() {
            // Only initialize DataTables on desktop view
            if (window.innerWidth > 768) {
                $('#sparepart-table').DataTable();
            }
            
            // Reinitialize DataTables on window resize
            $(window).resize(function() {
                if (window.innerWidth > 768 && !$.fn.DataTable.isDataTable('#sparepart-table')) {
                    $('#sparepart-table').DataTable();
                } else if (window.innerWidth <= 768 && $.fn.DataTable.isDataTable('#sparepart-table')) {
                    $('#sparepart-table').DataTable().destroy();
                }
            });
        });
    </script>
@endpush