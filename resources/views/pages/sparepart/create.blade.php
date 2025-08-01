@extends('layouts.master')
@section('title', 'Tambah Sparepart')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm"> {{-- Tambahkan shadow-sm untuk efek bayangan --}}
                    <div class="card-header bg-primary text-white"> {{-- Gunakan header biru --}}
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i> {{-- Ikon tambah --}}
                            Tambah Sparepart Baru
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sparepart.store') }}" method="POST">
                            @csrf

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
                                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                                <input type="date" class="form-control @error('expired_date') is-invalid @enderror" name="expired_date" value="{{ old('expired_date') }}" required>
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
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
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
                                                <input type="text" class="form-control @error('code_part') is-invalid @enderror" id="code_part" name="code_part" value="{{ old('code_part') }}" required>
                                                @error('code_part')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Hidden input untuk selling_price tetap di sini --}}
                                    <input type="hidden" class="form-control" name="selling_price" value="0">
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
                                        Simpan Sparepart
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
                placeholder: '-- Pilih Kategori --', // Mengubah placeholder sedikit lebih jelas
                allowClear: true,
                dropdownParent: $('body') // Penting untuk modal atau jika ada masalah z-index
            });

            // Jika memiliki elemen lain yang menggunakan select2-init, Anda bisa menginisialisasinya di sini juga
            // $('.select2-init').not('#category_id').select2({
            //     placeholder: 'Pilih...',
            //     allowClear: true,
            //     dropdownParent: $('body')
            // });
        });
    </script>
@endpush