@extends('layouts.master')
@section('title', 'Edit Sparepart')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm"> {{-- Tambahkan shadow-sm untuk efek bayangan --}}
                    <div class="card-header bg-primary text-white"> {{-- Gunakan header biru --}}
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i> {{-- Ikon edit --}}
                            Edit Data Sparepart
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sparepart.update', $sparepart->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Section 1: Informasi Sparepart --}}
                            <div class="section-card mb-4">
                                <div class="section-header">
                                    <i class="fas fa-wrench text-primary me-2"></i> {{-- Ikon kunci inggris --}}
                                    <h5 class="mb-0">Detail Sparepart</h5>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-6"> {{-- Gunakan kolom untuk layout 2 kolom --}}
                                            <div class="form-group">
                                                <label for="category_id" class="form-label fw-semibold">
                                                    <i class="fas fa-tags me-1"></i> {{-- Ikon tag --}}
                                                    Kategori <span class="text-danger">*</span>
                                                </label>
                                                <select name="category_id" id="category_id" class="form-select select2-init @error('category_id') is-invalid @enderror" required>
                                                    <option value="">-- Pilih Kategori --</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ old('category_id', $sparepart->category_id) == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="expired_date" class="form-label fw-semibold">
                                                    <i class="fas fa-calendar-alt me-1"></i> {{-- Ikon kalender --}}
                                                    Expired Date <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" class="form-control @error('expired_date') is-invalid @enderror"
                                                    name="expired_date"
                                                    value="{{ old('expired_date', $sparepart->expired_date ? \Carbon\Carbon::parse($sparepart->expired_date)->format('Y-m-d') : '') }}" required>
                                                @error('expired_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name" class="form-label fw-semibold">
                                                    <i class="fas fa-box me-1"></i> {{-- Ikon box --}}
                                                    Nama Sparepart <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                    id="name" name="name" value="{{ old('name', $sparepart->name) }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="code_part" class="form-label fw-semibold">
                                                    <i class="fas fa-qrcode me-1"></i> {{-- Ikon barcode/QR code --}}
                                                    Kode Sparepart <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control @error('code_part') is-invalid @enderror"
                                                    id="code_part" name="code_part" value="{{ old('code_part', $sparepart->code_part) }}" required>
                                                @error('code_part')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: Harga & Diskon --}}
                            <div class="section-card mb-4">
                                <div class="section-header">
                                    <i class="fas fa-money-bill-wave text-primary me-2"></i> {{-- Ikon uang --}}
                                    <h5 class="mb-0">Harga & Diskon</h5>
                                </div>
                                <div class="section-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="selling_price" class="form-label fw-semibold">
                                                    <i class="fas fa-hand-holding-usd me-1"></i> {{-- Ikon harga jual --}}
                                                    Harga Jual <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control @error('selling_price') is-invalid @enderror"
                                                    id="selling_price" name="selling_price" value="{{ old('selling_price', $sparepart->selling_price) }}" required>
                                                @error('selling_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="quantity" class="form-label fw-semibold">
                                                    <i class="fas fa-sort-numeric-up-alt me-1"></i> {{-- Ikon kuantitas --}}
                                                    Kuantitas <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                                    id="quantity" name="quantity" value="{{ old('quantity', $sparepart->quantity) }}" required>
                                                @error('quantity')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Diskon --}}
                                    <div class="form-group">
                                        <label for="discount_percentage" class="form-label fw-semibold">
                                            <i class="fas fa-percent me-1"></i> {{-- Ikon persen --}}
                                            Diskon (%)
                                        </label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('discount_percentage') is-invalid @enderror"
                                            id="discount_percentage" name="discount_percentage"
                                            value="{{ old('discount_percentage', $sparepart->discount_percentage) }}">
                                        @error('discount_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="discount_start_date" class="form-label fw-semibold">
                                                    <i class="fas fa-calendar-plus me-1"></i> {{-- Ikon tanggal mulai --}}
                                                    Tanggal Mulai Diskon
                                                </label>
                                                <input type="date"
                                                    class="form-control @error('discount_start_date') is-invalid @enderror"
                                                    id="discount_start_date" name="discount_start_date"
                                                    value="{{ old('discount_start_date', $sparepart->discount_start_date ? $sparepart->discount_start_date->format('Y-m-d') : '') }}">
                                                @error('discount_start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="discount_end_date" class="form-label fw-semibold">
                                                    <i class="fas fa-calendar-minus me-1"></i> {{-- Ikon tanggal selesai --}}
                                                    Tanggal Selesai Diskon
                                                </label>
                                                <input type="date"
                                                    class="form-control @error('discount_end_date') is-invalid @enderror"
                                                    id="discount_end_date" name="discount_end_date"
                                                    value="{{ old('discount_end_date', $sparepart->discount_end_date ? $sparepart->discount_end_date->format('Y-m-d') : '') }}">
                                                @error('discount_end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Form Actions --}}
                            <div class="form-actions">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('sparepart.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kustomisasi CSS yang sama dengan halaman pembelian --}}
    <style>
        .section-card {
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            background: #fff;
        }

        .section-header {
            background: linear-gradient(135deg, #f8f9fc 0%, #eaecf4 100%);
            padding: 15px 20px;
            border-bottom: 1px solid #e3e6f0;
            border-radius: 10px 10px 0 0;
            display: flex;
            align-items: center;
        }

        .section-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: #5a5c69;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid #e3e6f0;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .form-actions {
            background: #f8f9fc;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #e3e6f0;
            margin-top: 20px;
        }

        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .text-danger {
            color: #e74a3b !important;
        }

        .bg-light {
            background-color: #f8f9fc !important;
        }

        .border-primary {
            border-color: #4e73df !important;
        }

        /* Style untuk Select2 */
        .select2-container .select2-selection--single {
            height: 42px;
            border: 2px solid #e3e6f0;
            border-radius: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 15px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .select2-container--default .select2-results__option--highlighted {
            background-color: #4e73df;
            color: white;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #f8f9fa;
        }

        .select2-container--default .select2-results__option[aria-selected=true]:hover {
            background-color: #4e73df;
            color: white;
        }

        @media (max-width: 768px) {
            .section-body {
                padding: 15px;
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2 untuk category_id
            $('#category_id').select2({
                placeholder: '-- Pilih Kategori --',
                allowClear: true,
                dropdownParent: $('body')
            });
        });
    </script>
@endpush